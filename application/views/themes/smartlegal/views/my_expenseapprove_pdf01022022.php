<?php 
$dimensions = $pdf->getPageDimensions();

// Get Y position for the separation
$y = $pdf->getY();
$organizaion_info = pdf_logo_url();
$organizaion_info .= '<div style="color:#424242;">';
$organizaion_info .= format_organization_info();
$organizaion_info .= '</div><br><br>Date : '.date('Y-m-d');

$pdf->writeHTMLCell(($swap == '1' ? ($dimensions['wk']) - ($dimensions['lm'] * 2) : ($dimensions['wk'] / 2) - $dimensions['lm']), '', '', $y-4, $organizaion_info, 0, 0, false, true, ($swap == '1' ? 'R' : 'J'), true);
$pdf->Ln(34);
$tblhtml ='';

$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;"><u>Expense Statement</u></h3></div><h4>'.$expenseapprove->name.'</h4>';
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
    <th width="50%" style="text-align:center;">' .$expenseapprove->lastclaim .'</th>
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
    <th width="10%" style="text-align:center;"><b>'._l('total_amount').'</b></th>
    <th width="10%" style="text-align:center;"><b>'._l('paid_amount').'</b></th>
    <th width="10%" style="text-align:center;"><b>'._l('now_payable').'</b></th>
	 <th width="10%" style="text-align:center;"><b>'._l('vat').'</b></th>
    <th width="10%" style="text-align:center;"><b>'._l('bal_payable').'</b></th>
    <th width="35%" style="text-align:center;"><b>'._l('remarks').'</b></th>
  </tr></thead>';
$total_amount = $total_paid =$total_balance=$total_lastpaid=$tvat=0;
foreach ($expenseapprove->expenses as  $expenses) {
$bpaid=$expenses['paid_amount']-$expenses['last_amount'];
  $tblhtml .='<tr>';
  $tblhtml .= '<td width="15%">'.$expenses['category_name'].'</td>';
  $tblhtml .= '<td width="10%">'.$expenses['amount'].'</td>';
  $tblhtml .= '<td width="10%">'.$bpaid.'</td>';
  $tblhtml .= '<td width="10%">'.$expenses['last_amount'].'</td>';
  $tblhtml .= '<td width="10%">'.$expenses['vat_amount'].'</td>';
  $tblhtml .= '<td width="10%">'.$expenses['balance_amount'].'</td>';
  $tblhtml .= '<td width="35%">'.$expenses['note'].'</td>';
  $tblhtml .= '</tr>';

  $total_amount += $expenses['amount'];
  $total_paid += $expenses['paid_amount'];
  $total_lastpaid += $expenses['last_amount'];
  $total_balance += $expenses['balance_amount'];
	$tvat+=$expenses['vat_amount'];
}; 
$total_bpaid=$total_paid-$total_lastpaid;
$tblhtml .= '<tr>
                <td width="15%">Total Expenses</td>
                <td width="10%">'.number_format($total_amount,2).'</td>
                <td width="10%">'.number_format($total_bpaid,2).'</td>
                <td width="10%">'.number_format($total_lastpaid,2).'</td>
				 <td width="10%">'.number_format($tvat,2).'</td>
                <td width="10%">'.number_format($total_balance,2).'</td>
                <td width="35%"></td>
            </tr>';
  
$tblhtml .= '<tbody></tbody>';
$tblhtml .= '</table> <br /><br />&nbsp;&nbsp;&nbsp;&nbsp;<b>Total Now Payable :'.number_format(($total_lastpaid + $tvat),2).'<br></b>';
$tblhtml .= '<hr><br /><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">
<thead>';
foreach ($expenseapprove->approval as  $approval) {
	$apstatus='';$apdt='';
	if($approval['approval_status']==3)
	{
		$apstatus='Approved';
		$apdt=date('d-m-Y h:s',strtotime($approval['dateapproved']));
	}
 $tblhtml .= '<tr> 
    <th width="40%" style="text-align:center;">'._l($approval['approval_type']).'<br>'.get_staff_full_name($approval['staffid']).'</th>
    <th width="20%" style="text-align:center;">'.$apstatus.'<br>'.$apdt.'</th>
    <th width="40%" style="text-align:center;">'.$approval['approval_remarks'].'</th>  
  </tr>';
}

 $tblhtml .=' </thead><tbody></tbody></table>';

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>