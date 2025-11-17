<?php
defined('BASEPATH') or exit('No direct script access allowed'); 
$aColumns = array(
    'tblhearings.hearing_date as hearing_date',
  //  'tblproject_instances.instance_name as hearing_type',
    'tblhearings.subject as hearing_subject',
   // 'tblclients.company as company', 
   
    'court_no as court_no',
	'tblhearings.lawyer_id as assigned_lawyer',
  //  '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM tbloppositeparty INNER JOIN tblproject_opposite_parties ON tbloppositeparty.id = tblproject_opposite_parties.opposite_party_id WHERE tblproject_opposite_parties.project_id = tblprojects.id) as opposite_party',  
    'tblhearings.proceedings as proceedings',
    );

$sIndexColumn = "id";
$sTable = 'tblhearings';

$join = array(
    'INNER JOIN tblprojects ON tblprojects.id = tblhearings.project_id',
   // 'LEFT JOIN tblproject_instances ON tblproject_instances.id = tblhearings.h_instance_id',
    'LEFT JOIN tblclients ON tblclients.userid = tblprojects.clientid',
    //'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
);

$where = array();
$filter = array();
$userId = get_staff_user_id();

if ($project_id != '') {
    array_push($where, "AND tblhearings.project_id=".$project_id);
}

/*if ($hearing_type != '' && $hearing_type != 'all' ) {
    array_push($where, "AND tblhearings.hearing_type='".$hearing_type."'");
}*/
if ($hearing_instance != '' ) {
    array_push($where, "AND tblhearings.h_instance_id=".$hearing_instance);
}
if ($clientid != '') {
    array_push($where, "AND tblprojects.clientid='".$clientid."'");
}



//$aColumns = do_action('casediary_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,array(
    'tblprojects.clientid as clientid','tblhearings.project_id as case_id','tblhearings.id as id','tblhearings.h_instance_id as hearing_stage','tblprojects.project_stage as project_stage'

));

$output = $result['output'];
$rResult = $result['rResult'];

//print_r($rResult);

foreach ($rResult as $aRow) {
    $row = array();       
    $row[] = _d($aRow['hearing_date']);
  //  $row[] = $aRow['hearing_type'];
    $subjectOutput = '<a  href="'.admin_url('projects/view/'.$aRow['case_id']).'/?group=hearings" onclick="init_hearing(' . $aRow['id'] . ',' . $aRow['case_id'] . ');return false;">'.$aRow['hearing_subject']. '</a>';
    $subjectOutput .= '<div class="row-options">';

    $subjectOutput .= ' <a href="#" onclick="init_hearing(' . $aRow['id'] . ');return false;">' . _l('view') . '</a>';

    if (has_permission('projects', '', 'edit')) {
        $subjectOutput .= ' | <a href="#" onclick="init_hearing(' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';
    }

    if (has_permission('projects', '', 'delete')) {
        $subjectOutput .= ' | <a href="' . admin_url('projects/delete_hearing/'.$aRow['case_id'].'/'.$aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $subjectOutput .= '</div>';
    $row[] = $subjectOutput;
   
   
    //$row[] = $aRow['court_name'];
   //  $row[] = '<a href="'.admin_url('clients/client/'.$aRow['clientid']).'">'. $aRow['company'] . '</a>';
   // $row[] = $aRow['court_fee'];
    $row[] = $aRow['court_no'];

    $row[] = get_staff_full_name($aRow['assigned_lawyer']);
    $row[] = $aRow['proceedings'];
	//$row[] = get_casedetails_complete_update($aRow['case_id'],$aRow['id']);
    $options='';
    if($aRow['hearing_stage']==$aRow['project_stage']){
	$options= '<a href="#" data-toggle="tooltip" data-title="'. _l('hearing_comment').'" class="btn btn-info  btn-icon hide" onclick="new_hearing_comments(' . $aRow['id'] . ',' . $aRow['case_id'] . '); return false;">
            <i class="hide fa fa-comment"></i>
                </a> <a href="#" data-toggle="tooltip" data-title="'. _l('set_hearing_judgement').'" class="btn btn-warning  btn-icon" onclick="init_hearing_judgement(' . $aRow['id'] . '); return false;">
            <i class="fa fa-gavel"></i>
                </a>';
            }
  //  $row[]=$options;
    $output['aaData'][] = $row;
}



