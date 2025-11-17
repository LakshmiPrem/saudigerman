    <div id="matter-age-wise-report" class="hide">
      <div class="row">
         
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid2" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
          <div class="col-md-3">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="p_status1" name="p_status1" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($proj_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-age-wise-report scroll-responsive">
            <thead>
               <tr> 
                  <th><?php echo _l('case_title'); ?></th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                  <th><?php echo _l('casediary_file_no'); ?></th>
                  <th><?php echo _l('project_status'); ?></th>
                  <th><?php echo _l('project_start_date'); ?></th>   
                  <th><?php echo _l('age_in_days'); ?></th> 
                  <!-- <th><?php echo _l('age_in_days'); ?></th>   -->             
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
                  <!-- <td></td> -->
               </tr>
            </tfoot>
         </table>
   </div>
