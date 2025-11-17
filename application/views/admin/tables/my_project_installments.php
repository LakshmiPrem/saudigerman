<?php
defined('BASEPATH') or exit('No direct script access allowed');

$total_client_contacts = total_rows('tblrecoveries_installments', array('recovery_id'=>$client_id));

$aColumns = array(
    'id',
    'installment_date',
    'installment_amount',
    'amount_received',
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
 array_push($where, 'AND recovery_type="project_recovery"');

if ($client_id != '') {
    array_push($where, 'AND recovery_id='.$client_id);
}



$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblrecoveries_installments.id as id,recovery_id'));

$output = $result['output'];
$rResult = $result['rResult'];
$j=1;
foreach ($rResult as $aRow) {
    $row = array();

    $row[] = $j++;
    $row[] = form_hidden('id[]',$aRow['id']).' '.render_date_input('installment_date[]','',_d($aRow['installment_date']));
   $row[] = render_input('installment_amount[]','',$aRow['installment_amount'],'number',array('style'=>'width:115px'));
    $row[] = render_input('amount_received[]','',$aRow['amount_received'],'number',array('style'=>'width:115px'));

    //$row[] = $aRow['recovery_id'];
    if($aRow['installment_status'] == 'not_paid'){
    $status = '<div  style="color:red;"><b>'._l($aRow['installment_status']).'</b></div>';

    }else{
    $status = '<div style="color:green;"><b>'._l($aRow['installment_status']).'</b></div>';

    }
    $paid_sele = $n_paid_sele = $p_paid_sele = ''; 
    if($aRow['installment_status'] == 'paid'){
        $paid_sele = 'selected';
    }elseif($aRow['installment_status'] == 'not_paid'){
        $n_paid_sele = 'selected';
    }elseif($aRow['installment_status'] == 'partially_paid'){
        $p_paid_sele = 'selected';
    }
    $installment_status =  '<select name="installment_status[]" class="form-control" style="width:115px;">
                <option value="paid"  '.$paid_sele.'>Received</option>
                <option value="partially_paid" '.$p_paid_sele.'>Partially Received</option>
                <option value="not_paid" '.$n_paid_sele.'>Not Received</option>
    </select>';
    $row[] = $installment_status;
     // Toggle active/inactive customer
    $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch" data-toggle="tooltip" data-title="' . _l('is_verified') . '">
        <input type="checkbox" onchange="setTimeout(function(){ reload_tbl()},100);" data-switch-url="' . admin_url().'projects/verify_installment" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] . '" data-id="' . $aRow['id'] . '" ' . ($aRow['is_verified'] == '1' ? 'checked' : '') . '>
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
    $row[] = render_textarea('remarks[]','',$aRow['remarks'],array('rows'=>1,'style'=>'width:180px'));
    $options = '';
    /*if($aRow['installment_status'] == 'paid'){
         $options .='<a data-toggle="modal" onclick="append_notify('.$aRow['id'].')"  data-target="#notify_installment" class="btn btn-info btn-icon "><i class="fa fa-bell"></i></a>';
    }*/
    $options .= icon_btn('#', 'paperclip', 'btn-default', array('onclick'=>'installment('.$aRow['recovery_id'].','.$aRow['id'].');return false;'));
    if (has_permission('projects', '', 'delete') ) {
        
            $options .= icon_btn('projects/delete_installment/'.$aRow['recovery_id'].'/'.$aRow['id'], 'remove', 'btn-danger _delete');
    
    }

    $row[] = $options;
    $output['aaData'][] = $row;
}
