<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();


//$pdf->writeHTMLCell($organizaion_info, true, false, false, false, '');
//$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(5);
$pdf->setMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->setHeaderMargin(PDF_MARGIN_HEADER);
$pdf->setFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetFont('helvetica', '', 8);
$pdf->SetAutoPageBreak(FALSE,2);
$gchq1='Not Available';
if($legalapprove->bank_stmt=="yes") $gchq1="Available";
$pterm='Every month PDC';
if($legalapprove->payment_terms=="yes") $pterm="Due Date";
$tblhtml ='';
$tblhtml .='<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5"><thead></thead><tbody><tr><td width="25%" align="center">'.get_branch_ticketlogo($legalapprove->userid).'<br></td><td width="50%" align="center"><b><h2>'.strtoupper($legalapprove->company).'</h2></b></td><td width="25%"><br>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'<br><b>Date: '._dt($legalapprove->bmapproval).' </b></td></tr></tbody></table>';
$tblhtml .= '<h3 style="font-size:20px;"><u>Sub:  Request for New Application Credit Limit (permanent)</u></h3>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody><tr><td width="10%" align="center" >Sl No</td><td width="40%" align="center">Customer</td><td width="20%" align="center">Customer Code</td><td width="30%" align="center" >'._l('credit_saleperson').'</td></tr>
<tr><td width="10%" align="center">1</td><td align="center">'.get_opposite_party_name($legalapprove->opposteparty).'</td><td align="center">'.$legalapprove->customer_code.'</td><td align="center">'.$legalapprove->credit_saleperson.'</td></tr>
<tr><td colspan="4" align="center"><b>For Office Use Only</b></td></tr></tbody></table>';

if($legalapprove->approval!=null){
$tblhtml .= '<table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="3" border="1"><thead>';


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
 $tblhtml .= '<tr nobr="true"> 
    <th colspan="4"><b><i>'.get_approval_heading_name_by_id($approval['approval_heading_id']).' - '.get_staff_full_name($approval['staffid']).'</i></b></th></tr><tr>
	 <th colspan="4">'.$approval['approval_remarks'].'<br><br></th></tr>';
	 if(($legalapprove->branchapproval!='') && ($legalapprove->branchapproval->addedfrom==$approval['staffid']) ){
		$tblhtml.='<tr><th colspan="4"><b>Payment Method : </b>'.ucwords(str_replace("_"," ",$legalapprove->branchapproval->payment_type)).'<br><b>Cheque Issuance : </b>'.ucwords($legalapprove->branchapproval->cheque_type).' Cheque</th></tr>' ;
	 }
	
	 
     if(($legalapprove->creditapproval!='') && ($legalapprove->creditapproval->addedfrom==$approval['staffid']) ){
		
		$tblhtml.='<tr><td width="40%" align="center"><strong>Credit Offered By Syster Concern</strong></td><td  width="20%" align="center"><strong>'.$legalapprove->creditapproval->branch1.'</strong></td><td  width="20%" align="center"><strong>'.$legalapprove->creditapproval->branch2.'</strong></td><td  width="20%" align="center"><strong>'.$legalapprove->creditapproval->branch3.'</strong></td></tr>
	<tr><td><strong>Credit Limit</strong></td><td align="center">'.$legalapprove->creditapproval->btcredit_limit.'</td><td align="center">'.$legalapprove->creditapproval->bagcredit_limit.'</td><td align="center">'.$legalapprove->creditapproval->bmccredit_limit.'</td></tr>
	<tr><td><strong>Credit Days</strong></td><td align="center">'.$legalapprove->creditapproval->btcredit_days.'</td><td align="center">'.$legalapprove->creditapproval->bagcredit_days.'</td><td align="center">'.$legalapprove->creditapproval->bmccredit_days.'</td></tr>'; 
		
	 }
	if($approval['credit_period']!=''){
	$tblhtml.= '<tr><th colspan="4"> Recommended Period: '.$approval['credit_period'].' Days &nbsp;&nbsp;&nbsp;&nbsp;Recommended Amount: '.$approval['credit_amount'] .'&nbsp;&nbsp;&nbsp;'.$apstatus.' - '.$apdt.'</th></tr>';
	}
	else{
		$tblhtml.= '<tr><th colspan="4"> &nbsp;&nbsp;&nbsp;'.$apstatus.' - '.$apdt.'</th></tr>';
	}
			

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