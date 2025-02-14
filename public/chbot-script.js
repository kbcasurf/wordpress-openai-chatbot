document.addEventListener('DOMContentLoaded', () => {
    const container = document.querySelector('.openai-chatbot-container');
    const icon = container.querySelector('.chatbot-icon');
    const modal = container.querySelector('.chatbot-modal');
    const messagesContainer = modal.querySelector('.chatbot-messages');
    const input = modal.querySelector('input');
    const sendButton = modal.querySelector('.send-button');

    let threadId = localStorage.getItem('openai_thread_id') || '';

    icon.addEventListener('click', () => {
        modal.style.display = modal.style.display === 'none' ? 'flex' : 'none';
    });

    const addMessage = (text, isUser = true) => {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${isUser ? 'user' : 'assistant'}`;
        messageDiv.textContent = text;
        messagesContainer.appendChild(messageDiv);
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
    };

    const sendMessage = async () => {
        const message = input.value.trim();
        if (!message) return;

        addMessage(message, true);
        input.value = '';
        
        try {
            const response = await fetch(oacbData.ajaxUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    action: 'openai_chatbot_process',
                    nonce: oacbData.nonce,
                    message: message,
                    thread_id: threadId
                }),
            });

            const data = await response.json();

            if (data.success) {
                addMessage(data.data.response, false);
                threadId = data.data.thread_id;
                localStorage.setItem('openai_thread_id', threadId);
            } else {
                throw new Error(data.data);
            }
        } catch (error) {
            addMessage(`Error: ${error.message}`, false);
        }
    };

    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') sendMessage();
    });

    sendButton.addEventListener('click', sendMessage);
});