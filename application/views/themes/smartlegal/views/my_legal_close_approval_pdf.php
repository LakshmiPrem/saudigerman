<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = '&nbsp;&nbsp;&nbsp; To <br><br><b>&nbsp;&nbsp;&nbsp;The Chairman';
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div>';

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(25);

$tblhtml ='';
$tblhtml .= '<table width="100%"><thead></thead><tbody><tr><td width="70%">&nbsp;&nbsp;&nbsp;</td><td><b>Date : '._dt($legalapprove->bmapproval).'<br>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'</b></td></tr></tbody></table>';
$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;">REQUEST FOR CLOSING CASE OF '.strtoupper($legalapprove->opposteparty).' vs '.strtoupper($legalapprove->company).'</h3></div>';

$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">
<tr>
    <th width="35%">' . _l('name_client_name') . '</th>
    <th width="65%" >' . strtoupper($legalapprove->opposteparty).'</th>
  </tr>
 <tr>  
    <th width="35%" >'._l('project_customer').'</th>
    <th width="65%">' . $legalapprove->company . '</th>
  </tr> 
  <tr>
    <th width="35%">' . _l('ticket_settings_code') . '</th>
    <th width="65%">' . $legalapprove->customer_code.'</th>
  </tr>
   <tr>  
    <th width="35%">'._l('closecase_type').'</th>
    <th width="65%">'.$legalapprove->closecase_type.'</th>
   </tr>
   
  
 <tr>
    <th width="35%">' . _l('case_reason') . '</th>
    <th width="65%">' . $legalapprove->gen_reason.'</th>
  </tr>
   <tr>
    <th width="35%">' . _l('ledgerclaim_amount') . '</th>
    <th width="65%">' . $legalapprove->ledgerclaim_amount.'</th>
  </tr>
   <tr>
    <th width="35%">' . _l('legal_request') . '</th>
    <th width="65%">' . $legalapprove->legalrequest_no.'</th>
  </tr>';
$tblhtml .=' <tr  nobr="true">
    <th width="35%" >' . _l('civilcase_fileddet') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td width="20%" style="text-align:center;"><b>'._l('guaranteamount').'</b></td><td width="80%" style="text-align:center;"><b>'._l('remarks').'</b></td></tr>';
	if($legalapprove->civilcase_fileddet!=''){
						$civilcases=json_decode($legalapprove->civilcase_fileddet,true);
							$limit=sizeof($civilcases['amount']);
							for($i=0;$i<$limit;$i++) {
								if($civilcases['amount'][$i]!=''){
									$civil20=number_format($civilcases['amount'][$i],2);
								}
								else{
									$civil20='';
								}
				
	$tblhtml.='<tr><td style="text-align:center;">'.$civil20 .'</td><td style="text-align:center;">'.$civilcases['remarks'][$i].'</td></tr>';
							}
	}
$tblhtml.='</tbody></table></th></tr>
 <tr>
    <th width="35%"><b>' . _l('particulars') . '</b></th>
    <th width="65%"><b>' . _l('guaranteamount').'</b></th>
  </tr>
   <tr>
    <th width="35%">' . _l('closecase_amount') . '</th>
    <th width="65%">' . number_format($legalapprove->closecase_amount,2).'</th>
  </tr>
   <tr>
    <th width="35%">' . _l('total_expense') . '</th>
    <th width="65%">' . number_format($legalapprove->total_expense,2).'</th>
  </tr>
   <tr>
    <th width="35%"><b>' . _l('total_amount') . '</b></th>
    <th width="65%"><b>' . number_format(($legalapprove->closecase_amount+$legalapprove->total_expense),2).'</b></th>
  </tr>
   <tr>
    <th width="35%">' . _l('amount_received1') . '</th>
    <th width="65%">' . number_format($legalapprove->amount_received,2).'</th>
  </tr>
   <tr>
    <th width="35%">' . _l('excess_amount') . '</th>
    <th width="65%">' . number_format($legalapprove->excess_amount,2).'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('writeoff_amount') . '</th>
    <th width="65%">' . number_format($legalapprove->writeoff_amount,2).'</th>
  </tr>';
	
if($legalapprove->approval!=null){
	$tblhtml .= '<tr  nobr="true"><td colspan="2"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr>';
foreach ($legalapprove->approval as  $approval) {
	$appstatus='';
	if($approval['approval_status']=='3') $appstatus=get_staff_full_name($approval['staffid']).'<br><br>'.date('d/m/Y',strtotime($approval['dateapproved'])).'<br> Approved';
  $tblhtml .='<td align="center">'.get_approval_heading_name_by_id($approval['approval_heading_id']).'<br><br>'.$appstatus.'<br></td>';
}


$tblhtml .= '</tr></tbody></table></td></tr>';
	}
$tblhtml.='</tbody></table>';

/*//print_r($legaltask);
foreach ($legaltask as $value) {
 
$tblhtml .= '<tr>';
$tblhtml .= '<td style="text-align:center;height:80px;">'.$value['name'].'</td>';
$tblhtml .= '<td style="text-align:center;">'. get_staff_full_name($value['staffid']).'<br>'.date('d-m-Y',strtotime($value['startdate'])).'</td>';
$tblhtml .= '</tr>';
 <tr><th colspan="2"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="40" border="0"><tbody>
	<tr><th>Prepared By</th><th>'._l('Forwarded By').'</th><th>'._l('Reviewed By').'</th></tr>
	<tr><th>Recommended By<br></th><th>'._l('Verified by').'<br></th><th>'._l('Approved By').'<br></th></tr>
}
$tblhtml .= '</tbody>';
$tblhtml .= '</table>';*/

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>