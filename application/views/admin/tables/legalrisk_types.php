<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'name',
    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'risktypes';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'name') {
            $_data = '<a href="#" onclick="edit_type1(this,' . $aRow['id'] . '); return false;" data-name="' . $aRow['name'] . '">' . $_data . '</a> ' . '<span class="badge pull-right">' . total_rows(db_prefix().'legal_risk', ['risktype' => $aRow['id']]) . '</span>';
        }
        $row[] = $_data;
    }

    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_type1(this,' . $aRow['id'] . '); return false;', 'data-name' => $aRow['name']]);
    $row[]   = $options .= icon_btn('legal_risks/delete_legalrisk_type/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
