<?php
class OAC_Admin_Settings {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_settings_page']);
        add_action('admin_init', [$this, 'register_settings']);
    }

    public function add_settings_page() {
        add_options_page(
            __('OpenAI Chatbot Settings', 'openai-chatbot'),
            __('Chatbot Settings', 'openai-chatbot'),
            'manage_options',
            'oac-settings',
            [$this, 'render_settings_page']
        );
    }

    public function register_settings() {
        register_setting('oac_options', 'oac_settings', [
            'sanitize_callback' => [OAC_Chatbot_Security::class, 'sanitize_settings']
        ]);

        add_settings_section('main_section', __('API Configuration', 'openai-chatbot'), null, 'oac-settings');

        add_settings_field('api_key', __('OpenAI API Key', 'openai-chatbot'), [$this, 'render_api_key_field'], 'oac-settings', 'main_section');
        add_settings_field('assistant_id', __('Assistant ID', 'openai-chatbot'), [$this, 'render_assistant_id_field'], 'oac-settings', 'main_section');
    }

    public function render_settings_page() {
        ?>
        <div class="wrap">
            <h1><?php echo esc_html__('OpenAI Chatbot Settings', 'openai-chatbot'); ?></h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('oac_options');
                do_settings_sections('oac-settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    public function render_api_key_field() {
        $options = get_option('oac_settings');
        echo '<input type="password" name="oac_settings[api_key]" value="' . esc_attr($options['api_key'] ?? '') . '" class="regular-text">';
    }

    public function render_assistant_id_field() {
        $options = get_option('oac_settings');
        echo '<input type="text" name="oac_settings[assistant_id]" value="' . esc_attr($options['assistant_id'] ?? '') . '" class="regular-text">';
    }
}