<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
      <div class="col-md-7 border-right project-overview-left">
       
     <div class="panel_s panel-info">
                     <div class="panel-body">
      <div class="row">
       <div class="col-md-12">
         <p class="project-info text-uppercase bold text-dark font-medium" style="color: #FF1493">
            <?php echo _l('overview'); ?>
         </p>
      </div>
      
      <div class="col-md-6">
         <table class="table no-margin project-overview-table">
            <tbody>
               
                 
                 <?php if(get_option('enable_legal_request')==1) { ?>
              <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('legal_request'); ?></td>
                  <td>  <?php foreach ($requests as $req) {
    if ($req['ticketid'] == $contract->ticketid) {
        $subject = $req['subject'];
         echo  $subject;
        break; // stop after first match
    }
}
					  ?></td>
               </tr>
             <?php } ?>
              <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('client'); ?></td>
                  <td>  <?php  $selected = (isset($contract) ? $contract->client : '');
                        if($selected == ''){
                         $selected = (isset($customer_id) ? $customer_id: '16');
                      }
                      if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                       
                      }?>
                      <a style="color:#1446E5;font-weight: bold" href="<?php echo admin_url(); ?>clients/client/<?php echo $selected; ?>">
                        <?php echo $rel_val['name']; ?>
                      </a></td>
               </tr>
             <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_subject'); ?></td>
                  <td><?php echo $contract->subject; ?></td>
               </tr>
               
               <tr class="project-overview-file-no <?php if((isset($contract) && !customer_has_projects($contract->client))){ echo ' hide';} ?> ">
                  <td class="bold"><?php echo _l('project'); ?></td>
                  <td><?php if(isset($contract) && $contract->project_id != 0){
                        echo get_project_name_by_id($contract->project_id);
                     } ?></td>
               </tr>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('other_party'); ?></td>
                  <td>  <?php 
//                   foreach ($oppositeparty_names as $req) {
//     if ($req['id'] == $contract->other_party) {
//         $subject = $req['name'];
//          echo  $subject;
//         break; // stop after first match
//     }
// }

					  ?><a style="color:#075722;font-weight: bold" href="<?php echo admin_url(); ?>opposite_parties/opposite_party/<?php echo $contract->other_party; ?>">
                        <?php
                                           foreach ($oppositeparty_names as $req) {
    if ($req['id'] == $contract->other_party) {
        $subject = $req['name'];
         echo  $subject;
        break; // stop after first match
    }
} 
?>
                      </a></td>
               </tr>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_value'); ?></td>
                  <td><?php echo $contract->contract_value; ?></td>
               </tr>
                 <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_type'); ?></td>
              <td>  <?php foreach ($types as $req) {
    if ($req['id'] == $contract->contract_type) {
        $subject = $req['name'];
         echo  $subject;
        break; // stop after first match
    }
}
					  ?></td>
               </tr>
                   <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_template'); ?></td>
                  <td>  <?php 


					  ?><a style="color:#075722;font-weight: bold" href="#">
                        <?php
                                           foreach ($templates as $req) {
    if ($req['id'] == $contract->contract_template_id) {
        $subject = $req['name'];
         echo  $subject;
        break; // stop after first match
    }
} 
?>
                      </a></td>
               </tr>
              
      </tbody>
   </table>
</div>
<div class="col-md-6  project-percent-col mtop2">
   
   <p class="bold text-center hide"><?php echo _l('project_progress_text'); ?></p>
   <div class="project-progress relative mtop15 text-center ">
      <strong class="project-percent"></strong>
   </div>
  
    <div class="col-md-12 border-right project-overview-left">
    <table class="table no-margin project-overview-table">
            <tbody>
             <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_start_date'); ?></td>
                  <td><?php $date=_d($contract->datestart); echo $date ; ?></td>
               </tr>
                <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_end_date'); ?></td>
                  <td><?php $date=_d($contract->dateend); echo $date ; ?></td>
               </tr> 
                </tr>
                   <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('payment_terms'); ?></td>
                  <td>  <?php 


					  ?><span style=" bold" href="#">
                        <?php
                          $payment_terms=get_payment_terms();  
                                           foreach ($payment_terms as $req) {
    if ($req['id'] == $contract->payment_terms) {
        $subject = $req['name'];
         echo  $subject;
        break; // stop after first match
    }
} 
?>
                      </span></td>
               </tr>
                <tr class="project-overview-file-no hide">
                  <td class="bold"><?php echo _l('no_of_installment'); ?></td>
                  <td><?php echo $contract->no_of_installment; ?></td>
               </tr>
             <tr class="project-overview-file-no hide">
                  <td class="bold"><?php echo _l('default_effective_date'); ?></td>
                  <td><?php echo $contract->default_effective_date; ?></td>
               </tr>
               <tr class="project-overview-file-no hide">
                  <td class="bold"><?php echo _l('installment_amount'); ?></td>
                  <td><?php echo $contract->installment_amount; ?></td>
               </tr>
                   <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('status'); ?></td>
                  <td>  <?php 


					  ?><a style="color:#075722;font-weight: bold" href="#">
                        <?php $selected = (isset($contract) ? $contract->status : '2');
                        if($selected==0){
                           $selected =2;
                        }
                        // print_r($selected);
                                           foreach ($statuses as $req) {
    if ($req['id'] == $selected) {
        $subject = $req['name'];
         echo  $subject;
        break; // stop after first match
    }
} 
?>
                      </a></td>
               </tr>
  <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('final_expiry_date'); ?></td>
                  <td><?php $date=_d($contract->final_expiry_date); echo $date ; ?></td>
               </tr> 
               
		</tbody>
	</table>
	</div>
