/**/<?php 
$dimensions = $pdf->getPageDimensions();

$info_right_column = '';
$info_left_column  = '';

$info_right_column .= '<br><br><br><span style="font-weight:bold;font-size:27px;">' . _l('contract_approvalform') . '</span><br />';
//$info_right_column .= '<b style="color:#4e4e4e;">' . format_organization_info() . '</b>';

// Add logo
$info_left_column .= pdf_logo_url();

// Write top left logo and right column info/text
pdf_multi_row($info_right_column, $info_left_column, $pdf, ($dimensions['wk'] / 2) - $dimensions['lm']);

$pdf->ln(5);
$tblhtml ='<hr color="grey" height="7px">';


$tblhtml .= '<br><br>';
$tblhtml .= '<table width="100%" bgcolor="#fff" cellspacing="0" cellpadding="10" border="1">
 <tr>  
    <th width="30%" style="text-align:center;">'._l('contract_name').'</th>
    <th width="70%" colspan="3" style="text-align:center;">'.$legalapprove->subject.'</th>
   </tr>
    <tr>  
    <th width="30%" style="text-align:center;">'._l('application_department').'</th>
    <th width="30%" style="text-align:center;">'.$legalapprove->department_name.'</th>
	<th width="20%" style="text-align:center;">'._l('amount_relatednot').'</th>
    <th width="20%" style="text-align:center;">'.ucwords($legalapprove->amount_related).'</th>
   </tr>
 <tr>
    <th width="30%" style="text-align:center;">' . _l('applicant') . '</th>
    <th width="30%" style="text-align:center;">' . $legalapprove->company . '</th>
	<th width="20%" style="text-align:center;">'._l('reference_no').'</th>
    <th width="20%" style="text-align:center;">  #'.$legalapprove->ticketid.'</th>
  </tr> 
  <tr>
    <th width="30%" style="text-align:center;">' . _l('client_name') . '</th>
    <th width="70%"  colspan="3" style="text-align:center;">' .get_opposite_party_name($legalapprove->opposteparty).'</th>
  </tr>
  <tr>
    <th width="30%" style="text-align:center;">' . _l('type_stamp') . '</th>
    <th width="70%"  colspan="3" style="text-align:center;">' . $legalapprove->stamp_type.'</th>
  </tr>
  <tr>
    <th width="30%" style="text-align:center;">' . _l('contract_summary') . '</th>
    <th width="70%"   colspan="3" style="text-align:center;">' . $legalapprove->message.'</th>
  </tr>
  <tr>
  <th colspan="4"> <h3 style="font-size:14px;">Applicant Signature And Date: </h3></th>
  </tr>';


$tblhtml .= '<tbody></tbody></table>';

if($legalapprove->approval!=null){
$tblhtml .= '<h3 style="font-size:20px;text-align:center;">Approved And Signature By</h3><table  width="100%" bgcolor="#fff" cellspacing="0" cellpadding="5" border="1">
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
    <th width="40%" style="text-align:center;">'.get_approval_heading_name_by_id($approval['approval_heading_id']).'<br>'.get_staff_full_name($approval['staffid']).'<br></th>
    <th width="20%" style="text-align:center;">'.$apstatus.'<br>'.$apdt.'</th>
    <th width="40%" style="text-align:center;">'.$approval['approval_remarks'].'</th>  
  </tr>';
}

 $tblhtml .=' </thead><tbody></tbody></table>';
	}

$pdf->writeHTML($tblhtml, true, false, false, false, '');

?>