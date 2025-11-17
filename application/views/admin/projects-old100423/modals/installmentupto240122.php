<!-- Modal Contact -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/projects/installment/'.$project_id.'/'.$contactid,array('id'=>'contact-form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?><br /><small id=""><?php echo get_recovers_name($project_id,true); ?></small></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        
                        <!-- // For email exist check -->
                        <?php echo form_hidden('contactid',$contactid); ?>
                        <?php $value=( isset($contact) ? $contact->installment_amount : ''); ?>
                        <?php echo render_input( 'installment_amount', 'installment_amount',$value); ?>
                        <?php $value=( isset($contact) ? $contact->installment_date  : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input( 'installment_date', 'installment_date ',$value); ?>
                        <?php $selected=( isset($contact) ? $contact->installment_status : 'not_paid'); ?>
                        <?php $status_arr = array(array('id'=>'paid','name'=>'Paid'),array('id'=>'not_paid','name'=>'Not Paid')); ?>
         
           <?php echo render_select('installment_status',$status_arr,array('id','name'),'installment_status',$selected);?>

           <?php $value=( isset($contact) ? $contact->remarks : ''); ?>
                    <?php echo render_textarea( 'remarks','remarks',$value); ?>

               
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#contact-form"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
</div>
