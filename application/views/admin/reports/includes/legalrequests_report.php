    <div id="legalrequests-report" class="hide">
      <div class="row">
      
         <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid23" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div> 
              <div class="col-md-3">
            <?php echo render_select('service_type',$legal_services,array('serviceid','name'),'items'); ?>
         </div>
               <div class="col-md-3">
            <label for="status"><?php echo _l('status'); ?></label>
           <select class="form-control selectpicker" id="t_status" name="t_status" >
               <option value=""><?php  echo _l('all');?></option>
               <?php foreach($tick_statuses as $proj_statuse){ ?>
                   <option value="<?=$proj_statuse['ticketstatusid']?>"><?=$proj_statuse['name']?></option>
               <?php } ?>
           </select>
         </div> 
         
         <div class="clearfix"></div>
      </div>
            <?php $this->load->view('admin/reports/includes/legalrequests_report_table_html'); ?>
   </div>
