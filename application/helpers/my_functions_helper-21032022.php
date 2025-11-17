<?php
defined('BASEPATH') or exit('No direct script access allowed');
function get_approval_record_status($projectid)
{
    $CI = &get_instance();
    if(is_numeric($projectid)){
        $query  = $CI->db->query('SELECT id FROM `tblexpense_approval_names` 
                                  WHERE overall_status NOT IN ? AND   tblexpense_approval_names.project_id = ?',array(array('0','R','4'),$projectid));
        if($query->num_rows() > 0){
            return false;
        }else{
            return true;
        }
        
    }
}
function get_approvalreference_record_status($projectid,$appid)
{
    $CI = &get_instance();
    if(is_numeric($projectid)){
        $query  = $CI->db->query('SELECT id FROM `tblexpense_approval_names` 
                                  WHERE overall_status NOT IN ? AND   tblexpense_approval_names.project_id = ? AND   tblexpense_approval_names.id = ?',array(array('0'),$projectid,$appid));
        if($query->num_rows() > 0){
            return false;
        }else{
            return true;
        }
        
    }
}
function get_approvalticket_record_status($ticketid)
{
    $CI = &get_instance();
    if(is_numeric($ticketid)){
        $query  = $CI->db->query('SELECT id FROM `tblapprovals` 
                                  WHERE approval_status IN ? AND   tblapprovals.rel_id = ? AND   tblapprovals.rel_type = ?',array(array('3'),$ticketid,'ticket'));
        if($query->num_rows() > 0){
            return false;
        }else{
            return true;
        }
        
    }
}
function get_approval_heading_name_by_id($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblapproval_headings',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function create_slug($string){
   $slug=preg_replace('/[^A-Za-z0-9-]+/', '_', $string);
   return strtolower($slug);
}
function assign_all($assignded_ids,$rel_name,$rel_id,$project_id='')
{
    
    
    $CI = &get_instance();
    $all_old_assignees = get_all_assignees($rel_name,$rel_id);
    $old_assigned = array_column($all_old_assignees, 'assigneeid');
    //Delete old assigned staff ids
    $CI->db->where('rel_name',$rel_name);
    $CI->db->where('rel_id',$rel_id);
    $CI->db->delete('tblall_assignees');
	 foreach ($assignded_ids as $key => $value) {
        $CI->db->insert('tblall_assignees', array(
                'rel_id' => $rel_id,
                'staff_id' => $value,
                'assigned_by'=>get_staff_user_id(),
                'rel_name'=>$rel_name,
                'dateadded'=>date('Y-m-d H:i:s'),
                'project_id'=>$project_id
            ));
        if($rel_name == 'lead'){
            $CI->load->model('leads_model');
            if(!in_array($value, $old_assigned)){
                $CI->leads_model->lead_assigned_member_notification($rel_id, $value);
            }
        }
    }
}
function update_project_amounts($data,$id)
{
	$affectedRows = 0;
        $CI = &get_instance();
        $CI->db->where('id', $id);
        $CI->db->update('tblprojects', $data);
        if ($CI->db->affected_rows() > 0) { 
    
            return true;
        }
        return false;
}
function get_all_assignees($rel_name,$rel_id)
{
    $CI = &get_instance();
    $CI->db->select('id,tblall_assignees.staff_id as assigneeid,assigned_by,firstname,lastname,CONCAT(firstname, " ", lastname) as full_name');
        $CI->db->from('tblall_assignees');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblall_assignees.staff_id');
        $CI->db->where('rel_name', $rel_name );
        $CI->db->where('rel_id', $rel_id );
    return $CI->db->get()->result_array();
}

function get_project_instances_added_for_project($id){
    $CI = &get_instance();

    if($id){
        $row =  $CI->db->select('tblproject_instances.*,tblproject_instances.instance_name as name')
                ->from('tblproject_instances')
                ->join('tblcase_details','tblproject_instances.id = tblcase_details.instance_id','inner')
                ->where('project_id',$id)
                ->get()->result_array();

        if($row)
            return $row;
        else
            return []; 
    }
}
function get_court_instance_name_by_id($id){
    $CI = &get_instance();

    if($id){
      $row =    $CI->db->get_where('tblproject_instances',array('id'=>$id))->row();
      if($row)
        return $row->instance_slug;
      else
        return ''; 
    }
}
function get_project_instances()
{
    $CI = &get_instance();
    return  $CI->db->select('tblproject_instances.*,tblproject_instances.instance_name as name')->from('tblproject_instances')->get()->result_array(); 
}
function get_project_casenature()
{
    $CI = &get_instance();
    return  $CI->db->select('tblcase_natures.*,tblcase_natures.name as name')->from('tblcase_natures')->get()->result_array(); 
}
function get_hearing_types(){
    return array(
        array('id'=>'first_instance','name'=>_l('first_instance')),
        array('id'=>'appeal','name'=>_l('appeal')),
        array('id'=>'appeal_opposite','name'=>_l('appeal_opposite')),
        array('id'=>'cassation','name'=>_l('cassation')),
        array('id'=>'execution','name'=>_l('execution')),
        array('id'=>'execution_appeal','name'=>_l('execution_appeal')),
        array('id'=>'small_claim','name'=>_l('small_claim')),
        array('id'=>'partial_commercial_claim','name'=>_l('partial_commercial_claim')),
        array('id'=>'commercial_claim','name'=>_l('commercial_claim')),
        array('id'=>'legal_warnings','name'=>_l('legal_warnings')),
        array('id'=>'advertisement','name'=>_l('advertisement')),
        array('id'=>'judgement','name'=>_l('judgement')) ,
        array('id'=>'expert_committee','name'=>_l('expert_committee')) ,
        array('id'=>'criminal','name'=>_l('criminal')) , 
    );
}
function get_approval_types($type='expense'){
    if($type == 'ticket'){
        return array(
                    array('id'=>'prepared_by_accountant1','name'=>_l('prepared_by_accountant1')),
                    array('id'=>'forwarded_by_bm','name'=>_l('forwarded_by_bm')),
					array('id'=>'recommended_by_director','name'=>_l('recommended_by_director')),
                    array('id'=>'reviewed_by_legal_advisor','name'=>_l('reviewed_by_legal_advisor')),
                    array('id'=>'verified_by_cfo','name'=>_l('verified_by_cfo')),
                    array('id'=>'approved_by_cmd','name'=>_l('approved_by_cmd')),
             );
    }else{
		$CI = &get_instance();
    $CI->db->where('rel_type', $type);
    
    $approvals_qry = $CI->db->get('tblapproval_headings');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->result_array();
               }
        return $approvals;
       /* return  array(array('id'=>'prepared_by_accountant','name'=>_l('prepared_by_accountant')),
				array('id'=>'reviewed_by_legal_advisor','name'=>_l('reviewed_by_legal_advisor')),
                array('id'=>'verified_by_personal_account_manager','name'=>_l('verified_by_personal_account_manager')),
                array('id'=>'verified_by_hr_admin_manager','name'=>_l('verified_by_hr_admin_manager')),
                array('id'=>'approved_by_cmd','name'=>_l('approved_by_cmd')),
             );  */   
    }
    
}
function get_approvals($rel_id,$rel_type,$status='')
{
    $CI = &get_instance();
    $CI->db->where('rel_id', $rel_id);
    $CI->db->where('rel_type', $rel_type);
    if($status !=''){
       $CI->db->where('approval_status', $status);
    }
    $approvals_qry = $CI->db->get('tblapprovals');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->result_array();
        return $approvals;
    }
    return false;  
}

function get_branchapproval_by_id($rel_id,$rel_type,$status=''){
	$approvals='';
    $CI = &get_instance();
	$CI->db->where('rel_id', $rel_id);
    $CI->db->where('rel_type', $rel_type);
	 $CI->db->where('approval_type', 'forwarded_by_bm');
    if($status !=''){
       $CI->db->where('approval_status', $status);
    }
	 $approvals_qry = $CI->db->get('tblapprovals');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->row()->dateapproved;
       
    }
    return $approvals;
   
}

