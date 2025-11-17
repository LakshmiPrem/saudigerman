<?php

defined('BASEPATH') or exit('No direct script access allowed');

$total_client_contacts = total_rows(db_prefix() . 'client_subfile', ['userid' => $client_id]);
$aColumns        = [db_prefix() . 'constitution_type.name as document_name','subject','issue_date','expiry_date'];
$sIndexColumn = 'id';
$sTable       = db_prefix() . 'client_subfile';
$where   = array('  ');
$join         = [ 'LEFT JOIN ' . db_prefix() . 'constitution_type ON ' . db_prefix() . 'client_subfile.document_type = ' . db_prefix() . 'constitution_type.id',];

$where = ['AND userid=' . $this->ci->db->escape_str($client_id)];

    array_push($where, 'AND client_cat_type="meeting"');

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, ['tblclient_subfile.id','userid','client_cat_type',db_prefix() . 'constitution_type.shortname as documentshort',]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
	
    $row = [];
	//$row[] = $aRow['matter_subrefno'];
	
    $rowName = '<a href="#" data-type="'.$aRow['client_cat_type'].'" onclick="constitution(' . $aRow['userid'] . ',' . $aRow['id'] . ',this);return false;">' . $aRow['subject'] . '</a>';

    $rowName .= '<div class="row-options">';

    $rowName .= '<a href="#" data-type="'.$aRow['client_cat_type'].'" onclick="constitution(' . $aRow['userid'] . ',' . $aRow['id'] . ',this);return false;">' . _l('edit') . '</a>';

    if (has_permission('customers', '', 'delete') || is_customer_admin($aRow['userid'])) {
       
            $rowName .= ' | <a   href="' . admin_url('clients/delete_clientconstitution/' . $aRow['userid'] . '/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
       
    }
	if (has_permission('customers', '', 'view')) {
            $rowName .= ' | <a  href="' . site_url('download/downloadconstitution/' . $aRow['userid'] .'/'. $aRow[ 'id']) . '">' . _l('download') . '</a>';
			
        }

    $rowName .= '</div>';

    $row[] = $rowName;
	$row[] =$aRow['document_name'];
    $row[] = _d($aRow['issue_date']);
	$row[] = _d($aRow['expiry_date']);
	
   

   $output['aaData'][] = $row;
}
