<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="negotiation" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open(admin_url('contracts/add_negotiation'),array('id'=>'negotiation-contract-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('add_negotiations'); ?>
                </h4>
            </div>
            <div class="modal-body">
              
                <?php echo render_input('negotiate_value','contract_value',$contract->contract_value,'number'); ?>
                <?php echo render_textarea('content','negotiations'); ?>
              <?php echo form_hidden('contract_id',$contract->id); ?>
               <?php echo form_hidden('comment_type','negotiation'); ?>
            
           
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
