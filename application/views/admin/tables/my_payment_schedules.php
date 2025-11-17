<?php
defined('BASEPATH') or exit('No direct script access allowed');

$total_client_contacts = total_rows('tblpayment_schedule', array('project_id'=>$client_id));

$aColumns = array(
    'project_id',
    'installment_date',
    'installment_amount',
    'installment_status',
    'is_verified',
    'verified_by',
    'verified_date',
    'remarks'
);

$sIndexColumn = "id";
$sTable = 'tblpayment_schedule';
$join = array();



$where = array();
 array_push($where, 'AND project_type="project"');

if ($client_id != '') {
    array_push($where, 'AND project_id='.$client_id);
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblpayment_schedule.id as id'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    
    $row[] = _d($aRow['installment_date']);
    $row[] = app_format_money($aRow['installment_amount'],'AED');

    //$row[] = $aRow['recovery_id'];
    if($aRow['installment_status'] == 'not_paid'){
    $status = '<div  style="color:red;"><b>'._l($aRow['installment_status']).'</b></div>';

    }else{
    $status = '<div style="color:green;"><b>'._l($aRow['installment_status']).'</b></div>';

    }
    $row[] = $status;
     // Toggle active/inactive customer
    $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch" data-toggle="tooltip" data-title="' . _l('is_verified') . '">
        <input type="checkbox" onchange="setTimeout(function(){ reload_tbl()},100);" data-switch-url="' . admin_url().'projects/verify_payinstallment" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['is_verified'] == '1' ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    //For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['is_verified'] == '1' ? _l('yes') : _l('no')) . '</span>';
    //$row[] = $aRow['datecreated'];


    
    $row[] = $toggleActive;
    if($aRow['is_verified'] == 1){
        $row[] = ($aRow['verified_by'] > 0) ? get_staff_full_name($aRow['verified_by']) : '';
        $row[] = _dt($aRow['verified_date']);
    }else{
        $row[] = '-';
        $row[] = '-';
    }
    $row[] = $aRow['remarks'];
    $options = '';
    /*if($aRow['installment_status'] == 'paid'){
         $options .='<a data-toggle="modal" onclick="append_notify('.$aRow['id'].')"  data-target="#notify_installment" class="btn btn-info btn-icon "><i class="fa fa-bell"></i></a>';
    }*/
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('onclick'=>'payinstallment('.$aRow['project_id'].','.$aRow['id'].');return false;'));
    if (has_permission('projects', '', 'delete') ) {
        
            $options .= icon_btn('projects/delete_payinstallment/'.$aRow['project_id'].'/'.$aRow['id'], 'remove', 'btn-danger _delete');
    
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
