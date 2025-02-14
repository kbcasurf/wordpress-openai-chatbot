<div class="oacb-chat-container">
    <div class="oacb-chat-header">
        <lottie-player
        src="<?php echo esc_url(OACB_PLUGIN_URL . 'public/chat-icon.json') ?>"
        background="transparent"
        speed="1"
        style="width: 40px; height: 40px;"
        loop
        autoplay
    ></lottie-player>
        <h3><?php echo esc_html(get_option('oacb_assistant_name', 'AI Assistant')); ?></h3>
    </div>
    <div class="oacb-chat-messages"></div>
    <div class="oacb-input-container">
        <input type="text" placeholder="Type your message..." />
        <button class="oacb-send-btn">Send</button>
    </div>
</div>

<button class="oacb-chat-toggle">
    <dotlottie-player 
        src="<?php echo esc_url(OACB_PLUGIN_URL . 'public/chat-icon.lottie'); ?>" 
        background="transparent" 
        speed="1" 
        style="width: 30px; height: 30px;" 
        loop autoplay>
    </dotlottie-player>
</button>