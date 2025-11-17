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
    <th width="50%" style="text-align:center;"><b>' .$expenseapprove->lastclaim .'</b></th>
  </tr>
  <tr>
    <th width="50%" style="text-align:center;">' . _l('lawyer_name') . '</th>
    <th width="50%" style="text-align:center;">'.$expenseapprove->lawyername.'</th>
  </tr>';


$tblhtml .= '<tbody></tbody></table><br><br><br>';
$tblhtml .= '<table class="table" width="100%" bgcolor="#fff" cellspacing="0" cellpadding="8" border="1">
<thead>
 <tr>  
    <th width="20%" style="text-align:center;"><b>'._l('categories').'</b></th>
	<th width="20%" style="text-align:center;"><b>'._l('total_amount').'</b></th>
    <th width="20%" style="text-align:center;"><b>'._l('paid_amount').'</b></th>
    <th width="20%" style="text-align:center;"><b>'._l('now_payable').'</b></th>
	<th width="20%" style="text-align:center;"><b>'._l('bal_payable').'</b></th>
   
  </tr></thead>';
$total_amount = $total_paid =$total_balance=$total_lastpaid=$tvat=$tpay=0;
foreach ($expenseapprove->expenses as  $expenses) {
$bpaid=$expenses['amount']-($expenses['paid_amount']+$expenses['last_amount']+$expenses['vat_amount']);
	$npayable=$expenses['last_amount']+$expenses['vat_amount'];
  $tblhtml .='<tr>';
  $tblhtml .= '<td width="20%">'.$expenses['category_name'].'</td>';
	
  $tblhtml .= '<td width="20%">'.number_format($expenses['amount'],2).'</td>';
  $tblhtml .= '<td width="20%">'.number_format($expenses['paid_amount'],2).'</td>';
  $tblhtml .= '<td width="20%">'.number_format($npayable,2).'</td>';
  $tblhtml .= '<td width="20%">'.number_format($bpaid,2).'</td>';
 
  $tblhtml .= '</tr>';

  $total_amount += $expenses['amount'];
  $total_paid += $expenses['paid_amount'];
  $total_lastpaid += $expenses['last_amount'];
  $total_balance += $bpaid;
	$tvat+=$expenses['vat_amount'];
	
}; 
$tpay=$total_lastpaid+$tvat;
$total_bpaid=$total_paid-$total_lastpaid;
$tblhtml .= '<tr><td colspan="5"></td></tr><tr>
                <td width="20%"><b>Total Expenses</b></td>
				<td width="20%"><b>'.number_format($total_amount,2).'</b></td>
                <td width="20%"><b>'.number_format($total_paid,2).'</b></td>
                <td width="20%"><b>'.number_format($tpay,2).'</b></td>
				<td width="20%"><b>'.number_format($total_balance,2).'</b></td>
              
            </tr>';
  
$tblhtml .= '<tbody></tbody>';
$tblhtml .= '</table> <br />';
$tblhtml .= '<div style="text-align:center;"> <h3 style="font-size:20px;"><u>Expense Detailed  Statement</u></h3></div>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="8" border="1">
<thead>
 <tr>  
    <th width="15%" style="text-align:center;"><b>'._l('fees_charges').'</b></th>
	   <th width="12%" style="text-align:center;"><b>'._l('Date').'</b></th>
	   <th width="15%" style="text-align:center;"><b>'._l('reference_no').'</b></th>
    <th width="15%" style="text-align:center;"><b>'._l('total_amount').'</b></th>
    <th width="10%" style="text-align:center;"><b>'._l('paid_amount').'</b></th>
    <th width="15%" style="text-align:center;"><b>'._l('now_payable').'</b></th>
	<th width="20%" style="text-align:center;"><b>'._l('remarks').'</b></th>
  </tr></thead>';
$total_amount = $total_paid =$total_balance=$total_lastpaid=$tvat=0;
foreach ($expenseapprove->expensesall as  $expenses) {
$bpaid=$expenses['amount']-($expenses['paid_amount']+$expenses['last_amount']+$expenses['vat_amount']);
	$npayable=$expenses['last_amount']+$expenses['vat_amount'];
  $tblhtml .='<tr>';
  $tblhtml .= '<td width="15%">'.$expenses['category_name'].'</td>';
  $tblhtml .= '<td width="12%">'._d($expenses['date']).'</td>';
	$tblhtml .= '<td width="15%">'.$expenses['reference_no'].'</td>';
  $tblhtml .= '<td width="15%">'.number_format($expenses['amount'],2).'</td>';
  $tblhtml .= '<td width="10%">'.number_format($expenses['paid_amount'],2).'</td>';
  $tblhtml .= '<td width="15%">'.number_format($npayable,2).'</td>';
  $tblhtml .= '<td width="20%">'.$expenses['note'].'</td>';
  $tblhtml .= '</tr>';

  $total_amount += $expenses['amount'];
  $total_paid += $expenses['paid_amount'];
  $total_lastpaid += $expenses['last_amount'];
  $total_balance += $bpaid;
	$tvat+=$expenses['vat_amount'];
}; 
$tpay=$total_lastpaid+$tvat;
$total_bpaid=$total_paid-$total_lastpaid;
$tblhtml .= '<tr>
                <td width="27%" colspan="2"><b>Total Expenses</b></td>
				<td width="15%"></td>
                <td width="15%"><b>'.number_format($total_amount,2).'</b></td>
                <td width="10%"><b>'.number_format($total_paid,2).'</b></td>
                <td width="15%"><b>'.number_format($tpay,2).'</b></td>
				 <td width="20%"></td>
            </tr>';
  
$tblhtml .= '<tbody></tbody>';
$tblhtml .= '</table> <br /><br />';

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>