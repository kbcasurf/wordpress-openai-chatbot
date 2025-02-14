<?php
/*
Plugin Name: OpenAI ChatBot
Description: ChatGPT-like interface for OpenAI Assistants API
Version: 1.0
Author: Paschoal Diniz
Text Domain: openai-chatbot
Domain Path: /languages
*/

if (!defined('ABSPATH')) exit;

// Define plugin constants
define('OACB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OACB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Include required files
require_once OACB_PLUGIN_DIR . 'admin/plugin-admin.php';
require_once OACB_PLUGIN_DIR . 'public/chat-interface.php';

class OpenAI_ChatBot {
    public function __construct() {
        add_action('wp_enqueue_scripts', [$this, 'enqueue_public_assets']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_assets']);
        add_action('wp_footer', [$this, 'render_chat_interface']);
    }

    public function enqueue_public_assets() {
        wp_enqueue_style(
            'openai-chatbot-style',
            OACB_PLUGIN_URL . 'public/chbot-style.css',
            [],
            filemtime(OACB_PLUGIN_DIR . 'public/chbot-style.css')
        );

        wp_enqueue_script(
            'dotlottie-player',
            'https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs',
            [],
            null,
            true
        );

        wp_enqueue_script(
            'openai-chatbot-script',
            OACB_PLUGIN_URL . 'public/chbot-script.js',
            ['jquery'],
            filemtime(OACB_PLUGIN_DIR . 'public/chbot-script.js'),
            true
        );

        wp_localize_script('openai-chatbot-script', 'oacbData', [
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('openai_chatbot_nonce')
        ]);
    }

    public function enqueue_admin_assets($hook) {
        if ($hook !== 'settings_page_openai-chatbot') return;
        
        wp_enqueue_style(
            'openai-chatbot-admin-style',
            OACB_PLUGIN_URL . 'admin/admin-style.css',
            [],
            filemtime(OACB_PLUGIN_DIR . 'admin/admin-style.css')
        );
    }

    public function render_chat_interface() {
        include OACB_PLUGIN_DIR . 'public/chat-interface.php';
    }
}

new OpenAI_ChatBot();

// AJAX handlers
add_action('wp_ajax_openai_chatbot_process', 'openai_chatbot_process_message');
add_action('wp_ajax_nopriv_openai_chatbot_process', 'openai_chatbot_process_message');

function openai_chatbot_process_message() {
    check_ajax_referer('openai_chatbot_nonce', 'nonce');

    // Validate and sanitize input
    $message = sanitize_text_field($_POST['message'] ?? '');
    $thread_id = sanitize_text_field($_POST['thread_id'] ?? '');

    if (empty($message)) {
        wp_send_json_error(__('Message cannot be empty', 'openai-chatbot'));
    }

    $api_key = get_option('openai_chatbot_api_key');
    $assistant_id = get_option('openai_chatbot_assistant_id');

    if (!$api_key || !$assistant_id) {
        wp_send_json_error(__('Plugin misconfigured - please contact administrator', 'openai-chatbot'));
    }

    // Process OpenAI API request
    try {
        $response = openai_chatbot_api_request($api_key, $assistant_id, $message, $thread_id);
        wp_send_json_success($response);
    } catch (Exception $e) {
        wp_send_json_error($e->getMessage());
    }
}

function openai_chatbot_api_request($api_key, $assistant_id, $message, $thread_id = '') {
    $headers = [
        'Content-Type' => 'application/json',
        'Authorization' => 'Bearer ' . $api_key,
        'OpenAI-Beta' => 'assistants=v1'
    ];

    try {
        // Create a new thread if one doesn't exist
        if (empty($thread_id)) {
            $thread_url = 'https://api.openai.com/v1/threads';
            $thread_response = wp_remote_post($thread_url, [
                'headers' => $headers
            ]);

            if (is_wp_error($thread_response)) {
                throw new Exception(__('Failed to create thread:', 'openai-chatbot') . ' ' . $thread_response->get_error_message());
            }

            $thread_body = json_decode(wp_remote_retrieve_body($thread_response), true);
            $thread_id = $thread_body['id'];
        }

        // Add the user's message to the thread
        $message_url = "https://api.openai.com/v1/threads/{$thread_id}/messages";
        $message_response = wp_remote_post($message_url, [
            'headers' => $headers,
            'body' => json_encode([
                'role' => 'user',
                'content' => $message
            ])
        ]);

        if (is_wp_error($message_response)) {
            throw new Exception(__('Failed to send message:', 'openai-chatbot') . ' ' . $message_response->get_error_message());
        }

        // Create a run for the assistant
        $run_url = "https://api.openai.com/v1/threads/{$thread_id}/runs";
        $run_response = wp_remote_post($run_url, [
            'headers' => $headers,
            'body' => json_encode([
                'assistant_id' => $assistant_id
            ])
        ]);

        if (is_wp_error($run_response)) {
            throw new Exception(__('Failed to create run:', 'openai-chatbot') . ' ' . $run_response->get_error_message());
        }

        $run_body = json_decode(wp_remote_retrieve_body($run_response), true);
        $run_id = $run_body['id'];

        // Wait for the run to complete
        do {
            sleep(1); // Avoid overwhelming the API
            $run_status_url = "https://api.openai.com/v1/threads/{$thread_id}/runs/{$run_id}";
            $run_status_response = wp_remote_get($run_status_url, [
                'headers' => $headers
            ]);

            if (is_wp_error($run_status_response)) {
                throw new Exception(__('Failed to check run status:', 'openai-chatbot') . ' ' . $run_status_response->get_error_message());
            }

            $run_status_body = json_decode(wp_remote_retrieve_body($run_status_response), true);
            $status = $run_status_body['status'];
        } while ($status !== 'completed');

        // Retrieve the assistant's response
        $messages_url = "https://api.openai.com/v1/threads/{$thread_id}/messages";
        $messages_response = wp_remote_get($messages_url, [
            'headers' => $headers,
            'body' => json_encode([
                'limit' => 1,
                'order' => 'desc'
            ])
        ]);

        if (is_wp_error($messages_response)) {
            throw new Exception(__('Failed to retrieve messages:', 'openai-chatbot') . ' ' . $messages_response->get_error_message());
        }

        $messages_body = json_decode(wp_remote_retrieve_body($messages_response), true);
        $response_text = $messages_body['data'][0]['content'][0]['text']['value'];

        return [
            'response' => $response_text,
            'thread_id' => $thread_id
        ];
    } catch (Exception $e) {
        throw new Exception(__('Error communicating with OpenAI:', 'openai-chatbot') . ' ' . $e->getMessage());
    }
}