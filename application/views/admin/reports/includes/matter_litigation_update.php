<div id="matter-litigation-update-report" class="hide">
      <div class="row mbot20">
         
         <div class="col-md-3">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid3" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="col-md-3">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="p_status" name="p_status" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($proj_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
        <!-- <div class="col-md-3">
            <?php
              $path = base_url('uploads/reports/'._l('litigation_collective_report').'.pdf');
		   $path1 = base_url('uploads/reports/'._l('litigation_collective_update_report').'.pdf');
      ?>
            <?php echo render_select('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty'); ?>
         </div>-->
            <div class="col-md-3 mtop20">
            <a href="<?php echo $path;?>" target="_blank" class="btn btn-info"><i class="fa fa-file-pdf-o"></i> <?php echo _l('report_with_description')?></a>
         </div>
          <div class="col-md-3 mtop20">
            <a href="<?php echo $path1;?>" target="_blank"  class="btn btn-info"><i class="fa fa-file-pdf-o"></i> <?php echo _l('report_without_description')?></a>
         </div>
         <div class="clearfix"></div>
      </div>
      
         <table class="table table-matter-litigation-update-report scroll-responsive">
            <thead>
               <tr>
                  <th class="not_sortable"><?php echo _l('sl_no'); ?></th>
                <!--  <th><?php echo _l('matter_id'); ?></th>
                   <th><?php echo _l('project_name'); ?></th>-->
                  <th><?php echo _l('client_position'); ?></th>
                   <th><?php echo _l('opposite_party_position'); ?></th>
                   <th><?php echo _l('case_no'); ?></th>
                   <th><?php echo _l('hearing_postponed_until'); ?></th>
                   <th><?php echo _l('case_type'); ?></th>
                <!--    <th><?php echo _l('case_nature'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>-->
                  <th><?php echo _l('claiming_amount'); ?></th>
                   <th><?php echo _l('case_updates'); ?></th>
                   <th><?php echo _l('project_description'); ?></th>
                    <th><?php echo _l('law_firm'); ?></th>
                
                     <th><?php echo _l('project_start_date'); ?></th>
                                   
               </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                 <!--  <td></td>
                 <td></td>-->
                                  
               </tr>
            </tfoot>
         </table>
   </div>
 