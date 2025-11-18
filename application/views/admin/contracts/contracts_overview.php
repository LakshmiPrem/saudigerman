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
               <?php if($contract->type=='contracts') { ?>
               <tr class="project-overview-file-no <?php if((isset($contract) && !customer_has_projects($contract->client))){ echo ' hide';} ?> ">
                  <td class="bold"><?php echo _l('project'); ?></td>
                  <td><?php if(isset($contract) && $contract->project_id != 0){
                        echo get_project_name_by_id($contract->project_id);
                     } ?></td>
               </tr>
               <?php } ?>

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
                <?php if($contract->type=='contracts') { ?>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_value'); ?></td>
                  <td><?php echo number_format($contract->contract_value,2); ?></td>
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
                   <tr class="project-overview-file-no hide ">
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
               
               
                 <?php if(!empty($contract->contract_category)){ ?>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_category'); ?></td>
                  <td><?php  echo get_contract_categorybyid($contract->contract_category) ; ?></td>
               </tr> 
             <?php } ?>

             <?php if(!empty($contract->contract_subcategory)){ ?>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('contract_subcategory'); ?></td>
                  <td><?php  echo get_contract_subcategorybyid($contract->contract_subcategory) ; ?></td>
               </tr> 
             <?php } ?>
             <?php } ?> 
      </tbody>
   </table>
</div>
<?php if($contract->type=='contracts') { ?>
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
              <?php  if(!empty($contract->status )){?>
                   <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('status'); ?></td>
                  <td>  <?php 


					  ?><a style="color:#075722;font-weight: bold" href="#">
                        <?php $selected =$contract->status ;
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
             <?php } ?>
  <tr class="project-overview-file-no  hide">
                  <td class="bold"><?php echo _l('final_expiry_date'); ?></td>
                  <td><?php $date=_d($contract->final_expiry_date); echo $date ; ?></td>
               </tr> 
                <?php if(!empty($contract->is_receivable)){ ?>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('receivable_payable'); ?></td>
                  <td><?php  echo _l('is_receivable') ; ?></td>
               </tr> 
             <?php } ?>
          <?php if(!empty($contract->is_payable)){ ?>
               <tr class="project-overview-file-no ">
                  <td class="bold"><?php echo _l('receivable_payable'); ?></td>
                  <td><?php  echo _l('is_payable') ; ?></td>
               </tr> 
             <?php } ?>
             
             
           
               
		</tbody>
	</table>
	</div>
</div>
<?php } ?>
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
            <?php
   if(count($contract_risklist) > 0){?>     
      <div class="row">
        
         <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('checklists'); ?>
      <span class=""><a class="btn btn-link btn-sm pull-right" style="" href="<?php echo admin_url('contracts/contract/'.$contract->id.'?tab=risklist'); ?>"> <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a></span>
   </p>
   <div class="clearfix"></div>
 
     <table 
  class="table dt-table" data-order-col="0" data-order-type="desc">
  <thead>
    <tr>
       <th><?php echo _l('keyarea_provision')?></th>
      <th><?php echo _l('status')?></th>
    
     
    
      
    </tr>
  </thead>



   <?php foreach ($contract_risklist as $approval){
    if($approval['approval_status']==1)
       $risk_status='Complaint';
    elseif($approval['approval_status']==2)
      $risk_status='Non-Complaint';
    elseif($approval['approval_status']==3)
      $risk_status='Not Relevant';
    else
      $risk_status='';
    ?>
      <tr>
        <td><?=$approval['riskname'].'<br>'.$approval['riskprovision']?> </td>
    
          <td width="20%"><?php echo $risk_status; ?></td>
      
     
    </tr>
   <?php } ?>
   </table>
         </div>
  </div>
  </div>
  </div>
<?php } ?>
      <?php
       if($contract->type!=='po'){  
   if(count($contract_negotiations) > 0){?>     
      <div class="row">
        
         <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('contract_negotiations'); ?>
       <span class=""><a class="btn btn-link btn-sm pull-right" style="" href="<?php echo admin_url('contracts/contract/'.$contract->id.'?tab=negotiation'); ?>"> <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a></span>
   </p>
   <div class="clearfix"></div>
 
     <table 
  class="table dt-table">
  <thead>
    <tr>
       <th><?php echo _l('negotiate_value')?></th>
      <th><?php echo _l('hearing_comments')?></th>
    
     
    
      
    </tr>
  </thead>



   <?php foreach ($contract_negotiations as $contract_negotiation){?>
      <tr>
        <td width="20%"><?php echo number_format($contract_negotiation['negotiate_value'],2);?> </td>
    
          <td><?php echo $contract_negotiation['content']?></td>
      
     
    </tr>
   <?php } ?>
   </table>
         </div>
  </div>
  </div>
  </div>
<?php } ?>
<?php } ?>
             <div class="row">
			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="tc-content project-overview-description">
   <p class="text-uppercase bold text-dark font-medium" style="color: green"><?php echo _l('approvals'); ?></p>
   <hr class="hr-panel-heading project-area-separation" />
   <div id="div_approvals_list_overview"></div>
