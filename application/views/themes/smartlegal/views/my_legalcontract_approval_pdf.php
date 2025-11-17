<?php 
$dimensions = $pdf->getPageDimensions();

$pdf->ln(5);
$pdf->SetFont('DroidSansFallback', '', 10);

$tblhtml = '<br><br>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">

 <tr>  
    <th width="30%">'._l('contract_name').'</th>
    <th width="70%" colspan="3">'.$legalapprove->type_name.'</th>
   </tr>
    <tr>  
    <th width="30%">'._l('application_department').'</th>
    <th width="30%" >'.$legalapprove->company.'</th>
	<th width="20%">'._l('amount_relatednot').'</th>
    <th width="20%">'.number_format($legalapprove->contract_value,2).'</th>
   </tr>
 <tr>
    <th width="30%">' . _l('applicant') . '</th>
    <th width="30%">' .  get_opposite_party_name($legalapprove->other_party ). '</th>
	<th width="20%">'._l('reference_number').'</th>
    <th width="20%">  #'.$legalapprove->id.'</th>
  </tr> 

  <tr>
    <th width="30%">' . _l('type_stamp') . '</th>
    <th width="70%">' . $legalapprove->type_stamp.'</th>
	
  </tr>
  <tr>
    <th width="30%"><br>' . _l('contract_summary') . '</th>
    <th width="70%"   colspan="3">' . $legalapprove->description.'</th>
  </tr>
  <tr>
  <th colspan="2" width="60%"> <h3 style="font-size:14px;">'._l('applicant_signature').'</h3></th>
   <th colspan="2" width="40%"> <h3 style="font-size:14px;">'._l('appdate').'</h3></th>
  </tr>';


$tblhtml .= '<tbody></tbody></table>';

if($legalapprove->approval!=null){
$tblhtml .= '<h3 style="font-size:20px;text-align:center;">'._l('approved_and_signatureby').'</h3><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">
<thead>';

foreach ($legalapprove->approval as  $approval) {

 $apstatus='';$apdt='';
	if($approval['approval_status']==3)
	{
		$apstatus='Approved';
		$apdt=_dt($approval['dateapproved']);
	}elseif($approval['approval_status']==4)
	{
		$apstatus='Rejected';
		$apdt=_dt($approval['dateapproved']);
	}
 $tblhtml .= '<tr> 
    <th width="30%">'.get_approval_heading_name_by_id($approval['approval_heading_id']).'<br>'.'</th>
	<th width="50%" >'._l('signature').' : '.get_staff_full_name($approval['staffid']).'<br>'.$apstatus.'</th>
    <th width="20%" >'._l('appdate').' : '.$apdt.'</th>
   
  </tr>';
}
	$j=1;
	$tblhtml .= '<tr>
		 <th width="30%">'._l('pdfremarks').'</th> 
		 <th width="70%" colspan="3">';
	
	foreach ($legalapprove->approval as  $approval1) {
		$tblhtml.= $j++.' ) '. $approval1['approval_remarks'].'<br>';
	}
$tblhtml.='</th> </tr>';
 $tblhtml .=' </thead><tbody></tbody></table>';
	}

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>