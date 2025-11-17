<?php ############### New Approval Start ##################### ?>
   <a class="btn btn-info mbot25" data-toggle="collapse" href="#newApproval" role="button" aria-expanded="false" aria-controls="newApproval"><i class="fa fa-plus"></i>
    <?php echo _l('add_new').' '._l('approval'); ?>
   </a>

   <div class="collapse" id="newApproval">
     
         <table class="table text-center">
            <tr>
               <th></th>
               <th colspan="2" align="center">
                 
                  <div class="form-group">
                     <?php ############## Refeence No ################### ?>
                     <label for="<?php echo _l('approval_name'); ?>"><small class="req text-danger">* </small><?php echo _l('approval_name'); ?></label>
                     <?php
                      $next_ref_number = get_option('next_reference_no');
                        $prefix = get_option('reference_prefix');
					   $_file_number = str_pad($next_ref_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>
                     <input type="text" name="approval_name" id="approval_name" class="form-control" value="<?=$prefix.$_file_number?>" >
                  </div></th>
               <th></th>
            </tr>
            <?php 
            $approval_types = get_approval_types('expense'); ?>
         
            <tr>
             <?php foreach($approval_types as $approval_type){ ?>  
               <th><?=$approval_type['name']?>
                  <div class="form-group select-placeholder">
                     <select name="approval_assigned[]" data-live-search="true" id="approval_assigned" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                        <?php foreach($staff as $member){
                           if($member['active'] == 0 && $project->assigned != $member['staffid']) {
                              continue;
                           }
                           ?>
                           <option value="<?php echo $member['staffid']; ?>" <?php if(get_staff_user_id() == $member['staffid'] && $approval_type['id']== 1){echo 'selected';} ?>>
                              <?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
                           </option>
                        <?php } ?>
                     </select>
                  </div>
               </th>
               <?php } ?>
            </tr>
            <tr>
               <td colspan="<?=sizeof($approval_types)?>">
                  <div class="col-md-12 text-center">
                     <button type="button"  class="btn btn-info save_changes_expense_approval_ticket"><?php echo _l('submit'); ?></button>
                  </div>
               </td>
            </tr>
         </table>
     
   </div>

<?php ############# New Approval End ######################  ?>


<div class="row">
<div class="col-md-12">
<ul class="nav nav-tabs" id="expenseApprovalTabs" role="tablist"> 
<?php
	if(is_array($approvals)){
	foreach ($approvals as $key => $approval) { 
   $tab_name = str_replace(' ', '-', strtolower($approval['approval_name']));
   ?>
   <li class="nav-item <?php if($key == 0) echo 'active'; ?>" role="presentation">
    <a class="nav-link <?php if($key == 0) echo 'active'; ?>" id="<?php echo $tab_name;?>-tab" data-toggle="tab" href="#<?php echo $tab_name;?>" role="tab" aria-controls="home" aria-selected="true"><?php echo $approval['approval_name']; ?><br></a>
  </li>
  
<?php } 
	}?>
</ul>   

<div class="tab-content" id="expenseApprovalTabsContent">
 
   <?php 
	
	if(is_array($approvals)){
		foreach ($approvals as $key2 => $approval2) { 
      $tab_name = str_replace(' ', '-', strtolower($approval2['approval_name']));?>
      
      <div class="tab-pane fade <?php if( $key2 == 0) echo 'in active';?>" id="<?php echo $tab_name;?>" role="tabpanel" aria-labelledby="<?php echo $tab_name;?>-tab">
            <a target="_blank" href="<?php echo admin_url('projects/expense_statement/'.$approval2['id'].'/'.$project->id); ?>" class="btn btn-warning btn-with-tooltip mleft25 mbot25" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Expense Statement"> <i class="fa fa-file-pdf-o"></i>Expense statement(Approve)  </a>
            <table class="table table-bordered text-center">
               <?php $approval_headings = $approval2['approval_headings']; 
                  foreach ($approval_headings as $approval_heading) { ?>
                     <tr>
                        <th>
                           <div class="col-md-3">
                            <?php echo get_approval_heading_name_by_id($approval_heading['approval_heading_id']);?>  
                            <select name="approval_assigned[]" data-live-search="true" id="approval_assigned" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" disabled>
                              <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                              <?php foreach($staff as $member){
                                         
                                 if($member['active'] == 0 && $project->assigned != $member['staffid']) {
                                    continue;
                                 }
                                 ?>
                                 <option value="<?php echo $member['staffid']; ?>" <?php if( $approval_heading['staffid'] == $member['staffid'] ){ echo 'selected';} ?>  >
                                    <?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
                                 </option>
                                 <?php } ?>
                              </select>
                           </div>

                           <div class="col-md-2 mtop20">
                              <?php  if(($approval_heading['staffid'] != get_staff_user_id())|| ($approval_heading['approval_status']==3)){ 
                              $attr=array('disabled'=>'disabled'); } else{
                                 $attr=array('onchange'=>'update_approval_status(this,'.$approval_heading['id'].')');}
                                 ?>
                              <?php echo render_select('approval_status',$appro_statuses,array('ticketstatusid','name'),'',$approval_heading['approval_status'],$attr,array(),'no-mbot','',false); ?>
                           </div>
                           
                           <div class="col-md-7 mtop8">
                              <?php  if(($approval_heading['staffid'] != get_staff_user_id())){ 
                              $attr=array('rows'=>2,'placeholder'=>'Remarks','disabled'=>'disabled'); } else{
                                 $attr=array('rows'=>2,'placeholder'=>'Remarks','onblur'=>'update_approval_remarks(this,'.$approval_heading['id'].')');}
                                 ?>
                              <?php echo render_textarea('approval_remarks','',$approval_heading['approval_remarks'],$attr); ?>
                           </div>
                        </th>
                     </tr>   
                  <?php } ?>
            </table>
      </div>
   <?php } 
	}?>
</div>

</div>
</div>





      