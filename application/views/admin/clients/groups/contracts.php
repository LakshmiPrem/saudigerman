<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ 
?>
<h4 class="customer-profile-group-heading"><?php echo _l('contracts_invoices_tab'); ?></h4>
<?php if(has_permission('contracts','','create')){ ?>
<a onclick="new_quick_contract();return false;" href="#" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_contract'); ?></a>
<div class="clearfix"></div>
<?php } ?>
<?php $this->load->view('admin/contracts/table_html', array('class'=>'contracts-single-client')); ?>
<?php } ?>
