<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="col-md-12">
	<div class="col-md-9"><p style="font-size: 20px;font-weight: 400"><?php echo _l('updates'); ?></p></div><div class="col-md-3"><?php if(isset($project)){ ?>
<a href="#" data-toggle="modal" data-target=".reminder-modal-project-<?php echo $project->id; ?>" class="btn btn-success mbot25"><i class="fa fa-bell-o"></i> <?php echo _l('set_reminder'); ?></a></div></div>

<div class="clearfix"></div>

<?php //render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders');
$this->load->view('admin/includes/modals/reminder',array('id'=>$project->id,'name'=>'project','members'=>$members,'reminder_title'=>_l('set_reminder')));
} ?>
<hr />
<?php echo form_open(admin_url('casediary/save_case_update/'.$project->id)); ?>
<input type="hidden" name="rel_type" value="project">
<?php echo render_textarea('content','','',array(),array(),'',''); ?>
<button type="submit" class="btn btn-info"><?php echo _l('add_update'); ?></button>
<?php echo form_close(); ?>

<hr>


<?php
   $len = count($case_updates);
    foreach($case_updates as $note){ ?>
   	<div class="panel_s " style="border: 1px solid #a8e3b6;border-radius: 4px;">

	<div class="media lead-note" style="padding:5px;">
		<a href="<?php echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">
		<?php echo staff_profile_image($note['addedfrom'],array('staff-profile-image-small','pull-left mright10')); ?>
		</a>

   <div class="media-body" style="color: black;font-weight: bold">
      <?php if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
      <a href="#" class="btn btn-default pull-right text-danger" onclick="delete_case_update(this,<?php echo $note['id']; ?>);return false;"><i class="fa fa fa-times"></i></a>
      <a href="#" class="btn btn-default pull-right mright5" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
      <?php } ?>
    
      <span class="media-heading"><?php echo _l('date_added',_dt($note['dateadded'])); ?></span>
      <a href="<?php echo admin_url('profile/'.$note["addedfrom"]); ?>" target="_blank">
         <h5 class="media-heading bold"><?php echo get_staff_full_name($note['addedfrom']); ?></h5>
      </a>
      <div data-note-description="<?php echo $note['id']; ?>" class="text-muted" style="color: black;white-space:normal">
         <?php echo app_happy_text($note['content']); ?>
      </div>
      <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide mtop15">
         <?php echo render_textarea('content'.$note['id'],'', $note['content'],array(),array(),'',''); ?>
         <div class="text-right">
            <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
            <button type="button" class="btn btn-info" onclick="edit_case_update(<?php echo $note['id']; ?>);"><?php echo _l('update'); ?></button>
         </div>
      </div>
   </div>
</div>
</div>
<?php  } ?>

