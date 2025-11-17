<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name','active',
    );
$sIndexColumn = "id";
$sTable       = 'tblcase_natures';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('id'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_case_nature(this,'.$aRow['id'].'); return false;" data-name="'.$aRow['name'].'">' . $_data . '</a> '. '<span class="badge pull-right">'.total_rows('tblcase_details',array('court_id'=>$aRow['id'])).'</span>';
        }
        $row[] = $_data;
	
	$row[]=$aRow[active]; 
}
    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_case_nature(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name']));
    $row[]   = $options .= icon_btn('casediary/delete_case_nature/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
