<?php
defined('BASEPATH') or exit('No direct script access allowed'); 
$aColumns = array(
     'tblproject_instances.instance_name as stage_name',
       'award', 
    'judgement_ruling',
	'judgement_date',
    'decree_order_status', 
    'judgement_ruling_status',
	'addedby',
    'summary',
	'judge_attachment'
    );

$sIndexColumn = "id";
$sTable = 'tblproject_judgement';

$join = array(
    //'INNER JOIN tblprojects ON tblprojects.id = tblproject_judgement.project_id',
    'LEFT JOIN tblproject_instances ON tblproject_instances.id = tblproject_judgement.stage_id',
   // 'LEFT JOIN tblclients ON tblclients.userid = tblprojects.clientid',
    //'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party',
);

$where = array();
$filter = array();
$userId = get_staff_user_id();

if ($project_id != '') {
    array_push($where, "AND tblproject_judgement.project_id='".$project_id."'");
}

/*if ($hearing_type != '' && $hearing_type != 'all' ) {
    array_push($where, "AND tblproject_judgement.stage_id='".$hearing_type."'");
}
if ($clientid != '') {
    array_push($where, "AND tblprojects.clientid='".$clientid."'");
}*/



//$aColumns = do_action('casediary_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,array(
   'tblproject_judgement.project_id as case_id','tblproject_judgement.id as id',

));

$output = $result['output'];
$rResult = $result['rResult'];

//print_r($rResult);

foreach ($rResult as $aRow) {
    $row = array();       
    
   
    $subjectOutput = '<a  href="'.admin_url('projects/view/'.$aRow['case_id']).'/?group=project_judgement" onclick="init_hearing_judgement(' . $aRow['id'] . ',' . $aRow['case_id'] . ');return false;">'.$aRow['stage_name']. '</a>';
    $subjectOutput .= '<div class="row-options">';

    $subjectOutput .= ' <a href="#" onclick="init_hearing_judgement(' . $aRow['id'] . ');return false;">' . _l('view') . '</a>';

    if (has_permission('projects', '', 'edit')) {
        $subjectOutput .= ' | <a href="#" onclick="init_hearing_judgement(' . $aRow['id'] . ');return false;">' . _l('edit') . '</a>';
    }

    if (has_permission('projects', '', 'delete')) {
        $subjectOutput .= ' | <a href="' . admin_url('projects/delete_hearing_judgement/'.$aRow['case_id'].'/'.$aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $subjectOutput .= '</div>';
    $row[] = $subjectOutput;
    $row[] = $aRow['award'];
   
    $row[] = ucwords($aRow['judgement_ruling']);
	$row[] = _d($aRow['judgement_date']);
   //  $row[] = '<a href="'.admin_url('clients/client/'.$aRow['clientid']).'">'. $aRow['company'] . '</a>';
    $row[] = ucwords(str_replace('_',' ',$aRow['decree_order_status']));
    $row[] = ucwords(str_replace('_',' ',$aRow['judgement_ruling_status']));
	 $row[] = get_staff_full_name($aRow['addedby']);
    $row[] = $aRow['summary'];
	$rowName='';
	if (has_permission('projects', '', 'view')) {
        if($aRow['judge_attachment']!=''){
            $rowName = ' <a  href="' . site_url('download/downloadjudgement/' . $aRow['case_id'] .'/'. $aRow[ 'id']) . '"><i class="fa fa-download"></i> ' . _l('download') . '</a>';
        }
			
        }
	$row[]=$rowName;
    $output['aaData'][] = $row;
}



