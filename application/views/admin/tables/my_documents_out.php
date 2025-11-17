<?php
defined('BASEPATH') or exit('No direct script access allowed');

$this->ci->load->model('currencies_model');
$baseCurrencySymbol = $this->ci->currencies_model->get_base_currency()->symbol;

$aColumns = array(
    'tblcommunication.id as id',
    'subject',
    // get_sql_select_client_company(),
    // 'tbldocument_types.name as type_name',
    'msg_from',
    'msg_to',
    'date',
    'date_received',
    // 'sent_to',
    // 'content',
     'mode_of_msg',
    );

$sIndexColumn = "id";
$sTable = 'tblcommunication';

$join = array(
    // 'LEFT JOIN tblclients ON tblclients.userid = tbldocuments_out.client',
    'LEFT JOIN tbldocument_types ON tbldocument_types.id = tblcommunication.document_type'
);

$custom_fields = get_table_custom_fields('documents');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_'.$key);
    array_push($customFieldsColumns,$selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);

    array_push($join, 'LEFT JOIN tblcustomfieldsvalues as ctable_'.$key . ' ON tblcommunication.id = ctable_'.$key . '.relid AND ctable_'.$key . '.fieldto="'.$field['fieldto'].'" AND ctable_'.$key . '.fieldid='.$field['id']);
}

$where = array();
$filter = array();

if ($this->ci->input->post('exclude_trashed_contracts')) {
    array_push($filter, 'AND trash = 0');
}
// if ($this->ci->input->post('trash')) {
//     array_push($filter, 'OR trash = 1');
// }
if ($this->ci->input->post('actioned')) {
    array_push($filter, 'and status = 1');
}
if ($this->ci->input->post('pending')) {
    array_push($filter, 'and status = 2');
}
if ($this->ci->input->post('not_actioned')) {
    array_push($filter, 'and status = 3');
}
/*if ($this->ci->input->post('expired')) {
    array_push($filter, 'OR dateend IS NOT NULL AND dateend <"'.date('Y-m-d').'" and trash = 0');
}*/

/*if ($this->ci->input->post('without_dateend')) {
    array_push($filter, 'OR dateend IS NULL AND trash = 0');
}
*/
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
    array_push($filter, 'AND YEAR(date) IN ('.implode(', ', $yearsArray).')');
}*/

$monthArray = array();
for ($m = 1; $m <= 12; $m++) {
    if ($this->ci->input->post('contracts_by_month_'.$m)) {
        array_push($monthArray, $m);
    }
}
if (count($monthArray) > 0) {
    array_push($filter, 'AND MONTH(date) IN ('.implode(', ', $monthArray).')');
}

if (count($filter) > 0) {
    array_push($where, 'AND ('.prepare_dt_filter($filter).')');
}

if ($clientid != '') {
    array_push($where, 'AND client='.$clientid);
}

if (!has_permission('communication', '', 'view')) {
    array_push($where, 'AND tblcommunication.addedfrom='.get_staff_user_id());
}

$filesArr = $this->ci->documents_model->get_attached_documents_out();
$fileNames = array();
$rel_arr=array();
$result_filenames='';


$aColumns = hooks()->apply_filters('documents_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('tblcommunication.id', 'trash', 'client'));

$output = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
if(sizeof($filesArr)>0){
for($t=0;$t<sizeof($filesArr);$t++) {
   $filename ='';
   $path =CONTRACTS_UPLOADS_FOLDER.$filesArr[$t]['rel_id']."/";
   $href_url = site_url('download/file/documents_out/'.$filesArr[$t]['id']);
   //$data .= '<a href="'.$href_url.'">'.$attachment['file_name'].'</a>';
   if(!in_array($filesArr[$t]['rel_id'], $rel_arr)){
        for($k=0;$k<sizeof($filesArr);$k++) {
            if($filesArr[$k]['rel_id'] == $filesArr[$t]['rel_id']){
                $filename .=  '<a href="'.$href_url.'">'.$filesArr[$k]['file_name'].'</a>,';
            }
        }
    $fileNames[$filesArr[$t]['rel_id']] = trim($filename,","); 
    $rel_arr[] =$filesArr[$t]['rel_id'];   
   }
    
}
$result_filenames= $fileNames[$aRow['id']];
}
    $row[] = $aRow['id'];

    $subjectOutput = '<a href="'.admin_url('documents/document_out/'.$aRow['id']).'">'.$aRow['subject'].'</a>';
    if ($aRow['trash'] == 1) {
        $subjectOutput .= '<span class="label label-danger mleft5 inline-block">'._l('contract_trash').'</span>';
    }

    $row[] = $subjectOutput;

    // $row[] = '<a href="'.admin_url('clients/client/'.$aRow['client']).'">'. $aRow['company'] . '</a>';
    // //  $case_name =  get_caseproject_name_by_id($aRow['case_id']);
    // // $row[] = '<a href="'.admin_url('casediary/view/'.$aRow['case_id']).'">'. $case_name . '</a>';
    // $row[] = '<a href="'.admin_url('casediary/view/'.$aRow['case_id']).'"></a>';
   

    $row[] = $aRow['msg_from'];
    $row[] = $aRow['msg_to'];
    //$row[] = format_money($aRow['contract_value'], $baseCurrencySymbol);date_received

    $row[] = _d($aRow['date']);
    $row[] = _d($aRow['date_received']);
    // print_r($aRow['mode_of_msg']);
    $row[]=get_mode_of_msg_name($aRow['mode_of_msg']);
    
    // $row[] = get_staff_full_name($aRow['sent_by']);
    //  $row[] = get_staff_full_name($aRow['address_to']);

    //  $row[] = $result_filenames."<br>".$aRow['content'];

    // Custom fields add values
    // foreach($customFieldsColumns as $customFieldColumn){
    //     $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    // }

    $hook = hooks()->apply_filters('contracts_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];

    $options = icon_btn('documents/document_out/'.$aRow['id'], 'pencil-square-o');
    if (has_permission('documents', '', 'delete')) {
        $options .= icon_btn('documents/delete/'.$aRow['id'].'/out', 'remove', 'btn-danger _delete');
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
