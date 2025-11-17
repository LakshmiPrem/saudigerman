<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = pdf_logo_url();
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br><br>';

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(34);
$tblhtml ='<br><br>';

$tblhtml .= '<div style="text-align:center;"> <h1 style="font-size:26px;">Hearing Notice</h1></div>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">
 <tr>  
    <th width="50%" style="text-align:center;">'._l('hearing_type').'</th>
    <th width="50%" style="text-align:center;">'._l($hearing->hearing_type).'</th>
   </tr>
 <tr>
    <th width="50%" style="text-align:center;">' . _l('client') . '</th>
    <th width="50%" style="text-align:center;">' . $hearing->client_name . '</th>
  </tr> 
<tr>
    <th width="50%" style="text-align:center;">' . _l('hearing_subject') . '</th>   
    <th width="50%" style="text-align:center;">' .$hearing->subject . '</th>
</tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('hearing_date') . '</th>
    <th width="50%" style="text-align:center;">' . _d($hearing->hearing_date) . '</th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('hearing_against') . '</th>
    <th width="50%" style="text-align:center;">' . $hearing->opposite_party_name . '</th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('casediary_casenumber') . '</th>
    <th width="50%" style="text-align:center;">' . $hearing->court_no . '</th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('next_hearing') . '</th>
    <th width="50%" style="text-align:center;">' . _d($hearing->postponed_until) . '</th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('last_decision') . '</th>
    <th width="50%" style="text-align:center;">' . $hearing->proceedings . '</th>
  </tr>';


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