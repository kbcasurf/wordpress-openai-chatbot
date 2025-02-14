<?php
add_action('admin_menu', 'openai_chatbot_add_admin_menu');
add_action('admin_init', 'openai_chatbot_settings_init');

function openai_chatbot_add_admin_menu() {
    add_options_page(
        'OpenAI ChatBot',
        'OpenAI ChatBot',
        'manage_options',
        'openai-chatbot',
        'openai_chatbot_options_page'
    );
}

function openai_chatbot_settings_init() {
    register_setting('openai_chatbot', 'openai_chatbot_settings');

    add_settings_section(
        'openai_chatbot_section',
        __('API Configuration', 'openai-chatbot'),
        'openai_chatbot_settings_section_callback',
        'openai_chatbot'
    );

    add_settings_field(
        'openai_api_key',
        __('OpenAI API Key', 'openai-chatbot'),
        'openai_api_key_render',
        'openai_chatbot',
        'openai_chatbot_section'
    );

    add_settings_field(
        'assistant_id',
        __('Assistant ID', 'openai-chatbot'),
        'assistant_id_render',
        'openai_chatbot',
        'openai_chatbot_section'
    );
}

function openai_api_key_render() {
    $value = get_option('openai_chatbot_api_key');
    ?>
    <input type="password" name="openai_chatbot_api_key" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function assistant_id_render() {
    $value = get_option('openai_chatbot_assistant_id');
    ?>
    <input type="text" name="openai_chatbot_assistant_id" value="<?php echo esc_attr($value); ?>" class="regular-text">
    <?php
}

function openai_chatbot_settings_section_callback() {
    echo __('Add your OpenAI API credentials and Assistant ID:', 'openai-chatbot');
}

function openai_chatbot_options_page() {
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('OpenAI ChatBot Settings', 'openai-chatbot'); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields('openai_chatbot');
            do_settings_sections('openai_chatbot');
            submit_button();
            ?>
        </form>
    </div>
    <?php
}