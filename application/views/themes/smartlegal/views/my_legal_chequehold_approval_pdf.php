<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
 // Title
 // Set font
        $pdf->SetFont('helvetica', 'B', 18);
     $pdf->Cell(0, 15, strtoupper($legalapprove->company), 0, false, 'C', 0, '', 0, false, 'M', 'M');
 $pdf->SetFont('helvetica', 'B', 9);
 $pdf->Ln(5);

$tblhtml ='';
$tblhtml .='<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="4"><thead></thead><tbody><tr><td width="35%">To<br>The Managing Director<br>Bosco Group of Companies L.L.C<br></td><td width="30%" align="center"></td><td width="35%"><br><br>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'<br><b>Date: '._dt($legalapprove->bmapproval).' </b></td></tr></tbody></table>';

$tblhtml .= ' <h3 style="font-size:20px;">Sub: REQUEST FOR CHEQUE HOLDING </h3>';

$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="4" border="1">
<tr>
	<th width="5%"> 1 </th>
    <th width="40%">' . _l('company_name') . '</th>
    <th width="55%" >' . strtoupper(get_opposite_party_name($legalapprove->opposteparty)).'</th>
  </tr>
 <tr>  
    <th width="5%"> 2 </th>
    <th width="40%">' ._l('doe').'</th>
    <th width="55%">' . _d($legalapprove->doe) . '</th>
  </tr> 
  <tr>
   <th width="5%"> 3 </th>
    <th width="40%">'  . _l('sister_concern') . '</th>
    <th width="55%">' . $legalapprove->sister_concern.'</th>
  </tr>
   <tr>  
   <th width="5%"> 4 </th>
    <th width="40%">' ._l('typeof_business1').'</th>
    <th width="55%">'.$legalapprove->typeof_business.'</th>
   </tr> 
 <tr>
    <th width="5%"> 5 </th>
    <th width="40%">'  . _l('typeof_license') . '</th>
    <th width="55%">' . $legalapprove->typeof_license.'</th>
  </tr>
   <tr>
    <th width="5%"> 6 </th>
    <th width="40%">'  . _l('partner_name') . '</th>
    <th width="55%">' . $legalapprove->partner_name.'</th>
  </tr>
   <tr>
   <th width="5%"> 7 </th>
    <th width="40%">'  . _l('holdnationality') . '</th>
    <th width="55%">' . $legalapprove->holdnationality.'</th>
  </tr>';
$tblhtml .=' <tr  nobr="true">
 	<th width="5%"> 8 </th>
    <th width="40%" >' . _l('document_status') . '</th>
    <th width="55%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td width="35%" style="text-align:center;"><b>'.strtoupper(_l('pp')).'</b></td><td width="35%" style="text-align:center;"><b>'.strtoupper(_l('tl')).'</b></td><td width="30%" style="text-align:center;"><b>'.strtoupper(_l('cc')).'</b></td></tr>';
$tblhtml.='<tr><td style="text-align:center;">'.$legalapprove->pp_avail .'</td><td style="text-align:center;">'.$legalapprove->tl_avail .'</td><td style="text-align:center;">'.$legalapprove->cc_avail.'</td></tr>';
						
