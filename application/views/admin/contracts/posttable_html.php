<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = array(
 _l('the_number_sign'),
 _l('contract_list_subject'),
 array(
   'name'=>_l('contract_list_client'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
 _l('other_party'),
 _l('contract_types_list_name'),
 _l('contract_value'),
 _l('contract_list_start_date'),
 _l('contract_list_end_date'),
 /*(!isset($project) ? _l('project') : array(
   'name'=>_l('project'),
   'th_attrs'=>array('class'=>'not_visible')
 )),*/
  _l('signed_contract'),
 _l('contract_amendment'),
 _l('post_contract_action'),
 _l('post_review')
);
$custom_fields = get_custom_fields('contracts',array('show_on_table'=>1));

foreach($custom_fields as $field){
 	array_push($table_data,$field['name']);
}

$table_data = hooks()->apply_filters('contracts_table_columns', $table_data);

render_datatable($table_data, (isset($class) ? $class : 'signcontracts'),[],[
  'data-last-order-identifier' => 'contracts',
  'data-default-order'         => get_table_last_order('contracts'),
]);

?>
