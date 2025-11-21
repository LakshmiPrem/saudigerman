<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('contracts_invoices_tab'); ?></h4>
<?php if(has_permission('contracts','','create')){ ?>
<a href="<?php echo admin_url('contracts/contract?party_id='.$client->id); ?>" class="btn btn-info mbot25 hide <?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_contract'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php // $this->load->view('admin/contracts/table_html', array('class'=>'contracts-single-client'));

 $table_data = array(
 _l('the_number_sign'),
 _l('contract_list_subject'),
 array(
   'name'=>_l('contract_list_client'),
   'th_attrs'=>array('class'=> (isset($client) ? '' : ''))
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
$table_data = hooks()->apply_filters('contracts_table_columns', $table_data);

render_datatable($table_data, 'contracts-single-client',[],[
  'data-last-order-identifier' => 'contracts',
  'data-default-order'         => get_table_last_order('contracts'),
]);

 ?>
<?php } ?>
