<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Login extends REST_Controller {
    
	  /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       $this->load->model('Authentication_model');
       $this->load->model('Api_casediary_model');
        
    }
       
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post()
    {
        //header("Access-Control-Allow-Origin: *");
        $input = $this->input->post();
        $email = $input['email'];
        $password = $input['password'];
        
        $data = $this->Authentication_model->login($email, $password,'', true);
        if($data){
            //$token = $this->Api_casediary_model->create_token(get_staff_user_id());
            $response['status'] = TRUE;
            //$response['message'] = 'Login Success';
            $tokenData['id']   = get_staff_user_id();
            //$tokenData['role'] = 'admin';
            $tokenData['time'] = strtotime('now');

            $this->load->library('Authorization_Token');
            //$this->ObjectOfJwt = new Authorization_Token();
            $jwtToken = $this->authorization_token->GenerateToken($tokenData);
            $response['token'] = $jwtToken;
            $this->response($response, REST_Controller::HTTP_OK);

        }else{
             // Set the response and exit
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid User'
            ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
        }
        
    } 

    /*public function GetTokenData()
    {
        $received_Token = $this->input->request_headers('Authorization');
        try
        {
            $jwtData = $this->objOfJwt->DecodeToken($received_Toekn['Token']);
            echo json_encode($jwtData);
        }

        catch (Exception $e)
        {
            http_response_code('401');
            echo json_encode(array("status"=>false,"message"=>$e->getMessage()));
            exit;
        }
    }*/
     
   
}