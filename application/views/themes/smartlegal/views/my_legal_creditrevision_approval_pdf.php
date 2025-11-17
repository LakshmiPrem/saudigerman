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
$tblhtml .= '<h3 style="font-size:20px;"><u>Sub: '.$legalapprove->subject.'</u></h3>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody><tr><td width="10%" align="center" rowspan="2">Sl No</td><td width="20%" align="center" rowspan="2">Customer</td><td width="15%" align="center" rowspan="2">Customer Code</td><td width="15%" align="center" rowspan="2">Type Of Business</td><td width="20%" align="center" colspan="2">Existing Credit Limit</td><td width="20%" align="center" colspan="2">Proposed Credit Limit</td></tr>
<tr><td>Days</td><td>Amount</td><td>Days</td><td>Amount</td></tr>
<tr><td width="10%" align="center">1</td><td align="center">'.get_opposite_party_name($legalapprove->opposteparty).'</td><td align="center">'.$legalapprove->customer_code.'</td><td align="center">'.$legalapprove->business_type.'</td><td>'.$legalapprove->excredit_days.'</td><td>'.$legalapprove->excredit_amount.'</td><td>'.$legalapprove->procredit_days.'</td><td>'.$legalapprove->procredit_amount.'</td></tr></tbody></table>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">
<tr>
 	<th colspan="3" width="100%" align="center">'._l('additional_info').'</th>
   
  </tr> 
 <tr>
 	<th width="10%" align="center">a)</th>
    <th width="70%">'._l('bank_statment1').'</th>
    <th width="20%">' . $gchq1 . '</th>
  </tr> 
   <tr>  
   <th width="10%" align="center">b)</th>
    <th width="70%">'._l('payment_terms1').'</th>
    <th width="20%">'.$pterm.'</th>
   </tr>
   <tr>
   <th width="10%" align="center">c)</th>
    <th width="70%">'._l('securechq_amount').'</th>
    <th width="20%">' . $legalapprove->securechq_amount.'</th>
  </tr>
  <tr>
   <th width="10%" align="center">d)</th>
    <th width="70%">'._l('balance_confirm').'</th>
    <th width="20%">' . strtoupper($legalapprove->balance_confirm).'</th>
  </tr>
 <tr>
    <th width="10%" align="center">e)</th>
    <th width="70%">'._l('year_return_cheque').'</th>
    <th width="20%">' . $legalapprove->year_return_cheque.'</th>
  </tr>
   <tr>
   <th width="10%" align="center">f)</th>
    <th width="70%">'._l('police_civilcase').'</th>
    <th width="20%">' . strtoupper($legalapprove->police_civilcase).'</th>
  </tr>
   <tr>
   <th width="10%" align="center">g)</th>
    <th width="70%">'._l('year_payment_default').'</th>
    <th width="20%">' . strtoupper($legalapprove->year_payment_default).'</th>
  </tr>
   <tr>
   <th width="10%" align="center">h)</th>
    <th width="70%">'._l('dealing_customer').'</th>
    <th width="20%">' . $legalapprove->dealing_customer.'</th>
  </tr>';

$tblhtml .= '</table>';


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
	 
     if(($legalapprove->creditapproval!='') && ($legalapprove->creditapproval->addedfrom==$approval['staffid']) ){
		
		$tblhtml.='<tr><td width="40%" align="center"><strong>Credit Offered By Syster Concern</strong></td><td  width="20%" align="center"><strong>'.$legalapprove->creditapproval->branch1.'</strong></td><td  width="20%" align="center"><strong>'.$legalapprove->creditapproval->branch2.'</strong></td><td  width="20%" align="center"><strong>'.$legalapprove->creditapproval->branch3.'</strong></td></tr>
	<tr><td><strong>Credit Limit</strong></td><td align="center">'.$legalapprove->creditapproval->btcredit_limit.'</td><td align="center">'.$legalapprove->creditapproval->bagcredit_limit.'</td><td align="center">'.$legalapprove->creditapproval->bmccredit_limit.'</td></tr>
	<tr><td><strong>Credit Days</strong></td><td align="center">'.$legalapprove->creditapproval->btcredit_days.'</td><td align="center">'.$legalapprove->creditapproval->bagcredit_days.'</td><td align="center">'.$legalapprove->creditapproval->bmccredit_days.'</td></tr>'; 
		
	 }
	if($approval['credit_period']!=''){
	$tblhtml.= '<tr><th colspan="4"> Credit Period: '.$approval['credit_period'].' Days &nbsp;&nbsp;&nbsp;&nbsp;Credit Amount: '.$approval['credit_amount'] .'&nbsp;&nbsp;&nbsp;'.$apstatus.' - '.$apdt.'</th></tr>';
	}
	else{
		$tblhtml.= '<tr><th colspan="4"> &nbsp;&nbsp;&nbsp;'.$apstatus.' - '.$apdt.'</th></tr>';
	}
	
}

 $tblhtml .=' </thead><tbody></tbody></table>';
	}

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>