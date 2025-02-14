<div class="openai-chatbot-container">
    <dotlottie-player 
        class="chatbot-icon" 
        src="https://unpkg.com/@dotlottie/player-component@latest/dist/dotlottie-player.mjs" 
        background="transparent" 
        speed="1" 
        style="width: 60px; height: 60px;" 
        loop 
        autoplay
    ></dotlottie-player>
    
    <div class="chatbot-modal" style="display: none;">
        <div class="chatbot-header">
            <img src="<?php echo OACB_PLUGIN_URL . 'public/assistant-thumb.png'; ?>" alt="Assistant">
            <h3><?php esc_html_e('AI Assistant', 'openai-chatbot'); ?></h3>
        </div>
        
        <div class="chatbot-messages"></div>
        
        <div class="chatbot-input">
            <input type="text" placeholder="<?php esc_attr_e('Type your message...', 'openai-chatbot'); ?>">
            <button class="send-button"><?php esc_html_e('Send', 'openai-chatbot'); ?></button>
        </div>
    </div>
</div>