<?php 
use Microsoft\Graph\Graph;
use Microsoft\Graph\Model; 
Class Sharegraph{
 function __construct() {
        $this->tenantID = get_option('ms_tenantid');
        $this->clientID = get_option('ms_clientid');
        $this->clientSecret = get_option('ms_clientsecret');
        $this->baseURL = 'https://graph.microsoft.com/v1.0/';
        $this->Token = $this->get_accesstoken();
    }	
function get_accesstoken(){
$guzzle = new \GuzzleHttp\Client();
//$tenantId =get_option('ms_password');//'96a06bdb-557f-43d5-8c7a-432ac411ab98'; //'692f378a-47f8-404f-a997-88087a473145';
//$clientId = '1fa75029-d3e1-46ee-9654-02d36c5c09c4';//'595870ee-6f6c-4117-9ddb-e71823617cdc';
//$clientSecret ='Cwp8Q~XdQOdyRnHnV9tNT9owFRgR3YTWFHRhzdpA';//r558Q~mz7a3vbs56pJDrsZXTTRlXR3w4zckTAc9A';//yQ88Q~rN-qHsfLp.fFMGmS14po_h~RzXRa4YibmI';
$url = 'https://login.microsoftonline.com/' . $this->tenantID . '/oauth2/token';
$user_token = json_decode($guzzle->post($url, [
    'form_params' => [
        'client_id' => $this->clientID,
        'client_secret' =>$this->clientSecret,
        'resource' => 'https://graph.microsoft.com/',
        //'grant_type' => 'client_credentials',
         'grant_type' => 'password',
        'username' =>  get_option('ms_username'), 
        'password' =>  get_option('ms_password')
    ],
])->getBody()->getContents());
$user_accessToken = $user_token->access_token;
return $user_accessToken;
}
public function rungraphuser($source,$dest,$contract_id){

$user_accessToken =$this->Token;
//print_r($user_accessToken);
$graph = new Graph();
$graph->setAccessToken($user_accessToken);
 $this->sharefolder($contract_id);  
//	echo($source);
//	echo($dest);
	$graph->createRequest("PUT", "/me/drive/root:/contracts/".$contract_id."/".$source.":/content")
      ->upload($dest);
	//$graph->createRequest("PUT", "/me/drive/root/children/".$source."/content")
	//  ->upload($dest);

}
public function rungraphversionuser($source,$dest,$contract_id,$version){

$user_accessToken =$this->Token;
//print_r($user_accessToken);
$graph = new Graph();
$graph->setAccessToken($user_accessToken);
 //$this->sharefolder($version);  
//	echo($source);
//	echo($dest);
	$newsource=$source."-".$version;
	$graph->createRequest("PUT", "/me/drive/root:/contracts/".$contract_id."/".$newsource.":/content")
      ->upload($dest.'/'.$newsource);
	//$graph->createRequest("PUT", "/me/drive/root/children/".$source."/content")
	//  ->upload($dest);

}
public function sharefolder($name){
$user_accessToken = $this->Token;
$link = 'https://graph.microsoft.com/v1.0/me/drive/root/children';
//$name='GAG';
$data = [
    "name"   => $name,
    "folder" => ["childCount" => '0']
];

$headers = [
    'Authorization: Bearer '.$user_accessToken,
    'Content-Type: application/json'
];

$curl=curl_init(); 
curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
curl_setopt($curl,CURLOPT_URL,$link);
curl_setopt($curl,CURLOPT_CUSTOMREQUEST,'POST');
curl_setopt($curl,CURLOPT_HEADER,false);
curl_setopt($curl,CURLOPT_HTTPHEADER, $headers);
curl_setopt($curl,CURLOPT_POSTFIELDS, json_encode($data));
curl_setopt($curl,CURLOPT_SSL_VERIFYPEER,0);
curl_setopt($curl,CURLOPT_SSL_VERIFYHOST,0);

$out = curl_exec($curl); 
$codeCurl = curl_getinfo($curl,CURLINFO_HTTP_CODE);
curl_close($curl);
}
public function download_updatefile_simple($contract_id,$userfilename){
$user_accessToken =$this->Token;
//print_r($user_accessToken);
$graph = new Graph();
$graph->setAccessToken($user_accessToken);

$target_dir = get_upload_path_by_type('contract') . $contract_id . '/'.$userfilename;
	//"/me/drive/root:/".$project_id."/".$source.":/content")
$graph->createRequest("GET", "/me/drive/root:/contracts/".$contract_id."/".$userfilename.":/content")
    ->download($target_dir);
}

public function download_updatefile($project_id,$userfilename,$subfolder='project'){
$user_accessToken =$this->Token;
//print_r($user_accessToken);
$graph = new Graph();
$graph->setAccessToken($user_accessToken);

$target_dir = get_upload_path_by_type($subfolder) . $project_id . '/'.$userfilename;
	//"/me/drive/root:/".$project_id."/".$source.":/content")
if($subfolder=='oppositeparty'){
	$graph->createRequest("GET", "/me/drive/root:/person_company/".$project_id."/".$userfilename.":/content")
       ->download($target_dir);
	}elseif($subfolder=='customer'){
		$graph->createRequest("GET", "/me/drive/root:/companies/".$project_id."/".$userfilename.":/content")
       ->download($target_dir);
	}else{
$graph->createRequest("GET", "/me/drive/root:/UAT_legal_documents/".$project_id."/".$userfilename.":/content")
    ->download($target_dir);
}
}
public function download_updateversionfile($version,$contract_id,$currentfilename,$userfilename){
$user_accessToken =$this->Token;
$graph = new Graph();
$graph->setAccessToken($user_accessToken);

$target_dir = get_upload_path_by_type('contract') . $contract_id . '/'. $userfilename;
	//"/me/drive/root:/".$project_id."/".$source.":/content")
if($version==1){
$graph->createRequest("GET", "/me/drive/root:/contracts/".$contract_id."/".$currentfilename.":/content")
    ->download($target_dir);	
}else{
$graph->createRequest("GET", "/me/drive/root:/contracts/".$contract_id."/".$currentfilename.":/content")
    ->download($target_dir);	
}
}
public function createdocgraph(){
	$text_data=file_get_contents('C:\Users\Lab1-WS-5\Downloads/wordfile.docx');
$graph = new Graph();
$graph->setAccessToken($this->Token);
$data = $graph->createRequest('PUT', '/sites/09e0add7-675f-4694-a1d9-999be420a807/drives/b!163gCV9nlEah2Zmb5CCoBwohbFPtU9lDr3_IcXUI8qiDyOuVQMhDSohSZRBTS6YL/root:/wordfile1.docx:/content')
                ->addHeaders(array('Content-Type' => 'text/plain'))
                ->attachBody($text_data)
                ->setReturnType(Model\User::class)
                ->execute();
}
	
public function getweburl($contract_id,$filename){
	$user_accessToken = $this->Token;
//print_r($user_accessToken);
$graph = new Graph();
$graph->setAccessToken($user_accessToken);
	$link = $graph->createRequest("GET", "/me/drive/root:/contracts/".$contract_id."/".$filename)
    ->addHeaders(["Content-Type" => "application/json"])
    ->setReturnType(Microsoft\Graph\Model\DriveItem::class)
    ->execute();
return $link->getWebUrl();
 }

public function getsitelink($contract_id,$filename){
$user_accessToken = $this->Token;
//print_r($user_accessToken);
$graph = new Graph();
$graph->setAccessToken($user_accessToken);
	$link = $graph->createRequest("POST", "/me/drive/root:/contracts/" . $contract_id ."/". $filename . ":/createLink")
    ->addHeaders(["Content-Type" => "application/json"])
    ->setReturnType( Microsoft\Graph\Model\SharingLink::class)
    ->execute();

    return($link);
}
}
?>