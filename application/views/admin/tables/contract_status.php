<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'statuscolor',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'contracts_status';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_status(this,' . $aRow['id'] . '); return false;" data-name="' . $aRow['name'] . '">' . $_data . '</a> ' ;
        }
        $row[] = $_data;
    }
    
    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_status(this,' . $aRow['id'] . '); return false;', 'data-name' => $aRow['name'],'data-color'=>$aRow['statuscolor']]);
    $row[]   = $options .= icon_btn('contracts/delete_contract_status/' . $aRow['id'], 'remove', 'btn-danger _delete');
    // $row[]  =$aRow['statuscolor'];
    $output['aaData'][] = $row;
}
