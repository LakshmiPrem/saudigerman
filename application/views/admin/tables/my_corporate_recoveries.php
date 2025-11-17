<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('corporate_recovery', '', 'delete');

$custom_fields = get_table_custom_fields('corporate_recovery');

$aColumns = array(
    '1',
    'file_no',
    'debtor_title',
    'tblcorporate_recoveries.id as userid',
    'email_id',
    'tblcorporate_recoveries.mobile_no as phonenumber',
    'tblcorporate_recoveries.active',
    'client_id',
    'datecreated',
    'city'
    );

$sIndexColumn = "id";
$sTable       = 'tblcorporate_recoveries';
$where   = array();
// Add blank where all filter can be stored
$filter  = array();

$join = array(  );
/*foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_' . $key . ' ON tblcorporate_recoveries.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}*/

// Filter by proposals
$customAdminIds = array();
foreach ($this->ci->corporate_recoveries_model->get_customers_admin_unique_ids() as $cadmin) { 
    if ($this->ci->input->post('responsible_admin_' . $cadmin['staff_id'])) {

        array_push($customAdminIds, $cadmin['staff_id']);
    }
}

if (count($customAdminIds) > 0) {
    array_push($filter, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id IN (' . implode(', ', $customAdminIds) . '))');
}


if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if (!has_permission('corporate_recovery', '', 'view')) {
    array_push($where, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id=' . get_staff_user_id() . ')');
}
/*if (!has_permission('corporate_recovery', '', 'view')) {
    array_push($where, 'AND tblcorporate_recoveries.addedfrom='.get_staff_user_id());
}*/
if ($clientid != '') {
    array_push($where, 'AND client_id='.$clientid);
}

if($this->ci->input->post('my_customers')){
    array_push($where,'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id='.get_staff_user_id().')');
}
if($this->ci->input->post('exclude_inactive')){
    array_push($where,'AND tblcorporate_recoveries.active=1');
}


if ($this->ci->input->post('client_position')) {
    if($this->ci->input->post('client_position') != '')
    array_push($where, "AND tblcorporate_recoveries.client_id='".$this->ci->input->post('client_position')."'");
}
if($this->ci->input->post('contact_code')){
    array_push($where,'AND tblcorporate_recoveries.current_status="'.$this->ci->input->post('contact_code').'"');
}


/*if($this->ci->input->post('assigned_date')){

  $assigned_date = $this->ci->input->post('assigned_date');
  if ($assigned_date) {
      if ($assigned_date == 'today') {
          $beginOfDay = strtotime("midnight");
          $today   = date('Y-m-d');
          array_push($where, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id=' . get_staff_user_id() . ' AND DATE(date_assigned) = "'.$today. '" )');
      } elseif ($assigned_date == 'this_month') {
          $beginThisMonth = date('Y-m-01');
          $endThisMonth   = date('Y-m-t');
          array_push($where, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id=' . get_staff_user_id() . ' AND DATE(date_assigned) BETWEEN "' . $beginThisMonth . '" AND  "' .$endThisMonth.'" )');
      } elseif ($assigned_date == 'last_month') {
          $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
          $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
          array_push($where, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id=' . get_staff_user_id() . ' AND DATE(date_assigned)  BETWEEN "' . $beginLastMonth . '" AND  "' . $endLastMonth.'" )');
      } elseif ($assigned_date == 'this_week') {
          $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
          $endThisWeek   = date('Y-m-d', strtotime('sunday this week'));
          array_push($where, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id=' . get_staff_user_id() . ' AND DATE(date_assigned) BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'" )');
      } elseif ($assigned_date == 'last_week') {
          $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
          $endLastWeek   = date('Y-m-d', strtotime('sunday last week'));
          array_push($where, 'AND tblcorporate_recoveries.id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id=' . get_staff_user_id() . ' AND DATE(date_assigned) BETWEEN "' . $beginLastWeek . '" AND "' .$endLastWeek.'" )');
          
      }
  }

}*/

if($this->ci->input->post('range')){

  $range = $this->ci->input->post('range');
  if ($range) {
      if ($range == 'today') {
          $beginOfDay = strtotime("midnight");
          $today   = date('Y-m-d');
          array_push($where, ' AND DATE(tblcorporate_recoveries.datecreated) = "'.$today. '"');
      } elseif ($range == 'this_month') {
          $beginThisMonth = date('Y-m-01');
          $endThisMonth   = date('Y-m-t');
          array_push($where, ' AND DATE(tblcorporate_recoveries.datecreated) BETWEEN "' . $beginThisMonth . '" AND  "' .$endThisMonth.'"');
      } elseif ($range == 'last_month') {
          $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
          $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
          array_push($where, ' AND DATE(tblcorporate_recoveries.datecreated)  BETWEEN "' . $beginLastMonth . '" AND  "' . $endLastMonth.'"');
      } elseif ($range == 'this_week') {
          $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
          $endThisWeek   = date('Y-m-d', strtotime('sunday this week'));
          array_push($where, ' AND DATE(tblcorporate_recoveries.datecreated) BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
      } elseif ($range == 'last_week') {
          $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
          $endLastWeek   = date('Y-m-d', strtotime('sunday last week'));
          array_push($where, ' AND DATE(tblcorporate_recoveries.datecreated) BETWEEN "' . $beginLastWeek . '" AND "' .$endLastWeek.'"');
          
      }
  }

}
//$aColumns = do_action('customers_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array());

$output  = $result['output'];
$rResult = $result['rResult'];
//print_r($rResult);
foreach ($rResult as $aRow) {

    $row = array();

    // Bulk actions
    $row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['userid'] . '"><label></label></div>';
    // User id
   // $row[] = '<a href="' . admin_url('corporate_recoveries/corporate_recovery/' . $aRow['userid']) . '">' .$aRow['userid']. '</a>';
    $row[] = '<a href="' . admin_url('corporate_recoveries/corporate_recovery/' . $aRow['userid']) . '">' .$aRow['file_no']. '</a>';

    // Company
    $company = $aRow['debtor_title'];

    $row[] = '<a href="' . admin_url('corporate_recoveries/corporate_recovery/' . $aRow['userid']) . '">' . $company . '</a>';
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['client_id']) . '">' . get_company_name($aRow['client_id']). '</a>';
     $row[] = $aRow['city'];
    // Primary  email
    $row[] = ($aRow['email_id'] ? '<a href="mailto:' . $aRow['email_id'] . '">' . $aRow['email_id'] . '</a>' : '');
    // Primary contact phone
    $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : ''); 
    $row[] = _dt($aRow['datecreated']);

   $row[] = get_latest_update($aRow['userid'],'corporate');

   // $row[] = icon_btn('https://wa.me/' . $aRow['phonenumber'], 'whatsapp', 'btn-success _delete',array('target'=>'_blank'));

    // Toggle active/inactive customer
    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
        <input type="checkbox" data-switch-url="' . admin_url().'corporate_recoveries/change_defaulter_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['userid'] . '" data-id="' . $aRow['userid'] . '" ' . ($aRow['tblcorporate_recoveries.active'] == 1 ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['userid'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['tblcorporate_recoveries.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $toggleActive;


    /*$hook = do_action('customers_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];*/

    // Table options
    $options = icon_btn('corporate_recoveries/corporate_recovery/' . $aRow['userid'], 'pencil-square-o');

    // Show button delete if permission for delete exists
    if ($hasPermissionDelete) {
        $options .= icon_btn('corporate_recoveries/delete/' . $aRow['userid'], 'remove', 'btn-danger _delete');

    }

    

    $row[] = $options;
    $output['aaData'][] = $row;
}
