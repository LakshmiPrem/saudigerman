<?php

defined('BASEPATH') or exit('No direct script access allowed');

$dimensions    = $pdf->getPageDimensions();
$pdf->Ln(10);

$exowner=$exmanager=$exdirector=$exsecretary='';
$html = '<br>';
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">

 <tr>  
    <th width="30%">'._l('client_company').'</th>
    <th width="70%" colspan="3">'.$client->company.'</th>
   </tr>

    <tr>  
    <th width="30%">'._l('client_address').'</th>
    <th width="70%" colspan="3">'.$client->address.'<br>'.$client->city.'<br>'.$client->state.'<br>'.$client->phonenumber.'</th>
   </tr>';
if(count($client_owners)>0){
   foreach($client_owners as $owner){
						 $exowner .= $owner['firstname'].' '.$owner['lastname'].'<br>'; 
						  }
$html .= '<tr><td width="30%"><b>' . _l('client_owner') . ' </b></td><td width="70%" colspan="3">' .$exowner. '<br /></td></tr>';
}
if (!empty($client->country)) {
  $html .='<tr>
   <th width="30%">'._l('clients_country').'</th>
    <th width="70%">'.get_countryproject_name($client->country).'</th>
   </tr>';
}
   
if (!empty($client->service_agent)) {
  $html .='<tr>
   <th width="30%">'._l('service_agent').'</th>
    <th width="70%">'.$client->service_agent.'</th>
   </tr>';
}
if (!empty($client->other_identiyno)) {
  $html .='<tr>
   <th width="30%">'._l('other_identityno').'</th>
    <th width="70%">'.$client->other_identiyno.'</th>
   </tr>';
}
if (!empty($client->tax_regno)) {
  $html .='<tr>
   <th width="30%">'._l('tax_regno').'</th>
    <th width="70%">'.$client->tax_regno.'</th>
   </tr>';
}
if(count($client_managers)>0){
   foreach($client_managers as $owner1){
						 $exmanager .= $owner1['firstname'].' '.$owner1['lastname'].' '; 
						  }
$html .= '<tr><td width="30%"><b>' . _l('client_manager') . ' </b></td><td width="70%" colspan="3">' .$exmanager. '<br /></td></tr>';
}
if(count($client_directors)>0){
   foreach($client_directors as $owner11){
						 $exdirector .= $owner11['firstname'].' '.$owner11['lastname'].' '; 
						  }
$html .= '<tr><td width="30%"><b>' . _l('client_director') . ' </b></td><td width="70%" colspan="3">' .$exdirector. '<br /></td></tr>';
}
if(count($client_secretarys)>0){
   foreach($client_secretarys as $owner12){
						 $exsecretary .= $owner12['firstname'].' '.$owner12['lastname'].' '; 
						  }
$html .= '<tr><td width="30%"><b>' . _l('client_secretary') . ' </b></td><td width="70%" colspan="3">' .$exsecretary. '<br /></td></tr>';
}
$html .= '</table>';
$pdf->writeHTML($html, true, false, false, false, ''); 
// Stakeholder overview
$pdf->Ln(5);
if(sizeof($stakeeholders) > 0 ){
$html = '';
$html .= '<h2><b>' . ucwords(_l('customer_contacts')) . '</b></h2>';
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$html .= '<th width="40%"><b>' . _l('clients_list_full_name') . '</b></th>';
$html .= '<th width="30%"><b>' . _l('contact_position') . '</b></th>';
$html .= '<th width="30%"><b>' . _l('client_phonenumber') . '</b></th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
foreach ($stakeeholders as $contact) {
    $html .= '<tr style="color:#4a4a4a;">';
    $html .= '<td width="40%">' . $contact['firstname'].' '.$contact['lastname'] . '</td>';
    $html .= '<td width="30%">' . $contact['title'] . '</td>';
    $html .= '<td width="30%">' . $contact['phonenumber'] . '</td>';
    $html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';
// Write tasks data
$pdf->writeHTML($html, true, false, false, false, '');
}
if(sizeof($shareholders) > 0 ){
// Shareholder overview
$pdf->Ln(5);
$html = '';
$html .= '<h2><b>' . ucwords(_l('shareholders')) . '</b></h2>';
$html .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">';
$html .= '<thead>';
$html .= '<tr bgcolor="#323a45" style="color:#ffffff;">';
$html .= '<th width="70%"><b>' . _l('shareholder_name') . '</b></th>';
$html .= '<th width="30%"><b>' . _l('shareholder_percentage') . '</b></th>';
$html .= '</tr>';
$html .= '</thead>';
$html .= '<tbody>';
foreach ($shareholders as $plegal) {
    $html .= '<tr style="color:#4a4a4a;">';
    $html .= '<td width="70%">' . get_clientshareholdername($plegal['shareholder_id']) . '</td>';
    $html .= '<td width="30%">' . $plegal['share_percentage'] . ' % </td>';
    $html .= '</tr>';
}
$html .= '</tbody>';
$html .= '</table>';
// Write tasks data
$pdf->writeHTML($html, true, false, false, false, '');
}
// project overview heading
$pdf->Ln(5);
$html='';


if (ob_get_length() > 0 && ENVIRONMENT == 'production') {
    ob_end_clean();
}

// Output PDF to user
$pdf->output('#' . $client->userid . '_' . $client->company . '_' . _d(date('Y-m-d')) . '.pdf', 'I');
