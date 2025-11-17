<?php

defined('BASEPATH') or exit('No direct script access allowed');

$aColumns = [
    //db_prefix().'staff .firstname as employee_name',
    'file_name',
    'CONCAT(' . db_prefix() . 'staff.firstname, \' \', ' . db_prefix() . 'staff.lastname) as uploadedby',
    'dateadded',
    'rel_type',
    'subject',
    ];
    $join = [
        'LEFT JOIN tblstaff on tblstaff.staffid=tblfiles.rel_id',
    ];
    $where = [];
    
   

    if ($this->ci->input->post('related_to')) {
    
        array_push($where, 'AND' . db_prefix() . 'files.rel_type="'.$this->ci->input->post('related_to').'" ');
        
    }

    $start_date=$this->ci->input->post('start_date');
    
    $end_date=$this->ci->input->post('end_date');
    
    if ($start_date!='' && $end_date!='') {
    
        
        $start_date=to_sql_date($start_date);
        $end_date=to_sql_date($end_date);
        
        
        array_push($where,'AND tblfiles.dateadded between "' .$start_date .'" and "' .$end_date.'" ');
        
    }


    





$sIndexColumn = 'id';
$sTable       = db_prefix().'files';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,$join,$where,['id','rel_id','attachment_key']);
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];
    
        
    //$hrefAttr = 'onclick="add_new_leave(' . $aRow['file_name'] . ');"';  
    if($aRow['rel_type']=="customer"){

        $row[]            = '<a href="' . site_url('download/file/client') . '/'.$aRow['attachment_key'].'">' . $aRow['file_name'] . '</a>';  

    }else if($aRow['rel_type']=="contract"){

        $row[]            = '<a href="' . site_url('download/file/contract') . '/'.$aRow['attachment_key'].'">' . $aRow['file_name'] . '</a>';  

    }else if($aRow['rel_type']=="expense"){

        $row[]            = '<a href="' . site_url('download/file/' . $aRow['rel_type']) . '/'.$aRow['rel_id'].'">' . $aRow['file_name'] . '</a>';  

    }else{

        $row[]            = '<a href="' . site_url('download/file/' . $aRow['rel_type']) . '/'.$aRow['id'].'">' . $aRow['file_name'] . '</a>';  
    }
        
       
    $row[]            = $aRow['uploadedby'];
        
        
    $row[]            = $aRow['dateadded'];
        
    $rel_data = get_relation_data($aRow['rel_type'], $aRow['rel_id']);
    
    $rel_values = get_relation_values($rel_data, $aRow['rel_type']);
    //$rel_values=$rel_values['link'];
    //print_r($rel_values['link']);

                
    $row[]      = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
    

    $row[]            = $aRow['subject'];
    

    
       
       

 


    
                     
        
        
  
    
    //$options = icon_btn('roles/role/' . $aRow['roleid'], 'pencil-square-o');
    //$row[]   = $options .= icon_btn('roles/delete/' . $aRow['roleid'], 'remove', 'btn-danger _delete');

    $output['aaData'][] = $row;
    
}
