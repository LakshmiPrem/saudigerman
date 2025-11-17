<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    //db_prefix().'staff .firstname as employee_name',
    'file_name',
    'tblprojects.name as project_name',
    'dateadded',
    'subject',
    'tbldocument_types.name as document_type',
    'issue_date',
    'expiry_date',
    ];
    $join = [
        'INNER JOIN tblprojects on tblprojects.id=tblproject_files.project_id',
        'LEFT JOIN tbldocument_types on tbldocument_types.id=tblproject_files.document_type',
    ];
    $where = [];
    
   

    

    $start_date=$this->ci->input->post('start_date');
    
    $end_date=$this->ci->input->post('end_date');
    
    if ($start_date!='' && $end_date!='') {
    
        $start_date=to_sql_date($start_date);
        $end_date=to_sql_date($end_date);
        
        
        array_push($where,'AND tblproject_files.dateadded between "' .$start_date .'" and "' .$end_date.'" ');
        
    }

    $expiry_start_date=$this->ci->input->post('expiry_start_date');
    
    $expiry_end_date=$this->ci->input->post('expiry_end_date');
    
    if ($expiry_start_date!='' && $expiry_end_date!='') {
    
        $expiry_start_date=to_sql_date($expiry_start_date);
        $expiry_end_date=to_sql_date($expiry_end_date);
        
        
        array_push($where,'AND tblproject_files.expiry_date between "' .$expiry_start_date .'" and "' .$expiry_end_date.'" ');
        
    }


    





$sIndexColumn = 'id';
$sTable       = db_prefix().'project_files';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,$join,$where,['tblproject_files.id,tblproject_files.project_id']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    
        
    //$hrefAttr = 'onclick="add_new_leave(' . $aRow['file_name'] . ');"';  
    
    //$row[]            = '<a href="' . site_url('download/file/projects') . '/'.$aRow['id'].'">' . $aRow['file_name'] . '</a>';  
    //$row[]            = '<a href="' . site_url('projects/download_all_files/' . $aRow['id']) . '">' . $aRow['file_name'] . '</a>';      
    //$row[]            = '<a href="' . admin_url('projects/download_all_files/'.$aRow['id']).'">' . $aRow['file_name'] . '</a>'; 
    $row[]='<a href="' . site_url('uploads/projects/' . $aRow['project_id'] . '/' . $aRow['file_name']) . '" download>' . $aRow['file_name'] . '</a>'; 

    $row[]            = $aRow['project_name'];
        
        
    $row[]            = $aRow['dateadded'];
        

    $row[]            = $aRow['subject'];

    $row[]            = $aRow['document_type'];

    $row[]            = $aRow['issue_date'];

    $row[]            = $aRow['expiry_date'];
    

    
       
       

 


    
                     
        
        
  
    
    //$options = icon_btn('roles/role/' . $aRow['roleid'], 'pencil-square-o');
    //$row[]   = $options .= icon_btn('roles/delete/' . $aRow['roleid'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
    
}
