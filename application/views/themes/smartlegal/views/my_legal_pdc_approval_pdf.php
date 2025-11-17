<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
// Title
 // Set font
        $pdf->SetFont('helvetica', 'B', 18);
     $pdf->Cell(0, 15, strtoupper($legalapprove->company), 0, false, 'C', 0, '', 0, false, 'M', 'M');
 $pdf->SetFont('helvetica', 'B', 10);
$pdf->Ln(5);

$tblhtml ='';
$tblhtml .= '<table width="100%"><thead></thead><tbody><tr><td width="70%">&nbsp;&nbsp;&nbsp;</td><td><b>Date : '._dt($legalapprove->bmapproval).'<br>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'</b></td></tr></tbody></table>';
$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;">REQUEST FOR APPROVING LONG DATED PDC</h3></div>';

$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">
<tr>
    <th width="35%">' . _l('name_client_name') . '</th>
    <th width="65%" >' . strtoupper(get_opposite_party_name($legalapprove->opposteparty)).'</th>
  </tr>
 <tr>  
    <th width="35%" >'._l('ldc_salesperson').'</th>
    <th width="65%">' . $legalapprove->ldc_salesperson . '</th>
  </tr> 
 <tr>  
    <th colspan="2" style="text-align:center;" >'.strtoupper(_l('ldc_chequedetail')).'</th>
    
  </tr>'; 
$tblhtml .=' <tr  nobr="true">
   <th><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td width="30%" style="text-align:center;"><b>'._l('chequecount').'</b></td><td width="30%" style="text-align:center;"><b>'._l('chequenumber').'</b></td><td width="40%" style="text-align:center;"><b>'._l('chequeamount').'</b></td><td width="40%" style="text-align:center;"><b>'._l('chequedt').'</b></td><td width="40%" style="text-align:center;"><b>'._l('receive_month').'</b></td><td width="40%" style="text-align:center;"><b>'._l('allocation_amount').'</b></td><td width="40%" style="text-align:center;"><b>'._l('act_period').'</b></td><td width="33%" style="text-align:center;"><b>'._l('excess_days').'</b></td></tr>';
	if($legalapprove->ldc_chequedet!=''){
						$civilcases=json_decode($legalapprove->ldc_chequedet,true);
							$limit=sizeof($civilcases['chequeno']);
							for($i=0;$i<$limit;$i++) {
								if($civilcases['chequeamount'][$i]!=''){
									$civil20=number_format($civilcases['chequeamount'][$i],2);
								}
								else{
									$civil20='';
								}
				
	$tblhtml.='<tr><td style="text-align:center;">'.$civilcases['chequecount'][$i].'</td><td style="text-align:center;">'.$civilcases['chequeno'][$i].'</td><td style="text-align:center;">'.$civil20 .'</td><td style="text-align:center;">'.$civilcases['chequedt'][$i].'</td><td style="text-align:center;">'.$civilcases['receive_month'][$i].'</td><td style="text-align:center;">'.$civilcases['allocate_amount'][$i].'</td><td style="text-align:center;">'.$civilcases['act_period'][$i].'</td><td style="text-align:center;">'.$civilcases['excess_days'][$i].'</td></tr>';
							}
	}
$tblhtml.='</tbody></table></th></tr>
<tr>  
    <th colspan="2" style="text-align:center;" >'.strtoupper(_l('ldc_chequeotherdetail')).'</th>
    
  </tr>';
  $tblhtml .=' <tr  nobr="true">
   <th><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td width="30%" style="text-align:center;"><b>'._l('appcredit_days').'</b></td><td width="30%" style="text-align:center;"><b>'._l('yearsale').'</b></td><td width="40%" style="text-align:center;"><b>'._l('total_collection').'</b></td><td width="40%" style="text-align:center;"><b>'._l('collection_month').'</b></td><td width="40%" style="text-align:center;"><b>'._l('collection_period').'</b></td><td width="40%" style="text-align:center;"><b>'._l('average_period').'</b></td><td width="40%" style="text-align:center;"><b>'._l('ret_year').'</b></td><td width="33%" style="text-align:center;"><b>'._l('return_status').'</b></td></tr>';
	if($legalapprove->ldc_chequeothers!=''){
						$civilcases1=json_decode($legalapprove->ldc_chequeothers,true);
							$limit=sizeof($civilcases1['approveday']);
							for($i=0;$i<$limit;$i++) {
								if($civilcases1['total'][$i]!=''){
									$civil201=number_format($civilcases1['total'][$i],2);
								}
								else{
									$civil201='';
								}
				
	$tblhtml.='<tr><td style="text-align:center;">'.$civilcases1['approveday'][$i].'</td><td style="text-align:center;">'.$civilcases1['saleyear'][$i].'</td><td style="text-align:center;">'.$civil201 .'</td><td style="text-align:center;">'.$civilcases1['pendmonth'][$i].'</td><td style="text-align:center;">'.$civilcases1['pendamount'][$i].'</td><td style="text-align:center;">'.$civilcases1['average'][$i].'</td><td style="text-align:center;">'.$civilcases1['retcheque'][$i].'</td><td style="text-align:center;">'.$civilcases1['retstatus'][$i].'</td></tr>';
							}
	}
$tblhtml.='</tbody></table></th></tr>';
$tblhtml.='</tbody></table><br>';
	
if($legalapprove->approval!=null){
$tblhtml .= '<table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="3" border="1"><thead></thead><tbody>';

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
	if($approval['approval_remarks']!=''){
  $tblhtml .= '<tr nobr="true"> 
    <th colspan="4"><b><i>Comments By  '.get_staff_full_name($approval['staffid']).'</i></b></th></tr><tr>
	 <th colspan="4">'.$approval['approval_remarks'].'<br><br></th></tr>';
	 	
}
}
	$tblhtml .= '<tr  nobr="true"><td colspan="4"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr>';
foreach ($legalapprove->approval as  $approval) {
	$appstatus='';
	if($approval['approval_status']=='3') $appstatus=get_staff_full_name($approval['staffid']).'<br><br>'.date('d/m/Y',strtotime($approval['dateapproved'])).'<br> Approved';
  $tblhtml .='<td align="center">'.get_approval_heading_name_by_id($approval['approval_heading_id']).'<br><br>'.$appstatus.'<br></td>';
}
$tblhtml .= '</tr></tbody></table></td></tr>';


 $tblhtml .='</tbody></table>';
	}

$pdf->writeHTML($tblhtml, true, false, false, false, '');