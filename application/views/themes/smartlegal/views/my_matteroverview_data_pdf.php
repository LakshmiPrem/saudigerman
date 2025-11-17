<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions    = $pdf->getPageDimensions();
$custom_fields = get_custom_fields('projects');

// Like heading project name
$pdf->Ln(10);

$html = '';

$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="5" bordercolor="red">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#fff;">';
$html .= '<th>';
$html .= '<b style="font-size:24px;">' . _l('project_overview') . '</b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tr>';

$html .= '<td><br /><br />';

 $casetypes=get_case_client_types();
												  foreach($casetypes as $case1){
													  if($case1['id']==$project->case_type){
														$type1=$case1['name'];  
													  }
													  
												  }



$status = get_project_status_by_id($project->status);
// Project status
$html.='<table><thead></thead><tbody>';
$html .='<tr><td class="bold" width="30%">'. _l('file_no').'</td>
            <td style="color:#1446E5;font-weight: bold">'.$project->file_no.'</td><td class="bold">'. _l('project_start_date').'</td>
            <td style="color:#1446E5;font-weight: bold">'._d($project->start_date).'<br/></td></tr>';
$html .= '<tr><td><b>' . _l('particulars') . ': </b></td><td  colspan="3">' .ucwords($project->name).' - '. $type1 . '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('client_company') . ': </b></td><td  colspan="3">' .$project->client_data->company. '<br /></td></tr>';
$addclient='';
 if(!empty($project->addition_client)){  
						   	 $addclients=json_decode($project->addition_client);
                             foreach ($addclients as $value) {

                                $addclient .=get_company_name($value).'<br>';

                            }
	 $html .= '<tr><td><b>' . _l('additional_clients') . ': </b></td><td colspan="3">' .$addclient. '<br /></td></tr>';

  }