</div>
			</div></div></div></div>

			   	
</div>
<div class="col-md-5 project-overview-right">
   <?php if ($contract->type != 'contracts') { ?>
<div class="row">
    <div class="col-md-12">
        <div class="panel_s panel-info">

            <div class="panel-heading">
                <h4 class="panel-title">
                    <i class="fa fa-file-text-o"></i> <?php echo _l($contract->type); ?>
                </h4>
            </div>

            <div class="panel-body">

                <?php if ($contract->marked_as_signed == 1) { ?>

                    <p class="text-success mtop10">
                        <i class="fa fa-check-circle"></i> This <?php echo _l($contract->type); ?> has been signed.
                    </p>

                    <div class="btn-group mtop15" role="group">

                        <!-- DOWNLOAD USING CONTRACT VERSION LOGIC -->
                        <?php
                            $totalversions = total_rows(db_prefix().'contract_versions','contractid='.$contract->id);
                            if ($totalversions > 0) {
                                $latest_version = get_current_contract_versioninfo($contract->id);

                                $path1 = site_url('download/downloadagreementversion/' . 
                                    $latest_version->contractid . '/' . $latest_version->id);

                                $file_path = get_upload_path_by_type('contract') . 
                                    $latest_version->contractid . '/' . 
                                    $latest_version->version_internal_file_path;

                                if (file_exists($file_path)) {
                                    echo '<a href="'.$path1.'" class="btn btn-primary" 
                                            data-toggle="tooltip" title="Download Signed PO">
                                            <i class="fa fa-download"></i> Download PO
                                          </a>';
                                }
                            }
                        ?>

                        <!-- VIEW -->
                        <a href="<?php echo admin_url('contracts/contract_external_review/' . $contract->id); ?>" 
                           class="btn btn-info">
                            <i class="fa fa-eye"></i> View <?php echo _l($contract->type); ?>
                        </a>

                        <!-- EMAIL -->
                        <a href="#" data-target="#contract_send_to_client_modal" data-toggle="modal"
                           class="btn btn-success">
                            <i class="fa fa-envelope"></i> Email <?php echo _l($contract->type); ?>
                        </a>
                    </div>

                   

                <?php } else { ?>

                    <p class="text-warning mtop10">
                        <i class="fa fa-hourglass-half"></i> This <?php echo _l($contract->type); ?> is not Signed Yet.
                    </p>

                <?php } ?>
                 <!-- <p class="text-success mtop10">
                        <i class="fa fa-check-circle"></i> This <?php //echo _l($contract->type); ?> has been Stamped.
                 </p> -->

            </div>



        </div>
    </div>
</div>
<?php } ?>
   <?php if($contract->type=='contracts') { ?>
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
           
         </h5>
      </div>
   </div>
   <?php } ?>

   <div class="clearfix"></div>
       <hr class="hr-panel-heading project-area-separation" />


</div>
			</div></div></div></div>
			
			
			
			
			<!--------------renewal------------------------>

<div class="row">
	<div class="col-md-12">
      <div class="panel_s panel-info">
         <div class="panel-body">

         <p class="bold font-size-14 project-info" style="color: #FF1493">
            <?php echo _l('contract_renewals'); ?>
         </p>

         <table class="table dt-table">
            <thead>
               <tr>
                  <th><?php echo _l('Total Contract Old Value')?></th>
                  <th><?php echo _l('Total Contract New Value')?></th>
                  <th><?php echo _l('Old Contract Period')?></th>
                  <th><?php echo _l(' New Contract Period ')?></th>
                  <th><?php echo _l('  Old Contract Value/ Year  ')?></th>
                  <th><?php echo _l('   New Contract Value/Year  ')?></th>
                  <th><?php echo _l('  Price Variance/Year  ')?></th>
                  <th><?php echo _l('   Price Variance/Total  ')?></th>
                  
                  
               </tr>
            </thead>


            <?php foreach ($contract_renewal_history as $contract_amendment){
               
               $old_start_date = $contract_amendment['old_start_date'];
               $old_end_date   = $contract_amendment['old_end_date'];

               $start = new DateTime($old_start_date);
               $end   = new DateTime($old_end_date);

               $diff = $start->diff($end);
               $old_period = $diff->days;
               
               
               $new_start_date = $contract_amendment['new_start_date'];
               $new_end_date   = $contract_amendment['new_end_date'];

               $start = new DateTime($new_start_date);
               $end   = new DateTime($new_end_date);

               $diff = $start->diff($end);
               $new_period = $diff->days;?>
               <tr>
                     <td><?php echo $contract_amendment['old_value']?></td>
                     <td><?php echo $contract_amendment['new_value']?></td>
                     <td><?php echo $old_period;?></td>
                     <td><?php echo $new_period;?></td>
                     <td><?php echo $contract_amendment['old_value']?></td>
                     <td><?php echo $contract_amendment['new_value']?></td>
                     <?php
$old_value  = isset($contract_amendment['old_value']) ? (float)$contract_amendment['old_value'] : 0;
$new_value  = isset($contract_amendment['new_value']) ? (float)$contract_amendment['new_value'] : 0;
$new_period = isset($new_period) ? (float)$new_period : 0;

$diff = $old_value - $new_value;

// If negative, set to 0
if ($diff < 0) {
    $diff = 0;
}

$total = $diff * $new_period;

// If total is negative (just in case), also set to 0
if ($total < 0) {
    $total = 0;
}
?>
<td><?php echo number_format($diff, 2); ?></td>
<td><?php echo number_format($total, 2); ?></td>

                  
                  
            
               </tr>
            <?php } ?>
         </table>

         </div>
      </div>
   </div>
