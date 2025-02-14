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