$exagent='';
if($project->case_type=='intellectual_properties'){
foreach($agent_stakeholders as $agent){
						 $exagent .= $agent['full_name'].'<br>'; 
						  }
$html .= '<tr><td><b>' . _l('externalagent_firm') . ': </b></td><td colspan="3">' .$exagent. '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('project_status') . ': </b></td><td colspan="3">' . $status['name'] . '<br/></td></tr>';
if (!empty($project->ip_category)) {
  $html .= '<tr><td><b>' . _l('ip_category') . ': </b></td><td  colspan="3">' .ucwords(get_matteripcategory($project->ip_category)). '<br /></td></tr>';
	if($project->ip_category==6){
		$subcat=$project->ip_artwork;
	 $html .= '<tr><td><b>' . _l('ip_subcategory') . ': </b></td><td colspan="3">' . ucwords($subcat) . '<br/></td></tr>';
	}
}

if (!empty($project->ip_subcategory)) {
	$subcat=get_matteripsubcategory($project->ip_subcategory);
  $html .= '<tr><td><b>' . _l('ip_subcategory') . ': </b></td><td colspan="3">' . ucwords($subcat) . '<br/></td></tr>';
}
if (!empty($project->ip_logo)) {
  $html .= '<tr><td><b>' . _l('artwork_filename') . ': </b></td><td  colspan="3">' .ucwords($project->ip_logo). '<br /></td></tr>';
}
if (!empty($project->ip_class)) {
  $html .= '<tr><td><b>' . _l('class') . ': </b></td><td  colspan="3">' .ucwords($project->ip_class). '<br /></td></tr>';
}
if (!empty($project->ip_filingno)) {
  $html .= '<tr><td><b>' . _l('file_no') . ': </b></td><td  colspan="3">' .ucwords($project->ip_filingno). '<br /></td></tr>';
}
if (!empty($project->ip_filingdate)) {
  $html .= '<tr><td><b>' . _l('file_date') . ': </b></td><td  colspan="3">' ._d($project->ip_filingdate). '<br /></td></tr>';
}
if (!empty($project->ip_regno)) {
  $html .= '<tr><td><b>' . _l('registration_no') . ': </b></td><td  colspan="3">' .ucwords($project->ip_regno). '<br /></td></tr>';
}
if (!empty($project->ip_registrationdt)) {
  $html .= '<tr><td><b>' . _l('ip_issue_date') . ': </b></td><td  colspan="3">' ._d($project->ip_registrationdt). '<br /></td></tr>';
}
if (!empty($project->ip_description)) {
  $html .= '<tr><td><b>' . _l('ip_description') . ': </b></td><td  colspan="3">' .ucwords($project->ip_description). '<br /></td></tr>';
}
}
if($project->case_type=='agreements'){
	if($project->internal_external=='external'){
	$party1=get_opposite_party_name($project->opposite_party);
		if (!empty($project->opposite_party)) {
  $html .= '<tr><td><b>' . _l('other_party') .' - '.ucwords($project->internal_external). ': </b></td><td  colspan="3">' .$party1. '<br /></td></tr>';
}
}else{
	$party1=get_company_name($project->internal_party);
	if (!empty($project->internal_party)) {
  $html .= '<tr><td><b>' . _l('other_party_internal') . ': </b></td><td  colspan="3">' .$party1. '<br /></td></tr>';
}
}
foreach($agent_stakeholders as $agent){
						 $exagent .= $agent['full_name'].'<br>'; 
						  }
$html .= '<tr><td><b>' . _l('business_stakeholder') . ': </b></td><td colspan="3">' .$exagent. '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('project_status') . ': </b></td><td colspan="3">' . $status['name'] . '<br/></td></tr>';

if (!empty($project->signed_by)) {
  $html .= '<tr><td><b>' . _l('signed_by') . ': </b></td><td  colspan="3">' .ucwords($project->signed_by). '<br /></td></tr>';
}
if (!empty($project->claiming_amount)) {
  $html .= '<tr><td><b>' . _l('c_value') . ': </b></td><td  colspan="3">' .$project->claiming_amount. '<br /></td></tr>';
}
if (!empty($project->datestart)) {
  $html .= '<tr><td><b>' . _l('contract_start_date') . ': </b></td><td  colspan="3">' ._d($project->datestart). '<br /></td></tr>';
}
if (!empty($project->dateend)) {
  $html .= '<tr><td><b>' . _l('contract_end_date') . ': </b></td><td  colspan="3">' ._d($project->dateend). '<br /></td></tr>';
}
if (!empty($project->countryid)) {
  $html .= '<tr><td><b>' . _l('country') . ': </b></td><td  colspan="3">' .get_countryproject_name($project->countryid). '<br /></td></tr>';
}
if (!empty($project->city)) {
  $html .= '<tr><td><b>' . _l('city') . ': </b></td><td  colspan="3">' .ucwords($project->city). '<br /></td></tr>';
}
}
if($project->case_type=='litigation'){
if (!empty($project->litclient_id)) {
  $html .= '<tr><td><b>' . _l('cindividual_name') . ': </b></td><td  colspan="3">' .ucwords(get_clientshareholdername($project->litclient_id)). '<br /></td></tr>';
}
	if (!empty($project->opposite_party)) {
  $html .= '<tr><td><b>' . _l('other_party') . ': </b></td><td  colspan="3">' .get_opposite_party_name($project->opposite_party). '<br /></td></tr>';
}
if (!empty($project->litopposite_id)) {
  $html .= '<tr><td><b>' . _l('oindividual_name') . ': </b></td><td  colspan="3">' .ucwords(get_opposite_contactperson($project->litopposite_id)). '<br /></td></tr>';
}
if (!empty($project->jurisdiction)) {
  $html .= '<tr><td><b>' . _l('jurisdiction') . ': </b></td><td  colspan="3">' .ucwords(get_jurisdiction_name_by_id($project->jurisdiction)). '<br /></td></tr>';
}
	if(count($lastcourt_instances)>0){
 foreach($lastcourt_instances as $final){
$html .= '<tr><td><b>' . _l('client_position') . ': </b></td><td  colspan="3">' .ucwords(get_client_positionsbyId($final['client_position'])). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('opposite_party_position') . ': </b></td><td  colspan="3">' .ucwords( get_client_positionsbyId($final['opposite_party_position'])). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('nature_matter') . ': </b></td><td  colspan="3">' .ucwords($final['case_nature_name']). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('hearing_court') . ': </b></td><td  colspan="3">' .ucwords($final['courtname']). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('category') . ': </b></td><td  colspan="3">' .ucwords(get_litcategory_name_by_id($project->lit_category)). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('case_no'). ' (Latest)' . ': </b></td><td  colspan="3">' .ucwords($final['case_number']). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('law_firm') . ': </b></td><td  colspan="3">' .ucwords( get_opposite_party_name($project->lawyer_id)). '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('stage') . ': </b></td><td  colspan="3">' .ucwords( get_court_stage_name_by_id($project->lit_stage)). '<br /></td></tr>';
 }
	}
}