</div>
</div>
		 </div>
			   </div>
             <div class="row">
			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="tc-content project-overview-description">
   <p class="text-uppercase bold text-dark font-medium" style="color: green"><?php echo _l('contract_description'); ?></p>
   <hr class="hr-panel-heading project-area-separation" />
  
   <?php if(empty($contract->description)){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_contract') . '</p>';
   }
   echo '<b>'.check_for_links($contract->description).'</b>';?>
</div>
			</div></div></div></div>
			   	
</div>
<div class="col-md-5 project-overview-right">
  			 <div class="row">
				
   			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="team-members project-overview-team-members">
   
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('contract_assignees'); ?>
   </p>
   <div class="clearfix"></div>
   <?php
   if(count($project_members) == 0){
      echo '<p class="text-muted mtop10 no-mbot">'._l('no_contract_members').'</p>';
   }
   foreach($project_members as $member){ ?>
   <div class="media">
      <div class="media-left">
         <a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>">
            <?php echo staff_profile_image($member['staff_id'],array('staff-profile-image-small','media-object')); ?>
         </a>
      </div>
      <div class="media-body">
         
         <h5 class="media-heading mtop5"><a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>"><?php echo get_staff_full_name($member['staff_id']); ?></a>
            <?php if(has_permission('projects','','create') || $member['staff_id'] == get_staff_user_id()){ ?>
            <br /><small class="text-muted"><?php echo _l('total_logged_hours_by_staff') .': '.seconds_to_time_format($member['total_logged_time']); ?></small>
            <?php } ?>
         </h5>
      </div>
   </div>
   <?php } ?>

   <div class="clearfix"></div>
       <hr class="hr-panel-heading project-area-separation" />


</div>
			</div></div></div></div>
     <?php
   if(count($contract_amendments) > 0){?>     
      <div class="row">
				
   			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('contract_amendment'); ?>
   </p>
   <div class="clearfix"></div>

                     <table 
	class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('amendment_number')?></th>
			<th width="50%" class="hide"><?php echo _l('file_name')?></th>
			<th><?php echo _l('amendment_text')?></th>
			<th class="hide"><?php echo _l('contract_effectivedate')?></th>
			<th ><?php echo _l('status')?></th>
			<th><?php echo _l('created_by')?></th>
			<th><?php echo _l('attachments')?></th>
			<th class="hide"><?php echo _l('action')?></th>
			
		</tr>
	</thead>



   <?php foreach ($contract_amendments as $contract_amendment){?>
      <tr>
      		<td><?php echo $contract_amendment['amendment_number']?></td>
			<td width="50%" class="hide"><?php echo $contract_amendment['amendement_file'];?> </td>
			<td title="<?php echo htmlspecialchars($contract_amendment['amendment_text']); ?>">
  <?php 
    $text = $contract_amendment['amendment_text'];
    echo strlen($text) > 50 ? substr($text, 0, 50) . '...' : $text;
  ?>
</td>

			<td class="hide"><?php echo _d($contract_amendment['effective_date'])?></td>
			<td><?php echo ucwords($contract_amendment['amend_status'])?></td>
			<td><?php echo get_staff_full_name($contract_amendment['created_by'])?></td>
			<td><?php
		  $path1 = site_url('download/downloadagreementamendment/'. $contract_amendment['contract_id'].'/'.$contract_amendment['id']);
			 
    $file_path   = get_upload_path_by_type('contract').$contract_amendment['contract_id'].'/'.$contract_amendment['amendement_file'];
    if(file_exists($file_path) && !empty($contract_amendment['amendement_file'])){ 
	
		$dispaly = '<a href="'. $path1 .'"  class="btn btn-sm btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('download').'" data-placement="bottom"><i class="fa fa-download" aria-hidden="true"></i></a>';
		
		echo $dispaly;
	}else{
        echo  '-';
    }
	 ?>
	
			</td>
			<td>
				         
                              <?php echo icon_btn('contracts/delete_amendment/' . $contract_amendment['id'].'/'.$contract_amendment['contract_id'], 'remove', 'btn-danger _delete hide '); ?>
			</td>
     
		</tr>
   <?php } ?>
   </table>
			</div></div></div></div> <?php } ?>
      
      </div>
</div>
<!-- /.modal -->
