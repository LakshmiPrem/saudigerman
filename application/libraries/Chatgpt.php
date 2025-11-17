<?php 
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model; 
use GuzzleHttp\Client;
Class Chatgpt{
 function __construct() {
             $this->baseURL = 'https://api.openai.com/v1/';
        $this->api_key = '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';
    }	
function get_chat($message){
$guzzle = new \GuzzleHttp\Client();
$client=new \GuzzleHttp\Client([
    'base_url'=>'https://api.openai.com/v1/',
    'headers' =>[
        'Authorization: Bearer ' . $this->api_key,
        'Content-Type: application/json',
     ],
]);

$response=$client->post('engines/davinci/completions',[
'json' =>[
    'prompt' => $message,
    'max_tokens' => 200,
    'temperature' =>0.2
],
]);
$data=json_decode($response->getBody(),true);
$reply=$data['choices'][0]['text'];
$resp=response()->json(['reply' =>$reply]);
print_r($resp);
return $reply;
}

}
?>