<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionDelete = has_permission('lawyers', '', 'delete');

 
$aColumns = array(
    '1',
    'tblstaff.staffid as lawyerid',
    'CONCAT(firstname," ",lastname) as name',
    'tblstaff.email as email',
    'tblstaff.phonenumber as phonenumber',
    'city',
    
    );

$sIndexColumn = "staffid";
$sTable       = 'tblstaff';
$where   = array();
// Add blank where all filter can be stored
$filter  = array(' ');

$join = array();

array_push($where,'WHERE is_lawyer = "1"');


$aColumns = hooks()->apply_filters('customers_table_sql_columns', $aColumns);


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('is_lawyer'));

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

    $row = array();

    // Bulk actions
    $row[] = $aRow['lawyerid'];;
    // User id
    // Company
    $company = $aRow['name'];

    $row[] = '<a href="' . admin_url('lawyers/lawyer/' . $aRow['lawyerid']) . '">' . $company . '</a>';


    // Primary contact email
    $row[] = ($aRow['email'] ? '<a href="mailto:' . $aRow['email'] . '">' . $aRow['email'] . '</a>' : '');

    // Primary contact phone
    $row[] = ($aRow['phonenumber'] ? '<a href="tel:' . $aRow['phonenumber'] . '">' . $aRow['phonenumber'] . '</a>' : '');

   

    //$row[] = $groupsRow;
    $row[] = $aRow['city'];
    //$row[] = @$this->ci->db->get_where('tblprojects',array('lawyer_id'=>$aRow['lawyerid']))->num_rows();
    //if($aRow['is_lawyer'] == 1) $is_lawyer = 'Lawyer'; else $is_lawyer = 'User';
    //$row[] = $is_lawyer; 
    $hook = hooks()->apply_filters('customers_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];

    // Table options
    $options = icon_btn('lawyers/lawyer/' . $aRow['lawyerid'], 'pencil-square-o');

    // Show button delete if permission for delete exists
    if ($hasPermissionDelete) {
        $options .= icon_btn('lawyers/delete/' . $aRow['lawyerid'], 'remove', 'btn-danger _delete');
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
