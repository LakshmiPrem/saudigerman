 <p class="bold mtop10 text-right"> 
                            <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_notice'); ?>" class="btn btn-info" onclick="upload_noticeversionfile(<?php echo $notice->id; ?>); return false;">
            				<i class="fa fa-upload"></i>
           			 		<?php echo _l('upload_noticeversion'); ?>
      					  </a>
                            <?php
	 				$totalversions = total_rows(db_prefix().'notice_versions','noticeid='.$notice->id);
							  if(get_option('enable_sharepoint')==1){?>
                            <?php if($totalversions==0){?>
                             <a href="<?php echo $notice->sharepoint_link; ?>" target="_blank" class="btn btn-warning btn-sm mleft20" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit_base') ?> </a>
                             <?php } ?>
                           <a href="#" class="btn btn-success btn-sm mright10" onclick="save_as_notice_new_version(<?php $notice->id ?>); return false;"><!-- <i class=" fa fa-info-circle pull-left fa-lg" data-toggle="tooltip" data-title="<?php echo _l('load_latest_content_from_sharepoint_info'); ?>"></i> --> <?php echo _l('load_latest_content_from_sharepoint') ?> <i class="fa fa-arrow-circle-down"></i></a>
                           <?php } ?>
                        </p>
                     

<?php
                  // select all notice versions
                  $notice_versions = get_all_notice_versions($notice->id); ?>
                    <table 
	class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('version')?></th>
			<th width="20%"><?php echo _l('file_name')?></th>
			<th><?php echo _l('version_added_date')?></th>
			<th><?php echo _l('version_added')?></th>
			<th><?php echo _l('billing_action')?></th>
			<th><?php echo _l('mark_as_final')?></th>
      <th><?php echo _l('active')?></th>
		</tr>
	</thead>



   <?php foreach ($notice_versions as $notice_version){?>
      <tr>
      		<td><?php echo $notice_version['version']?></td>
			<td width="20%"><?php echo $notice_version['version_internal_file_path'];?>
			<?php if(get_option('enable_sharepoint')==1){?>
			  <a href="<?php echo $notice_version['version_sharpoint_link'] ?>"  target="_blank" class="btn btn-warning btn-sm mleft10" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit') ?> </a>
			 <?php } ?>
			 </td>
			
			<td><?php echo _d($notice_version['dateadded'])?></td>
			<td><?php echo get_staff_full_name($notice_version['addedby'])?></td>
			<td><?php
		 $path1 = site_url('download/downloadagreementversion/'. $notice_version['noticeid'].'/'.$notice_version['id']);
			 
    $file_path   = get_upload_path_by_type('notice').$notice_version['noticeid'].'/'.$notice_version['version_internal_file_path'];
    if(file_exists($file_path)){ 
	
		$dispaly = '<a href="'. $path1 .'"  class="btn btn-sm btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('download').'" data-placement="bottom"><i class="fa fa-download" aria-hidden="true"></i></a>';
		
		echo $dispaly;
	}else{
        echo  '-';
    }
	 ?>
	
			</td>
			<td>
				                    <?php if($notice_version['active']== 1){?>
                              <?php if($notice->final_doc != $notice_version['id']){  ?>
                              <a class="btn btn-success" title=" <?php echo _l('mark_as_final') ?>" href="<?php echo admin_url('notices/mark_as_final_doc/'.$notice->id.'/'.$notice_version['id']) ?>"><i class="fa fa-star-o"></i></a>
                              <?php }} ?>
                              <?php echo icon_btn('notices/delete_version/' . $notice_version['id'].'/'.$notice_version['noticeid'], 'remove', 'btn-danger _delete hide'); ?>
			</td>
      <td>

		   <?php
            $checked = '';
            if($notice_version['active'] == 1){
              $checked = 'checked';
            }
            ?>
		  <div class="onoffswitch">
              <input type="checkbox" data-switch-url="<?php echo admin_url(); ?>notices/change_version_status" id="<?php echo $notice_version['id']; ?>" data-id="<?php echo $notice_version['id']; ?>" class="onoffswitch-checkbox" value="<?php echo $notice_version['id']; ?>" <?php echo $checked; ?>>
              <label class="onoffswitch-label" for="<?php echo $notice_version['id']; ?>"></label>
            </div>
      </td>
		</tr>
   <?php } ?>
   </table>