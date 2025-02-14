<?php
/**
 * Plugin Name: OpenAI Chatbot
 * Description: A ChatGPT-style interface for OpenAI Assistants API.
 * Version: 1.0.0
 * Author: Paschoal Diniz
 * Text Domain: openai-chatbot
 */

// Security check
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('OAC_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('OAC_PLUGIN_URL', plugin_dir_url(__FILE__));

// Load core files
require_once OAC_PLUGIN_PATH . 'includes/class-openai-handler.php';
require_once OAC_PLUGIN_PATH . 'includes/class-chatbot-security.php';
require_once OAC_PLUGIN_PATH . 'admin/plugin-admin.php';
require_once OAC_PLUGIN_PATH . 'public/partials/chat-interface.php';

// Main plugin class
class OpenAI_Chatbot {
    public function __construct() {
        // Initialize modules
        new OAC_Admin_Settings();
        new OAC_Chatbot_Interface();

        // Load text domain
        add_action('plugins_loaded', [$this, 'load_textdomain']);

        // Enqueue scripts and styles
        add_action('wp_enqueue_scripts', [$this, 'enqueue_assets']);

        // AJAX handler
        add_action('wp_ajax_oac_send_message', [$this, 'handle_chat_message']);
        add_action('wp_ajax_nopriv_oac_send_message', [$this, 'handle_chat_message']);
    }

    public function load_textdomain() {
        load_plugin_textdomain('openai-chatbot', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }

    public function enqueue_assets() {
        wp_enqueue_style('oac-chatbot-style', OAC_PLUGIN_URL . 'public/css/chatbot-style.css');
        wp_enqueue_script('oac-lottie-loader', OAC_PLUGIN_URL . 'public/js/lottie-loader.js', [], '1.0.0', true);
        wp_enqueue_script('oac-chatbot-script', OAC_PLUGIN_URL . 'public/js/chatbot-script.js', ['oac-lottie-loader'], '1.0.0', true);

        // Localize script for AJAX
        wp_localize_script('oac-chatbot-script', 'oac_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('oac_chat_nonce')
        ]);
    }

    public function handle_chat_message() {
        check_ajax_referer('oac_chat_nonce', 'nonce');

        $message = sanitize_text_field($_POST['message']);
        $openai_handler = new OAC_OpenAI_Handler();
        $response = $openai_handler->create_chat_thread($message);

        wp_send_json($response);
    }
}

new OpenAI_Chatbot();