if($project->case_type=='projects' || $project->case_type=='general_matters'){
	if (!empty($project->opposite_party)) {
  $html .= '<tr><td><b>' . _l('firms') . ': </b></td><td  colspan="3">' .get_opposite_party_name($project->opposite_party). '<br /></td></tr>';
}
$addopponent='';
 if(!empty($project->additional_party)){  
						   	 $addpartys=json_decode($project->additional_party);
                             foreach ($addpartys as $value) {

                                $addopponent .=get_opposite_party_name($value).'<br>';

                            }
	 $html .= '<tr><td><b>' . _l('additional_partys') . ': </b></td><td colspan="3">' .$addopponent. '<br /></td></tr>';

  }
foreach($agent_stakeholders as $agent){
						 $exagent .= $agent['full_name'].'<br>'; 
						  }
$html .= '<tr><td><b>' . _l('business_stakeholder') . ': </b></td><td colspan="3">' .$exagent. '<br /></td></tr>';
if (!empty($project->jurisdiction)) {
  $html .= '<tr><td><b>' . _l('jurisdiction') . ': </b></td><td  colspan="3">' .ucwords(get_jurisdiction_name_by_id($project->jurisdiction)). '<br /></td></tr>';
}
if (!empty($project->countryid)) {
  $html .= '<tr><td><b>' . _l('country') . ': </b></td><td  colspan="3">' .get_countryproject_name($project->countryid). '<br /></td></tr>';
}
}
if($project->case_type=='properties'){
		if($project->internal_external=='external'){
	$party1=get_opposite_party_name($project->opposite_party);
		if (!empty($project->opposite_party)) {
  $html .= '<tr><td><b>' . _l('individual_owner') .' - '._l('individual'). ': </b></td><td  colspan="3">' .$party1. '<br /></td></tr>';
}
}else{
	$party1=get_company_name($project->internal_party);
	if (!empty($project->internal_party)) {
  $html .= '<tr><td><b>' . _l('entity_owner') . ': </b></td><td  colspan="3">' .$party1. '<br /></td></tr>';
}
}
$addopponent='';
 if(!empty($project->addition_party)){  
						   	 $addpartys=json_decode($project->addition_party);
                             foreach ($addpartys as $value) {

                                $addopponent .=get_opposite_party_name($value).'<br>';

                            }
	 $html .= '<tr><td><b>' . _l('additional_partys') . ': </b></td><td colspan="3">' .$addopponent. '<br /></td></tr>';

  }

if (!empty($project->prissue_date)) {
  $html .= '<tr><td><b>' . _l('issue_date') . ': </b></td><td  colspan="3">' ._d($project->prissue_date). '<br /></td></tr>';
}
if (!empty($project->mortgage_status)) {
	 if($project->mortgage_status=='mortgaged')
						 $resperson='('.ucwords($project->prmortgaged).')';
					  else
						  $resperson='';
   $html .= '<tr><td><b>' . _l('mortgage_status') . ': </b></td><td  colspan="3">' .ucwords(str_replace('_',' ',$project->mortgage_status)).' '.$resperson. '<br /></td></tr>';
}
if (!empty($project->prproperty_type)) {
  $html .= '<tr><td><b>' . _l('property_type') . ': </b></td><td  colspan="3">' .ucwords($project->prproperty_type). '<br /></td></tr>';
}
if (!empty($project->prcommunity)) {
  $html .= '<tr><td><b>' . _l('community') . ': </b></td><td  colspan="3">' .ucwords($project->prcommunity). '<br /></td></tr>';
}
if (!empty($project->pramount)) {
  $html .= '<tr><td><b>' . _l('guaranteamount') . ': </b></td><td  colspan="3">' .ucwords($project->pramount). '<br /></td></tr>';
}
if (!empty( $project->prplot_no)) {
  $html .= '<tr><td><b>' . _l('plot_no') . ': </b></td><td  colspan="3">' . $project->prplot_no. '<br /></td></tr>';
}
if (!empty($project->prmuncipality_no)) {
  $html .= '<tr><td><b>' . _l('municipality_no') . ': </b></td><td  colspan="3">' .ucwords($project->prmuncipality_no). '<br /></td></tr>';
}
if (!empty($project->prbuilding_no)) {
  $html .= '<tr><td><b>' . _l('building_no') . ': </b></td><td  colspan="3">' .ucwords($project->prbuilding_no). '<br /></td></tr>';
}
if (!empty($project->prbuilding_name)) {
  $html .= '<tr><td><b>' . _l('building_name') . ': </b></td><td  colspan="3">' . $project->prbuilding_name. '<br /></td></tr>';
}
if (!empty($project->prproperty_no)) {
  $html .= '<tr><td><b>' . _l('property_no') . ': </b></td><td  colspan="3">' .ucwords($project->prproperty_no). '<br /></td></tr>';
}
if (!empty($project->prfloor_no)) {
  $html .= '<tr><td><b>' . _l('floor_no') . ': </b></td><td  colspan="3">' .ucwords($project->prfloor_no). '<br /></td></tr>';
}
if (!empty($project->prparkings)) {
  $html .= '<tr><td><b>' . _l('parkings') . ': </b></td><td  colspan="3">' .ucwords($project->prparkings). '<br /></td></tr>';
}
if (!empty($project->prarea_sqm)) {
  $html .= '<tr><td><b>' . _l('area_sqm') . ': </b></td><td  colspan="3">' .ucwords($project->prarea_sqm). '<br /></td></tr>';
}
if (!empty($project->prarea_sqfeet)) {
  $html .= '<tr><td><b>' . _l('area_sqfeet') . ': </b></td><td  colspan="3">' .ucwords($project->prarea_sqfeet). '<br /></td></tr>';
}
if (!empty($project->prcommon_area)) {
  $html .= '<tr><td><b>' . _l('common_area') . ': </b></td><td  colspan="3">' .ucwords($project->prcommon_area). '<br /></td></tr>';
}
if (!empty($project->prowner_no)) {
  $html .= '<tr><td><b>' . _l('owner_no') . ': </b></td><td  colspan="3">' .ucwords($project->prowner_no). '<br /></td></tr>';
}
if (!empty($project->prpuchase_from)) {
  $html .= '<tr><td><b>' . _l('purchased_from') . ': </b></td><td  colspan="3">' .ucwords($project->prpuchase_from). '<br /></td></tr>';
}
if (!empty($project->prland_regno)) {
  $html .= '<tr><td><b>' . _l('land_regno') . ': </b></td><td  colspan="3">' .ucwords($project->prland_regno). '<br /></td></tr>';
}
}
if($project->case_type=='intellectual_properties'){
foreach($agent_stakeholders as $agent){
						 $exagent .= $agent['full_name'].'<br>'; 
						  }
$html .= '<tr><td><b>' . _l('externalagent_firm') . ': </b></td><td colspan="3">' .$exagent. '<br /></td></tr>';
$html .= '<tr><td><b>' . _l('project_status') . ': </b></td><td colspan="3">' . $status['name'] . '<br/></td></tr>';
if (!empty($project->ip_category)) {
  $html .= '<tr><td><b>' . _l('ip_category') . ': </b></td><td  colspan="3">' .ucwords(get_matteripcategory($project->ip_category)). '<br /></td></tr>';
	if($project->ip_category==6){
		$subcat=$project->ip_artwork;
	 $html .= '<tr><td><b>' . _l('ip_subcategory') . ': </b></td><td colspan="3">' . ucwords($subcat) . '<br/></td></tr>';
	}
}

if (!empty($project->ip_subcategory)) {
	$subcat=get_matteripsubcategory($project->ip_subcategory);
  $html .= '<tr><td><b>' . _l('ip_subcategory') . ': </b></td><td colspan="3">' . ucwords($subcat) . '<br/></td></tr>';
}
if (!empty($project->ip_logo)) {
  $html .= '<tr><td><b>' . _l('artwork_filename') . ': </b></td><td  colspan="3">' .ucwords($project->ip_logo). '<br /></td></tr>';
}
if (!empty($project->ip_class)) {
  $html .= '<tr><td><b>' . _l('class') . ': </b></td><td  colspan="3">' .ucwords($project->ip_class). '<br /></td></tr>';
}
if (!empty($project->ip_filingno)) {
  $html .= '<tr><td><b>' . _l('file_no') . ': </b></td><td  colspan="3">' .ucwords($project->ip_filingno). '<br /></td></tr>';
}
if (!empty($project->ip_filingdate)) {
  $html .= '<tr><td><b>' . _l('file_date') . ': </b></td><td  colspan="3">' ._d($project->ip_filingdate). '<br /></td></tr>';
}
if (!empty($project->ip_regno)) {
  $html .= '<tr><td><b>' . _l('registration_no') . ': </b></td><td  colspan="3">' .ucwords($project->ip_regno). '<br /></td></tr>';
}
if (!empty($project->ip_registrationdt)) {
  $html .= '<tr><td><b>' . _l('ip_issue_date') . ': </b></td><td  colspan="3">' ._d($project->ip_registrationdt). '<br /></td></tr>';
}
if (!empty($project->ip_description)) {
  $html .= '<tr><td><b>' . _l('ip_description') . ': </b></td><td  colspan="3">' .ucwords($project->ip_description). '<br /></td></tr>';
}
}
$html .= '</tbody></table>';
 								 
