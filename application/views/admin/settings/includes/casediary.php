
<div class="form-group">
        <?php $value = get_option('file_no_prefix'); ?>
    <?php echo render_input('settings[file_no_prefix]','casediary_file_no_prefix',$value,'text',array('required'=>'true')); ?>
</div>
<div class="form-group">
        <?php $value = get_option('next_file_number'); ?>
    <?php echo render_input('settings[next_file_number]','casediary_next_file_number',$value,'text',array('required'=>'true')); ?>
</div>
<div class="form-group">
    <?php $value = get_option('send_document_expiry_reminder_before'); ?>
    <?php echo render_input('settings[send_document_expiry_reminder_before]','send_document_expiry_reminder_before',$value,'text',array('required'=>'true')); ?>
</div>
<hr />
<?php render_yes_no_option('allow_staff_view_project_subfile','allow_staff_view_project_subfile'); ?>
<hr />
<?php render_yes_no_option('enable_legalrequest_in_case','enable_legalrequest_in_case'); ?>
<hr />
<?php render_yes_no_option('enable_casediary_in_dashboard','enable_casediary_in_dashboard'); ?>
<hr />
<?php render_yes_no_option('enable_legaldashboard','enable_legaldashboard'); ?>
