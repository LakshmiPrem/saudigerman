<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = '&nbsp;&nbsp;&nbsp;'.pdf_logo_url();
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br><br><br>';

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(34);
$tblhtml ='<br><br>&nbsp;&nbsp;&nbsp;';
$trade1='';
if($legalapprove->civils->trade_license=="yes") $trade1="Available";
$vat1='';
if($legalapprove->civils->vat_certificate=="yes") $vat1="Available";
$credit1='';
if($legalapprove->civils->credit_app=="yes") $credit1="Available";
$passport1='';
if($legalapprove->civils->passport_copy=="yes") $passport1="Available";
$invoice1='';
if($legalapprove->civils->sales_invoice=="yes") $invoice1="Available";
$do1='';
if($legalapprove->civils->sales_do=="yes") $do1="Available";
$lpo1='';
if($legalapprove->civils->sales_lpo=="yes") $lpo1="Available";
$balc1='';
if($legalapprove->civils->sales_balconfirm=="yes") $balc1="Available";

$tblhtml .= '<table width="100%"><thead></thead><tbody><tr><td width="70%">&nbsp;&nbsp;&nbsp;Date : '._dt(date('Y-m-d H:i:s')).'</td><td><b>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'</b></td></tr></tbody></table>';
$tblhtml .= '<br><div style="text-align:center;"> <h3 style="font-size:20px;">Request For Filing Civil Case </h3></div>';
//$tblhtml .= '<br> <span style="font-size:16px;text-transform: capitalize;">'.$legalapprove->message.'</span><br><br>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1"><thead></thead><tbody>
 <tr>
    <th width="40%">' . _l('request_from') . '</th>
    <th width="60%">' . $legalapprove->company . '</th>
  </tr> 
 <tr>  
    <th width="40%" >'._l('nameof_salesexecutive').'</th>
    <th width="60%">'._l($legalapprove->firstname).'</th>
   </tr>
     <tr>
    <th width="40%">' . _l('client_name') . '</th>
    <th width="60%">' . $legalapprove->opposteparty.'</th>
  	</tr>
	  <tr>
    <th width="40%">' . _l('customer_code') . '</th>
    <th width="60%">' . $legalapprove->customer_code.'</th>
  </tr>
  <tr>
    <th width="40%">' . _l('typeof_business') . '</th>
    <th width="60%">' . $legalapprove->civils->typeof_business.'</th>
  </tr>
  <tr>
    <th width="40%">' . _l('typeof_liscence') . '</th>
    <th width="60%" >' . $legalapprove->civils->typeof_liscence.'</th>
  </tr>
   <tr>
    <th width="40%">' . _l('company') . '</th>
    <th width="60%">' . $legalapprove->civils->company.'</th>
  </tr>
   <tr>
    <th width="40%" >' . _l('company_docs') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('trade_license').'</td><td>'._l('vat_certificate').'</td><td>'._l('credit_application').'</td><td>'._l('passport_copy').'</td></tr>
	<tr><td>'.$trade1.'</td><td>'.$vat1.'</td><td>'.$credit1.'</td><td>'.$passport1.'</td></tr>
	</tbody></table></th>
  </tr>
   
  <tr>
    <th width="40%" >' . _l('credit_terms') . '</th>
    <th width="60%">'._l('current_credit_appamount').' - ' . $legalapprove->civils->current_credit_appamount.'<br>'._l('current_credit_days').' - ' . $legalapprove->civils->current_credit_days.'</th>
  </tr>
  <tr>
    <th width="40%" >' . _l('total_outstanding_amount') . '</th>
    <th width="60%">' . $legalapprove->civils->total_outstanding_amount.'</th>
  </tr>
  <tr>
    <th width="40%">' . _l('reason_civil_case') . '</th>
    <th width="60%">' . $legalapprove->civils->civil_case_reason.'</th>
  </tr>
   <tr>
    <th width="40%" >' . _l('dues') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5"><thead></thead><tbody>
	<tr><td>'._l('due_amount').'</td><td>'._l('due_date').'</td><td>'._l('due_days').'</td></tr>
	<tr><td>'.$legalapprove->civils->due_amount.'</td><td>'._d($legalapprove->civils->due_date).'</td><td>'.$legalapprove->civils->due_days.'</td></tr>
	</tbody></table></th>
  </tr>
  <tr>
    <th width="40%" >' . _l('sales_document_status') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('invoices').'</td><td>'._l('do').'</td><td>'._l('lpo').'</td><td>'._l('balance_confirmation').'</td></tr>
	<tr><td>'.$invoice1.'</td><td>'.$do1.'</td><td>'.$lpo1.'</td><td>'.$balc1.'</td></tr>
	</tbody></table></th>
  </tr>
    <tr>
    <th width="40%" >' . _l('det_return_cheque') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('nameof_bank').'</td><td>'._l('chequeno').'</td><td>'._l('cheque_amount').'</td><td>'._l('partial_payment').'</td></tr>
	<tr><td>'.$legalapprove->civils->nameof_bank.'</td><td>'.$legalapprove->civils->chequeno.'</td><td>'.$legalapprove->civils->cheque_amount.'</td><td>'.$legalapprove->civils->partial_payment.'</td></tr>
	<tr><td>'._l('balance').'</td><td>'._l('dateon_cheque').'</td><td colspan="2">'._l('cheque_return').'</td></tr>
	<tr><td>'.$legalapprove->civils->balance.'</td><td>'.$legalapprove->civils->dateon_cheque.'</td><td  colspan="2">'.$legalapprove->civils->cheque_return.'</td></tr>
	</tbody></table></th>
  </tr>
   <tr>
    <th width="40%" >' . _l('det_pdc_hand') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('pdc_bank').'</td><td>'._l('pdc_chequeno').'</td><td>'._l('pdccheque_amount').'</td><td>'._l('pdc_dateon_cheque').'</td></tr>
	<tr><td>'.$legalapprove->civils->pdc_bank.'</td><td>'.$legalapprove->civils->pdc_chequeno.'</td><td>'.$legalapprove->civils->pdccheque_amount.'</td><td>'._d($legalapprove->civils->pdc_dateon_cheque).'</td></tr>
	</tbody></table></th>
  </tr>
   <tr>
    <th width="40%" >' . _l('amt_civil_case') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('returncheque_amount').'</td><td>'._l('outstandingamount').'</td><td>'._l('totalamount').'</td></tr>
	<tr><td>'.$legalapprove->civils->returncheque_amount.'</td><td>'.$legalapprove->civils->outstandingamount.'</td><td>'.$legalapprove->civils->totalamount.'</td></tr>
	</tbody></table></th>
  </tr>
  <tr>
    <th width="40%" >' . _l('guarantee_chequedet') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('quarante_chequeno').'</td><td>'._l('guarantee_amount').'</td><td>'._l('quarantee_date').'</td></tr>
	<tr><td>'.$legalapprove->civils->guarantee_chequeno.'</td><td>'.$legalapprove->civils->guarantee_amount.'</td><td>'.$legalapprove->civils->quarantee_date.'</td></tr>
	</tbody></table></th>
  </tr>
   <tr>
    <th width="40%" >' . _l('owner_signat_det') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('owner_name').'</td><td>'._l('passportno_owner').'</td><td>'._l('nationality_owner').'</td><td>'._l('owner_status').'</td></tr>
	<tr><td>'.$legalapprove->civils->owner_name.'</td><td>'.$legalapprove->civils->passportno_owner.'<br>'.$legalapprove->civils->passport_expdt.'</td><td>'.$legalapprove->civils->nationality_owner.'</td><td>'.$legalapprove->civils->owner_status.'</td></tr>
	<tr><td>'._l('emirates_owner').'</td><td>'._l('email').'</td><td>'._l('contact1').'</td><td>'._l('contact2').'</td></tr>
	<tr><td>'.$legalapprove->civils->emirates_owner.'<br>'._d($legalapprove->civils->emirates_expdt).'</td><td>'.$legalapprove->civils->owner_email.'</td><td>'.$legalapprove->civils->owner_contact1.'</td><td>'.$legalapprove->civils->owner_contact2.'</td></tr>
	<tr><td colspan="2">'._l('owner_address').'</td><td colspan="2">'._l('home_contact').'</td></tr>
	<tr><td colspan="2">'.$legalapprove->civils->owner_address.'</td><td colspan="2">'.$legalapprove->civils->home_contact.'</td></tr>
	</tbody></table></th>
  </tr>
  <tr>
    <th width="40%">' . _l('makani_land') . '</th>
    <th width="60%">' . $legalapprove->civils->makani_land.'</th>
  </tr>
  <tr>
    <th width="40%">' . _l('location_map') . '</th>
    <th width="60%">' . $legalapprove->civils->location_map.'</th>
  </tr>
   
  <tr>
    <th width="40%" style="text-align:center;">' . _l('company_status') . ' (Present) </th>
    <th width="60%" style="text-align:center;"><span style="font-size:16px;text-transform: capitalize;">' . $legalapprove->civils->company_status.'</span></th>
  </tr>
  <tr>
    <th width="40%">' . _l('remarks') . '</th>
    <th width="60%">' . $legalapprove->civils->remarks.'</th>
  </tr>
  <tr>
    <th width="40%" >' . _l('previous_case_typedet') . '</th>
    <th width="60%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('previous_case_type').'</td><td>'._l('case_no').'</td><td>'._l('case_date').'</td></tr>
	<tr><td>'.$legalapprove->civils->previous_case_type.'</td><td>'.$legalapprove->civils->case_no.'</td><td>'._d($legalapprove->civils->case_date).'</td></tr>
	</tbody></table></th>
  </tr>';
