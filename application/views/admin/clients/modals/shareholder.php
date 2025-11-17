<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Modal Contact -->
<div class="modal fade" id="shareholder" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open(admin_url('clients/form_shareholder/'.$customer_id.'/'.$contactid),array('id'=>'shareholder-form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?><br /><small class="color-white" id=""><?php echo get_company_name($customer_id,true); ?></small></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                 
                        <!-- // For email exist check -->
                     
                         <?php $selected=( isset($contact) ? $contact->shareholder_id : ''); ?>
                         <?php echo render_select_with_input_group('shareholder_id',$shareholders,array('id','shareholder_name'),'shareholder_name',$selected,'<a href="#" data-toggle="modal" data-target="#customer_shareholder_modal"><i class="fa fa-plus"></i></a>');?>
                        <?php $value=( isset($contact) ? $contact->share_percentage : ''); ?>
                        <?php echo render_input('share_percentage', 'shareholder_percentage',$value,'number'); ?>
                       
                                     
                     

            </div>
        </div>
       </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#shareholder-form"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
</div>

