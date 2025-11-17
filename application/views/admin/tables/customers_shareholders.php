<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = ['firstname','lastname','phonenumber','email','stake_type'];

$sIndexColumn = 'id';
$sTable       = db_prefix().'shareholders';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id','shareholder_name']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
  	 $row[] = '<a href="#" data-toggle="modal" data-target="#customer_shareholder_modal" data-id="' . $aRow['id'] . '">' . $aRow['shareholder_name'] . '</a>';
	$row[]= $aRow['firstname'];
	$row[]= $aRow['lastname'];
	$row[]= $aRow['stake_type'];
	$row[]= $aRow['phonenumber'];
	$row[]= $aRow['email'];
	/* $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch"  data-toggle="tooltip" data-title="' . _l('active') . '">
        <input type="checkbox"  data-switch-url="' . admin_url().'casediary/change_status/'.$sTable.'" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] .'" data-id="' . $aRow['id'] . '" ' . ($aRow['active'] == '1' ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    //For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['active'] == '1' ? _l('yes') : _l('no')) . '</span>';
    //$row[] = $aRow['datecreated'];


    
    $row[] = $toggleActive;*/
	 $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['data-toggle' => 'modal', 'data-target' => '#customer_shareholder_modal', 'data-id' => $aRow['id']]);
    $row[]   = $options .= icon_btn('clients/delete_shareholder/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
  
}
