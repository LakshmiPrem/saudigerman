<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_expenses_tab'); ?></h4>
<?php  if(has_permission('expenses','','create')){  ?>
<a href="<?php echo admin_url('expenses/expense?customer_id=16&opposite_id='.$client->id); ?>" target="_blank" class="btn btn-info mbot15  <?php if($client->active == 0){echo ' disabled';} ?>">
    <?php echo _l('new_expense'); ?>
</a>
<?php } ?>

<?php  $this->load->view('admin/expenses/table2_html', [
	'class'=>'expenses-single-client',
	'withBulkActions'=>false
]); ?>
<?php } ?>
