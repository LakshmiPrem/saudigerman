<?php
defined('BASEPATH') or exit('No direct script access allowed');
function get_lead_client_types(){
    return array(array('id'=>'Internal','name'=>'Internal'),
                array('id'=>'External','name'=>'External')               
             );
}

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
function get_approval_types($type='expense',$rel_id=''){
$approvals=[];
		$CI = &get_instance();
	 $CI->db->order_by('head_order','ASC'); 
    $CI->db->where('rel_type', $type);
	$CI->db->where('active', '1');
	if($rel_id!=''){
		 $CI->db->where('rel_id', $rel_id);
	}
    
    $approvals_qry = $CI->db->get('tblapproval_headings');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->result_array();
               }
        return $approvals;
         
}
function get_approvals($rel_id,$rel_type,$status='',$approval_key='')
{
    $CI = &get_instance();
    $CI->db->where('rel_id', $rel_id);
    $CI->db->where('rel_type', $rel_type);
    if($status !=''){
       $CI->db->where('approval_status', $status);
    }
	if($approval_key !=''){
       $CI->db->where('approval_key', $approval_key);
    }
    $approvals_qry = $CI->db->get('tblapprovals');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->result_array();
        return $approvals;
    }
    return[];  
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
        $result = ' <p class="text-uppercase bold text-dark font-medium " style="color:green" align="left">
                    '._l('project_files').'</p> <hr class="hr-panel-heading project-area-separation" /><div class="col-md-12">';
        foreach ($files as $file) {
           $result .= ' <div class="col-md-3 total-column"> <div class="panel_s"><div class="panel-body"> <p class="text-uppercase text-info bold"><a href="'.admin_url('projects/view/'.$project_id.'?group=project_files').'">'.$file['subject'].'</a></p>
         <p class="bold font-medium">'. _d($file['issue_date']).'</p><span class="text-right"><a href="'. site_url('download/downloadfile/'.$project_id.'/'.$file['id']).'"><i class="'.get_mime_class($file['filetype']).'"></i></a></span></div></div></div>';   
        }
       return  $result .= '</div>';
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
	return '';  
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
                     <div class="panel-body"><h4>Latest Update</h4>'.$update.'</div></div>';
                         

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
function get_ip_statuses(){
    return array(array('id'=>'applied','name'=>'Applied'),
				 array('id'=>'accepted','name'=>'Accepted'),
				 array('id'=>'under examination','name'=>'Under Exaination'),
               	array('id'=>'opposed','name'=>'Opposed'),
				array('id'=>'published','name'=>'Published'),
                array('id'=>'rejected','name'=>'Rejected'),
                array('id'=>'office action','name'=>'Office Action'),
             );
}
function get_ip_types(){
	$CI = &get_instance();
    $CI->load->model('ipcategory_model');
    return $CI->ipcategory_model->get();
	
 /*return array(array('id'=>'trade_marks','name'=>'Trade Marks'),
                array('id'=>'logo','name'=>'Logo'),
                array('id'=>'letters','name'=>'Letters'),
                array('id'=>'designs','name'=>'Designs'),
                array('id'=>'word','name'=>'Word'),
                array('id'=>'combined','name'=>'Combined'),
				array('id'=>'copyrights','name'=>'Copyrights'),
                array('id'=>'patent','name'=>'Patent'),
                array('id'=>'industrial_design','name'=>'Industrial Design'),
                array('id'=>'domain_names','name'=>'Domain Names'),
             );*/
}
function get_ipsub_types(){
	$CI = &get_instance();
    $CI->load->model('ipcategory_model');
    return $CI->ipcategory_model->get_ipsubcategory();
   /* return array(array('id'=>'logo','name'=>'Logo'),
                array('id'=>'letter','name'=>'Letter'),
                array('id'=>'word','name'=>'word'),
                array('id'=>'others','name'=>'Others'),
             
             );*/
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
    'name'=>'overview',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=overview'),
    'icon'=>'fa fa-user-circle',
    'lang'=>_l('client_overview'),
    'visible'=>true,
    'order'=>1,
    ),
  array(
    'name'=>'profile',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=profile'),
    'icon'=>'fa fa-user-circle',
    'lang'=>_l('debtor_add_edit_profile'),
    'visible'=>true,
    'order'=>2,
    ),
  array(
    'name'=>'notes',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('contracts_notes_tab'),
    'visible'=>true,
    'order'=>3,
    ),
   array(
    'name'=>'contacts',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=contacts'),
    'icon'=>'fa fa-users menu-icon',
    'lang'=>_l('defendant_signatory'),
    'visible'=>true,
    'order'=>4,
    ),
	array(
    'name'=>'contracts',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=contracts'),
    'icon'=>'fa fa-balance-scale menu-icon',
    'lang'=>_l('contracts'),
    'visible'=>(has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')),
    'order'=>5,
    ),	

  array(
    'name'=>'kycattachments',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=kycattachments'),
    'icon'=>'fa fa-paperclip',
    'lang'=>_l('customer_kycattachments'),
    'visible'=>true,
    'order'=>14,
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
 
  array(
        'name'     => 'expenses',
        'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=expenses'),
		'icon'     => 'fa fa-file-text-o',
		'lang'=>_l('expenses'),
        
        'order' => 6,
    ),


  );
  
 

 // Include projects tab only if 'enable_legaldashboard' is enabled
    if (get_option('enable_legaldashboard')) {
        $customer_tabs[] = 	array(
    'name'=>'casediary',
    'url'=>admin_url('opposite_parties/opposite_party/'.$customer_id.'?group=projects'),
    'icon'=>'fa fa-balance-scale menu-icon',
    'lang'=>_l('related_matter'),
    'visible'=>(has_permission('projects', '', 'view') || has_permission('projects', '', 'view_own')),
    'order'=>6,
    );
    }
    
    
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
function get_case_client_types($type=''){
	 $CI = &get_instance();
	if($type!='')
    return  $CI->db->select('*')->from('tblproject_types')->where('active',1)->where('type',$type)->get()->result_array(); 
	else
    return  $CI->db->select('*')->from('tblproject_types')->where('active',1)->get()->result_array(); 
  /*  return array(
            array('id'=>'court_case','name'=>'Court Case'),
            array('id'=>'legal_consultancy','name'=>'Legal Consultancy'),
            array('id'=>'personal_law','name'=>'Personal Law'),
            array('id'=>'other_projects','name'=>'Other Projects'),
            array('id'=>'chequebounce','name'=>'PDC/Cheque Bounce'),
            array('id'=>'policecase','name'=>'Police Case'),
            array('id'=>'labour_case','name'=>'Labour Case'),
             /* array('id'=>'legal_drafting','name'=>'Legal Drafting'),
            array('id'=>'intellectual_property','name'=>'Intellectual Property')
        );*/
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
        );
}
function get_client_positions(){
    // return array(
    //         array('id'=>'defendant','name'=>'Defendant'),
    //         array('id'=>'plaintiff','name'=>'Plaintiff')
    //     );
    $CI = &get_instance();
    $CI->load->model('partytype_model');
    return $CI->partytype_model->get();
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
    /*    $document_masters = $CI->db->get_where('tblcasetemplates',array('id'=>$templateid))->row()->document_checklists;
        $document_masters = explode(',', $document_masters);

        foreach ($document_masters as $doc) {
            $d_data['case_id'] = $matterid;
            $d_data['dateadded'] = date('Y-m-d H:i:s');
            $d_data['added_by'] = get_staff_user_id();
            $d_data['document_checklists_id'] = $doc;
            $d_data['name'] = get_document_master_name($doc);
            $this->db->insert('tblcase_documentmaster',$d_data);

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
	

            
        } */
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
/*function get_expense_approvalsname($nameid,$rel_id)
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
}*/
 function get_expense_approvalsname($nameid,$rel_id)
{
    $CI = &get_instance();
    $CI->db->where('rel_id', $rel_id);
	 $CI->db->where('rel_type','expense');
    $CI->db->where('id', $nameid);
    $approvals_qry = $CI->db->get('tblapprovals');
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
function get_nationality()
{
    $CI = &get_instance();
    $CI->db->select('*');
        $CI->db->from('tblnationality');
       return $CI->db->get()->result_array();
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
function get_ticket_count($ticketid,$service)
{
    $CI = &get_instance();
    if(is_numeric($ticketid)){
        $query  = $CI->db->query('SELECT id FROM `tblapprovals` 
                                  WHERE  tblapprovals.rel_id = ? AND   tblapprovals.rel_type = ?',array($ticketid,$service));
        if($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
        
    }
}
function get_request_client($requestid,$service='ticket')
{
	$CI = &get_instance();
    if(is_numeric($requestid)){
		$referenceno='';
		if($service=='ticket'){
        $query  = $CI->db->query('SELECT * FROM `tbltickets` 
                                  WHERE  tbltickets.ticketid = ?',array($requestid));
			 if($query->num_rows() > 0){
				 $result=$query->row();
				 $referenceno=get_client_referenceno($result->userid);
			 }
		}else{
		 $query  = $CI->db->query('SELECT * FROM `tblcontracts` 
                                  WHERE  tblcontracts.id = ?',array($requestid));	
			if($query->num_rows() > 0){
				 $result=$query->row();
				 $referenceno=get_client_refcontractno($result->client);
			 }
		}
        return $referenceno;
        
    }
}
function get_client_referenceno($clientid){
$CI = &get_instance();
    if(is_numeric($clientid)){
        $query  = $CI->db->query('SELECT * FROM `tblclients` 
                                  WHERE  tblclients.userid = ? ',array($clientid));
        if($query->num_rows() > 0){
			$result=$query->row();
			 $next_ref_number =$result->legal_count+1;
                        $prefix = $result->client_no;
					   $_file_number = str_pad($next_ref_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); 
						
							$value=$prefix.$_file_number;
            return $value;
        }else{
            return '';
        }	
	}
}
function get_contract_count($contractid,$service)
{
    $CI = &get_instance();
    if(is_numeric($contractid)){
        $query  = $CI->db->query('SELECT id FROM `tblapprovals` 
                                  WHERE  tblapprovals.rel_id = ? AND   tblapprovals.rel_type = ?',array($contractid,$service));
        if($query->num_rows() > 0){
            return true;
        }else{
            return false;
        }
        
    }
}
function get_ticketsbyservice()
{
    $CI = &get_instance();
    $CI->db->select('*');
        $CI->db->from('tbltickets');
	// $CI->db->where('service', $sid);
      $CI->db->order_by('ticketid','DESC');   
      return $CI->db->get()->result_array();
}
function get_contracttype_name_by_id($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblcontracts_types',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function get_approval_service()
{
	 return array(array('id'=>'ticket','name'=>'Ticket'),
                array('id'=>'contract','name'=>'Contract'),
				  array('id'=>'expense','name'=>'Expense'),
               
             );
   /* $CI = &get_instance();
    $CI->db->distinct()->select('tblapproval_headings.rel_type as reltype');
        $CI->db->from('tblapproval_headings');
      
    return $CI->db->get()->result_array();*/
}
function get_approval_referenceno($rel_id,$reltype)
{
    $CI = &get_instance();
    $CI->db->where('rel_id', $rel_id);
	$CI->db->where('rel_type', $reltype);
    $CI->db->order_by('id', 'DESC');
	 $CI->db->limit('1');
    $approvals_qry = $CI->db->get('tblapprovals');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->row()->approval_name;
        
        return $approvals;
    }
    return '';  
}
function get_ticket_servicename($sid=''){
    $CI = &get_instance();
$nation='';
    if($sid){
        $nation= $CI->db->get_where('tblservices',array('serviceid'=>$sid))->row()->name;
    }
    return $nation;
}
function get_buyerinfo($userid)
{
    
    $CI =& get_instance();

    $client = $CI->db->select('name,address,tradelicence,city')
    ->where('id', $userid)
    ->from('tbloppositeparty')
    ->get()
    ->row();
    if ($client) {
        return $client->address.' , '.$client->city;
    } else {
        return '';
    }
}
/* Bosco To Smartlegal V2-05/04/2023-Ticket*/
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
function get_approvalticket_record_status($ticketid,$service)
{
    $CI = &get_instance();
    if(is_numeric($ticketid)){
        $query  = $CI->db->query('SELECT id FROM `tblapprovals` 
                                  WHERE approval_status IN ? AND   tblapprovals.rel_id = ? AND   tblapprovals.rel_type = ?',array(array('3'),$ticketid,$service));
        if($query->num_rows() > 0){
            return false;
        }else{
            return true;
        }
        
    }
}
function get_credit_payment(){
    return array(array('id'=>'monthly_pdc','name'=>'Monthly PDC'),
                array('id'=>'pdc_on_delivery','name'=>'PDC On Delivery'),
                array('id'=>'due_date','name'=>'Due Date')
             );
}
function get_credit_cheque(){
    return array(array('id'=>'company','name'=>'Company Cheque'),
                array('id'=>'personal','name'=>'Personal Cheque')
				);
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
function get_firstapproval_name($type='expense',$rel_id=''){
	$approvalsid='';
		$CI = &get_instance();
	  $CI->db->limit(1);
    $CI->db->order_by('head_order','ASC');  
    $CI->db->where('rel_type', $type);
	$CI->db->where('active', '1');
    if($rel_id!=''){
		 $CI->db->where('rel_id', $rel_id);
	}
    $approvals_qry = $CI->db->get('tblapproval_headings');
    if($approvals_qry->num_rows() > 0){
        $approvalsid = $approvals_qry->row()->id;
               }
        return $approvalsid;
         
}
function get_branchapproval_by_id($rel_id,$rel_type,$status=''){
	$approvals='';
	$CI = &get_instance();
	$CI->db->limit(1);
    $CI->db->order_by('id','ASC'); 
	$CI->db->where('rel_id', $rel_id);
    $CI->db->where('rel_type', $rel_type);
	// $CI->db->where('approval_type', 'forwarded_by_bm');
    if($status !=''){
       $CI->db->where('approval_status', $status);
    }
	 $approvals_qry = $CI->db->get('tblapprovals');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->row()->dateapproved;
       
    }
    return $approvals;
   
}
//shareholder 4/03/2023
function get_client_list(){
    $CI = &get_instance();
    $CI->load->model('clients_model');
    return $CI->clients_model->get();
}
function get_otherparty_list(){
    $CI = &get_instance();
    $CI->load->model('casediary_model');
    return $CI->casediary_model->get_oppositeparty();
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
function get_project_files_attached1($project_id){
	$result=false;
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
    $case_files_qry = $CI->db->get('tblproject_files');

    if($case_files_qry->num_rows() > 0){
        $result=true;
         
        }
       return  $result;
  } 
//end shareholder
function get_latestexpense_approvalsname($rel_id)
{
    $CI = &get_instance();
    $CI->db->where('project_id', $rel_id);
    $CI->db->order_by('id', 'DESC');
	 $CI->db->limit('1');
    $approvals_qry = $CI->db->get('tblexpense_approval_names');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->row()->approval_name;
        
        return $approvals;
    }
    return '';  
}
function get_common_approvals($rel_id='',$rel_type='expense'){
	$CI = &get_instance();
    $CI->db->distinct()->select('tblapprovals.approval_key as id,tblapprovals.approval_name as approval_name');
     $CI->db->where('rel_id', $rel_id);
	 $CI->db->where('rel_type', $rel_type);
      
    return $CI->db->get('tblapprovals')->result_array();
	
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
function get_branch_ticketlogo($userid)
{
    $CI = &get_instance();
    if(is_numeric($userid)){
        $query  = $CI->db->query('SELECT * FROM `tblclients` 
                                  WHERE userid =? AND   tblclients.client_no LIKE  ?',array($userid,'BT%'));
        if($query->num_rows() > 0){
            $companyUploadPath         = get_upload_path_by_type('company');
    $logoUrl                   = '';
              $logoUrl = $companyUploadPath . 'boscotr.png';
             $width = 120;
             $logoImage = '<img width="' . $width . 'px" src="' . $logoUrl . '">';
            return $logoImage;
           
        }else{
             $query  = $CI->db->query('SELECT * FROM `tblclients` 
                                  WHERE userid =? AND   tblclients.client_no LIKE  ?',array($userid,'VFS'));
        if($query->num_rows() > 0){
             $companyUploadPath         = get_upload_path_by_type('company');
    $logoUrl                   = '';
              $logoUrl = $companyUploadPath . 'ventana.png';
             $width = 140;
             $logoImage = '<img valign="top" width="' . $width . 'px" src="' . $logoUrl . '">';
            return $logoImage;
        }
            else{
             $query  = $CI->db->query('SELECT * FROM `tblclients` 
                                  WHERE userid =? AND   tblclients.client_no LIKE  ?',array($userid,'V%'));
        if($query->num_rows() > 0){
             $companyUploadPath         = get_upload_path_by_type('company');
    $logoUrl                   = '';
              $logoUrl = $companyUploadPath . 'variety.png';
             $width = 120;
             $logoImage = '<img valign="top" width="' . $width . 'px" src="' . $logoUrl . '">';
            return $logoImage;
        }
            else{
                 return pdf_logo_url();
            }
        
        }
    }
    }
}
function get_all_contract_versions($contractid)
{
    $CI = &get_instance();
    if(is_numeric($contractid)){
        $query  = $CI->db->query('SELECT * FROM tblcontract_versions
                                  WHERE contractid = ?',array($contractid));
        if($query->num_rows() > 0){
            return $query->result_array();
        }else{
            return [];
        }
        
    }
    return [];
}
function get_current_contract_version($contractid){
    $CI = &get_instance();
    if(is_numeric($contractid)){
        $query  = $CI->db->query('SELECT version FROM tblcontract_versions
                                  WHERE contractid = ? ORDER BY id DESC LIMIT 1',array($contractid));
        if($query->num_rows() > 0){
            return $query->row()->version;
        }else{
            return 0;
        }
        
    }
    return 0;
}
function create_new_contract_version($contract_version_data)
{
    $CI = &get_instance();
    $CI->db->insert('tblcontract_versions',$contract_version_data);
    return true;
}

function get_current_contract_versioninfo($contractid){
    $CI = &get_instance();
    if(is_numeric($contractid)){
        $query  = $CI->db->query('SELECT * FROM tblcontract_versions
                                  WHERE contractid = ? ORDER BY id DESC LIMIT 1',array($contractid));
        if($query->num_rows() > 0){
            return $query->row();
        }else{
            return 0;
        }
        
    }
    return 0;
}
function get_judgement_status(){
    return array(
                 array('id'=>'closed','name'=>'Closed'),
              array('id'=>'appeal','name'=>'Appeal'),
                array('id'=>'review','name'=>'Review'),
                array('id'=>'complied','name'=>'Complied'),
                array('id'=>'not_complied','name'=>'Not Complied'),
          );
  }

  function get_decree_order_status(){
    return array(
                 array('id'=>'extracted','name'=>'Extracted'),
              array('id'=>'executed','name'=>'Executed'),
                array('id'=>'being_executed','name'=>'Being Executed'),
                array('id'=>'appealed','name'=>'Appealed'),
                
          );
  }
  function get_judge_rule_status(){
    return array(
		 array('id'=>'ruling','name'=>'Ruling'),
       array('id'=>'judgement','name'=>'Judgement'),
       
        );
  }
  function get_hearing_mention(){
    return array(
                 array('id'=>'hearing','name'=>'Hearing'),
              array('id'=>'mention','name'=>'Mention'),
          );
  }

 function get_staffinfo(){
    $CI = &get_instance();
   $CI->load->model('staff_model');
   return $CI->staff_model->get('', ['active' => 1]);
}

function  get_payment_terms(){
    return array(
        array('id'=>'1 Year','name'=>'1 Year'),
        array('id'=>'2 Years','name'=>'2 Years'),
        array('id'=>'3 Years','name'=>'3 Years'),
        array('id'=>'5 Years','name'=>'5 Years'),
        array('id'=>'6 Years','name'=>'6 Years'),
        array('id'=>'7 Years','name'=>'7 Years'),
        array('id'=>'9 Years','name'=>'9 Years'),
        array('id'=>'Any Time','name'=>'Any Time'),
          );
          

  }
function  get_contract_category(){
$CI = &get_instance();
    $category = $CI->db->get_where('tblcontract_category')->result_array();
    if($category)
        return $category;
      else
        return ''; 
}

function  get_contract_subcategory(){
$CI = &get_instance();
    $subcategory = $CI->db->get_where('tblcontract_subcategory')->result_array();
    if($subcategory)
        return $subcategory;
      else
        return ''; 
}

function get_contract_categorybyid($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblcontract_category',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}

function get_contract_subcategorybyid($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblcontract_subcategory',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}

function get_contractsubcategories($cateid= '')
{
    $CI = &get_instance();
    if(is_numeric($cateid)){
        $CI->db->where('category_id',$cateid);
    }
    $cats =  $CI->db->select('tblcontract_subcategory.*')->get('tblcontract_subcategory')->result_array();
    return $cats; 
}
function get_parent_project_namebyid($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblprojects',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function get_court_name($userid = '')
{
    $CI = &get_instance();

    if(is_numeric($userid) && $userid != 0){
        return  $CI->db->get_where('tblcourts',array('id'=>$userid))->row()->name;
    }
    return '';
}
function getlatesthearingbystage($stage_id='',$project_id=''){
	$CI = &get_instance();
   $CI->db->where('project_id', $project_id);
	$CI->db->where('h_instance_id', $stage_id);
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblhearings');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
         
        return $row;
    }else{
		return '';
	}

    return ' ';
}
function get_hearing_latest_nextdate($project_id,$stageid){
	$output='';
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
	$CI->db->where('h_instance_id',$stageid);
	$CI->db->where('DATE(hearing_date) <=',date('Y-m-d'));
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblhearings');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
        $update = _dt($row->postponed_until);

		if($update=='' || $update=='0000-00-00 00:00:00'){
		
	    $output .= '<p class="card-text" style="margin:  0 0 4px;"> <a href="#" onclick="init_hearing(' . $row->id . ',' . $row->project_id . ');return false;" title="'.$update.'" class="btn btn-success hide" style="border-radius: 12px;">'.' <b>'.$update.'</b></a> <a href="#" data-toggle="tooltip" data-title="'. _l('set_hearing_judgement').'" data-type="judgement" class="btn btn-info pull-right mright5" onclick="init_hearing_judgement(); return false;">
            <i class="fa fa-gavel"></i> <b>'._l('judgement').'</b>
                </a>';
		$output .=' <a href="#" onclick="init_hearing(' . $row->id . ',' . $row->project_id . ');return false;" title="'._l('not_booked').'" class="btn btn-warning mright15 hide" style="border-radius: 12px;">'.' <b>'._l('not_booked').'</b></a> </p>';
		}
		return $output;
		/* return '<p class="card-text" style="margin:  0 0 4px;"> <a href="#" onclick="init_hearing(' . $row->id . ',' . $row->project_id . ');return false;" title="'._l('not_booked').'" class="btn btn-warning" style="border-radius: 12px;">'.' <b>'._l('not_booked').'</b></a> </p>';*/
    }

    return $output;
}
function get_judgedhearing_details($project_id,$stage_id,$hear_status){
    $CI = &get_instance();
    $CI->db->where('project_id', $project_id);
	$CI->db->where('stage_id',$stage_id);
	$CI->db->where('judge_stage_status',$hear_status);
	$CI->db->where('judgement_ruling','judgement');
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblproject_judgement');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
        $jupdate = _d($row->judgement_date);
		/*return ' <div class="col-md-4">  <ul class="list-group"><li class="list-group-item">
		 <span class="bold">'. _l('judgement_date').'</span>
                     <span class="pull-right bold">'.$jupdate.'</span></li></ul></div>
					 <div class="col-md-4">  <ul class="list-group">
	<li class="list-group-item">
		 <span class="bold">'. _l('judgement_amount').'</span>
                     <span class="pull-right bold">'.$row->judgement_amount.'</span></li>
	 </ul> </div>
	 <div class="col-md-4">  <ul class="list-group">
	<li class="list-group-item">
		 <span class="bold">'. _l('hearing_outcome').'</span>
                     <span class="pull-right bold">'.ucwords($row->hearing_outcome).'</span></li>
	 </ul> </div>';*/
	return $row;
	   
		
    }

    return ' ';
}
function getfirststage_byproject($project_id)
{
	
	$CI = &get_instance();
    $CI->db->where('project_id', $project_id);
    $CI->db->limit(1);
    $CI->db->order_by('id','ASC');   
    $case_details_qry = $CI->db->get('tblcase_details');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
         $stageid = $row->instance_id;
		
        return $row->details_type;
    }

    return ' ';
           
 }
function get_position_name_by_id($id){
    $CI = &get_instance();

    if($id > 0){
      $row =    $CI->db->get_where('tblpartytypes',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function get_signparty_types (){
    return array(array('id'=>'first_party','name'=>'First Party'),
                array('id'=>'second_party','name'=>'Second Party')               
             );
}

function get_ip_class_drop_down(){
    $j=0;
    for($i=1;$i<=46; $i ++){
        $class_array[$j]['id'] = $i;
        $class_array[$j]['name'] = $i;
        $j++;
    }

    return $class_array;
}   

function get_safedocumentname($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tbldocuments',array('id'=>$id))->row();
      if($row)
        return $row->safe_uniqueno;
      else
        return ''; 
    }
}

 function get_contractinfo(){
    $CI = &get_instance();	
  $result=$CI->db->select('id,subject')->from('tblcontracts')->get()->result_array(); 
   return $result;
 }
function get_clientshareholdername($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblshareholders',array('id'=>$id))->row();
      if($row){
		  if($row->stake_type=='internal')
			  $stype='Internal Entity / Business Unit';
		  else if($row->stake_type=='external')
			  $stype='External Party';
		  else
			  $stype='Internal Individual';
        return $row->shareholder_name.' ( '.$stype.' ) ';
	  }
      else
        return ''; 
    }

}
function getfirsthearingdate($project_id=''){
	$CI = &get_instance();
   // $CI->db->where('project_id', $project_id);
    $CI->db->limit(1);
    $CI->db->order_by('hearing_date','ASC');   
    $case_details_qry = $CI->db->get('tblhearings');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
         $update = DATE($row->hearing_date);
        return $update;
    }

    return ' ';
}



function first_box() {
    
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes',['box_status'=>1])->result_array(),'id');
    $fst_box = '';
    $where = '(signed=0 or marked_as_signed=0)';

    if (!has_permission('contracts', '', 'view')) { 
        $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
    }

    $contract_list = $CI->db->order_by('tblcontracts.id', 'desc')->limit(5)->group_by('tblcontracts.id')->select('tblcontracts.id,tblcontracts.other_party,tblclients.company,tblcontracts.datestart,tblcontracts.dateend,tblcontracts.subject,tblcontracts.dateadded,tblcontracts.contract_value,tblcontracts.final_expiry_date')->from('tblcontracts')->where($where)->join('tblprojects', 'tblprojects.clientid = tblcontracts.project_id', 'left')->join('tblclients', 'tblclients.userid = tblcontracts.client', 'left')->get()->result_array();
    
    $contracts_count = $CI->db->from('tblcontracts')->where($where, NULL, FALSE)->count_all_results();

    $fst_box .= '<div class="col-md-4 ' . ((in_array(1, $active_boxes))&& (sizeof($contract_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
    $fst_box .= '<div class="panel panel-default">';
    $fst_box .= '<div class="panel-heading"><i class="fa fa-users fa-lg"></i> ' . _l('unsigned_contracts') . ' <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $contracts_count . '</a></div>';
    $fst_box .= '<div class="panel-body alen-panel"><ul class="list-group alen-ul" style="margin-bottom: 10px;">';

    if (sizeof($contract_list) > 0) {
        foreach ($contract_list as $value) {
            $fst_box .= '<li class="list-group-item">';
            if (date($value['dateend']) != "0000-00-00" && $value['dateend'] != NULL) {
                $fst_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Contract End Date">' . date($value['dateend']) . '</span>';
            }
            $fst_box .= '<a href="' . admin_url('contracts/contract/' . $value['id']) . '">' . $value['subject'] . '</a>';
            $fst_box .= '<p class="alen-p" style="margin:0 0 5px;">' . $value['contract_value'] . '</p>';
            $fst_box .= '<p style="margin:0 0 2px;"><span class="text-default">' . _l('start_date') . ' : ' . date($value['datestart']) . '</span></p>';
            $fst_box .= '<a href="' . admin_url('opposite_parties/opposite_party/' . $value['other_party']) . '">' . get_opposite_party_name($value['other_party']) . '</a>';
            $fst_box .= '</li>';
        }
    } else {
        $fst_box .= '<li class="list-group-item center_li"><p>' . _l('no_data_found') . '<i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p></li>';
        $fst_box .= '<li class="list-group-item li_new_button"><a href="' . admin_url('contracts?filter=unsigned') . '" class="btn btn-info btn-sm mb-4"><i class="fa fa-plus"></i> ' . _l('new_contract') . '</a></li>';
    }

    $fst_box .= '</ul></div>';
    $fst_box .= '<div class="panel-footer panel-footer-height">';
    $fst_box .= '<span class=""><a class="btn btn-link btn-sm" style="" target="_blank" href="' . admin_url('contracts') . '">' . _l('view_contracts') . ' <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a></span>';
    $fst_box .= '</div></div></div>';

    return $fst_box;
}
function second_box() {

    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');
    $second_box = '';
    $_where = 'DATE(hearing_date) <= "' . date('Y-m-d') . '" AND postponed="n" ';
    if (!has_permission('projects', '', 'view')) {
        if (total_rows('tblproject_members', ['staff_id' => get_staff_user_id()]) > 0) {
            $_where .= ' AND  project_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
        }
    }
    // office
    if (!is_admin() && get_option('enable_office') == 1 && has_permission('projects', '', 'view')) {
        $_where .= 'AND project_id IN (SELECT id FROM tblprojects WHERE tblprojects.company_entity=' . get_office_id() . ')';
    }
    $not_booked_hearings_list = $CI->db->order_by('DATE(hearing_date)', 'desc')->limit(5)->select('tblhearings.id as id,hearing_date,postponed_until,project_id,subject,proceedings,court_no,case_type,clientid,court_no as case_number')->from('tblhearings')->join('tblprojects', 'tblprojects.id = tblhearings.project_id', 'inner')->where($_where)->get()->result_array();
    $total_hearings_count = $CI->db->from('tblhearings')->where($_where)->count_all_results();

    $second_box .= '<div class="col-md-4 ' . ((in_array(2, $active_boxes))&& (sizeof($not_booked_hearings_list) > 0  || (is_admin())) ? '' : 'hide') . '">
        <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-th-large fa-lg"></i> ' . _l('decisions') . ' <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $total_hearings_count . '</a></div>
            <div class="panel-body alen-panel">
                <ul class="list-group mt-2" style="margin-bottom: 12px;">';

    if (sizeof($not_booked_hearings_list) > 0) {
        foreach ($not_booked_hearings_list as $key => $value) {
            $second_box .= '<li class="list-group-item color_' . date('D', strtotime($value['hearing_date'])) . '">
                <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Hearing Date" title="' . _l('hearing_date') . '">' . date('Y M d , D', strtotime($value['hearing_date'])) . '</span>
                <button type="button" class="btn btn-default btn-sm btn-icon   pop" data-container="body" data-toggle="popover" data-html="true" data-placement="bottom" data-content="' . date('Y M d ', strtotime($value['hearing_date'])) . '<hr>' . $value['proceedings'] . '"data-original-title="' . date('Y M d ', strtotime($value['hearing_date'])) . '" title="' . _l('proceedings') . '"> <i class="fa fa-tag"></i></button>
                <a  onclick="init_hearing(' . $value['id'] . ');return false;" href="#">' . $value['subject'] . '</a>
                <p style="margin:0 0 5px;"><a href="' . admin_url('projects/view/' . $value['project_id']) . '">' . get_project_name_by_id($value['project_id']) . '</a></p>
                <p style="margin:0 0 5px;">' . _l('casediary_casenumber') . ': <strong><a href="' . admin_url('projects/view/' . $value['project_id']) . '">' . $value['case_number'] . '</a></strong> | <strong>' . _l($value['case_type']) . '</strong></p>
            </li>';
        }
    } else {
        $second_box .= '<li class="list-group-item center_li">
            <p>' . _l('no_data_found') . ' <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
        </li>
        <li class="list-group-item li_new_button">
            <!-- <a onclick="init_hearing();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> ' . _l('new_hearing') . '</a> -->
        </li>';
    }

    $second_box .= '</ul>
            </div>
            <div class="panel-footer panel-footer-height">
                <span class="" > 
                    <a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('hearings/hearing?filter=without_next_session') . '">' . _l('') . '  <i class="fa fa-arrow-right fa-lg mleft5 hide" aria-hidden="true"></i></a>
                </span>
            </div>
        </div>
    </div>';

    // Return the generated HTML content
    return $second_box;
}

function third_box(){

    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  
    $_where = 'current_stage_status = 1';
    
        if (!has_permission('projects', '', 'view')) {
            $_where .= ' AND  tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
        }
    
        $judgment_cases_list = $CI->db->query("SELECT id, name,case_type,clientid,start_date,(SELECT hearing_date FROM tblhearings WHERE tblprojects.id = tblhearings.project_id AND hearing_stage_status = 1 GROUP BY project_id ORDER BY tblhearings.id DESC LIMIT 1) as judgement_date,(SELECT tblhearings.court_no FROM tblhearings  INNER JOIN tblprojects ON tblprojects.id = tblhearings.project_id GROUP BY project_id ORDER BY tblhearings.id DESC LIMIT 1) as case_number FROM tblprojects WHERE    ".$_where."  ORDER BY id DESC LIMIT 5")->result_array();
    
        $total_judgment_cases_count = $CI->db->from('tblprojects')->where($_where,NULL,FALSE)->count_all_results();
    
        // Start building the HTML content
         $output = '<div class="col-md-4 ' . ((in_array(3, $active_boxes))&& (sizeof($judgment_cases_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
        $output .= '<div class="panel panel-default">';
        $output .= '<div class="panel-heading"><i class="fa fa-balance-scale fa-lg"></i>  ' . _l('judgment_cases') . '  <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $total_judgment_cases_count . '</a></div>';
        $output .= '<div class="panel-body alen-panel">';
    
        $output .= '<ul class="list-group mt-2">';
        if (sizeof($judgment_cases_list) > 0) {
            foreach ($judgment_cases_list as $key => $value) {
                $output .= '<li class="list-group-item">';
                $output .= '<span class="badge badge-dashboard">' . date('Y F d', strtotime($value['judgement_date'])) . '</span>';
                $output .= '<button type="button" class="btn btn-default btn-sm btn-icon pop" data-container="body" data-toggle="popover" data-html="true"  data-placement="bottom" data-content="' . date('Y M d ', strtotime($value['judgement_date'])) . '<hr>' . '" data-original-title="' . date('Y M d ', strtotime($value['judgement_date'])) . '" title="' . _l('judgment') . '"> <i class="fa fa-tag"></i></button>';
                $output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '">' . $value['name'] . '</a>';
                $output .= '<p class="alen-p ">' . _l('casediary_casenumber') . ': <strong>' . $value['case_number'] . '</strong> | <strong>' . _l($value['case_type']) . '</strong></p>';
                $output .= '</li>';
            }
        } else {
            $output .= '<li class="list-group-item center_li">';
            $output .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
            $output .= '<br>';
            $output .= '</li>';
        }
    
        $output .= '</ul>';
        $output .= '</div>';
        $output .= '<div class="panel-footer panel-footer-height">';
        $output .= '<span class="" >';
        $output .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('projects/matters') . '">' . _l('') . '  <i class="fa fa-arrow-right fa-lg mleft5 hide" aria-hidden="true"></i></a>';
        $output .= '</span>';
        $output .= '</div>';
        $output .= '</div>';
        $output .= '</div>';
    
        // Return the generated content
        return $output; 
}

function fourth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id'); 
    $_where = 'duedate IS NOT NULL AND duedate != " " AND status != ' . Tasks_model::STATUS_COMPLETE;
    
        if (!has_permission('tasks', '', 'view')) {
            $_where .= ' AND ' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';
        }
    
        $tasks_list = $CI->db->order_by('duedate', 'asc')->limit(5)->select('id,name,status,startdate,duedate')->from('tbltasks')->where($_where)->get()->result_array();
        $todays_tasks_count = $CI->db->from('tbltasks')->where($_where)->count_all_results();
    
        // Start building the HTML content
         $seventh_box = '<div class="col-md-4 ' . ((in_array(4, $active_boxes))&& (sizeof($tasks_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
        $seventh_box .= '<div class="panel panel-default">';
        $seventh_box .= '<div class="panel-heading"><i class="fa fa-tasks fa-lg" aria-hidden="true"></i> ' . _l('tasks') . ' <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $todays_tasks_count . '</a></div>';
        $seventh_box .= '<div class="panel-body alen-panel">';
    
        $seventh_box .= '<ul class="list-group">';
        if (sizeof($tasks_list) > 0) {
            foreach ($tasks_list as $key => $value) {
                $status = get_task_status_by_id($value['status']);
                $seventh_boxStatus = '';
                $seventh_boxStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $value['status'] . '">';
                $seventh_boxStatus .= $status['name'];
                $seventh_boxStatus .= '</span>';
    
                $seventh_box .= '<li class="list-group-item">';
                $seventh_box .= '<a href="javascript:void(0);" onclick="init_task_modal(\'' . $value['id'] . '\'); return false;">' . $value['name'] . '</a>';
                $seventh_box .= '<span class="pull-right">' . $seventh_boxStatus . '</span>';
                $seventh_box .= '<p style="margin:0 0 2px;" ><span class="text-default"> ' . _l('task_add_edit_start_date') . '  : ' . date('Y M d', strtotime($value['startdate'])) . ' </span> </p>';
                $seventh_box .= '<p style="margin:0 0 12px;" > ' . _l('task_add_edit_due_date') . ' : <span class="text-danger"> ' . date('Y M d', strtotime($value['duedate'])) . '</span></p>';
                $seventh_box .= '</li>';
            }
        } else {
            $seventh_box .= '<li class="list-group-item center_li">';
            $seventh_box .= '<p>' . _l('no_data_found') . ' <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
            $seventh_box .= '</li>';
            $seventh_box .= '<li class="list-group-item li_new_button">';
            $seventh_box .= '<a onclick="new_task();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> ' . _l('new_task') . '</a>';
            $seventh_box .= '</li>';
        }
    
        $seventh_box .= '</ul>';
        $seventh_box .= '</div>';
        $seventh_box .= '<div class="panel-footer panel-footer-height">';
        $seventh_box .= '<span class="" >';
        $seventh_box .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('tasks') . '">' . _l('view_all_tasks') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
        $seventh_box .= '</span>';
        $seventh_box .= '</div>';
        $seventh_box .= '</div>';
        $seventh_box .= '</div>';
    
        // Return the generated content
        return $seventh_box;
}

function fifth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id'); 
    
    $_where = 'staff =  ' . get_staff_user_id() . ' ';
        $my_reminders = $CI->db->order_by('id', 'desc')->limit(5)->select('*')->from('tblreminders')->where($_where)->get()->result_array();
        $my_reminders_count = $CI->db->from('tblreminders')->where($_where)->count_all_results();
    
        // Start building the HTML content
        $ninth_box = '<div class="col-md-4 ' . ((in_array(5, $active_boxes))&& (sizeof($my_reminders) > 0) ? '' : 'hide') . ' ">';
        $ninth_box .= '<div class="panel panel-default">';
        $ninth_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('my') . ' ' . _l('reminders') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $my_reminders_count . '</a> </div>';
        $ninth_box .= '<div class="panel-body alen-panel">';
    
        $ninth_box .= '<ul class="list-group">';
        if (sizeof($my_reminders) > 0) {
            foreach ($my_reminders as $key => $value) {
                $rel_data = get_relation_data($value['rel_type'], $value['rel_id']);
                $rel_values = get_relation_values($rel_data, $value['rel_type']);
                $_data = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
    
                $ninth_box .= '<li class="list-group-item">';
                $ninth_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Reminder Date">' . date('Y M d', strtotime($value['date'])) . '</span>';
                $ninth_box .= '<p>' . $value['description'] . '</p>';
                $ninth_box .= $_data;
                $ninth_box .= '</li>';
            }
        } else {
            $ninth_box .= '<li class="list-group-item center_li">';
            $ninth_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
            $ninth_box .= '</li>';
            $ninth_box .= '<li class="list-group-item li_new_button">';
            // Uncomment the line below if needed
            // $ninth_box .= '<a onclick="new_quick_reminder();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> ' . _l('reminder') . '</a>';
            $ninth_box .= '</li>';
        }
    
        $ninth_box .= '</ul>';
        $ninth_box .= '</div>';
        $ninth_box .= '<div class="panel-footer panel-footer-height">';
        $ninth_box .= '<span class="" >';
        $ninth_box .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('misc/reminders') . '">' . _l('view_all_reminder') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
        $ninth_box .= '</span>';
        $ninth_box .= '</div>';
        $ninth_box .= '</div>';
        $ninth_box .= '</div>';
    
        // Return the generated content
        return $ninth_box;
} 
function sixth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id'); 
    $_where = 'status IN (1,2)';
        if (!has_permission('tickets', '', 'view')) {
            $_where .= ' AND assigned = ' . get_staff_user_id();   
        }
    
        $tickets_list = $CI->db->order_by('DATE(date)', 'desc')->limit(5)->select('ticketid,name,userid,subject,message,date')->from('tbltickets')->where($_where)->get()->result_array(); 
        $tickets_count = $CI->db->from('tbltickets')->where($_where)->count_all_results();
    
        // Start building the HTML content
        $tenth_box = '<div class="col-md-4 ' . ((in_array(6, $active_boxes))&& (sizeof($tickets_list) > 0  || (is_admin())) ? '' : 'hide') . ' ">';
        $tenth_box .= '<div class="panel panel-default">';
        $tenth_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('new_legal_requests') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $tickets_count . '</a> </div>';
        $tenth_box .= '<div class="panel-body alen-panel">';
    
        $tenth_box .= '<ul class="list-group">';
        if (sizeof($tickets_list) > 0) {
            foreach ($tickets_list as $key => $value) {
                $tenth_box .= '<li class="list-group-item">';
                $tenth_box .= '<span class="badge badge-dashboard hide">' . date('Y M d', strtotime($value['date'])) . '</span>';
                $tenth_box .= '<a target="_blank" href="' . admin_url('tickets/ticket/' . $value['ticketid']) . '">' . $value['subject'] . '</a>';
                $tenth_box .= '<p style="margin:0 0 5px;">' . get_company_name($value['userid']) . '</p>';
                $tenth_box .= '<p  style="margin:0 0 5px;" ><strong>' . _l($value['name']) . '</strong></p>';
                $tenth_box .= '</li>';
            }
        } else {
            $tenth_box .= '<li class="list-group-item center_li">';
            $tenth_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
            $tenth_box .= '</li>';
        }
    
        $tenth_box .= '</ul>';
        $tenth_box .= '</div>';
        $tenth_box .= '<div class="panel-footer panel-footer-height">';
        $tenth_box .= '<span class="" >';
	
        $tenth_box .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('tickets') . '">' . _l('view_all_legal_request') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
        $tenth_box .= '</span>';
        $tenth_box .= '</div>';
        $tenth_box .= '</div>';
        $tenth_box .= '</div>';
    
        // Return the generated content
        return $tenth_box;
}
function seventh_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id'); 
    if (!is_admin()) {
        return ''; // Return an empty string if the user is not an admin
    }
    
    $_where = 'expiry_date IS NOT NULL AND expiry_date != " " AND expiry_date BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 30 DAY)';
    $project_documents_list = $CI->db->order_by('DATE(expiry_date)', 'desc')->limit(5)->select('id,document_type,expiry_date,subject,project_id')->from('tblproject_files')->where($_where)->get()->result_array();
    $subfile_documents_list = $CI->db->order_by('DATE(expiry_date)', 'desc')->limit(5)->select('id,userid,document_type,expiry_date,subject,issue_date')->from('tblclient_subfile')->where($_where)->get()->result_array();
    $project_documents_count = $CI->db->from('tblproject_files')->where($_where)->count_all_results();
    $subfile_documents_count = $CI->db->from('tblclient_subfile')->where($_where)->count_all_results();
    $total_doc_count = $project_documents_count + $subfile_documents_count;
    
    $i = 0;
    $doc_lists = [];
    foreach ($project_documents_list as $key => $value) {
        $value['related_to'] = 'Project';
        $doc_lists[$i] = $value;
        $i++;
    }
    foreach ($subfile_documents_list as $key => $value) {
        $value['related_to'] = 'Client';
        $doc_lists[$i] = $value;
        $i++;
    }
    
    // Start building the HTML content
    $leventh_box = '<div class="col-md-4 ' . ((in_array(7, $active_boxes))&& (sizeof($doc_lists) > 0) ? '' : 'hide') . ' ">';
    $leventh_box .= '<div class="panel panel-default">';
    $leventh_box .= '<div class="panel-heading"><i class="fa fa-pencil-square fa-lg" aria-hidden="true"></i> ' . _l('documents_expiry') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $total_doc_count . '</a></div>';
    $leventh_box .= '<div class="panel-body alen-panel">';
    $leventh_box .= '<ul class="list-group">';
    if (sizeof($doc_lists) > 0) {
        foreach ($doc_lists as $key => $value) {
            $leventh_box .= '<li class="list-group-item">';
            $leventh_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Document Expiry Date">' . date($value['expiry_date']) . '</span>';
            $leventh_box .= '<p>' . $value['subject'] . '</p>';
            $leventh_box .= '<p>Related To : ' . $value['related_to'] . '</p>';
            if (isset($value['project_id'])) {
                $leventh_box .= '<p style="margin:0 0 5px;"><a target="_blank" href="' . admin_url('projects/view/' . $value['project_id']) . '">' . get_project_name_by_id($value['project_id']) . '</a></p>';
            }
            if (isset($value['userid'])) {
                $leventh_box .= '<p style="margin:0 0 5px;"><a target="_blank" href="' . admin_url('clients/client/' . $value['userid']) . '">' . get_company_name($value['userid']) . '</a></p>';
            }
            $leventh_box .= '</li>';
        }
    } else {
        $leventh_box .= '<li class="list-group-item center_li">';
        $leventh_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
        $leventh_box .= '</li>';
    }
    $leventh_box .= '</ul>';
    $leventh_box .= '</div>';
    $leventh_box .= '<div class="panel-footer panel-footer-height">';
    $leventh_box .= '<span class="" >';
    // $leventh_box .= '<a class="btn btn-link btn-sm " target="_blank"  href="' . admin_url('defence_papers') . '">' . _l('view_all') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
    $leventh_box .= '</span>';
    $leventh_box .= '</div>';
    $leventh_box .= '</div>';
    $leventh_box .= '</div>';
    
    // Return the generated content
    return $leventh_box;

}
function eighth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  
    
    $where = ' dateend BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 30 DAY)';
        
        if (!has_permission('contracts', '', 'view')) {
            $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
        }
    
        $renewal_contract_list = $CI->db->order_by('DATE(tblcontracts.dateend)', 'desc')->limit(5)->group_by('tblcontracts.id')->select('tblcontracts.id,tblclients.company,tblcontracts.subject,tblcontracts.dateadded,tblcontracts.dateend,tblcontracts.contract_value,tblcontracts.final_expiry_date')->from('tblcontracts')->where($where)->join('tblprojects', 'tblprojects.clientid = tblcontracts.project_id', 'left')->join('tblclients', 'tblclients.userid = tblcontracts.client', 'left')->get()->result_array();
        $renewal_contract_count = $contracts_count = $CI->db->from('tblcontracts')->where($where, NULL, FALSE)->count_all_results();
    
        // Start building the HTML content
        $twelvth_box = '<div class="col-md-4 ' . ((in_array(8, $active_boxes))&& (sizeof($renewal_contract_list) > 0  || (is_admin())) ? '' : 'hide') . ' ">';
        $twelvth_box .= '<div class="panel panel-default">';
        $twelvth_box .= '<div class="panel-heading"><i class="fa fa-paper-plane-o fa-lg" aria-hidden="true"></i> ' . _l('contract_renewal') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $renewal_contract_count . '</a></div>';
        $twelvth_box .= '<div class="panel-body alen-panel">';
        $twelvth_box .= '<ul class="list-group">';
        if (sizeof($renewal_contract_list) > 0) {
            foreach ($renewal_contract_list as $key => $value) {
                $twelvth_box .= '<li class="list-group-item">';
                $twelvth_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Contract Renewal End date">' . $value['dateend'] . '</span>';
                $twelvth_box .= '<a  href="' . admin_url('contracts/contract/' . $value['id']) . '">' . $value['subject'] . '</a>';
                $twelvth_box .= '<p class="alen-p" style="margin:0 0 5px;">' . $value['contract_value'] . ' | ' . $value['company'] . '</p>';
                $twelvth_box .= '</li>';
            }
        } else {
            $twelvth_box .= '<li class="list-group-item center_li">';
            $twelvth_box .= '<p>' . _l('no_data_found') . ' <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
            $twelvth_box .= '</li>';
        }
        $twelvth_box .= '</ul>';
        $twelvth_box .= '</div>';
        $twelvth_box .= '<div class="panel-footer panel-footer-height">';
        $twelvth_box .= '<span class="" >';
        $twelvth_box .= '<a class="btn btn-link btn-sm"  target="_blank"  href="#">' . _l('') . '  <i class="fa fa-arrow-right fa-lg mleft5 hide" aria-hidden="true"></i></a>';
        $twelvth_box .= '</span>';
        $twelvth_box .= '</div>';
        $twelvth_box .= '</div>';
        $twelvth_box .= '</div>';
    
        // Return the generated content
        return $twelvth_box;
    
}
function ninth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');   
    $type = '"contract"';
    $where = 'rel_type=' . $type . ' AND approval_status=2 AND staffid=' . get_staff_user_id();
    
    $my_approvals = $CI->db->order_by('id', 'desc')->limit(5)->select('tblapprovals.id,tblapprovals.rel_id,tblapprovals.approval_type,tblapprovals.approval_name,tblapprovals.dateadded,tblapprovals.approval_remarks,tblcontracts.subject')->from('tblapprovals')->where($where)->join('tblcontracts', 'tblcontracts.id = tblapprovals.rel_id', 'left')->get()->result_array();
    $my_approvals_count = $CI->db->from('tblapprovals')->where($where)->count_all_results();
    
     $third_box = '<div class="col-md-4 ' . ((in_array(9, $active_boxes))&& (sizeof($my_approvals) > 0) ? '' : 'hide') . '">
        <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' ._l('contract_approvals') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $my_approvals_count . '</a></div>
            <div class="panel-body alen-panel">
                <ul class="list-group">';
    
    if (sizeof($my_approvals) > 0) {
        foreach ($my_approvals as $key => $value) {
            $third_box .= '<li class="list-group-item">
                <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Approval Date Added">' . date('Y M d', strtotime($value['dateadded'])) . '</span>
                <a href="' . admin_url('contracts/contract/' . $value['rel_id'] . '?tab=approvals') . '">' . $value['approval_name'] . '</a><br>
                <a href="' . admin_url('contracts/contract/' . $value['rel_id']) . '">' . $value['subject'] . '</a>
                <p>' . $value['approval_remarks'] . '</p>
            </li>';
        }
    } else {
        $third_box .= '<li class="list-group-item center_li">
            <p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>
        </li>';
    }
    
    $third_box .= '</ul>
            </div>
            <div class="panel-footer panel-footer-height">
                <span class=""><a class="btn btn-link btn-sm" style="" target="_blank" href="#"></a></span>
            </div>
        </div>
    </div>';
    
    // Return the generated HTML content
    return $third_box;
    
}

function tenth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');      
    $type = '"ticket"';
    $where = 'rel_type=' . $type . ' AND approval_status=2 AND staffid=' . get_staff_user_id();
    
    $my_legal_approvals = $CI->db->order_by('id', 'desc')->limit(5)->select('id,rel_id,rel_type,approval_type,staffid,approval_name,dateadded,approve_expectdt,approval_remarks')->from('tblapprovals')->where($where)->get()->result_array();
    $my_legal_approvals_count = $CI->db->from('tblapprovals')->where($where)->count_all_results();
    
    $fourth_box = '<div class="col-md-4 ' . ((in_array(10, $active_boxes))&& (sizeof($my_legal_approvals) > 0) ? '' : 'hide') . '">
        <div class="panel panel-default">
            <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('legal_request_approvals') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $my_legal_approvals_count . '</a></div>
            <div class="panel-body alen-panel">
                <ul class="list-group">';
    
    if (sizeof($my_legal_approvals) > 0) {
        foreach ($my_legal_approvals as $key => $value) {
            $rel_data = get_relation_data($value['rel_type'], $value['rel_id']);
            $rel_values = get_relation_values($rel_data, $value['rel_type']);
            $_data = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
    
            $fourth_box .= '<li class="list-group-item">
                <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Legal Request Approval Added Date">' . date('Y M d', strtotime($value['dateadded'])) . '</span>
                <a href="' . admin_url('tickets/ticket/' . $value['rel_id'] . '?tab=approvals') . '">' . $value['approval_name'] . '</a><br>
                <p>' . $value['approval_remarks'] . '</p>
                <p>' . $_data . '</p>';
    
            if ($value['approve_expectdt'] != '' || $value['approve_expectdt'] != NULL) {
                $fourth_box .= '<p>' . _l('approval_expected_date') . ':' . date($value['approve_expectdt']) . '</p>';
            }
    
            $fourth_box .= '</li>';
        }
    } else {
        $fourth_box .= '<li class="list-group-item center_li">
            <p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>
        </li>';
    }
    
    $fourth_box .= '</ul>
            </div>
            <div class="panel-footer panel-footer-height">
                <span class=""><a class="btn btn-link btn-sm" style="" target="_blank" href="#"><p class="hide">' . _l('view_all_reminders') . '</p></a></span>
            </div>
        </div>
    </div>';
    
    // Return the generated HTML content
    return $fourth_box; 
}
function leventh_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id'); 
    
    $_where = 'DATE(hearing_date) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) ';
    
        if (!has_permission('projects', '', 'view')) {
            $_where .= ' AND project_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
        }
    
        $hearings_list = $CI->db->order_by('DATE(hearing_date)', 'desc')->limit(5)->select('tblhearings.id as id,hearing_date,postponed_until,project_id,subject,proceedings,court_no,case_type,clientid,court_no as case_number')->from('tblhearings')->join('tblprojects', 'tblprojects.id = tblhearings.project_id', 'inner')->where($_where)->get()->result_array();
        $next_week_hearings_count = $CI->db->from('tblhearings')->where($_where)->count_all_results();
    
        // Start building the HTML content
        $eighth_box = '<div class="col-md-4 ' . ((in_array(11, $active_boxes))&& (sizeof($hearings_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
        $eighth_box .= '<div class="panel panel-default">';
        $eighth_box .= '<div class="panel-heading"><i class="fa fa-calendar-plus-o fa-lg" aria-hidden="true"></i> ' . _l('next_week_hearings') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $next_week_hearings_count . '</a></div>';
        $eighth_box .= '<div class="panel-body alen-panel">';
    
        $eighth_box .= '<ul class="list-group">';
        if (sizeof($hearings_list) > 0) {
            foreach ($hearings_list as $key => $value) {
                $eighth_box .= '<li class="list-group-item">';
                $eighth_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Hearing Date">' . date('Y M d', strtotime($value['hearing_date'])) . '</span>';
                $eighth_box .= '<a href="#" onclick="init_hearing(' . $value['id'] . ');return false;">' . $value['subject'] . '</a>';
                $eighth_box .= '<p style="margin:0 0 5px;">' . get_project_name_by_id($value['project_id']) . '</p>';
                $eighth_box .= '<p style="margin:0 0 5px;">' . _l('casediary_casenumber') . ': <strong>' . $value['case_number'] . '</strong> | <strong>' . _l($value['case_type']) . '</strong></p>';
                $eighth_box .= '</li>';
            }
        } else {
            $eighth_box .= '<li class="list-group-item center_li">';
            $eighth_box .= '<p>' . _l('no_data_found') . ' <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
            $eighth_box .= '</li>';
            $eighth_box .= '<li class="list-group-item li_new_button">';
            // Uncomment the line below if needed
            // $eighth_box .= '<a onclick="init_hearing();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> ' . _l('new_hearing') . '</a>';
            $eighth_box .= '</li>';
        }
    
        $eighth_box .= '</ul>';
        $eighth_box .= '</div>';
        $eighth_box .= '<div class="panel-footer panel-footer-height">';
        $eighth_box .= '<span class="" >';
        $eighth_box .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('hearings') . '">' . _l('view_all_hearings') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
        $eighth_box .= '</span>';
        $eighth_box .= '</div>';
        $eighth_box .= '</div>';
        $eighth_box .= '</div>';
    
        // Return the generated content
        return $eighth_box;
    
}

function twelth_box() {
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');

    $where = '(marked_as_signed=1 OR (signed=1 AND party_signed=1))';
    if (!has_permission('contracts', '', 'view')) {
        $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
    }

    $contract_list = $CI->db->order_by('tblcontracts.id', 'desc')->limit(5)->group_by('tblcontracts.id')->select('tblcontracts.id,tblcontracts.other_party,tblclients.company,tblcontracts.datestart,tblcontracts.dateend,tblcontracts.subject,tblcontracts.dateadded,tblcontracts.contract_value,tblcontracts.final_expiry_date,tblcontracts.acceptance_date,tblcontracts.signed_contract_filename')->from('tblcontracts')->where($where)->join('tblprojects', 'tblprojects.clientid = tblcontracts.project_id', 'left')->join('tblclients', 'tblclients.userid = tblcontracts.client', 'left')->get()->result_array();
    $contracts_count = $CI->db->from('tblcontracts')->where($where, NULL, FALSE)->count_all_results();

    $twelth_box = '<div class="col-md-4 ' . ((in_array(12, $active_boxes))&& (sizeof($contract_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
    $twelth_box .= '<div class="panel panel-default">';
    $twelth_box .= '<div class="panel-heading"><i class="fa fa-users fa-lg"></i> ' . _l('signed_contracts') . ' <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $contracts_count . '</a></div>';
    $twelth_box .= '<div class="panel-body alen-panel"><ul class="list-group alen-ul" style="margin-bottom: 10px;">';

    if (sizeof($contract_list) > 0) {
        foreach ($contract_list as $value) {
            $extension = pathinfo($value['signed_contract_filename'], PATHINFO_EXTENSION);

            $twelth_box .= '<li class="list-group-item">';
            $twelth_box .= '<a class="badge" style="background-color: #807B7A;padding: 6px;border-radius: 4px;font-weight: 544;" data-toggle="tooltip" data-placement="top" title="Signed Date" href="#">' . date($value['acceptance_date']) . '</a>';

            if (has_permission('contracts', '', 'view')) {
                //if ($extension == 'docx' || $extension == 'doc') {
                // Uncomment the code block below if needed
                // $twelth_box .= '<a class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Download Signed Document" href="' . site_url('download/downloadsigned_agreement/' . $value['id']) . '">' . _l('download') . '</a>';
                //}
            }

            $twelth_box .= '<a href="' . admin_url('contracts/contract/' . $value['id']) . '">' . $value['subject'] . '</a>';
            $twelth_box .= '<p class="alen-p" style="margin:0 0 3px;">' . $value['contract_value'] . ' <br>';
            $twelth_box .= '<a href="' . admin_url('opposite_parties/opposite_party/' . $value['other_party']) . '">' . get_opposite_party_name($value['other_party']) . '</a><br>';

            $file_path = base_url('uploads/contracts/') . $value['id'] . '/' . $value['signed_contract_filename'];
            $twelth_box .= '<a class="badge" data-toggle="tooltip" data-placement="top" title="View Document" href="' . admin_url('contracts/pdf/' . $value['id'] . '?output_type=I') . '" target="_blank">' . _l('view') . '</a>';
            $twelth_box .= '</li>';
        }
    } else {
        $twelth_box .= '<li class="list-group-item center_li">';
        $twelth_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
        $twelth_box .= '</li>';
        $twelth_box .= '<li class="list-group-item li_new_button">';
        $twelth_box .= '<a href="' . admin_url('contracts?filter=signed') . '" class="btn btn-info btn-sm mb-4"><i class="fa fa-plus"></i> ' . _l('new_contract') . '</a>';
        $twelth_box .= '</li>';
    }

    $twelth_box .= '</ul></div>';
    $twelth_box .= '<div class="panel-footer panel-footer-height">';
    $twelth_box .= '<span class=""><a class="btn btn-link btn-sm" style="" target="_blank" href="' . admin_url('contracts') . '">' . _l('view_contractss') . ' <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a></span>';
    $twelth_box .= '</div></div></div>';

    return $twelth_box;
}

function thurtinth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  

    $where = 'meeting_date BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 10 DAY)';

    $my_meetings = $CI->db->order_by('meeting_date', 'asc')->limit(5)->select('tblprojectdiscussions.*,tblprojects.name')->from('tblprojectdiscussions')->where($where)->join('tblprojects', 'tblprojects.id = tblprojectdiscussions.project_id', 'left')->get()->result_array();
    $my_meetings_count = $CI->db->from('tblprojectdiscussions')->where($where)->count_all_results();

    // Start building the HTML content
    $thurtinth_box = '<div class="col-md-4 ' . ((in_array(13, $active_boxes))&& (sizeof($my_meetings) > 0 ) ? '' : 'hide') . ' ">';
    $thurtinth_box .= '<div class="panel panel-default">';
    $thurtinth_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('my_meetings') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $my_meetings_count . '</a> </div>';
    $thurtinth_box .= '<div class="panel-body alen-panel">';
    $thurtinth_box .= '<ul class="list-group">';
    if (sizeof($my_meetings) > 0) {
        foreach ($my_meetings as $key => $value) {
            $thurtinth_box .= '<li class="list-group-item">';
            $thurtinth_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Meeting Date">' . date('Y M d', strtotime($value['meeting_date'])) . '</span>';
            $thurtinth_box .= '<a  href="' . admin_url('projects/view/' . $value['project_id'] . '?group=project_discussions') . '" onclick="#">' . $value['name'] . '</a><br>';
            $thurtinth_box .= '<p data-toggle="tooltip" data-placement="left" title="Subject">' . $value['subject'] . '</p>';
            $thurtinth_box .= '<p >' . $value['location'] . '</p>';
            $thurtinth_box .= '</li>';
        }
    } else {
        $thurtinth_box .= '<li class="list-group-item center_li">';
        $thurtinth_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
        $thurtinth_box .= '</li>';
    }
    $thurtinth_box .= '</ul>';
    $thurtinth_box .= '</div>';
    $thurtinth_box .= '<div class="panel-footer panel-footer-height">';
    $thurtinth_box .= '<span class="" > ';
    // You can add additional content or links here if needed
    $thurtinth_box .= '</span>';
    $thurtinth_box .= '</div>';
    $thurtinth_box .= '</div>';
    $thurtinth_box .= '</div>';

    // Return the generated content
    return $thurtinth_box;
}
function fourteenth_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  

    $_where = 'status=3';
    //$project_status = get_project_status_by_id(3);
    if(!has_permission('projects','','view')){
        $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
    }

    $cases_under_settlement = $CI->db->order_by('project_created', 'asc')->limit(5)->select('id,name,project_created,outstanding_amount,settlement_type')->from('tblprojects')->where($_where)->get()->result_array();
    $cases_under_settlement_count = $CI->db->from('tblprojects')->where($_where)->count_all_results();

    // Start building the HTML content
   $fourteenth_box = '<div class="col-md-4 ' . ((in_array(14, $active_boxes))&& (sizeof($cases_under_settlement) > 0  || (is_admin())) ? '' : 'hide') . ' ">';
    $fourteenth_box .= '<div class="panel panel-default">';
    $fourteenth_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('project_status_3') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $cases_under_settlement_count . '</a> </div>';
    $fourteenth_box .= '<div class="panel-body alen-panel">';
    $fourteenth_box .= '<ul class="list-group">';
    if (sizeof($cases_under_settlement) > 0) {
        foreach ($cases_under_settlement as $key => $value) {
            $fourteenth_box .= '<li class="list-group-item">';
            $fourteenth_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Project Created Date">' . date('Y M d', strtotime($value['project_created'])) . '</span>';
            $fourteenth_box .= '<a  href="' . admin_url('projects/view/'.$value['id'].'?group=project_settlement') . '" onclick="#">' . $value['name'] . '</a><br>';
            $fourteenth_box .= '<p data-toggle="tooltip" data-placement="left" title="Settlement Amount">' . $value['outstanding_amount'] . '</p>';
            $fourteenth_box .= '<p data-toggle="tooltip" data-placement="left" title="Settlement Type">' . $value['settlement_type'] . '</p>';
            $fourteenth_box .= '</li>';
        }
    } else {
        $fourteenth_box .= '<li class="list-group-item center_li">';
        $fourteenth_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
        $fourteenth_box .= '</li>';
    }
    $fourteenth_box .= '</ul>';
    $fourteenth_box .= '</div>';
    $fourteenth_box .= '<div class="panel-footer panel-footer-height">';
    $fourteenth_box .= '<span class="" > ';
    // You can add additional content or links here if needed
    $fourteenth_box .= '</span>';
    $fourteenth_box .= '</div>';
    $fourteenth_box .= '</div>';
    $fourteenth_box .= '</div>';

    // Return the generated content
    return $fourteenth_box;
}
function ip_trademark_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  

    $_where = 'case_type="intellectual_property" and ip_category=1';
    //$project_status = get_project_status_by_id(3);
    if(!has_permission('projects','','view')){
        $_where = 'tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
    }

    $ip_trademarks = $CI->db->order_by('project_created', 'asc')->limit(5)->select('tblprojects.id,name,tblip_subcategory.subcategory_name as ip_subcategory,current_application_no,ip_class,ip_regno,ip_filingdt,case_type,file_no,ip_logo')->from('tblprojects')->where($_where)->join('tblip_subcategory', 'tblip_subcategory.category_id=tblprojects.ip_subcategory','left')->get()->result_array();
    $cip_trademarks_count = $CI->db->from('tblprojects')->where($_where)->join('tblip_subcategory', 'tblip_subcategory.category_id=tblprojects.ip_subcategory','left')->count_all_results();

    // Start building the HTML content
    $ip_trademark_box = '<div class="col-md-4 ' . ((in_array(15, $active_boxes))&& (sizeof($ip_trademarks) > 0  || (is_admin())) ? '' : 'hide') . ' ">';
    $ip_trademark_box .= '<div class="panel panel-default">';
    $ip_trademark_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('latest_ip_trademarks') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $cip_trademarks_count . '</a> </div>';
    $ip_trademark_box .= '<div class="panel-body alen-panel">';
    $ip_trademark_box .= '<ul class="list-group">';
    if (sizeof($ip_trademarks) > 0) {
        foreach ($ip_trademarks as $key => $value) {
            $ip_trademark_box .= '<li class="list-group-item">';
            if($value['ip_filingdt']!=null && $value['ip_filingdt']!="0000-00-00"){
                $ip_trademark_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="IP Filing Date">' . date('Y M d', strtotime($value['ip_filingdt'])) . '</span>';
            }
            $ip_trademark_box .= '<a  href="' . admin_url('projects/view/'.$value['id'].'') . '" onclick="#">' . $value['name'] . '</a><br>';
            if($value['ip_subcategory']!=''){
            $ip_trademark_box .= '<p class="alen-p "><strong>' . _l('ip_subcategory') . '</strong>: ' . $value['ip_subcategory'].' </p>';
            }
            if($value['current_application_no']!=''){
                $ip_trademark_box .= '<p class="alen-p "><strong>' . _l('current_application_no') . '</strong>: ' . $value['current_application_no'].'  |';
            }
            if($value['ip_class']!=''){
            $ip_trademark_box .= '<strong>' . _l('ip_class') . '</strong>: ' . $value['ip_class'].'  |';
            }
            if($value['ip_regno']!=''){
            $ip_trademark_box .= '<strong>' . _l('ip_regno') . '</strong>: ' . $value['ip_regno'].'  |</p>';
            }
            
            $file_type=get_file_extension($value['ip_logo']);
            $upload_path = get_upload_path_by_type('project');
            $path=$upload_path . $value['id'] . '/' . $value['ip_logo'];
            $is_image = is_image($path);
            
            
            $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$file_type);
            $lightBoxUrl = site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$file_type);
            if($is_image){
                
                $ip_trademark_box .= '<a class="badge" style="width: 62px;" href="' . $lightBoxUrl . '" data-lightbox="customer-profile" class="display-block mbot5">View</a>';
            }

            $downloadLink = site_url('download/downloadlogofile/'.$value['id'].'/'.$value['ip_logo']);
            $ip_trademark_box .= '<a class="badge pull-right" href="' . $downloadLink . '">Download</a><br>';
            
            $ip_trademark_box .= '</li>';
        }
    } else {
        $ip_trademark_box .= '<li class="list-group-item center_li">';
        $ip_trademark_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
        $ip_trademark_box .= '</li>';
    }
    $ip_trademark_box .= '</ul>';
    $ip_trademark_box .= '</div>';
    $ip_trademark_box .= '<div class="panel-footer panel-footer-height">';
    $ip_trademark_box .= '<span class="" > ';
    // You can add additional content or links here if needed
    $ip_trademark_box .= '</span>';
    $ip_trademark_box .= '</div>';
    $ip_trademark_box .= '</div>';
    $ip_trademark_box .= '</div>';

    // Return the generated content
    return $ip_trademark_box;
}
function ip_patent_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  

    $_where = 'case_type="intellectual_property" and ip_category=3';
    //$project_status = get_project_status_by_id(3);
    if(!has_permission('projects','','view')){
        $_where = 'tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
    }

    $ip_patents = $CI->db->order_by('project_created', 'asc')->limit(5)->select('tblprojects.id,name,tblip_subcategory.subcategory_name as ip_subcategory,current_application_no,ip_class,ip_regno,ip_filingdt,case_type,file_no,ip_logo')->from('tblprojects')->where($_where)->join('tblip_subcategory', 'tblip_subcategory.category_id=tblprojects.ip_subcategory','left')->get()->result_array();
    $ip_patents_count = $CI->db->from('tblprojects')->where($_where)->join('tblip_subcategory', 'tblip_subcategory.category_id=tblprojects.ip_subcategory','left')->count_all_results();

    // Start building the HTML content
    $ip_patent_box = '<div class="col-md-4 ' . ((in_array(16, $active_boxes))&& (sizeof($ip_patents) > 0  || (is_admin())) ? '' : 'hide') . ' ">';
    $ip_patent_box .= '<div class="panel panel-default">';
    $ip_patent_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('latest_ip_patent') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $ip_patents_count . '</a> </div>';
    $ip_patent_box .= '<div class="panel-body alen-panel">';
    $ip_patent_box .= '<ul class="list-group">';
    if (sizeof($ip_patents) > 0) {
        foreach ($ip_patents as $key => $value) {
            $ip_patent_box .= '<li class="list-group-item">';
            if($value['ip_filingdt']!=null && $value['ip_filingdt']!="0000-00-00"){
                $ip_patent_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="IP Filing Date">' . date('Y M d', strtotime($value['ip_filingdt'])) . '</span>';
            }
            $ip_patent_box .= '<a  href="' . admin_url('projects/view/'.$value['id'].'') . '" onclick="#">' . $value['name'] . '</a><br>';
            if($value['ip_subcategory']!=''){
            $ip_patent_box .= '<p class="alen-p "><strong>' . _l('ip_subcategory') . '</strong>: ' . $value['ip_subcategory'].' </p>';
            }
            if($value['current_application_no']!=''){
                $ip_patent_box .= '<p class="alen-p "><strong>' . _l('current_application_no') . '</strong>: ' . $value['current_application_no'].'  |';
            }
            if($value['ip_class']!=''){
            $ip_patent_box .= '<strong>' . _l('ip_class') . '</strong>: ' . $value['ip_class'].'  |';
            }
            if($value['ip_regno']!=''){
            $ip_patent_box .= '<strong>' . _l('ip_regno') . '</strong>: ' . $value['ip_regno'].'  |</p>';
            }
            
            $file_type=get_file_extension($value['ip_logo']);
            $upload_path = get_upload_path_by_type('project');
            $path=$upload_path . $value['id'] . '/' . $value['ip_logo'];
            $is_image = is_image($path);
            
            
            $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$file_type);
            $lightBoxUrl = site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$file_type);
            if($is_image){
                
                $ip_patent_box .= '<a class="badge" style="width: 62px;" href="' . $lightBoxUrl . '" data-lightbox="customer-profile" class="display-block mbot5">View</a>';
            }

            $downloadLink = site_url('download/downloadlogofile/'.$value['id'].'/'.$value['ip_logo']);
            $ip_patent_box .= '<a class="badge pull-right" href="' . $downloadLink . '">Download</a><br>';
            
            $ip_patent_box .= '</li>';
        }
    } else {
        $ip_patent_box .= '<li class="list-group-item center_li">';
        $ip_patent_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
        $ip_patent_box .= '</li>';
    }
    $ip_patent_box .= '</ul>';
    $ip_patent_box .= '</div>';
    $ip_patent_box .= '<div class="panel-footer panel-footer-height">';
    $ip_patent_box .= '<span class="" > ';
    // You can add additional content or links here if needed
    $ip_patent_box .= '</span>';
    $ip_patent_box .= '</div>';
    $ip_patent_box .= '</div>';
    $ip_patent_box .= '</div>';

    // Return the generated content
    return $ip_patent_box;
}

function ip_domain_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');  

    $_where = 'case_type="intellectual_property" and ip_category=5';
    //$project_status = get_project_status_by_id(3);
    if(!has_permission('projects','','view')){
        $_where = 'tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
    }

    $ip_domain = $CI->db->order_by('project_created', 'asc')->limit(5)->select('tblprojects.id,name,tblip_subcategory.subcategory_name as ip_subcategory,current_application_no,ip_class,ip_regno,ip_filingdt,case_type,file_no,ip_logo')->from('tblprojects')->where($_where)->join('tblip_subcategory', 'tblip_subcategory.category_id=tblprojects.ip_subcategory','left')->get()->result_array();
    $ip_domain_count = $CI->db->from('tblprojects')->where($_where)->join('tblip_subcategory', 'tblip_subcategory.category_id=tblprojects.ip_subcategory','left')->count_all_results();


    // Start building the HTML content
    $ip_domain_box = '<div class="col-md-4 ' . ((in_array(17, $active_boxes))&& (sizeof($ip_domain) > 0  || (is_admin())) ? '' : 'hide') . ' ">';
    $ip_domain_box .= '<div class="panel panel-default">';
    $ip_domain_box .= '<div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> ' . _l('latest_ip_domain') . '<a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $ip_domain_count . '</a> </div>';
    $ip_domain_box .= '<div class="panel-body alen-panel">';
    $ip_domain_box .= '<ul class="list-group">';
    if (sizeof($ip_domain) > 0) {
        foreach ($ip_domain as $key => $value) {
            $ip_domain_box .= '<li class="list-group-item">';
            if($value['ip_filingdt']!=null && $value['ip_filingdt']!="0000-00-00"){
                $ip_domain_box .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="IP Filing Date">' . date('Y M d', strtotime($value['ip_filingdt'])) . '</span>';
            }
            $ip_domain_box .= '<a  href="' . admin_url('projects/view/'.$value['id'].'') . '" onclick="#">' . $value['name'] . '</a><br>';
            if($value['ip_subcategory']!=''){
            $ip_domain_box .= '<p class="alen-p "><strong>' . _l('ip_subcategory') . '</strong>: ' . $value['ip_subcategory'].' </p>';
            }
            if($value['current_application_no']!=''){
                $ip_domain_box .= '<p class="alen-p "><strong>' . _l('current_application_no') . '</strong>: ' . $value['current_application_no'].'  |';
            }
            if($value['ip_class']!=''){
            $ip_domain_box .= '<strong>' . _l('ip_class') . '</strong>: ' . $value['ip_class'].'  |';
            }
            if($value['ip_regno']!=''){
            $ip_domain_box .= '<strong>' . _l('ip_regno') . '</strong>: ' . $value['ip_regno'].'  |</p>';
            }
            
            $file_type=get_file_extension($value['ip_logo']);
            $upload_path = get_upload_path_by_type('project');
            $path=$upload_path . $value['id'] . '/' . $value['ip_logo'];
            $is_image = is_image($path);
            
            
            $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$file_type);
            $lightBoxUrl = site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$file_type);
            if($is_image){
                
                $ip_domain_box .= '<a class="badge" style="width: 62px;" href="' . $lightBoxUrl . '" data-lightbox="customer-profile" class="display-block mbot5">View</a>';
            }

            $downloadLink = site_url('download/downloadlogofile/'.$value['id'].'/'.$value['ip_logo']);
            $ip_domain_box .= '<a class="badge pull-right" href="' . $downloadLink . '">Download</a><br>';
            
            $ip_domain_box .= '</li>';
        }
    } else {
        $ip_domain_box .= '<li class="list-group-item center_li">';
        $ip_domain_box .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o" aria-hidden="true"></i></p>';
        $ip_domain_box .= '</li>';
    }
    $ip_domain_box .= '</ul>';
    $ip_domain_box .= '</div>';
    $ip_domain_box .= '<div class="panel-footer panel-footer-height">';
    $ip_domain_box .= '<span class="" > ';
    // You can add additional content or links here if needed
    $ip_domain_box .= '</span>';
    $ip_domain_box .= '</div>';
    $ip_domain_box .= '</div>';
    $ip_domain_box .= '</div>';

    // Return the generated content
    return $ip_domain_box;
}

function litigation_cases_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');

    $where = 'case_type IN (SELECT id FROM  tblproject_types  where type="litigation")';
    if (!has_permission('projects', '', 'view')) {
        $where .= ' AND  id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
    }

    $lit_cases_list = $CI->db->order_by('id', 'desc')->limit(5)->select('id,name,opposite_party,clientid,start_date,case_type,current_application_no,current_case_number,project_created,(SELECT tblhearings.court_no FROM tblhearings  WHERE tblprojects.id = tblhearings.project_id GROUP BY project_id ORDER BY tblhearings.id DESC LIMIT 1) as case_number')->from('tblprojects')->where($where)->get()->result_array();
    $lit_cases_count = $CI->db->from('tblprojects')->where($where)->count_all_results();

    $html_output = '<div class="col-md-4 ' . ((in_array(18, $active_boxes))&& (sizeof($lit_cases_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
    $html_output .= '<div class="panel panel-default">';
    $html_output .= '<div class="panel-heading"><i class="fa fa-users fa-lg"></i> ' . _l('litigation_cases') . ' <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $lit_cases_count . '</a></div>';
    $html_output .= '<div class="panel-body alen-panel"><ul class="list-group alen-ul" style="margin-bottom: 10px;">';
    
    if (sizeof($lit_cases_list) > 0) {
        foreach ($lit_cases_list as $value) {
            
            // $html_output .= '<li class="list-group-item">';
            // $html_output .= '<a class="badge" style="background-color: #807B7A;padding: 6px;border-radius: 4px;font-weight: 544;" data-toggle="tooltip" data-placement="top" title="Case Created" href="#">' . date($value['project_created']) . '</a>';

            // $html_output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '" data-toggle="tooltip" data-placement="top" title="Case Name">' . $value['name'] . '</a> <br>';
            // $html_output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '" data-toggle="tooltip" data-placement="top" title="Case Type">' . _l($value['case_type']) . '</a><br>';
            // $html_output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '" data-toggle="tooltip" data-placement="top" title="Application Number">' . $value['application_number'] . '</a><br>';
            // $html_output .= '<p class="alen-p" data-toggle="tooltip" data-placement="top" title="Opposite Party" style="margin:0 0 3px;">' . get_opposite_party_name($value['opposite_party']) . ' <br>';
            
            // $html_output .= '</li>';

            $html_output .= '<li class="list-group-item">';
            $html_output .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Case Created">' . date('Y F d', strtotime($value['project_created'])) . '</span>';
            //$html_output .= '<button type="button" class="btn btn-default btn-sm btn-icon pop" data-container="body" data-toggle="popover" data-html="true"  data-placement="bottom" data-content="' . date('Y M d ', strtotime($value['judgement_date'])) . '<hr>' . $value['judgement_remark'] . '" data-original-title="' . date('Y M d ', strtotime($value['judgement_date'])) . '" title="' . _l('judgment') . '"> <i class="fa fa-tag"></i></button>';
            $html_output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '">' . $value['name'] . '</a>';
            $html_output .= '<p class="alen-p ">' . _l('casediary_casenumber') . ': <strong>' . $value['current_case_number'] . '</strong> | <strong>' . _l($value['case_type']) . '</strong> | <strong>' . _l($value['current_application_no']) . '</strong> </p>';
            $html_output .= '</li>';
        }
    } else {
        $html_output .= '<li class="list-group-item center_li">';
        $html_output .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
        $html_output .= '</li>';
        $html_output .= '<li class="list-group-item li_new_button">';
		 if (has_permission('projects', '', 'create')) {
        $html_output .= '<a href="#" onclick="init_projects(); return false;" class="btn btn-info btn-sm mb-4"><i class="fa fa-plus"></i> ' . _l('new_project') . '</a>';
		 }
        $html_output .= '</li>';
    }

$html_output .= '</ul>';
$html_output .= '</div>';
$html_output .= '<div class="panel-footer panel-footer-height">';
$html_output .= '<span class="" >';
$html_output .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('projects/all_cases/litigation') . '">' . _l('view_all_cases') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
$html_output .= '</span>';
$html_output .= '</div>';
$html_output .= '</div>';
$html_output .= '</div>';

return $html_output;

}
function non_litigation_cases_box(){
    $CI = &get_instance();
    $active_boxes = array_column($CI->db->select('id')->get_where('tbldashboard_boxes', ['box_status' => 1])->result_array(), 'id');

    $where = 'case_type IN (SELECT id FROM  tblproject_types  where type="nonlitigation")';
    if (!has_permission('projects', '', 'view')) {
        $where .= ' AND  id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
    }

    $non_lit_cases_list = $CI->db->order_by('id', 'desc')->limit(5)->select('id,name,opposite_party,clientid,start_date,case_type,current_case_number,current_application_no,project_created,(SELECT tblhearings.court_no FROM tblhearings  WHERE tblprojects.id = tblhearings.project_id GROUP BY project_id ORDER BY tblhearings.id DESC LIMIT 1) as case_number')->from('tblprojects')->where($where)->get()->result_array();
    $non_lit_cases_count = $CI->db->from('tblprojects')->where($where)->count_all_results();
    
    $html_output = '<div class="col-md-4 ' . ((in_array(19, $active_boxes))&& (sizeof($non_lit_cases_list) > 0  || (is_admin())) ? '' : 'hide') . '">';
    $html_output .= '<div class="panel panel-default">';
    $html_output .= '<div class="panel-heading"><i class="fa fa-users fa-lg"></i> ' . _l('non_litigation_cases') . ' <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> ' . $non_lit_cases_count . '</a></div>';
    $html_output .= '<div class="panel-body alen-panel"><ul class="list-group alen-ul" style="margin-bottom: 10px;">';
    
    if (sizeof($non_lit_cases_list) > 0) {
        foreach ($non_lit_cases_list as $value) {
            
            // $html_output .= '<li class="list-group-item">';
            // $html_output .= '<a class="badge" style="background-color: #807B7A;padding: 6px;border-radius: 4px;font-weight: 544;" data-toggle="tooltip" data-placement="top" title="Project Created" href="#">' . date($value['project_created']) . '</a>';

            // $html_output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '" data-toggle="tooltip" data-placement="top" title="Case Name">' . $value['name'] . '</a><br>';
            // $html_output .= '<a href="#" data-toggle="tooltip" data-placement="top" title="Case Type">' . _l($value['case_type']) . '</a><br>';
            // $html_output .= '<a href="#" data-toggle="tooltip" data-placement="top" title="Application Number">' . $value['application_number'] . '</a><br>';
            // $html_output .= '<p class="alen-p" data-toggle="tooltip" data-placement="top" title="Opposite Party" style="margin:0 0 3px;">' . get_opposite_party_name($value['opposite_party']) . ' <br>';
            
            // $html_output .= '</li>';

            $html_output .= '<li class="list-group-item">';
            $html_output .= '<span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Case Created">' . date('Y F d', strtotime($value['project_created'])) . '</span>';
            //$html_output .= '<button type="button" class="btn btn-default btn-sm btn-icon pop" data-container="body" data-toggle="popover" data-html="true"  data-placement="bottom" data-content="' . date('Y M d ', strtotime($value['judgement_date'])) . '<hr>' . $value['judgement_remark'] . '" data-original-title="' . date('Y M d ', strtotime($value['judgement_date'])) . '" title="' . _l('judgment') . '"> <i class="fa fa-tag"></i></button>';
            $html_output .= '<a href="' . admin_url('projects/view/' . $value['id']) . '">' . $value['name'] . '</a>';
            $html_output .= '<p class="alen-p ">' . _l('casediary_casenumber') . ': <strong>' . $value['current_case_number'] . '</strong> | <strong>' . _l($value['case_type']) . '</strong> | <strong>' . _l($value['current_application_no']) . '</strong> </p>';
            $html_output .= '</li>';
        }
    } else {
        $html_output .= '<li class="list-group-item center_li">';
        $html_output .= '<p>' . _l('no_data_found') . '<i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>';
        $html_output .= '</li>';
        $html_output .= '<li class="list-group-item li_new_button">';
		 if (has_permission('projects', '', 'create')) {
        $html_output .= '<a href="'. admin_url('projects/project?case_type=other_projects').'" class="btn btn-info btn-sm mb-4"><i class="fa fa-plus"></i> ' . _l('new_project') . '</a>';
		 }
        $html_output .= '</li>';
    }

    $html_output .= '</ul>';
$html_output .= '</div>';
$html_output .= '<div class="panel-footer panel-footer-height">';
$html_output .= '<span class="" >';
$html_output .= '<a class="btn btn-link btn-sm " style="" target="_blank"  href="' . admin_url('projects/all_cases/nonlitigation') . '">' . _l('view_all_cases') . '  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>';
$html_output .= '</span>';
$html_output .= '</div>';
$html_output .= '</div>';
$html_output .= '</div>';

return $html_output;

}
function get_matteripcategory($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblip_categories',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function get_matteripsubcategory($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblip_subcategory',array('id'=>$id))->row();
      if($row)
        return $row->subcategory_name;
      else
        return ''; 
    }
}
function get_ipsubcategories($cateid= '')
{
    $CI = &get_instance();
    if(is_numeric($cateid)){
        $CI->db->where('category_id',$cateid);
    }
    $cats =  $CI->db->select('tblip_subcategory.*')->get('tblip_subcategory')->result_array();
    return $cats; 
}
function get_contracttype($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblcontracts_types',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function get_court_types(){
    return array(
        array('id'=>'litigation','name'=>'Litigation'),
        array('id'=>'nonlitigation','name'=>'Non Litigation'),
      
    );
}
/**
 * Get predefined tabs array, used in customer profile
 * @param  mixed $customer_id customer id to prepare the urls
 * @return array
 */
function get_lawyers_profile_tabs($customer_id)
{
    $customer_tabs = array(
  array(
    'name'=>'profile',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=profile'),
    'icon'=>'fa fa-user-circle',
    'lang'=>_l('lawyer_add_edit_profile'),
    'visible'=>true,
    'order'=>1,
    ),
  array(
    'name'=>'notes',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('contracts_notes_tab'),
    'visible'=>true,
    'order'=>2,
    ),
  array(
    'name'=>'statement',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=statement'),
    'icon'=>'fa fa-area-chart',
    'lang'=>_l('customer_statement'),
    //'visible'=>(has_permission('invoices', '', 'view') && has_permission('payments', '', 'view')),
    'order'=>3,
    'visible'=>false,
    ),
  array(
    'name'=>'invoices',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=invoices'),
    'icon'=>'fa fa-file-text',
    'lang'=>_l('lawyer_invoices_tab'),
    //'visible'=>(has_permission('invoices', '', 'view') || has_permission('invoices', '', 'view_own')),
    'order'=>4,'visible'=>false,
    ),
  array(
    'name'=>'payments',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=payments'),
    'icon'=>'fa fa-line-chart',
    'lang'=>_l('lawyer_payments_tab'),
    //'visible'=>(has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own')),
    'order'=>5,
    'visible'=>false,
    ),
   array(
    'name'=>'receipts',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=receipts'),
    'icon'=>'fa fa-money',
    'lang'=>_l('lawyer_receipts_tab'),
    //'visible'=>(has_permission('payments', '', 'view') || has_permission('invoices', '', 'view_own')),
    'order'=>5,
    'visible'=>false,
    ),
  array(
    'name'=>'proposals',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=proposals'),
    'icon'=>'fa fa-file-powerpoint-o',
    'lang'=>_l('proposals'),
   // 'visible'=>(has_permission('proposals', '', 'view') || has_permission('proposals', '', 'view_own') || (get_option('allow_staff_view_proposals_assigned') == 1 && total_rows('tblproposals', array('assigned'=>get_staff_user_id())) > 0)),
    'order'=>6,
    'visible'=>false,
    ),
    array(
    'name'=>'credit_notes',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=credit_notes'),
    'icon'=>'fa fa-sticky-note-o',
    'lang'=>_l('credit_notes'),
   // 'visible'=>(has_permission('credit_notes', '', 'view') || has_permission('credit_notes', '', 'view_own')),
    'order'=>7,
    'visible'=>false,
    ),
  array(
    'name'=>'estimates',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=estimates'),
    'icon'=>'fa fa-clipboard',
    'lang'=>_l('estimates'),
   // 'visible'=>(has_permission('estimates', '', 'view') || has_permission('estimates', '', 'view_own')),
    'order'=>8,
    'visible'=>false,
    ),
  array(
    'name'=>'expenses',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=expenses'),
    'icon'=>'fa fa-file-text-o',
    'lang'=>_l('expenses'),
    'visible'=>(has_permission('expenses', '', 'view') || has_permission('expenses', '', 'view_own')),
    'order'=>9,
    
    ),
  /*array(
    'name'=>'contracts',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=contracts'),
    'icon'=>'fa fa-floppy-o',
    'lang'=>_l('contracts'),
    'visible'=>(has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own')),
    'order'=>10,
    ),*/
   array(
    'name'=>'documents',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=documents'),
    'icon'=>'fa fa-file',
    'lang'=>_l('documents'),
    //'visible'=>(has_permission('documents', '', 'view') || has_permission('documents', '', 'view_own')),
    'order'=>11,
    'visible'=>false,

    ),

  

    
   
    array(
    'name'=>'tasks',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=tasks'),
    'icon'=>'fa fa-tasks',
    'lang'=>_l('tasks'),
    'visible'=>false,
    'order'=>14,
    ),
     
  /*array(
    'name'=>'tickets',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=tickets'),
    'icon'=>'fa fa-ticket',
    'lang'=>_l('tickets'),
    'visible'=>((get_option('access_tickets_to_none_staff_members') == 1 && !is_staff_member()) || is_staff_member()),
    'order'=>15,
    ),*/
  array(
    'name'=>'attachments',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=attachments'),
    'icon'=>'fa fa-paperclip',
    'lang'=>_l('customer_attachments'),
    'visible'=>true,
    'order'=>16,
    ),
  /*array(
    'name'=>'vault',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=vault'),
    'icon'=>'fa fa-lock',
    'lang'=>_l('vault'),
    'visible'=>true,
    'order'=>17,
    ),*/
  array(
    'name'=>'reminders',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=reminders'),
    'icon'=>'fa fa-clock-o',
    'lang'=>_l('lawyer_reminders_tab'),
    'visible'=>false,
    'order'=>18,
    'id'=>'reminders',

    ),
  array(
    'name'=>'map',
    'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=map'),
    'icon'=>'fa fa-map-marker',
    'lang'=>_l('customer_map'),
    'visible'=>false,
    'order'=>19,
    ),

  );
  
  
  
   // Include projects tab only if 'enable_legaldashboard' is enabled
    if (get_option('enable_legaldashboard')) {
        $customer_tabs[] =   array(
                            'name'=>'projects',
                            'url'=>admin_url('lawyers/lawyer/'.$customer_id.'?group=projects'),
                            'icon'=>'fa fa-bars',
                            'lang'=>_l('projects'),
                            'visible'=>true,
                            'order'=>13,
                            );
    }

    $hook_data = hooks()->apply_filters('customer_profile_tabs', array('tabs'=>$customer_tabs, 'customer_id'=>$customer_id));
    $customer_tabs = $hook_data['tabs'];

    usort($customer_tabs, function ($a, $b) {
        return $a['order'] - $b['order'];
    });

    return $customer_tabs;
}
function get_all_lawyers_attachments($id){
    $CI = &get_instance();
    $CI->db->where('rel_id', $id);
    $CI->db->where('rel_type', 'lawyer');
    $client_main_attachments = $CI->db->get('tblfiles')->result_array();

    $attachments['lawyer'] = $client_main_attachments;

    return $attachments;
}
/**
 * Return contact profile image url
 * @param  mixed $contact_id
 * @param  string $type
 * @return string
 */
function lawyer_profile_image_url($contact_id, $type = 'small')
{
    
    $url = base_url('assets/images/user-placeholder.jpg');
    $CI =& get_instance();
    $CI->db->select('profile_image');
    $CI->db->from('tbllawyers');
    $CI->db->where('lawyerid', $contact_id);
    $contact = $CI->db->get()->row();
    
    if ($contact) {
        if (!empty($contact->profile_image)) {
            $path = 'uploads/lawyer_profile_images/' . $contact_id . '/' . $type . '_' . $contact->profile_image;
            if (file_exists($path)) {
                $url = base_url($path);
            }
        }
    }

    return $url;
}

function get_noticetype_name_by_id($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblnotices_types',array('id'=>$id))->row();
      if($row)
        return $row->name;
      else
        return ''; 
    }
}
function get_nextapprover_bykey($rel_id,$rel_type,$approve_key){
	$approvals='';
	$CI = &get_instance();
	$CI->db->limit(1);
    $CI->db->order_by('id','ASC'); 
	$CI->db->where('rel_id', $rel_id);
    $CI->db->where('rel_type', $rel_type);
	 $CI->db->where('approval_key',$approve_key);
	  $CI->db->where('approval_status',2);
	// $CI->db->where('last_approving IS NOT NULL');
   /* if($status !=''){
       $CI->db->where('approval_status', $status);
    }*/
	 $approvals_qry = $CI->db->get('tblapprovals');
    if($approvals_qry->num_rows() > 0){
        $approvals = $approvals_qry->row()->last_approving;
       
    }
    return $approvals;
   
}
function generate_task_ticket_reassign($ticketid,$staff){

    // Copy Scopes
    $CI =& get_instance();
    
    // Put Tasks in the ticket
$tasks=  [ '1' => 'Legal Request Reassigned'];


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
        	   if ($task_id) {
              $CI->db->where('taskid', $task_id);
                   $data2['staffid']=$staff; 
                     $CI->db->update(db_prefix() . 'task_assigned', $data2);
                   
    }

        }
   
}


function get_department_name_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('departmentid', $id);
    $department_qry = $CI->db->get('tbldepartments');
    if($department_qry->num_rows() > 0){
        $departments = $department_qry->row()->name;
        
        return $departments;
    }
    return '';  
}
function get_reassign_satatus_by_id($id)
{
    $CI = &get_instance();
    $CI->db->where('ticketstatusid', $id);
    $department_qry = $CI->db->get('tickets_reassign_status');
    if($department_qry->num_rows() > 0){
        $departments = $department_qry->row()->name;
        
        return $departments;
    }
    return '';  
}
function  get_amend_statuses(){
    return array(
                 array('id'=>'Draft','name'=>'Draft'),
                array('id'=>'Pending Approval','name'=>'Pending Approval'),
                array('id'=>'Active','name'=>'Active'),
          );
  }
  function get_amendment_latest_by_id($amendid,$contractid)
{
    $CI = &get_instance();
    $CI->db->where('amendment_number', $amendid);
    $CI->db->where('contract_id', $contractid);
    $department_qry = $CI->db->get('contract_amendments');
    if($department_qry->num_rows() > 0){
        $departments = $department_qry->row()->amendment_text;
        
        return $departments;
    }
    return '';  
}
function  get_postaction_statuses(){
    return array(
                 array('id'=>'Pending','name'=>'Pending'),
                array('id'=>'In Progress','name'=>'In Progress'),
                array('id'=>'Completed','name'=>'Completed'),
                array('id'=>'Escalated','name'=>'Escalated'),
          );
  }
function get_contract_actions($id='')
{
    $CI =& get_instance();
  
    //$CI->db->where('case_type', $casetype);
    $project = $CI->db->get('tblaction_categories')->result_array();
    if ($project) {
        return $project;
    }

    return [];
}
function get_postaction_latest_by_id($categoryid,$contractid)
{
	$departments='';
    $CI = &get_instance();
    $CI->db->where('category_id', $categoryid);
    $CI->db->where('contract_id', $contractid);
    $department_qry = $CI->db->get('tblcontract_actions');
    if($department_qry->num_rows() > 0){
        $departments = $department_qry->row();
        
       
    }
    return $departments;
}
function get_contractaction_name_by_id($id){
    $CI = &get_instance();
    if($id){
      $row =    $CI->db->get_where('tblaction_categories',array('id'=>$id))->row();
      if($row)
        return $row->category_name;
      else
        return ''; 
    }
}

function get_approval_duedate($contractid,$service)
{
    $duedate='';
    $CI = &get_instance();
    if(is_numeric($contractid)){
        $query  = $CI->db->query('SELECT id,approvaldue_date FROM `tblapprovals` 
                                  WHERE  tblapprovals.rel_id = ? AND   tblapprovals.rel_type = ? ORDER BY id DESC LIMIT 1',array($contractid,$service));
        if($query->num_rows() > 0){
            $duedate=$query->row()->approvaldue_date;
            return $duedate;
        }else{
            return  $duedate;
        }
        
    }
      return  $duedate;
}
function get_client_refcontractno($clientid){
$CI = &get_instance();
    if(is_numeric($clientid)){
        $query  = $CI->db->query('SELECT * FROM `tblclients` 
                                  WHERE  tblclients.userid = ? ',array($clientid));
        if($query->num_rows() > 0){
            $result=$query->row();
             $next_ref_number =$result->contract_count+1;
                        $prefix = $result->client_no;
                       $_file_number = str_pad($next_ref_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); 
                        
                            $value=$prefix.$_file_number;
            return $value;
        }else{
            return '';
        }   
    }
}
function get_approval_reference($contractid,$service)
{
    $refno='';
    $CI = &get_instance();
    if(is_numeric($contractid)){
        $query  = $CI->db->query('SELECT id,approval_name FROM `tblapprovals` 
                                  WHERE  tblapprovals.rel_id = ? AND   tblapprovals.rel_type = ? ORDER BY id DESC LIMIT 1',array($contractid,$service));
        if($query->num_rows() > 0){
            $refno=$query->row()->approval_name;
            return $refno;
        }else{
            return  $refno;
        }
        
    }
      return  $duedate;
}
function is_stamper($staff_id = '')
{
    $CI = & get_instance();
    if ($staff_id == '') {
        $staff_id = get_staff_user_id();
    }

    $CI->db->where('staffid', $staff_id)
               
    ->where('is_stamper', '1');

    return $CI->db->count_all_results(db_prefix() . 'staff') > 0 ? true : false;
}
function  get_approval_access(){
    return array(
                 array('id'=>'signed_by','name'=>'Signed By'),
                array('id'=>'read_by','name'=>'Read By'),
                array('id'=>'delegate_to','name'=>'Delegate To'),
          );
  }
  function  get_threshold_limits($type='contract'){
    if($type=='contract'){
    return array(
                 array('id'=>'1','name'=>'<500K'),
                array('id'=>'2','name'=>'>500'),
                              
          );
}else{
     return array(
                 array('id'=>'1','name'=>'<500K'),
                array('id'=>'2','name'=>'>500'),
                array('id'=>'3','name'=>'>1M'),
               
          );
  }
}
