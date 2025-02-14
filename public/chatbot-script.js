document.addEventListener('DOMContentLoaded', () => {
    const chatToggle = document.querySelector('.oacb-chat-toggle');
    const chatContainer = document.querySelector('.oacb-chat-container');
    let currentThread = sessionStorage.getItem('oacb_thread_id');

    chatToggle.addEventListener('click', () => {
        chatContainer.style.display = chatContainer.style.display === 'none' ? 'block' : 'none';
    });

    document.querySelector('.oacb-send-btn').addEventListener('click', sendMessage);
    document.querySelector('.oacb-input-container input').addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    function sendMessage() {
        const input = document.querySelector('.oacb-input-container input');
        const message = input.value.trim();
        if (!message) return;

        // Add user message
        appendMessage('user', message);
        input.value = '';

        // AJAX request
        jQuery.post(oacbData.ajaxUrl, {
            action: 'oacb_chat',
            nonce: oacbData.nonce,
            message: message,
            thread_id: currentThread
        }, function(response) {
            if (response.success) {
                currentThread = response.data.thread_id;
                sessionStorage.setItem('oacb_thread_id', currentThread);
                appendMessage('assistant', response.data.response);
            }
        });
    }

    function appendMessage(role, content) {
        const div = document.createElement('div');
        div.className = `oacb-message oacb-${role}-message`;
        div.innerHTML = wp.ksesPost(content);
        document.querySelector('.oacb-chat-messages').appendChild(div);
    }
});