function get_contracts_complete_update($project_id){
    $CI = &get_instance();
    $CI->db->select('description');
    $CI->db->where('rel_id', $project_id);
    $CI->db->where('rel_type', 'contract');
    $case_details_qry = $CI->db->get('tblnotes');
    if($case_details_qry->num_rows() > 0){
        $result = $case_details_qry->result();
        $case_updates = '';
        foreach ($result as $row) {
            $case_updates .= $row->description.'<br>';
        }
        return $case_updates;
    }

    return '-';
}
function get_project_files_attached($project_id){
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
    $case_files_qry = $CI->db->get('tblproject_files');

    if($case_files_qry->num_rows() > 0){
        $files = $case_files_qry->result_array();
        $result = '<div class="row">
            <div class="col-md-12">
                <p class="text-uppercase bold text-dark font-medium" style="color:green">
                    '._l('project_files').'</p>
        <ol class="list-group">';
        foreach ($files as $file) {
           $result .= ' <li class="list-group-item"><a href="'.admin_url('projects/view/'.$project_id.'?group=project_files').'">'.$file['subject'].' - '._d($file['issue_date']).'</a></li>';   
        }
       return  $result .= '</ul></div></div><hr>';
    }
}  
function get_all_court_attachments($project_id,$type){
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
    $CI->db->where('document_type', $type);
    $client_main_attachments = $CI->db->get('tblproject_files');
if($client_main_attachments->num_rows()>0){
    $attachments = $client_main_attachments->result_array();

    return $attachments;
}
	else{
		return false;
	}
}

function get_casedetails_complete_update($project_id){
    $CI = &get_instance();
    $CI->db->select('dateadded,content');
    $CI->db->where('rel_id', $project_id);
    $CI->db->where('rel_type', 'project');
	 $CI->db->order_by('dateadded', 'desc');
    $case_details_qry = $CI->db->get('tblproject_updates');
    if($case_details_qry->num_rows() > 0){
        $result = $case_details_qry->result();
        $case_updates = '';
        foreach ($result as $row) {
			$dt=date('d-m-Y',strtotime($row->dateadded));
            $case_updates .= html_entity_decode('<b>'.$dt.'</b> -'.$row->content.'<br><hr><br>');
        }
        return $case_updates;
    }

    return '-';
}

