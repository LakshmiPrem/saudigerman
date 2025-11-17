<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name',
    'rate'
    );
$sIndexColumn = "id";
$sTable       = 'tbllawyercategories';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),array(),array('id'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_category(this,'.$aRow['id'].'); return false;" data-name="'.$aRow['name'].'##'.$aRow['rate'].'"  >' . $_data . '</a> '. '<span class="badge pull-right">'.total_rows('tbllawyers',array('category_id'=>$aRow['id'])).'</span>';
        }
        $row[] = $_data;
    }

    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_category(this,'.$aRow['id'].'); return false;','data-name'=>$aRow['name'].'##'.$aRow['rate']));
    $row[]   = $options .= icon_btn('lawyers/delete_lawyer_category/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
