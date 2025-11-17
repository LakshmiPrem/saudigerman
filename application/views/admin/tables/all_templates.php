<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name','agreement_type','active',
    );
  $where = [];
    
    array_push($where, ' AND ' . db_prefix() . 'templates.type="contracts" AND is_legalclause=0');
    if ($this->ci->input->post('agreement_type')) {
    
        array_push($where, ' AND ' . db_prefix() . 'templates.agreement_type="'.$this->ci->input->post('agreement_type').'" ');
        
    }
$sIndexColumn = "id";
$sTable       = 'tbltemplates';

$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),$where,array('id','type','addedfrom'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
  	 $row[] = '<a href="#" onclick="edit_alltemplate('.$aRow['id'].',2); return false;" data-name="'.$aRow['name'].'" data-type="'.$aRow['type'].'" data-category="'.$aRow['agreement_type'].'">' . $aRow['name']. '</a> '. '<span class="badge pull-right">'.total_rows('tblcontracts',array('contract_template_id'=>$aRow['id'])).'</span>';
	$row[]= get_contracttype_name_by_id($aRow['agreement_type']);
	
	$options='';
	if($aRow['addedfrom'] == get_staff_user_id() || is_admin()){
    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_alltemplate('.$aRow['id'].',2); return false;','data-name'=>$aRow['name'], 'data-type' => $aRow['type'],'data-category' => $aRow['agreement_type']));
   $options .= icon_btn('#', 'remove','btn-danger',array('onclick'=>'delete_alltemplate('.$aRow['id'].'); return false;','data-name'=>$aRow['name'], 'data-type' => $aRow['type'],'data-category' => $aRow['agreement_type']));
	
	}
	$row[]=$options;

    $output['aaData'][] = $row;
}
