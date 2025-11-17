<?php



defined('BASEPATH') or exit('No direct script access allowed');


$pdf->SetFont('dejavusans', '', 10);
$dimensions    = $pdf->getPageDimensions();

$custom_fields = get_custom_fields('projects');



// Like heading project name

$html = '<h1>' . _l('project_name') . ': ' . $project->name . '</h1>';

// project overview heading

$html .= '<h3>' . ucwords(_l('project_overview')) . '</h3>';



if (!empty($project->description)) {

    // Project description

    $html .= '<p><b style="background-color:#f0f0f0;">' . _l('project_description') . '</b><br /><br /> ' . $project->description . '</p>';

}



$pdf->writeHTML($html, true, false, false, false, '');

$pdf->Ln(10);



$html = '';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';

$html .= '<thead>';

$html .= '<tr  bgcolor="#323a45" style="color:#ffffff;">';

$html .= '<th align="center">';



$html .= '<b style="font-size:20px;">' . _l('project_overview') . '</b>';



$html .= '</th>';



$html .= '<th align="center">';



$html .= '<b style="font-size:20px;">' . ucwords(_l('finance_overview')) . '</b>';



$html .= '</th>';



if (count($custom_fields) > 0) {

    $html .= '<th align="center">';

    $html .= '<b style="font-size:20px;">' . ucwords(_l('project_custom_fields')) . '</b>';

    $html .= '</th>';

}



$html .= '<th align="center">';

$html .= '<b style="font-size:20px;">' . ucwords(_l('project_customer1')) . '</b>';

$html .= '</th>';



$html .= '</tr>';



$html .= '</thead>';

$html .= '<tbody>';



$html .= '<tr>';



$html .= '<td><br /><br />';

 $casetypes=get_case_client_types();

												  foreach($casetypes as $case1){

													  if($case1['id']==$project->case_type){

														$type1=$case1['id'];  

													  }

													  

												  }







$status = get_project_status_by_id($project->status);

// Project status

$html.='<table><thead></thead><tbody>';

$html .= '<tr><td><b>' . _l('case_type') . ': </b></td><td>' . _l($type1) . '<br /></td></tr>';

$html .= '<tr><td><b>' . _l('project_status') . ': </b></td><td>' . $status['name'] . '<br/></td></tr>';

// Date created

$html .= '<tr><td><b>' . _l('legal_requestdt') . ': </b></td><td>' . _d($project->project_created) . '<br /></td></tr>';

// Start date

$html .= '<tr><td><b>' . _l('project_start_date') . ': </b></td><td>' . _d($project->start_date) . '<br /></td></tr>';



// Total members

$html .= '<tr><td><b>' . _l('lawyer_attending') . ': </b></td><td>';

if(count($asslawyers) == 0){

     $html.='<p class="text-muted no-mbot mtop15">' . _l('no_description_projectlawyer') . '</p>';

   }

	 foreach($asslawyers as $plawyer){

		 $html.= '<b>'.get_staff_full_name($plawyer['assigneeid']).'</b><br>';

	   }

// Total files

$html .= '</td></tr><tr><td><b>' . _l('legal_coordinator') . ': </b></td><td>';

if(count($legals) == 0){

     $html.= '<p class="text-muted no-mbot mtop15">' . _l('no_description_projectlegal') . '</p>';

   }

	  

	 foreach($legals as $plegal){

		   $html.= '<b>'.get_staff_full_name($plegal['legal_ids']).'</b><br>';

	   }

$html .= '</td></tr>';



$html .= '</tbody></table>';

  $html .= '</td><td>';

$html.='<table><thead></thead><tbody>';

$html .= '<tr><td><b>' . _l('claiming_amount') . ' </b></td><td>' . app_format_money($project->claiming_amount, $project->currency_data) . '<br /></td></tr>';

// Not paid invoices total

$html .= '<tr><td><b>' . _l('project_overview_expenses') . ' </b></td><td>' . app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id], 'field' => 'paid_amount']), $project->currency_data) . '<br /></td></tr>';

