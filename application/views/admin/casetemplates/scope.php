<div class="task-table">
   <!-- <button id="btn_add_hearing"  type="button" data-toggle="collapse" data-target="#demo" class="btn btn-info mbot25"><?php echo _l('add_scope'); ?></button> -->
    
<!-- <div id="demo" class="collapse">-->  
<div class="row">
  <div class="col-md-12">
  <?php echo form_open(admin_url('casetemplates/scope/'),array('id'=>'scope-form')); ?>
  <?php echo form_hidden('casetemplate_id',$project->id); ?>
      <div class="row">
          <div class="col-md-8">
          <?php echo render_textarea('scope_description','scope'); ?>
          </div>
          <div class="col-md-4 mtop40">
             <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#discussion_form"><?php echo _l('add_scope'); ?></button>
          </div>
      </div>
  <?php echo form_close(); ?>
  </div>
</div>

<table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="desc">
  <thead>
    <tr>       
      <th><?php echo _l('scope'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($scopes as $row_) { ?>
        <tr>
         <td width="50%">
                  <div data-note-description="<?php echo $row_['id']; ?>">
                    <?php echo $row_['scope_description']; ?>
                </div>
                <div data-note-edit-textarea="<?php echo $row_['id']; ?>" class="hide">
                    <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($row_['scope_description']); ?></textarea>
                    <div class="text-right mtop15">
                      <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $row_['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                      <button type="button" class="btn btn-info" onclick="edit_scope(<?php echo $row_['id']; ?>);"><?php echo _l('update_scope'); ?></button>
                  </div>
              </div>
          </td>
        <td>
          <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $row_['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
          <a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('casetemplates/delete_scope/'.$row_['casetemplate_id'].'/'.$row_['id']) ?>"><i class="fa fa-remove"></i></a></td>
      </tr>
      <?php }?>  
  </tbody>
 </table>
</div>
    
