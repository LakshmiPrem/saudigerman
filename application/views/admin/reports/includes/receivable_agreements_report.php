    <div id="receivable-agreements-report" class="hide">
      <div class="row">
      
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid22rec" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div> 
              <div class="col-md-3">
            <?php echo render_select('contract_typerec',$contract_types,array('id','name'),'contract_type'); ?>
         </div>
               <div class="col-md-3">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="c_statusrec" name="c_statusrec" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($contract_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
         
         <div class="clearfix"></div>
      </div>
         <table class="table table-receivable-agreements-report scroll-responsive">
            <thead>
               <tr>
                  <th class="not_sortable"><?php echo _l('#'); ?></th>
                   <th><?php echo _l('contract_list_start_date'); ?></th>
                  <th><?php echo _l('contract_list_end_date');  ?></th>
                   <th><?php echo _l('payment_terms'); ?></th>
                  <th><?php echo _l('hearing_list_subject'); ?></th>
                  <th><?php echo _l('purchaser'); ?></th>
                  <th><?php echo _l('contract_department'); ?></th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('other_party'); ?></th>
                  <th><?php echo _l('agreement_type'); ?></th>
                  <th><?php echo _l('contract_value'); ?></th>
                  <th><?php echo _l('description'); ?></th>
               
                  <th><?php echo _l('status'); ?></th>
                  <th class="not_visible"><?php echo _l('account_status'); ?></th>
               </tr>
            </thead>
            <tbody>   </tbody>
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
                  
               </tr>
               </tfoot>
         
          
         </table>
   </div>
