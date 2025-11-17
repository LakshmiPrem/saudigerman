<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    'statusname',
	    ];
$sIndexColumn = 'id';
$sTable       = db_prefix().'riskstatus';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], ['id','statuscolor']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    for ($i = 0; $i < count($aColumns); $i++) {
        $_data = $aRow[$aColumns[$i]];
        if ($aColumns[$i] == 'statusname') {
            $_data = '<a href="#" onclick="edit_type_status(this,' . $aRow['id'] . '); return false;" data-name="' . $aRow['statusname'] . '" data-color="' . $aRow['statuscolor'] . '">' . $_data . '</a> ' . '<span class="badge pull-right">' . total_rows(db_prefix().'legal_risk', ['risk_status' => $aRow['id']]) . '</span>';
        }
        $row[] = $_data;
    }

    $options = icon_btn('#', 'pencil-square-o', 'btn-default', ['onclick' => 'edit_type_status(this,' . $aRow['id'] . '); return false;', 'data-name' => $aRow['statusname'],'data-color' => $aRow['statuscolor']]);
    $row[]   = $options .= icon_btn('legal_risks/delete_legalrisk_status/' . $aRow['id'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
}
