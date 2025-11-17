<?php
defined('BASEPATH') OR exit('No direct script access allowed');
$aColumns     = array(
    'name','active',
    );
  $where = [];
    
  //  array_push($where, ' AND ' . db_prefix() . 'templates.type="contracts"');
array_push($where, ' AND ' . db_prefix() . 'templates.is_legalclause=1');
$sIndexColumn = "id";
$sTable       = 'tbltemplates';
$result  = data_tables_init($aColumns, $sIndexColumn, $sTable,array(),$where,array('id','type','addedfrom'));
$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = array();
  	 $row[] = '<a href="#" onclick="edit_allclosure('.$aRow['id'].',2); return false;" data-name="'.$aRow['name'].'" data-type="'.$aRow['type'].'">' . $aRow['name']. '</a> ';
		
	$options='';
	if($aRow['addedfrom'] == get_staff_user_id() || is_admin()){
    $options = icon_btn('#', 'pencil-square-o','btn-default',array('onclick'=>'edit_allclosure('.$aRow['id'].',2); return false;','data-name'=>$aRow['name'], 'data-type' => $aRow['type']));
   $options .= icon_btn('#', 'remove','btn-danger',array('onclick'=>'delete_allclosure('.$aRow['id'].'); return false;','data-name'=>$aRow['name'], 'data-type' => $aRow['type']));
	
	}
	$row[]=$options;

    $output['aaData'][] = $row;
}
