<?php
/*
Plugin Name: OpenAI ChatBot
Plugin URI: https://aiservers.com.br/
Description: ChatGPT-style interface for OpenAI Assistants API
Version: 1.0
Author: Paschoal Diniz
Text Domain: openai-chatbot
*/

// Security check
defined('ABSPATH') || die('Unauthorized access');

// Define plugin constants
define('OACB_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('OACB_PLUGIN_URL', plugin_dir_url(__FILE__));

// Admin setup
require_once OACB_PLUGIN_DIR . 'admin/plugin-admin.php';

// Public interface
add_action('wp_enqueue_scripts', 'oacb_enqueue_public_assets');
add_action('wp_footer', 'oacb_render_chat_interface');
add_action('wp_ajax_oacb_chat', 'oacb_handle_chat_request');
add_action('wp_ajax_nopriv_oacb_chat', 'oacb_handle_chat_request');

function oacb_enqueue_public_assets() {
    // Lottie player
    wp_enqueue_script('dotlottie-player', 'https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs', array(), null, true);
    
    // Plugin assets
    wp_enqueue_style('oacb-chat-style', OACB_PLUGIN_URL . 'public/chipublic/chatbot-style.css');
    wp_enqueue_script('oacb-chat-script', OACB_PLUGIN_URL . 'public/chatbot-script.js', array('jquery'), '1.0', true);
    
    // Localize script
    wp_localize_script('oacb-chat-script', 'oacbData', array(
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('oacb_chat_nonce')
    ));
}

function oacb_render_chat_interface() {
    include OACB_PLUGIN_DIR . 'public/chat-interface.php';
}

function oacb_handle_chat_request() {
    try {
        // Verify nonce
        if (!wp_verify_nonce($_POST['nonce'], 'oacb_chat_nonce')) {
            throw new Exception('Invalid request');
        }

        // Sanitize inputs
        $message = sanitize_text_field($_POST['message']);
        $thread_id = sanitize_text_field($_POST['thread_id'] ?? null);

        // Get settings
        $api_key = get_option('oacb_openai_key');
        $assistant_id = get_option('oacb_assistant_id');

        if (!$api_key || !$assistant_id) {
            throw new Exception('API configuration missing');
        }

        // Initialize OpenAI client
        $client = new OpenAI\Client($api_key);

        // Create thread if new conversation
        if (!$thread_id) {
            $thread = $client->assistants()->threads()->create();
            $thread_id = $thread->id;
        }

        // Add message to thread
        $client->assistants()->threads($thread_id)->messages()->create([
            'role' => 'user',
            'content' => $message
        ]);

        // Create run
        $run = $client->assistants()->threads($thread_id)->runs()->create([
            'assistant_id' => $assistant_id
        ]);

        // Wait for completion
        do {
            sleep(1);
            $run = $client->assistants()->threads($thread_id)->runs()->retrieve($run->id);
        } while ($run->status !== 'completed');

        // Get response
        $messages = $client->assistants()->threads($thread_id)->messages()->list();
        $response = end($messages->data)->content[0]->text->value;

        wp_send_json_success([
            'response' => wp_kses_post($response),
            'thread_id' => $thread_id
        ]);
    } catch (Exception $e) {
        wp_send_json_error(['message' => $e->getMessage()]);
    }
}