// Due invoices total

$exp=sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id], 'field' => 'paid_amount']);

$tt=$project->claiming_amount+$exp;

$tamount=app_format_money($tt, $project->currency_data);

$html .= '<tr><td><b>' . _l('total_amount') . ' </b></td><td>' . $tamount . '<br /></td></tr>';

// Paid invoices

$html .= '<tr><td><b>' . _l('execution_amount') . ' </b></td><td>' . app_format_money($project->execution_amount, $project->currency_data) . '<br /></td></tr>';



// Total expenses + money

$html .= '<tr><td><b>' . _l('settlement_amount') . ': </b></td><td>' . app_format_money($project->outstanding_amount, $project->currency_data). '<br /></td></tr>';

// Billable expenses + money

$html .= '<tr><td><b>' . _l('project_overview_settle_paid') . ':</b></td><td>' .

	app_format_money(sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status'=>'paid'),'field'=>'amount_received'))+sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'partially_paid'),'field'=>'amount_received')), $project->currency_data) . '<br /></td></tr>';

// Billed expenses + money

  $total=$project->claiming_amount+sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount'));

			$paid1=sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'paid'),'field'=>'amount_received'));

		 $paid2=sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'partially_paid'),'field'=>'amount_received'));

												  $bal=$total-($paid1+$paid2);

$html .= '<tr><td><b>' . _l('project_overview_settle_balance') . ': </b></td><td>' . app_format_money($bal, $project->currency_data) . '<br /></td></tr>';



// Custom fields

if (count($custom_fields) > 0) {

    $html .= '<tr><td></td><td><br /><br />';

    foreach ($custom_fields as $field) {

        $value = get_custom_field_value($project->id, $field['id'], 'projects');

        $value = $value === '' ? '/' : $value;

        $html .= '<b>' . ucfirst($field['name']) . ': </b>' . $value . '<br />';

    }



    $html .= '</td></tr>';

}

$html .= '</tbody></table><br /><br /></td>';

// Customer Info_l('casediary_oppositeparty')

$html .= '<td><br /><br />';

$html.= '&nbsp;&nbsp;&nbsp;<b>'. get_opposite_party_name($project->opposite_party). ' </b><br /><br/>';

$html.= '&nbsp;&nbsp;&nbsp;<b>'._l('ledger_code'). ': </b>' . $project->ledger_code. '<br /><br/>';

										 

$html .= '&nbsp;&nbsp;&nbsp;<b>' . $project->client_data->company . '</b><br />&nbsp;&nbsp;&nbsp;';

$html .= $project->client_data->address . '<br />&nbsp;&nbsp;&nbsp;';



if (!empty($project->client_data->city)) {

    $html .= $project->client_data->city;

}



if (!empty($project->client_data->state)) {

    $html .= ', ' . $project->client_data->state;

}



$country = get_country_short_name($project->client_data->country);



if (!empty($country)) {

    $html .= '<br />&nbsp;&nbsp;&nbsp;' . $country;

}



if (!empty($project->client_data->zip)) {

    $html .= ', ' . $project->client_data->zip;

}



if (!empty($project->client_data->phonenumber)) {

    $html .= '<br />&nbsp;&nbsp;&nbsp;' . $project->client_data->phonenumber;

}



if (!empty($project->client_data->vat)) {

    $html .= '<br />&nbsp;&nbsp;&nbsp;' . _l('client_vat_number') . ': ' . $project->client_data->vat;

}



$html .= '</td></tr>';





$html .= '</tbody>';

$html .= '</table>';



$pdf->writeHTML($html, true, false, false, false, '');



$pdf->ln(5);



// Court instance overview

$html = '';

// Heading

$html .= '<p><b style="background-color:#f0f0f0;font-size:12;font-weight:bold;">' . ucwords(_l('court_instance_details')) . '</b></p>';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';

$html .= '<thead>';

$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';

$html .= '<th width="20%"><b>' . _l('court_instance') . '</b></th>';

