<?php
defined('BASEPATH') or exit('No direct script access allowed');

$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;

$aColumns     = array(
    'tblservice_agreements.id as id',
    'subject',
    'proposal_to', 
    'total',
    'file_no_agreement',
    'date',
    'valid_for',
    'open_till',
    'datecreated',
    'status',
);

$sIndexColumn = "id";
$sTable       = 'tblservice_agreements';

$where = array();
$filter = array();

if ($this->ci->input->post('leads_related')) {
    array_push($filter, 'OR rel_type="lead"');
}
if ($this->ci->input->post('customers_related')) {
    array_push($filter, 'OR rel_type="customer"');
}
if ($this->ci->input->post('expired')) {
    array_push($filter, 'OR open_till IS NOT NULL AND open_till <"'.date('Y-m-d').'" AND status NOT IN(2,3)');
}

$statuses = $this->ci->agreements_model->get_statuses();
$statusIds = array();

foreach ($statuses as $status) {
    if ($this->ci->input->post('proposals_'.$status)) {
        array_push($statusIds, $status);
    }
}
if (count($statusIds) > 0) {
    array_push($filter, 'AND status IN (' . implode(', ', $statusIds) . ')');
}

$agents = $this->ci->agreements_model->get_sale_agents();
$agentsIds = array();
foreach ($agents as $agent) {
    if ($this->ci->input->post('sale_agent_'.$agent['sale_agent'])) {
        array_push($agentsIds, $agent['sale_agent']);
    }
}
if (count($agentsIds) > 0) {
    array_push($filter, 'AND assigned IN (' . implode(', ', $agentsIds) . ')');
}

$years = $this->ci->agreements_model->get_proposals_years();
$yearsArray = array();
foreach ($years as $year) {
    if ($this->ci->input->post('year_'.$year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}
if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR(date) IN ('.implode(', ', $yearsArray).')');
}

if (count($filter) > 0) {
    array_push($where, 'AND ('.prepare_dt_filter($filter).')');
}

if (!has_permission('proposals', '', 'view')) {
    $userWhere = 'AND ((addedfrom='.get_staff_user_id().' AND addedfrom IN (SELECT staffid FROM tblstaffpermissions JOIN tblpermissions ON tblpermissions.permissionid=tblstaffpermissions.permissionid WHERE tblpermissions.name = "proposals" AND can_view_own=1))';
    if (get_option('allow_staff_view_proposals_assigned') == 1) {
        $userWhere .= ' OR assigned='.get_staff_user_id();
    }
    $userWhere .= ')';
    array_push($where, $userWhere);
}

$join = array();

//$aColumns = do_action('proposals_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit




$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'currency',
    'rel_id',
    'rel_type',
    'invoice_id',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $numberOutput = '<a href="' . admin_url('agreements/agreement/' . $aRow['id']) . '" >' . $aRow['id'] . '</a>';

    if ($aRow['invoice_id']) {
        $numberOutput .= '<br /> <span class="hide"> - </span><span class="text-success">' . _l('estimate_invoiced') . '</span>';
    }

    $row[] = $numberOutput;

    $row[] = '<a href="' . admin_url('agreements/agreement/' . $aRow['id']) . '" >' . $aRow['subject'] . '</a>';

    $row[] = $aRow['file_no_agreement'];

    if ($aRow['rel_type'] == 'lead') {
        $toOutput = '<a href="#" onclick="init_lead('.$aRow['rel_id'].');return false;" target="_blank" data-toggle="tooltip" data-title="'._l('lead').'">'.$aRow['proposal_to'].'</a>';
    } elseif ($aRow['rel_type'] == 'customer') {
        $toOutput = '<a href="'.admin_url('clients/client/'.$aRow['rel_id']).'" target="_blank" data-toggle="tooltip" data-title="'._l('client').'">'.$aRow['proposal_to'].'</a>';
    }

    $row[] = $toOutput;

    $row[] = $aRow['valid_for'];

    $row[] = _d($aRow['date']);

    //$row[] = _d($aRow['open_till']);

    $row[] = _d($aRow['date']);

    $row[] = _d($aRow['datecreated']);
    $path =  base_url('uploads/service_agreement/').$aRow['id'].'/Service_Agreement.docx';
    $file_path   = get_upload_path_by_type('service_agreement').$aRow['id'].'/';
    if(file_exists($file_path.'Service_Agreement.docx')){ 
        $row[] = '<a href="'.$path.'"  class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('service_agreement').'" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i></a>';
    }else{
        $row[] = '-';
    }

    //$row[] = format_proposal_status($aRow['status']);


    $output['aaData'][] = $row;
}
