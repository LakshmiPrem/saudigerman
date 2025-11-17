<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                 
                  <a href="<?php echo admin_url('chequebounces'); ?>" class="btn btn-default pull-left"><?php echo _l('back_to_chequebounce_list'); ?></a>
                
                  <div class="clearfix"></div>
                  <hr />
                  <?php echo form_open($this->uri->uri_string()); ?>
                  <div class="row">
                  
                     <?php if(has_permission('chequebounces','','view')){ ?>
                     <div class="col-md-3 border-right">
                       <label for="sale_agent_invoices"><?php echo _l('project_members'); ?></label>
                        <?php
	$value=(!is_admin())?$staff_id:''; 
                           echo render_select('member',$members,array('staffid',array('firstname','lastname')),'',$value,array('data-none-selected-text'=>_l('all_staff_members')),array(),'no-margin'); ?>
                     </div>
                     <?php } ?>
                     <div class="col-md-3 border-right">
                   <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
                     </div>
                     <div class="col-md-3 border-right">
                       <label for="sale_agent_invoices"><?php echo _l('status'); ?></label>
                        <div class="form-group no-margin select-placeholder">
                           <select name="status" id="status" class="selectpicker no-margin" data-width="100%" data-title="<?php echo _l('task_status'); ?>">
                              <option value="" selected><?php echo _l('task_list_all'); ?></option>
                              <?php foreach($cheque_statuses as $status){ ?>
                              <option value="<?php echo $status['chequestatusid']; ?>" <?php if($this->input->post('status') == $status['chequestatusid']){echo 'selected'; } ?>><?php echo $status['name']; ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                    
                     <div class="col-md-2">
                        <button type="submit" class="btn btn-info btn-block" style="margin-top:27px;"><?php echo _l('filter'); ?></button>
                     </div>
                  </div>
                  <?php echo form_close(); ?>
               </div>
            </div>
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($overview )){ ?>
               <h4 class="bold text-success"><?php //echo  _l(date('F', mktime(0, 0, 0, $month, 1))); ?>
                     
                     <?php //if(is_numeric($staff_id) && has_permission('chequebounces','','view')) { echo ' ('.get_staff_full_name($staff_id).')';} ?>
                  </h4>
                  <table class="table chequebounce-overview dt-table">
                     <thead>
                        <tr>
                          <th>SL NO</th>
                          <th><?php echo _l('branch'); ?></th>
                          <th><?php echo _l('ledger_code'); ?></th>
                           <th><?php echo _l('customer_name'); ?></th>
                           
                           <?php 
								foreach($years as $row1){ ?>
                          <th><?=$row1['year'];?></th>
                          <?php }
														 ?>
														 
                           <th><?php echo _l('total_amount'); ?></th>
                           <th><?php echo _l('amount_received'); ?></th>
                           <th><?php echo _l('balance'); ?></th>
                           <th><?php echo _l('remarks'); ?></th>
                           <th><?php echo _l('status'); ?></th>
                           
                        </tr>
                     </thead>
                     <tbody>
                        <?php
						 $j=1;
                           foreach($overview as $row){ ?>
                        <tr>
                         <td><?=$j++;?></td>
                           <td><?=get_company_name($row['client'])?></td>
                            <td><?=$row['customer_code']?></td>
                          <td><?=get_opposite_party_name($row['customer_name'])?></td>
                         
                            <?php 
								foreach($years as $row1){ ?>
                          <td><?=get_chequeamount_byyear($row['id'],$row1['year']) ?></td>
                          <?php }
														 ?>
                          <td><?=$row['total_amount']?>  </td>                 
           
                           <td><?php echo sum_from_table(db_prefix().'chequebounces_return',array('where'=>array('bounce_id'=>$row['id']),'field'=>'amount_received')); ?> </td>
                             <td><?php echo sum_from_table(db_prefix().'chequebounces_return',array('where'=>array('bounce_id'=>$row['id']),'field'=>'balance')); ?>    </td>     
                             <td><?= get_chequebounce_latest_update($row['id']);?></td>            
           
                          
                          
                           <td><?php echo chequebounce_status_translate($row['status']); ?></td>
                          
                        </tr>
                        <?php } ?>
                     </tbody>
                  </table>
                  <hr />
                  <?php } ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<?php init_tail(); ?>
</body>
</html>
