<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Email extends REST_Controller {
    
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
            $client_id = $this->input->get('client_id');
            $data = $this->api_casediary_model->get($client_id);

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


    public function index_search($key = '')
    {
        $token_res = $this->authorization_token->validateToken($this->input->request_headers('authorization'));
        if(!$token_res['status']){
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Token.Authentication Failed.'
            ], REST_Controller::HTTP_UNAUTHORIZED); // NOT_FOUND (401) being the HTTP response code
        }else{
        $data = $this->api_casediary_model->_search_projects($key);

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



      
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_post()
    {
        $token_res = $this->authorization_token->validateToken($this->input->request_headers('authorization'));
        if(!$token_res['status']){
            $this->response([
                'status' => FALSE,
                'message' => 'Invalid Token.Authentication Failed.'
            ], REST_Controller::HTTP_UNAUTHORIZED); // NOT_FOUND (401) being the HTTP response code
        }else{

            $input = $this->input->post();
            $inserted = $this->api_casediary_model->add($input);


            if ($inserted)
            {
                if($input['related_to'] == 'matter'){
                    handle_communinication_attachment($inserted,$input['matter_id']);
                    $input['attachments'] = $this->api_casediary_model->get_communication_files($inserted);
                }
                
                //$input = json_encode($input);             
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => ' Email Exported Successfully',
                    //'project_id' => $input['project_id'],
                    'submitted_data'  => $input,
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Failed or Already Exported'
                ], REST_Controller::HTTP_NOT_FOUND); // NOT_FOUND (404) being the HTTP response code
            }
        }

    } 
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_put($id)
    {
        $input = $this->put();
        $this->db->update('items', $input, array('id'=>$id));
     
        $this->response(['Item updated successfully.'], REST_Controller::HTTP_OK);
    }
     
    /**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function index_delete($id)
    {
        $this->db->delete('items', array('id'=>$id));
       
        $this->response(['Item deleted successfully.'], REST_Controller::HTTP_OK);
    }
    	
}