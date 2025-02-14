document.addEventListener('DOMContentLoaded', function () {
    const chatbotIcon = document.createElement('div');
    chatbotIcon.className = 'oac-chatbot-icon';
    chatbotIcon.innerHTML = `<dotlottie-player src="https://assets5.lottiefiles.com/packages/lf20_5tkzkblw.json" background="transparent" speed="1" style="width: 100%; height: 100%;" loop autoplay></dotlottie-player>`;
    document.body.appendChild(chatbotIcon);

    const chatContainer = document.createElement('div');
    chatContainer.className = 'oac-chatbot-container';
    chatContainer.innerHTML = `
        <div class="oac-chat-header">
            <img src="https://via.placeholder.com/40" alt="Assistant">
            <h3>AI Assistant</h3>
        </div>
        <div class="oac-chat-body"></div>
        <div class="oac-chat-input">
            <input type="text" placeholder="Type your message..." id="oac-chat-input">
            <button id="oac-send-btn">Send</button>
        </div>
    `;
    document.body.appendChild(chatContainer);

    const chatBody = chatContainer.querySelector('.oac-chat-body');
    const chatInput = chatContainer.querySelector('#oac-chat-input');
    const sendButton = chatContainer.querySelector('#oac-send-btn');

    // Toggle chat interface
    chatbotIcon.addEventListener('click', () => {
        chatContainer.classList.toggle('open');
    });

    // Send message
    const sendMessage = async () => {
        const message = chatInput.value.trim();
        if (!message) return;

        // Add user message to chat
        chatBody.innerHTML += `
            <div class="oac-message user">
                <div class="oac-message-bubble">${message}</div>
            </div>
        `;
        chatInput.value = '';
        chatBody.scrollTop = chatBody.scrollHeight;

        // Show loading state
        chatBody.innerHTML += `<div class="oac-loading">Thinking...</div>`;
        chatBody.scrollTop = chatBody.scrollHeight;

        try {
            // Send message to backend
            const response = await fetch(oac_ajax.ajax_url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                    'X-WP-Nonce': oac_ajax.nonce
                },
                body: `action=oac_send_message&message=${encodeURIComponent(message)}`
            });

            const data = await response.json();

            if (data.success) {
                // Add assistant response to chat
                chatBody.innerHTML += `
                    <div class="oac-message">
                        <div class="oac-message-bubble">${data.response}</div>
                    </div>
                `;
            } else {
                // Show error message
                chatBody.innerHTML += `<div class="oac-error">${data.error}</div>`;
            }
        } catch (error) {
            chatBody.innerHTML += `<div class="oac-error">Connection error. Please try again.</div>`;
        }

        // Scroll to bottom
        chatBody.scrollTop = chatBody.scrollHeight;
    };

    // Send message on button click or Enter key
    sendButton.addEventListener('click', sendMessage);
    chatInput.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });
});