<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name','shortname','active',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'constitution_type';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    $row[] = '<a href="#" onclick="edit_contype(this,'.$aRow['id'].'); return false;" data-name="'.$aRow['name'].'" data-short="'.$aRow['shortname'].'">' . $aRow['name']. '</a> '. '<span class="badge pull-right">'.total_rows('tblclient_subfile',array('document_type'=>$aRow['id'])).'</span>';
	$row[]= $aRow['shortname'];
	 $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch"  data-toggle="tooltip" data-title="' . _l('active') . '">
        <input type="checkbox"  data-switch-url="' . admin_url().'casediary/change_status/'.$sTable.'" name="onoffswitch" class="onoffswitch-checkbox" id="' . $aRow['id'] .'" data-id="' . $aRow['id'] . '" ' . ($aRow['active'] == '1' ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' . $aRow['id'] . '"></label>
    </div>';

    //For exporting
    $toggleActive .= '<span class="hide">' . ($aRow['active'] == '1' ? _l('yes') : _l('no')) . '</span>';
	$row[] = $toggleActive;
    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_contype(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name'],'data-short'=>$aRow['shortname']));
    $row[]   = $options .= icon_btn('clients/delete_constitution_type/' . $aRow['id'], 'remove', 'btn-danger _delete');
	$output['aaData'][] = $row;
}
