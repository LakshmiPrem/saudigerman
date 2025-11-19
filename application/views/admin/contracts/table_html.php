<?php defined('BASEPATH') or exit('No direct script access allowed');
$type=isset($type)?$type:$this->input->get('type');
if($type=='contracts'){
  $table_data = array(
 _l('the_number_sign'),
 _l('contract_list_subject'),
 array(
   'name'=>_l('contract_list_client'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
 array(
   'name'=>_l('other_party'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
  _l('purchaser'),
  _l('contract_department'),
 _l('contract_types_list_name'),
 _l('contract_value'),
 _l('contract_list_start_date'),
 _l('contract_list_end_date'),
 _l('payment_terms'),

 /*(!isset($project) ? _l('project') : array(
   'name'=>_l('project'),
   'th_attrs'=>array('class'=>'not_visible')
 )),*/
 _l('account_status'),
 _l('contract_status'),
 //_l('total_comments'),
 _l('signed_contract')
);

}else{
  $table_data = array(
 _l('the_number_sign'),
 _l('contract_list_subject'),
 array(
   'name'=>_l('contract_list_client'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
 array(
   'name'=>_l('other_party'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
  
 _l('signed_po')
);

}

$custom_fields = get_custom_fields('contracts',array('show_on_table'=>1));

foreach($custom_fields as $field){
 	array_push($table_data,$field['name']);
}

$table_data = hooks()->apply_filters('contracts_table_columns', $table_data);

render_datatable($table_data, (isset($class) ? $class : 'contracts'),[],[
  'data-last-order-identifier' => 'contracts',
  'data-default-order'         => get_table_last_order('contracts'),
]);

?>