function get_court_name_by_id($id){
    $CI = &get_instance();

    if($id && $id != 0){
        return  $CI->db->get_where('tblcourts',array('id'=>$id))->row()->name;
    }else{
        return ' ';
    }
}
function get_instance_claim($pid){
    $CI = &get_instance();

    if($pid && $pid != 0){
		 return  $CI->db->limit(1)->order_by('id','DESC')->get_where('tblcase_details',array('project_id'=>$pid))->row()->claiming_amount;
    }else{
        return ' ';
    }
}
function get_case_numbers($id){
    $CI = &get_instance();
    $CI->db->select('case_number');
    $CI->db->where('project_id', $id);
    $case_details_qry = $CI->db->get('tblcase_details');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->result_array();
        return json_encode($row);
    }

    return ' -';
}
function get_case_natures($id){
    $CI = &get_instance();
	$CI->db->distinct();
    $CI->db->select('name');
    $CI->db->where('project_id', $id);
    $case_details_qry = $CI->db->get('tblcase_natures');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->result_array();
        return json_encode($row);
    }

    return ' -';
}
function get_nature_of_case_by_id($id){
    $CI = &get_instance();

    if($id && $id != 0){
        return  $CI->db->get_where('tblcase_natures',array('id'=>$id))->row()->name;
    }else{
        return ' ';
    }
	  
}
function get_case_latest_update($project_id,$dashboard=false){
    $CI = &get_instance();
    $CI->db->where('rel_id', $project_id);
    $CI->db->where('rel_type', 'project');
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblproject_updates');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
        $type = 'Latest Update';
        $update = _d($row->dateadded).' -  '.nl2br($row->content);
        if($dashboard)
            return $type.'^'.$row->content;
        else
            return '<div class="panel_s panel-info">
                     <div class="panel-body"  style="height:150px;overflow-y:scroll;"><h4>Latest Update</h4><b>'.$update.'</b></div></div>';
                         

                    // return $type.' - '.$update;
    }

    return ' No updates';
}
function get_hearing_latest_update($project_id){
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblhearings');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
        $type = _l($row->hearing_type);
        $update = nl2br($row->proceedings);
        return '<div class="panel_s panel-info">
                     <div class="panel-body"><h4>'.$type.'</h4>'.$update.'</div></div>';
    }

    return ' ';
}

function get_hearing_latest_date($project_id){
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblhearings');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
       
        return $row->hearing_date;
    }

    return ' ';
}

