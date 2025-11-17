    <div id="invoices-report" class="hide">
      <div class="row">
         <div class="col-md-4">
            <div class="form-group">
               <label for="invoice_status"><?php echo _l('case_type'); ?></label>
               <select name="case_type" class="selectpicker"  data-width="100%">
                  <option value="" selected><?php echo _l('invoice_status_report_all'); ?></option>
                  <option value="court_case"><?php echo _l('court_case')?></option>
                  <option value="legal_consultancy"><?php echo _l('legal_consultancy')?></option>
                  
               </select>
            </div>
         </div>
        
          <div class="col-md-4 hide">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="clearfix"></div>
      </div> <?php //print_r($staff_admins); ?>
         <table class="table table-pb-report scroll-responsive">
            <thead >
               <tr class="text-center" style="color: black;">
                  <th><?php echo _l('report_invoice_customer'); ?></th>
                  <th><?php echo _l('projects'); ?></th>
                  <th><?php echo _l('project_billing_type'); ?></th>
                  <th><?php echo _l('case_type'); ?></th>
                  <th><?php echo _l('report_invoice_number'); ?></th>
                  <th><?php echo _l('report_total_fees'); ?></th>
                  <th><?php echo _l('report_collection_amount'); ?></th>
                  <th ><?php echo _l('pb_cc'); ?></th>
                  <th ><?php echo _l('pb_lc'); ?></th>
                  <th colspan="3" class="text-center"><?php echo _l('sk_share'); ?></th>
                  <th><?php echo _l('kf_share'); ?></th>
                  <th colspan="3" class="text-center"><?php echo _l('admin_share'); ?></th>
                  <th colspan="3" class="text-center"><?php echo _l('lawyers_share'); ?></th>
                  <th colspan="<?php echo sizeof($staff_admins)+1; ?>" class="text-center"><?php echo _l('admin_share'); ?></th>
               </tr>

               <!-- <tr style="display:none">
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>
                  <th></th>

                  <th></th>
                  <th></th>

                  <th><?php echo _l('cc'); ?></th>
                  <th><?php echo _l('lc'); ?></th>
                  <th><?php echo _l('total'); ?></th>
                  
                  <th><?php echo _l('cc'); ?></th>

                  <th><?php echo _l('cc'); ?></th>
                  <th><?php echo _l('lc'); ?></th>
                  <th><?php echo _l('total'); ?></th>

                  <th><?php echo _l('cc'); ?></th>
                  <th><?php echo _l('lc'); ?></th>
                  <th><?php echo _l('total'); ?></th>

                  <?php  foreach ($staff_admins as $admins) { ?>
                     <th ><?php echo $admins['full_name']; ?></th>
                  <?php } ?>
                 
                  <th><?php echo _l('total'); ?></th>
               </tr> -->
               </thead>
            <tbody></tbody>
            <tfoot>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td class="total_fees"></td>
                  <td class="subtotal"></td>
                  <td class="pb_cc"></td>
                  <td class="pb_lc"></td>

                  <td class="sk_cc"></td>
                  <td class="sk_lc"></td>
                  <td class="sk_total"></td>
                 
                  <td class="kf_share"></td>

                  <td class="admin_cc"></td>
                  <td class="admin_lc"></td>
                  <td class="admin_total"></td>

                  <td class="lawyer_cc"></td>
                  <td class="lawyer_lc"></td>
                  <td class="lawyer_total"></td>
                  
                 <?php $t=0; foreach ($staff_admins as $admins) { ?>
                     <td class="<?=$t++?>"></td>
                  <?php } ?>
                  <td class="total_admin_share"></td>
               </tr>
            </tfoot>
         </table>
   </div>
<style type="text/css">
   .table-pb-report thead tr>th {
      border-color : #d7baba!important;
   }
</style>