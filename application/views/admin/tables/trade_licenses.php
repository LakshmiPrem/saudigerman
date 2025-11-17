<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;

$aColumns = array(
    'tbltrade_licenses.id as id',
    'license_no',
    get_sql_select_client_company(),
    'share_capital',
    'issue_date',
    'expiry_date',
    'license_cost'
    );

$sIndexColumn = "id";
$sTable = 'tbltrade_licenses';

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tbltrade_licenses.client',
);

$custom_fields = get_table_custom_fields('trade_licenses');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tbltrade_licenses.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
}

$where = array();
$filter = array();

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
$monthArray = [];
for ($m = 1; $m <= 12; $m++) {
    if ($this->ci->input->post('contracts_by_month_' . $m)) {
        array_push($monthArray, $m);
    }
}

if (count($monthArray) > 0) {
    array_push($filter, 'AND MONTH(datestart) IN (' . implode(', ', $monthArray) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if ($clientid != '') {
    array_push($where, 'AND client='.$clientid);
}

if (!has_permission('trade_licenses', '', 'view')) {
    array_push($where, 'AND tbltrade_licenses.addedfrom='.get_staff_user_id());
}

$aColumns =hooks()->apply_filters('contracts_table_sql_columns',$aColumns);


// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tbltrade_licenses.id', 'trash', 'client'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $row[] = $aRow['id'];

    $subjectOutput = '<a href="'.admin_url('trade_licenses/trade_license/'.$aRow['id']).'">'.$aRow['license_no'].'</a>';
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger mleft5 inline-block">'._l('contract_trash').'</span>';
    }

    $row[] = $subjectOutput;

    $row[] = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $aRow['company'] . '</a>';


    $row[] = _d($aRow['issue_date']);

    $row[] = _d($aRow['expiry_date']);
    $row[] = app_format_money($aRow['share_capital'], $baseCurrencySymbol);
    $row[] = app_format_money($aRow['license_cost'], $baseCurrencySymbol);
    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }



    $options = icon_btn('trade_licenses/trade_license/'.$aRow['id'], 'pencil-square-o');
    if (has_permission('trade_licenses', '', 'delete')) {
        $options .= icon_btn('trade_licenses/delete/'.$aRow['id'], 'remove', 'btn-danger _delete');
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
