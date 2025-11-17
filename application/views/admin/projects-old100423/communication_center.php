
<table class="table dt-table scroll-responsive table-project-communications" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
      <th data-orderable="false"><?php echo _l('mode'); ?></th>
      <th><?php echo _l('date'); ?></th>
      <th><?php echo _l('subject'); ?></th>
      <th><?php echo _l('mail_from'); ?></th>
      <th><?php echo _l('mail_to'); ?></th>
      <th><?php echo _l('content'); ?></th> 
      <th><?php echo _l('attachments'); ?></th>
      <th><?php echo _l('added_by'); ?></th>
      <th></th>
    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($communication_center as $cc) { 
        if(($cc['date']) > date('Y-m-d')){
          $cc['date'] = $cc['dateadded'];
        }

        ?>
        <tr>
        <td><?=$cc['mode']?></td>
        <td data-order="<?php echo $cc['date']; ?>" ><?=_dt($cc['date'])?></td>
        <td><a onclick="view_communication_details(<?=$cc['id']?>)" href="javascript:void(0)"><?=$cc['subject']?></a></td>
        <td><?=$cc['mail_from']?></td>
        <td><?=nl2br(str_replace(',', '<br>',$cc['mail_to'] ))?></td>
        <td><a onclick="view_communication_details(<?=$cc['id']?>)" href="javascript:void(0)"><?=nl2br(substr($cc['content'],0,50))?></a></td>
        <td><?php $y=1; foreach($cc['attachments'] as $files){ $path = CASEDIARY_UPLOADS_FOLDER.$cc['case_id'].'/'.$files['file_name'];
                  echo $y.'.<a href="'.site_url('uploads/casediary/'.$cc['case_id'].'/'.$files['file_name']).'" target="_blank" >'.$files['file_name'].'</a><br>'; $y++;}
          ?></td>
        <td><?=get_staff_full_name($cc['addedfrom'])?></td>
        <td><a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('projects/delete_communications/'.$cc['case_id'].'/'.$cc['id']) ?>"><i class="fa fa-remove"></i></a>
           <a href="<?php echo admin_url('projects/communication_center_print/'.$cc['id'].'?print=true'); ?>" target="_blank" class="btn btn-primary btn-with-tooltip btn-icon " data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom"><i class="fa fa-print"></i></a> </td>
      </tr>
      <?php } ?>  
  </tbody>
 </table>
</div><!-- 
 foreach($cc['attachments'] as $files){ $path = CASEDIARY_UPLOADS_FOLDER.$cc['case_id'].'/'.$files['file_name'];
                  echo $y.'.<a href="'.$path.'" download>'.$files['file_name'].'</a><br>'; $y++;} -->   

                  