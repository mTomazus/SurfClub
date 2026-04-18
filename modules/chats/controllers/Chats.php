<?php
class Chats extends Trongate {
    
    public function proxy(): void {
        $api_key = constant('ANTHROPIC_API_KEY');

        header('Content-Type: application/json');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            die();
        }

        $input = json_decode(file_get_contents('php://input'), true);

        if (!$input || !isset($input['messages']) || !is_array($input['messages']) || empty($input['messages'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid request']);
            die();
        }

        $messages = array_slice($input['messages'], -50);

        $api_data = json_encode([
            'model' => 'claude-sonnet-4-6',
            'max_tokens' => 2048,
            'system' => $input['system'] ?? '',
            'messages' => $messages
        ]);
        
        $ch = curl_init('https://api.anthropic.com/v1/messages');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'x-api-key: ' . $api_key,
                'anthropic-version: 2023-06-01'
            ],
            CURLOPT_POSTFIELDS => $api_data,
            CURLOPT_TIMEOUT => 60
        ]);

        $response = curl_exec($ch);
        $curl_error = curl_error($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($response === false) {
            http_response_code(502);
            echo json_encode(['error' => 'Upstream request failed', 'detail' => $curl_error]);
            die();
        }

        http_response_code($http_code);
        echo $response;
        die();
    }
}