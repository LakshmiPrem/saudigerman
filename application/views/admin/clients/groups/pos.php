<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ 
	?>
<h4 class="customer-profile-group-heading"><?php echo _l('purchase_order'); ?></h4>
<?php if(has_permission('contracts','','create')){ ?>
<a onclick="new_quick_po(<?=$client->userid?>,'<?=$client->company?>');return false;" href="#" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_po'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php  //$this->load->view('admin/contracts/table_html', array('class'=>'contracts-single-client')); ?>
<?php  
$table_data = array(
 _l('the_number_sign'),
 _l('contract_list_subject'),
 array(
   'name'=>_l('contract_list_client'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
 array(
   'name'=>_l('other_party'),
   'th_attrs'=>array('class'=> (isset($client) ? '' : ''))
 ),
  
 _l('signed_po')
);
$table_data = hooks()->apply_filters('contracts_table_columns', $table_data);

render_datatable($table_data, 'pos-single-client',[],[
  'data-last-order-identifier' => 'contracts',
  'data-default-order'         => get_table_last_order('contracts'),
]);

?>
<?php } ?>
