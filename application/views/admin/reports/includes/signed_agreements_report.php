    <div id="signed-agreements-report" class="hide">
      <div class="row">
      
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid22sign" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div> 
              <div class="col-md-3">
            <?php echo render_select('contract_typesign',$contract_types,array('id','name'),'contract_type'); ?>
         </div>
               <div class="col-md-3 hide">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="c_statussign" name="c_statussign" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($contract_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
         
         <div class="clearfix"></div>
      </div>
         <table class="table table-signed-agreements-report scroll-responsive">
            <thead>
               <tr>
                  <th class="not_sortable">Sr. No.</th>
                  <th><?php echo _l('particulars'); ?></th>
                  <th><?php echo _l('client'); ?></th>
                   <th><?php echo _l('other_party'); ?></th>
                   <th><?php echo _l('agreement_type'); ?></th>
                  <th><?php echo _l('contract_value'); ?></th>
                  <th><?php echo _l('contract_list_start_date'); ?></th>
                  <th><?php echo _l('contract_list_end_date'); ?></th>
                  <th><?php echo _l('status'); ?></th>
                  <th><?php echo _l('signed_contract'); ?></th>
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
               </tr>
            </tfoot>
         </table>
   </div>
