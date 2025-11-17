<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    'key_provision',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'risk_checklists ';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_checklist(this,' . $aRow['id'] . '); return false;" data-name="' . $aRow['name'] . '">' . $_data . '</a> ' . '<span class="badge pull-right">' . total_rows(db_prefix().'contracts', ['contract_type' => $aRow['id']]) . '</span>';
        }
        if ($aColumns[$i] == 'key_provision') {
            $_data = $aRow['key_provision'];
        }
        $row[] = $_data;
    }

    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_checklist(this,' . $aRow['id'] . '); return false;', 'data-name' => $aRow['name'] , 'data-key' => $aRow['key_provision']]);
    $row[]   = $options .= icon_btn('contracts/delete_risk_value_checklist/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
