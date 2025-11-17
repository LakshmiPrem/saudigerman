<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Legal_requests extends REST_Controller {
    
	/**
     * Get All Data from this method.
     *
     * @return Response
    */
    public function __construct() {
       parent::__construct();
       //$this->load->database();
       $this->load->model('api_casediary_model');
       $this->load->model('projects_model');                
       $this->load->library('Authorization_Token');
       $this->load->library('app_tabs');
       $this->load->library('app_object_cache');

        $this->load->library('mails/App_mail_template');
        $this->load->library('merge_fields/app_merge_fields');
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
            if(!empty($this->input->get('request_id'))){
                $subject = $this->input->get('request_id'); // This is subject of the email
                $data = $this->api_casediary_model->check_with_request_id($subject);
            }else{
                $client_id = $this->input->get('client_id'); // This is client id
                $data = $this->api_casediary_model->get_legal_requests($client_id);
            }
            
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
            $project_members[] = get_staff_user_id(); //$this->api_casediary_model->value($this->input->post('project_members', TRUE));
            $contact_id = 0;
            $contact_id_query = $this->db->get_where('tblcontacts',['userid'=>$this->input->post('branch_id'),'is_primary'=>1]);
            if($contact_id_query->num_rows() > 0){
                $contact_id = $contact_id_query->row()->id;
            }
            $insert_data = [
                'subject' => $this->input->post('subject', TRUE),
                'department' => $this->input->post('department_id', TRUE),
                'service' => $this->api_casediary_model->value($this->input->post('service_type_id', TRUE)),
                'userid' => $this->api_casediary_model->value($this->input->post('branch_id', TRUE)),

                'contactid' => $contact_id,
                'assigned' => get_staff_user_id(),//$this->api_casediary_model->value($this->input->post('assigned', TRUE)),
                'message' => $this->api_casediary_model->value($this->input->post('message', TRUE))

               /* 'cc' => $this->api_casediary_model->value($this->input->post('cc', TRUE)),
                'tags' => $this->api_casediary_model->value($this->input->post('tags', TRUE)),
                'priority' => $this->api_casediary_model->value($this->input->post('priority', TRUE)),
                'project_id' => $this->api_casediary_model->value($this->input->post('project_id', TRUE)),*/
             ];
                // insert data                    
            $this->load->model('tickets_model');
            $inserted = $this->tickets_model->add($insert_data);

            if ($inserted)
            {
               
              
                //$input = json_encode($input);             
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'Request Created Successfully',
                    //'project_id' => $input['project_id'],
                    'submitted_data'  => $input,
                ], REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
            }
            else
            {
                // Set the response and exit
                $this->response([
                    'status' => FALSE,
                    'message' => 'Failed'
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