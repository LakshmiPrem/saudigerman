<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
       <?php echo form_open('admin/chequebounces/renew/'.$bounce_id.'/'.$contactid,array('id'=>'renew-contract-form','autocomplete'=>'off')); ?>
         <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                   <?php echo $title; ?><br /><small id="" style="color: white;"><?php //echo get_party_name($bounce_id,true); ?></small></h4>
                </h4>
            </div>
            <div class="modal-body">
                <?php $value=( isset($contact) ? _d($contact->retcheque_date) : _d(date('Y-m-d'))); ?>
                <?php echo render_date_input('retcheque_date','retcheque_date',$value); ?>
                 <?php $value=( isset($contact) ? $contact->cheque_no : ''); ?>
             <?php echo render_input('cheque_no','cheque_no',$value,'text'); ?>
                <?php $value=( isset($contact) ? $contact->cheque_amount : ''); ?>
                <?php echo render_input('cheque_amount','cheque_amount',$value,'number'); ?>
                 <?php $value=( isset($contact) ? $contact->amount_received : ''); ?>
                <?php echo render_input('amount_received','amount_received',$value,'number'); ?>
                 <?php $value=( isset($contact) ? $contact->balance : ''); ?>
                 <?php echo render_input('balance','balance',$value,'number'); ?>
                   <?php $value=( isset($contact) ? $contact->remarks : ''); ?>
                   <?php echo render_textarea( 'remarks', 'remarks', $value,array( 'rows'=>3)); ?>
                
              <?php echo form_hidden('contactid',$contactid); ?>
             
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
<script>
 $('#renew-contract-form input[name="cheque_amount"] ,input[name="amount_received"]').blur(function(){
	
         var total_amount = $('#renew-contract-form input[name="cheque_amount"]').val();
		
         var paid_amount = $('#renew-contract-form input[name="amount_received"]').val();
		
         var balance = $('#renew-contract-form input[name="balance"]').val();
		 if(isNaN(total_amount)||total_amount=='')total_amount=0;
		 if(isNaN(paid_amount)||paid_amount=='')paid_amount=0;
		  var balance = total_amount - paid_amount;
         $('#renew-contract-form input[name="balance"]').val(parseFloat(balance).toFixed(2));
         
    });
</script>
