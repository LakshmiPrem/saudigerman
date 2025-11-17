<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_expenses_tab'); ?></h4>
<?php if(has_permission('expenses','','create')){ ?>
<a href="<?php echo admin_url('expenses/expense?lawyer_id='.$client->lawyerid); ?>" class="btn btn-info mbot25<?php if($client->active == 0){echo ' disabled';} ?>"><?php echo _l('new_expense'); ?></a>
<?php } ?>
<div id="expenses_total" style="display: none;"></div>
<?php
 $this->load->view('admin/expenses/table2_html', [
    'class'=>'expenses-single-client',
    'withBulkActions'=>false
]); ?>

<?php } ?>
