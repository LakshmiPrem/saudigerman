    <div id="matter-clients-report" class="hide">
      <div class="row">
         
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid2" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-client-report scroll-responsive">
            <thead>
               <tr> 
                  <th class="not_sortable">Sr. No.</th>
                  <th><?php echo _l('project_customer'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                    <th><?php echo _l('case_title'); ?></th>
                      <th><?php echo _l('lawyer_name'); ?></th>
                      <th><?php echo _l('legal_coordinator'); ?></th>
                      <th><?php echo _l('date_filing'); ?></th>
                    <th ><?php echo _l('case_no'); ?></th>
                     <th><?php echo _l('case_nature'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>
                   <th><?php echo _l('claiming_amount'); ?></th>
                    <th><?php echo _l('court_expenses'); ?></th>
                     <th><?php echo _l('total_amount'); ?></th>
                   <th><?php echo _l('settlement_amount'); ?></th>
                   <th><?php echo _l('execution_amount'); ?></th>
                    <th><?php echo _l('judgement_amount'); ?></th>
                  
                    <th width="25%"><?php echo _l('case_updates'); ?></th>
                     <th><?php echo _l('status'); ?></th>
                  
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
               </tr>
            </tfoot>
         </table>
   </div>