function get_activecase_final_statuses(){
    return array(
      
        array('id'=>'writeoff','name'=>'Write Off'),
        array('id'=>'abscounded','name'=>'Absconding'),
		array('id'=>'others','name'=>'Others'),
    );
}
function get_case_final_statuses(){
    return array(
        array('id'=>'dismissed','name'=>'Dismissed'),
        array('id'=>'won','name'=>'Won'),
        array('id'=>'failure','name'=>'Failure'),
		 array('id'=>'others','name'=>'Others'),
    );
}
function get_approval_statuses(){
    return array(array('id'=>'approved','name'=>'Approved'),
                array('id'=>'on_hold','name'=>'On Hold'),
                array('id'=>'rejected','name'=>'Rejected')
             );
}
function get_case_statuses(){
    return array(array('id'=>'not_started','name'=>'Not Started'),
                array('id'=>'progress','name'=>'In Progress'),
                array('id'=>'won','name'=>'closed-Won'),
				 array('id'=>'lost','name'=>'closed-Lost')
             );
}
function get_legal_statuses(){
    return array(array('id'=>'civil_case','name'=>'Civil Case'),
                array('id'=>'civil_req_sent','name'=>'Civil Case Request Sent'),
				  array('id'=>'police_casefailed','name'=>'Police Case Filed'),
                array('id'=>'no_legal','name'=>'No Legal Action'),
				  array('id'=>'part_payment','name'=>'Part Payment'),
				 array('id'=>'refund_guarantee','name'=>'Refund / Guarantee Case'),
				 array('id'=>'settlement','name'=>'Settlement'),
				 array('id'=>'settlement_case','name'=>'Settlement Case')
             );
}
function get_opposite_party_name($id){
    $CI = &get_instance();

    if(is_numeric($id) && $id != 0) {
      $row =    $CI->db->get_where('tbloppositeparty',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return '-'; 
    }
    return  ' ';
}
function get_opposite_party_cmpstatus($id){
    $CI = &get_instance();

    if(is_numeric($id) && $id != 0) {
      $row =    $CI->db->get_where('tbloppositeparty',array('id'=>$id))->row();
      if($row)
        return $row->company_status;
      else
        return '-'; 
    }
    return  ' ';
}
function get_ip_statuses(){
    return array(array('id'=>'applied','name'=>'Applied'),
                array('id'=>'published','name'=>'Published'),
                array('id'=>'active','name'=>'Active'),
             );
}
function get_ip_types(){
    return array(array('id'=>'trademark','name'=>'Trademark'),
                array('id'=>'patent','name'=>'Patent'),
                array('id'=>'domain_names','name'=>'Domain Names'),
                array('id'=>'designs','name'=>'Designs'),
                array('id'=>'copy_rights','name'=>'Copy Rights'),
                array('id'=>'trade_secrets','name'=>'Trade Secrets'),
                array('id'=>'ip_litigations','name'=>'IP Litigations'),
             );
}
function is_approver($staff_id = '')
{
    $CI = & get_instance();
    if ($staff_id == '') {
        $staff_id = get_staff_user_id();
    }

    $CI->db->where('staffid', $staff_id)
    ->where('is_approver', '1');

    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}
function get_all_recovery_attachments($id,$type='corporate'){
    $CI = &get_instance();
    $CI->db->where('rel_id', $id);
    $CI->db->where('rel_type', $type);
    $client_main_attachments = $CI->db->get('tblfiles')->result_array();

    $attachments[$type] = $client_main_attachments;

    return $attachments;
}

function get_defaulter_follow_up_actions(){
    return array(array('id'=>'settlement','name'=>'Settlement'),
                 array('id'=>'field_visit','name'=>'Field Visit'),
                 array('id'=>'office_visit','name'=>'Office Visit'),
                 array('id'=>'follow_up','name'=>'Follow Up')
             );
}
function get_mode_of_contact(){
    return array(array('id'=>'call','name'=>'Call'),
                 array('id'=>'visit','name'=>'Visit'),
                 array('id'=>'email','name'=>'Email'),
             );
}
function get_recovers_name($userid)
{
    
    $CI =& get_instance();

    $client = $CI->db->select('file_no,debtor_title')
    ->where('id', $userid)
    ->from('tblcorporate_recoveries')
    ->get()
    ->row();
    if ($client) {
        return $client->file_no.'-'.$client->debtor_title;
    } else {
        return '';
    }
}

function new_number_format($n){
    // first strip any formatting
    if($n != ''){
        $n = (0+str_replace(",", "", $n));
        return number_format($n);
    }
    return $n;
}

function replace_comas($n){
     $n = (0+str_replace(",", "", $n));
    return $n;
}
function get_status_(){
    return array(
          array('id'=>'submitted','name'=>'Submitted'),
          array('id'=>'finance_confirmed','name'=>'Finance Confirmed'),
          array('id'=>'debt_collection','name'=>'Debt Collection'),
          array('id'=>'legal','name'=>'Legal'),
          array('id'=>'closed','name'=>'Closed')
      );
}

function get_oppositeparty_profile_tabs($customer_id)
{
    $customer_tabs = array(
  array(
    'name'=>'profile',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=profile'),
    'icon'=>'fa fa-user-circle',
    'lang'=>_l('debtor_add_edit_profile'),
    'visible'=>true,
    'order'=>1,
    ),
  array(
    'name'=>'notes',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('contracts_notes_tab'),
    'visible'=>true,
    'order'=>2,
    ),
   array(
    'name'=>'contacts',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=contacts'),
    'icon'=>'fa fa-users menu-icon',
    'lang'=>_l('defendant_signatory'),
    'visible'=>true,
    'order'=>3,
    ),
		
	array(
    'name'=>'casediary',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=projects'),
    'icon'=>'fa fa-balance-scale menu-icon',
    'lang'=>_l('projects'),
    'visible'=>(has_permission('projects', '', 'view') || has_permission('projects', '', 'view_own')),
    'order'=>4,
    ),
  
  array(
    'name'=>'attachments',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=attachments'),
    'icon'=>'fa fa-paperclip',
    'lang'=>_l('customer_attachments'),
    'visible'=>true,
    'order'=>16,
    ),
 
  array(
    'name'=>'reminders',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=reminders'),
    'icon'=>'fa fa-clock-o',
    'lang'=>_l('client_reminders_tab'),
    'visible'=>true,
    'order'=>18,
    'id'=>'reminders',
    ),
 

  );


    usort($customer_tabs, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $customer_tabs;
}
function get_recoveries_profile_tabs($customer_id)
{
    $customer_tabs = array(
  array(
    'name'=>'profile',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=profile'),
    'icon'=>'fa fa-user-circle',
    'lang'=>_l('debtor_add_edit_profile'),
    'visible'=>true,
    'order'=>1,
    ),
  array(
    'name'=>'notes',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('defaulters_notes_tab'),
    'visible'=>true,
    'order'=>2,
    ),
   array(
    'name'=>'demand_notice',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=demand_notice'),
    'icon'=>'fa fa-file-text-o',
    'lang'=>_l('demand_notice'),
    'visible'=>true,
    'order'=>3,
    ),
  /* array(
    'name'=>'casediary',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=projects'),
    'icon'=>'fa fa-balance-scale menu-icon',
    'lang'=>_l('projects'),
    'visible'=>(has_permission('projects', '', 'view') || has_permission('projects', '', 'view_own')),
    'order'=>4,
    ),*/
   array(
    'name'=>'tickets',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=tickets'),
    'icon'=>'fa fa-ticket',
    'lang'=>_l('issues'),
    'visible'=>((get_option('access_tickets_to_none_staff_members') == 1 && !is_staff_member()) || is_staff_member()),
    'order'=>5,
    ),
   /*array(
    'name'=>'expenses',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=expenses'),
    'icon'=>'fa fa-money',
    'lang'=>_l('expenses'),
    'visible'=>(has_permission('expenses', '', 'view') || has_permission('expenses', '', 'view_own')),
    'order'=>4,
    ),*/
  /*array(
    'name'=>'statement',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=statement'),
    'icon'=>'fa fa-area-chart',
    'lang'=>_l('customer_statement'),
    'visible'=>(has_permission('invoices', '', 'view') && has_permission('payments', '', 'view')),
    'order'=>3,
    ),
  array(
    'name'=>'invoices',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=invoices'),
    'icon'=>'fa fa-file-text',
    'lang'=>_l('client_invoices_tab'),
    'visible'=>(has_permission('invoices', '', 'view') || has_permission('invoices', '', 'view_own')),
    'order'=>4,
    ),
  array(
    'name'=>'payments',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=payments'),
    'icon'=>'fa fa-line-chart',
    'lang'=>_l('client_payments_tab'),
    'visible'=>(has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own')),
    'order'=>5,
    ),
   array(
    'name'=>'receipts',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=receipts'),
    'icon'=>'fa fa-money',
    'lang'=>_l('client_receipts_tab'),
    'visible'=>(has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own')),
    'order'=>5,
    ),
  array(
    'name'=>'proposals',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=proposals'),
    'icon'=>'fa fa-file-powerpoint-o',
    'lang'=>_l('proposals'),
    'visible'=>(has_permission('proposals', '', 'view') || has_permission('proposals', '', 'view_own') || (get_option('allow_staff_view_proposals_assigned') == 1 && total_rows('tblproposals', array('assigned'=>get_staff_user_id())) > 0)),
    'order'=>6,
    ),
    array(
    'name'=>'credit_notes',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=credit_notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('credit_notes'),
    'visible'=>(has_permission('credit_notes', '', 'view') || has_permission('credit_notes', '', 'view_own')),
    'order'=>7,
    ),
   array(
    'name'=>'estimates',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=estimates'),
    'icon'=>'fa fa-clipboard',
    'lang'=>_l('estimates'),
    'visible'=>(has_permission('estimates', '', 'view') || has_permission('estimates', '', 'view_own')),
    'order'=>8,
    ),*/
 
  /*array(
    'name'=>'contracts',
    'url'=>admin_url('clients/client/'.$customer_id.'?group=contracts'),
    'icon'=>'fa fa-floppy-o',
    'lang'=>_l('contracts'),
    'visible'=>(has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')),
    'order'=>10,
    ),*/
    /*array(
    'name'=>'documents',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=documents'),
    'icon'=>'fa fa-file',
    'lang'=>_l('documents'),
    'visible'=>(has_permission('documents', '', 'view') || has_permission('documents', '', 'view_own')),
    'order'=>11,
    ),
    array(
    'name'=>'casediary',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=casediary'),
    'icon'=>'fa fa-balance-scale menu-icon',
    'lang'=>_l('casediary'),
    'visible'=>(has_permission('casediary', '', 'view') || has_permission('casediary', '', 'view_own')),
    'order'=>12,
    ),*/
    /*array(
    'name'=>'tasks',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=tasks'),
    'icon'=>'fa fa-tasks',
    'lang'=>_l('tasks'),
    'visible'=>true,
    'order'=>14,
    ),*/
  /*array(
    'name'=>'tickets',
    'url'=>admin_url('clients/client/'.$customer_id.'?group=tickets'),
    'icon'=>'fa fa-ticket',
    'lang'=>_l('tickets'),
    'visible'=>((get_option('access_tickets_to_none_staff_members') == 1 && !is_staff_member()) || is_staff_member()),
    'order'=>15,
    ),*/
  array(
    'name'=>'attachments',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=attachments'),
    'icon'=>'fa fa-paperclip',
    'lang'=>_l('customer_attachments'),
    'visible'=>true,
    'order'=>16,
    ),
  /*array(
    'name'=>'vault',
    'url'=>admin_url('clients/client/'.$customer_id.'?group=vault'),
    'icon'=>'fa fa-lock',
    'lang'=>_l('vault'),
    'visible'=>true,
    'order'=>17,
    ),*/
  array(
    'name'=>'reminders',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=reminders'),
    'icon'=>'fa fa-clock-o',
    'lang'=>_l('client_reminders_tab'),
    'visible'=>true,
    'order'=>18,
    'id'=>'reminders',
    ),
 /* array(
    'name'=>'map',
    'url'=>admin_url('corporate_recoveries/corporate_recovery/'.$customer_id.'?group=map'),
    'icon'=>'fa fa-map-marker',
    'lang'=>_l('customer_map'),
    'visible'=>true,
    'order'=>19,
    ),*/

  );


    usort($customer_tabs, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $customer_tabs;
}

function get_latest_update($id,$type){
  $CI = &get_instance();
  $description_qry = $CI->db->limit(1)->order_by('id','DESC')->get_where('tblnotes',array('rel_type'=>$type,'rel_id'=>$id));
  if($description_qry->num_rows() > 0)
    return $description_qry->row()->description;
  else
    return '';

}

function get_contact_codes(){
    $CI =& get_instance();
    return $CI->db->select('id,CONCAT(code,"-",name)as name')->get('tblcontactcodes')->result_array();
}
function get_case_client_types(){
    return array(
            array('id'=>'court_case','name'=>'Court Case'),
            array('id'=>'legal_consultancy','name'=>'Legal Consultancy'),
            array('id'=>'personal_law','name'=>'Personal Law'),
            array('id'=>'other_projects','name'=>'Other Projects'),
            array('id'=>'chequebounce','name'=>'PDC/Cheque Bounce'),
            array('id'=>'policecase','name'=>'Police Case'),
            array('id'=>'labour_case','name'=>'Labour Case'),
            array('id'=>'transfer_case','name'=>'Cases Transferred To Other Countries'),
	       /* array('id'=>'intellectual_property','name'=>'Intellectual Property')*/
        );
}

function get_emirates(){
    return array(
            array('id'=>'Abu Dhabi','name'=>'Abu Dhabi'),
            array('id'=>'Dubai','name'=>'Dubai'),
            array('id'=>'Sharjah','name'=>'Sharjah'),
            array('id'=>'Ajman','name'=>'Ajman'),
            array('id'=>'Umm Al-Quwain','name'=>'Umm Al-Quwain'),
            array('id'=>'Fujairah','name'=>'Fujairah'),
            array('id'=>'Ras Al Khaimah','name'=>'Ras Al Khaimah'),
		 	array('id'=>'Others','name'=>'Others'),
        );
}
function get_client_positions(){
    return array(
            array('id'=>'defendant','name'=>'Defendant'),
            array('id'=>'plaintiff','name'=>'Plaintiff')
        );
}


// Templates

/**
 * Default project tabs
 * @param  mixed $project_id project id to format the url
 * @return array
 */
function get_templates_tabs_admin($project_id)
{
    $project_tabs = array(
    array(
        'name'=>'case_overview',
        'url'=>admin_url('casetemplates/view/'.$project_id.'?group=case_overview'),
        'icon'=>'fa fa-th',
        'lang'=>_l('case_overview'),
        'visible'=>true,
        'order'=>1,
        ),
    array(
        'name'=>'scope',
        'url'=>admin_url('casetemplates/view/'.$project_id.'?group=scope'),
        'icon'=>'fa fa-balance-scale',
        'lang'=>_l('scope'),
        'visible'=>true,
        'order'=>2,
        ),
    array(
        'name'=>'project_tasks',
        'url'=>admin_url('casetemplates/view/'.$project_id.'?group=project_tasks'),
        'icon'=>'fa fa-check-circle',
        'lang'=>_l('tasks'),
        'visible'=>true,
        'order'=>3,
        'linked_to_customer_option'=>array('view_tasks'),
        ),
    array(
        'name'=>'document_checklists',
        'url'=>admin_url('casetemplates/view/'.$project_id.'?group=document_checklists'),
        'icon'=>'fa fa-rocket',
        'lang'=>_l('document_checklists'),
        'visible'=>true,
        'order'=>4,
        'linked_to_customer_option'=>array('view_documents'),
        ),
    
    /*array(
        'name'=>'project_milestones',
        'url'=>admin_url('casetemplates/view/'.$project_id.'?group=project_milestones'),
        'icon'=>'fa fa-rocket',
        'lang'=>_l('project_milestones'),
        'visible'=>false,
        'order'=>5,
        'linked_to_customer_option'=>array('view_milestones'),
        ),*/
    );

    //$project_tabs = do_action('project_tabs_admin', $project_tabs);

    usort($project_tabs, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $project_tabs;
}

function init_relation_tasks_templates_table($table_attributes = array())
{
    $table_data = array(
        array(
            'name'=>_l('tasks_dt_name'),
            'th_attrs'=>array(
                'style'=>'min-width:200px',
                ),
            ),
         array(
            'name'=>_l('tasks_dt_datestart'),
            'th_attrs'=>array(
                'style'=>'min-width:75px',
                'class'=>'not_visible',
                ),
            ),
         array(
            'name'=>_l('task_duedate'),
            'th_attrs'=>array(
                'style'=>'min-width:75px',
                'class'=>'duedate not_visible',

                ),
            ),

         array(
            'name'=>_l('tags'),
            'th_attrs'=>array(
                'class'=>'duedate not_visible',

                ),
            ),
        _l('task_add_edit_description'),
        _l('task_status'),
    );

   

    $custom_fields = get_custom_fields('tasks', array(
        'show_on_table' => 1,
    ));

    foreach ($custom_fields as $field) {
        array_push($table_data, $field['name']);
    }

    //$table_data = do_action('tasks_related_table_columns', $table_data);

    array_push($table_data, array('name'=>_l('options'), 'th_attrs'=>array('class'=>'table-tasks-options')));

    $name = 'rel-tasks';
    if ($table_attributes['data-new-rel-type'] == 'lead') {
        $name = 'rel-tasks-leads';
    }if ($table_attributes['data-new-rel-type'] == 'defaulter') {
        $name = 'rel-tasks-defaulter';
    }

    $table = '';
    $CI =& get_instance();
    $table_name = '.table-' . $name;
    /*$CI->load->view('admin/tasks/tasks_filter_by', array(
        'view_table_name' => $table_name,
    ));*/
    if (has_permission('tasks', '', 'create')) {
        $disabled   = '';
        $table_name = addslashes($table_name);
        if ($table_attributes['data-new-rel-type'] == 'customer' && is_numeric($table_attributes['data-new-rel-id'])) {
            if (total_rows('tblclients', array(
                'active' => 0,
                'userid' => $table_attributes['data-new-rel-id'],
            )) > 0) {
                $disabled = ' disabled';
            }
        }
        /*if ($table_attributes['data-new-rel-type'] == 'defaulter' && is_numeric($table_attributes['data-new-rel-id'])) {
            if (total_rows('tbldefaulters', array(
                'active' => 0,
                'id' => $table_attributes['data-new-rel-id'],
            )) > 0) {
                $disabled = ' disabled';
            }
        }*/
        /*if($table_attributes['data-new-rel-type'] == 'casediary'){
            $new_task_label = 'new_action';
        }else{*/
            $new_task_label = 'new_task';
        //}
        // projects have button on top
        
        echo "<a href='#' class='btn btn-info pull-left mbot25 mright5 new-task-relation" . $disabled . "' onclick=\"new_template_task_from_relation('$table_name'); return false;\" data-rel-id='".$table_attributes['data-new-rel-id']."' data-rel-type='".$table_attributes['data-new-rel-type']."'>" . _l($new_task_label) . "</a>";
        
        

    }
    echo "<div class='clearfix'></div>";
    $table .= render_datatable($table_data, $name, array(), $table_attributes);

    return $table;
}
function get_document_masters(){
    $CI =& get_instance();
    $documentmaster = $CI->db->get('tbldocumentmaster')->result_array();
    if ($documentmaster) {
        return $documentmaster;
    }

    return array();
}

function get_document_master_name($id){
    $CI = &get_instance();

    if($id){
        return  $CI->db->get_where('tbldocumentmaster',array('id'=>$id))->row()->name;
    }
}
function get_case_templates($casetype='')
{
    $CI =& get_instance();
    if($casetype!= '')
    $CI->db->where('case_type', $casetype);
    $project = $CI->db->get('tblcasetemplates')->result_array();
    if ($project) {
        return $project;
    }

    return [];
}

function template_data_transfer($matterid,$templateid){

        // Copy Scopes
        $CI =& get_instance();
        $scopes = $CI->db->get_where('tblcasetemplate_scopes',array('casetemplate_id'=>$templateid))->result();
        foreach ($scopes as $scope) {
            $data['scope_description'] = $scope->scope_description;
            $data['case_id']  = $matterid;
            $CI->db->insert('tblcase_scopes',$data);
        }

        // Get Tasks in the milestones
        $tasks = $CI->db->get_where('tblstafftasks_templates',array('rel_id'=>$templateid,'rel_type'=>'casetemplates'))->result_array();
            foreach ($tasks as $task) {
                $cur_task_id = $task['id'];
                $task['milestone'] = 0;
                $task['rel_id'] = $matterid;
                $task['rel_type'] = 'project';
                $task['startdate'] = _d(date('Y-m-d'));
                $CI->load->model('tasks_model');
                unset($task['id']);
                unset($task['repeat_every']);
                unset($task['repeat_type_custom']);
                
                $task_id = $CI->tasks_model->add($task);
            }
        // Documnt master
        $document_masters = $CI->db->get_where('tblcasetemplates',array('id'=>$templateid))->row()->document_checklists;
        $document_masters = explode(',', $document_masters);

        foreach ($document_masters as $doc) {
           /* $d_data['case_id'] = $matterid;
            $d_data['dateadded'] = date('Y-m-d H:i:s');
            $d_data['added_by'] = get_staff_user_id();
            $d_data['document_checklists_id'] = $doc;
            $d_data['name'] = get_document_master_name($doc);
            $this->db->insert('tblcase_documentmaster',$d_data);*/

            $data2 = array(
                'project_id' => $matterid,
                'file_name' => '',
                'filetype' => '',
                'dateadded' => date('Y-m-d H:i:s'),
                'staffid' =>get_staff_user_id(),
                'contact_id' => 0,
                'subject' => get_document_master_name($doc),
                'visible_to_customer' =>0,
                'document_master_id' =>$doc,
            );
            $CI->db->insert('tblproject_files', $data2);

            
        } 
    }

function get_proposal_types(){
    return array(
            array('id'=>'fee_proposal_without_loe','name'=>'Fee Proposal without LoE'),
            array('id'=>'fee_proposal_with_loe','name'=>'Fee Proposal with LoE'),
            array('id'=>'loe','name'=>'LoE'),
            array('id'=>'offer_letter_for_retainer_contract','name'=>'Offer Letter for Retainer Contract'),
            array('id'=>'retainer_contract','name'=>'Retainer Contract'),
        );
}
function get_matter_template_name_by_id($id)
{
    $CI =& get_instance();
    $CI->db->select('name');
    $CI->db->where('id', $id);
    $project = $CI->db->get('tblcasetemplates')->row();

    if ($project) {
        return $project->name;
    }
    return '';
}

function get_proposal_fee_statuses(){
    $CI = &get_instance();
    return $CI->db->get('tblproposal_fee_status')->result();

} 
function generate_task_default($ticketid){

        // Copy Scopes
        $CI =& get_instance();
        
        // Put Tasks in the ticket
	$tasks=  [ '2' => 'Prepared By accountant',
              '3' => 'Forwarded By Branch Manager',
              '4' => 'Reviewed By Legal Advisor',
				'5' => 'Verified By CFO CA',
              '6' => 'Recommended By Director',
              '7' => 'Approved By CMD'];


	         foreach ($tasks as $key => $task1) {
                $task['milestone'] = 0;
                $task['rel_id'] = $ticketid;
                $task['rel_type'] = 'Ticket';
	           $task['name'] = $task1;
                $task['startdate'] = _d(date('Y-m-d'));
				  $task['duedate'] = _d(date('Y-m-d'));
				  $task['priority']=2;
                $CI->load->model('tasks_model');
              //  unset($task['id']);
              //  unset($task['repeat_every']);
             //  unset($task['repeat_type_custom']);
                
                $task_id = $CI->tasks_model->add($task);
			/*	   if ($task_id) {
                  $CI->db->where('taskid', $task_id);
					   $data2['staffid']=$key;
					     $CI->db->update(db_prefix() . 'task_assigned', $data2);
				       
        }*/
    
            }
       
    }
function get_settlement_type(){
    return array(array('id'=>'onetime','name'=>'One Time'),
                array('id'=>'installment','name'=>'Installment'),
				 array('id'=>'nonsettle','name'=>'Non Settlement'),
               
             );
}
function get_settlement_nature(){
    return array(array('id'=>'courtorder','name'=>'By Court Order'),
                array('id'=>'legaldept','name'=>'By Legal Department'),
				  array('id'=>'branch','name'=>'By Branch'),
               
             );
}
function get_refund_status(){
    return array(array('id'=>'1','name'=>'Received'),
                array('id'=>'2','name'=>'Not Received'),
			  );
}
function get_expense_approvals($rel_id)
{
    $CI = &get_instance();
    $CI->db->where('project_id', $rel_id);
    
    $approvals_qry = $CI->db->get('tblexpense_approval_names');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->result_array();
        $i=0;
        foreach($approvals as $approval){
            $approvals[$i]['approval_headings'] = get_expense_approvals_by_name_id($approval['id']); 
            $i++;
        }
        return $approvals;
    }
    return false;  
}
function get_expense_approvalsname($nameid,$rel_id)
{
    $CI = &get_instance();
    $CI->db->where('project_id', $rel_id);
    $CI->db->where('id', $nameid);
    $approvals_qry = $CI->db->get('tblexpense_approval_names');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->row()->approval_name;
        
        return $approvals;
    }
    return '';  
}
 

function get_expense_approvals_by_name_id($approval_name_id){
    $CI = &get_instance();
    $CI->db->where('approval_name_id', $approval_name_id);
    $approvals_qry = $CI->db->get('tblexpense_approvals');
    if($approvals_qry->num_rows() > 0){
        return $approvals_qry->result_array();
    }
    return [];
}
function get_expense_approvals_pdf($name_id,$project_id,$status='')
{
    $CI = &get_instance();
   $CI->db->select('tblexpense_approvals.*,tblexpense_approval_names.approval_name,tblapproval_headings.name as approval_type');
        $CI->db->from('tblexpense_approvals');
        $CI->db->join('tblexpense_approval_names', 'tblexpense_approval_names.id = tblexpense_approvals.approval_name_id');
	 $CI->db->join('tblapproval_headings', 'tblapproval_headings.id = tblexpense_approvals.approval_heading_id');
       $CI->db->where('project_id', $project_id);
        $CI->db->where('approval_name_id', $name_id);
 if($status !=''){
      // $CI->db->where('approval_status', $status);
    }
    $approvals_qry = $CI->db->get();
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->result_array();
        return $approvals;
    }
    return false;  
}
function get_document_type_name($id){
    $CI = &get_instance();

    if($id){
        return  $CI->db->get_where('tbldocument_types',array('id'=>$id))->row()->name;
    }
}
function get_courtorder_type_name($id){
    $CI = &get_instance();

    if($id){
        return  $CI->db->get_where('tblcourt_ordernames',array('id'=>$id))->row()->name;
    }
}
function get_all_assignees_byproject($project_id)
{
    $CI = &get_instance();
    $CI->db->distinct()->select('tblall_assignees.staff_id as assigneeid');
        $CI->db->from('tblall_assignees');
       // $CI->db->join('tblstaff', 'tblstaff.staffid = tblall_assignees.staff_id');
        $CI->db->where('project_id', $project_id );
    return $CI->db->get()->result_array();
}

function get_all_legal_byproject($project_id)
{
    $CI = &get_instance();
    $CI->db->distinct()->select('tblcase_details.legal_cordinator  as legal_ids');
        $CI->db->from('tblcase_details');
       // $CI->db->join('tblstaff', 'tblstaff.staffid = tblall_assignees.staff_id');
        $CI->db->where('project_id', $project_id );
	 $CI->db->where('legal_cordinator!=' ,' ' );
    return $CI->db->get()->result_array();
}
function get_latest_assignees_byproject($project_id)
{
    $CI = &get_instance();
	
    $CI->db->select('tblstaff.*');
        $CI->db->from('tblall_assignees');
        $CI->db->join('tblstaff', 'tblstaff.staffid = tblall_assignees.staff_id');
	 $CI->db->where('project_id', $project_id);
	  $CI->db->limit(1);
        $CI->db->order_by('tblall_assignees.id', 'DESC');
	 $qry=$CI->db->get();
	 if($qry->num_rows() > 0){
	 
    return $qry->row()->firstname.' '. $qry->row()->lastname;
	 }
	return ' ';
}
function get_project_latest_update($project_id){
    $CI = &get_instance();
    $CI->db->where('rel_id', $project_id);
    $CI->db->where('rel_type', 'project');
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblproject_updates');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
        $type =_d($row->dateadded);
        $update = nl2br($row->content);
          return '<b>'.$type.'</b>  - '.$update;
    }

    return ' ';
}
function get_party_contacttype(){
    return array(
            array('id'=>'defendant','name'=>'Defendant'),
            array('id'=>'signatory','name'=>'Authorized Signatory')
        );
}
function get_nationality($nid='')
{
    $CI = &get_instance();
    $CI->db->select('*');
        $CI->db->from('tblnationality');
	
       return $CI->db->get()->result_array();
}
function get_countryproject()
{
    $CI = &get_instance();
    $CI->db->select('*');
        $CI->db->from('tblcountries');
       return $CI->db->get()->result_array();
}
function get_countryproject_name($id){
    $CI = &get_instance();

    if($id){
        return  $CI->db->get_where('tblcountries',array('country_id'=>$id))->row()->short_name;
    }
}
function get_project_requestno($id){
    $CI = &get_instance();

    if($id){
		
		$result=$CI->db->get_where('tbltickets',array('ticketid'=>$id))->row();
		$rno=$result->request_no.' - '.date('d/m/Y',strtotime($result->date));
        return  $rno;
    }
	return ' ';
}
function get_party_name($userid)
{
    
    $CI =& get_instance();

    $client = $CI->db->select('name,tradelicence,city')
    ->where('id', $userid)
    ->from('tbloppositeparty')
    ->get()
    ->row();
    if ($client) {
        return $client->name.'-'.$client->tradelicence.'-'.$client->city;
    } else {
        return '';
    }
}
function fetch_courtinstance_numrows($pid)
    {
           $CI =& get_instance();
            $CI->db->select('*');

            $CI->db->from(db_prefix() . 'case_details');

            $CI->db->where('project_id',$pid);
			 
           return $CI->db->get()->num_rows();
        
    }
