    <div id="referrals-report" class="hide">
      <div class="row">
         <div class="col-md-4 hide">
            <div class="form-group">
               <label for="invoice_status"><?php echo _l('report_invoice_status'); ?></label>
               <select name="invoice_status" class="selectpicker" multiple data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <?php foreach($invoice_statuses as $status){ if($status ==5){continue;} ?>
                  <option value="<?php echo $status; ?>"><?php echo format_invoice_status($status,'',false) ?></option>
                  <?php } ?>
               </select>
            </div>
         </div>
         <?php if(count($invoices_sale_agents) > 0 ) { ?>
         <div class="col-md-4 hide">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('sale_agent_string'); ?></label>
               <select name="sale_agent_invoices" class="selectpicker" multiple data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <?php foreach($invoices_sale_agents as $agent){ ?>
                  <option value="<?php echo $agent['sale_agent']; ?>"><?php echo get_staff_full_name($agent['sale_agent']); ?></option>
                  <?php } ?>
               </select>
            </div>
         </div>
         <?php } ?>
          <div class="col-md-4 hide">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
         <table class="table table-referrals-report scroll-responsive">
            <thead>
<!--    Client   Matter   Invoice Number Invoice Amount Collected Amount  % on Invoice Amount (25%)  % on Collection Amount  Referral Amount based on Invoice Referral Amount based on Collection Invoice Date   Period (From Date & To Date) -->
            
               <tr>
                  <th><?php echo _l('refered_employee_id'); ?></th>
                  <th><?php echo _l('assigned_to'); ?></th>
                  <th><?php echo _l('report_invoice_customer'); ?></th>
                  <th><?php echo _l('projects'); ?></th>
                  <th><?php echo _l('case_type'); ?></th>
                  <th><?php echo _l('project_billing_type'); ?></th>
                  <th><?php echo _l('proposed_value'); ?></th>
                  <th><?php echo _l('report_invoice_date'); ?></th>
                  <th><?php echo _l('report_invoice_number'); ?></th>
                  <!-- <th><?php echo _l('invoice_estimate_year'); ?></th>
                  <th><?php echo _l('report_invoice_duedate'); ?></th> -->
                  <th ><?php echo _l('report_invoice_amount'); ?></th>
                  <th><?php echo _l('report_invoice_amount_with_tax'); ?></th>
                  <th ><?php echo _l('report_invoice_total_tax'); ?></th>
                  <?php foreach($invoice_taxes as $tax){ ?>
                  <th ><?php echo $tax['taxname']; ?> <small><?php echo $tax['taxrate']; ?>%</small></th>
                  <?php } ?>
                  <th><?php echo _l('report_invoice_collected_date'); ?></th>
                  <th><?php echo _l('report_invoice_collected_amount'); ?></th>
                  <th><?php echo _l('referral_staff_amount'); ?></th>
                  <th><?php echo _l('referral_assigned_amount'); ?></th>
                  <th><?php echo _l('total_referral'); ?></th>
                  <th><?php echo _l('referral_staff_collection'); ?></th>
                  <th><?php echo _l('referral_assigned_collection'); ?></th>
                  <th><?php echo _l('invoice_discount'); ?></th>
                  <th><?php echo _l('invoice_adjustment'); ?></th>
                  <th><?php echo _l('applied_credits'); ?></th>
                  <th><?php echo _l('report_invoice_amount_open'); ?></th>
                  <th><?php echo _l('report_invoice_status'); ?></th>
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
                  <!-- <td></td>
                  <td></td>-->
                  <td></td> 
                  <td></td>
                  <td class="subtotal"></td>
                  <td class="total"></td>
                  <td class="total_tax not_visible"></td>
                  <?php foreach($invoice_taxes as $key => $tax){ ?>
                  <td class="total_tax_single_<?php echo $key; ?>"></td>
                  <?php } ?>
                  <td></td>
                  <td class="collected_amount"></td>
                  <td class="referral_1"></td>
                  <td class="referral_2"></td>
                  <td class="total_referral"></td>
                  <td class="referral_3"></td>
                  <td class="referral_4"></td>
                  <td class="discount_total"></td>
                  <td class="adjustment"></td>
                  <td class="applied_credits"></td>
                  <td class="amount_open"></td>
                  <td></td>
               </tr>
            </tfoot>
         </table>
   </div>
