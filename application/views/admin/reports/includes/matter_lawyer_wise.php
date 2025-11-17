    <div id="matter-lawyers-report" class="hide">
      <div class="row">
          <div class="col-md-2">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid9" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
                 <?php echo render_select('lawyerid2',$lawyers_arr,array('staffid','full_name'),'lawyer_assigned','');?>
            </div>
         </div>
           <div class="col-md-2">
            <div class="form-group">
                 <?php echo render_select('lawyerid3',$lawyers_arr,array('staffid','full_name'),'legal_coordinator','');?>
            </div>
         </div>
              <div class="col-md-2">
            <label for="status"><?php echo _l('case_type1'); ?></label>
           <select class="form-control selectpicker" id="case_type" name="case_type" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($case_types as $cases){ ?>
                   <option value="<?=$cases['id']?>"><?=$cases['name']?></option>
               <?php } ?>
           </select>
         </div>
         <div class="col-md-2">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="p_status" name="p_status2" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($proj_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
     
          <div class="col-md-2">
            <label for="status"><?php echo _l('active_status'); ?></label>
           <select class="form-control selectpicker" id="a_status" name="a_status2" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($active_status as $proj_statuse1){ ?>
                   <option value="<?=$proj_statuse1['id']?>"><?=$proj_statuse1['name']?></option>
               <?php } ?>
           </select>
         </div> 
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-lawyer-report scroll-responsive">
            <thead>
               <tr>
                 <th class="not_sortable">Sr. No.</th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('ledger_code'); ?></th>
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
                      <th><?php echo _l('judgement_amount'); ?></th>
                       <th><?php echo _l('execution_amount'); ?></th>
                   <th><?php echo _l('settlement_amount'); ?></th>
                   <th><?php echo _l('amount_received'); ?></th>
                    <th><?php echo _l('balance'); ?></th>
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
                  <td></td>
                  
               </tr>
            </tfoot>
         </table>
   </div>
