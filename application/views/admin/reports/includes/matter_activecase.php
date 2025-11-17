    <div id="matter-activecase" class="hide">
       <div class="row">
         
             <div class="col-md-3">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid7" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
		    <div class="col-md-3">
			 <?php $case_types = get_case_client_types('litigation'); ?>
            <label for="status"><?php echo _l('case_type'); ?></label>
           <select class="form-control selectpicker" id="case_type11" name="case_type11" >
               <option value=""><?php  echo _l('all');?></option>
               <?php  foreach($case_types as $case_type){ ?>
                   <option value="<?=$case_type['id']?>"><?=_l($case_type['id'])?></option>
               <?php } ?>
           </select>
         </div> 
         <div class="col-md-3">
            <div class="form-group">
                 <?php echo render_select('lawyerid4',$lawyers_arr,array('staffid','full_name'),'lawyer_assigned','');?>
            </div>
         </div>
		   
        <!--   <div class="col-md-3">
            <div class="form-group">
                 <?php echo render_select('lawyerid5',$lawyers_arr,array('staffid','full_name'),'legal_coordinator','');?>
            </div>
         </div>-->
          <div class="col-md-3">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="a_status" name="a_status" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($active_status as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
         
         <div class="clearfix"></div>
      </div> 
         <table class="table table-matter-activecase-report scroll-responsive">
            <thead>
               <tr>
                   <th class="not_sortable">Sl. No.</th>
                    <th><?php echo _l('client');?></th>
                     <th><?php echo _l('ledger_code'); ?></th>
                   <th><?php echo _l('case_title'); ?></th>
                   <th><?php echo _l('law_firm'); ?></th>
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
