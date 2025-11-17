    <div id="matter-verification" class="hide">
       <div class="row">
         
             <div class="col-md-3">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid27" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="col-md-3">
            <div class="form-group">
                 <?php echo render_select('lawyerid24',$lawyers_arr,array('staffid','full_name'),'lawyer_assigned','');?>
            </div>
         </div>
           <div class="col-md-3">
            <div class="form-group">
                 <?php echo render_select('lawyerid25',$lawyers_arr,array('staffid','full_name'),'legal_coordinator','');?>
            </div>
         </div>
      
         
         <div class="clearfix"></div>
      </div> 
         <table class="table table-matter-verification-report scroll-responsive">
            <thead>
               <tr>
                   <th class="not_sortable">Sr. No.</th>
                    <th><?php echo _l('client');?></th>
                     <th><?php echo _l('ledger_code'); ?></th>
                   <th><?php echo _l('case_title'); ?></th>
                   <th><?php echo _l('law_firm'); ?></th>
                    <th><?php echo _l('legal_coordinator'); ?></th>
                   <th><?php echo _l('date_filing'); ?></th>
                  <th><?php echo _l('case_no'); ?></th>
                     <th><?php echo _l('case_nature'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>
                 <th><?php echo _l('claiming_amount'); ?></th>
                   <th><?php echo _l('court_expenses'); ?></th>
                    <th><?php echo _l('total_amount'); ?></th>
                     <th><?php echo _l('judgement_amount'); ?></th>
                   <th><?php echo _l('execution_amount'); ?></th>
                   <th><?php echo _l('settlement_amount'); ?></th>
                   <th><?php echo _l('amount_received'); ?></th>
                    <th><?php echo _l('balance'); ?></th>
                    <th  width="25%"><?php echo _l('case_update'); ?></th>
                     <th><?php echo _l('verified_by'); ?></th>
                    <th><?php echo _l('verified_date'); ?></th>
                    <th  width="25%"><?php echo _l('scope'); ?></th>
                   
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
				  <td></td>
                <td></td>
                  <td></td>
				  <td></td>
                 
                  </tr>
            </tfoot>
         </table>
   </div>
