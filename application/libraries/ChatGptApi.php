<?php 
// application/libraries/ChatGptApi.php

class ChatGptApi {
    private $api_key;
    private $api_url = 'https://api.openai.com/v1/engines/davinci-002/completions';

    public function __construct() {
        // Load your API key from a config file or environment variable
        $this->api_key = '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';//get_option('OPENAI_API_KEY');
    }

    public function sendRequest($prompt) {
        
        $data = array(
            'prompt' => $prompt,
            'max_tokens' =>50 // You can adjust this value as needed
        );

        $headers = array(
            'Content-Type: application/json',
            'Authorization: Bearer ' . $this->api_key
        );

        $ch = curl_init($this->api_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        return json_decode($response, true);
    }
}

?>