<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Casediary_model extends App_Model
{
	public function __construct()
    {
        parent::__construct();
       
        $this->load->model('court_model');
        $this->load->model('court_type_model');
        $this->load->model('oppositeparty_model');
        $this->load->model('partytype_model');
        $this->load->model('hallnumber_model');
		 $this->load->model('document_types_model');
        $this->load->model('case_nature_model');
        $this->load->model('court_instance_model');
		$this->load->model('ipcategory_model'); 
    }

     public function delete_case_update($note_id)
    {
        $this->db->where('id', $note_id);
        $note = $this->db->get('tblproject_updates')->row();
        if ($note->addedfrom != get_staff_user_id() && !is_admin()) {
            return false;
        }
        $this->db->where('id', $note_id);
        $this->db->delete('tblproject_updates');
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
    public function edit_case_update($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblproject_updates', array(
            'content' => nl2br($data['content'])
        ));
        if ($this->db->affected_rows() > 0) {  
            return true;
        }
        return false;
    }
    public function save_case_update($data, $project_id)
    {
        
        $this->db->insert(db_prefix() . 'project_updates', [
            'addedfrom'   => get_staff_user_id(),
            'content'    => $data['content'],
            'rel_id' => $project_id,
            'dateadded' => date('Y-m-d H:i:s'),
            'rel_type' => $data['rel_type']
        ]);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            return true;
        }

        return false;
    }

    public function get_case_updates($case_id,$rel_type)
    {
        $this->db->where('rel_id', $case_id);
        $this->db->where('rel_type', $rel_type);
        return $this->db->get('tblproject_updates')->result_array();
    }


    public function get_casenatures($id = '')
    {
        return $this->case_nature_model->get($id);
    }

   
     /**
     * @param  integer ID (optional)
     * @return mixed
     * Get Court object based on passed id if not passed id return array of all types
     */
    public function get_courts($id = '')
    {
        return $this->court_model->get($id);
		 
    }
    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get Court Type object based on passed id if not passed id return array of all types
     */
    public function get_court_types($id = '')
    {
        return $this->court_type_model->get($id);
    }
    public function get_oppositeparty($id = '')
    {
        return $this->oppositeparty_model->get($id);
    }
    public function get_partytypes($id = '')
    {
        return $this->partytype_model->get($id);
    }


    public function delete_court($id='')
    {
        return $this->court_model->delete($id);
    }

    public function delete_case_nature($id='')
    {
        return $this->case_nature_model->delete($id);
    }

     public function delete_hearing_reference($id='')
    {
        return $this->hearing_reference_model->delete($id);
    }

    public function delete_court_region($id='')
    {
        return $this->court_region_model->delete($id);
    }

    public function delete_hall_number($id='')
    {
        return $this->hallnumber_model->delete($id);
    }


    public function update_court($data, $id)
    {
        return $this->court_model->update($data, $id);
    }

     public function update_case_nature($data, $id)
    {
        return $this->case_nature_model->update($data, $id);
    }

    public function add_new_court($data)
    {
        return $this->court_model->add($data);
    }

    public function add_new_court_instance($data)
    {
        return $this->court_instance_model->add($data);
    }

    

     public function add_case_nature($data)
    {
        return $this->case_nature_model->add($data);
    }

     /**
     * Add new contract type
     * @param mixed $data All $_POST data
     */
    public function add_new_hearing_reference($data)
    {
        return $this->hearing_reference_model->add($data);
    }

     /**
     * Add new contract type
     * @param mixed $data All $_POST data
     */
    public function add_new_CourtType($data)
    {
        return $this->court_type_model->add($data);
    }

     public function add_new_hallnumber($data)
    {
        return $this->hallnumber_model->add($data);
    }

     /**
     * Add new contract type
     * @param mixed $data All $_POST data
     */
    public function add_new_court_regions($data)
    {
        return $this->court_region_model->add($data);
    }

     public function add_new_oppositeparty($data)
    {
        return $this->oppositeparty_model->add($data);
    }

    public function add_new_partytype($data)
    {
        return $this->partytype_model->add($data);
    }
	 public function add_document_type($data)
    {
        return $this->document_types_model->add($data);
    }

    

     /**
     * Add new Area Descriptions
     * @param mixed $data All $_POST data
     */
    public function add_new_court_degree($data)
    {
        return $this->court_degree_model->add($data);
    }

    public function update_court_type($data, $id)
    {
        return $this->court_type_model->update($data, $id);
    }

     public function update_hearing_reference($data, $id)
    {
        return $this->hearing_reference_model->update($data, $id);
    }

    public function update_court_region($data, $id)
    {
        return $this->court_region_model->update($data, $id);
    }


    public function get_matter_templates_by_case_type($casetype){

        return $this->db->select('name,id')
                ->from('tblcasetemplates')
                ->where('case_type',$casetype)
                ->get()->result_array();
    }

    public function get_scopes($case_id)
    {
        $this->db->where('case_id', $case_id);
        return $this->db->get('tblcase_scopes')->result_array();
    }

      public function add_scope($data){
		  $data['addedfrom']   = get_staff_user_id();
           $data['dateadded'] = date('Y-m-d H:i:s');
        $this->db->insert('tblcase_scopes',$data);
        return true;
    }

    public function edit_scope($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblcase_scopes', array(
            'scope_description' => nl2br($data['description'])
        ));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
   
    public function delete_scope($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcase_scopes');
        //if ($this->db->affected_rows() > 0) {
            return true;
        //}

        //return false;
    }


     public function clients_bd_report($id = '', $where = array())
    {
        $this->db->select('*,tblleads.name, tblleads.id,tblleads_status.name as status_name,tblleads_sources.name as source_name');
        $this->db->join('tblleads_status', 'tblleads_status.id=tblleads.status', 'left');
        $this->db->join('tblleads_sources', 'tblleads_sources.id=tblleads.source', 'left');
        if ($this->input->post('view_status')) {
            //array_push($where, 'AND status IN ('.implode(',',$this->input->post('view_status')).')');
            $statuses = implode(',',$this->input->post('view_status'));
            $this->db->where_in('status',$this->input->post('view_status'));
        }

       


        $this->db->where($where);

        if ($this->input->post()) {
            $from_date = to_sql_date($this->input->post('staff_report_from_date'));
            $to_date   = to_sql_date($this->input->post('staff_report_to_date'));
            if ($this->input->post('client')) {
                $this->db->where('client_id ',$this->input->post('client'));
            }
        }
        if (isset($to_date) && isset($from_date)) {
            $this->db->where('dateadded >=',$from_date);
            $this->db->where('dateadded <=',$to_date);
        }

        /*if ($this->input->post('view_assigned')) {
            $assignees = $this->input->post('view_assigned');
           $this->db->where(' tblleads.id IN (SELECT lead_id FROM tblleadassignees WHERE staffid = '.$assignees .')');
        }*/
        
        $leads = $this->db->get('tblleads')->result_array();
        $i     = 0;
        /*foreach ($leads as $lead) {
            $leads[$i]['assignees'] = $this->get_lead_assignees($lead['id']);
            $i++;
        }*/

        return $leads;

    }

    public function get_all_tasks(){
        $this->db->select('id,name');
        return $this->db->get('tbltasks')->result_array();
    }

    public function get_communication_center($case_id)
    {
        $this->db->order_by('date','DESC');
        $this->db->where('case_id', $case_id);

        $results = $this->db->get('tblcase_communication_center')->result_array();
        $i=0;
        foreach ($results as $result) { 
            $results[$i]['attachments'] = $this->get_communication_files($result['id']);
            $i++;
        }
        return $results;
    }

     public function get_communication_center_by_id($id)
    {
        $this->db->where('id', $id);
        $result = $this->db->get('tblcase_communication_center')->row();
        $result->attachments = $this->get_communication_files($id);
        return $result;
    }
    

    public function get_communication_files($communication_id)
    {
        $this->db->where('communication_id', $communication_id);
        $s = $this->db->get('tblproject_files')->result_array();
        return $s;
    }

    public function delete_communication($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcase_communication_center');
        //if ($this->db->affected_rows() > 0) {
            return true;
        //}

        //return false;
    }
	public function delete_document_type($id='')
    {
        return $this->document_types_model->delete($id);
    }
    public function get_details($id = '',$project_id='',$type='', $where = array())
    {
        $this->db->select('tblcase_details.*');
        $this->db->join('tblprojects', 'tblprojects.id=tblcase_details.project_id', 'left');
       
        $this->db->where($where);
        if(is_numeric($project_id)){
            $this->db->where('project_id',$project_id);

        }
		 if($type != '' ){

            $this->db->where('instance_id',$type);   

        }
       /* if($type != '' ){
            $this->db->where('details_type',$type);   
        }*/

        if(is_numeric($id)){
            $this->db->where('id',$id);
            $instance = $this->db->get('tblcase_details')->row(); 
            $instance->lawyer_assignees   = get_all_assignees('lead',$id);
        }

        $leads = $this->db->get('tblcase_details')->row();

        return $leads;

    }

    
    

     public function update_project_table_data($data,$id){
        $affectedRows = 0;  
        $data['pc_regstrn_date'] = (isset($data['pc_regstrn_date']) ? to_sql_date($data['pc_regstrn_date']) : '' );
        $this->db->where('id', $id);
        $this->db->update('tblprojects', $data);
        if ($this->db->affected_rows() > 0) { 
    
            return true;
        }
        return false;
    }
  public function add_case_details_data($data){
        $data['dateadded']   = date('Y-m-d H:i:s');
        $data['addedfrom']     = get_staff_user_id();
        $data['details_type']  = get_court_instance_name_by_id($data['instance_id']);
	  if (!empty($data['stage_applicationdt'])) {
            $data['stage_applicationdt'] = to_sql_date($data['stage_applicationdt'],true);
        } else {
            unset($data['stage_applicationdt']);
        }
		 if (!empty($data['stage_registrationdt'])) {
            $data['stage_registrationdt'] = to_sql_date($data['stage_registrationdt'],true);
        } else {
            unset($data['stage_registrationdt']);
        }
	   if (!empty($data['stage_courtfeedt'])) {
            $data['stage_courtfeedt'] = to_sql_date($data['stage_courtfeedt'],true);
        } else {
            unset($data['stage_courtfeedt']);
        }
	  // $pdata['claiming_amount']=$data['claiming_amount'];
	   
	  
	 
	    $project_id=$data['project_id'];
      
	   if(isset($data['lawyer_id'])){
             $assignded_ids = $data['lawyer_id'];
           // $data['opposite_party'] =  implode(",",$data['opposite_party']);  
		     $data['lawyer_id'] = json_encode($data['lawyer_id']);
			
        }
       
        $this->db->insert('tblcase_details', $data);
        $inserted_id = $this->db->insert_id();
        if ($inserted_id) { 
			$project_stage=$this->db->get_where('tblprojects',array('id'=>$data['project_id']))->row()->project_stage;
			if($data['instance_id']!=$project_stage){
				$pdata['project_stage']=$data['instance_id'];
				//$pdata['case_registrationdt']=date('Y-m-d');
				$pdata['current_application_no']=$data['stage_requestno'];
				$pdata['current_application_date']=isset($data['stage_applicationdt']) ? to_sql_date($data['stage_applicationdt'],true) : '' ;
				$pdata['case_registrationdt']=isset($data['stage_registrationdt']) ?  to_sql_date($data['stage_registrationdt'],true) : '' ;
				if($data['instance_id']==5){
			 $pdata['execution_amount']=$data['execution_amount'];
			 }elseif($data['instance_id']==1){
				$pdata['claiming_amount']=$data['claiming_amount'];
			}
				$pdata['current_case_number']=$data['case_number'];
				
				//$pdata['current_judgement_date']=null;
				//$pdata['current_stage_status']=0;
				//$pdata['isjudgementnotified']=0;
				$this->db->where('id',$data['project_id']);
        		$this->db->update('tblprojects', $pdata);
			}
			 
        if ($this->db->affected_rows() > 0) 
    
              assign_all($assignded_ids,$data['details_type'],$inserted_id,$project_id);
			 log_activity('New Project Stage Created [Case :'.$data['project_id'].' ID: ' . $inserted_id . ']');

 $this->projects_model->log_activity($data['project_id'], 'project_activity_stage_added', $data['stage_requestno'].'-'._l(get_court_instance_name_by_id($data['instance_id'])));
			return true;
        }
        return false;
    }
  
    public function update_case_details_data($data,$id){
        $affectedRows = 0;
		 $data['details_type']  = get_court_instance_name_by_id($data['instance_id']);
		  $project_id=$data['project_id'];
       
		if(isset($data['lawyer_id'])){
             $assignded_ids = $data['lawyer_id'];
           // $data['opposite_party'] =  implode(",",$data['opposite_party']);  
		     $data['lawyer_id'] = json_encode($data['lawyer_id']);
			
        }
		if (!empty($data['stage_applicationdt'])) {
            $data['stage_applicationdt'] = to_sql_date($data['stage_applicationdt']);
        } else {
            unset($data['stage_applicationdt']);
        }
		 if (!empty($data['stage_registrationdt'])) {
            $data['stage_registrationdt'] = to_sql_date($data['stage_registrationdt']);
        } else {
            unset($data['stage_registrationdt']);
        }
		 if (!empty($data['stage_courtfeedt'])) {
            $data['stage_courtfeedt'] = to_sql_date($data['stage_courtfeedt'],true);
        } else {
            unset($data['stage_courtfeedt']);
        }
		//$pdata['claiming_amount']=$data['claiming_amount'];
	 	
        $this->db->where('id', $id);
        $this->db->update('tblcase_details', $data);
        if ($this->db->affected_rows() > 0) {
			$project_stage=$this->db->get_where('tblprojects',array('id'=>$data['project_id']))->row()->project_stage;
			if($data['instance_id']!=$project_stage){
					$pdata['project_stage']=$data['instance_id'];
				//$pdata['case_registrationdt']=date('Y-m-d');
				$pdata['current_application_no']=$data['stage_requestno'];
				$pdata['current_application_date']=isset($data['stage_applicationdt']) ? to_sql_date($data['stage_applicationdt'],true) : '' ;
				$pdata['case_registrationdt']=isset($data['stage_registrationdt']) ?  to_sql_date($data['stage_registrationdt'],true) : '' ;
				if($data['instance_id']==5){
			 $pdata['execution_amount']=$data['execution_amount'];
			 }elseif($data['instance_id']==1){
				$pdata['claiming_amount']=$data['claiming_amount'];
			}
				$pdata['current_case_number']=$data['case_number'];
				 $this->db->where('id',$data['project_id']);
        		$this->db->update('tblprojects', $pdata);
			}else{
				$pdata['current_application_no']=$data['stage_requestno'];
				$pdata['current_application_date']=isset($data['stage_applicationdt']) ? to_sql_date($data['stage_applicationdt'],true) : '' ;
				$pdata['case_registrationdt']=isset($data['stage_registrationdt']) ?  to_sql_date($data['stage_registrationdt'],true) : '' ;
                $pdata['current_case_number']=$data['case_number'];
				if($data['instance_id']==5){
			 $pdata['execution_amount']=$data['execution_amount'];
			 }elseif($data['instance_id']==1){
				$pdata['claiming_amount']=$data['claiming_amount'];
			}
				//$pdata['status']=$data['project_stage_staus'];
				 $this->db->where('id',$data['project_id']);
        		$this->db->update('tblprojects', $pdata);
			}
			/*if($data['instance_id']==5){
			update_project_amounts($pdata,$project_id);
			 }elseif($data['instance_id']==1){
				update_project_amounts($pdata1,$project_id);
			}*/
			
             assign_all($assignded_ids,$data['details_type'],$id,$project_id);
			log_activity('Project Stage Updated [Case :'.$data['project_id'].' ID: ' . $id . ']');

 			$this->projects_model->log_activity($data['project_id'], 'project_activity_stage_updated', $data['case_number'].'-'._l(get_court_instance_name_by_id($data['instance_id'])));
            return true;
        }
        return false;
    }
    public function get_project_instances_by_id($id='', $where = array())
    {
        $this->db->select('tblcase_details.*,tblproject_instances.instance_name as instance_name,tblproject_instances.instance_slug as instance_slug,tblcase_details.id as id,tblcourts.name as courtname,tblcase_natures.name as case_nature_name');
        $this->db->join('tblprojects', 'tblprojects.id=tblcase_details.project_id', 'left');
        $this->db->join('tblproject_instances', 'tblproject_instances.instance_slug=tblcase_details.details_type', 'left');
        $this->db->join('tblcourts', 'tblcase_details.court_id=tblcourts.id', 'left');
        $this->db->join('tblcase_natures', 'tblcase_natures.id=tblcase_details.instance_casenature', 'left');
       
        $this->db->where($where);
        if(is_numeric($id)){
            $this->db->where('tblcase_details.id',$id);
            $instance= $this->db->get('tblcase_details')->row();
            return $instance;


        }
        $instances= $this->db->get('tblcase_details')->result_array();
        $i         = 0;
        
        return $instances;

    }
     public function get_project_instances_by_project_id_old($project_id='', $where = array())
    {
        $this->db->select('tblcase_details.*,tblproject_instances.instance_name as instance_name,tblproject_instances.instance_slug as instance_slug,tblcase_details.id as id');
        $this->db->join('tblprojects', 'tblprojects.id=tblcase_details.project_id', 'left');
        $this->db->join('tblproject_instances', 'tblproject_instances.instance_slug=tblcase_details.details_type', 'left');
       
        $this->db->where($where);
        if(is_numeric($project_id)){
            $this->db->where('project_id',$project_id);

        }
        $instances = $this->db->get('tblcase_details')->result_array();
        $i         = 0;
        
        foreach ($instances as $instance) {
            $instances[$i]['lawyers_assigned'] = get_all_assignees($instance['details_type'],$instance['id']);
            $i++;
        }

        return $instances;

    }
	 public function get_project_instances_by_project_id($project_id='', $where = array())
    {
        $this->db->select('tblcase_details.*,tblproject_instances.instance_name as instance_name,tblproject_instances.instance_slug as instance_slug,tblcase_details.id as id,tblcase_details.id as id,tblcourts.name as courtname,tblcase_natures.name as case_nature_name,tblcase_details.details_type as detailtype');
        $this->db->join('tblprojects', 'tblprojects.id=tblcase_details.project_id', 'left');
        $this->db->join('tblproject_instances', 'tblproject_instances.id=tblcase_details.instance_id', 'left');
        $this->db->join('tblcourts', 'tblcase_details.court_id=tblcourts.id', 'left');
        $this->db->join('tblcase_natures', 'tblcase_natures.id=tblcase_details.instance_casenature', 'left');

        $this->db->where($where);
        if(is_numeric($project_id)){
            $this->db->where('project_id',$project_id);

        }
        $instances = $this->db->get('tblcase_details')->result_array();
        $i         = 0;
        
        /*foreach ($instances as $instance) {
            $instances[$i]['lawyers_assigned'] = get_all_assignees($instance['details_type'],$instance['id']);
            $i++;
        }*/

        return $instances;
 

    }
  public function verify_status($id,$stable,$status)
    {
        $this->db->where('id', $id);
        $this->db->update($stable, array(
            'active' => $status,
            ));

        if ($this->db->affected_rows() > 0) {
           // logActivity('Installment Verified  [ID: ' . $id . ' : ' . $status . ']');

            return true;
        }

        return false;
    }
	 public function get_document_types($id = '')
    {
        return $this->document_types_model->get($id);
    }
	 public function get_document_typesproject($id = '')
    {
        return $this->document_types_model->getprojecttypes($id);
    }
	 public function get_document_types_bycategory($cid = '')
    {
        return $this->document_types_model->getbycategory($cid);
    }
	 public function update_document_type($data, $id)
    {
        return $this->document_types_model->update($data, $id);
    }
	public function get_courtorders($project_id,$status='')
    { if($status!='')
	{
		 $this->db->where('active', $status);
	}
        $this->db->where('project_id', $project_id);
        return $this->db->get('tblcourt_orders')->result_array();
    }
	 public function get_courtordernames($id='')
    {
      $this->db->where('active', '1');
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblcourt_ordernames')->row();
        }
        $types = $this->db->get('tblcourt_ordernames')->result_array();
        return $types;
      }

     public function add_courtorder($data){
		 $data['order_date']=to_sql_date($data['order_date']);
		  $data['end_date']=to_sql_date($data['end_date']);
        $this->db->insert('tblcourt_orders',$data);
        return true;
    }

    public function edit_courtorder($data, $id)
    {
		 $data['order_date']=to_sql_date($data['order_date']);
		  $data['end_date']=to_sql_date($data['end_date']);
        $this->db->where('id', $id);
        $this->db->update('tblcourt_orders',$data);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
   
    public function delete_courtorder($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcourt_orders');
        //if ($this->db->affected_rows() > 0) {
            return true;
        //}

        //return false;
    }
    public function add_hearing_judgement($data)
    {
        
        
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedby'] = get_staff_user_id();
		$data['judgement_date'] = to_sql_date($data['judgement_date'], true);
        $data['judge_stage_status']=1;
        $this->db->insert('tblproject_judgement', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
			if($data['judgement_ruling']=='judgement'){
				$this->db->where('id', $data['stage_hearing_id']);
		$this->db->where('project_id', $data['project_id']);
        $this->db->update('tblhearings',array('hearing_stage_status'=>$data['judge_stage_status']));
			 $this->db->where('instance_id', $data['stage_id']);
		$this->db->where('project_id', $data['project_id']);
        $this->db->update('tblcase_details',array('stage_status'=>$data['judge_stage_status']));
		$this->db->where('id', $data['project_id']);
        $this->db->update('tblprojects',array('current_stage_status'=>$data['judge_stage_status'],'current_judgement_date'=>$data['judgement_date']));
			}
            log_activity('New Judgement / Ruling  Added [' ._l(get_court_instance_name_by_id( $data['stage_id'])). ']');
			$this->projects_model->log_activity($data['project_id'], 'project_activity_hearing_judgement_added', _l(get_court_instance_name_by_id($data['stage_id'])));
			//print_r($insert_id);
            return $insert_id;
        }

        return false;
    }
	   public function update_hearing_judgement($data, $id)
    {
        $affectedRows      = 0;
        $hearing_sts = $this->db->get_where('tblproject_judgement',array('id'=>$id))->row();
		
        $data['judgement_date'] = to_sql_date($data['judgement_date'],true);
        $this->db->where('id', $id);
        $this->db->update('tblproject_judgement', $data);
        if ($this->db->affected_rows() > 0) {
		/* $this->db->where('instance_id', $hearing_sts->h_instance_id);
		$this->db->where('project_id', $hearing_sts->project_id);
        $this->db->update('tblcase_details',array('stage_status'=>$data['hearing_stage_status']));*/
            log_activity('Hearing Judgement Updated for Stage  [' ._l(get_court_instance_name_by_id( $hearing_sts->stage_id)) . ']');
			$this->projects_model->log_activity($data['project_id'], 'project_activity_hearing_judgement_updated', _l(get_court_instance_name_by_id($data['stage_id'])));
            return true;
        }

        return false;
    }
    public function delete_judgement($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblproject_judgement');
        if ($this->db->affected_rows() > 0) {
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'hearings');
            $this->db->delete('tblcustomfieldsvalues');

         /*   $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'hearings');
            $this->db->delete('tblfiles');*/
            //$attachments = $this->db->get('tblfiles')->result_array();
            //foreach ($attachments as $attachment) {
                //$this->delete_hearing_attachment($attachment['id']);
           // }
            
            log_activity('Judgement / Ruling  Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    public function update_party_type($data, $id)

    {

        return $this->partytype_model->update($data, $id);

    }


    public function delete_partytype($id='')
    {
        return $this->partytype_model->delete($id);
    }
	/* Hearing/judgement */

	
  public function delete_courtinstances($id='')
    {
        return $this->court_instance_model->delete($id);
    }  

	public function update_hall_number($data, $id)

    {

        return $this->hallnumber_model->update($data, $id);

    }
 public function update_court_instance($data, $id)
    {
        return $this->court_instance_model->update($data, $id);
    }
 public function remove_trade_mark_logo($project_id){

        $path = get_upload_path_by_type('projects') . $project_id . '/';
        $file_name = $this->db->get_where('tblprojects',array('id'=>$project_id))->row()->ip_logo;
        $fullPath =$path.$file_name;
        if (file_exists($fullPath)) {
            unlink($fullPath); 
        }
       /* $fullPath2 =$path.'small_'.$file_name; 
        if (file_exists($fullPath2)) {
            unlink($fullPath2); 
        }*/
        $this->db->where('id',$project_id);
        $this->db->update('tblprojects',array('ip_logo'=>''));
        return true;
    }
	
	public function add_new_ipcategory($data)
    {
        return $this->ipcategory_model->add($data);
    }
	public function add_new_ipsubcategory($data)
    {
        return $this->ipcategory_model->add_subcategory($data);
    }
 	public function get_ipcategories($id = '')
    {
        return $this->ipcategory_model->get($id);
		 
    }
	 public function get_ipsubcategories($id = '')
    {
        return $this->ipcategory_model->get_subcategory($id);
		 
    }
	public function delete_ipcategory($id='')
    {
        return $this->ipcategory_model->delete($id);
    }
	public function delete_ipsubcategory($id='')
    {
        return $this->ipcategory_model->delete_subcategory($id);
    }
	public function update_ipcategory($data, $id)
    {
        return $this->ipcategory_model->update($data, $id);
    }
 	public function update_ipsubcategory($data, $id)
    {
        return $this->ipcategory_model->update_subcategory($data, $id);
    }
}