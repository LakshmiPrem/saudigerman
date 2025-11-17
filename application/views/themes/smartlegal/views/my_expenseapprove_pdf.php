<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = pdf_logo_url();
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br><br>Date : '._dt(date('Y-m-d H:i:s'));

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(34);
$tblhtml ='';

$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;"><u>Expense Statement</u></h3></div><h4>'.$expenseapprove->name.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$expenseapprove->referenceno.'</h4>';
$tblhtml .= '<table class="table" width="100%" bgcolor="#fff" cellspacing="0" cellpadding="8" border="1">

 <tr>  
    <th width="50%" style="text-align:center;">'._l('name_of_company').'</th>
    <th width="50%" style="text-align:center;">'.get_opposite_party_name($expenseapprove->opposite_party).'</th>
   </tr>
    <tr>  
    <th width="50%" style="text-align:center;">'._l('date_of_filing').'</th>
    <th width="50%" style="text-align:center;">'._d($expenseapprove->start_date).'</th>
   </tr>
 <tr>
    <th width="50%" style="text-align:center;">' . _l('branch') . '</th>
    <th width="50%" style="text-align:center;">' . get_company_name($expenseapprove->clientid) . '</th>
  </tr> 
  <tr>
    <th width="50%" style="text-align:center;">' . _l('file_no') . '</th>
    <th width="50%" style="text-align:center;">'.$expenseapprove->file_no.'</th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('claiming_amount') . '</th>
    <th width="50%" style="text-align:center;"><b>' .$expenseapprove->lastclaim .'</b></th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('lawyer_name') . '</th>
    <th width="50%" style="text-align:center;">'.$expenseapprove->lawyername.'</th>
  </tr>';


$tblhtml .= '<tbody></tbody></table><br><br>';
$tblhtml .= '<table class="table" width="100%" bgcolor="#fff" cellspacing="0" cellpadding="8" border="1">
<thead>
 <tr>  
    <th width="15%" style="text-align:center;"><b>'._l('fees_charges').'</b></th>
    <th width="11%" style="text-align:center;"><b>'._l('total_amount').'</b></th>
    <th width="10%" style="text-align:center;"><b>'._l('paid_amount').'</b></th>
    <th width="10%" style="text-align:center;"><b>'._l('now_payable').'</b></th>
	 <th width="8%" style="text-align:center;"><b>'._l('vat').'</b></th>
    <th width="11%" style="text-align:center;"><b>'._l('bal_payable').'</b></th>
    <th width="35%" style="text-align:center;"><b>'._l('remarks').'</b></th>
  </tr></thead>';
$total_amount = $total_paid =$total_balance=$total_lastpaid=$tvat=0;
foreach ($expenseapprove->expenses as  $expenses) {
$bpaid=$expenses['amount']-($expenses['paid_amount']+$expenses['last_amount']+$expenses['vat_amount']);
  $tblhtml .='<tr>';
  $tblhtml .= '<td width="15%">'.$expenses['category_name'].'</td>';
  $tblhtml .= '<td width="11%">'.$expenses['amount'].'</td>';
  $tblhtml .= '<td width="10%">'.$expenses['paid_amount'].'</td>';
  $tblhtml .= '<td width="10%">'.$expenses['last_amount'].'</td>';
  $tblhtml .= '<td width="8%">'.$expenses['vat_amount'].'</td>';
  $tblhtml .= '<td width="11%">'.number_format($bpaid,2).'</td>';
  $tblhtml .= '<td width="35%">'.$expenses['note'].'</td>';
  $tblhtml .= '</tr>';

  $total_amount += $expenses['amount'];
  $total_paid += $expenses['paid_amount'];
  $total_lastpaid += $expenses['last_amount'];
  $total_balance += $bpaid;
	$tvat+=$expenses['vat_amount'];
}; 
$total_bpaid=$total_paid-$total_lastpaid;
$tblhtml .= '<tr>
                <td width="15%"><b>Total Expenses</b></td>
                <td width="11%"><b>'.number_format($total_amount,2).'</b></td>
                <td width="10%"><b>'.number_format($total_paid,2).'</b></td>
                <td width="10%"><b>'.number_format($total_lastpaid,2).'</b></td>
				 <td width="8%"><b>'.number_format($tvat,2).'</b></td>
                <td width="11%"><b>'.number_format($total_balance,2).'</b></td>
                <td width="35%"></td>
            </tr>';
  
$tblhtml .= '<tbody></tbody>';
$tblhtml .= '</table> <br/><br/><table  width="50%" bgcolor="#fff" cellspacing="0" cellpadding="7" border="2"><tr><td><h3>Total Now Payable</h3></td><td style="text-align:center;"><h3>'.number_format(($total_lastpaid + $tvat),2).'</h3></td></tr></table><br/>';
$tblhtml .= '<br><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">
<thead>';
foreach ($expenseapprove->approval as  $approval) {
	$apstatus='';$apdt='';
	if($approval['approval_status']==3)
	{
		$apstatus='Approved';
		$apdt=_dt($approval['dateapproved']);
	}
 $tblhtml .= '<tr> 
    <th width="40%" style="text-align:center;">'._l($approval['approval_type']).'<br>'.get_staff_full_name($approval['staffid']).'<br></th>
    <th width="20%" style="text-align:center;">'.$apstatus.'<br>'.$apdt.'</th>
    <th width="40%" style="text-align:center;">'.$approval['approval_remarks'].'</th>  
  </tr>';
}

 $tblhtml .=' </thead><tbody></tbody></table>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>