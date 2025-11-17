    <div id="matter-settlement" class="hide">
       <div class="row">
            <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid13" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
          <div class="col-md-3">
            <div class="form-group">
                 <?php echo render_select('lawyerid15',$lawyers_arr,array('staffid','full_name'),'lawyer_assigned','');?>
            </div>
         </div>
         <div class="col-md-3">
            <div class="form-group">
               
                <?php echo render_select('nature_type',$settle_nature,array('id','name'),'nature_type','');
				
				?>
            </div>
         </div>
         <div class="clearfix"></div>
      </div> 
        
         <table class="table table-matter-settlement-report scroll-responsive">
            <thead>
               <tr> 
                  <th class="not_sortable">Sr. No.</th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('ledger_code'); ?></th>
                  <th><?php echo _l('case_title'); ?></th>
                  <th><?php echo _l('lawyer_name'); ?></th>
                  <th><?php echo _l('case_no'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>
                  <th><?php echo _l('case_nature'); ?></th>
                  <th><?php echo _l('claiming_amount'); ?></th>
                  <th><?php echo _l('court_expenses'); ?></th>
                  <th><?php echo _l('total_amount'); ?></th>
                  <th><?php echo _l('judgement_amount'); ?></th>
                  <th><?php echo _l('execution_amount'); ?></th>
                  <th><?php echo _l('settlement_amount'); ?></th>
                  
                  <th><?php echo _l('no_of_installment'); ?></th>
                  <th><?php echo _l('project_start_date'); ?></th>
                  <th><?php echo _l('contract_end_date'); ?></th>
                  <th><?php echo _l('amount_received'); ?></th>
                  <th><?php echo _l('balance'); ?></th>
                <!--  <th><?php echo _l('installment_status'); ?></th>-->
                  <th width="25%"><?php echo _l('case_updates'); ?></th>
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
                 
               </tr>
            </tfoot>
         </table>
   </div>
