<?php

defined('BASEPATH') or exit('No direct script access allowed');

$total_client_contacts = total_rows(db_prefix() . 'client_subfile', ['userid' => $client_id]);
$aColumns        = [db_prefix() . 'constitution_type.name as document_name','subject','issue_date','expiry_date','matter_subrefno'];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'client_subfile';
$join         = [ 'LEFT JOIN ' . db_prefix() . 'constitution_type ON ' . db_prefix() . 'client_subfile.document_type = ' . db_prefix() . 'constitution_type.id',
                  'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'client_subfile.userid'];

$where = ['AND tblclient_subfile.userid=' . $this->ci->db->escape_str($client_id)];

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblclient_subfile.id','tblclient_subfile.userid as userid','file_name','file_type','tblclients.email_id as email_id',db_prefix() . 'constitution_type.shortname as documentshort',]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	
    $row = [];
	$row[] = $aRow['id'];
	$row[] = $aRow['documentshort'].' - '.$aRow['document_name'];
    $rowName = '<a href="#" onclick="constitution(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . $aRow['subject'] . '</a>';

    $rowName .= '<div class="row-options">';

    $rowName .= '<a href="#" onclick="constitution(' . $aRow['userid'] . ',' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';

    if (has_permission('customers', '', 'delete') || is_customer_admin($aRow['userid'])) {
       
            $rowName .= ' | <a href="' . admin_url('clients/delete_clientconstitution/' . $aRow['userid'] . '/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
       
    }
	if (has_permission('customers', '', 'view')) {
            $rowName .= ' | <a  href="' . site_url('download/downloadconstitution/' . $aRow['userid'] .'/'. $aRow[ 'id']) . '">' . _l('download') . '</a>';
			
        }

    $rowName .= '</div>';

    $row[] = $rowName;

    $row[] = _d($aRow['issue_date']);
	$row[] = _d($aRow['expiry_date']);
    $upload_path=get_upload_path_by_type('customer_subfile_images');
    $path = $upload_path . $aRow['userid'] . '/' . $aRow['file_name'];
	$row[]= '<button type="button" class="btn btn-info btn-icon" onclick="sendsubfile(this,'.$aRow['id'].'); return false;" data-file_name="'.$aRow['file_name'].'" data-file_path="'.$path.'" data-file_type="'.$aRow['file_type'].'" data-email_id="'.$aRow['email_id'].'"><i class="fa fa-envelope"></i></button>';
   

   $output['aaData'][] = $row;
}
