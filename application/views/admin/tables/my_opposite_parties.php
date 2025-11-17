<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('projects', '', 'delete');

 
$aColumns = array(
    'tbloppositeparty.id as id',
	'profile_image',
	 'tradelicence',
    'name',
    'trade_expiry',
  //  get_sql_select_client_company(),
    'email',
    'mobile',
    'tbloppositeparty.city as city',
	'tbloppositeparty.active',
    );

$sIndexColumn = "id";
$sTable       = 'tbloppositeparty';
$where   = array('  ');
// Add blank where all filter can be stored
$filter  = array(' ');

$join = array('LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'oppositeparty.client_id');

if ($clientid != '') {
    array_push($where, 'AND client_id=' . $this->ci->db->escape_str($clientid));
}
$party_type=$this->ci->input->post('party_type');
if ($party_type != '') {
    array_push($where, 'AND party_type='.$party_type);
}
$active=$this->ci->input->post('active');

if ($active != '') {
    if($active==2){
    array_push($where, 'AND tbloppositeparty.active=0');
}else{
    array_push($where, 'AND tbloppositeparty.active='.$active);
}
}

if($this->ci->input->post('dateadded')){

  $dateadded = $this->ci->input->post('dateadded');
  if ($dateadded) {
      if ($dateadded == 'today') {
          $beginOfDay = strtotime("midnight");
          $today   = date('Y-m-d');
          array_push($where, 'AND tbloppositeparty.dateadded="'.$today. '"');
      } elseif ($dateadded == 'this_month') {
          $beginThisMonth = date('Y-m-01');
          $endThisMonth   = date('Y-m-t');
          array_push($where, '   AND DATE(tbloppositeparty.dateadded) BETWEEN "' . $beginThisMonth . '" AND  "' .$endThisMonth.'" ');
      } elseif ($dateadded == 'last_month') {
          $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
          $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
          array_push($where, ' AND DATE(tbloppositeparty.dateadded)  BETWEEN "' . $beginLastMonth . '" AND  "' . $endLastMonth.'" ');
      } elseif ($dateadded == 'this_week') {
          $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
          $endThisWeek   = date('Y-m-d', strtotime('sunday this week'));
          array_push($where, ' AND DATE(tbloppositeparty.dateadded) BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'" ');
      } elseif ($dateadded == 'last_week') {
          $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
          $endLastWeek   = date('Y-m-d', strtotime('sunday last week'));
          array_push($where, 'AND DATE(tbloppositeparty.dateadded) BETWEEN "' . $beginLastWeek . '" AND "' .$endLastWeek.'" ');
          
      }
  }

}
//  print_r(date('Y-m-d', strtotime($this->ci->input->post('start_date'))));
// print_r($this->ci->input->post('end_date'));
if($this->ci->input->post('start_date')){
    if($this->ci->input->post('end_date')){
        $start_date = str_replace('/', '-',$this->ci->input->post('start_date'));
        // print_r($start_date);
         $start_date = date('Y-m-d', strtotime($start_date));
        //  print_r($start_date);
        $end_date = str_replace('/', '-',$this->ci->input->post('end_date'));
        $end_date = date('Y-m-d', strtotime($end_date));
        array_push($where, 'AND DATE(tbloppositeparty.dateadded) BETWEEN "' . $start_date . '" AND "' .$end_date.'" ');
    }
}



//$aColumns = do_action('customers_table_sql_columns', $aColumns);


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('client_id'));

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

    $row = array();

    // Bulk actions
    $row[] = $aRow['id'];;
    // User id
	 $extension = pathinfo($aRow['profile_image'] , PATHINFO_EXTENSION);
if ($aRow['profile_image'] != '' && $extension!='pdf') {
            $row[] = '<img class="staff-profile-image-small" src="'. base_url('uploads/oppositeparty/').$aRow['id'].'/'.$aRow['profile_image'] .'">';
}else{
	 $row[] = '<img class="staff-profile-image-small" src="'. base_url('uploads/oppositeparty/user-placeholder.jpg').'">';
}
    $row[] = '<a href="' . admin_url('opposite_parties/opposite_party/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';

    $row[] = $aRow['tradelicence']; 
    $row[] = _d($aRow['trade_expiry']); 
  //  $row[] = '<a href="' . admin_url('clients/client/' . $aRow['client_id']) . '">' . $aRow['company'] . '</a>';
    // Primary contact email
    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    $row[] = ($aRow['mobile'] ? '<a href="tel:' . $aRow['mobile'] . '">' . $aRow['mobile'] . '</a>' : '');

   

    //$row[] = $groupsRow;
    $row[] = $aRow['city'];
	  // Toggle active/inactive customer
    $toggleActive = '<div class="onoffswitch" data-toggle="tooltip" data-title="' . _l('customer_active_inactive_help') . '">
    <input type="checkbox"' .  ' data-switch-url="' . admin_url() . 'opposite_parties/change_opponent_status" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow[db_prefix().'oppositeparty.active'] == 1 ? 'checked' : '') . '>
    <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    // For exporting
    $toggleActive .= '<span class="hide">' . ($aRow[db_prefix().'oppositeparty.active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';

    $row[] = $toggleActive;
   
    /*$hook = do_action('customers_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];*/

    // Table options
    $options = icon_btn('opposite_parties/opposite_party/' . $aRow['id'], 'pencil-square-o');

    // Show button delete if permission for delete exists
    if ($hasPermissionDelete) {
        $options .= icon_btn('opposite_parties/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
