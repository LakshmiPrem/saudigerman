<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = '&nbsp;&nbsp;&nbsp; To <br><br><b>&nbsp;&nbsp;&nbsp;The Chairman';
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br>';

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(34);

$tblhtml ='<br>';
$tblhtml .= '<table width="100%"><thead></thead><tbody><tr><td width="70%">&nbsp;&nbsp;&nbsp;</td><td><b>Date : '._dt($legalapprove->bmapproval).'<br>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'</b></td></tr></tbody></table>';
$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;">'.$legalapprove->subject.'</h3></div>';

$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">
 <tr>  
    <th width="50%">'._l('request_from').'</th>
    <th width="50%">' . $legalapprove->company . '</th>
  </tr> 
   <tr>  
    <th width="50%" >'._l('request_type').'</th>
    <th width="50%">'.$legalapprove->service_name.'</th>
   </tr>
   <tr>
    <th width="50%">' . _l('client_name') . '</th>
    <th width="50%">' . $legalapprove->opposteparty.'</th>
  </tr>
  <tr>
    <th width="50%">' . _l('ticket_settings_code') . '</th>
    <th width="50%">' . $legalapprove->customer_code.'</th>
  </tr>
 <tr>
    <th width="50%">' . _l('reason') . '</th>
    <th width="50%">' . $legalapprove->gen_reason.'</th>
  </tr>
   <tr>
    <th width="50%">' . _l('oth_comments') . '</th>
    <th width="50%">' . $legalapprove->oth_comments.'</th>
  </tr>';
if($legalapprove->service=='5' || $legalapprove->service=='6'){
	$tblhtml .='<tr><th colspan="2" style="text-align:center;">' . _l('credit_attachment') . '</th></tr><tr><th colspan="2"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><tbody>
	<tr><th width="10%" style="text-align:center;">Sr. No.</th><th width="20%" style="text-align:center;" >'._l('document_type').'</th><th width="20%" style="text-align:center;">'._l('document_number').'</th><th width="20%" style="text-align:center;">'._l('document_name').'</th><th width="15%" style="text-align:center;" >'._l('nationality').'</th><th width="15%" style="text-align:center;">'._l('expiry_date').'</th></tr>';
	$i=1;
	
							foreach($legalapprove->creditapp as $row){
						$tblhtml .='<tr><td>'.$i++. '</td><td>'.get_document_type_name($row['document_type']).'</td><td>'.$row['document_number'].'</td><td>'.$row['document_name'].'</td><td>'.get_countryproject_name($row['nationality']).'</td>
                         <td>'._d($row['expiry_date']).'</td></tr>';		
							}
	$tblhtml .= '</tbody></table></th></tr>';
}
$tblhtml .= '</table>';




if($legalapprove->approval!=null){
$tblhtml .= '<h3 style="font-size:20px;text-align:center;">Approval</h3><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">
<thead>';

foreach ($legalapprove->approval as  $approval) {

 $apstatus='';$apdt='';
	if($approval['approval_status']==3)
	{
		$apstatus='Approved';
		$apdt=_dt($approval['dateapproved']);
	}elseif($approval['approval_status']==4)
	{
		$apstatus='Rejected';
		$apdt=_dt($approval['dateapproved']);
	}
 $tblhtml .= '<tr> 
    <th width="40%" style="text-align:center;">'.get_approval_heading_name_by_id($approval['approval_heading_id']).'<br>'.get_staff_full_name($approval['staffid']).'<br></th>
    <th width="20%" style="text-align:center;">'.$apstatus.'<br>'.$apdt.'</th>
    <th width="40%" style="text-align:center;">'.$approval['approval_remarks'].'</th>  
  </tr>';
}

 $tblhtml .=' </thead><tbody></tbody></table>';
	}
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