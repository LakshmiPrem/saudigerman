<div class="modal fade email-template" data-editor-id=".<?php echo 'tinymce-'.$project->id; ?>" id="matter_send_to_customer" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" style="z-index: 999999999999;">
    <div class="modal-dialog" role="document">
        <?php echo form_open('admin/projects/send_matter_overview_to_email/'.$project->id); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close close-send-template-modal" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('matter_send_to_email'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <!--<input type="hidden" name="hid_hearing_id" id="hid_hearing_id" value="">-->
                        <div class="checkbox checkbox-primary">
                            <input type="checkbox" name="attach_pdf" id="attach_pdf" checked>
                            <label for="attach_pdf"><?php echo _l('matter_attach_pdf'); ?></label>
                        </div>
                        <?php echo render_input('cc','To'); ?>
                        <h5 class="bold"><?php echo _l('proposal_preview_template'); ?></h5>
                        <hr />
                        <?php $content = "Sir,<br>
                        
Please find the attached matter overview <br> Matter Details :- ".$project->file_no.' - '.$project->name."<br> Thanks" ?>
                        <?php echo render_textarea('email_template_custom','',$content,array(),array(),'','tinymce-'.$project->id); ?>
                        <?php //echo form_hidden('template_name',$template_name); ?>
                    </div>

                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default close-send-template-modal" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('send'); ?></button>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>