function get_instance_last($pid){
    $CI = &get_instance();

    if($pid && $pid != 0){
		 return  $CI->db->limit(1)->order_by('id','DESC')->get_where('tblcase_details',array('project_id'=>$pid))->row();
    }else{
        return ' ';
    }
}
function fetch_civilticket_numrows($pid)
    {
           $CI =& get_instance();
            $CI->db->select('*');

            $CI->db->from(db_prefix() . 'tickets_civil');

            $CI->db->where('ticketid',$pid);
			 
           return $CI->db->get()->num_rows();
        
    }
function fetch_policeticket_numrows($pid)
    {
           $CI =& get_instance();
            $CI->db->select('*');

            $CI->db->from(db_prefix() . 'tickets_police');

            $CI->db->where('ticketid',$pid);
			 
           return $CI->db->get()->num_rows();
        
    }
function get_latest_ticket(){
    $CI = &get_instance();
$ticket=$CI->db->limit(1)->order_by('ticketid','DESC')->get('tbltickets');
    if($ticket->num_rows()>0){
		 return  $ticket->row()->ticketid;
    }else{
        return '0';
    }
}
function get_opposite_contact_name($oppid='',$partytype='')
{
    $CI =& get_instance();
    if($oppid!= '')
    $CI->db->where('opposite_id', $oppid);
	if($partytype!=''){
	$CI->db->where('party_type', $partytype);
	}
    $contact1 = $CI->db->get('tbloppsitecontacts')->result_array();
    if ($contact1) {
        return $contact1;
    }

    return [];
}
function get_contact_nationality($nid=''){
    $CI = &get_instance();
$nation='';
    if($nid){
        $nation= $CI->db->get_where('tblnationality',array('id'=>$nid))->row()->nation;
    }
	return $nation;
}
function get_project_latest_verify($project_id){
    $CI = &get_instance();
    $CI->db->where('case_id', $project_id);
      $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblcase_scopes');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
       
          return $row;
    }

    return ' ';
}
function get_project_latest_handover($project_id){
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
      $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblcase_handover');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
       
          return $row;
    }

    return ' ';
}

?>