$html .= '<th width="10%"><b>' . _l('case_no') . '</b></th>';

$html .= '<th width="20%"><b>' . _l('case_nature') . '</b></th>';

$html .= '<th width="20%"><b>' . _l('hearing_court') . '</b></th>';

$html .= '<th width="15%"><b>' . _l('claiming_amount') . '</b></th>';

$html .= '<th width="15%"><b>' . _l('judgement_amount') . '</b></th>';



$html .= '</tr>';

$html .= '</thead>';

$html .= '<tbody>';

 foreach ($court_instances as $row_) {

    $html .= '<tr style="color:#4a4a4a;">';

    $html .= '<td width="20%">' . $row_['instance_name'] . '</td>';

    $html .= '<td  width="10%">' .$row_['case_number'] . '</td>';

    $html .= '<td width="20%">' . $row_['case_nature_name'] . '</td>';

    $html .= '<td width="20%">' . $row_['courtname'] . '</td>';

    $html .= '<td width="15%">' . $row_['claiming_amount'] . '</td>';

	 $examt='';

	 if($row_['instance_id']!='5') $examt= app_format_money($row_['execution_amount'], $project->currency_data);

    $html .= '<td width="15%">' .$examt . '</td>';

    $html .= '</tr>';

}

$html .= '</tbody>';

$html .= '</table>';

// Write project members table data

$pdf->writeHTML($html, true, false, false, false, '');











// Hearings overview



if(sizeof($hearings) > 0 ){

$html = '';

$html .= '<p><b style="background-color:#f0f0f0;font-size:12;font-weight:bold;">'.ucwords(_l('hearings')).'</b></p>';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';

$html .= '<thead>';

$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';

$html .= '<th width="16.66%"><b>'._l('hearing_date').'</b></th>';

$html .= '<th width="16.66%"><b>'._l('hearing_list_subject').'</b></th>';

$html .= '<th width="16.66%"><b>'._l('hearing_no').'</b></th>';

$html .= '<th width="16.66%"><b>'._l('hearing_type').'</b></th>';

$html .= '<th width="16.66%"><b>'._l('casediary_oppositeparty').'</b></th>';

$html .= '<th width="16.66%"><b>'._l('court_decision').'</b></th>';

$html .= '</tr>';

$html .= '</thead>';

$html .= '<tbody>';

foreach($hearings as $hearing){

    $html .= '<tr style="color:#4a4a4a;">';

        $html .= '<td>'._dt($hearing->hearing_date).'</td>';

        $html .= '<td>' . $hearing->subject . '</td>';

        $html .= '<td>'.$hearing->court_no.'</td>';

        $html .= '<td>'._l($hearing->hearing_type).'</td>';

        $html .= '<td>'.$hearing->opposite_party_name.'</td>';

        $html .= '<td>'.$hearing->proceedings.'</td>';



    $html .= '</tr>';

}

$html .= '</tbody>';

$html .= '</table>';

// Write timesheets data

$pdf->writeHTML($html, true, false, false, false, '');



$pdf->Ln(5);



}





// Court Atttachment overview

$pdf->Ln(5);

$html = '';

$html .= '<br><p><b style="background-color:#f0f0f0; font-size:12;font-weight:bold;">' . ucwords(_l('project_court_attach_grant')) . '</b></p>';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';

$html .= '<thead>';

$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';

$html .= '<th width="40%"><b>' . _l('corder_type') . '</b></th>';

$html .= '<th width="30%"><b>' . _l('corder_date') . '</b></th>';

$html .= '<th width="30%"><b>' . _l('corder_amount') . '</b></th>';



$html .= '</tr>';

$html .= '</thead>';

$html .= '<tbody>';

foreach ($court_order as $row_) {

    $html .= '<tr style="color:#4a4a4a;">';

    $html .= '<td width="40%">' . get_document_type_name($row_['documentid']) . '</td>';

    $html .= '<td width="30%">' . _d($row_['order_date']) . '</td>';

    $html .= '<td width="30%">' . app_format_money($row_['corder_amount'], $project->currency_data) . '</td>';

    $html .= '</tr>';

}

