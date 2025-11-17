<div class="task-table">
   
<div class="row">
  <div class="col-md-12">
  <?php echo form_open(admin_url('casediary/handover/'),array('id'=>'handover-form')); ?>
            <?php echo form_hidden('project_id',$project->id); ?>

      <div class="row">
         <div class="col-md-3">
                         
              <?php echo render_select('replyfrom',$members,array('staffid',array('firstname','lastname')),'handover_to');
							 ?>
						</div>
          <div class="col-md-6">
          <?php //$value = (isset($scopes) ? $scopes->scope_description : ''); ?>
          <?php echo render_textarea('handover_out','action_taken','',array('rows'=>2,'required'=>'true'),array(),'',''); ?>
          </div>
          <div class="col-md-3 mtop40">
             <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#discussion_form"><?php echo _l('add_handover'); ?></button>
          </div>
      </div>
  <?php echo form_close(); ?>
  </div>
</div>

  <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
      <th><?php echo _l('handover_by'); ?></th>         
      <th><?php echo _l('action_taken'); ?></th>
        <th><?php echo _l('reply_comment'); ?></th>
       <th><?php echo _l('date'); ?></th>
      <th></th>

    </tr>
  </thead>
  <tbody>
    <?php //if(isset($scope)){
      foreach ($case_handover as $row_) { ?>
        <tr>
        <td> <?php echo get_staff_full_name($row_['addedfrom']); ?></td>
         <td><?php echo $row_['handover_out']; ?></td>
         <td width="50%">
                  <div data-note-description="<?php echo $row_['id']; ?>">
                    <?php echo $row_['reply_comment']; ?>
                </div>
                <div data-note-edit-textarea="<?php echo $row_['id']; ?>" class="hide">
                    <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($row_['reply_comment']); ?></textarea>
                    <div class="text-right mtop15">
                      <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $row_['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                      <button type="button" class="btn btn-info" onclick="edit_handover(<?php echo $row_['id']; ?>);"><?php echo _l('update_handover'); ?></button>
                  </div>
              </div>
          </td>
          <td> <?php echo _d($row_['handover_dt']); ?></td>
        <td>
          <?php  if(($row_['replyfrom'] == get_staff_user_id())){ ?>
          <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $row_['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
          <?php }  if(($row_['addedfrom'] == get_staff_user_id())){ ?>
          <a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('casediary/delete_handover/'.$row_['project_id'].'/'.$row_['id']) ?>"><i class="fa fa-remove"></i></a>
          <?php }
										 ?>
          </td>
      </tr>
      <?php }//} ?>  
  </tbody>
 </table>
</div>
    
