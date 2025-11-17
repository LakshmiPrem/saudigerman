<!-- Client send file modal -->
<div class="modal fade" id="notify_installment" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/corporate_recoveries/send_notify_email/'.$client->userid); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo _l('send_installment_notification'); ?></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <?php echo render_input('send_file_subject','send_file_subject','Installment Payment Notification'); ?>
                        <?php
                        $selected = array();
                        echo render_select('notify_staff[]',$staff,array('staffid',array('firstname','lastname')),'Staff',$selected,array('multiple'=>true),array(),'','',false); ?>
                    </div>
                    <div id="addiT"></div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('send'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
