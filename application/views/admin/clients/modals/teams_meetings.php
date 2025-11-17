<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Client send file modal -->
<div class="modal fade" id="team_meeting" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/clients/create_meeting/'.$client->userid); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo _l('schedule_meeting'); ?></h4>
            </div>
            <div class="modal-body">
            <?php echo render_input('subject','subject'); ?>
            <?php echo render_datetime_input('start_date','start_date'); ?>
            <?php echo render_datetime_input('end_date','end_date'); ?>
            <?php echo render_input('addi_email','addi_email'); ?>
            <p style="color: red; margin-top: 5px;">Please enter email addresses separated by semicolons.</p>
            <?php $primary_contact = get_primary_contact_user_id($client->userid);
                  if($primary_contact) { $disable = ''; $message = '';} else { $disable = 'disabled'; $message = 'No primary contact added.';}  ?>
            <input type="checkbox"  id="add_contacts" name="add_contacts" style="margin-right: 10px;" value="1" <?php echo $disable;?>>
            <label for="add_contacts"><?php echo _l('add_contacts'); ?></label>
            <p style="color: red; margin-top: 5px;"><?php echo $message;?></p>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('schedule'); ?></button>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
