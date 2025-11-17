<?php

defined('BASEPATH') or exit('No direct script access allowed');

$total_client_contacts = total_rows(db_prefix() . 'client_shareholder', ['userid' => $client_id]);
$aColumns        = [db_prefix() . 'shareholders.shareholder_name as holdername','share_percentage'];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'client_shareholder';
$join         = [ 'LEFT JOIN ' . db_prefix() . 'shareholders ON ' . db_prefix() . 'client_shareholder.shareholder_id = ' . db_prefix() . 'shareholders.id',];

$where = ['AND userid=' . $this->ci->db->escape_str($client_id)];


$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblclient_shareholder.id','userid']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $rowName = '<a href="#" onclick="shareholder(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . $aRow['holdername'] . '</a>';

    $rowName .= '<div class="row-options">';

    $rowName .= '<a href="#" onclick="shareholder(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';

    if (has_permission('customers', '', 'delete') || is_customer_admin($aRow['userid'])) {
       
            $rowName .= ' | <a href="' . admin_url('clients/delete_clientshareholder/' . $aRow['userid'] . '/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
       
    }

    $rowName .= '</div>';

    $row[] = $rowName;
    $row[] = $aRow['share_percentage'];

   
  /*  $outputActive = '<div class="onoffswitch">
                <input type="checkbox"' . (total_rows(db_prefix() . 'clients', 'registration_confirmed=0 AND userid=' . $aRow['userid']) > 0 ? ' disabled' : '') . ' data-switch-url="' . admin_url() . 'clients/change_contact_status" name="onoffswitch" class="onoffswitch-checkbox" id="c_' . $aRow['id'] . '" data-id="' . $aRow['id'] . '"' . ($aRow['active'] == 1 ? ' checked': '') . '>
                <label class="onoffswitch-label" for="c_' . $aRow['id'] . '"></label>
            </div>';
    // For exporting
    $outputActive .= '<span class="hide">' . ($aRow['active'] == 1 ? _l('is_active_export') : _l('is_not_active_export')) . '</span>';
    $row[] = $outputActive;
*/
   $output['aaData'][] = $row;
}
