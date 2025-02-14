<?php
// Security check
defined('ABSPATH') || die('Unauthorized access');

// Add settings link to plugin list
add_filter('plugin_action_links_' . plugin_basename(OACB_PLUGIN_DIR . 'main.php'), 'oacb_add_settings_link');

function oacb_add_settings_link($links) {
    $settings_link = sprintf(
        '<a href="%s">%s</a>',
        esc_url(admin_url('options-general.php?page=openai-chatbot')),
        esc_html__('Settings', 'openai-chatbot')
    );
    
    if (current_user_can('manage_options')) {
        array_unshift($links, $settings_link);
    }
    
    return $links;
}

// Register admin menu and settings
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
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest' => false
    ]);
    
    register_setting('oacb_settings_group', 'oacb_assistant_id', [
        'type' => 'string',
        'sanitize_callback' => 'sanitize_text_field',
        'show_in_rest' => false
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

function oacb_api_section_cb() {
    echo '<p>Enter your OpenAI API credentials below:</p>';
}

function oacb_openai_key_cb() {
    $value = get_option('oacb_openai_key', '');
    echo sprintf(
        '<input type="password" class="regular-text" name="oacb_openai_key" value="%s" />',
        esc_attr($value)
    );
}

function oacb_assistant_id_cb() {
    $value = get_option('oacb_assistant_id', '');
    echo sprintf(
        '<input type="text" class="regular-text" name="oacb_assistant_id" value="%s" />',
        esc_attr($value)
    );
}

function oacb_render_settings_page() {
    // Security check
    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }
    
    // Check if settings were updated
    if (isset($_GET['settings-updated'])) {
        add_settings_error(
            'oacb_messages',
            'oacb_message',
            __('Settings Saved', 'openai-chatbot'),
            'updated'
        );
    }
    
    // Show error/update messages
    settings_errors('oacb_messages');
    
    // Render settings form
    ?>
    <div class="wrap">
        <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('oacb_settings_group');
            do_settings_sections('openai-chatbot');
            submit_button('Save Settings');
            ?>
        </form>
    </div>
    <?php
}