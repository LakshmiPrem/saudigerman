<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get contract short_url
 * @since  Version 2.7.3
 * @param  object $contract
 * @return string Url
 */
function get_contract_shortlink($contract)
{
    $long_url = site_url("contract/{$contract->id}/{$contract->hash}");
    if (!get_option('bitly_access_token')) {
        return $long_url;
    }

    // Check if contract has short link, if yes return short link
    if (!empty($contract->short_link)) {
        return $contract->short_link;
    }

    // Create short link and return the newly created short link
    $short_link = app_generate_short_link([
        'long_url'  => $long_url,
        'title'     => 'Contract #'. $contract->id
    ]);

    if ($short_link) {
        $CI = &get_instance();
        $CI->db->where('id', $contract->id);
        $CI->db->update(db_prefix() . 'contracts', [
            'short_link' => $short_link
        ]);
        return $short_link;
    }
    return $long_url;
}

/**
 * Check the contract view restrictions
 *
 * @param  int $id
 * @param  string $hash
 *
 * @return void
 */
function check_contract_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('contracts_model');

    if (!$hash || !$id) {
        show_404();
    }

    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_contract_only_logged_in') == 1) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }
    }

    $contract = $CI->contracts_model->get($id);

    if (!$contract || ($contract->hash != $hash)) {
        show_404();
    }

    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_contract_only_logged_in') == 1) {
            if ($contract->client != get_client_user_id()) {
                show_404();
            }
        }
    }
}

/**
 * Function that will search possible contracts templates in applicaion/views/admin/contracts/templates
 * Will return any found files and user will be able to add new template
 *
 * @return array
 */
function get_contract_templates()
{
    $contract_templates = [];
    if (is_dir(VIEWPATH . 'admin/contracts/templates')) {
        foreach (list_files(VIEWPATH . 'admin/contracts/templates') as $template) {
            $contract_templates[] = $template;
        }
    }

    return $contract_templates;
}

/**
 * Send contract signed notification to staff members
 *
 * @param  int $contract_id
 *
 * @return void
 */
function send_contract_signed_notification_to_staff($contract_id)
{
    $CI = &get_instance();
    $CI->db->where('id', $contract_id);
    $contract = $CI->db->get(db_prefix() . 'contracts')->row();

    if (!$contract) {
        return false;
    }

    // Get creator
    $CI->db->select('staffid, email');
    $CI->db->where('staffid', $contract->addedfrom);
    $staff_contract = $CI->db->get(db_prefix() . 'staff')->result_array();

    $notifiedUsers = [];

    foreach ($staff_contract as $member) {
        $notified = add_notification([
            'description'     => 'not_contract_signed',
            'touserid'        => $member['staffid'],
            'fromcompany'     => 1,
            'fromuserid'      => 0,
            'link'            => 'contracts/contract/' . $contract->id,
            'additional_data' => serialize([
                '<b>' . $contract->subject . '</b>',
            ]),
        ]);

        if ($notified) {
            array_push($notifiedUsers, $member['staffid']);
        }

        send_mail_template('contract_signed_to_staff', $contract, $member);
    }

    pusher_trigger_notification($notifiedUsers);
}

/**
 * Get the recently created contracts in the given days
 *
 * @param  integer $days
 * @param  integer|null $staffId
 *
 * @return integer
 */
function count_recently_created_contracts($days = 7, $staffId = null)
{
    $diff1     = date('Y-m-d', strtotime('-' . $days . ' days'));
    $diff2     = date('Y-m-d', strtotime('+' . $days . ' days'));
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;
    $where_own = [];

    if (!staff_can('view', 'contracts')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'contracts', 'dateadded BETWEEN "' . $diff1 . '" AND "' . $diff2 . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : ''));
}

