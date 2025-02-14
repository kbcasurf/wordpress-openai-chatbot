# wordpress-openai-chatbot
Small application connecting OpenAI Assistant to an AI Based Chatbot for Wordpress.

**User Manual:**

1. **Installation:**
   - Download the plugin zip file
   - Go to WordPress Admin → Plugins → Add New → Upload Plugin
   - Activate the plugin after installation

2. **Configuration:**
   - Go to Settings → Chatbot Settings
   - Enter your OpenAI API key and Assistant ID
   - Save changes

3. **Usage:**
   - The chat icon will appear in the bottom right corner of all pages
   - Click to open the chat interface
   - Start conversing with the AI assistant

4. **Customization:**
   - Modify chatbot-style.css to match your theme
   - Update the Lottie animation URL for different icons
   - Adjust chat window dimensions in CSS

**Security Features Implemented:**
1. Input sanitization and output escaping
2. Nonce verification for all AJAX requests
3. Role-based access control
4. Secure API credential storage
5. Rate limiting protection
6. XSS and SQL injection prevention