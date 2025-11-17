    <div id="matter-individualcase-report" class="hide">
      <div class="row">
           <div class="col-md-4">
                               
               <?php  echo render_select('case_id1',$cases,array('id',array('name','company','file_no')),'projects');?>
                              </div>
       <!--  <div class="col-md-3">
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
         <div class="col-md-3">
            <?php echo render_select('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty'); ?>
         </div>-->
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-individualcase-report scroll-responsive">
            <thead>
               <tr>
                  <th class="not_sortable">Sr. No.</th>
                  <th><?php echo _l('project_customer'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                    <th><?php echo _l('case_title'); ?></th>
                      <th><?php echo _l('lawyer_name'); ?></th>
                     
                       <th><?php echo _l('legal_coordinator'); ?></th>
				   <th ><?php echo _l('case_no'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>
                   <th><?php echo _l('claiming_amount'); ?></th>
                    <th><?php echo _l('court_expenses'); ?></th>
                     <th><?php echo _l('total_amount'); ?></th>
                   <th><?php echo _l('settlement_amount'); ?></th>
                   <th><?php echo _l('execution_amount'); ?></th>
                    <th><?php echo _l('judgement_amount'); ?></th>
                   <th><?php echo _l('amount_received'); ?></th>
                    <th><?php echo _l('balance'); ?></th>
                    <th class="truncate"><?php echo _l('case_updates'); ?></th>
                     <th><?php echo _l('status'); ?></th>
                      <th width="15px"><?php echo _l('project_court_attach'); ?></th>
                   
                <!--  <th><?php echo _l('business_responsibility'); ?></th>
                  <th><?php echo _l('law_firm'); ?></th>-->
                  
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
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
               	  <td></td>
                  <td class='truncate'></td>
                  <!--   <td></td>-->
               </tr>
            </tfoot>
         </table>
   </div>
