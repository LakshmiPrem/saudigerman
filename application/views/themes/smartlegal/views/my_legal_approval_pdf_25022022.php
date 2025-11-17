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
$tblhtml ='<br>';

$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;">'.$legalapprove->subject.' - Approval</h3></div>';
$tblhtml .= '<br> <span style="font-size:16px;text-transform: capitalize;">'.$legalapprove->message.'</span><br><br>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">
 <tr>  
    <th width="50%" style="text-align:center;">'._l('request_by').'</th>
    <th width="50%" style="text-align:center;">'._l($legalapprove->firstname).'</th>
   </tr>
    <tr>  
    <th width="50%" style="text-align:center;">'._l('date').'</th>
    <th width="50%" style="text-align:center;">'._l(date('d-M-Y',strtotime($legalapprove->date))).'</th>
   </tr>
 <tr>
    <th width="50%" style="text-align:center;">' . _l('client') . '</th>
    <th width="50%" style="text-align:center;">' . $legalapprove->company . '</th>
  </tr> 
  <tr>
    <th width="50%" style="text-align:center;">' . _l('client_name') . '</th>
    <th width="50%" style="text-align:center;">' . $legalapprove->opposteparty.'</th>
  </tr>';


$tblhtml .= '<tbody></tbody></table>';

$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;">Approval</h3></div><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="15" border="1">
<thead>';

foreach ($legalapprove->approval as  $approval) {

 $tblhtml .= '<tr> 
    <th width="40%" style="text-align:center;">'._l($approval['approval_type']).'</th>
    <th width="20%" style="text-align:center;">Approved <br> <img src="'.base_url('assets/images/checked.jpg').'" style="width:50px;height:50px;" /></th>
    <th width="40%" style="text-align:center;">'.get_staff_full_name($approval['staffid']).'<br>'.date('Y-m-d',strtotime($approval['dateadded'])).'<br>(Remark : '.$approval['approval_remarks'].'.)</th>
  </tr>';
}

 $tblhtml .=' </thead><tbody></tbody></table>';
/*//print_r($legaltask);
foreach ($legaltask as $value) {
 
$tblhtml .= '<tr>';
$tblhtml .= '<td style="text-align:center;height:80px;">'.$value['name'].'</td>';
$tblhtml .= '<td style="text-align:center;">'. get_staff_full_name($value['staffid']).'<br>'.date('d-m-Y',strtotime($value['startdate'])).'</td>';
$tblhtml .= '</tr>';

}
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';*/

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>