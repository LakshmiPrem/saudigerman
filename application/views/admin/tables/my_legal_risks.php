<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;
$hasPermissionDelete = has_permission('legal_risks', '', 'delete');
 
$aColumns = array(
   '1',
	
    'tblrisktypes.name as risk_type',
	db_prefix() . 'tickets_priorities.name as priority_name',
    'risk_value',
    get_sql_select_client_company(),
    db_prefix() . 'riskstatus.statusname as risk_status',
	db_prefix() . 'contacts.firstname as user_firstname',
	db_prefix() . 'contacts.lastname as user_lastname',
		
    );

$sIndexColumn = "id";
$sTable       = 'tbllegal_risk';
$join = array('LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'legal_risk.branchid',
			 'LEFT JOIN ' . db_prefix() . 'risktypes ON ', '' . db_prefix() . 'risktypes.id = ' . db_prefix() . 'legal_risk.risktype',
			  'LEFT JOIN ' .db_prefix() . 'riskstatus ON ', '' . db_prefix() . 'riskstatus.id = ' . db_prefix() . 'legal_risk.risk_status', 
			  'LEFT JOIN ' .db_prefix() . 'tickets_priorities ON ', db_prefix() . 'tickets_priorities.priorityid = ' . db_prefix() . 'legal_risk.probability',
			  'LEFT JOIN ' .db_prefix() . 'contacts ON ', '' . db_prefix() . 'contacts.id = ' . db_prefix() . 'legal_risk.contactid');
	$custom_fields = get_table_custom_fields('legal_risk');
foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tbllegal_risk.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
}

$where  = [];
$filter = [];

if ($this->ci->input->post('exclude_trashed_contracts')) {
    array_push($filter, 'AND trash = 0');
}
if ($this->ci->input->post('trash')) {
    array_push($filter, 'OR trash = 1');
}
if ($this->ci->input->post('expired')) {
    array_push($filter, 'OR expiry_date IS NOT NULL AND expiry_date <"'.date('Y-m-d').'" and trash = 0');
}

if ($this->ci->input->post('without_dateend')) {
    array_push($filter, 'OR expiry_date IS NULL AND trash = 0');
}
$statusIds = [];

foreach ($this->ci->legalrisk_model->get_legalrisk_status() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR risk_status IN (' . implode(', ', $statusIds) . ')');
}

$types    = $this->ci->legalrisk_model->get_legalrisk_type();
$typesIds = [];
foreach ($types as $type) {
    if ($this->ci->input->post('contracts_by_type_' . $type['id'])) {
        array_push($typesIds, $type['id']);
    }
}

if (count($typesIds) > 0) {
    array_push($filter, 'AND risktype IN (' . implode(', ', $typesIds) . ')');
}
if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}
if ($clientid != '') {
    array_push($where, 'AND branchid='.$clientid);
}

if (!has_permission('legal_risks', '', 'view')) {
    array_push($where, 'AND tbllegal_risk.addedfrom='.get_staff_user_id());
}

$aColumns =hooks()->apply_filters('contracts_table_sql_columns',$aColumns);


// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tbllegal_risk.id as id', 'trash','branchid','risktitle',));

$output  = $result['output'];
$rResult = $result['rResult'];
foreach ($rResult as $aRow) {

    $row = array();

    // Bulk actions
    $row[] = $aRow['id'];;
    // User id

   // $row[] = '<a href="' . admin_url('legal_risks/legal_risk/' . $aRow['id']) . '">' . $aRow['risktitle'] . '</a>';
        $subjectOutput='';
    
if(has_permission('legal_risks','','view')) {
    $subjectOutput .='<a href="' . admin_url('legal_risks/legal_risk/' . $aRow['id']) . '">' . $aRow['risktitle'] . '</a>';
}
   

    $subjectOutput .= '<div class="row-options">';

  
    if (has_permission('legal_risks', '', 'edit')) {
        $subjectOutput .= ' <a href="' . admin_url('legal_risks/legal_risk/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }

    if (has_permission('legal_risks', '', 'delete')) {
        $subjectOutput .= ' | <a href="' . admin_url('legal_risks/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $subjectOutput .= '</div>';
    $row[] = $subjectOutput;
	$row[] = '<a href="' . admin_url('clients/client/' . $aRow['branchid']) . '">' . $aRow['company'] . '</a>';
	$row[] = $aRow['user_firstname'].' '.$aRow['user_lastname']; 
    $row[] = $aRow['risk_type']; 
    $row[] = app_format_money($aRow['risk_value'], $baseCurrencySymbol);;
	$row[] = $aRow['priority_name'];
    $row[] = $aRow['risk_status'];
	 // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }
    // Table options
    // $options = icon_btn('legal_risks/legal_risk/' . $aRow['id'], 'pencil-square-o');

    // // Show button delete if permission for delete exists
    // if ($hasPermissionDelete) {
    //     $options .= icon_btn('legal_riska/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    // }

    // $row[] = $options;
      $row = hooks()->apply_filters('contracts_table_row_data', $row, $aRow);
    $output['aaData'][] = $row;
}
