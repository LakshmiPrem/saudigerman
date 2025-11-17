<?php ############### New Approval Start ##################### ?>
   <!-- <a class="btn btn-info mbot25" data-toggle="collapse" href="#newApproval" role="button" aria-expanded="false" aria-controls="newApproval"><i class="fa fa-plus"></i>
    <?php echo _l('add_new').' '._l('approval'); ?>
   </a> -->

   <div class="collapse in" id="newApproval">
     
         <table class="table text-center">
            <tr>
               <th></th>
               <th colspan="2" align="center">
                 
                  <div class="form-group">
                     <?php ############## Refeence No ################### ?>
                     <label for="<?php echo _l('approval_name'); ?>"><small class="req text-danger">* </small><?php echo _l('approval_name'); ?></label>
                     <?php
                      $next_ref_number = get_option('next_reference_no');
                      $prefix = 'APPROVAL';
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
                     <button type="button"  class="btn btn-info "><?php echo _l('submit'); ?></button>
                  </div>
               </td>
            </tr>
         </table>
     
   </div>

<?php ############# New Approval End ######################  ?>