$html .= '</td></tr>';
$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');

$pdf->ln(5);

// Agreement overview

if(sizeof($project_contracts) > 0 ){
$html = '';
// Heading
$html .= '<p><b style="background-color:#f0f0f0;font-size:12;font-weight:bold;">' . ucwords(_l('agreementsub_details')) . '</b></p>';
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$html .= '<th width="20%"><b>' . _l('contract_id') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('particulars') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('type_subfile') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('contract_start_date') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('clients_estimate_dt_duedate') . '</b></th>';

$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
 foreach ($project_contracts as $row_) {
    $html .= '<tr style="color:#4a4a4a;">';
    $html .= '<td width="20%">' . $row_['id'] . '</td>';
    $html .= '<td  width="20%">' .$row_['subject'] . '</td>';
    $html .= '<td width="20%">' .  get_contracttype($row_['contract_type']) . '</td>';
    $html .= '<td width="20%">' . _d($row_['datestart']) . '</td>';
	$html .= '<td width="20%">' . _d($row_['dateend']) . '</td>';
	$html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';
// Write project members table data
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Ln(5);
}

/* court Stages*/
if(sizeof($court_instances) > 0 ){
$html = '';
// Heading
$html .= '<p><b style="background-color:#f0f0f0;font-size:12;font-weight:bold;">' . ucwords(_l('stage_details')) . '</b></p>';
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$html .= '<th width="20%"><b>' . _l('stage') . '</b></th>';
$html .= '<th width="15%"><b>' . _l('case_no') . '</b></th>';
$html .= '<th width="20%"><b>' .  _l('case_nature') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('hearing_court') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('claiming_amount') . '</b></th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
 foreach ($court_instances as $row_) {
	 $insta1='';
	 if($row_['instance_name']!='') $insta1='<br>'. $row_['instance_name'];
    $html .= '<tr style="color:#4a4a4a;">';
     $html .= '<td  width="25%">' .$insta1 . '</td>';
    $html .= '<td width="15%">' .  $row_['case_number'] . '</td>';
    $html .= '<td width="20%">' . $row_['case_nature_name'] . '</td>';
    $html .= '<td width="20%">' . $row_['courtname'] . '</td>';
	  $html .= '<td width="20%">' .$row_['claiming_amount']  . '</td>';
	$html .= '</tr>';
}  
$html .= '</tbody>';
$html .= '</table>';
// Write project members table data
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Ln(5);
}

if(sizeof($project_subfiles) > 0 ){
$html = '';
// Heading
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$html .= '<th width="20%"><b>' . _l('matter_id') . '</b></th>';
$html .= '<th width="10%"><b>' . _l('particulars') . '</b></th>';
$html .= '<th width="20%"><b>' . _l('type_subfile') . '</b></th>';
$html .= '<th width="20%"><b>' .  _l('filing_date') . '</b></th>';
$html .= '<th width="15%"><b>' . _l('contract_start_date') . '</b></th>';
$html .= '<th width="15%"><b>' . _l('clients_estimate_dt_duedate') . '</b></th>';

$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
 foreach ($project_subfiles as $row_) {
    $html .= '<tr style="color:#4a4a4a;">';
    $html .= '<td width="20%">' . $row_['matter_subrefno'] . '</td>';
    $html .= '<td  width="10%">' .$row_['subject'] . '</td>';
    $html .= '<td width="20%">' .  get_document_type_name($row_['document_type']) . '</td>';
    $html .= '<td width="20%">' . _d($row_['subfiling_date']) . '</td>';
    $html .= '<td width="15%">' . _d($row_['issue_date']) . '</td>';
	$html .= '<td width="15%">' . _d($row_['expiry_date']) . '</td>';
	$html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';
// Write project members table data
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Ln(5);
}


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
        $html .= '<td>'._d($hearing->hearing_date).'</td>';
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


if(sizeof($case_updates) > 0 ){
$html = '';
$html .= '<p><b style="background-color:#f0f0f0;font-size:12;font-weight:bold;">' . ucwords(_l('update_overview')) . '</b></p>';
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$html .= '<th width="100%" align="left"><b>' . _l('details') . '</b></th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
foreach ($case_updates as $rowu) {
    $html .= '<tr style="color:#4a4a4a;">';
    $html .= '<td width="100%">' . $rowu['content'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';
// Write timesheets data
$pdf->writeHTML($html, true, false, false, false, '');
	$pdf->Ln(5);
}
// project overview heading
$pdf->Ln(5);
$html='';

if (!empty($project->description)) {
// Heading
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="5" bordercolor="red">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#fff;">';
$html .= '<th>';
$html .= '<b style="font-size:24px;">' . _l('project_description') . '</b>';
$html .= '</th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
$html .= '<tr>';
$html .= '<td>';

	
    // Project description
    $html .= '<p>'. nl2br($project->description) . '</p>';


$html .= '</td>';
$html .= '</tr>';
$html .= '</tbody>';
$html .= '</table>';

$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Ln(5);
}
if (ob_get_length() > 0 && ENVIRONMENT == 'production') {
    ob_end_clean();
}