$tblhtml.='</tbody></table></th></tr>';
$tblhtml.='<tr>
    <th width="5%"> 9 </th>
    <th width="40%">' . _l('bus_startdate') . '</th>
    <th width="55%">' . _d($legalapprove->bus_startdate).'</th>
  </tr>
  <tr>
     <th width="5%"> 10 </th>
    <th width="40%">' . _l('sister_deal') . '</th>
    <th width="55%">'.$legalapprove->sister_deal.'</th>
   </tr>
   <tr>
   <th width="5%"> 11 </th>
    <th width="40%">' . _l('hold_salesperson') . '</th>
    <th width="55%">'.$legalapprove->hold_salesperson.'</th>
   </tr>
   <tr>
    <th width="5%"> 12 </th>
    <th width="40%">' . _l('chqhold_amount') . '</th>
    <th width="55%">'.$legalapprove->chqhold_amount.'</th>
  </tr>
   <tr>
    <th width="5%"> 13 </th>
    <th width="40%">' . _l('sales_month') . '</th>
    <th width="55%">'.$legalapprove->sales_month.'</th>
  </tr>
  <tr>
    <th width="5%"> 14 </th>
    <th width="40%">' . _l('cheque_no') . '</th>
    <th width="55%">'.$legalapprove->cheque_no.'</th>
  </tr>
  <tr>
    <th width="5%"> 15 </th>
    <th width="40%">' . _l('cheque_bank') . '</th>
    <th width="55%">'.$legalapprove->cheque_bank.'</th>
  </tr>
   <tr>
    <th width="5%"> 16 </th>
    <th width="40%">' . _l('cheque_dt') . '</th>
    <th width="55%">'._d($legalapprove->cheque_dt).'</th>
  </tr>
  <tr>
    <th width="5%"> 17 </th>
    <th width="40%">' . _l('newdeposit_dt') . '</th>
    <th width="55%">'._d($legalapprove->newdeposit_dt).'</th>
  </tr>
  <tr>
    <th width="5%"> 18 </th>
    <th width="40%">' . _l('cheque_type') . '</th>
    <th width="55%">'.$legalapprove->cheque_type.'</th>
  </tr>
  <tr>
    <th width="5%"> 19 </th>
    <th width="40%">' . _l('credit_period') . '</th>
    <th width="55%">'.$legalapprove->credit_period.'</th>
  </tr>
  <tr>
    <th width="5%"> 20 </th>
    <th width="40%">' . _l('actcredit_period') . '</th>
    <th width="55%">'.$legalapprove->actcredit_period.'</th>
  </tr>
  
  <tr>
    <th width="5%"> 21 </th>
    <th width="40%">' . _l('nextpdc') . '</th>
    <th width="55%">'.$legalapprove->nextpdc.'</th>
  </tr>
  <tr>
    <th width="5%"> 22 </th>
    <th width="40%">' . _l('pdc_inhand') . '</th>
    <th width="55%">'.$legalapprove->pdc_inhand.'</th>
  </tr>
  <tr>
    <th width="5%"> 23 </th>
    <th width="40%">' . _l('out_dues') . '</th>
    <th width="55%">'.$legalapprove->out_dues.'</th>
  </tr>
  <tr>
    <th width="5%"> 24 </th>
    <th width="40%">' . _l('os_salesmonth') . '</th>
    <th width="55%">'.$legalapprove->os_salesmonth.'</th>
  </tr>
  <tr>
    <th width="5%"> 25 </th>
    <th width="40%">' . _l('holdchq_amount') . '</th>
    <th width="55%">'.$legalapprove->holdchq_amount.'</th>
  </tr>
  <tr>
    <th width="5%"> 26 </th>
    <th width="40%">' . _l('no_cheque_return') . '</th>
    <th width="55%">'.$legalapprove->no_cheque_return.'</th>
  </tr>
  <tr>
    <th width="5%"> 27 </th>
    <th width="40%">' . _l('no_cheque_hold') . '</th>
    <th width="55%">'.$legalapprove->actcredit_period.'</th>
  </tr>
  <tr>
    <th width="5%"> 28 </th>
    <th width="40%">' . _l('reasonforhold') . '</th>
    <th width="55%">'.$legalapprove->reasonforhold.'</th>
  </tr>
  <tr>
    <th width="5%"> 29 </th>
    <th width="40%">' . _l('holdpolice_civilcase') . '</th>
    <th width="55%">'.$legalapprove->holdpolice_civilcase.'</th>
  </tr>
  <tr>
    <th width="5%"> 30 </th>
    <th width="40%">' . _l('payment_nature') . '</th>
    <th width="55%">'.$legalapprove->payment_nature.'</th>
  </tr>
  <tr>
    <th width="5%"> 31 </th>
    <th width="40%">' . _l('sales_history') . '</th>
    <th width="55%">'.$legalapprove->sales_history.'</th>
  </tr>
  <tr><td colspan="3"><b><i>Note: PP- Passport copy, TL-Trade License copy, CC- Chember of Commerce Certificate copy</i></b></td></tr>';

	
if($legalapprove->approval!=null){
	$tblhtml .= '<tr  nobr="true"><td colspan="3"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr>';
foreach ($legalapprove->approval as  $approval) {
	$appstatus='';
	if($approval['approval_status']=='3') $appstatus=get_staff_full_name($approval['staffid']).'<br><br>'.date('d/m/Y',strtotime($approval['dateapproved'])).'<br> Approved';
  $tblhtml .='<td align="center">'.get_approval_heading_name_by_id($approval['approval_heading_id']).'<br><br>'.$appstatus.'<br></td>';
}


$tblhtml .= '</tr></tbody></table></td></tr>';
	}
$tblhtml.='</tbody></table>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>