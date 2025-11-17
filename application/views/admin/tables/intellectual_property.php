<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;

$aColumns = array(
    'tblintellectual_property.id as id',
    'subject',
    'ip_status',
    get_sql_select_client_company(),
    'file_no',
    'issue_date',
    'expiry_date',
   '(select name from tblip_categories where tblip_categories.id=tblintellectual_property.ip_type) as ip_type'
    );

$sIndexColumn = "id";
$sTable = 'tblintellectual_property';

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tblintellectual_property.client',
);

$custom_fields = get_table_custom_fields('trade_licenses');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tblintellectual_property.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
}

$where = array();
$filter = array();

if ($this->ci->input->post('exclude_trashed_contracts')) {
    array_push($filter, 'AND trash = 0');
}
if ($this->ci->input->post('trash')) {
    array_push($where, 'OR trash = 1');
}
if ($this->ci->input->post('expired')) {
    array_push($where, 'OR expiry_date IS NOT NULL AND expiry_date <"'.date('Y-m-d').'" and trash = 0');
}

if ($this->ci->input->post('without_dateend')) {
    array_push($where, 'OR expiry_date IS NULL AND trash = 0');
}
$monthArray = [];
for ($m = 1; $m <= 12; $m++) {
    if ($this->ci->input->post('contracts_by_month_' . $m)) {
        array_push($monthArray, $m);
    }
}

if (count($monthArray) > 0) {
    array_push($where, 'AND MONTH(issue_date) IN (' . implode(', ', $monthArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if ($clientid != '') {
    array_push($where, 'AND client='.$clientid);
}

if (!has_permission('trade_licenses', '', 'view')) {
    array_push($where, 'AND tblintellectual_property.addedfrom='.get_staff_user_id());
}

$aColumns = hooks()->apply_filters('contracts_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblintellectual_property.id', 'trash', 'client'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $row[] = $aRow['id'];

    $subjectOutput = '<a href="'.admin_url('intellectual_property/intellectual_property/'.$aRow['id']).'">'.$aRow['subject'].'</a>';
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger mleft5 inline-block">'._l('contract_trash').'</span>';
    }

    $row[] = $subjectOutput;

    $row[] = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $aRow['company'] . '</a>';

    $row[] = _l($aRow['ip_status']);
    $row[] = _d($aRow['issue_date']);

    $row[] = _d($aRow['expiry_date']);
    $row[] = $aRow['file_no'];
    $row[] = $aRow['ip_type'];
    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

  

    $options = icon_btn('intellectual_property/intellectual_property/'.$aRow['id'], 'pencil-square-o');
    if (has_permission('intellectual_property', '', 'delete')) {
        $options .= icon_btn('intellectual_property/delete/'.$aRow['id'], 'remove', 'btn-danger _delete');
    }
    $row[] = $options;

    if (!empty($aRow['expiry_date'])) {
        $_date_end = date('Y-m-d', strtotime($aRow['expiry_date']));
        if ($_date_end < date('Y-m-d')) {
            $row['DT_RowClass'] = 'alert-danger';
        }
    }

    $output['aaData'][] = $row;
}
