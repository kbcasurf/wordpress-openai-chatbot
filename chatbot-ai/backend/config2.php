function generate_chat_response( $last_prompt, $conversation_history ) {

// OpenAI API URL and key
$api_url = 'https://api.openai.com/v1/threads';
$api_key = 'sk-XXX'; // Substitua pela sua chave real
$assistant_id = 'asst-XXX'; // Substitua pelo ID do assistente configurado

// Headers para a API OpenAI
$headers = [
    'Content-Type' => 'application/json',
    'Authorization' => 'Bearer ' . $api_key
];

// Criar uma nova thread
$thread_response = wp_remote_post($api_url, [
    'headers' => $headers,
    'body' => json_encode([]),
    'timeout' => 120
]);

if (is_wp_error($thread_response)) {
    return $thread_response->get_error_message();
}

$thread_data = json_decode(wp_remote_retrieve_body($thread_response), true);
if (!isset($thread_data['id'])) {
    return ['success' => false, 'message' => 'Falha ao criar thread'];
}
$thread_id = $thread_data['id'];

// Adicionar a mensagem do usuário à thread
$message_url = "https://api.openai.com/v1/threads/$thread_id/messages";
wp_remote_post($message_url, [
    'headers' => $headers,
    'body' => json_encode([ 'role' => 'user', 'content' => $last_prompt ]),
    'timeout' => 120
]);

// Iniciar a execução do assistente
$run_url = "https://api.openai.com/v1/threads/$thread_id/runs";
$run_response = wp_remote_post($run_url, [
    'headers' => $headers,
    'body' => json_encode([ 'assistant_id' => $assistant_id ]),
    'timeout' => 120
]);

if (is_wp_error($run_response)) {
    return $run_response->get_error_message();
}

$run_data = json_decode(wp_remote_retrieve_body($run_response), true);
if (!isset($run_data['id'])) {
    return ['success' => false, 'message' => 'Falha ao iniciar execução'];
}
$run_id = $run_data['id'];

// Polling para aguardar a resposta do assistente
$max_attempts = 10;
while ($max_attempts--) {
    sleep(2);
    $status_url = "https://api.openai.com/v1/threads/$thread_id/runs/$run_id";
    $status_response = wp_remote_get($status_url, ['headers' => $headers]);
    $status_data = json_decode(wp_remote_retrieve_body($status_response), true);
    
    if ($status_data['status'] === 'completed') {
        break;
    }
}

// Obter as mensagens da thread
$messages_url = "https://api.openai.com/v1/threads/$thread_id/messages";
$messages_response = wp_remote_get($messages_url, ['headers' => $headers]);
$messages_data = json_decode(wp_remote_retrieve_body($messages_response), true);

if (!isset($messages_data['data'])) {
    return ['success' => false, 'message' => 'Falha ao recuperar resposta'];
}

// Capturar a resposta mais recente do assistente
$assistant_response = '';
foreach (array_reverse($messages_data['data']) as $msg) {
    if ($msg['role'] === 'assistant') {
        $assistant_response = $msg['content'];
        break;
    }
}

return [
    'success' => true,
    'message' => 'Resposta gerada',
    'result' => $assistant_response
];
}
