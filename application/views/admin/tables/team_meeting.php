<?php
defined('BASEPATH') or exit('No direct script access allowed'); 
$aColumns = array(
    'id',
    'subject',
    'start_date',
    'end_date',
    'meeting_id',
    'meeting_url',
    'dateadded',
    );

$sIndexColumn = "id";
$sTable = 'tblclient_teams_meeting';

$join = array();

$where = array();
$filter = array();
$userId = get_staff_user_id();

if ($clientid != '') {
    array_push($where, "AND clientid='".$clientid."'");
}

//$aColumns = do_action('casediary_table_sql_columns',$aColumns);

// Fix for big queries. Some hosting have max_join_limit

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where,array());

$output = $result['output'];
$rResult = $result['rResult'];

//print_r($rResult);

foreach ($rResult as $aRow) {
    $row = array();       
    
    $row[] = $aRow['id'];
    $row[] = $aRow['subject'];
    $row[] = $aRow['start_date'];
    $row[] = $aRow['end_date'];
    $row[] = $aRow['meeting_id'];
    $row[] = '<a href="' . $aRow['meeting_url'] . '" target="_blank">Meeting Link</a>';
    $row[] = $aRow['dateadded'];
    
    $output['aaData'][] = $row;
}



