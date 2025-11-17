    <div id="matter-police-case-report" class="hide"> 
      <div class="row">
       
         <div class="col-md-3">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid10" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div> 
          <div class="col-md-3">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="p_status32" name="p_status32" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($proj_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
        <div class="col-md-3">
            <?php echo render_select('country_id1',$countries,array('country_id','short_name'),'country'); ?>
         </div>
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-police-case-report scroll-responsive">
            <thead>
               <tr> 
                  <th class="not_sortable">Sr. No.</th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('ledger_code'); ?></th>
                 
                  <th><?php echo _l('customer_name'); ?></th>
                  <th><?php echo _l('casediary_file_no'); ?></th>
                  <th><?php echo _l('pc_filedby'); ?></th>
                  <th><?php echo _l('pc_checksno'); ?></th>
                  <th><?php echo _l('total_claim_amount'); ?></th>
                  <th><?php echo _l('pc_regstrn_date'); ?></th>
                  <th><?php echo _l('pc_name'); ?></th>
                  <th><?php echo _l('pc_complaint_no'); ?></th>
                  <th><?php echo _l('pc_criminal_caseno'); ?></th>
                  <th><?php echo _l('status'); ?></th>
                 <th><?php echo _l('pc_converted_civil'); ?></th>
                 <th><?php echo _l('pc_civil_caseno'); ?></th>
                
                 <!-- <th><?php echo _l('remarks'); ?></th> -->
                  <th><?php echo _l('abscounded_yn'); ?></th>
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
                 <!--  <td></td>
                 <td></td>-->
                    
               </tr>
            </tfoot>
         </table>
   </div>
