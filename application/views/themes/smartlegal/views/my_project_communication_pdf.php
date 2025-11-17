<?php 
$dimensions = $pdf->getPageDimensions();
$communication = $project->communication;
// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = pdf_logo_url();
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br><br>';

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(25);
$tblhtml ='';

//$tblhtml .= '<div style="text-align:center;"> <h1 style="font-size:26px;">Hearing Notice</h1></div>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="0">
  <tr>  
    <th width="20%" style="text-align:left;">From</th>
    <th width="5%" style="text-align:center;">:</th>
    <th width="75%" style="text-align:left;">'.$communication->mail_from.'</th>
  </tr>
  <tr>  
    <th width="20%" style="text-align:left;">Sent</th>
    <th width="5%" style="text-align:center;">:</th>
    <th width="75%" style="text-align:left;">'.date('l , F d , Y H:i:s',strtotime($communication->date)).'</th>
  </tr>
  <tr>  
    <th width="20%" style="text-align:left;">To</th>
    <th width="5%" style="text-align:center;">:</th>
    <th width="75%" style="text-align:left;">'.$communication->mail_to.'</th>
  </tr>
  <tr>  
    <th width="20%" style="text-align:left;">Subject</th>
    <th width="5%" style="text-align:center;">:</th>
    <th width="75%" style="text-align:left;">'.$communication->subject.'</th>
  </tr>
  <tr>
    <th colspan="3">'.nl2br(trim($communication->content)).'</th>
  </tr>';
if($communication->attachments){   
  $tblhtml .='<tr>
    <th colspan="3">Attachments </th>
  </tr>';
  $att = array_column($communication->attachments, 'file_name');  
 // print_r($att); 
$tblhtml .= '<tr><th colspan="3">'.implode('<br>', $att).'</th></tr>';
}
$tblhtml .= '<tbody>';

/*foreach ($hearing as $value) {
 
$tblhtml .= '<tr>';
$tblhtml .= '<td style="text-align:center;">'.$value->case_title.'</td>';
$tblhtml .= '<td style="text-align:center;">'.$value->case_number.'</td>';
$tblhtml .= '<td style="text-align:center;">'.$value->hearings_dates.'</td>';
$tblhtml .= '<td style="text-align:center;">'.$value->claiming_amount.'</td>';
$tblhtml .= '<td style="text-align:center;" >'.$value->case_details.'</td>';
$tblhtml .= '<td style="text-align:center;">'.$value->referred_by.'</td>';

$tblhtml .= '</tr>';

}*/

$tblhtml .= '</tbody>';
$tblhtml .= '</table>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>