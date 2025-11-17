<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = '&nbsp;&nbsp;&nbsp; To <br><br><b>&nbsp;&nbsp;&nbsp;The Chairman';
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br><br>';

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(34);

$tblhtml ='<br>&nbsp;&nbsp;&nbsp;';
$tdue=0;
$tret1=0;$tret2=0;$tret3=0;
$tsale1=0;
$tchq1=0;
$trade1='Not Available';
if($legalapprove->civils->trade_license=="yes") $trade1="Available";
$vat1='Not Available';
if($legalapprove->civils->vat_certificate=="yes") $vat1="Available";
$credit1='Not Available';
if($legalapprove->civils->credit_app=="yes") $credit1="Available";
$passport1='Not Available';
if($legalapprove->civils->passport_copy=="yes") $passport1="Available";
$invoice1='Not Available';
if($legalapprove->civils->sales_invoice=="yes") $invoice1="Available";
$do1='Not Available';
if($legalapprove->civils->sales_do=="yes") $do1="Available";
$lpo1='Not Available';
if($legalapprove->civils->sales_lpo=="yes") $lpo1="Available";
$balc1='Not Available';
if($legalapprove->civils->sales_balconfirm=="yes") $balc1="Available";

$tblhtml .= '<table width="100%"><thead></thead><tbody><tr><td width="70%">&nbsp;&nbsp;&nbsp;</td><td><b>Date : '._dt($legalapprove->bmapproval).'<br>Reference No:&nbsp;&nbsp;'.$legalapprove->request_no.'</b></td></tr></tbody></table>';
$tblhtml .= '<br><div style="text-align:center;"> <h3 style="font-size:20px;">Request For Filing Civil Case </h3></div>';
//$tblhtml .= '<br> <span style="font-size:16px;text-transform: capitalize;">'.$legalapprove->message.'</span><br><br>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1"><thead></thead><tbody>
 <tr>
    <th width="35%">' . _l('request_from') . '</th>
    <th width="65%">' . $legalapprove->company . '</th>
  </tr> 
    <tr>
    <th width="35%">' . _l('client_name') . '</th>
    <th width="65%">' . $legalapprove->opposteparty.'</th>
  	</tr>
	  <tr>
    <th width="35%">' . _l('customer_code') . '</th>
    <th width="65%">' . $legalapprove->customer_code.'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('sales_executive') . '</th>
    <th width="65%">' . $legalapprove->civils->sales_executive.'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('typeof_business') . '</th>
    <th width="65%">' . $legalapprove->civils->typeof_business.'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('typeof_liscence') . '</th>
    <th width="65%" >' . $legalapprove->civils->typeof_liscence.'</th>
  </tr>
   <tr>
    <th width="35%">' . _l('company') . '</th>
    <th width="65%">' . $legalapprove->civils->company.'</th>
  </tr>
   <tr>
    <th width="35%" >' . _l('company_docs') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('trade_license').'</td><td>'._l('vat_certificate').'</td><td>'._l('credit_application').'</td><td>'._l('passport_copy').'</td></tr>
	<tr><td>'.$trade1.'</td><td>'.$vat1.'</td><td>'.$credit1.'</td><td>'.$passport1.'</td></tr>
	</tbody></table></th>
  </tr>
   
  <tr>
    <th width="35%" >' . _l('credit_terms') . '</th>
    <th width="65%">'._l('current_credit_appamount').' - ' . $legalapprove->civils->current_credit_appamount.'<br>'._l('current_credit_days').' - ' . $legalapprove->civils->current_credit_days.'</th>
  </tr>
  <tr>
    <th width="35%" >' . _l('total_outstanding_amount') . '</th>
    <th width="65%">' . $legalapprove->civils->total_outstanding_amount.'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('reason_civil_case') . '</th>
    <th width="65%">' . $legalapprove->civils->civil_case_reason.'</th>
  </tr>
   <tr  nobr="true">
    <th width="35%" >' . _l('dues') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('due_date').'</td><td>'._l('due_amount').'</td><td>'._l('due_days').'</td></tr>';
	if($legalapprove->civils->overdue_detail!=''){
						$overdue=json_decode($legalapprove->civils->overdue_detail,true);
							$limit=sizeof($overdue['due_date']);
							for($i=0;$i<$limit;$i++) {
	$tblhtml.='<tr><td>'._d($overdue['due_date'][$i]).'</td><td>'.$overdue['due_amount'][$i].'</td><td>'.$overdue['due_days'][$i].'</td></tr>';
								$tdue+=$overdue['due_amount'][$i];
							}
	}
	$tblhtml.='<tr><td><b>Total </b></td><td><b>'.$tdue.'</b></td><td></td></tr></tbody></table></th>
  </tr>
  <tr>
    <th width="35%" >' . _l('sales_document_status') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('invoices').'</td><td>'._l('do').'</td><td>'._l('lpo').'</td><td>'._l('balance_confirmation').'</td></tr>
	<tr><td>'.$invoice1.'</td><td>'.$do1.'</td><td>'.$lpo1.'</td><td>'.$balc1.'</td></tr>
	</tbody></table></th>
  </tr>
      <tr  nobr="true">
    <th width="35%" >' . _l('det_return_cheque') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('chequeno').'</td><td>'._l('cheque_amount').'</td><td>'._l('partial_payment').'</td><td>'._l('balance').'</td><td>'._l('dateon_cheque').'</td></tr>';
	if($legalapprove->civils->return_chq_details!=''){
						$retcheque=json_decode($legalapprove->civils->return_chq_details,true);
							$limit=sizeof($retcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {
	$tblhtml.='<tr><td>'.$retcheque['chequeno'][$i].'</td><td>'.$retcheque['cheque_amount'][$i].'</td><td>'.$retcheque['partial_payment'][$i].'</td><td>'.$retcheque['balance'][$i].'</td><td>'._d($retcheque['dateon_cheque'][$i]).'</td></tr>';
								     if($retcheque['cheque_amount'][$i]!=''){
                $tret1+=$retcheque['cheque_amount'][$i];
              } if($retcheque['partial_payment'][$i]!=''){
                $tret2+=$retcheque['partial_payment'][$i];
              }
               if($retcheque['balance'][$i]!=''){
                $tret3+=$retcheque['balance'][$i];
              }
							}
	}
$tblhtml.='<tr><td><b>Total </b></td><td><b>'.$tret1.'</b></td><td><b>'.$tret2.'</b></td><td><b>'.$tret3.'</b></td><td></td></tr></tbody></table></th>
	
  </tr>
    <tr  nobr="true">
    <th width="35%" >' . _l('add_details_of_return_cheque') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('chequeno').'</td><td>'._l('returncheque_amount').'</td><td>'._l('days_sale').'</td><td>'._l('days_return').'</td><td>'._l('remarks').'</td></tr>';
	if($legalapprove->civils->addreturn_chq_details!=''){
						$addretcheque=json_decode($legalapprove->civils->addreturn_chq_details,true);
							$limit=sizeof($addretcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {
	$tblhtml.='<tr><td>'.$addretcheque['chequeno'][$i].'</td><td>'.$addretcheque['cheque_amount'][$i].'</td><td>'.$addretcheque['days_sale'][$i].'</td><td>'.$addretcheque['days_return'][$i].'</td><td>'.$addretcheque['remark'][$i].'</td></tr>';
							}
	}
	$tblhtml.='</tbody></table></th>
  </tr>
    <tr  nobr="true">
    <th width="35%" >' . _l('det_pdc_hand') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('chequeno').'</td><td>'._l('cheque_amount').'</td><td>'._l('dateon_cheque').'</td><td>'._l('sale_date').'</td></tr>';
	if($legalapprove->civils->pdc_in_hand!=''){
						$pdccheque=json_decode($legalapprove->civils->pdc_in_hand,true);
							$limit=sizeof($pdccheque['chequeno']);
							
							for($i=0;$i<$limit;$i++) {
	$tblhtml.='<tr><td>'.$pdccheque['chequeno'][$i].'</td><td>'.$pdccheque['cheque_amount'][$i].'</td><td>'._d($pdccheque['dateon_cheque'][$i]).'</td><td>'._d($pdccheque['sale_date'][$i]).'</td></tr>';
								if($pdccheque['cheque_amount'][$i]!=''){
								$tsale1+=$pdccheque['cheque_amount'][$i];
								}
							}
	}
	$tblhtml.='<tr><td><b>Total </b></td><td><b>'.$tsale1.'</b></td><td></td><td></td></tr></tbody></table>
  </th>
	
  </tr>
   <tr>
    <th width="35%" >' . _l('amt_civil_case') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('returncheque_amount').'</td><td>'._l('outstanding_amount').'</td><td>'._l('total_amount').'</td></tr>
	<tr><td>'.$legalapprove->civils->returncheque_amount.'</td><td>'.$legalapprove->civils->outstandingamount.'</td><td>'.$legalapprove->civils->totalamount.'</td></tr>
	</tbody></table></th>
  </tr>
  <tr  nobr="true">
    <th width="35%" >' . _l('guarantee_chequedet') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('chequeno').'</td><td>'._l('cheque_amount').'</td><td>'._l('nameof_bank').'</td></tr>';
	if($legalapprove->civils->guarantee_chequedet!=''){
		
				$gcheque=json_decode($legalapprove->civils->guarantee_chequedet,true);
		$limit=sizeof($gcheque['chequeno']);
							
for($i=0;$i<$limit;$i++) {
							
	$tblhtml.='<tr><td>'.$gcheque['chequeno'][$i].'</td><td>'.$gcheque['cheque_amount'][$i].'</td><td>'.$gcheque['cheque_bank'][$i].'</td></tr>';
	  if($gcheque['cheque_amount'][$i]!=''){
		$tchq1+=$gcheque['cheque_amount'][$i];	
	  }
	}}
	$tblhtml.='<tr><td><b>Total </b></td><td><b>'.$tchq1.'</b></td><td></td></tr></tbody></table></th>
  </tr>
  <tr  nobr="true">
    <th width="35%" >' . _l('owner_signat_det') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('owner_name').'</td><td>'._l('emirates_owner').'</td><td>'._l('contact11').'</td><td>'._l('contact21').'</td></tr>';
	if($legalapprove->civils->owner_detail!=''){
						$ownerdet=json_decode($legalapprove->civils->owner_detail,true);
		
							$limit=sizeof($ownerdet['owner_name']);
							
							for($i=0;$i<$limit;$i++) {
	$tblhtml.='<tr><td>'.$ownerdet['owner_name'][$i].'<br>'.$ownerdet['nationality'][$i].'<br>'.$ownerdet['ownstatus'][$i].'</td><td>'.$ownerdet['emirates'][$i].'<br>'.$ownerdet['passport'][$i].'<br>'.$ownerdet['email'][$i].'</td><td>'.$ownerdet['contact1'][$i].'</td><td>'.$ownerdet['contact2'][$i].'</td></tr>';
							}
	}
	$tblhtml.='</tbody></table></th>
  </tr>
  <tr>
    <th width="35%">' . _l('makani_land') . '</th>
    <th width="65%">' . $legalapprove->civils->makani_land.'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('location_map') . '</th>
    <th width="65%">' . $legalapprove->civils->location_map.'</th>
  </tr>
   
  <tr>
    <th width="35%">' . _l('company_status1') . ' (Present) </th>
    <th width="65%"><span style="font-size:16px;text-transform: capitalize;">' . $legalapprove->civils->company_status.'</span></th>
  </tr>
  <tr>
    <th width="35%">' . _l('asset_detail') . '  </th>
    <th width="65%"><span style="font-size:16px;text-transform: capitalize;">' . $legalapprove->civils->asset_detail.'</span></th>
  </tr>
  <tr>
    <th width="35%">' . _l('remarks') . '</th>
    <th width="65%">' . $legalapprove->civils->remarks.'</th>
  </tr>
  <tr  nobr="true">
    <th width="35%" >' . _l('previous_case_typedet') . '</th>
    <th width="65%"><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1"><thead></thead><tbody>
	<tr><td>'._l('previous_case_type').'</td><td>'._l('case_no').'</td><td>'._l('case_date').'</td></tr>
	<tr><td>'.$legalapprove->civils->previous_case_type.'</td><td>'.$legalapprove->civils->case_no.'</td><td>'._d($legalapprove->civils->case_date).'</td></tr>
	</tbody></table></th>
  </tr>
  <tr>
    <th width="35%">' . _l('court_nature') . '</th>
    <th width="65%">' . $legalapprove->civils->court_nature.'</th>
  </tr>
  <tr>
    <th width="35%">' . _l('lawyer_name') . '</th>
    <th width="65%">' . $legalapprove->civils->lawyer_name.'</th>
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