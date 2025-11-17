<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Api_casediary_model extends APP_Model
{
    private $project_settings;

    public function __construct()
    {
        parent::__construct();

        $project_settings       = array(
            'available_features',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'comment_on_tasks',
            'view_task_comments',
            'view_task_attachments',
            'view_task_checklist_items',
            'upload_on_tasks',
            'view_task_total_logged_time',
            'view_finance_overview',
            'upload_files',
            'open_discussions',
            'view_milestones',
            'view_gantt',
            'view_timesheets',
            'view_activity_log',
            'view_team_members',
            'hide_tasks_on_main_tasks_table',
        );
        //$this->load->model('litigation_model');
        //$this->load->model('hallnumber_model');
       // $this->load->model('casestatus_model');
       // $this->load->model('partytype_model');
       // $this->load->model('area_description_model');
        //$this->load->model('oppositeparty_model');
       // $this->load->model('lawyer_attending_model');
        $this->project_settings = '';//do_action('project_settings', $project_settings);
    }

    
    function create_token ($customer_id) {
        $this->load->database();

        // ***** Generate Token *****
        $char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY!@#%";
        $token = '';
        for ($i = 0; $i < 47; $i++) $token .= $char[(rand() % strlen($char))];

        // ***** Insert into Database *****
        //$sql = "INSERT INTO api_tokens SET `token` = ?, customer_id = ?;";
        //$this->db->query($sql, [$token, $customer_id];
        $this->db->where('staffid',$customer_id);
        $this->db->update('tblstaff',array('auth_token'=>$token));    
        //return array('http_code' => 200, 'token' => $token);
        return $token;
    }   

    public function get($client_id,$where = array())
    {
        $this->db->select('tblprojects.id,tblprojects.name,tblclients.company as client');
        $this->db->from('tblprojects');
        $this->db->join('tblclients', 'tblclients.userid=tblprojects.clientid');
        //$this->db->join('tblproject_members', 'tblproject_members.project_id=tblprojects.id','left');
        //$staff_id = get_staff_user_id();
        //if(!is_admin($staff_id)){
          //  $this->db->where('tblproject_members.staff_id',$staff_id);
        //}
        $this->db->where('tblprojects.clientid',$client_id);
        $this->db->order_by('tblprojects.name', 'ASC');

        return $this->db->get()->result_array();
    }

    public function get_legal_requests($client_id,$where = array())
    {
        $this->db->select('tbltickets.ticketid as id,tbltickets.subject as name,tblclients.company as client,tblclients.userid as client_id');
        $this->db->from(db_prefix() . 'tickets');
        $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'tickets.department');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'tickets.userid', 'left');
        $staff_id = get_staff_user_id();
        $this->db->where('tbltickets.userid',$client_id);
        //$this->db->where('tblprojects.id',$intval);
        $check = $this->db->get();
        return $check->result_array();
        
    }
    public function check_with_matter_id($subject,$where = array())
    {
        
        $delimiter = ' ';
		$subdelimiter = '##';

        $words = explode($delimiter, $subject);
        foreach ($words as $word){
            if($word != '' && strlen($word) > 0){
					   if(strpos($word, $subdelimiter) != false){
				 $subwords = explode($subdelimiter, $word);
				 foreach ($subwords as $subword){
				  if($subword != '' && strlen($subword) > 0){
					  $searchword=$subword;
					                $intval = (int)filter_var($word, FILTER_SANITIZE_NUMBER_INT);

                $this->db->select('tblprojects.id as matter_id,tblprojects.name as matter_title,tblclients.company as branch,tblclients.userid as branch_id');
                $this->db->from('tblprojects');
                $this->db->join('tblclients', 'tblclients.userid=tblprojects.clientid');
                //$this->db->join('tblproject_members', 'tblproject_members.project_id=tblprojects.id','left');
                $staff_id = get_staff_user_id();
                //if(!is_admin($staff_id)){
                   // $this->db->where('tblproject_members.staff_id',$staff_id);
                ///}
                $this->db->where('file_no',trim($searchword));
                //$this->db->where('tblprojects.id',$intval);
                $check = $this->db->get();
                if($check->num_rows() > 0){
					$result=$check->result_array();
					 return $result;
                     break;
                }
				 }
				 }
				}
				else{
				$searchword=$word;	
				$intval = (int)filter_var($word, FILTER_SANITIZE_NUMBER_INT);

                $this->db->select('tblprojects.id as matter_id,tblprojects.name as matter_title,tblclients.company as branch,tblclients.userid as branch_id');
                $this->db->from('tblprojects');
                $this->db->join('tblclients', 'tblclients.userid=tblprojects.clientid');
                //$this->db->join('tblproject_members', 'tblproject_members.project_id=tblprojects.id','left');
                $staff_id = get_staff_user_id();
                //if(!is_admin($staff_id)){
                   // $this->db->where('tblproject_members.staff_id',$staff_id);
                ///}
                $this->db->where('file_no',trim($searchword));
                //$this->db->where('tblprojects.id',$intval);
                $check = $this->db->get();
                if($check->num_rows() > 0){
					 return $check->result_array();
                     break;
                }
				}
  
            }
            

        }
        
        
    }
	 public function find_sub_matter_id($subject)
    {
        $delimiter = ' ';
		$subdelimiter = '##';
		$searchword='';
        $words = explode($delimiter, $subject);
		
        foreach ($words as $word){
               if($word != '' && strlen($word) > 0){
				   if(strpos($word, $subdelimiter) != false){
				 $subwords = explode($subdelimiter, $word);
			//	print_r($word);print_r($subwords);
				 foreach ($subwords as $subword){
				  if($subword != '' && strlen($subword) > 0){
					  $searchword=$subword;
					 
					          //  $intval = (int)filter_var($word, FILTER_SANITIZE_NUMBER_INT);

                $this->db->select('tblprojects.id as matter_id,tblprojects.name as matter_title,tblclients.company as branch,tblclients.userid as branch_id');
                $this->db->from('tblprojects');
                $this->db->join('tblclients', 'tblclients.userid=tblprojects.clientid');
                //$this->db->join('tblproject_members', 'tblproject_members.project_id=tblprojects.id','left');
                $staff_id = get_staff_user_id();
                //if(!is_admin($staff_id)){
                   // $this->db->where('tblproject_members.staff_id',$staff_id);
                ///}
                $this->db->where('file_no',trim($searchword));
                //$this->db->where('tblprojects.id',$intval);
                $check = $this->db->get();
                if($check->num_rows() > 0){
					//print_r($word);
                     return $word;
                     break;
                }
				 }
				 }
				}
				else{
				$searchword=$word;	
					        //  $intval = (int)filter_var($word, FILTER_SANITIZE_NUMBER_INT);

                $this->db->select('tblprojects.id as matter_id,tblprojects.name as matter_title,tblclients.company as branch,tblclients.userid as branch_id');
                $this->db->from('tblprojects');
                $this->db->join('tblclients', 'tblclients.userid=tblprojects.clientid');
                //$this->db->join('tblproject_members', 'tblproject_members.project_id=tblprojects.id','left');
                $staff_id = get_staff_user_id();
                //if(!is_admin($staff_id)){
                   // $this->db->where('tblproject_members.staff_id',$staff_id);
                ///}
                $this->db->where('file_no',trim($searchword));
                //$this->db->where('tblprojects.id',$intval);
                $check = $this->db->get();
                if($check->num_rows() > 0){
					//print_r($searchword);
                     return $searchword;
                     break;
                }
				}
				 
      
            }
            

        }
        
        
    }

     public function check_with_request_id($subject,$where = array())
    {
        
        $delimiter = ' ';

        $words = explode($delimiter, $subject);
        foreach ($words as $word){
            if($word != '' && strlen($word) > 0){
                $intval = (int)filter_var($word, FILTER_SANITIZE_NUMBER_INT);
                $this->db->select('tbltickets.ticketid as request_id,tbltickets.subject as subject,tblclients.company as branch,tblclients.userid as branch_id');
                $this->db->from(db_prefix() . 'tickets');
                $this->db->join(db_prefix() . 'departments', db_prefix() . 'departments.departmentid = ' . db_prefix() . 'tickets.department');
                $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'tickets.userid', 'left');
                $staff_id = get_staff_user_id();
                $this->db->where('request_no',trim($word));
                //$this->db->where('tblprojects.id',$intval);
                $check = $this->db->get();
                if($check->num_rows() > 0){
                     return $check->result_array();
                     break;
                }
            }
            

        }
        
        
    }

    


    
    public function _search_projects($q, $limit = 0, $where = false, $rel_type = null, $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'projects',
            'search_heading' => _l('projects'),
        ];

        $projects = has_permission('projects', '', 'view');
        // Projects
        $this->db->select('tblprojects.*');
        $this->db->from('tblprojects');
        
        $this->db->join('tblclients', 'tblclients.userid = tblprojects.clientid','LEFT'); 
           
        if ($where != false) {
            $this->db->where($where);
        }
        if (!_startsWith($q, '#')) {
            $this->db->where('(tblclients.company LIKE "%' . $q . '%"
                OR tblprojects.case_number LIKE "%' . $q . '%"
                OR tblprojects.name LIKE "%' . $q . '%"
                )');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                (SELECT id FROM tbltags WHERE name="' . strafter($q, '#') . '")
                AND tbltags_in.rel_type=\'projects\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        if ($limit != 0) {
            $this->db->limit($limit);
        }

        $this->db->order_by(db_prefix() . 'projects.name', 'ASC');
        $result['result'] = $this->db->get()->result_array();

        return $result;
    }

     /**
     * @param   array $_POST data
     * @return  integer Insert ID
     * Add new contract
     */
    public function add($data)
    {
       // print_r($data);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();
        if($data['related_to'] == 'matter'){
            $data['case_id'] = intval($data['matter_id']);    
        }else{
            $data['case_id'] = intval($data['request_id']);    
        }
        $data['date']    = to_sql_date($data['date'],true);
        //$data['mode'] = 'Email';
      //  print_r($_FILES['attachments']['name']);
       // print_r($_FILES['outlook_msg']['name']);
        $data['outlook_msg'] = json_encode($_FILES);
        $check_already_exported = total_rows('tblcase_communication_center',['related_to'=>$data['related_to'],'date'=>$data['date']]);
        unset($data['matter_id']);
        unset($data['request_id']);
        if($check_already_exported > 0){
            return false;
        }else{
            $this->db->insert('tblcase_communication_center', $data);
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                
                return $insert_id;
            }
        }
        return false;
    }

     public function get_clients($id = '', $where = array())
    {
        $this->db->select('tblclients.userid as id,tblclients.company as name');
        //$this->db->join('tblcountries', 'tblcountries.country_id = tblclients.country', 'left');
        //$this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid AND is_primary = 1', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblclients.userid', $id);
            $client = $this->db->get('tblclients')->row();

            /*if (get_option('company_requires_vat_number_field') == 0) {
                $client->vat = null;
            }*/

            return $client;
        }

        $this->db->order_by('company', 'asc');

        return $this->db->get('tblclients')->result_array();
    }

    public function get_case_types($id = '', $where = array())
    {
          
        
         return  $this->db->select('id,name')->from('tblmatter_types')->where('id !=',4)->where('id !=',6)->get()->result_array(); 

        /*  return array(
            array('id'=>'court_case','name'=>'Court Case'),
            array('id'=>'legal_consultancy','name'=>'Legal Consultancy'),
            array('id'=>'personal_law','name'=>'Personal Law'),
            array('id'=>'other_projects','name'=>'Other Projects'),
            array('id'=>'policecase','name'=>'Police Case'),
            array('id'=>'labour_case','name'=>'Labour Case'),
            array('id'=>'transfer_case','name'=>'Cases Transferred To Other Countries'),
        );*/
    }

    public function get_case_type_slug($id='')
    {
        if($id == ''){
            return 'projects';
        }else{
            return  $this->db->select('slug')->from('tblmatter_types')->where('id',$id)->get()->row()->slug; 
        }
    }

    public function get_service_types($id = '', $where = array())
    {
        
        $this->db->select('serviceid as id,name');
        $this->db->order_by('name', 'asc');

        return $this->db->get(db_prefix() . 'services')->result_array();
    }

    public function get_departments($id = '', $where = array())
    {
       $this->db->select('departmentid as id,name');
       $this->db->order_by('name', 'asc');

       return $this->db->get(db_prefix() . 'departments')->result_array(); 
    }

    public function get_export_projectfileno($project_id)
    {
        $this->db->select('file_no');
        $this->db->where('id', $project_id);
        $s = $this->db->get('tblprojects')->row()->file_no;
        return $s;
    }


    public function get_communication_files($communication_id)
    {
        $this->db->select('file_name');
        $this->db->where('communication_id', $communication_id);
        $s = $this->db->get('tblproject_files')->result_array();
        return $s;
    }

     public function value($value)
    {
        if($value){
            return $value;
        }else{
            return '';
        }
    }

}