/**
 * Get total number of active contracts
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_active_contractsold($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
       // $where_own = ['addedfrom' => $staffId];
		 return total_rows(db_prefix() . 'contracts', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0' . ' AND ' . db_prefix() . 'contracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . $staffId . ')) OR (DATE(dateend) IS NULL AND trash=0' . ' AND ' . db_prefix() . 'contracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . $staffId . '))');
    }
else
    return total_rows(db_prefix() . 'contracts', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0) OR (DATE(dateend) IS NULL AND trash=0)');
}

function count_active_contracts($type='contracts', $staffId = null)
{
    $staffId = is_null($staffId) ? get_staff_user_id() : $staffId;

    // Build type filter
    $type_where = "";
    if (!empty($type)) {
        $type_where = " AND type = '" . $type . "'";
    }

    // Active condition
    $active_where = '( (DATE(dateend) > "' . date('Y-m-d') . '" AND trash = 0) 
                      OR (DATE(dateend) IS NULL AND trash = 0) )';

    // If user does NOT have full permissions → filter using assigned contracts
    if (!has_permission('contracts', '', 'view')) {

        $assigned_where = " AND " . db_prefix() . "contracts.id IN 
                            (SELECT contractid FROM " . db_prefix() . "contracts_assigned 
                             WHERE staff_id = " . $staffId . ")";

        return total_rows(
            db_prefix() . 'contracts',
            $active_where . $assigned_where . $type_where
        );
    }

    // User has full view permission → return all matching type
    return total_rows(
        db_prefix() . 'contracts',
        $active_where . $type_where
    );
}

/**
 * Get total number of expired contracts
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
// function count_expired_contracts($staffId = null)
// {
//     $where_own = [];
//     $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

//     if (!has_permission('contracts', '', 'view')) {
//         $where_own = ['addedfrom' => $staffId];
		
//     }

//     return total_rows(db_prefix() . 'contracts', array_merge(['DATE(dateend) <' => date('Y-m-d'), 'trash' => 0], $where_own));
// }
function count_expired_contracts($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
		
    }

    return total_rows(db_prefix() . 'contracts', array_merge(['status' => 3, 'trash' => 0], $where_own));
}
/**
 * Get total number of trash contracts
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_trash_contracts($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'contracts', array_merge(['trash' => 1], $where_own));
}
function count_receivable_contracts($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'contracts', array_merge(['is_receivable' => 1], $where_own));
}
function count_payable_contracts($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'contracts', array_merge(['is_payable' => 1], $where_own));
}
function get_contract_name_by_id($id)
{
    $CI      = & get_instance();
    $contract = $CI->app_object_cache->get('contract-name-data-' . $id);

    if (!$contract) {
        $CI->db->select('subject');
        $CI->db->where('id', $id);
        $contract = $CI->db->get(db_prefix() . 'contracts')->row();
        $CI->app_object_cache->add('contract-name-data-' . $id, $contract);
    }

    if ($contract) {
        return $contract->subject;
    }

    return '';
}


function get_contract_value($type="")
{
    $CI = &get_instance();

$CI->db->select_sum('contract_value');
if($type=='trash'){
$CI->db->where('trash', 1);
}elseif($type=='is_payable'){
    $CI->db->where('is_payable', 1); 
    }  
 elseif($type=='is_receivable'){
   $CI->db->where('is_receivable', 1); 
}

$total_contract_value = $CI->db->get(db_prefix() . 'contracts')->row()->contract_value;

       
  

if($total_contract_value==''){
    $total_contract_value=0;
}
        return $total_contract_value ;

  
}
function get_contract_approval_count()
{


    return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => get_staff_user_id(), 'approval_status !=' => 3,'rel_type'=>'contract']
));
 
}
function get_contract_approval_count_waiting_others()
{ $count=0;
  $CI = &get_instance();
    $CI->db->where('staffid', get_staff_user_id());
    $CI->db->where('rel_type','contract');
    
    $approvals = $CI->db->get(db_prefix() . 'approvals')->result_array();
    foreach($approvals as $approval){
         $CI->db->select('approval_status');
        $CI->db->where('rel_id', $approval['rel_id']);
        $CI->db->where('rel_type', 'contract');
         $CI->db->where('staffid!=',get_staff_user_id());
        $contract = $CI->db->get(db_prefix() . 'approvals')->row();
        if(isset($contract->approval_status)){
        if($contract->approval_status!=3){
            $count++;
        }}
    }
    // return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => get_staff_user_id(), 'approval_status !=' => 3]
// ));
return $count;
 
}
function get_contract_approval_count_completed()
{


    return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => get_staff_user_id(), 'approval_status' => 3,'rel_type'=>'contract']
));
 
}


 function get_contract_distinct_staff()
    {
        
 $CI = &get_instance();
        return $CI->db->query('SELECT DISTINCT(staffid) FROM ' . db_prefix() . 'approvals')->result_array();

    }

    function get_contract_approval_count_with_id($staff)
{

    return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => $staff,'rel_type'=>'contract']
));
 
}
    function get_contract_approval_count_with_id_pending($staff)
{

    return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => $staff, 'approval_status' => 2,'rel_type'=>'contract']
));
 
}
function get_contract_approval_count_completed_with_id($staff)
{

    return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => $staff, 'approval_status' => 2,'rel_type'=>'contract']
));
 
}
function get_contract_approval_count_approching_deadline()
{ $count=0;
  $CI = &get_instance();
    $CI->db->where('staffid', get_staff_user_id());
    $CI->db->where('rel_type','contract');
    
    $approvals = $CI->db->get(db_prefix() . 'approvals')->result_array();
    foreach($approvals as $approval){
         $CI->db->select('approval_status');
       $CI->db->where('rel_id', $approval['rel_id']);
$CI->db->where('rel_type', 'contract');
$CI->db->where('DATEDIFF(approvaldue_date, CURDATE()) >= 0', null, false);
$end_date=get_option('approval_expiration_before');
$CI->db->where('DATEDIFF(approvaldue_date, CURDATE()) <='.$end_date, null, false);


        $contract = $CI->db->get(db_prefix() . 'approvals')->row();
        if(isset($contract->approval_status)){
        if($contract->approval_status!=2){
            $count++;
        }}
    }
    // return total_rows(db_prefix() . 'approvals', array_merge(['staffId' => get_staff_user_id(), 'approval_status !=' => 3]
// ));
return $count;
 
}


function count_ongoing_contracts($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
		
    }

    return total_rows(db_prefix() . 'contracts', array_merge(['status' => 1, 'trash' => 0], $where_own));
}
function count_expired_in_three_months_contracts($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('contracts', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    $today = date('Y-m-d');
    $threeMonthsLater = date('Y-m-d', strtotime('+3 months'));

    return total_rows(
        db_prefix() . 'contracts',
        array_merge([
            'DATE(dateend) >=' => $today,
            'DATE(dateend) <=' => $threeMonthsLater,
            'trash'            => 0
        ], 
        $where_own)
    );
}
