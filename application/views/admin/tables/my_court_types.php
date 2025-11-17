<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name',
    );
$sIndexColumn = "id";
$sTable       = 'tblcourttypes';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('id'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_courtType(this,'.$aRow['id'].'); return false;" data-name="'.$aRow['name'].'">' . $_data . '</a> '. '<span class="badge pull-right">'.total_rows('tblcaseprojects',array('court_type'=>$aRow['id'])).'</span>';
        }
        $row[] = $_data;


    }

    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_courtType(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name']));
    $row[]   = $options .= icon_btn('hearing/delete_court_type/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
