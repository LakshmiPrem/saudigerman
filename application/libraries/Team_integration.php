<?php
use GuzzleHttp\Client;

class Team_integration {

    private $tenantId;
    private $clientId;
    private $clientSecret;
    private $username;
    private $password;

    public function __construct() {
        // $this->tenantId = 'a6379b22-22bf-4c5a-836c-fe0e23d5b5dd';
        // $this->clientId = '90216d46-e8e7-4965-8e1b-a69e593adfc7';
        // $this->clientSecret = 'kic8Q~PqjWlcXGMNqtyZBfpTvlAXyey4CwS9kc3M';
        // $this->username = 'maaacloud@maaashaout.onmicrosoft.com';
        // $this->password = 'Rithik@@2024';

        $this->tenantId = get_option('teams_api_tenantid');
        $this->clientId = get_option('teams_api_clientid');
        $this->clientSecret = get_option('teams_api_clientsecret');
        $this->username = get_option('teams_api_username');
        $this->password = get_option('teams_api_password');
    }

    public function createMeeting($startDateTime, $endDateTime, $subject) {
        try {
            $guzzle = new Client();
            $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/token';

            // Request for user access token
            $response = $guzzle->post($url, [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'resource' => 'https://graph.microsoft.com/',
                    'grant_type' => 'password',
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $user_token = json_decode($response->getBody()->getContents());
            $user_accessToken = $user_token->access_token;

            // Create the meeting
            $response = $guzzle->post('https://graph.microsoft.com/v1.0/users/0fd2d46e-6339-40f7-b657-5341b48ed4b7/onlineMeetings', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $user_accessToken,
                    'Content-Type' => 'application/json',
                ],
                'json' => [
                    'startDateTime' => $startDateTime,
                    'endDateTime' => $endDateTime,
                    'subject' => $subject,
                ],
            ]);

            return json_decode($response->getBody(), true);

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    public function get_userid() {
        $guzzle = new Client();
            $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/token';

            // Request for user access token
            $response = $guzzle->post($url, [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'resource' => 'https://graph.microsoft.com/',
                    'grant_type' => 'password',
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $user_token = json_decode($response->getBody()->getContents());
            $user_accessToken = $user_token->access_token;
        $response = $guzzle->get('https://graph.microsoft.com/v1.0/users/maaacloud@maaashaout.onmicrosoft.com', [
            'headers' => [
                'Authorization' => 'Bearer ' . $user_accessToken,
            ],
        ]);
        
        $user = json_decode($response->getBody(), true);
        print_r($user);
    }

    public function list_events(){
        $guzzle = new Client();
            $url = 'https://login.microsoftonline.com/' . $this->tenantId . '/oauth2/token';

            // Request for user access token
            $response = $guzzle->post($url, [
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'resource' => 'https://graph.microsoft.com/',
                    'grant_type' => 'password',
                    'username' => $this->username,
                    'password' => $this->password,
                ],
            ]);

            $user_token = json_decode($response->getBody()->getContents());
            $user_accessToken = $user_token->access_token;
        $response = $guzzle->get('https://graph.microsoft.com/v1.0/users/0fd2d46e-6339-40f7-b657-5341b48ed4b7/events', [
            'headers' => [
                'Authorization' => 'Bearer ' . $user_accessToken,
            ],
        ]);
        $events = json_decode($response->getBody(), true);
        print_r($events);
        
    }
}
