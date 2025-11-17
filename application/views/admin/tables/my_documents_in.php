<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;

$aColumns = array(
    'tbldocuments_in.id as id',
    'subject',
    get_sql_select_client_company(),
    'safe_documentid as type_name',
    'sent_by',
	'sent_date',
	'approved_by',
    'received_by',
	'received_date',
	 'is_approve',
	'(SELECT GROUP_CONCAT(CONCAT(tblfiles.file_name," ^ ",tblfiles.id) SEPARATOR "~") from tblfiles WHERE tblfiles.rel_id=tbldocuments_in.safe_documentid  AND rel_type = "document"  ORDER BY tblfiles.id) as file_number',
	// '(SELECT GROUP_CONCAT(tblfiles.id SEPARATOR ",") FROM tblfiles WHERE tblfiles.rel_id=tbldocuments_in.safe_documentid  AND rel_type = "document" ) as file_name',
   
   
    );

$sIndexColumn = "id";
$sTable = 'tbldocuments_in';

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tbldocuments_in.client',
   // 'LEFT JOIN tbldocument_types ON tbldocument_types.id = tbldocuments_in.document_type'
);

$custom_fields = get_table_custom_fields('documents');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tbldocuments_in.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
}

$where = array();
$filter = array();



$requested_start_date=to_sql_date($this->ci->input->post('requested_date_from'));

$requested_end_date=to_sql_date($this->ci->input->post('requested_date_to'));

if ($requested_start_date!='' && $requested_end_date!='') {

    // $start_date=date('Y-m-d', strtotime($start_date));
    // $end_date=date('Y-m-d', strtotime($end_date));
    // $requested_start_date=date('Y-m-d', strtotime($requested_start_date));
    // $requested_end_date=date('Y-m-d', strtotime($requested_end_date));
    
    array_push($where,'AND sent_date between "' .$requested_start_date .'" and "' .$requested_end_date.'"');
    //print_r($where);
    //array_push($where ,' AND tblprojects.project_created between "' .$start_date .'" and "' .$end_date.'"');
}

$return_date_from=to_sql_date($this->ci->input->post('return_date_from'));

$return_date_to=to_sql_date($this->ci->input->post('return_date_to'));

if ($return_date_from!='' && $return_date_to!='') {
    
     
    array_push($where,'AND received_date between "' .$return_date_from .'" and "' .$return_date_to.'"');
    //print_r($where);
    //array_push($where ,' AND tblprojects.project_created between "' .$start_date .'" and "' .$end_date.'"');
}

if ($this->ci->input->post('exclude_trashed_contracts')) {
    array_push($filter, 'AND trash = 0');
}
if ($this->ci->input->post('trash')) {
    array_push($filter, 'OR trash = 1');
}
/*if ($this->ci->input->post('expired')) {
    array_push($filter, 'OR dateend IS NOT NULL AND dateend <"'.date('Y-m-d').'" and trash = 0');
}*/

/*if ($this->ci->input->post('without_dateend')) {
    array_push($filter, 'OR dateend IS NULL AND trash = 0');
}*/

$types = $this->ci->documents_model->get_contract_types();
$typesIds = array();
foreach ($types as $type) {
    if ($this->ci->input->post('contracts_by_type_'.$type['id'])) {
        array_push($typesIds, $type['id']);
    }
}
if (count($typesIds) > 0) {
    array_push($filter, 'AND document_type IN ('.implode(', ', $typesIds).')');
}
$years = $this->ci->documents_model->get_contracts_years();
$yearsArray = array();
foreach ($years as $year) {
    if ($this->ci->input->post('year_'.$year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}
/*if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR(received_date) IN ('.implode(', ', $yearsArray).')');
}*/

$monthArray = array();
for ($m = 1; $m <= 12; $m++) {
    if ($this->ci->input->post('contracts_by_month_'.$m)) {
        array_push($monthArray, $m);
    }
}
if (count($monthArray) > 0) {
    array_push($filter, 'AND MONTH(received_date) IN ('.implode(', ', $monthArray).')');
}

if (count($filter) > 0) {
    array_push($where, 'AND ('.prepare_dt_filter($filter).')');
}

if ($clientid != '') {
    array_push($where, 'AND client='.$clientid);
}
if ($safeid != '') {
    array_push($where, 'AND safe_documentid='.$safeid);
}
if (!has_permission('documents', '', 'view')) {
    array_push($where, 'AND tbldocuments_in.addedfrom='.get_staff_user_id());
}

$aColumns = hooks()->apply_filters('documents_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tbldocuments_in.id', 'trash', 'client','safe_documentid', 'approved_by',));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $row[] = $aRow['id'];

    $subjectOutput = '<a href="'.admin_url('documents/document_in/'.$aRow['id']).'">'.$aRow['subject'].'</a>';
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger mleft5 inline-block">'._l('contract_trash').'</span>';
    }

    $row[] = $subjectOutput;

    $row[] = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $aRow['company'] . '</a>';
    //  $case_name =  get_caseproject_name_by_id($aRow['case_id']);
    // $row[] = '<a href="'.admin_url('casediary/view/'.$aRow['case_id']).'">'. $case_name . '</a>';

   // $row[] = '<a href="'.admin_url('casediary/view/'.$aRow['case_id']).'">'. $aRow['case_id'] . '</a>';

    $row[] = get_safedocumentname($aRow['type_name']);
   
    //$row[] = format_money($aRow['contract_value'], $baseCurrencySymbol);
	 $row[] = get_staff_full_name($aRow['sent_by']);
    $row[] = _d($aRow['sent_date']);
    $row[] = get_staff_full_name($aRow['approved_by']);
    $row[] = get_staff_full_name($aRow['received_by']);
	$row[] = _d($aRow['received_date']);
	$filenum = '';
	if($aRow['is_approve']!=0){
	 
                $explode_filenumber = explode('~',$aRow['file_number']);
                foreach ($explode_filenumber as $cs) {                    
                    $exp = explode('^',$cs);
                    if(isset($exp[0]) && isset($exp[1]))
                    $filenum .=  '<a href="' . site_url('download/file/document/' . trim($exp[1])) . '">' .$exp[0] . '</a><br>';
						
                }
		$row[] = $filenum;
	}
	else{
	$row[] = _l('waiting_for_approval');	
	}
             //   $row[] = $filenum;
	// $row[] = '<a href="' . site_url('download/file/download/' . $aRow['file_name']) . '">' .$aRow['file_name'] . '</a>';
   //  $row[] = $result_filenames."<br>";

    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook = hooks()->apply_filters('contracts_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];

    $options = icon_btn('documents/document_in/'.$aRow['id'], 'pencil-square-o');
    if (has_permission('documents', '', 'delete')) {
        $options .= icon_btn('documents/delete/'.$aRow['id'].'/in', 'remove', 'btn-danger _delete hide');
    }
    $row[] = $options;

    if (!empty($aRow['dateend'])) {
        $_date_end = date('Y-m-d', strtotime($aRow['dateend']));
        if ($_date_end < date('Y-m-d')) {
            $row['DT_RowClass'] = 'alert-danger';
        }
    }

    $output['aaData'][] = $row;
}
