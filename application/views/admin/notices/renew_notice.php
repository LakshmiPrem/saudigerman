<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="renew_Notice_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <?php echo form_open_multipart(admin_url('Notices/renew'),array('id'=>'renew-Notice-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <?php echo _l('Notice_renew_heading'); ?>
                </h4>
            </div>
            <div class="modal-body">
                <?php
                $new_end_date_assume = '';
                if(!empty($Notice->dateend)){
                    $dStart                      = new DateTime($Notice->datestart);
                    $dEnd                        = new DateTime($Notice->dateend);
                    $dDiff                       = $dStart->diff($dEnd);
                    $new_end_date_assume = date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY'))));
                }
                ?>
                <?php echo render_date_input('new_start_date','Notice_start_date',_d(date('Y-m-d'))); ?>
                <?php echo render_date_input('new_end_date','Notice_end_date',_d($new_end_date_assume)); ?>
                <?php echo render_input('new_value','Notice_value',$Notice->Notice_value,'number'); ?>
                <?php if($Notice->signed == 1) { ?>
                <div class="checkbox">
                  <input type="checkbox" name="renew_keep_signature" id="renew_keep_signature">
                  <label for="renew_keep_signature"><?php echo _l('keep_signature'); ?></label>
              </div>
              <?php } ?>
              <?php echo form_hidden('Noticeid',$Notice->id); ?>
              <?php echo form_hidden('old_start_date',$Notice->datestart); ?>
              <?php echo form_hidden('old_end_date',$Notice->dateend); ?>
              <?php echo form_hidden('old_value',$Notice->Notice_value); ?>
               <div class="col-md-12 border-right">
     
     
         <div class="form-group">
                                <label for="installment_receipt" class="profile-image"><?php echo _l('attach_renewdoc'); ?></label>
                              
                                <input type="file" name="new_filename" class="form-control" id="new_filename" >
                             </div>
  
                
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
