<?php
class OAC_OpenAI_Handler {
    private $api_key;
    private $assistant_id;

    public function __construct() {
        $settings = get_option('oac_settings');
        $this->api_key = $settings['api_key'] ?? '';
        $this->assistant_id = $settings['assistant_id'] ?? '';
    }

    public function create_chat_thread($message) {
        if (!OAC_Chatbot_Security::validate_api_credentials($this->api_key, $this->assistant_id)) {
            return [
                'success' => false,
                'error' => __('Invalid API credentials.', 'openai-chatbot')
            ];
        }

        try {
            $client = OpenAI::factory()
                ->withApiKey($this->api_key)
                ->make();

            $thread = $client->threads()->create();
            $client->messages()->create($thread->id, [
                'role' => 'user',
                'content' => sanitize_textarea_field($message)
            ]);

            $run = $client->runs()->create(
                threadId: $thread->id,
                parameters: ['assistant_id' => $this->assistant_id]
            );

            // Wait for completion
            do {
                $run = $client->runs()->retrieve(
                    threadId: $thread->id,
                    runId: $run->id
                );
                sleep(1);
            } while ($run->status !== 'completed');

            $messages = $client->messages()->list($thread->id);

            return [
                'success' => true,
                'response' => end($messages->data)->content[0]->text->value
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => __('Error communicating with OpenAI:', 'openai-chatbot') . ' ' . $e->getMessage()
            ];
        }
    }
}