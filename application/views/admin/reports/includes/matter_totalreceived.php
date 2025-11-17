 <div id="matter-totalreceived" class="hide">
       <div class="row">
         
             <div class="col-md-3">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid32" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>

         
         <div class="clearfix"></div>
      </div> 
         <table class="table table-matter-totalreceived-report scroll-responsive">
            <thead>
               <tr>
                   <th class="not_sortable">Sr. No.</th>
                    <th><?php echo _l('client'); ?></th>
                     <th><?php echo _l('case_title'); ?></th>
                     <th><?php echo _l('casediary_oppositeparty'); ?></th>
                      <th><?php echo _l('law_firm'); ?></th>
                       <th><?php echo _l('hearing_court'); ?></th>
                    <th><?php echo _l('receipt_date'); ?></th>
                   <th><?php echo _l('installment_amount'); ?></th>
                   <th><?php echo _l('amount_received'); ?></th>
                   <th><?php echo _l('balance'); ?></th>
                <!--  <th><?php echo _l('installment_status'); ?></th>-->
                  <th><?php echo _l('remarks'); ?></th>
                   
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
                <!--  <td></td>-->
                 
                  </tr>
            </tfoot>
         </table>
   </div>