</div>
<!--------------renewal------------------------>
     <?php
   if(count($contract_amendments) > 0){?>     
      <div class="row">
				
   			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('contract_amendment'); ?>
       <span class=""><a class="btn btn-link btn-sm pull-right" style="" href="<?php echo admin_url('contracts/contract/'.$contract->id.'?tab=tab_amendment'); ?>"> <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a></span>

   </p>
   <div class="clearfix"></div>
 <table 
  class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('amendment_number')?></th>
			<th width="20%" class="hide"><?php echo _l('file_name')?></th>
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
			<td width="20%" class="hide"><?php echo $contract_amendment['amendement_file'];?> </td>
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
			</div></div></div></div> 
    <?php } ?>
    
 <?php
   if(count($contract_postactions) > 0){?>     
      <div class="row">
        
         <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('sign_contracts'); ?>
       <span class=""><a href="#" data-toggle="tooltip" data-title="<?php _l('contract_postaction')?>" class="btn btn-info  btn-icon pull-right" onclick="upload_contractreview(<?= $contract->id ?>); return false;">
            <i class="fa fa-comment"></i> <?=_l('post_review')?>                </a></span>

   </p>
   <div class="clearfix"></div>
 <table 
  class="table dt-table">
  <thead>
    <tr>
      <th><?php echo _l('action_category')?></th>
      <th><?php echo _l('project_description')?></th>
     <th><?php echo _l('due_date')?></th>
     <th><?php echo _l('created_by')?></th>
       <th ><?php echo _l('status')?></th>
      <th><?php echo _l('attachments')?></th>
     
      <th class="hide"><?php echo _l('action')?></th>
      
    </tr>
  </thead>



   <?php foreach ($contract_postactions as $contract_postaction){?>
      <tr>
          <td><?php echo get_contractaction_name_by_id($contract_postaction['category_id']); ?></td>
          <td title="<?php echo htmlspecialchars($contract_postaction['description']); ?>">
  <?php 
    $text = $contract_postaction['description'];
    echo strlen($text) > 50 ? substr($text, 0, 50) . '...' : $text;
  ?>
</td>

      <td class=""><?php echo _d($contract_postaction['due_date'])?></td>
        <td><?php echo get_staff_full_name($contract_postaction['created_by'])?></td>
      <td><?php echo ucwords($contract_postaction['status'])?></td>
    
      <td><?php
      $path1 = site_url('download/downloadagreementpostaction/'. $contract_postaction['contract_id'].'/'.$contract_postaction['id']);
       
    $file_path   = get_upload_path_by_type('contract').$contract_postaction['contract_id'].'/'.$contract_postaction['post_attachment'];
    if(file_exists($file_path) && !empty($contract_postaction['post_attachment'])){ 
  
    $dispaly = '<a href="'. $path1 .'"  class="btn btn-sm btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('download').'" data-placement="bottom"><i class="fa fa-download" aria-hidden="true"></i></a>';
    
    echo $dispaly;
  }else{
        echo  '-';
    }
   ?>
  
      </td>
      <td>
                 
                              <?php echo icon_btn('contracts/delete_postaction/' . $contract_postaction['id'].'/'.$contract_postaction['contract_id'], 'remove', 'btn-danger _delete hide '); ?>
      </td>
     
    </tr>
   <?php } ?>
   </table>
      </div></div></div></div> 
    <?php } ?>
 <?php } ?>
         <div class="row contract_comments_overview">
				
   			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
  
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('contract_comments'); ?>
   </p>
   <div class="clearfix"></div>
 <div id="contract-comments-overview"></div>
                 
			</div></div></div></div>
               <div class="row hide">
       <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="tc-content project-overview-description">
   <p class="text-uppercase bold text-dark font-medium" style="color: green"><?php echo _l('contract_summary_ai'); ?></p>
   <hr class="hr-panel-heading project-area-separation" />
  
   <?php if(empty($contract->ai_agreement_summary)){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_summary_contract') . '</p>';
   }
   echo '<b>'.check_for_links($contract->ai_agreement_summary).'</b>';?>
</div>
      </div></div></div></div>
      
      </div>
</div>
<!-- /.modal -->
