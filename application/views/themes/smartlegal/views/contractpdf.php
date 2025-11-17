<?php defined('BASEPATH') or exit('No direct script access allowed');

/*if ($contract->signed == 1) {
    $contract->content .= '<div style="font-weight:bold;text-align: left;">';
    $contract->content .= '<p>' . _l('contract_signed_by') . ": {$contract->acceptance_firstname} {$contract->acceptance_lastname}</p>";
    $contract->content .= '<p>' . _l('contract_signed_date') . ': ' . _dt($contract->acceptance_date) . '</p>';
    $contract->content .= '<p>' . _l('contract_signed_ip') . ": {$contract->acceptance_ip}</p>";
    $contract->content .= '</div>';
}
if ($contract->party_signed == 1) {
    $contract->content .= '<div style="font-weight:bold;text-align: right;">';
    $contract->content .= '<p>' . _l('contract_signed_by') . ": {$contract->partyacc_firstname} {$contract->acceptance_lastname}</p>";
    $contract->content .= '<p>' . _l('contract_signed_date') . ': ' . _dt($contract->partyacc_date) . '</p>';
    $contract->content .= '<p>' . _l('contract_signed_ip') . ": {$contract->partyacc_ip}</p>";
    $contract->content .= '</div>';
}*/
// Theese lines should aways at the end of the document left side. Dont indent these lines
$html = <<<EOF
<div style="width:680px !important;">
$contract->content
</div>
EOF;
$pdf->writeHTML($html, true, false, true, false, '');
// create columns content
$left_column = '';
if ($contract->signed == 1) {
    $left_column .= '<div style="font-weight:bold;text-align: left;">';
    $left_column .= '<p>' . _l('contract_signed_by') . ": {$contract->acceptance_firstname} {$contract->acceptance_lastname}</p>";
    $left_column .= '<p>' . _l('contract_signed_date') . ': ' . _dt($contract->acceptance_date) . '</p>';
    $left_column  .= '<p>' . _l('contract_signed_ip') . ": {$contract->acceptance_ip}</p>";
    $left_column  .= '</div>';
}
$right_column = '';
if ($contract->party_signed == 1) {
    $right_column .= '<div style="font-weight:bold;text-align: right;">';
    $right_column .= '<p>' . _l('contract_signed_by') . ": {$contract->partyacc_firstname} {$contract->partyacc_lastname}</p>";
    $right_column .= '<p>' . _l('contract_signed_date') . ': ' . _dt($contract->partyacc_date) . '</p>';
    $right_column .= '<p>' . _l('contract_signed_ip') . ": {$contract->partyacc_ip}</p>";
    $right_column .= '</div>';
}
// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

// get current vertical position
$y = $pdf->getY();

// set color for background
$pdf->SetFillColor(255, 255, 255);

// set color for text
$pdf->SetTextColor(0, 63, 127);

// write the first column
$pdf->writeHTMLCell(80, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

// set color for background
$pdf->SetFillColor(255,255, 255);

// set color for text
//$pdf->SetTextColor(127, 31, 0);

// write the second column
$pdf->writeHTMLCell(80, '', '', '', $right_column, 0, 1, 1, true, 'J', true);

