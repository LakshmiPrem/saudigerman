<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('documents_invoices_tab'); ?></h4>
<?php if(has_permission('documents','','create')){ ?>
<a href="<?php echo admin_url('documents/document?customer_id='.$client->userid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_document'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php
$table_data = array(
 '#',
 _l('document_list_subject'),
 array(
   'name'=>_l('document_list_client'),
   'th_attrs'=>array('class'=>'not_visible')
   ),
 _l('document_types_list_name'),
 //_l('contract_value'),

 _l('document_list_start_date'),
 _l('document_list_end_date'),
 );
$custom_fields = get_custom_fields('documents',array('show_on_table'=>1));
foreach($custom_fields as $field){
 array_push($table_data,$field['name']);
}
$table_data = hooks()->apply_filters('documents_table_columns',$table_data);

array_push($table_data,_l('options'));
render_datatable($table_data, 'documents-single-client'); ?>
<?php } ?>
