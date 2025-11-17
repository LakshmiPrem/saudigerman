<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Ai_service {

    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->apiKey  = get_option('openai_apikey'); 
        $this->baseUrl = "https://api.openai.com/v1/chat/completions";
    }

public function generateClause($purpose, $jurisdiction, $constraints)
{
    $prompt = "You are a senior legal counsel. Draft a clause.\n
    Purpose: $purpose\n
    Jurisdiction: $jurisdiction\n
    Constraints: $constraints\n\n
    Return the response ONLY in strict JSON format, no explanations, no markdown.\n
    JSON keys must be: clause (string), summary (string), risks (array of strings).";

    $payload = [
        "model" => "gpt-4o-mini",
        "temperature" => 0,
        "messages" => [
            ["role" => "system", "content" => "You draft precise legal clauses and always respond in JSON only."],
            ["role" => "user", "content" => $prompt]
        ]
    ];

    // --- Call API ---
    $ch = curl_init($this->baseUrl);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . $this->apiKey,
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);

    // Get model's reply safely
    $rawContent = $data['choices'][0]['message']['content'] ?? '';

    // Try parsing as JSON
    $parsed = json_decode(trim($rawContent), true);

    if ($parsed) {
        $clauseText = $parsed['clause'] ?? '';
        $summary    = $parsed['summary'] ?? '';
        $risks      = $parsed['risks'] ?? [];
    } else {
        // --- Fallback: Convert raw markdown/plaintext ---
        $clauseText = $rawContent;
        $summary    = '';
        $risks      = [];

        // Simple markdown → HTML converter
        $clauseText = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $clauseText); // bold
        $clauseText = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $clauseText); // italics
        $clauseText = preg_replace('/^#+\s*(.*)$/m', '<h3>$1</h3>', $clauseText); // headings
        $clauseText = preg_replace('/\n- (.*)/', '<li>$1</li>', $clauseText); // bullets
        $clauseText = preg_replace('/(<li>.*<\/li>)/s', '<ul>$1</ul>', $clauseText); // wrap list
        $clauseText = nl2br($clauseText);
    }

    // Format nicely for TinyMCE + TCPDF
    $html = '';
    if ($clauseText) {
        $html .= '<h3>Clause Text</h3><p>' . nl2br(htmlspecialchars($clauseText)) . '</p>';
    }
    if ($summary) {
        $html .= '<h3>Plain-English Summary</h3><p>' . nl2br(htmlspecialchars($summary)) . '</p>';
    }
    if (!empty($risks)) {
        $html .= '<h3>Risks List</h3><ul><li>' . implode('</li><li>', array_map('htmlspecialchars', $risks)) . '</li></ul>';
    }

    return [
        "html" => $html,
        "clause" => $clauseText,
        "summary" => $summary,
        "risks" => $risks,
        "meta" => [
            "prompt" => $prompt,
            "model" => $payload['model'],
            "hits" => []
        ]
    ];
}

}
