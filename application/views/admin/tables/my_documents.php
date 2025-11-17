<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;

$aColumns = array(
    'tbldocuments.id as id',
    'tbldocuments.safe_uniqueno as safe_uniqueno',
    'tbldocuments.file_number as file',
    // '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbloppositeparty INNER JOIN tblproject_opposite_parties ON tbloppositeparty.id = tblproject_opposite_parties.opposite_party_id WHERE tblproject_opposite_parties.project_id = tbldocuments.project_id) as opposite_parties',
    '(SELECT name FROM tbloppositeparty  WHERE tbldocuments.doc_other_party = tbloppositeparty.id ) as opposite_party',
    'subject',
    get_sql_select_client_company(),
    'tbldocument_types.name as type_name',
    'datestart',
    'dateend',
	'(SELECT GROUP_CONCAT(CONCAT(tblfiles.file_name," ^ ",tblfiles.id) SEPARATOR "~") from tblfiles WHERE tblfiles.rel_id=tbldocuments.id  AND rel_type = "document"  ORDER BY tblfiles.id) as file_number',
    'project_id',
    );

$sIndexColumn = "id";
$sTable = 'tbldocuments';

$join = array(
    'LEFT JOIN tblclients ON tblclients.userid = tbldocuments.client',
    'LEFT JOIN tbldocument_types ON tbldocument_types.id = tbldocuments.contract_type'
);

$custom_fields = get_table_custom_fields('documents');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tbldocuments.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
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
    array_push($filter, 'OR dateend IS NOT NULL AND dateend <"'.date('Y-m-d').'" and trash = 0');
}

if ($this->ci->input->post('without_dateend')) {
    array_push($filter, 'OR dateend IS NULL AND trash = 0');
}

$types = $this->ci->documents_model->get_contract_types();
$typesIds = array();
foreach ($types as $type) {
    if ($this->ci->input->post('contracts_by_type_'.$type['id'])) {
        array_push($typesIds, $type['id']);
    }
}
if (count($typesIds) > 0) {
    array_push($filter, 'AND contract_type IN ('.implode(', ', $typesIds).')');
}
$years = $this->ci->documents_model->get_contracts_years();
$yearsArray = array();
foreach ($years as $year) {
    if ($this->ci->input->post('year_'.$year['year'])) {
        array_push($yearsArray, $year['year']);
    }
}
if (count($yearsArray) > 0) {
    array_push($filter, 'AND YEAR(datestart) IN ('.implode(', ', $yearsArray).')');
}

$monthArray = array();
for ($m = 1; $m <= 12; $m++) {
    if ($this->ci->input->post('contracts_by_month_'.$m)) {
        array_push($monthArray, $m);
    }
}
if (count($monthArray) > 0) {
    array_push($filter, 'AND MONTH(datestart) IN ('.implode(', ', $monthArray).')');
}

if (count($filter) > 0) {
    array_push($where, 'AND ('.prepare_dt_filter($filter).')');
}

if ($clientid != '') {
    array_push($where, 'AND client='.$clientid);
}

if (!has_permission('documents', '', 'view')) {
    array_push($where, 'AND tbldocuments.addedfrom='.get_staff_user_id());
}


$aColumns = hooks()->apply_filters('documents_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tbldocuments.id', 'trash', 'client','safe_uniqueno'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();

    $row[] = $aRow['id'];

    $subjectOutput = '<a href="'.admin_url('documents/document/'.$aRow['id']).'">'.$aRow['safe_uniqueno'].' - '.$aRow['subject'].'</a>';
   
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger mleft5 inline-block">'._l('contract_trash').'</span>';
    }
    
    $row[] = $subjectOutput;
    $row[] = $aRow['safe_uniqueno'];
    $row[] = $aRow['file'];
    $row[] = $aRow['opposite_party'];
    $row[] = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $aRow['company'] . '</a>';

    // $case_name =  get_caseproject_name_by_id($aRow['case_id']);
    $row[] = '<a href="'.admin_url('casediary/view/'.$aRow['project_id']).'">'.get_parent_project_namebyid($aRow['project_id']).'</a>';

   

    $row[] = $aRow['type_name'];
   
    //$row[] = format_money($aRow['contract_value'], $baseCurrencySymbol);

    $row[] = _d($aRow['datestart']);

    $row[] = _d($aRow['dateend']);
//  print_r($filesArr);
   //  $row[] = $result_filenames."<br>".$aRow['content'];
    // $row[] = $aRow['id']."<br>".$aRow['content'];
 $filenum = '';
                $explode_filenumber = explode('~',$aRow['file_number']);
                foreach ($explode_filenumber as $cs) {                    
                    $exp = explode('^',$cs);
                    if(isset($exp[0]) && isset($exp[1]))
                    $filenum .=  '<a href="' . site_url('download/file/document/' . trim($exp[1])) . '">' .$exp[0] . '</a><br>';
						
                }
                $row[] = $filenum;
    // Custom fields add values
    foreach($customFieldsColumns as $customFieldColumn){
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $hook = hooks()->apply_filters('contracts_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];
	$options ='';
  /*  $options = icon_btn('documents/document/'.$aRow['id'], 'pencil-square-o');
    if (has_permission('documents', '', 'delete')) {
        $options .= icon_btn('documents/delete/'.$aRow['id'], 'remove', 'btn-danger _delete');
    }*/
	$options .= '<a href="'.admin_url('documents/document/').$aRow['id'].'"  class="btn btn-sm btn-warning mright5"> ' . _l('edit') . '</a>';

     if (has_permission('documents', '', 'delete')) {
	$options .= '<a href="'.admin_url('documents/delete/').$aRow['id'].'"  class="btn btn-sm btn-danger _delete hide">' . _l('delete') . '</a>';
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
