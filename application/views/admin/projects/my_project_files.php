<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo form_open_multipart(admin_url('projects/upload_file/'.$project->id),array('class'=>'dropzone','id'=>'project-files-upload')); ?>

  <input type="file" name="file" multiple />
  <div class="row" style="background-color: #e3e3ed;padding: 8px;border-radius: 4px;">
    <?php
    if ( has_permission('projects', '', 'create')) {?>
    <div class="col-md-3 border-right">       
    <?php echo render_input('file_subject', 'project_discussion_subject','' , 'text'); ?>
    </div>
     <?php $value =  _d(date('Y-m-d')); ?>

    <div class="col-md-2">
       <?php echo render_date_input('issue_date','issue_date',$value); ?> 
    </div>
    <?php $value = _d(date('Y-m-d')); ?>
     <div class="col-md-2">
       <?php echo render_date_input('expiry_date','expiry_date',''); ?> 
    </div>
      <div class="col-md-2">
      <div class="form-group">
        <label class=""><?php echo _l('document_type'); ?></label>
        <select class="form-control" id="document_type" name="document_type" >
           <option></option>
           <?php foreach($document_types as $doc_type){ ?>
              <option value="<?=$doc_type['id']?>"><?=$doc_type['name']?></option>
           <?php } ?>
        </select>
      </div>
    </div>
    <div class="col-md-3 border-right">
    <?php echo render_textarea('file_description', 'project_discussion_description','',array('rows'=>'1')); ?>
     </div>
    
    <?php
    } ?>
  </div>
  <hr />
<?php echo form_close(); ?>
<small class="mtop5 hide"><?php echo _l('project_file_visible_to_customer'); ?></small><br />
<div class="onoffswitch hide">
  <input type="checkbox" name="visible_to_customer" id="pf_visible_to_customer" class="onoffswitch-checkbox">
  <label class="onoffswitch-label" for="pf_visible_to_customer"></label>
</div>
<div class="text-right" style="margin-top:-25px;">
   <button class="gpicker" data-on-pick="projectFileGoogleDriveSave">
    <i class="fa fa-google" aria-hidden="true"></i>
    <?php echo _l('choose_from_google_drive'); ?>
  </button>
  <div id="dropbox-chooser"></div>
</div>
<div class="clearfix"></div>
<div class="mtop25"></div>
<div class="modal fade bulk_actions" id="project_files_bulk_actions" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
      </div>
      <div class="modal-body">
        <?php if(is_admin() ){ ?>
       <div id="b_delete_div">   
        <div class="checkbox checkbox-danger">
          <input type="checkbox" name="mass_delete" id="mass_delete">
          <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
        </div>
        <hr class="mass_delete_separator" />
        </div> 
        <div id="b_email_toggle">
          <div class="checkbox checkbox-danger">
          <input type="checkbox" name="mass_mail" id="mass_mail">
          <label for="mass_mail"><?php echo _l('bulk_email'); ?></label>
        </div>

        <!-- Client send file modal -->
        <div id="div_bulk_mail">
          <div class="row" >
          <br>
            <div class="col-md-12">
                <?php echo render_input('send_file_subject_','send_file_subject'); ?>
                <?php echo render_input('send_file_email_','send_file_email'); ?>
                <?php //echo render_input('bcc_','Other Emails'); ?>
                <?php echo render_textarea('send_file_message_','send_file_message',"Hi,\n\n Please find the attahced files.\n\nThanks".get_option('email_signature')); ?>
            </div>
        </div>

        </div>
       
        <hr class="mass_mail_separator" />
        </div>


        <?php } ?>
        <div id="bulk_change">
          <div class="form-group">
            <label class="mtop5"><?php echo _l('project_file_visible_to_customer'); ?></label>
            <div class="onoffswitch">
              <input type="checkbox" name="bulk_visible_to_customer" id="bulk_pf_visible_to_customer" class="onoffswitch-checkbox">
              <label class="onoffswitch-label" for="bulk_pf_visible_to_customer"></label>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <a href="#" class="btn btn-info" onclick="project_files_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<a href="#" data-toggle="modal" data-target="#project_files_bulk_actions" class="bulk-actions-btn table-btn hide" data-table=".table-project-files">
  <?php echo _l('bulk_actions'); ?>
