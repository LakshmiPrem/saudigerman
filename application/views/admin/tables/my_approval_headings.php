<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name','rel_type','active',
    );
$sIndexColumn = "rel_id";
$sTable       = 'tblapproval_headings';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('id','head_order','rel_type','designation_id','threshold_limit'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
  	 $row[] = '<a href="#" onclick="edit_approval_heading(this,'.$aRow['id'].'); return false;" data-name="'.$aRow['name'].'" data-order="'.$aRow['head_order'].'" data-desig="'.$aRow['designation_id'].'" data-limit="'.$aRow['threshold_limit'].'" data-type="'.$aRow['rel_type'].'">' . $aRow['name']. '</a> ';
	$row[]= ucwords($aRow['rel_type']);
	 $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch"  data-toggle="tooltip" data-title="' . _l('active') . '">
        <input type="checkbox"  data-switch-url="' . admin_url().'casediary/change_status/'.$sTable.'" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] .'" data-id="' . $aRow['id'] . '" ' . ($aRow['active'] == '1' ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    //For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['active'] == '1' ? _l('yes') : _l('no')) . '</span>';
    //$row[] = $aRow['datecreated'];

    
    $row[] = $toggleActive;
    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_approval_heading(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name'],'data-order'=>$aRow['head_order'],'data-desig'=>$aRow['designation_id'],'data-limit'=>$aRow['threshold_limit'],'data-type'=>$aRow['rel_type']));
    $row[]   = $options .= icon_btn('approval/delete_approval_heading/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
