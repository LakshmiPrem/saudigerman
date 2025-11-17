<?php
   
require APPPATH . 'libraries/REST_Controller.php';
     
class Projects extends REST_Controller {
    
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
            $matter_id = $this->input->get('matter_id');// This is subject of the email
            $data = $this->api_casediary_model->check_with_matter_id($matter_id);

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
			$visibletabdet=$this->db->select('visible_tabs')->from('tblmatter_types')->where('id', $this->input->post('case_type_id', TRUE))->get()->row()->visible_tabs;
		$vtabs=explode(',',$visibletabdet);
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
                    
                    'settings' => array('available_features' =>$vtabs) ,
                ];
			//array("project_contracts", "communication_center", "project_activity")
                    if($project_members != ''){
                        $insert_data['project_members'] = $project_members;
                    }
                // insert data    
            $insert_data['datestart'] = $insert_data['start_date'];            
            $case_types = $this->db->select('*')->from('tblmatter_types')->get()->result_array(); 
            $slug_arr =[];
            foreach ($case_types as  $value) {
                $slug[$value['id']] = $value['slug'];
            } 
            
            $insert_data['case_type'] = $slug[$insert_data['case_type']];
			$inserted = $this->projects_model->add($insert_data); 
            if ($inserted)
            {
                //$input = json_encode($input);             
                // Set the response and exit
                $this->response([
                    'status'    => TRUE,
                    'message'   => 'Matter Created Successfully',
                    'matter_id' => $inserted,
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