</a>
<a href="#" onclick="window.location.href = '<?php echo admin_url('projects/download_all_files/'.$project->id); ?>'; return false;" class="table-btn hide" data-table=".table-project-files"><?php echo _l('download_all'); ?></a>
<div class="clearfix"></div>
<table class="table dt-table table-project-files" data-order-col="6" data-order-type="desc">
  <thead>
    <tr>
      <th data-orderable="false"><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="project-files"><label></label></div></th>
      <th><?php echo _l('project_discussion_subject'); ?></th>
       <th><?php echo _l('document_type'); ?></th>
      <th><?php echo _l('issue_date'); ?></th>
      <th><?php echo _l('expiry_date'); ?></th>
      <th><?php echo _l('project_discussion_last_activity'); ?></th>
      <th><?php echo _l('project_discussion_total_comments'); ?></th>
     <!--  <th><?php echo _l('project_file_visible_to_customer'); ?></th> -->
      <th><?php echo _l('project_file_uploaded_by'); ?></th>
      <th><?php echo _l('project_file_dateadded'); ?></th>
      <th><?php echo _l('options'); ?></th>
    </tr>
  </thead>
  <tbody>
    <?php foreach($files as $file){
      $path = get_upload_path_by_type('project') . $project->id . '/'. $file['file_name'];
      ?>
      <tr>
        <td>
          <div class="checkbox"><input type="checkbox" value="<?php echo $file['id']; ?>"><label></label></div>
        </td>
        <td data-order="<?php echo $file['subject']; ?>">
          <a href="#" onclick="view_project_file(<?php echo $file['id']; ?>,<?php echo $file['project_id']; ?>); return false;">
            <?php /*if(is_image(PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']) || (!empty($file['external']) && !empty($file['thumbnail_link']))){
              echo '<div class="text-left"><i class="fa fa-spinner fa-spin mtop30"></i></div>';
              echo '<img class="project-file-image img-table-loading" src="#" data-orig="'.project_file_url($file,true).'" width="100">';
              echo '</div>';
            }*/
            echo $file['subject']; ?></a>
          </td>
           <td data-order="<?php echo get_document_type_name($file['document_type']); ?>"><?php echo get_document_type_name($file['document_type']); ?></td>
          <td data-order="<?php echo _d($file['issue_date']); ?>"><?php echo _d($file['issue_date']); ?></td>
          <td data-order="<?php echo _d($file['expiry_date']); ?>"><?php echo _d($file['expiry_date']); ?></td>
          <td data-order="<?php echo $file['last_activity']; ?>">
            <?php
            if(!is_null($file['last_activity'])){ ?>
            <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($file['last_activity']); ?>">
              <?php echo time_ago($file['last_activity']); ?>
            </span>
            <?php } else {
              echo _l('project_discussion_no_activity');
            }
            ?>
          </td>
          <?php $total_file_comments = total_rows(db_prefix().'projectdiscussioncomments',array('discussion_id'=>$file['id'],'discussion_type'=>'file')); ?>
          <td data-order="<?php echo $total_file_comments; ?>">
            <?php echo $total_file_comments; ?>
          </td>
         <!--  <td data-order="<?php echo $file['visible_to_customer']; ?>">
            <?php
            $checked = '';
            if($file['visible_to_customer'] == 1){
              $checked = 'checked';
            }
            ?>
            <div class="onoffswitch">
              <input type="checkbox" data-switch-url="<?php echo admin_url(); ?>projects/change_file_visibility" id="<?php echo $file['id']; ?>" data-id="<?php echo $file['id']; ?>" class="onoffswitch-checkbox" value="<?php echo $file['id']; ?>" <?php echo $checked; ?>>
              <label class="onoffswitch-label" for="<?php echo $file['id']; ?>"></label>
            </div>

          </td> -->
          <td>
            <?php if($file['staffid'] != 0){
              $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']). '">' .staff_profile_image($file['staffid'], array(
                'staff-profile-image-small'
              )) . '</a>';
              $_data .= ' <a href="' . admin_url('staff/member/' . $file['staffid'])  . '">' . get_staff_full_name($file['staffid']) . '</a>';
              echo $_data;
            } else {
             echo ' <img src="'.contact_profile_image_url($file['contact_id'],'thumb').'" class="client-profile-image-small mrigh5">
             <a href="'.admin_url('clients/client/'.get_user_id_by_contact_id($file['contact_id']).'?contactid='.$file['contact_id']).'">'.get_contact_full_name($file['contact_id']).'</a>';
           }
           ?>
         </td>
         <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
         <td>
           <?php if(empty($file['external'])){ ?>
           <button type="button" data-toggle="modal" data-original-file-name="<?php echo $file['file_name']; ?>" data-file-name="<?php echo $file['file_name']; ?>" data-filetype="<?php echo $file['filetype']; ?>" data-path="<?php echo PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>
           <?php } ?>
           <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','delete')){ ?>
           <a href="<?php echo admin_url('projects/remove_file/'.$project->id.'/'.$file['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
           <?php } ?>
         </td>
       </tr>
       <?php } ?>
     </tbody>
   </table>
   <div id="project_file_data"></div>
   <?php include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php'); ?>
