<?php
// Add admin menu
add_action('admin_menu', 'oacb_add_admin_menu');
add_action('admin_init', 'oacb_settings_init');

function oacb_add_admin_menu() {
    add_options_page(
        'OpenAI ChatBot Settings',
        'AI ChatBot',
        'manage_options',
        'openai-chatbot',
        'oacb_render_settings_page'
    );
}

function oacb_settings_init() {
    register_setting('oacb_settings_group', 'oacb_openai_key', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field'
    ]);
    
    register_setting('oacb_settings_group', 'oacb_assistant_id', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field'
    ]);

    add_settings_section(
        'oacb_api_section',
        'API Configuration',
        'oacb_api_section_cb',
        'openai-chatbot'
    );

    add_settings_field(
        'oacb_openai_key',
        'OpenAI API Key',
        'oacb_openai_key_cb',
        'openai-chatbot',
        'oacb_api_section'
    );

    add_settings_field(
        'oacb_assistant_id',
        'Assistant ID',
        'oacb_assistant_id_cb',
        'openai-chatbot',
        'oacb_api_section'
    );
}

// ... (remaining admin UI rendering functions)