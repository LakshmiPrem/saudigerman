<?php 

$dimensions = $pdf->getPageDimensions();

// set some language dependent data:

$lg = Array();

$lg['a_meta_charset'] = 'UTF-8';

$lg['a_meta_dir'] = 'rtl';

$lg['a_meta_language'] = 'fa';

$lg['w_page'] = 'page';



// set some language-dependent strings (optional)

$pdf->setLanguageArray($lg);

$pdf->setRTL(true);





// Get Y position for the separation

$y = $pdf->getY();

$pdf_logo_url = pdf_logo_url();

$organizaion_info = '<div style="color:#424242;">';

//$organizaion_info .= format_organization_info();

$organizaion_info .= '</div>';

//$pdf->writeHTMLCell(278, '', '', '', $pdf_logo_url, 0, 1, false, true, 'J', true);

$pdf->Ln(2);

//$pdf->SetFont($font_name, '', 10);
$pdf->SetFont('dejavusans', '', 9);
$tblhtml ='';
$from_date = $litigations[0]['from_date'];
$to_date = $litigations[0]['to_date'];
$daterange='';

if($litigations[0]['report_months']=='custom'){
	$daterange=' From '.$from_date.' To '.$to_date;
}elseif($litigations[0]['report_months']=='this_month'){
	$daterange=ucwords(_l('duration')).' : '._d(date('Y-m-01')) .' To '._d(date('Y-m-t'));
}elseif($litigations[0]['report_months']=='1'){
	$beginMonth = date('Y-m-01', strtotime("-1 MONTH"));
                   $endMonth   = date('Y-m-t', strtotime('-1 MONTH'));
	$daterange=ucwords(_l('duration')).' : '._d($beginMonth) .' To '._d($endMonth);
}elseif($litigations[0]['report_months']=='this_year'){
	$daterange=ucwords(_l('duration')).' : '._d(date('Y-m-d',strtotime(date('Y-01-01')))) .' To '._d(date('Y-m-d',strtotime(date('Y-12-'.date('d',strtotime('last day of this year'))))));
}elseif($litigations[0]['report_months']=='last_year'){
	$daterange=ucwords(_l('duration')).' : '._d(date('Y-m-d',strtotime(date(date('Y',strtotime('last year')).'-01-01')))) .' To '._d(date('Y-m-d',strtotime(date(date('Y',strtotime('last year')). '-12-'.date('d',strtotime('last day of last year'))))));
}elseif($litigations[0]['report_months']=='3'){
	 $beginMonth = date('Y-m-01', strtotime("-2 MONTH"));
                   $endMonth   = date('Y-m-t');
	$daterange=ucwords(_l('duration')).' : '._d($beginMonth) .' To '._d($endMonth);
}elseif($litigations[0]['report_months']=='6'){
	 $beginMonth = date('Y-m-01', strtotime("-5 MONTH"));
                   $endMonth   = date('Y-m-t');
	$daterange=ucwords(_l('duration')).' : '._d($beginMonth) .' To '._d($endMonth);
}elseif($litigations[0]['report_months']=='12'){
	 $beginMonth = date('Y-m-01', strtotime("-11 MONTH"));
                   $endMonth   = date('Y-m-t');
	$daterange=ucwords(_l('duration')).' : '._d($beginMonth) .' To '._d($endMonth);
}
$tblhtml .= '<p align="center"><h2><b style="background-color:#f0f0f0;">'.ucwords(_l('collective_litigation_report')).'</b></h2></p>';
$tblhtml .= '<p align="center"><h3><b style="background-color:#f0f0f0;">'.ucwords(_l('without_description')).'</b></h3></p>';
$tblhtml .= '<p align="right"><h4><b style="background-color:#f0f0f0;color:#4e4e4e;">'.$daterange.'<br/>Created By :'.get_staff_full_name(get_staff_user_id()).'<br/>Created Date : '._d(date('Y-m-d')).'</b></h4></p>';
//$tblhtml .= '<p align="right"><h4><b style="background-color:#f0f0f0;color:#4e4e4e;"></b>'.$daterange.'</h4></p>';
$tblhtml .= '<br><br><table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1" style="width:100%; margin:5px;">';

$tblhtml .= '<thead>';



$tblhtml .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$tblhtml .= '<th style="text-align:left;" width="10%"><b>'._l('law_firm').'</b></th>';
$tblhtml .= '<th  style="text-align:left;" width="21%"><b>'._l('case_updates').'</b></th>';
$tblhtml .= '<th  style="text-align:left;" width="10%"><b>'._l('claiming_amount').'</b></th>';
$tblhtml .= '<th style="text-align:left;" width="10%"><b>'._l('case_type').'</b></th>';
$tblhtml .= '<th  style="text-align:left;" width="10%"><b>'._l('hearing_postponed_until').'</b></th>';
$tblhtml .= '<th style="text-align:left;" width="10%"><b>'._l('case_no').'</b></th>';
$tblhtml .= '<th  style="text-align:left;" width="12%"><b>'._l('oppositeparty_position').'</b></th>';
$tblhtml .= '<th style="text-align:left;" width="13%"><b>'._l('client_position').'</b></th>';
$tblhtml .= '<th  style="text-align:left;" width="4%"><b>'._l('sl_no').'</b></th>';
 

$tblhtml .= '</tr>';

$tblhtml .= '</thead>';

$tblhtml .= '<tbody>';
$j=1;
foreach ($litigations as $aRow) {

	if(isset($aRow['company'])){

		$tblhtml .= '<tr nobr="true">';
		$tblhtml .= '<td  style="text-align:left;" width="10%">'.get_staff_full_name($aRow['lawyer_id']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="21%">'.nl2br($aRow['case_updates']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="10%">'.number_format($aRow['claiming_amount']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="10%">'._l($aRow['case_type']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="10%">'._d($aRow['hearingdate']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="10%">'.$aRow['case_number'].' - '.get_nature_of_case_by_id($aRow['casenature_id']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="12%">'.get_opposite_party_name($aRow['opposite_party']).'<br>'.get_position_name_by_id($aRow['opposite_party_position']).'</td>';
		$tblhtml .= '<td  style="text-align:left;" width="13%">'.$aRow['company'].'<br>'.get_position_name_by_id($aRow['client_position']).'</td>';
		$tblhtml .= '<td  style="text-align:left;"  width="4%">'.$j++.'</td>';
		$tblhtml .= '</tr>';
		
	}

}

$tblhtml .= '</tbody>';

$tblhtml .= '</table>';



$pdf->writeHTML($tblhtml, true, false, false, false, '');



?>