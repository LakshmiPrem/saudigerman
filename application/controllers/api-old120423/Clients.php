<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Clients extends REST_Controller {
    
	/**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       //$this->load->database();
       $this->load->model('api_casediary_model');
       $this->load->library('Authorization_Token');

    }
    

    /**
     * Get All Data from this method.
     *
     * @return Response
    */
	public function index_get()
	{

        $token_res = $this->authorization_token->validateToken($this->input->request_headers('authorization'));
        if(!$token_res['status']){
            $this->response([
                'status' => FALSE,
                'message' => 'Authorization Failed.'
            ], REST_Controller::HTTP_UNAUTHORIZED); // NOT_FOUND (401) being the HTTP response code
        }else{
            $data = $this->api_casediary_model->get_clients();

            // Check if the data store contains
            if ($data)
            {
                // Set the response and exit
                $this->response($data, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'No data were found'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }
        
	}
    	
}