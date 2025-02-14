<div class="oac-chatbot-icon">
    <dotlottie-player src="https://assets5.lottiefiles.com/packages/lf20_5tkzkblw.json" background="transparent" speed="1" style="width: 100%; height: 100%;" loop autoplay></dotlottie-player>
</div>

<div class="oac-chatbot-container">
    <div class="oac-chat-header">
        <img src="https://via.placeholder.com/40" alt="Assistant">
        <h3><?php echo esc_html__('AI Assistant', 'openai-chatbot'); ?></h3>
    </div>
    <div class="oac-chat-body"></div>
    <div class="oac-chat-input">
        <input type="text" placeholder="<?php echo esc_attr__('Type your message...', 'openai-chatbot'); ?>" id="oac-chat-input">
        <button id="oac-send-btn"><?php echo esc_html__('Send', 'openai-chatbot'); ?></button>
    </div>
</div>