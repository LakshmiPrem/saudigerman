    <div id="matter-updates" class="hide">
      <div class="row">
         
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid4" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-update-report scroll-responsive">
            <thead>
               <tr> 
                  <th><?php echo _l('case_title'); ?></th>
                  <th><?php echo _l('project_customer'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                   <th><?php echo _l('casediary_casenumber'); ?></th>
                   <th><?php echo _l('update_date'); ?></th>
                 
                  <th><?php echo _l('hearing_court'); ?></th> 
                  
                  <th><?php echo _l('proceedings'); ?></th>
                  
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
                 
               </tr>
            </tfoot>
         </table>
   </div>
