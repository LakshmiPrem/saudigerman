<?php
defined('BASEPATH') or exit('No direct script access allowed');

$total_client_contacts = total_rows('tblrecoveries_installments', array('recovery_id'=>$client_id));

$aColumns = array(
	 'recovery_id',
    'installment_date',
	'amount',
    'installment_amount',
	'balance_amount',
    'installment_status',
    'is_verified',
    'verified_by',
    'verified_date',
    'remarks'
);

$sIndexColumn = "id";
$sTable = 'tblrecoveries_installments';
$join = array();



$where = array();
 array_push($where, 'AND recovery_type="corporate"');

if ($client_id != '') {
    array_push($where, 'AND recovery_id='.$client_id);
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblrecoveries_installments.id as id'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    
    $row[] = _d($aRow['installment_date']);
	 $row[] = format_money($aRow['amount']);
    $row[] = format_money($aRow['installment_amount']);
	 $row[] = format_money($aRow['balance_amount']);

    //$row[] = $aRow['recovery_id'];
    if($aRow['installment_status'] == 'not_paid'){
    $status = '<div  style="color:red;"><b>'._l($aRow['installment_status']).'</b></div>';

    }else  if($aRow['installment_status'] == 'part_paid'){
    $status = '<div  style="color:orange;"><b>'._l('partinstallment_status').'</b></div>';

    }else{
    $status = '<div style="color:green;"><b>'._l($aRow['installment_status']).'</b></div>';

    }
    $row[] = $status;
     // Toggle active/inactive customer
    $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch" data-toggle="tooltip" data-title="' . _l('is_verified') . '">
        <input type="checkbox" onchange="setTimeout(function(){ reload_tbl()},100);" data-switch-url="' . admin_url().'corporate_recoveries/verify_installment" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['is_verified'] == '1' ? 'checked' : '') . '>
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
    $options .= icon_btn('#', 'pencil-square-o', 'btn-default', array('onclick'=>'installment('.$aRow['recovery_id'].','.$aRow['id'].');return false;'));
    if (has_permission('corporate_recovery', '', 'delete') ) {
        
            $options .= icon_btn('corporate_recoveries/delete_installment/'.$aRow['recovery_id'].'/'.$aRow['id'], 'remove', 'btn-danger _delete');
    
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
