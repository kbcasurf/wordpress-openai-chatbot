# wordpress-openai-chatbot
Small application connecting OpenAI Assistant to an AI Based Chatbot for Wordpress.

**Installation Instructions:**

1. **Installation**
- Download the plugin ZIP file
- Go to WordPress Admin → Plugins → Add New → Upload Plugin
- Activate the plugin

2. **Configuration**
- Navigate to Settings → AI ChatBot
- Enter your OpenAI API key and Assistant ID
- Save changes

3. **Usage**
- The chat icon will appear on all pages
- Click the icon to start chatting
- Type messages and press Enter/Send

**Security Measures Implemented:**
1. Input sanitization using WordPress sanitization functions
2. Nonce verification for all AJAX requests
3. Output escaping with wp_kses_post()
4. Secure option storage with update_option()
5. HTTPS enforcement for API calls
6. Error handling without exposing sensitive data

**Performance Optimizations:**
1. Asynchronous API calls
2. Client-side session management
3. Efficient DOM manipulation
4. Proper script enqueuing
5. Cached OpenAI client initialization
6. Lightweight dependencies