if($legalapprove->approval!=null){
	$tblhtml .= '<tr><td colspan="2"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr>';
foreach ($legalapprove->approval as  $approval) {
	$appstatus='';
	if($approval['approval_status']=='3') $appstatus='Approved';
  $tblhtml .='<td align="center">'._l($approval['approval_type']).'<br><br>'.get_staff_full_name($approval['staffid']).'<br><br>'.date('d/m/Y',strtotime($approval['dateadded'])).'<br>'.$appstatus.'<br></td>';
}

$tblhtml .= '</tr></tbody></table></td></tr>';
	}
$tblhtml.='</tbody></table>';

/*$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;">Approval</h3></div><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="15" border="1">
<thead>';

foreach ($legalapprove->approval as  $approval) {

 $tblhtml .= '<tr> 
    <th width="40%" style="text-align:center;">'._l($approval['approval_type']).'</th>
    <th width="20%" style="text-align:center;">Approved <br> <img src="'.base_url('assets/images/checked.jpg').'" style="width:50px;height:50px;" /></th>
    <th width="40%" style="text-align:center;">'.get_staff_full_name($approval['staffid']).'<br>'.date('Y-m-d',strtotime($approval['dateadded'])).'<br>(Remark : '.$approval['approval_remarks'].'.)</th>
  </tr>';
}

 $tblhtml .=' </thead><tbody></tbody></table>';
//print_r($legaltask);
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