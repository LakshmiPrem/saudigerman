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
                 $response['status'] = TRUE;
                 $response['branches'] = $data;
                // Set the response and exit
                $this->response($response, REST_Controller::HTTP_OK); // OK (200) being the HTTP response code
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
            $project_members[] = get_staff_user_id(); //$this->Api_model->value($this->input->post('project_members', TRUE));
            $insert_data = [
                    'name' => $this->input->post('matter_title', TRUE),
                    //'rel_type' => $this->input->post('rel_type', TRUE),
                    'clientid'  => $this->input->post('branch_id', TRUE),
                    'case_type' => $this->input->post('case_type_id', TRUE),
                    'billing_type' => 0,
                    'start_date' => _d(date('Y-m-d')),
                    'status' => 2,//$this->input->post('status', TRUE),
                    'description' => $this->api_casediary_model->value($this->input->post('description', TRUE)),
                    //'tags' => $this->api_casediary_model->value($this->input->post('tags', TRUE)),
                    
                    'settings' => array('available_features' => array( 'project_overview', 'project_milestones', 'project_gantt', 'project_tasks', 'project_expenses', 'project_tickets', 'project_timesheets', 'project_files', 'project_discussions', 'project_notes', 'project_activity')) 
                ];
                    if($project_members != ''){
                        $insert_data['project_members'] = $project_members;
                    }
                // insert data                    
            $inserted = $this->projects_model->add($insert_data); 

            if ($inserted)
            {
               
              
                //$input = json_encode($input);             
                // Set the response and exit
                $this->response([
                    'status' => TRUE,
                    'message' => 'Matter Created Successfully',
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
    	
}