$html .= '</tbody>';

$html .= '</table>';

// Write tasks data

$pdf->writeHTML($html, true, false, false, false, '');



// Update overview

$pdf->Ln(5);

$html = '';

$html .= '<p><b style="background-color:#f0f0f0;">' . ucwords(_l('update_overview')) . '</b></p>';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';

$html .= '<thead>';

$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';

$html .= '<th width="20%" align="center"><b>' . _l('date_added') . '</b></th>';

$html .= '<th width="80%" align="center"><b>' . _l('details') . '</b></th>';

$html .= '</tr>';

$html .= '</thead>';

$html .= '<tbody>';

foreach ($case_updates as $rowu) {

    $html .= '<tr style="color:#4a4a4a;">';

    $html .= '<td  width="20%">' . _d($rowu['dateadded']) . '</td>';

    $html .= '<td width="80%">' . $rowu['content'] . '</td>';

    $html .= '</tr>';

}

$html .= '</tbody>';

$html .= '</table>';

// Write timesheets data

$pdf->writeHTML($html, true, false, false, false, '');



// Milestones overview

$pdf->Ln(5);

$html = '';

$html .= '<p><b style="background-color:#f0f0f0;font-size:12;font-weight:bold;">' . ucwords(_l('project_expense_overview')) . '</b></p>';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';

$html .= '<thead>';

$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';

$html .= '<th width="20%"><b>' . _l('categories') . '</b></th>';

$html .= '<th width="20%"><b>' . _l('total_amount') . '</b></th>';

$html .= '<th width="20%"><b>' . _l('paid_amount') . '</b></th>';

$html .= '<th width="20%"><b>' . _l('now_payable') . '</b></th>';

$html .= '<th width="20%"><b>' . _l('bal_payable') . '</b></th>';

$html .= '</tr>';

$html .= '</thead>';

$html .= '<tbody>';

$total_amount = $total_paid =$total_balance=$total_lastpaid=$tvat=$tpay=0;

foreach ($expenses as  $expenses) {

$bpaid=$expenses['amount']-($expenses['paid_amount']+$expenses['last_amount']+$expenses['vat_amount']);

	$npayable=$expenses['last_amount']+$expenses['vat_amount'];

    $html .= '<tr style="color:#4a4a4a;">';

    $html .= '<td width="20%">' . $expenses['category_name'] . '</td>';

    $html .= '<td width="20%">' . number_format($expenses['amount'],2). '</td>';

    $html .= '<td width="20%">' .number_format($expenses['paid_amount'],2) . '</td>';

    $html .= '<td width="20%">' . number_format($npayable,2) . '</td>';

    $html .= '<td width="20%">' . number_format($bpaid,2) . '</td>';

    $html .= '</tr>';

	$total_amount += $expenses['amount'];

  $total_paid += $expenses['paid_amount'];

  $total_lastpaid += $expenses['last_amount'];

  $total_balance += $bpaid;

	$tvat+=$expenses['vat_amount'];

}

$tpay=$total_lastpaid+$tvat;

$total_bpaid=$total_paid-$total_lastpaid;

$html .= '<tr><td colspan="5"></td></tr><tr>

                <td width="20%"><b>Total Expenses</b></td>

				<td width="20%"><b>'.number_format($total_amount,2).'</b></td>

                <td width="20%"><b>'.number_format($total_paid,2).'</b></td>

                <td width="20%"><b>'.number_format($tpay,2).'</b></td>

				<td width="20%"><b>'.number_format($total_balance,2).'</b></td>

              

            </tr>';

$html .= '</tbody>';

$html .= '</table>';

// Write milestones table data

$pdf->writeHTML($html, true, false, false, false, '');



if (ob_get_length() > 0 && ENVIRONMENT == 'production') {

    ob_end_clean();

}



// Output PDF to user

$pdf->output('#' . $project->id . '_' . $project->name . '_' . _d(date('Y-m-d')) . '.pdf', 'I');

