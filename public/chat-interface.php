<div class="oacb-chat-container" style="display: none;">
    <div class="oacb-chat-header">
        <dotlottie-player 
            src="<?php echo OACB_PLUGIN_URL . 'public/ai-assistant.lottie' ?>" 
            background="transparent" 
            speed="1" 
            style="width: 40px; height: 40px;" 
            loop autoplay>
        </dotlottie-player>
        <h3><?php echo esc_html(get_option('oacb_assistant_name', 'AI Assistant')) ?></h3>
    </div>
    <div class="oacb-chat-messages"></div>
    <div class="oacb-input-container">
        <input type="text" placeholder="Type your message..." />
        <button class="oacb-send-btn">Send</button>
    </div>
</div>
<button class="oacb-chat-toggle">
    <dotlottie-player ...></dotlottie-player>
</button>