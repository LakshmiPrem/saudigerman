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

/**
 * Check the contract view restrictions
 *
 * @param  int $id
 * @param  string $hash
 *
 * @return void
 */
function check_chequebounces_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('chequebounces_model');

    if (!$hash || !$id) {
        show_404();
    }

    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_contract_only_logged_in') == 1) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }
    }

    $contract = $CI->chequebounces_model->get($id);

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

/**
 * Send contract signed notification to staff members
 *
 * @param  int $contract_id
 *
 * @return void
 */
function send_chequebounces_signed_notification_to_staff($contract_id)
{
    $CI = &get_instance();
    $CI->db->where('id', $contract_id);
    $contract = $CI->db->get(db_prefix() . 'chequebounces')->row();

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
            'link'            => 'chequebounces/chequebounce/' . $contract->id,
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
function count_recently_created_chequebounces($days = 7, $staffId = null)
{
    $diff1     = date('Y-m-d', strtotime('-' . $days . ' days'));
    $diff2     = date('Y-m-d', strtotime('+' . $days . ' days'));
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;
    $where_own = [];

    if (!staff_can('view', 'chequebounces')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'chequebounces_return', 'datecreated BETWEEN "' . $diff1 . '" AND "' . $diff2 . '" ' . (count($where_own) > 0 ? ' AND addedby=' . $staffId : ''));
}

/**
 * Get total number of active contracts
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_status_chequebounces($status)
{
    $where_own = [];
    $staffId   = get_staff_user_id();

    if (!has_permission('chequebounces', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }
return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0 AND  status="'.$status.'"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0 AND status="'.$status.'"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
//    return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
}
function count_open_chequebounces($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('chequebounces', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }
return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0 AND  status="1"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0 AND status="1"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
//    return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
}
function count_active_chequebounces($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('chequebounces', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }
return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0 AND  status="2"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0 AND status="2"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
//    return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
}
function count_hold_chequebounces($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('chequebounces', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }
return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0 AND  status="2"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0 AND status="2"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
//    return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
}

/**
 * Get total number of expired contracts
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_expired_chequebounces($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('chequebounces', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }
return total_rows(db_prefix() . 'chequebounces', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0 AND  status="4"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0 AND status="4"' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
  //  return total_rows(db_prefix() . 'chequebounces', array_merge(['DATE(dateend) <' => date('Y-m-d'), 'trash' => 0], $where_own));
}

/**
 * Get total number of trash contracts
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_trash_chequebounces($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('chequebounces', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'chequebounces', array_merge(['trash' => 1], $where_own));
}
function get_chequeamount_byyear($bid,$year)
{
	$result=' ';
    $CI = &get_instance();
    $CI->db->select('sum(cheque_amount)as chequeyear');
        $CI->db->from('tblchequebounces_return');
	 $CI->db->where('bounce_id',$bid);
	 $CI->db->where('YEAR(retcheque_date)', $year);
       $qry=$CI->db->get();
	if($qry->num_rows()>0)
		$result=$qry->row()->chequeyear;
	return $result;
}
function get_chequebounce_latest_update($bounce_id){
    $CI = &get_instance();
    $CI->db->where('bounce_id', $bounce_id);
   // $CI->db->where('rel_type', 'project');
    $CI->db->limit(1);
    $CI->db->order_by('id','DESC');   
    $case_details_qry = $CI->db->get('tblchequebounces_return');

    if($case_details_qry->num_rows() > 0){
        $row = $case_details_qry->row();
       
        $update = _d($row->datecreated).' -  '.nl2br($row->remarks);
        return $update;
    }

    return ' No updates';
}
function chequebounce_status_translate($id)
{
    if ($id == '' || is_null($id)) {
        return '';
    }

    $line = _l('cheque_status_db_' . $id, '', false);

    if ($line == 'db_translate_not_found') {
        $CI = & get_instance();
        $CI->db->where('chequestatusid', $id);
        $status = $CI->db->get(db_prefix() . 'chequebounces_status')->row();

        return !$status ? '' : $status->name;
    }

    return $line;
}
function have_assigned_chequebounces($staff_id = '')
{
    $CI       = &get_instance();
    $staff_id = is_numeric($staff_id) ? $staff_id : get_staff_user_id();
    $cache    = $CI->app_object_cache->get('staff-total-assigned-chequebounces-' . $staff_id);

    if (is_numeric($cache)) {
        $result = $cache;
    } else {
        $result = total_rows(db_prefix() . 'chequebounces_assigned', [
            'staff_id' => $staff_id,
        ]);
        $CI->app_object_cache->add('staff-total-assigned-chequebounces-' . $staff_id, $result);
    }

    return $result > 0 ? true : false;
}


