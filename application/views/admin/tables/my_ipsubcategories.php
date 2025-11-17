<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'subcategory_name','category_id','active',
    );
$sIndexColumn = "id";
$sTable       = 'tblip_subcategory';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('id'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
     $row[] = '<a href="#" onclick="edit_ipsubcategory(this,'.$aRow['id'].'); return false;" data-name="'.$aRow['subcategory_name'].'" data-type="'.$aRow['category_id'].'">' .$aRow['subcategory_name'] . '</a> '. '<span class="badge pull-right">'.total_rows('tblprojects',array('ip_subcategory'=>$aRow['id'],'case_type'=>'intellectual_properties')).'</span>';
	$row[]=get_matteripcategory($aRow['category_id']);
	 // Toggle active/inactive customer
    $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch"  data-toggle="tooltip" data-title="' . _l('active') . '">
        <input type="checkbox"  data-switch-url="' . admin_url().'casediary/change_status/'.$sTable.'" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] .'" data-id="' . $aRow['id'] . '" ' . ($aRow['active'] == '1' ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    //For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['active'] == '1' ? _l('yes') : _l('no')) . '</span>';
    //$row[] = $aRow['datecreated'];
  
    $row[] = $toggleActive;

    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_ipsubcategory(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['subcategory_name'],'data-type'=>$aRow['category_id']));
    $row[]   = $options .= icon_btn('casediary/delete_ipsubcategory/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
