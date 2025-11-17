<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use DocuSign\eSign\Configuration;
use DocuSign\eSign\Api\EnvelopesApi;
use DocuSign\eSign\Model\Signer;
use DocuSign\eSign\Model\EnvelopeDefinition;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
class Docusigncontroller extends AdminController {

    public function __construct() {
        parent::__construct();
    }

    public function index() {
        //get access token
     echo  $doctoken=$this->connectDocusign();
        $id=40;
        $path        = get_upload_path_by_type('contract').$id.'/';
        $file = $path.'Sign20231204104756.pdf';
        // DocuSign configuration
        $config = new Configuration();
        $config->setHost('https://demo.docusign.net/restapi');
        $config->addDefaultHeader('Authorization', 'Bearer ' . $doctoken);

        // Create an envelope with a signer
        $envelopeApi = new EnvelopesApi(new \DocuSign\eSign\Client\ApiClient($config));
        $envelopeDefinition = new EnvelopeDefinition([
            'status' => 'sent',
            'emailSubject' => 'Please sign this document',
            'documents' => [
                [
                    'documentBase64' => base64_encode(file_get_contents('PATH_TO_YOUR_DOCUMENT')),
                    'name' => 'Sample Document.pdf',
                    'fileExtension' => 'pdf',
                    'documentId' => '1'
                ]
            ],
            'recipients' => [
                'signers' => [
                    [
                        'email' => 'signer@example.com',
                        'name' => 'Signer Name',
                        'recipientId' => '1',
                        'routingOrder' => '1'
                    ]
                ]
            ]
        ]);

        // Create and send envelope
        try {
            $results = $envelopeApi->createEnvelope('4e69f881-ce43-42a3-aca9-c69147ecebe3', $envelopeDefinition);//YOUR_ACCOUNT_ID
          //  print_r($results);
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage();
        }
    }
	
public function get_accesstoken(){
		$authorizationUrl = "https://account-d.docusign.com/oauth/auth?" . http_build_query([
    'response_type' => 'code',
    'scope' => 'signature',
    'client_id' =>'c1d24d31-5dc8-4185-8101-7dbc320e35d8',// 'YOUR_CLIENT_ID',
    'redirect_uri' => 'http://localhost:81/legalcounsel2.beveron.net/admin/docusigncontroller/callback',//'YOUR_REDIRECT_URI',
]);

// Redirect the user to the authorization URL
header("Location: $authorizationUrl");

	}
 public function connectDocusign()
    {
        try {
            $params = [
                'response_type' => 'code',
                'scope' => 'signature',
                'client_id' =>'c1d24d31-5dc8-4185-8101-7dbc320e35d8', 
                //'state' => 'a39fh23hnf23',
                'redirect_uri' =>  admin_url('docusigncontroller/callback'),
            ];
            $queryBuild = http_build_query($params);

            $url = "https://account-d.docusign.com/oauth/auth?";

           $botUrl = $url . $queryBuild;
//$botUrl='https://account-d.docusign.com/oauth/auth?response_type=code&scope=signature&client_id=c1d24d31-5dc8-4185-8101-7dbc320e35d8&state=a39fh23hnf23&redirect_uri=http://localhost:81/legalcounsel2.beveron.net/admin/docusigncontroller/callback';
		//	echo $botUrl;
           redirect($botUrl);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Something Went wrong !');
        }
    }
    /**
     * This function called when you auth your application with docusign
     *
     * @return url
     */
    public function callback()//Request $request
    {
      //  $code = $request->code;
      $code = $this->input->get('code');

        $client_id = 'c1d24d31-5dc8-4185-8101-7dbc320e35d8';
        $client_secret = '3d3329cf-85a1-41b6-912d-e3ca5ca1cd45';

   $integrator_and_secret_key = "Basic " . base64_encode("{$client_id}:{$client_secret}");

        $client = new \GuzzleHttp\Client();
        $result = $client->post('https://account-d.docusign.com/oauth/token', [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'code' => $code,
            ],
            'headers' => [
                'Authorization' => $integrator_and_secret_key
            ]
        ]);

        $decodedData = json_decode($result->getBody());
        $this->session->set_userdata('docusign_auth_code', $decodedData->access_token);
   // echo 'Docusign Succesfully Connected';
       // redirect('docusign')->with('success', 'Docusign Successfully Connected');

       // return redirect()->route('docusign')->with('success', 'Docusign Succesfully Connected');
       $doctoken=$decodedData->access_token;
         $id=40;
        $path        = get_upload_path_by_type('contract').$id.'/';
        $file = $path.'Sign20231204104756.pdf';
        // DocuSign configuration
        $config = new Configuration();
        $config->setHost('https://demo.docusign.net/restapi');
        $config->addDefaultHeader('Authorization', 'Bearer ' . $doctoken);

        // Create an envelope with a signer
        $envelopeApi = new EnvelopesApi(new \DocuSign\eSign\Client\ApiClient($config));
        $envelopeDefinition = new EnvelopeDefinition([
            'status' => 'sent',
            'emailSubject' => 'Please sign this document',
            'documents' => [
                [
                    'documentBase64' => base64_encode(file_get_contents($file)),
                    'name' => 'Sample Document.pdf',
                    'fileExtension' => 'pdf',
                    'documentId' => '1'
                ]
            ],
            'recipients' => [
                'signers' => [
                    [
                        'email' => 'beverondev@gmail.com',
                        'name' => 'team developer',
                        'recipientId' => '1',
                        'routingOrder' => '1'
                    ]
                ]
            ]
        ]);

        // Create and send envelope
        try {
            $results = $envelopeApi->createEnvelope('4e69f881-ce43-42a3-aca9-c69147ecebe3', $envelopeDefinition);//YOUR_ACCOUNT_ID
          //  print_r($results);
        } catch (Exception $e) {
            echo 'Exception: ' . $e->getMessage();
        }
    }
}
