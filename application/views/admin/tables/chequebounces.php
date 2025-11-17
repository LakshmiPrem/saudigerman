<?php

defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency();

$aColumns = [
    db_prefix() . 'chequebounces.id as id',
    'subject',
	 get_sql_select_client_company(),
  'customer_name',
   'customer_code',
  // 'contract_value',
  //  'datestart',
  //  'dateend',
	'status',
	 'statuscolor',
    
    ];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'chequebounces';

$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'chequebounces.client',
	 'LEFT JOIN ' . db_prefix() . 'chequebounces_status ON ' . db_prefix() . 'chequebounces_status.chequestatusid = ' . db_prefix() . 'chequebounces.status',
	 'LEFT JOIN ' . db_prefix() . 'chequebounces_assigned ON ' . db_prefix() . 'chequebounces_assigned.bounceid = ' . db_prefix() . 'chequebounces.id',
  //  'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'contracts.project_id',
   // 'LEFT JOIN ' . db_prefix() . 'contracts_types ON ' . db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type',
];

$custom_fields = get_table_custom_fields('chequebounces');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'chequebounces.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where  = [];
$filter = [];



if ($this->ci->input->post('exclude_trashed_contracts')) {
    array_push($filter, 'AND trash = 0');
}

if ($this->ci->input->post('trash')) {
    array_push($filter, 'AND trash = 1');
}

if ($this->ci->input->post('expired')) {
    array_push($filter, 'AND dateend IS NOT NULL AND dateend <"' . date('Y-m-d') . '" and trash = 0');
}

if ($this->ci->input->post('without_dateend')) {
    array_push($filter, 'AND dateend IS NULL AND trash = 0');
}
if (!has_permission('chequebounces', '', 'view') || $this->ci->input->post('my_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'chequebounces.id IN (SELECT bounceid FROM ' . db_prefix() . 'chequebounces_assigned WHERE staff_id=' . get_staff_user_id() . ')');
}
$statusIds = [];

foreach ($this->ci->chequebounces_model->get_cheque_status() as $status) {
    if ($this->ci->input->post('project_status_' . $status['chequestatusid'])) {
        array_push($statusIds, $status['chequestatusid']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR status IN (' . implode(', ', $statusIds) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}


if ($clientid != '') {
    array_push($where, 'AND client=' . $this->ci->db->escape_str($clientid));
}


$aColumns = hooks()->apply_filters('contracts_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'chequebounces.id', 'trash', 'client', 'hash', 'marked_as_signed', 'other_party'],'GROUP BY tblchequebounces.id');

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $row[] = $aRow['id'];
	$subjectOutput='';
	
if(has_permission('chequebounces','','view') || have_assigned_chequebounces()) {
    $subjectOutput .='<a href="' . admin_url('chequebounces/chequebounce/' . $aRow['id']) . '">' . $aRow['subject'] . '</a>';
}
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger pull-right">' . _l('contract_trash') . '</span>';
    }

    $subjectOutput .= '<div class="row-options">';

  
    if (has_permission('chequebounces', '', 'edit')) {
        $subjectOutput .= ' <a href="' . admin_url('chequebounces/chequebounce/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }

    if (has_permission('chequebounces', '', 'delete')) {
        $subjectOutput .= ' | <a href="' . admin_url('chequebounces/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $subjectOutput .= '</div>';
    $row[] = $subjectOutput;

    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['client']) . '">' . $aRow['company'] . '</a>';
	 $row[] = get_opposite_party_name($aRow['customer_name']);
	

    $row[] = $aRow['customer_code'];

   // $row[] = app_format_money($aRow['contract_value'], $base_currency);

   // $row[] = _d($aRow['datestart']);

   // $row[] = _d($aRow['dateend']);
	$row[] = get_chequebounce_latest_update($aRow['id']);// $aRow['customer_code'];
	$row[]='<span class="label inline-block ticket-status-' . $aRow['status'] . '" style="border:1px solid ' . $aRow['statuscolor'] . '; color:' . $aRow['statuscolor'] . '">' . chequebounce_status_translate($aRow['status']) . '</span>';

   

  

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    if (!empty($aRow['dateend'])) {
        $_date_end = date('Y-m-d', strtotime($aRow['dateend']));
        if ($_date_end < date('Y-m-d')) {
            $row['DT_RowClass'] = 'alert-danger';
        }
    }

    if (isset($row['DT_RowClass'])) {
        $row['DT_RowClass'] .= ' has-row-options';
    } else {
        $row['DT_RowClass'] = 'has-row-options';
    }

    $row = hooks()->apply_filters('contracts_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
