<?php

defined('BASEPATH') or exit('No direct script access allowed');

$base_currency = get_base_currency();

$aColumns = [
	'1',
    db_prefix() . 'notices.id as id',
	'tracking_number',
    'subject',
    get_sql_select_client_company(),
    db_prefix() . 'notices_types.name as type_name',
    'notice_value',
    'datestart',
    'final_expiry_date',
    db_prefix() . 'projects.name as project_name',
    'signature',
	 '(SELECT tblnotices_status.name  FROM tblnotices_status WHERE tblnotices.status = tblnotices_status.id) as tracking_status',
    'signed_notice_filename',
    ];

$sIndexColumn = 'id';
$sTable       = db_prefix() . 'notices';

$join = [
    'LEFT JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'notices.client',
    'LEFT JOIN ' . db_prefix() . 'projects ON ' . db_prefix() . 'projects.id = ' . db_prefix() . 'notices.project_id',
    'LEFT JOIN ' . db_prefix() . 'notices_types ON ' . db_prefix() . 'notices_types.id = ' . db_prefix() . 'notices.notice_type',
];

$custom_fields = get_table_custom_fields('notices');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'notices.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$where  = [];
$filter = [];

$projectId = $this->ci->input->get('project_id');
if ($projectId) {
    array_push($where, 'AND project_id=' . $this->ci->db->escape_str($projectId));
}

if ($this->ci->input->post('exclude_trashed_notices')) {
    array_push($filter, 'AND trash = 0');
}

if ($this->ci->input->post('trash')) {
    array_push($filter, 'AND trash = 1');
}

if ($this->ci->input->post('expired')) {
    array_push($filter, 'AND dateend IS NOT NULL AND dateend <"' . date('Y-m-d') . '" and trash = 0');
}
if ($this->ci->input->post('unsigned')) { 
    array_push($filter, ' AND signed=0 or marked_as_signed=0');
}

if ($this->ci->input->post('without_dateend')) {
    array_push($filter, 'AND dateend IS NULL AND trash = 0');
}

$types    = $this->ci->notices_model->get_notice_types();
$typesIds = [];
foreach ($types as $type) {
    if ($this->ci->input->post('notices_by_type_' . $type['id'])) {
        array_push($typesIds, $type['id']);
    }
}

if (count($typesIds) > 0) {
    array_push($filter, 'AND notice_type IN (' . implode(', ', $typesIds) . ')');
}

$statuses    = $this->ci->notices_model->get_notice_status();
$statusIds = [];
foreach ($statuses as $status) {
    if ($this->ci->input->post('notices_by_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'AND tblnotices.status IN (' . implode(', ', $statusIds) . ')');
}
$years      = $this->ci->notices_model->get_notices_years();
$yearsArray = [];
foreach ($years as $year) {
    if ($this->ci->input->post('year_' . $year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}
if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR(datestart) IN (' . implode(', ', $yearsArray) . ')');
}

$monthArray = [];
for ($m = 1; $m <= 12; $m++) {
    if ($this->ci->input->post('notices_by_month_' . $m)) {
        array_push($monthArray, $m);
    }
}

if (count($monthArray) > 0) {
    array_push($filter, 'AND MONTH(datestart) IN (' . implode(', ', $monthArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if ($clientid != '' && $clientid!=0 && $clientid!='null') {
    array_push($where, 'AND client=' . $this->ci->db->escape_str($clientid));
}
if ($opposite_party != '') { 
    array_push($where, ' AND other_party=' . $this->ci->db->escape_str($opposite_party));
}
if (!has_permission('notices', '', 'view') || $this->ci->input->post('my_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'notices.id IN (SELECT noticeid FROM ' . db_prefix() . 'notices_assigned WHERE staff_id=' . get_staff_user_id() . ')');
}
/*if (!has_permission('notices', '', 'view')) {
    array_push($where, 'AND ' . db_prefix() . 'notices.addedfrom=' . get_staff_user_id());
}*/

$aColumns = hooks()->apply_filters('notices_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [db_prefix() . 'notices.id', 'trash', 'client', 'hash', 'marked_as_signed', 'project_id','other_party']);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    $row[] = $aRow['id'];
	
    $row[] = $aRow['tracking_number'];

    $subjectOutput = '<a href="' . admin_url('notices/notice/' . $aRow['id']) . '"' . ($projectId ? ' target="_blank"' : '') . '>' . $aRow['subject'] . '</a>';
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger pull-right">' . _l('notice_trash') . '</span>';
    }

    $subjectOutput .= '<div class="row-options">';

    $subjectOutput .= '<a href="' . site_url('notice/' . $aRow['id'] . '/' . $aRow['hash']) . '" target="_blank">' . _l('view') . '</a>';

    if (has_permission('notices', '', 'edit')) {
        $subjectOutput .= ' | <a href="' . admin_url('notices/notice/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }

    if (has_permission('notices', '', 'delete')) {
        $subjectOutput .= ' | <a href="' . admin_url('notices/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $subjectOutput .= '</div>';
    $row[] = $subjectOutput;

    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['client']) . '">' . $aRow['company'] . '</a>';

    $row[] = get_opposite_party_name($aRow['other_party']);

    $row[] = $aRow['type_name'];

    $row[] = app_format_money($aRow['notice_value'], $base_currency);

    $row[] = _d($aRow['datestart']);

    $row[] = _d($aRow['final_expiry_date']);

    $row[] = '<a href="' . admin_url('projects/view/' . $aRow['project_id']) . '">' . $aRow['project_name'] . '</a>';
	
	$row[] = $aRow['tracking_status'];
    if ($aRow['marked_as_signed'] == 1) {
        $row[] = '<span class="text-success">' . _l('marked_as_signed') . '</span>';
    } elseif (!empty($aRow['signature'])) {
        $row[] = '<span class="text-success">' . _l('is_signed') . '</span>';
    } else {
        $row[] = '<span class="text-muted">' . _l('is_not_signed') . '</span>';
    }
	
	$row[]=  '<a href="' . admin_url('notices/notice/' . $aRow['id']) . '?tab=comments">' .total_rows(db_prefix().'notice_comments','notice_id='.$aRow['id']).' '._l('comments').'</a>';
    
    $path = site_url('download/downloadsigned_agreement/'. $aRow['id']);
    if($aRow['signed_notice_filename'] != ''){
        $row[] = '<a download href="'.$path.'"  class="btn btn-primary maleft10"><i class="fa fa-download"></i>'._l('download').'</a>';
    }else{
        $row[] = '';
    }
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

    $row = hooks()->apply_filters('notices_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
