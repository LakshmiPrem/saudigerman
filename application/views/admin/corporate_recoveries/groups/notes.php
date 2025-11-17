<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('contracts_notes_tab'); ?></h4>
<div class="col-md-12">

 <a href="#" class="btn btn-success mtop15 mbot10" onclick="slideToggle('.usernote'); return false;"><?php echo _l('new_note'); ?></a>
 <div class="clearfix"></div>
<div class="row">
     <hr class="hr-panel-heading" />
</div>
 <div class="clearfix"></div>
 <div class="usernote hide">
    <?php echo form_open(admin_url('misc/add_note/'.$client->userid.'/corporate')); ?>
     <?php $mode_of_contact_arr = get_mode_of_contact(); ?>
    <?php //echo render_select('mode_of_contact',$mode_of_contact_arr,array('id','name'),'mode_of_contact');?>
    <div class="form-group">
      <label class="control-label"><?=_l('mode_of_contact')?></label><br>
      <label class="radio-inline">
      <input type="radio" name="mode_of_contact" value="call" checked>Call
      </label>
      <label class="radio-inline">
        <input type="radio" name="mode_of_contact" value="visit">Visit
      </label>
      <label class="radio-inline">
        <input type="radio" name="mode_of_contact" value="email">Email
      </label>
    </div>
    

    <?php $contact_code_arr = get_contact_codes(); ?>
    <?php echo render_select('contact_code',$contact_code_arr,array('id','name'),'contact_code');?>
    <div class="ptp_div hide">
      <?php echo render_input( 'ptp_amount', 'ptp_amount'); ?>
      <?php echo render_date_input( 'ptp_date', 'ptp_date',_d(date('Y-m-d'))); ?>
    </div>
    <?php $actions_arr = get_defaulter_follow_up_actions(); ?>
    <?php echo render_select('action',$actions_arr,array('id','name'),'follow_up_action');?>
    <?php echo render_textarea( 'description', 'note_description', '',array( 'rows'=>5)); ?>

    <button class="btn btn-info pull-right mbot15">
        <?php echo _l( 'submit'); ?>
    </button>
    <?php echo form_close(); ?>
</div>
<div class="clearfix"></div>
<div class="mtop15">
    <table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
        <thead>
            <tr>
                <th width="20%">
                    <?php echo _l( 'clients_notes_table_description_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'agent'); ?>
                </th>
                <th>
                    <?php echo _l( 'mode_of_contact'); ?>
                </th>
                <th>
                    <?php echo _l( 'contact_code'); ?>
                </th>
                <th>
                    <?php echo _l( 'ptp_date'); ?>
                </th>
                <th>
                    <?php echo _l( 'ptp_amount'); ?>
                </th>
                <th>
                    <?php echo _l( 'follow_up_action'); ?>
                </th>
                <th>
                    <?php echo _l( 'clients_notes_table_dateadded_heading'); ?>
                </th>
                <th>
                    <?php echo _l( 'options'); ?>
                </th> 
            </tr>
        </thead>
        <tbody>
            <?php foreach($user_notes as $note){ ?>
            <tr>
                <td width="20%">
                  <div data-note-description="<?php echo $note['id']; ?>">
                    <?php echo $note['description']; ?>
                </div>
                <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide">
                    <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                    <div class="text-right mtop15">
                      <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                      <button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                  </div>
              </div>
          </td>
          <td>
            <?php echo '<a href="'.admin_url( 'profile/'.$note[ 'addedfrom']). '">'.get_staff_full_name($note['addedfrom']). '</a>' ?>
          </td>
          <td><?=_l($note['mode_of_contact'])?></td>
          <td><?=$note['code']?></td>
          <td><?=_d($note['ptp_date'])?></td>
          <td><?=$note['ptp_amount']?></td>
          <td><?=_l($note['action'])?></td>
        <td data-order="<?php echo $note['dateadded']; ?>">
         <?php if(!empty($note['date_contacted'])){ ?>
           <span data-toggle="tooltip" data-title="<?php echo _dt($note['date_contacted']); ?>">
              <i class="fa fa-phone-square text-success font-medium valign" aria-hidden="true"></i>
          </span>
          <?php } ?>
          <?php echo _dt($note[ 'dateadded']); ?>
        </td>
         <td>
            <?php if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
            <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
            <a href="<?php echo admin_url('misc/delete_note/'. $note['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
</div>
<?php } ?>
