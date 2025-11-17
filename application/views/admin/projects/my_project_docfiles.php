<div class="content">
  <div class="row">

    <div class="panel_s">
      <div class="panel-body">
   <div class="col-md-12">
        <a href="#" onclick="new_projectsub();return false;" class="btn btn-info mbot25"><?php echo _l('add_vakalath'); ?></a>
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

<table class="table dt-table table-project-subfiles" data-order-col="6" data-order-type="desc">
  <thead>
    <tr>
      <th data-orderable="false"><span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="project-subfiles"><label></label></div></th>
      
      <th><?php echo _l('project_discussion_subject'); ?></th>
       <th><?php echo _l('document_type'); ?></th>
        <th><?php echo _l('filing_date'); ?></th>
      <th><?php echo _l('issue_date'); ?></th>
      <th><?php echo _l('expiry_date'); ?></th>
     <!-- <th><?php echo _l('project_discussion_last_activity'); ?></th>
      <th><?php echo _l('project_discussion_total_comments'); ?></th>-->
     
      <th><?php echo _l('project_file_uploaded_by'); ?></th>
      <th><?php echo _l('project_file_dateadded'); ?></th>
      <th><?php echo _l('options'); ?></th>
       <th><?php echo _l('change_attachement'); ?></th>
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
          <a href="#" onclick="view_project_versionfile(<?php echo $file['id']; ?>,<?php echo $file['project_id']; ?>); return false;">
            <?php /*if(is_image(PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']) || (!empty($file['external']) && !empty($file['thumbnail_link']))){
              echo '<div class="text-left"><i class="fa fa-spinner fa-spin mtop30"></i></div>';
              echo '<img class="project-file-image img-table-loading" src="#" data-orig="'.project_file_url($file,true).'" width="100">';
              echo '</div>';
            }*/
            echo $file['subject']; ?></a>
          </td>
           <td data-order="<?php echo get_document_type_name($file['document_type']); ?>"><?php echo get_document_type_name($file['document_type']); ?></td>
            <td data-order="<?php echo _d($file['filing_date']); ?>"><?php echo _d($file['filing_date']); ?></td>
          <td data-order="<?php echo _d($file['issue_date']); ?>"><?php echo _d($file['issue_date']); ?></td>
          <td data-order="<?php echo _d($file['expiry_date']); ?>"><?php echo _d($file['expiry_date']); ?></td>
          <!--<td data-order="<?php echo $file['last_activity']; ?>">
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
          </td>-->
         
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
          <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','delete')){ ?>
           <a href="<?php echo admin_url('projects/remove_file/'.$project->id.'/'.$file['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
           <?php } ?>
             <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','view')){ ?>
               <?php if(file_exists($path)){?>
            <a href="<?php echo site_url('download/downloadfile/'.$project->id.'/'.$file['id']); ?>" class="btn btn-info btn-icon"><i class="fa fa-download"></i></a>
           <?php }} ?>
          
         </td>
         <td>
         	<?php echo '<a href="#" data-toggle="tooltip" data-title="'. _l('change_attachement').'" class="btn btn-success  btn-icon" onclick="change_subfile(' . $file['id'] . ','.$project->id.'); return false;">
            <i class="fa fa-upload"></i>'. _l('change_attachement').'
                </a>'; 
	?>
         </td>
       </tr>
       <?php } ?>
     </tbody>
   </table>
    </div>
</div>
</div>

</div>
     <?php $this->load->view('admin/projects/modals/project_subfile'); ?>
   <div id="project_subfile_data"></div>


