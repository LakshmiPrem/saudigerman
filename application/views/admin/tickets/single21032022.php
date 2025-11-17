<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php set_ticket_open($ticket->adminread,$ticket->ticketid); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="horizontal-scrollable-tabs">
                     <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                     <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                     <div class="horizontal-tabs">
                        <ul class="nav nav-tabs no-margin nav-tabs-horizontal" role="tablist">
                            <li role="presentation" class="<?php if($ticket->service ==1){ if(!$this->session->flashdata('active_tab')){echo ' active';}} ?>">
                              <a href="#civilrequest" aria-controls="addcivil" role="tab" data-toggle="tab" class="<?php if($ticket->service !=1)echo 'hide'?>">
                                 <?php echo _l('ticket_single_add_civil'); ?>
                              </a>
                           </li>
                              <li role="presentation" class="<?php if($ticket->service ==2){ if(!$this->session->flashdata('active_tab')){echo ' active';}} ?>">
                              <a href="#policerequest" aria-controls="addpolice" role="tab" data-toggle="tab" class="<?php if($ticket->service !=2)echo 'hide'?>">
                                 <?php echo _l('ticket_single_add_police'); ?>
                              </a>
                           </li>
                           <li role="presentation" class="<?php if($ticket->service !=1 && $ticket->service !=2){ if(!$this->session->flashdata('active_tab')){echo 'active';}} ?>">
                              <a href="#addreply" aria-controls="addreply" role="tab" data-toggle="tab">
                                 <?php echo _l('ticket_single_add_reply'); ?>
                              </a>
                           </li>
                           <li role="presentation">
                              <a href="#note" aria-controls="note" role="tab" data-toggle="tab">
                                 <?php echo _l('ticket_single_add_note'); ?>
                              </a>
                           </li>
                           <li role="presentation">
                              <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $ticket->ticketid ;?> + '/' + 'ticket', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                 <?php echo _l('ticket_reminders'); ?>
                                 <?php
                                 $total_reminders = total_rows(db_prefix().'reminders',
                                   array(
                                     'isnotified'=>0,
                                     'staff'=>get_staff_user_id(),
                                     'rel_type'=>'ticket',
                                     'rel_id'=>$ticket->ticketid
                                  )
                                );
                                 if($total_reminders > 0){
                                   echo '<span class="badge">'.$total_reminders.'</span>';
                                }
                                ?>
                             </a>
                          </li>
                          <li role="presentation">
                           <a href="#othertickets" onclick="init_table_tickets(true);" aria-controls="othertickets" role="tab" data-toggle="tab">
                              <?php echo _l('ticket_single_other_user_tickets'); ?>
                           </a>
                        </li>
                        <li role="presentation">
                           <a href="#tasks" onclick="init_rel_tasks_table(<?php echo $ticket->ticketid; ?>,'ticket'); return false;" aria-controls="tasks" role="tab" data-toggle="tab">
                              <?php echo _l('tasks'); ?>
                           </a>
                        </li>
                        <li role="presentation" class="<?php if($this->session->flashdata('active_tab_settings')){echo 'active';} ?>">
                           <a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">
                              <?php echo _l('ticket1_single_settings'); ?>
                           </a>
                        </li>
                        <li role="presentation" class="<?php if($this->session->flashdata('active_tab_approvals')){echo 'active';} ?>" >
                           <a href="#approval"  aria-controls="approvals" role="tab" data-toggle="tab">
                              <?php echo _l('approvals'); ?>
                             
                           </a>
                        </li>
                        <?php hooks()->do_action('add_single_ticket_tab_menu_item', $ticket); ?>
                     </ul>
                  </div>
               </div>
            </div>
         </div>
         <div class="panel_s">
            <div class="panel-body">
               <div class="row">
                  <div class="col-md-6">
                     <h3 class="mtop4 mbot20 pull-left">
                        <span id="ticket_subject">
                           #<?php echo $ticket->request_no; ?> - <?php echo $ticket->subject; ?>
                        </span>
                        <?php if($ticket->project_id != 0){
                           echo '<br /><small>'._l('ticket_linked_to_project','<a href="'.admin_url('projects/view/'.$ticket->project_id).'">'.get_project_name_by_id($ticket->project_id).'</a>') .'</small>';
                        } ?>
                     </h3>
                     <?php echo '<div class="label mtop5 mbot15'.(is_mobile() ? ' ' : ' mleft15 ').'p8 pull-left single-ticket-status-label" style="background:'.$ticket->statuscolor.'">'.ticket_status_translate($ticket->ticketstatusid).'</div>'; ?>
                     <div class="clearfix"></div>
                  </div>
                  <div class="col-md-6 text-right">
                    
                     <div class="row">
                          <div class="col-md-2 col-md-offset-2">
                          <?php
                          $result=fetch_civilticket_numrows($ticket->ticketid);
                          if($result>0){?>
                      <a target="_blank" href="<?php echo admin_url('tickets/legal_approval/'.$ticket->ticketid); ?>" class="btn btn-success btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Legal Request"> <i class="fa fa-file-pdf-o"></i>Legal Civil Request  </a>
                      <?php }
							  else{
								   $result1=fetch_policeticket_numrows($ticket->ticketid);
                          if($result1>0){
							  ?>
                      <a target="_blank" href="<?php echo admin_url('tickets/legal_police_approval/'.$ticket->ticketid); ?>" class="btn btn-info btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Legal Request"> <i class="fa fa-file-pdf-o"></i>Legal Police Request  </a>
                      <?php }
								  else if($ticket->service=='5'){ ?>
                      <a target="_blank" href="<?php echo admin_url('tickets/legal_general_approval/'.$ticket->ticketid); ?>" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Legal Request"> <i class="fa fa-file-pdf-o"></i><?='Legal'.$ticket->service_name. ' Request';?>  </a>
                      <?php
							  }
								  else if($ticket->service=='6'){ ?>
                      <a target="_blank" href="<?php echo admin_url('tickets/legal_general_approval/'.$ticket->ticketid); ?>" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Legal Request"> <i class="fa fa-file-pdf-o"></i><?='Legal'.$ticket->service_name. ' Request';?>  </a>
                      <?php
							  }
								  else if($ticket->service=='7'){ ?>
                      <a target="_blank" href="<?php echo admin_url('tickets/legal_close_approval/'.$ticket->ticketid); ?>" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Legal Request"> <i class="fa fa-file-pdf-o"></i><?='Legal'.$ticket->service_name. ' Request';?>  </a>
                      <?php
							  }
								  
							  else{ ?>
                      <a target="_blank" href="<?php echo admin_url('tickets/legal_general_approval/'.$ticket->ticketid); ?>" class="btn btn-warning btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download Legal Request"> <i class="fa fa-file-pdf-o"></i><?='Legal General Request';?>  </a>
                      <?php
							  }}?>
                       </div>
                        <div class="col-md-4 col-md-offset-4">
                           <?php echo render_select('status_top',$statuses,array('ticketstatusid','name'),'',$ticket->status,array(),array(),'no-mbot','',false); ?>
                        </div>
                     </div>
                  </div>
                  <div class="clearfix"></div>
               </div>
               <div class="tab-content">
                                         <div role="tabpanel" class="tab-pane <?php if($ticket->service ==1){ if(!$this->session->flashdata('active_tab')){echo 'active';}} ?>" id="civilrequest">
                  <hr class="no-mtop" />
                  <div class="row">
                    <?php echo form_open_multipart('',array('id'=>'civil-ticket-form','novalidate'=>true)); ?>
                     <div class="col-md-4">
                        <div class="form-group">
                        <?php  $value=( isset($civil) ? $civil->typeof_business : ''); ?>
                        <?php echo render_input('typeof_business','typeof_business',$value,'text',array('required'=>'true')); ?>
                         <?php  $value=( isset($civil) ? $civil->id : ''); ?>
                         <input type="hidden" name="id" value=<?=$value?>>
                     
                     
                     </div>
					  </div>
                        <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($civil) ? $civil->typeof_liscence : ''); ?>
                        <?php echo render_input('typeof_liscence','typeof_liscence',$value,'text',array('required'=>'true')); ?>
                     
                     </div>
                        </div>
                          <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($civil) ? $civil->sales_executive : ''); ?>
                        <?php echo render_input('sales_executive','sales_executive',$value,'text'); ?>
                     
                     </div>
                        </div>
                          <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($civil) ? $civil->court_nature : ''); ?>
                        <?php echo render_input('court_nature','court_nature',$value,'text'); ?>
                     
                     </div>
                        </div>
                         <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($civil) ? $civil->lawyer_name : ''); ?>
                        <?php echo render_input('lawyer_name','lawyer_name',$value,'text'); ?>
                     
                     </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($civil) ? $civil->company : ''); ?>
                        <?php echo render_input('company','company',$value,'text'); ?>
                     
                     </div>
                        </div>
                       
                        <div class="col-md-12" >
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> Company Documents</legend>
                          <div class="col-md-3">
                       <div class="form-group">
          
            <label for="cars">Trade License</label>
  		<?php  $value=(isset($civil) ? $civil->trade_license : ''); ?>
               <select name="trade_license" id="trade_license" class="form-control selectpicker" >
                <option value="yes" <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no" <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                        <div class="col-md-3">
                         <div class="form-group">
           
            
            <label for="cars">VAT Certificate</label>
					<?php  $value=(isset($civil) ? $civil->vat_certificate : ''); ?>
               <select name="vat_certificate" id="vat_certificate" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                                <div class="col-md-3">
                         <div class="form-group">
           
            
            <label for="cars">Credit Application</label>
				<?php  $value=(isset($civil) ? $civil->credit_app : ''); ?>
               <select name="credit_app" id="credit_app" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                                <div class="col-md-3">
                         <div class="form-group">
           
            
            <label for="cars">Passport Copy</label>
					<?php  $value=(isset($civil) ? $civil->passport_copy : ''); ?>
               <select name="passport_copy" id="passport_copy" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>
			</div> </div><hr/> </fieldset></div>
                        <div class="clearfix"></div><br>
                        
                         <div class="col-md-4">
                        <div class="form-group">
                        	<?php  $value=(isset($civil) ? $civil->current_credit_appamount : ''); ?>
                        <?php echo render_input('current_credit_appamount','current_credit_appamount',$value,'text'); ?>
                     
							 </div></div>
                       <div class="col-md-4">
                        <div class="form-group">
                        	<?php  $value=(isset($civil) ? $civil->current_credit_days : ''); ?>
                        <?php echo render_input('current_credit_days','current_credit_days',$value,'text'); ?>
                     
						   </div></div>
                        <div class="col-md-4">
                        <div class="form-group">
                        	<?php  $value=(isset($civil) ? $civil->total_outstanding_amount : ''); ?>
                        <?php echo render_input('total_outstanding_amount','total_outstanding_amount',$value,'text'); ?>
                     
                     </div>
					  </div>
                        <div class="col-md-12">
                        <div class="form-group">
                         	<?php  $value=(isset($civil) ? $civil->civil_case_reason : ''); ?>
                          <?php echo render_textarea('civil_case_reason','reason_civil_case',$value,array('rows'=>2),array(),'',''); ?>
                                          
                     </div>
					  </div>
                                     <div class="clearfix"></div><br>
                                     
                                             <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('dues')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_dues">  
                   <thead>
                   	<th><?=_l('due_date')?></th>
                   	<th><?=_l('due_amount')?></th>
                   	<th><?=_l('due_days')?></th>
                   
                   </thead>
                    <tbody>
                    
                    <?php 
					if(isset($civil)&& ($civil->overdue_detail!='')){
						
						$overdue=json_decode($civil->overdue_detail,true);
							$limit=sizeof($overdue['due_date']);
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="drow<?=$i?>" class="dynamic-added-dues"> 
                     <td><input type="date" name="overdue_detail[due_date][]" placeholder="Enter Due Date" class="form-control" value="<?=$overdue['due_date'][$i]?>"  /></td> 
                      <td><input type="text" name="overdue_detail[due_amount][]" placeholder="Enter Due Amount" class="form-control" value="<?=$overdue['due_amount'][$i]?>"  /></td>
                       <td><input type="text" name="overdue_detail[due_days][]" placeholder="Enter Due Days" class="form-control" value="<?=$overdue['due_days'][$i]?>" readonly  /></td>
                      
                       
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addoverdue" id="addoverdue" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_due_remove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                     <?php
						}
					}
							 else{
								 $i=0;
					 ?>
                      <tr id="drow<?=$i?>" class="dynamic-added-dues"> 
                     <td><input type="date" name="overdue_detail[due_date][]" placeholder="Enter Due Date" class="form-control" value=""  /></td> 
                      <td><input type="text" name="overdue_detail[due_amount][]" placeholder="Enter Due Amount" class="form-control" value=""  /></td>
                       <td><input type="text" name="overdue_detail[due_days][]" placeholder="Enter Due Date" class="form-control" value="" readonly /></td>
                      
                        <td><button type="button" name="addoverdue" id="addoverdue" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                     <?php
				}
			?> 
                 
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
       <div class="clearfix"></div><br>
                                               <div class="col-md-12">
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> Sales Document Status</legend>
                          <div class="col-md-3">
                       <div class="form-group">
          
            <label for="cars">Invoices</label>
					<?php  $value=(isset($civil) ? $civil->sales_invoice : ''); ?>
               <select name="sales_invoice" id="sales_invoice" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                        <div class="col-md-3">
                         <div class="form-group">
           
            
            <label for="cars">DO</label>
					<?php  $value=(isset($civil) ? $civil->sales_do : ''); ?>
               <select name="sales_do" id="sales_do" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                                <div class="col-md-3">
                         <div class="form-group">
           
            
            <label for="cars">LPO</label>
					<?php  $value=(isset($civil) ? $civil->sales_lpo : ''); ?>
               <select name="sales_lpo" id="sales_lpo" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                                <div class="col-md-3">
                         <div class="form-group">
           
            
            <label for="cars">Balance Confirmation</label>
					<?php  $value=(isset($civil) ? $civil->sales_balconfirm : ''); ?>
               <select name="sales_balconfirm" id="sales_balconfirm" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


									</div> </div> </fieldset></div>
                                             <div class="clearfix"></div><br>
                                             
                                             <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('details_of_return_cheque')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_civil">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('cheque_amount')?></th>
                   	<th><?=_l('partial_payment')?></th>
                   	<th><?=_l('balance')?></th>
                   	<th><?=_l('dateon_cheque')?></th>
                   </thead>
                    <tbody>
                    
                    <?php 
					if(isset($civil)&& ($civil->return_chq_details!='')){
						$retcheque=json_decode($civil->return_chq_details,true);
							$limit=sizeof($retcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="vrow<?=$i?>" class="dynamic-added-civil"> 
                      <td><input type="text" name="return_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$retcheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="return_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$retcheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="text" name="return_chq_details[partial_payment][]" placeholder="Enter Prtial Payment" class="form-control" value="<?=$retcheque['partial_payment'][$i]?>"  /></td>
                       <td><input type="text" name="return_chq_details[balance][]" placeholder="Enter Balance" class="form-control" value="<?=$retcheque['balance'][$i]?>"  /></td>
                       <td><input type="date" name="return_chq_details[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value="<?=$retcheque['dateon_cheque'][$i]?>"  /></td> 
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addcivilcheque" id="addcivilcheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_chqremove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                     <?php
						}
					}
							 else{
								 $i=0;
					 ?>
                     <tr id="vrow<?=$i?>" class="dynamic-added-civil"> 
                      <td><input type="text" name="return_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="return_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                       <td><input type="text" name="return_chq_details[partial_payment][]" placeholder="Enter Prtial Payment" class="form-control" value=""  /></td>
                       <td><input type="text" name="return_chq_details[balance][]" placeholder="Enter Balance" class="form-control" value=""  /></td>
                       <td><input type="date" name="return_chq_details[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value=""  /></td> 
                      
                        <td><button type="button" name="addcivilcheque" id="addcivilcheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                     <?php
				}
			?> 
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
               
                        <div class="clearfix"></div><br>
                                                           <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('add_details_of_return_cheque')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_civil1">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('returncheque_amount')?></th>
                   	<th><?=_l('days_sale')?></th>
                   	<th><?=_l('days_return')?></th>
                   	<th><?=_l('remarks')?></th>
                   </thead>
                    <tbody>
                    
                    <?php 
					if(isset($civil)&& ($civil->addreturn_chq_details!='')){
						$addretcheque=json_decode($civil->addreturn_chq_details,true);
							$limit=sizeof($addretcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="cvrow<?=$i?>" class="dynamic-added-civil1"> 
                      <td><input type="text" name="addreturn_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$addretcheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="addreturn_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$addretcheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="text" name="addreturn_chq_details[days_sale][]" placeholder="Enter Sale days" class="form-control" value="<?=$addretcheque['days_sale'][$i]?>"  /></td>
                       <td><input type="text" name="addreturn_chq_details[days_return][]" placeholder="Enter Return Days" class="form-control" value="<?=$addretcheque['days_return'][$i]?>"  /></td>
                         <td><textarea  name="addreturn_chq_details[remark][]" rows="3" class="form-control"><?=$addretcheque['remark'][$i]?> </textarea></td>
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addcivilcheque1" id="addcivilcheque1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_chqremove1">X</button></td>
                        <?php } ?>
                    </tr>
                    
                     <?php
						}
					}
							 else{
								 $i=0;
					 ?>
                    <tr id="cvrow<?=$i?>" class="dynamic-added-civil1"> 
                      <td><input type="text" name="addreturn_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="addreturn_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                       <td><input type="text" name="addreturn_chq_details[days_sale][]" placeholder="Enter Sale days" class="form-control" value=""  /></td>
                       <td><input type="text" name="addreturn_chq_details[days_return][]" placeholder="Enter Return Days" class="form-control" value=""  /></td>
                         <td><textarea  name="addreturn_chq_details[remark][]" rows="3" class="form-control"> </textarea></td>
                      
                        <td><button type="button" name="addcivilcheque1" id="addcivilcheque1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                     <?php
				}
			?> 
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
               
                        <div class="clearfix"></div><br>
                    <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('det_pdc_hand')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_pdc1">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('cheque_amount')?></th>
                  <th><?=_l('dateon_cheque')?></th>
                   <th><?=_l('sale_date')?></th>  	
                   </thead>
                    <tbody>
                    <?php
	if(isset($civil)&& ($civil->pdc_in_hand!='')){
		
				$pdccheque=json_decode($civil->pdc_in_hand,true);
							$limit=sizeof($pdccheque['chequeno']);
							
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="pprow<?=$i?>" class="dynamic-added-pdc1"> 
                      <td><input type="text" name="pdc_in_hand[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$pdccheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="pdc_in_hand[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$pdccheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="date" name="pdc_in_hand[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value="<?=$pdccheque['dateon_cheque'][$i]?>"  /></td> 
                         <td><input type="date" name="pdc_in_hand[sale_date][]" placeholder="Enter Sale Date" class="form-control" value="<?=$pdccheque['sale_date'][$i]?>"  /></td> 
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addpdccheque1" id="addpdccheque1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_pdc_remove1">X</button></td>
                        <?php } ?>
                    </tr>
                    
                    <?php
						}
	}
		else{
			$i=0;
					 ?>
                    <tr id="pprow<?=$i?>" class="dynamic-added-pdc1"> 
                      <td><input type="text" name="pdc_in_hand[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="pdc_in_hand[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                       <td><input type="date" name="pdc_in_hand[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value=""  /></td> 
                         <td><input type="date" name="pdc_in_hand[sale_date][]" placeholder="Enter Sale Date" class="form-control" value=""  /></td> 
                       
                        <td><button type="button" name="addpdccheque1" id="addpdccheque1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                    <?php
		}
		?>
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div> 
                        <div class="clearfix"></div><br> 
         <div class="col-md-12">
                         <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('Amount to be filed Civil Case')?></legend>
                        
            	         <div class="col-md-4">
                        <div class="form-group">
          	<?php  $value=(isset($civil) ? $civil->returncheque_amount : ''); ?>
            <?php echo render_input('returncheque_amount','returncheque_amount',$value,'text'); ?>
            </div> </div>
             <div class="col-md-4">
                        <div class="form-group">
          	<?php  $value=(isset($civil) ? $civil->outstandingamount : ''); ?>
            <?php echo render_input('outstandingamount','outstanding_amount',$value,'text'); ?>
            </div> </div>
             <div class="col-md-4">
                        <div class="form-group">
          	<?php  $value=(isset($civil) ? $civil->totalamount : ''); ?>
            <?php echo render_input('totalamount','total_amount',$value,'text'); ?>
            </div> </div>
             </fieldset></div> 
                      <div class="clearfix"></div><br>
                               <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('guarantee_chequedet')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_gchq1">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('cheque_amount')?></th>
                    <th><?=_l('nameof_bank')?></th>  	
                   </thead>
                    <tbody>
                      <?php
	if(isset($civil)&& ($civil->guarantee_chequedet!='')){
		
				$gcheque=json_decode($civil->guarantee_chequedet,true);
					$limit=sizeof($gcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {	
						 ?>
                  <tr id="ggrow<?=$i?>" class="dynamic-added-gchq1"> 
                      <td><input type="text" name="guarantee_chequedet[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$gcheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="guarantee_chequedet[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$gcheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="text" name="guarantee_chequedet[cheque_bank][]" placeholder="Enter Bank Name" class="form-control" value="<?=$gcheque['cheque_bank'][$i]?>"  /></td> 
                     <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addguarcheque1" id="addguarcheque1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_gcheck1_remove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                     <?php
						}
					}
							 else{
								 $i=0;
					 ?>
                      <tr id="ggrow<?=$i?>" class="dynamic-added-gchq1"> 
                      <td><input type="text" name="guarantee_chequedet[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="guarantee_chequedet[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                     
                         <td><input type="text" name="guarantee_chequedet[cheque_bank][]" placeholder="Enter Bank Name" class="form-control" value=""  /></td> 
                                            
                        <td><button type="button" name="addguarcheque1" id="addguarcheque1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                                             
                    </tr>
                    <?php
				}
					   ?>
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
                
                                <div class="clearfix"></div><br>
                                  <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('owner_signat_det')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_owner1">  
                   <thead>
                   	<th><?=_l('owner_name').' & '._l('owner_status')?></th>
                   	 
                   	<th><?=_l('emirates_owner').' & '._l('passportno_owner')?></th>
                  <th><?=_l('contact1')?></th>
                    <th><?=_l('contact2')?></th>
                     	
                   </thead>
                    <tbody>
                    <?php
	if(isset($civil)&& ($civil->owner_detail!='')){
	
				$ownerdet=json_decode($civil->owner_detail,true);
		
							$limit=sizeof($ownerdet['owner_name']);
							
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="ssrow<?=$i?>" class="dynamic-added-owner1"> 
                      <td><input type="text" name="owner_detail[owner_name][]" placeholder="Enter Owner Name" class="form-control" value="<?=$ownerdet['owner_name'][$i]?>"  /><br>
                      <input type="text" name="owner_detail[nationality][]" placeholder="Enter Nationality" class="form-control" value="<?=$ownerdet['nationality'][$i]?>"  /> <br>
                        <input type="text" name="owner_detail[ownstatus][]" placeholder="Enter Owner Status" class="form-control" value="<?=$ownerdet['ownstatus'][$i]?>"  /></td> 
                         <td><input type="text" name="owner_detail[emirates][]" placeholder="Enter Emirates id & Expiry" class="form-control" value="<?=$ownerdet['emirates'][$i]?>"  /> <br>
                         <input type="text" name="owner_detail[passport][]" placeholder="Enter Passport No & Expiry" class="form-control" value="<?=$ownerdet['passport'][$i]?>"  /><br>
                         <input type="text" name="owner_detail[email][]" placeholder="Enter Email" class="form-control" value="<?=$ownerdet['email'][$i]?>"  /></td> 
                         <td><textarea  name="owner_detail[contact1][]" rows="3" class="form-control"><?=$ownerdet['contact1'][$i]?> </textarea></td> 
                         <td><textarea  name="owner_detail[contact2][]" rows="3" class="form-control"><?=$ownerdet['contact2'][$i]?></textarea></td> 
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addowner1" id="addowner1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_owner_remove1">X</button></td>
                        <?php } ?>
                    </tr>
                    
                    <?php
						}
	}
		else{
			$i=0;
					 ?>
                     <tr id="ssrow<?=$i?>" class="dynamic-added-owner1"> 
                     <td><input type="text" name="owner_detail[owner_name][]" placeholder="Enter Owner Name" class="form-control" value=""  /><br><input type="text" name="owner_detail[nationality][]" placeholder="Enter Nationality" class="form-control" value=""  />
                        <br><input type="text" name="owner_detail[ownstatus][]" placeholder="Enter Present Status" class="form-control" value=""  /></td> 
                         <td><input type="text" name="owner_detail[emirates][]" placeholder="Enter Emirates id & Expiry" class="form-control" value=""  /><br>
                         <input type="text" name="owner_detail[passport][]" placeholder="Enter Passport No & Expiry" class="form-control" value=""  />
                         <br><input type="text" name="owner_detail[email][]" placeholder="Enter Email" class="form-control" value=""  /></td> 
                         <td><textarea  name="owner_detail[contact1][]" rows="3" class="form-control"></textarea></td> 
                         <td><textarea  name="owner_detail[contact2][]" rows="3" class="form-control"></textarea></td> 
                       
                        <td><button type="button" name="addowner1" id="addowner1" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                    <?php
		}
		?>
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
                              <div class="clearfix"></div><br>
  
            	 <div class="clearfix"></div><br>
      <div class="col-md-4">
                  	<?php  $value=(isset($civil) ? $civil->makani_land : ''); ?>
                    <?php echo render_input('makani_land','makani_land',$value); ?>
                      
                  </div>
               
                    <div class="col-md-4"> 
                    	<?php  $value=(isset($civil) ? $civil->company_status : ''); ?> 
            <?php $yes_no_arr = [['id'=>'active','name'=>'Active'],['id'=>'closed','name'=>'Closed'],['id'=>'unknown','name'=>'Unknown']]  ?>
          
            <?php echo render_input('company_status','company_status1',$value);?>

          </div>   
           <div class="col-md-4">
                  	<?php  $value=(isset($civil) ? $civil->location_map : ''); ?>
                      <?php echo render_textarea( 'location_map', 'location_map',$value,array('rows'=>2)); ?>
                      
                  </div> 
        <div class="col-md-6">
                     <div class="form-group mbot20">
                      		<?php  $value=(isset($civil) ? $civil->asset_detail : ''); ?>
                       	<?php echo render_textarea('asset_detail','asset_detail',$value,array('rows'=>2),array(),''); ?>
                     </div>
					  </div>
                                                   <div class="col-md-6">
                     <div class="form-group mbot20">
                      		<?php  $value=(isset($civil) ? $civil->remarks : ''); ?>
                       	<?php echo render_textarea('remarks','remarks',$value,array('rows'=>2),array(),''); ?>
                     </div>
					  </div>
                                                   <div class="clearfix"></div><br>
                                                   <div class="col-md-12">
                        
           				 <fieldset>
 						 <legend> <?= _l('previous_case_typedet')?></legend>
                          <div class="col-md-4">
                       <div class="form-group">
          
           <label for=""><?= _l('previous_case_type')?></label>
                
             	<?php  $value=(isset($civil) ? $civil->previous_case_type : ''); ?>
               <select name="previous_case_type" class="form-control selectpicker"  >
                <option value="">No Case</option>
               <option value="police">Police</option>
               <option value="civil">Civil</option>
           </select>
            </div> </div>
                                       <div class="col-md-4">
                         <div class="form-group">
                         	<?php  $value=(isset($civil) ? $civil->case_no : ''); ?>
              <?php echo render_input('case_no','case_no',$value,'text'); ?>
            </div> </div>
                        <div class="col-md-4">
                         <div class="form-group">
                         	<?php  $value=(isset($civil) ? _d($civil->case_date) : ''); ?>
                         
             <?php echo render_date_input('case_date','case_date',$value); ?>
            </div> </div>
                
                       </fieldset></div>
      <div class="clearfix"></div><br>
                   
              
                 
                  <div class="col-md-12">
                     <?php echo render_custom_fields('tickets',$ticket->ticketid); ?>
                  </div>
               
               <?php hooks()->do_action('add_single_ticket_tab_menu_content', $ticket); ?>
               <div class="row">
                  <div class="col-md-12 text-center">
                     <hr />
                     <?php 
					  if(get_approvalticket_record_status($ticket->ticketid)){?>
                     <a href="#" class="btn btn-info add_civilcase_ticket">
                        <?php echo _l('submit'); ?>
                     </a>
                     <?php
																			  }
					  ?>
                  </div>
               </div>
                <?php echo form_close(); ?>
                 </div>
            </div>
            <!-- ###Police Request -->
             <div role="tabpanel" class="tab-pane <?php if($ticket->service ==2){ if(!$this->session->flashdata('active_tab')){echo 'active';}} ?>" id="policerequest">
                  <hr class="no-mtop" />
                    
                  <div class="row" id="policediv">
                    <?php echo form_open_multipart('',array('id'=>'police-ticket-form','novalidate'=>true)); ?>
                     <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($police) ? $police->sales_executive : ''); ?>
                        <?php echo render_input('sales_executive','sales_executive',$value,'text'); ?>
                     
                     </div>
                        </div>
                     <div class="col-md-4">
                        <div class="form-group">
                        <?php  $value=( isset($police) ? $police->typeof_business : ''); ?>
                        <?php echo render_input('typeof_business','typeof_business',$value,'text',array('required'=>'true')); ?>
                         <?php  $value=( isset($police) ? $police->id : ''); ?>
                         <input type="hidden" name="id" value=<?=$value?>>
                     
                     
                     </div>
					  </div>
                       
                        <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($police) ? $police->typeof_liscence : ''); ?>
                        <?php echo render_input('typeof_liscence','typeof_liscence',$value,'text',array('required'=>'true')); ?>
                     
                     </div>
                        </div>
                        <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($police) ? $police->company : ''); ?>
                        <?php echo render_input('company','company',$value,'text'); ?>
                     
                     </div>
                        </div>
                         <div class="col-md-4">
                        <div class="form-group">
                          <?php  $value=( isset($police) ? $police->amount_soa : ''); ?>
                        <?php echo render_input('amount_soa','amount_soa',$value,'text'); ?>
                     
                     </div>
                        </div>
                         <div class="col-md-4">
                        <div class="form-group">
                          <label for="cars"><?=_l('cheque_nature')?></label>
                          <?php  $value=( isset($police) ? $police->cheque_nature : ''); ?>
                      <select name="cheque_nature" id="cheque_nature" class="form-control selectpicker" >
               <option value="guarantee"  <?php if ($value=='guarantee')echo 'selected'?>>Guarantee</option>
               <option value="company"  <?php if ($value=='company')echo 'selected'?>>Comapny</option>
               <option value="personal"  <?php if ($value=='personal')echo 'selected'?>>Personal</option>
               <option value="undertaking"  <?php if ($value=='undertaking')echo 'selected'?>>Undertaking</option>
                <option value="others"  <?php if ($value=='others')echo 'selected'?>>Others</option>
               </select>
                     
                     </div>
                        </div>
                         <div class="col-md-12">
                        <div class="form-group">
                         	<?php  $value=(isset($police) ? $police->police_case_reason : ''); ?>
                          <?php echo render_textarea('police_case_reason','police_case_reason',$value,array('rows'=>2),array(),'',''); ?>
                                          
                     </div>
					  </div>
         
                        <div class="clearfix"></div><br>
                        
            
                                             <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('details_of_return_cheque')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('cheque_amount')?></th>
                   	<th><?=_l('partial_payment')?></th>
                   	<th><?=_l('balance')?></th>
                   	<th><?=_l('dateon_cheque')?></th>
                   </thead>
                    <tbody>
                    
                    <?php 
					if(isset($police)&& ($police->return_chq_details!='')){
						$retcheque=json_decode($police->return_chq_details,true);
							$limit=sizeof($retcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="row<?=$i?>" class="dynamic-added"> 
                      <td><input type="text" name="return_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$retcheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="return_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$retcheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="text" name="return_chq_details[partial_payment][]" placeholder="Enter Prtial Payment" class="form-control" value="<?=$retcheque['partial_payment'][$i]?>"  /></td>
                       <td><input type="text" name="return_chq_details[balance][]" placeholder="Enter Balance" class="form-control" value="<?=$retcheque['balance'][$i]?>"  /></td>
                       <td><input type="date" name="return_chq_details[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value="<?=$retcheque['dateon_cheque'][$i]?>"  /></td> 
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addcheque" id="addcheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_remove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                     <?php
						}
					}
							 else{
								 $i=0;
					 ?>
                     <tr id="row<?=$i?>" class="dynamic-added"> 
                      <td><input type="text" name="return_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="return_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                       <td><input type="text" name="return_chq_details[partial_payment][]" placeholder="Enter Prtial Payment" class="form-control" value=""  /></td>
                       <td><input type="text" name="return_chq_details[balance][]" placeholder="Enter Balance" class="form-control" value=""  /></td>
                       <td><input type="date" name="return_chq_details[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value=""  /></td> 
                      
                        <td><button type="button" name="addcheque" id="addcheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                     <?php
				}
			?> 
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
                        <div class="clearfix"></div><br>
                                            <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('det_pdc_hand')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_pdc">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('cheque_amount')?></th>
                  <th><?=_l('dateon_cheque')?></th>
                   <th><?=_l('sale_date')?></th>  	
                   </thead>
                    <tbody>
                    <?php
	if(isset($police)&& ($police->pdc_in_hand!='')){
		
				$pdccheque=json_decode($police->pdc_in_hand,true);
							$limit=sizeof($pdccheque['chequeno']);
							
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="prow<?=$i?>" class="dynamic-added-pdc"> 
                      <td><input type="text" name="pdc_in_hand[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$pdccheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="pdc_in_hand[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$pdccheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="date" name="pdc_in_hand[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value="<?=$pdccheque['dateon_cheque'][$i]?>"  /></td> 
                         <td><input type="date" name="pdc_in_hand[sale_date][]" placeholder="Enter sale Date" class="form-control" value="<?=$pdccheque['sale_date'][$i]?>"  /></td> 
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addpdccheque" id="addpdccheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_pdc_remove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                    <?php
						}
	}
		else{
			$i=0;
					 ?>
                    <tr id="prow<?=$i?>" class="dynamic-added-pdc"> 
                      <td><input type="text" name="pdc_in_hand[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="pdc_in_hand[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                       <td><input type="date" name="pdc_in_hand[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" value=""  /></td> 
                         <td><input type="date" name="pdc_in_hand[sale_date][]" placeholder="Enter sale Date" class="form-control" value=""  /></td> 
                       
                        <td><button type="button" name="addpdccheque" id="addpdccheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                    <?php
		}
		?>
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
                
            	 <div class="clearfix"></div><br>
     
                <div class="col-md-4">
                        <div class="form-group">
                        <?php  $value=( isset($police) ? $police->amount_filed_case : ''); ?>
                        <?php echo render_input('amount_filed_case','amt_police_case',$value,'text'); ?>
                                            
                     </div>
					  </div>
                                 <div class="col-md-4">
                       <div class="form-group">
          
            <label for="cars"><?=_l('balance_confirmation')?></label>
					<?php  $value=(isset($police) ? $police->balance_confirm: ''); ?>
               <select name="balance_confirm" id="balance_confirm" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                                          <div class="col-md-4">
                       <div class="form-group">
          
            <label for="cars"><?=_l('guarantee_cheque')?></label>
					<?php  $value=(isset($police) ? $police->guarantee_cheque: ''); ?>
               <select name="guarantee_cheque" id="guarantee_cheque" class="form-control selectpicker" >
               <option value="yes"  <?php if ($value=='yes')echo 'selected'?>>Available</option>
               <option value="no"  <?php if ($value=='no')echo 'selected'?>>Not Available</option>
              
               </select>


            </div> </div>
                    <div class="clearfix"></div><br>
                                            <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('guarantee_chequedet')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_gchq">  
                   <thead>
                   	<th><?=_l('chequeno')?></th>
                   	<th><?=_l('cheque_amount')?></th>
                 
                   <th><?=_l('nameof_bank')?></th>  	
                   </thead>
                    <tbody>
                      <?php
	if(isset($police)&& ($police->guarantee_chequedet!='')){
		
				$gcheque=json_decode($police->guarantee_chequedet,true);
						
									$limit=sizeof($gcheque['chequeno']);
							for($i=0;$i<$limit;$i++) {	
						 ?>
                  <tr id="ggrow<?=$i?>" class="dynamic-added-gchq"> 
                      <td><input type="text" name="guarantee_chequedet[chequeno][]" placeholder="Enter Cheque No" class="form-control" value="<?=$gcheque['chequeno'][$i]?>"  /></td>
                       <td><input type="text" name="guarantee_chequedet[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value="<?=$gcheque['cheque_amount'][$i]?>"  /></td>
                       <td><input type="text" name="guarantee_chequedet[cheque_bank][]" placeholder="Enter Bank Name" class="form-control" value="<?=$gcheque['cheque_bank'][$i]?>"  /></td> 
                     <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addguarcheque" id="addguarcheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_gcheck_remove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                     <?php
						}
					}
							 else{
								 $i=0;
					 ?>
                      <tr id="ggrow<?=$i?>" class="dynamic-added-gchq"> 
                      <td><input type="text" name="guarantee_chequedet[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td>
                       <td><input type="text" name="guarantee_chequedet[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td>
                     
                         <td><input type="text" name="guarantee_chequedet[cheque_bank][]" placeholder="Enter Bank Name" class="form-control" value=""  /></td> 
                                            
                        <td><button type="button" name="addguarcheque" id="addguarcheque" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                                             
                    </tr>
                    <?php
				}
					   ?>
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
                
                                <div class="clearfix"></div><br>
                                            <div class="col-md-12">
                                            
                        
           				 <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?= _l('owner_signat_det')?></legend>
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_owner">  
                   <thead>
                   	<th><?=_l('owner_name').' & '._l('owner_status')?></th>
                   	 
                   	<th><?=_l('emirates_owner').' & '._l('passportno_owner')?></th>
                  <th><?=_l('contact1')?></th>
                    <th><?=_l('contact2')?></th>
                     	
                   </thead>
                    <tbody>
                    <?php
	if(isset($police)&& ($police->owner_detail!='')){
	
				$ownerdet=json_decode($police->owner_detail,true);
		
							$limit=sizeof($ownerdet['owner_name']);
							
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                  <tr id="srow<?=$i?>" class="dynamic-added-owner"> 
                      <td><input type="text" name="owner_detail[owner_name][]" placeholder="Enter Owner Name" class="form-control" value="<?=$ownerdet['owner_name'][$i]?>"  /><br>
                      <input type="text" name="owner_detail[nationality][]" placeholder="Enter Nationality" class="form-control" value="<?=$ownerdet['nationality'][$i]?>"  /> <br>
                        <input type="text" name="owner_detail[ownstatus][]" placeholder="Enter Present Status" class="form-control" value="<?=$ownerdet['ownstatus'][$i]?>"  /></td> 
                         <td><input type="text" name="owner_detail[emirates][]" placeholder="Enter Emirates Id & Expiry" class="form-control" value="<?=$ownerdet['emirates'][$i]?>"  /> <br>
                         <input type="text" name="owner_detail[passport][]" placeholder="Enter Passport No & Expiry" class="form-control" value="<?=$ownerdet['passport'][$i]?>"  /><br>
                         <input type="text" name="owner_detail[email][]" placeholder="Enter Email Id" class="form-control" value="<?=$ownerdet['email'][$i]?>"  /></td> 
                         <td><textarea  name="owner_detail[contact1][]" rows="3" class="form-control"><?=$ownerdet['contact1'][$i]?> </textarea></td> 
                         <td><textarea  name="owner_detail[contact2][]" rows="3" class="form-control"><?=$ownerdet['contact2'][$i]?></textarea></td> 
                       <?php 
							 if($i==0){
								 ?>
							 
                        <td><button type="button" name="addowner" id="addowner" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_owner_remove">X</button></td>
                        <?php } ?>
                    </tr>
                    
                    <?php
						}
	}
		else{
			$i=0;
					 ?>
                     <tr id="srow<?=$i?>" class="dynamic-added-owner"> 
                     <td><input type="text" name="owner_detail[owner_name][]" placeholder="Enter Owner Name" class="form-control" value=""  /><br><input type="text" name="owner_detail[nationality][]" placeholder="Enter Nationality" class="form-control" value=""  />
                        <br><input type="text" name="owner_detail[ownstatus][]" placeholder="Enter Present Status" class="form-control" value=""  /></td> 
                         <td><input type="text" name="owner_detail[emirates][]" placeholder="Enter Emirates id & Expiry" class="form-control" value=""  /><br>
                         <input type="text" name="owner_detail[passport][]" placeholder="Enter Passport No & Expiry" class="form-control" value=""  />
                         <br><input type="text" name="owner_detail[email][]" placeholder="Enter Email" class="form-control" value=""  /></td> 
                         <td><textarea  name="owner_detail[contact1][]" rows="3" class="form-control"></textarea></td> 
                         <td><textarea  name="owner_detail[contact2][]" rows="3" class="form-control"></textarea></td> 
                       
                        <td><button type="button" name="addowner" id="addowner" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                    <?php
		}
		?>
                    </tbody>
                     
                </table>  
							 </div>
           

												 </fieldset></div>
                
            	 <div class="clearfix"></div><br>
                    <div class="col-md-4"> 
                    	<?php  $value=(isset($police) ? $police->company_status : ''); ?> 
            <?php $yes_no_arr = [['id'=>'active','name'=>'Active'],['id'=>'closed','name'=>'Closed'],['id'=>'unknown','name'=>'Unknown']]  ?>
            <?php // $selected = (isset($client) ? $client->company_status : 'yes');?>
            <?php echo render_select('company_status',$yes_no_arr,array('id','name'),'company_status1',$value);?>

          </div>   
           <div class="col-md-4">
                  	<?php  $value=(isset($police) ? $police->asset_detail : ''); ?>
                      <?php echo render_textarea( 'asset_detail', 'asset_detail',$value,array('rows'=>2)); ?>
                      
                  </div> 
        <div class="col-md-4">
                     <div class="form-group mbot20">
                      		<?php  $value=(isset($police) ? $police->remarks : ''); ?>
                       	<?php echo render_textarea('remarks','remarks',$value,array('rows'=>2),array(),''); ?>
                     </div>
					  </div>
                                                   <div class="clearfix"></div><br>
                                                   <div class="col-md-12">
                        
           				 <fieldset>
 						 <legend> <?= _l('previous_case_typedet')?></legend>
                          <div class="col-md-4">
                       <div class="form-group">
          
           <label for=""><?= _l('previous_case_type')?></label>
                
             	<?php  $value=(isset($police) ? $police->previous_case_type : ''); ?>
               <select name="previous_case_type" class="form-control selectpicker">
                 <option value="">No Case</option>
               <option value="police">Police</option>
               <option value="civil">Civil</option>
           </select>
            </div> </div>
                                       <div class="col-md-4">
                         <div class="form-group">
                         	<?php  $value=(isset($police) ? $police->case_no : ''); ?>
              <?php echo render_input('case_no','case_no',$value,'text'); ?>
            </div> </div>
                        <div class="col-md-4">
                         <div class="form-group">
                         	<?php  $value=(isset($police) ? _d($police->case_date) : ''); ?>
                         
             <?php echo render_date_input('case_date','case_date',$value); ?>
            </div> </div>
                
                       </fieldset></div>
      <div class="clearfix"></div><br>
                   
              
                  <div class="col-md-12">
                     <?php echo render_custom_fields('tickets',$ticket->ticketid); ?>
                  </div>
               
               <?php hooks()->do_action('add_single_ticket_tab_menu_content', $ticket); ?>
               <div class="row">
                  <div class="col-md-12 text-center">
                     <hr />
                     <?php 
					  if(get_approvalticket_record_status($ticket->ticketid)){?>
                   <a href="#" class="btn btn-info add_policecase_ticket">
                        <?php echo _l('submit'); ?>
                     </a>
                    
                     <?php
																			  }
					  ?>
                     
                  </div>
               </div>
             <?php echo form_close(); ?>
				 </div>
         
         
            </div>
                  <div role="tabpanel" class="tab-pane <?php if($ticket->service !=1 && $ticket->service !=2 ){ if(!$this->session->flashdata('active_tab')){echo 'active';}} ?>" id="addreply">
                     <hr class="no-mtop" />
                     <?php $tags = get_tags_in($ticket->ticketid,'ticket'); ?>
                     <?php if(count($tags) > 0){ ?>
                        <div class="row">
                           <div class="col-md-12">
                              <?php echo '<b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b><br /><br /> ' . render_tags($tags); ?>
                              <hr />
                           </div>
                        </div>
                     <?php } ?>
                     <?php if(sizeof($ticket->ticket_notes) > 0){ ?>
                        <div class="row">
                           <div class="col-md-12 mbot15">
                              <h4 class="bold"><?php echo _l('ticket_single_private_staff_notes'); ?></h4>
                              <div class="ticketstaffnotes">
                                 <div class="table-responsive">
                                    <table>
                                       <tbody>
                                          <?php foreach($ticket->ticket_notes as $note){ ?>
                                             <tr>
                                                <td>
                                                   <span class="bold">
                                                      <?php echo staff_profile_image($note['addedfrom'],array('staff-profile-xs-image')); ?> <a href="<?php echo admin_url('staff/profile/'.$note['addedfrom']); ?>"><?php echo _l('ticket_single_ticket_note_by',get_staff_full_name($note['addedfrom'])); ?>
                                                   </a>
                                                </span>
                                                <?php
                                                if($note['addedfrom'] == get_staff_user_id() || is_admin()){ ?>
                                                   <div class="pull-right">
                                                      <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
                                                      <a href="<?php echo admin_url('misc/delete_note/'.$note["id"]); ?>" class="mright10 _delete btn btn-danger btn-icon">
                                                         <i class="fa fa-remove"></i>
                                                      </a>
                                                   </div>
                                                <?php } ?>
                                                <hr class="hr-10" />
                                                <div data-note-description="<?php echo $note['id']; ?>">
                                                   <?php echo check_for_links($note['description']); ?>
                                                </div>
                                                <div data-note-edit-textarea="<?php echo $note['id']; ?>" class="hide inline-block full-width">
                                                   <textarea name="description" class="form-control" rows="4"><?php echo clear_textarea_breaks($note['description']); ?></textarea>
                                                   <div class="text-right mtop15">
                                                      <button type="button" class="btn btn-default" onclick="toggle_edit_note(<?php echo $note['id']; ?>);return false;"><?php echo _l('cancel'); ?></button>
                                                      <button type="button" class="btn btn-info" onclick="edit_note(<?php echo $note['id']; ?>);"><?php echo _l('update_note'); ?></button>
                                                   </div>
                                                </div>
                                                <small class="bold">
                                                   <?php echo _l('ticket_single_note_added',_dt($note['dateadded'])); ?>
                                                </small>
                                             </td>
                                          </tr>
                                       <?php } ?>
                                    </tbody>
                                 </table>
                              </div>
                           </div>
                        </div>
                     </div>
                  <?php } ?>
                  <div>
                     <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'single-ticket-form','novalidate'=>true)); ?>
                     <a href="<?php echo admin_url('tickets/delete/'.$ticket->ticketid); ?>" class="btn btn-danger _delete btn-ticket-label mright5">
                        <i class="fa fa-remove"></i>
                     </a>

                     <?php if(!empty($ticket->priority_name)){ ?>
                        <span class="ticket-label label label-default inline-block">
                           <?php echo _l('ticket_single_priority',ticket_priority_translate($ticket->priorityid)); ?>
                        </span>
                     <?php } ?>
                     <?php if(!empty($ticket->service_name)){ ?>
                        <span class="ticket-label label label-default inline-block">
                           <?php echo _l('service'). ': ' . $ticket->service_name; ?>
                        </span>
                     <?php } ?>
                     <?php echo form_hidden('ticketid',$ticket->ticketid); ?>
                     <span class="ticket-label label label-default inline-block">
                        <?php echo _l('department') . ': '. $ticket->department_name; ?>
                     </span>
                     <?php if($ticket->assigned != 0){ ?>
                        <span class="ticket-label label label-info inline-block">
                           <?php echo _l('ticket_assigned'); ?>: <?php echo get_staff_full_name($ticket->assigned); ?>
                        </span>
                     <?php } ?>
                     <?php if($ticket->lastreply !== NULL){ ?>
                        <span class="ticket-label label label-success inline-block" data-toggle="tooltip" title="<?php echo _dt($ticket->lastreply); ?>">
                           <span class="text-has-action">
                              <?php echo _l('ticket_single_last_reply',time_ago($ticket->lastreply)); ?>
                           </span>
                        </span>
                     <?php } ?>

                     <span class="ticket-label label label-info inline-block">
                        <a href="<?php echo get_ticket_public_url($ticket); ?>" target="_blank">
                           <?php echo _l('view_public_form'); ?>
                        </a>
                     </span>

                     <div class="mtop15">
                        <?php
                        $use_knowledge_base = get_option('use_knowledge_base');
                        ?>
                      <!--  <div class="row mbot15">
                           <div class="col-md-6">
                              <select data-width="100%" id="insert_predefined_reply" data-live-search="true" class="selectpicker" data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
                                 <?php foreach($predefined_replies as $predefined_reply){ ?>
                                    <option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
                                 <?php } ?>
                              </select>
                           </div>
                           <?php if($use_knowledge_base == 1){ ?>
                              <div class="visible-xs">
                                 <div class="mtop15"></div>
                              </div>
                              <div class="col-md-6">
                                 <?php $groups = get_all_knowledge_base_articles_grouped(); ?>
                                 <select data-width="100%" id="insert_knowledge_base_link" class="selectpicker" data-live-search="true" onchange="insert_ticket_knowledgebase_link(this);" data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
                                    <option value=""></option>
                                    <?php foreach($groups as $group){ ?>
                                       <?php if(count($group['articles']) > 0){ ?>
                                          <optgroup label="<?php echo $group['name']; ?>">
                                             <?php foreach($group['articles'] as $article) { ?>
                                                <option value="<?php echo $article['articleid']; ?>">
                                                   <?php echo $article['subject']; ?>
                                                </option>
                                             <?php } ?>
                                          </optgroup>
                                       <?php } ?>
                                    <?php } ?>
                                 </select>
                              </div>
                           <?php } ?>
                        </div>-->
                        <?php echo render_textarea('message','','',array(),array(),'','tinymce'); ?>
                     </div>
                     <div class="panel_s ticket-reply-tools">
                        <div class="btn-bottom-toolbar text-right">
                           <button type="submit" class="btn btn-info" data-form="#single-ticket-form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>">
                              <?php echo _l('ticket_single_add_response'); ?>
                           </button>
                        </div>
                        <div class="panel-body">
                           <div class="row">
                              <div class="col-md-5">
                                 <?php echo render_select('status',$statuses,array('ticketstatusid','name'),'ticket_single_change_status',get_option('default_ticket_reply_status'),array(),array(),'','',false); ?>
                                 <?php echo render_input('cc','CC'); ?>
                                 <?php if($ticket->assigned !== get_staff_user_id()){ ?>
                                    <div class="checkbox">
                                       <input type="checkbox" name="assign_to_current_user" id="assign_to_current_user">
                                       <label for="assign_to_current_user"><?php echo _l('ticket_single_assign_to_me_on_update'); ?></label>
                                    </div>
                                 <?php } ?>
                                 <div class="checkbox">
                                    <input type="checkbox" <?php echo hooks()->apply_filters('ticket_add_response_and_back_to_list_default','checked'); ?> name="ticket_add_response_and_back_to_list" value="1" id="ticket_add_response_and_back_to_list">
                                    <label for="ticket_add_response_and_back_to_list"><?php echo _l('ticket_add_response_and_back_to_list'); ?></label>
                                 </div>
                              </div>
                           </div>
                           <hr />
                           <div class="row attachments">
                              <div class="attachment">
                                		<div class="col-md-4 mbot15">
								      <div class="form-group">
                     <label class=""><?php echo _l('document_type'); ?></label>
                  <select class="form-control" name="document_type[]">
                     <option></option>
                     <?php foreach($document_types as $doc_type){ ?>
                        <option value="<?=$doc_type['id']?>" ><?=$doc_type['name']?></option>
                     <?php } ?>
                  </select>
									</div></div>
                                 <div class="col-md-4 mbot15">
                                    <div class="form-group">
                                       <label for="attachment" class="control-label">
                                          <?php echo _l('ticket_single_attachments'); ?>
                                       </label>
                                       <div class="input-group">
                                          <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]" accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                                          <span class="input-group-btn">
                                             <button class="btn btn-success add_more_attachments p8-half" data-max="<?php echo get_option('maximum_allowed_ticket_attachments'); ?>" type="button"><i class="fa fa-plus"></i></button>
                                          </span>
                                       </div>
                                    </div>
                                 </div>
                                 <div class="clearfix"></div>
                              </div>
                           </div>
                        </div>
                     </div>
                     <?php echo form_close(); ?>
                  </div>
               </div>
               <div role="tabpanel" class="tab-pane" id="note">
                  <hr class="no-mtop" />
                  <div class="form-group">
                     <label for="note_description"><?php echo _l('ticket_single_note_heading'); ?></label>
                     <textarea class="form-control" name="note_description" rows="5"></textarea>
                  </div>
                  <a class="btn btn-info pull-right add_note_ticket"><?php echo _l('ticket_single_add_note'); ?></a>
               </div>
               <div role="tabpanel" class="tab-pane" id="tab_reminders">
                  <a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target=".reminder-modal-ticket-<?php echo $ticket->ticketid; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('ticket_set_reminder_title'); ?></a>
                  <hr />
                  <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
               </div>
               <div role="tabpanel" class="tab-pane" id="othertickets">
                  <hr class="no-mtop" />
                  <div class="_filters _hidden_inputs hidden tickets_filters">
                     <?php echo form_hidden('filters_ticket_id',$ticket->ticketid); ?>
                     <?php echo form_hidden('filters_email',$ticket->email); ?>
                     <?php echo form_hidden('filters_userid',$ticket->userid); ?>
                  </div>
                  <?php echo AdminTicketsTableStructure(); ?>
               </div>
               <div role="tabpanel" class="tab-pane" id="tasks">
                <!--<a target="_blank" href="<?php echo admin_url('tickets/legal_approval/'.$ticket->ticketid); ?>" class="btn btn-info btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Legal Request"> <i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Download Legal Request  </a>-->
                  <hr class="no-mtop" />
                  <?php init_relation_tasks_table(array('data-new-rel-id'=>$ticket->ticketid,'data-new-rel-type'=>'ticket')); ?>
               </div>
             
               <div role="tabpanel" class="tab-pane <?php if($this->session->flashdata('active_tab_settings')){echo 'active';} ?>" id="settings">
                  <hr class="no-mtop" />
                  <div class="col-md-12">
                  	
                 <?php echo form_open_multipart('',array('id'=>'closecase-ticket-form','novalidate'=>true)); ?>
                  <div class="row">
                     <div class="col-md-6">
                        <div class="row"> <div class="col-md-12">
                              <?php 
	    echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service',$ticket->service,array('disabled'=>true));
                              ?>
							</div></div>
                        <?php echo render_input('subject','ticket_settings_subject',$ticket->subject); ?>
                        <div class="form-group select-placeholder">
                           <label for="contactid" class="control-label"><?php echo _l('contact'); ?></label>
                           <select name="contactid" id="contactid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"<?php if(!empty($ticket->from_name) && !empty($ticket->ticket_email)){echo ' data-no-contact="true"';} else {echo ' data-ticket-emails="'.$ticket->ticket_emails.'"';} ?>>
                              <?php
                              $rel_data = get_relation_data('contact',$ticket->contactid);
                              $rel_val = get_relation_values($rel_data,'contact');
                              echo '<option value="'.$rel_val['id'].'" selected data-subtext="'.$rel_val['subtext'].'">'.$rel_val['name'].'</option>';
                              ?>
                           </select>
                           <?php echo form_hidden('userid',$ticket->userid); ?>
                        </div>
                        <div class="row">
                           <div class="col-md-6">
                              <?php echo render_input('name','ticket_settings_to',$ticket->submitter,'text',array('disabled'=>true)); ?>
                           </div>
                           <div class="col-md-6">
                              <?php
                              if($ticket->userid != 0){
                                echo render_input('email','ticket_settings_email',$ticket->email,'email',array('disabled'=>true));
                             } else {
                                echo render_input('email','ticket_settings_email',$ticket->ticket_email,'email',array('disabled'=>true));
                             }
                             ?>
                          </div>
                       </div>
                       	<div class="row">
						<div class="col-md-6">
						 <div class="form-group">
                        <?php echo render_input('opposteparty','customer_name',$ticket->opposteparty,'text',array('required'=>'true')); ?>
                     
                     </div>
						</div>
						<div class="col-md-6">
								<?php echo render_input('customer_code','ticket_settings_code',$ticket->customer_code,'text'); ?>
							</div>
							<div class="col-md-12">
								<?php echo render_input('cc','CC'); ?>
							</div>
							
							
						</div>
                      
                    </div>
                    <div class="col-md-6">
                     <div class="form-group">
                        <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                        <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo prep_tags_input(get_tags_in($ticket->ticketid,'ticket')); ?>" data-role="tagsinput">
                     </div>
                      <div class="row">
                      <div class="col-md-6">
                     <div class="form-group select-placeholder">
                        <label for="assigned" class="control-label">
                           <?php echo _l('ticket_settings_assign_to'); ?>
                        </label>
                        <select name="assigned" data-live-search="true" id="assigned" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                           <?php foreach($staff as $member){
                                       // Ticket is assigned to member
                                       // Member is set to inactive
                                       // We should show the member in the dropdown too
                                       // Otherwise, skip this member
                              if($member['active'] == 0 && $ticket->assigned != $member['staffid']) {
                                 continue;
                              }
                              ?>
                              <option value="<?php echo $member['staffid']; ?>" <?php if($ticket->assigned == $member['staffid']){echo 'selected';} ?>>
                                 <?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
                              </option>
                           <?php } ?>
                        </select>
                     </div>
					  </div>
                     <div class="col-md-6">
                     	 <?php echo render_input('file_amount','amt_case',$ticket->file_amount,'text'); ?>
                     </div>
						</div>
                     <div class="row">
                        <div class="col-md-<?php if(get_option('services') == 1){ echo 6; }else{echo 12;} ?>">
                           <?php
                           $priorities['callback_translate'] = 'ticket_priority_translate';
                           echo render_select('priority',$priorities,array('priorityid','name'),'ticket_settings_priority',$ticket->priority); ?>
                        </div>
                       <div class="col-md-6">
								 <?php echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments',$ticket->department); ?>
							</div>
                          
                      
                    
                     </div>
                     <div class="form-group select-placeholder projects-wrapper<?php if($ticket->userid == 0){echo ' hide';} ?>">
                        <label for="project_id"><?php echo _l('project'); ?></label>
                        <div id="project_ajax_search_wrapper">
                           <select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <?php if($ticket->project_id != 0){ ?>
                                 <option value="<?php echo $ticket->project_id; ?>"><?php echo get_project_name_by_id($ticket->project_id); ?></option>
                              <?php } ?>
                           </select>
                        </div>
                     </div>
                   
                     
					
                          <?php echo render_textarea('gen_reason','reason',$ticket->gen_reason,array('rows'=>2),array(),'',''); ?>	
                      
                          <?php echo render_textarea('oth_comments','oth_comments',$ticket->oth_comments,array('rows'=>2),array(),'',''); ?>		
							
                  </div>
                  <div class="col-md-12">
                     <?php echo render_custom_fields('tickets',$ticket->ticketid); ?>
                  </div>
               </div>
               	<!--  Additional data for Case Close Application ------>
               	<?php if($ticket->service=='7'){?>
		<div class="row" id="closeapp">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<h4><?php echo _l('caseclose_info'); ?></h4>
					</div>
					<div class="panel-body">
					                          	<div class="row">
						<div class="col-md-4">
						<div class="form-group">
						<?php echo render_input('closecase_type','closecase_type',$ticket->closecase_type,'text'); ?>
							</div>	
						</div>
						<div class="col-md-4">
								<?php echo render_input('ledgerclaim_amount','ledgerclaim_amount',$ticket->ledgerclaim_amount,'text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('closecase_amount','closecase_amount',$ticket->closecase_amount,'text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('total_expense','total_expense',$ticket->total_expense,'text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('amount_received','amount_received1',$ticket->amount_received,'text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('excess_amount','excess_amount',$ticket->excess_amount,'text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('writeoff_amount','writeoff_amount',$ticket->writeoff_amount,'text'); ?>
							</div>
							
						</div>
						                          <div class="col-md-12">
                             <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?=_l('civilcase_fileddet')?></legend>                
                        
           				 
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_close">  
                   <thead>
                   	<th width="25%"><?=_l('amount')?></th>
                   	<th width="75%"><?=_l('remarks')?></th>
                   	
                   
                   </thead>
                    <tbody>
                    
                                        
                       <?php if(isset($ticket)&& ($ticket->civilcase_fileddet!='')){
						
						$civilcases=json_decode($ticket->civilcase_fileddet,true);
							$limit=sizeof($civilcases['amount']);
							for($i=0;$i<$limit;$i++) {
				
						 ?>
                      <tr id="clrow<?=$i?>" class="dynamic-added-close"> 
                     
                     
                      <td><input type="text" name="civilcase_fileddet[amount][]" placeholder="Enter Amount" class="form-control" value="<?=$civilcases['amount'][$i]?>"  /></td>
                      <td><textarea  name="civilcase_fileddet[remarks][]" rows="2" class="form-control"><?=$civilcases['remarks'][$i]?></textarea></td>
                      <?php 
							 if($i==0){
								 ?>
							 
                         <td><button type="button" name="addcasedet" id="addcasedet" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                        <?php } else{ ?>
                        <td><button type="button" name="remove" id="<?=$i ?>" class="btn btn-danger btn_close_remove">X</button></td>
                        <?php } ?>

                                             
                    </tr>
                    <?php
							}
}
	?>
                    
                 
                    </tbody>
                     
                </table>  
							 </div>
          </fieldset>
           

												</div>
			
					</div>
				</div>
			</div>
		</div>
              <?php } ?>
               <?php hooks()->do_action('add_single_ticket_tab_menu_content', $ticket); ?>
               <div class="row">
                  <div class="col-md-12 text-center">
                     <hr />
                      <?php 
					  if(get_approvalticket_record_status($ticket->ticketid)){?>
                     <a href="#" class="btn btn-info save_changes_settings_single_ticket">
                        <?php echo _l('submit'); ?>
                     </a>
                     <?php }
					  ?>
                  </div>
                    <div class="clearfix"></div>
                    <br> <hr class="no-mtop" /><br>
               </div>
                   <?php if(sizeof($creditapp)>0){?>
                    <div class="panel_s ticket-reply-tools">
                      
                        <div class="panel-body">
                          <h4>  <?php echo _l('credit_attachment'); ?></h4>
                           <div class="row">
                              <div class="col-md-12">
                               <div class="table-responsive">  
                <table class="table table-bordered text-center">  
                   <thead>
                   <th class="not_sortable" style="text-align: center">Sr. No.</th>
                   	<th style="text-align: center"><?=_l('document_type')?></th>
                   	<th style="text-align: center"><?=_l('document_number')?></th>
                   	<th style="text-align: center"><?=_l('document_name')?></th>
                   	<th style="text-align: center"><?=_l('nationality')?></th>
                   	<th style="text-align: center"><?=_l('expiry_date')?></th>
                  <?php if($ticket->admin!=get_staff_user_id()){?>   <th style="text-align: center"><?=_l('action')?></th><?php }?>
                                    
                   </thead>
                    <tbody>
                    
                                        
                       <?php 
							$i=1;
	
							foreach($creditapp as $row){
														 
					 ?>
                      <tr> 
                      <td><?=$i++; ?> </td>
                     <td><?=get_document_type_name($row['document_type'])?></td>
                      <td><?=$row['document_number']; ?></td>
                       <td><?=$row['document_name']; ?></td>
                        <td><?=get_countryproject_name($row['nationality']); ?></td>
                         <td><?=_d($row['expiry_date']); ?></td>
                        <?php if($ticket->admin!=get_staff_user_id()){?>  <td><a class="btn btn-info mbot25" href="#" onclick="showAjaxModal('<?php echo admin_url();?>tickets/editcredit/<?php echo $row['id']?>');">	<i class="fa fa-pencil"></i></a></td><?php } ?>
                      
                      
                       
                       
                    </tr>
                    
                 <?php
							}
						?>
                    </tbody>
                     
                </table>  
							 </div>  
                              </div>
                           </div>
                           <hr />
                     
                        </div>
                     </div>
                     <?php } ?>
                      <?php echo form_close(); ?>
				   </div>
            </div>
      <?php ###################### Approval ################################### ?>      
            <div role="tabpanel" class="tab-pane <?php if($this->session->flashdata('active_tab_approvals')){echo 'active';} ?>" id="approval">
               <hr class="no-mtop" />
               <div class="row">
                  <div class="col-md-12">
                     <?php if(is_array($ticket->approvals)){ ?> 
                        <table class="table table-bordered text-center">
                          <?php foreach($ticket->approvals as $approval){ ?>
                           <tr>
                              <th>
                                 <div class="col-md-3">
                                    <?=_l($approval['approval_type'])?>
                                    <?php  if($approval['staffid'] != 0){ 
                                       $attr1='disabled'; } else{
                                          $attr1='onchange=update_approval_staff(this,'.$approval['id'].')';
}
                                          ?>
                                    <select name="approval_assigned[]" data-live-search="true" id="approval_assigned" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?=$attr1;?>>
                                       <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                                       <?php foreach($staff as $member){
                                                  
                                          if($member['active'] == 0 && $ticket->assigned != $member['staffid']) {
                                             continue;
                                          }
                                          ?>
                                          <option value="<?php echo $member['staffid']; ?>" <?php if( $approval['staffid'] == $member['staffid'] ){ echo 'selected';} ?>  >
                                             <?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
                                          </option>
                                    <?php } ?>
                                    </select>
                                 </div>
                                 <div class="col-md-3 mtop20">
                                    <?php  if($approval['staffid'] != get_staff_user_id()){ 
                                       $attr=array('disabled'=>'disabled'); } else{
                                          $attr=array('onchange'=>'update_approval_status(this,'.$approval['id'].')');}
                                          ?>
                                    <?php echo render_select('approval_status',$statuses,array('ticketstatusid','name'),'',$approval['approval_status'],$attr,array(),'no-mbot','',false); ?>
                                 </div>
                                 <div class="col-md-6 mtop20">
                                   <?php  if($approval['staffid'] != get_staff_user_id()){ 
                                        $attr11=array('rows'=>2,'placeholder'=>'Remarks','disabled'=>'disabled'); } else{
                                 $attr11=array('rows'=>2,'placeholder'=>'Remarks','onblur'=>'update_approval_remarks(this,'.$approval['id'].')');}
                                          ?>
                                    <?php echo render_textarea('approval_remarks','',$approval['approval_remarks'],$attr11); ?>
                                 </div>
                       
                              </th>
                           </tr>
                           <?php }  ?>
                        </table>


                     <?php }else{ ?>
                     <table class="table table-bordered text-center">
                     <?php 
                     $approval_types = get_approval_types('ticket'); ?>
                     
                     <tr>
                      <?php foreach($approval_types as $approval_type){ ?>  
                        <th><?=$approval_type['name']?>
                           <div class="form-group select-placeholder">
                              <select name="approval_assigned[]" data-live-search="true" id="approval_assigned" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                 <option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
                                 <?php foreach($staff as $member){
                                    if($member['active'] == 0 && $ticket->assigned != $member['staffid']) {
                                       continue;
                                    }
                                    ?>
                                    <option value="<?php echo $member['staffid']; ?>" <?php if(get_staff_user_id() == $member['staffid'] && $approval_type['id']=='prepared_by_accountant'){echo 'selected';} ?>>
                                       <?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
                                    </option>
                                 <?php } ?>
                              </select>
                           </div>
                        </th>
                        <?php } ?>
                     </tr>
                     <tr>
                        <td colspan="<?=sizeof($approval_types)?>">
                           <div class="col-md-12 text-center">
                              <button type="button"  class="btn btn-info save_changes_approval_ticket"><?php echo _l('submit'); ?></button>
                           </div>
                        </td>
                     </tr>
                    
                     </table>
                  <?php } ?>
                  </div>
               </div>
            </div>
      <?php ###################### Approval end ################################### ?>      
         </div>
      </div>
   </div>
   <div class="panel_s mtop20">
      <div class="panel-body <?php if($ticket->admin == NULL){echo 'client-reply';} ?>">
         <div class="row">
            <div class="col-md-3 border-right ticket-submitter-info ticket-submitter-info">
               <p>
                  <?php if($ticket->admin == NULL || $ticket->admin == 0){ ?>
                     <?php if($ticket->userid != 0){ ?>
                        <a href="<?php echo admin_url('clients/client/'.$ticket->userid.'?contactid='.$ticket->contactid); ?>"
                           ><?php echo $ticket->submitter; ?>
                        </a>
                     <?php } else {
                        echo $ticket->submitter;
                        ?>
                        <br />
                        <a href="mailto:<?php echo $ticket->ticket_email; ?>"><?php echo $ticket->ticket_email; ?></a>
                        <hr />
                        <?php
                        if(total_rows(db_prefix().'spam_filters',array('type'=>'sender','value'=>$ticket->ticket_email,'rel_type'=>'tickets')) == 0){ ?>
                          <button type="button" data-sender="<?php echo $ticket->ticket_email; ?>" class="btn btn-danger block-sender btn-xs">     <?php echo _l('block_sender'); ?>
                       </button>
                       <?php
                    } else {
                       echo '<span class="label label-danger">'._l('sender_blocked').'</span>';
                    }
                 }
              } else {  ?>
               <a href="<?php echo admin_url('profile/'.$ticket->admin); ?>"><?php echo $ticket->opened_by; ?></a>
            <?php } ?>
         </p>
         <p class="text-muted">
            <?php if($ticket->admin !== NULL || $ticket->admin != 0){
               echo _l('ticket_staff_string');
            } else {
               if($ticket->userid != 0){
                 echo _l('ticket_client_string');
              }
           }
           ?>
        </p>
        <?php if(has_permission('tasks','','create')){ ?>
         <a href="#" class="btn btn-default btn-xs" onclick="convert_ticket_to_task(<?php echo $ticket->ticketid; ?>,'ticket'); return false;"><?php echo _l('convert_to_task'); ?></a>
      <?php } ?>
   </div>
   <div class="col-md-9">
      <div class="row">
         <div class="col-md-12 text-right">
            <?php if(!empty($ticket->message)) { ?>
               <a href="#" onclick="print_ticket_message(<?php echo $ticket->ticketid; ?>, 'ticket'); return false;" class="mright5"><i class="fa fa-print"></i></a>
            <?php } ?>
            <a href="#" onclick="edit_ticket_message(<?php echo $ticket->ticketid; ?>,'ticket'); return false;"><i class="fa fa-pencil-square-o"></i></a>
         </div>
      </div>
      <div data-ticket-id="<?php echo $ticket->ticketid; ?>" class="tc-content">
         <?php echo check_for_links($ticket->message); ?>
      </div>
      <?php if(count($ticket->attachments) > 0){
         echo '<hr />';
         foreach($ticket->attachments as $attachment){

           $path = get_upload_path_by_type('ticket').$ticket->ticketid.'/'.$attachment['file_name'];
           $is_image = is_image($path);

           if($is_image){
             echo '<div class="preview_image">';
          }
          ?>
          <a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block mbot5"<?php if($is_image){ ?> data-lightbox="attachment-ticket-<?php echo $ticket->ticketid; ?>" <?php } ?>>
            <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i><?php echo get_document_type_name($attachment['document_type']).' - '.$attachment['file_name']; ?>
            <?php if($is_image){ ?>
               <img class="mtop5" src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$attachment['filetype']); ?>">
            <?php } ?>
         </a>
         <?php if($is_image){
            echo '</div>';
         }
         if(is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_ticket_attachments') == '1')){
            echo '<a href="'.admin_url('tickets/delete_attachment/'.$attachment['id']).'" class="text-danger _delete">'._l('delete').'</a>';
         }
         echo '<hr />';
         ?>
      <?php }
   } ?>
</div>
</div>
</div>
<div class="panel-footer">
   <?php echo _l('ticket_posted',_dt($ticket->date)); ?>
</div>
</div>
<?php foreach($ticket_replies as $reply){ ?>
   <div class="panel_s">
      <div class="panel-body <?php if($reply['admin'] == NULL){echo 'client-reply';} ?>">
         <div class="row">
            <div class="col-md-3 border-right ticket-submitter-info">
               <p>
                  <?php if($reply['admin'] == NULL || $reply['admin'] == 0){ ?>
                     <?php if($reply['userid'] != 0){ ?>
                        <a href="<?php echo admin_url('clients/client/'.$reply['userid'].'?contactid='.$reply['contactid']); ?>"><?php echo $reply['submitter']; ?></a>
                     <?php } else { ?>
                        <?php echo $reply['submitter']; ?>
                        <br />
                        <a href="mailto:<?php echo $reply['reply_email']; ?>"><?php echo $reply['reply_email']; ?></a>
                     <?php } ?>
                  <?php }  else { ?>
                     <a href="<?php echo admin_url('profile/'.$reply['admin']); ?>"><?php echo $reply['submitter']; ?></a>
                  <?php } ?>
               </p>
               <p class="text-muted">
                  <?php if($reply['admin'] !== NULL || $reply['admin'] != 0){
                     echo _l('ticket_staff_string');
                  } else {
                     if($reply['userid'] != 0){
                       echo _l('ticket_client_string');
                    }
                 }
                 ?>
              </p>
              <hr />
              <a href="<?php echo admin_url('tickets/delete_ticket_reply/'.$ticket->ticketid .'/'.$reply['id']); ?>" class="btn btn-danger pull-left _delete mright5 btn-xs"><?php echo _l('delete_ticket_reply'); ?></a>
              <div class="clearfix"></div>
              <?php if(has_permission('tasks','','create')){ ?>
               <a href="#" class="pull-left btn btn-default mtop5 btn-xs" onclick="convert_ticket_to_task(<?php echo $reply['id']; ?>,'reply'); return false;"><?php echo _l('convert_to_task'); ?>
            </a>
            <div class="clearfix"></div>
         <?php } ?>
      </div>
      <div class="col-md-9">
         <div class="row">
            <div class="col-md-12 text-right">
               <?php if(!empty($reply['message'])) { ?>
                  <a href="#" onclick="print_ticket_message(<?php echo $reply['id']; ?>, 'reply'); return false;" class="mright5"><i class="fa fa-print"></i></a>
               <?php } ?>
               <a href="#" onclick="edit_ticket_message(<?php echo $reply['id']; ?>,'reply'); return false;"><i class="fa fa-pencil-square-o"></i></a>
            </div>
         </div>
         <div class="clearfix"></div>
         <div data-reply-id="<?php echo $reply['id']; ?>" class="tc-content">
            <?php echo check_for_links($reply['message']); ?>
         </div>
         <?php if(count($reply['attachments']) > 0){
            echo '<hr />';
            foreach($reply['attachments'] as $attachment){
              $path = get_upload_path_by_type('ticket').$ticket->ticketid.'/'.$attachment['file_name'];
              $is_image = is_image($path);

              if($is_image){
                echo '<div class="preview_image">';
             }
             ?>
             <a href="<?php echo site_url('download/file/ticket/'. $attachment['id']); ?>" class="display-block mbot5"<?php if($is_image){ ?> data-lightbox="attachment-reply-<?php echo $reply['id']; ?>" <?php } ?>>
               <i class="<?php echo get_mime_class($attachment['filetype']); ?>"></i> <?php echo get_document_type_name($attachment['document_type']).' - '.$attachment['file_name']; ?>
               <?php if($is_image){ ?>
                  <img class="mtop5" src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$attachment['filetype']); ?>">
               <?php } ?>
            </a>
            <?php if($is_image){
               echo '</div>';
            }
            if(is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_ticket_attachments') == '1')){
               echo '<a href="'.admin_url('tickets/delete_attachment/'.$attachment['id']).'" class="text-danger _delete">'._l('delete').'</a>';
            }
            echo '<hr />';
         }
      } ?>
   </div>
</div>
</div>
<div class="panel-footer">
   <span><?php echo _l('ticket_posted',_dt($reply['date'])); ?></span>
</div>
</div>
<?php } ?>
</div>
</div>
<div class="btn-bottom-pusher"></div>
<?php if(count($ticket_replies) > 1){ ?>
   <a href="#top" id="toplink">↑</a>
   <a href="#bot" id="botlink">↓</a>
<?php } ?>
</div>
</div>
<!-- The reminders modal -->
<?php $this->load->view('admin/includes/modals/reminder',array(
   'id'=>$ticket->ticketid,
   'name'=>'ticket',
   'members'=>$staff,
   'reminder_title'=>_l('ticket_set_reminder_title'))
); ?>
<!-- Edit Ticket Messsage Modal -->
<div class="modal fade" id="ticket-message" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
   <div class="modal-dialog modal-lg" role="document">
      <?php echo form_open(admin_url('tickets/edit_message')); ?>
      <div class="modal-content">
         <div id="edit-ticket-message-additional"></div>
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title" id="myModalLabel"><?php echo _l('ticket_message_edit'); ?></h4>
         </div>
         <div class="modal-body">
            <?php echo render_textarea('data','','',array(),array(),'','tinymce-ticket-edit'); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <?php echo form_close(); ?>
   </div>
</div>


<script>
   var _ticket_message;
</script>
<?php $this->load->view('admin/tickets/services/service'); ?>
<?php init_tail(); ?>
<?php hooks()->do_action('ticket_admin_single_page_loaded', $ticket); ?>
<script>
   $(function(){
      $('#single-ticket-form').appFormValidator();
      init_ajax_search('contact','#contactid.ajax-search',{tickets_contacts:true});
      init_ajax_search('project', 'select[name="project_id"]', {
         customer_id: function() {
            return $('input[name="userid"]').val();
         }
      });
      $('body').on('shown.bs.modal', '#_task_modal', function() {
         if(typeof(_ticket_message) != 'undefined') {
            // Init the task description editor
            if(!is_mobile()){
             $(this).find('#description').click();
          } else {
            $(this).find('#description').focus();
         }
         setTimeout(function(){
            tinymce.get('description').execCommand('mceInsertContent', false, _ticket_message);
            $('#_task_modal input[name="name"]').val($('#ticket_subject').text().trim());
         },100);
      }
   });
   });


   var Ticket_message_editor;
   var edit_ticket_message_additional = $('#edit-ticket-message-additional');

   function edit_ticket_message(id, type){
      edit_ticket_message_additional.empty();
      // type is either ticket or reply
      _ticket_message = $('[data-'+type+'-id="'+id+'"]').html();
      init_ticket_edit_editor();
      tinyMCE.activeEditor.setContent(_ticket_message);
      $('#ticket-message').modal('show');
      edit_ticket_message_additional.append(hidden_input('type',type));
      edit_ticket_message_additional.append(hidden_input('id',id));
      edit_ticket_message_additional.append(hidden_input('main_ticket',$('input[name="ticketid"]').val()));
   }

   function init_ticket_edit_editor(){
      if(typeof(Ticket_message_editor) !== 'undefined'){
         return true;
      }
      Ticket_message_editor = init_editor('.tinymce-ticket-edit');
   }
   <?php if(has_permission('tasks','','create')){ ?>
      function convert_ticket_to_task(id, type){
         if(type == 'ticket'){
            _ticket_message = $('[data-ticket-id="'+id+'"]').html();
         } else {
            _ticket_message = $('[data-reply-id="'+id+'"]').html();
         }
         var new_task_url = admin_url + 'tasks/task?rel_id=<?php echo $ticket->ticketid; ?>&rel_type=ticket&ticket_to_task=true';
         new_task(new_task_url);
      }
   <?php } ?>

</script>
</body>
</html>
<script type="text/javascript">
   $('.save_changes_approval_ticket').on('click', function(e) {
        e.preventDefault();
        var data={};
        data = $('#approval *').serialize();
        data += '&ticketid=' + $('input[name="ticketid"]').val();
        if (typeof(csrfData) !== 'undefined') {
            data += '&' + csrfData['token_name'] + '=' + csrfData['hash'];
        }
       $.post(admin_url + 'tickets/update_single_ticket_approvals', data).done(function(response) {
            response = JSON.parse(response);
            console.log(response);
            if (response.success == true) {
               window.location.reload();               
            }
        });
    });

   function update_approval_status(th,id) {
        var status = $(th).val();
        requestGetJSON('tickets/change_approval_status_ajax/' + id + '/' + status).done(function(response) {
            alert_float(response.alert, response.message);
			setTimeout(function() {
              window.location.reload();
          }, 500);
        });
    };
	function update_approval_staff(th,id) {
        var status = $(th).val();
        requestGetJSON('tickets/change_approval_staff_ajax/' + id + '/' + status).done(function(response) {
            alert_float(response.alert, response.message);
			setTimeout(function() {
              window.location.reload();
          }, 500);
        });
    };

    function update_approval_remarks(th,id) {
      var remarks = $(th).val();
      var data={"remarks" : remarks};
      $.post(admin_url + 'tickets/change_approval_remarks_ajax/'+id, data).done(function(response) {
         response = JSON.parse(response);
         alert_float(response.alert, response.message);
      });
   } 
   
</script>
<script type="text/javascript">
    $(document).ready(function(){      
      var i=1; 
		var p=1;
		var s=1;
		var cd=1;
		var ci=1;
		var cci=1;var cp=1;var ss=1;var gc=1;var gcc=1;
		$('#addoverdue').click(function(){  
		 
           cd++;  
           $('#dynamic_field_dues').append('<tr id="drow'+cd+'"class="dynamic-added-dues"> <td><input type="date" name="overdue_detail[due_date][]" placeholder="Enter Due Date" class="form-control" value=""  /></td><td><input type="text" name="overdue_detail[due_amount][]" placeholder="Enter Due Amount" class="form-control" value=""  /></td><td><input type="text" name="overdue_detail[due_days][]" placeholder="Enter Due Days" class="form-control" value="" readonly /></td><td><button type="button" name="remove" id="'+cd+'" class="btn btn-danger btn_due_remove">X</button></td></tr>');  
		$('[name="overdue_detail[due_date][]"]').on('keyup keydown keypress change',function(){
			
			calc();
		})	
      });
  
      $(document).on('click', '.btn_due_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#drow'+button_id+'').remove();  
      }); 
   		$('[name="overdue_detail[due_date][]"]').on('keyup keydown keypress change',function(){
			
			calc();
		})
		function calc(){
		var total = 0;
		$('#dynamic_field_dues tbody tr').each(function(){
			var _this = $(this)
			var duedate1 = _this.find('[name="overdue_detail[due_date][]"]').val()
			 var today = new Date().toLocaleDateString()
			 var date1, date2;  
         //define two date object variables with dates inside it  
         date1 = new Date(duedate1);  
         date2 = new Date(today);  
  
         //calculate time difference  
         var time_difference = date2.getTime() - date1.getTime();  
  
         //calculate days difference by dividing total milliseconds in a day  
         var days_difference = time_difference / (1000 * 60 * 60 * 24);  
			_this.find('[name="overdue_detail[due_days][]"]').val(Math.round(days_difference,0));
			
			
		})
			

	}
      $('#addcheque').click(function(){  
		 
           i++;  
           $('#dynamic_field').append('<tr id="row'+i+'" class="dynamic-added"><td><input type="text" name="return_chq_details[chequeno][]" placeholder="Enter Cheque Number" class="form-control" required /></td><td><input type="text" name="return_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" required /></td><td><input type="text" name="return_chq_details[partial_payment][]" placeholder="Enter Partial Amount" class="form-control" required /></td><td><input type="text" name="return_chq_details[balance][]" placeholder="Enter Balance" class="form-control" required /></td><td><input type="date" name="return_chq_details[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" required /></td><td><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove">X</button></td></tr>'); 
		  
		  $('#police-ticket-form [name="return_chq_details[cheque_amount][]"],[name="return_chq_details[partial_payment][]"]').on('keyup keydown keypress change',function(){
			
			calc1p();
		})
		 
      });
  
      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#row'+button_id+'').remove();  
      }); 
		$('#police-ticket-form [name="return_chq_details[cheque_amount][]"],[name="return_chq_details[partial_payment][]"]').on('keyup keydown keypress change',function(){
			
			calc1p();
		})
		function calc1p(){
		var total = 0;
		$('#dynamic_field tbody tr').each(function(){
			var _this = $(this)
			var camount= _this.find('[name="return_chq_details[cheque_amount][]"]').val()
			var pamount= _this.find('[name="return_chq_details[partial_payment][]"]').val()
			 
         var bal = parseFloat(camount) - parseFloat(pamount);  
  
        	_this.find('[name="return_chq_details[balance][]"]').val(parseFloat(bal));
			
			
		})
			

	}
		 $('#addcivilcheque').click(function(){  
		 
           ci++;  
           $('#dynamic_field_civil').append('<tr id="vrow'+ci+'" class="dynamic-added-civil"><td><input type="text" name="return_chq_details[chequeno][]" placeholder="Enter Cheque Number" class="form-control" required /></td><td><input type="text" name="return_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" required /></td><td><input type="text" name="return_chq_details[partial_payment][]" placeholder="Enter Partial Amount" class="form-control" required /></td><td><input type="text" name="return_chq_details[balance][]" placeholder="Enter Balance" class="form-control" required /></td><td><input type="date" name="return_chq_details[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" required /></td><td><button type="button" name="remove" id="'+ci+'" class="btn btn-danger btn_chqremove">X</button></td></tr>'); 
			 
			 $('#civil-ticket-form [name="return_chq_details[cheque_amount][]"],[name="return_chq_details[partial_payment][]"]').on('keyup keydown keypress change',function(){
			
			calc1();
		})
      });
  
      $(document).on('click', '.btn_chqremove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#vrow'+button_id+'').remove();  
      }); 
		$('#civil-ticket-form [name="return_chq_details[cheque_amount][]"],[name="return_chq_details[partial_payment][]"]').on('keyup keydown keypress change',function(){
			
			calc1();
		})
		function calc1(){
		var total = 0;
		$('#dynamic_field_civil tbody tr').each(function(){
			var _this = $(this)
			var camount= _this.find('[name="return_chq_details[cheque_amount][]"]').val()
			var pamount= _this.find('[name="return_chq_details[partial_payment][]"]').val()
			 
         var bal = parseFloat(camount) - parseFloat(pamount);  
  
        	_this.find('[name="return_chq_details[balance][]"]').val(parseFloat(bal));
			
			
		})
			

	}
		
		
		 $('#addcivilcheque1').click(function(){  
		 
           cci++;  
           $('#dynamic_field_civil1').append('<tr id="cvrow'+cci+'" class="dynamic-added-civil1"><td><input type="text" name="addreturn_chq_details[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td><td><input type="text" name="addreturn_chq_details[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td> <td><input type="text" name="addreturn_chq_details[days_sale][]" placeholder="Enter Sale days" class="form-control" value=""  /></td> <td><input type="text" name="addreturn_chq_details[days_return][]" placeholder="Enter Return Days" class="form-control" value=""  /></td> <td><textarea  name="addreturn_chq_details[remark][]" rows="3" class="form-control"> </textarea></td><td><button type="button" name="remove" id="'+cci+'" class="btn btn-danger btn_chqremove1">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_chqremove1', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#cvrow'+button_id+'').remove();  
      }); 
		
		
		 $('#addpdccheque').click(function(){  
		
           p++;  
           $('#dynamic_field_pdc').append('<tr id="prow'+p+'" class="dynamic-added-pdc"><td><input type="text" name="pdc_in_hand[chequeno][]" placeholder="Enter Cheque Number" class="form-control" required /></td><td><input type="text" name="pdc_in_hand[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" required /></td><td><input type="date" name="pdc_in_hand[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" required /></td><td><input type="date" name="pdc_in_hand[sale_date][]" placeholder="Enter Sale Date" class="form-control" required /></td><td><button type="button" name="remove" id="'+p+'" class="btn btn-danger btn_pdc_remove">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_pdc_remove', function(){  
           var button_id = $(this).attr("id");  
		
           $('#prow'+button_id+'').remove();  
      });  
		$('#addpdccheque1').click(function(){  
		
           cp++;  
           $('#dynamic_field_pdc1').append('<tr id="pprow'+cp+'" class="dynamic-added-pdc1"><td><input type="text" name="pdc_in_hand[chequeno][]" placeholder="Enter Cheque Number" class="form-control" required /></td><td><input type="text" name="pdc_in_hand[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" required /></td><td><input type="date" name="pdc_in_hand[dateon_cheque][]" placeholder="Enter Cheque Date" class="form-control" required /></td><td><input type="date" name="pdc_in_hand[sale_date][]" placeholder="Enter Sale Date" class="form-control" required /></td><td><button type="button" name="remove" id="'+cp+'" class="btn btn-danger btn_pdc_remove1">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_pdc_remove1', function(){  
           var button_id = $(this).attr("id");  
		  //alert(button_id);
           $('#pprow'+button_id+'').remove();  
      }); 
		$('#addguarcheque1').click(function(){  
		
           gc++;  
           $('#dynamic_field_gchq1').append('<tr id="ggrow'+gc+'" class="dynamic-added-gchq1"><td><input type="text" name="guarantee_chequedet[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td><td><input type="text" name="guarantee_chequedet[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td> <td><input type="text" name="guarantee_chequedet[cheque_bank][]" placeholder="Enter Bank Name" class="form-control" value=""  /></td> <td><button type="button" name="remove" id="'+gc+'" class="btn btn-danger btn_gcheck1_remove">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_gcheck1_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#ggrow'+button_id+'').remove();  
      }); 
		$('#addguarcheque').click(function(){  
		
           gcc++;  
           $('#dynamic_field_gchq').append('<tr id="grow'+gcc+'" class="dynamic-added-gchq"><td><input type="text" name="guarantee_chequedet[chequeno][]" placeholder="Enter Cheque No" class="form-control" value=""  /></td><td><input type="text" name="guarantee_chequedet[cheque_amount][]" placeholder="Enter Cheque Amount" class="form-control" value=""  /></td> <td><input type="text" name="guarantee_chequedet[cheque_bank][]" placeholder="Enter Bank Name" class="form-control" value=""  /></td> <td><button type="button" name="remove" id="'+gcc+'" class="btn btn-danger btn_gcheck_remove">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_gcheck_remove', function(){  
           var button_id = $(this).attr("id");  
		 // alert(button_id);
           $('#grow'+button_id+'').remove();  
      }); 
		
			
		$('#addowner').click(function(){  
		
           s++;  
           $('#dynamic_field_owner').append('<tr id="srow'+s+'" class="dynamic-added-owner"><td><input type="text" name="owner_detail[owner_name][]" placeholder="Enter Owner Name" class="form-control" value=""  /><br><input type="text" name="owner_detail[nationality][]" placeholder="Enter Nationality" class="form-control" value=""  /><br><input type="text" name="owner_detail[ownstatus][]" placeholder="Enter Present Status" class="form-control" value=""  /></td><td><input type="text" name="owner_detail[emirates][]" placeholder="Enter Emirates id & Expiry" class="form-control" value=""  /><br><input type="text" name="owner_detail[passport][]" placeholder="Enter Passport No & Expiry" class="form-control" value=""  /><br><input type="text" name="owner_detail[email][]" placeholder="Enter Email" class="form-control" value=""  /></td> <td><textarea  name="owner_detail[contact1][]" rows="3" class="form-control"></textarea></td>  <td><textarea  name="owner_detail[contact2][]" rows="3" class="form-control"></textarea></td> <td><button type="button" name="remove" id="'+s+'" class="btn btn-danger btn_owner_remove">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_owner_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#srow'+button_id+'').remove();  
      }); 
		$('#addowner1').click(function(){  
		
           ss++;  
           $('#dynamic_field_owner1').append('<tr id="ssrow'+ss+'" class="dynamic-added-owner1"><td><input type="text" name="owner_detail[owner_name][]" placeholder="Enter Owner Name" class="form-control" value=""  /><br><input type="text" name="owner_detail[nationality][]" placeholder="Enter Nationality" class="form-control" value=""  /><br><input type="text" name="owner_detail[ownstatus][]" placeholder="Enter Present Status" class="form-control" value=""  /></td><td><input type="text" name="owner_detail[emirates][]" placeholder="Enter Emirates id & Expiry" class="form-control" value=""  /><br><input type="text" name="owner_detail[passport][]" placeholder="Enter Passport No & Expiry" class="form-control" value=""  /><br><input type="text" name="owner_detail[email][]" placeholder="Enter Email" class="form-control" value=""  /></td> <td><textarea  name="owner_detail[contact1][]" rows="3" class="form-control"></textarea></td>  <td><textarea  name="owner_detail[contact2][]" rows="3" class="form-control"></textarea></td> <td><button type="button" name="remove" id="'+ss+'" class="btn btn-danger btn_owner_remove1">X</button></td></tr>');  
      });
  
      $(document).on('click', '.btn_owner_remove1', function(){  
           var button_id = $(this).attr("id");  
		  //alert(button_id);
           $('#ssrow'+button_id+'').remove();  
      }); 

  		$('#addcasedet').click(function(){  
		 
           cc++;  
           $('#dynamic_field_close').append('<tr id="clrow'+cc+'"class="dynamic-added-close"> <td width="25%"><input type="text" name="civilcase_fileddet[amount][]" placeholder="Enter Amount" class="form-control" value=""  /></td><td width="75%"><textarea  name="civilcase_fileddet[remarks][]" rows="2" class="form-control"></textarea></td> <td><button type="button" name="remove" id="'+cc+'" class="btn btn-danger btn_close_remove">X</button></td></tr>');  
			
      });
  
      $(document).on('click', '.btn_close_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#clrow'+button_id+'').remove();  
      }); 
    });  
</script>
<script type="text/javascript">
	function showAjaxModal(url)
	{
		// SHOWING AJAX PRELOADER IMAGE
		jQuery('#modal_ajax .modal-body').html('<div style="text-align:center;margin-top:50px;"><img src="<?php echo base_url(); ?>/assets/images/preloader.gif" /></div>');
		
		// LOADING THE AJAX MODAL
		jQuery('#modal_ajax').modal('show', {backdrop: 'true'});
		
		// SHOW AJAX RESPONSE ON REQUEST SUCCESS
		$.ajax({
			url: url,
			success: function(response)
			{
				jQuery('#modal_ajax .modal-body').html(response);
			}
		});
	}
	</script>
    
    <!-- (Ajax Modal)-->
    <div class="modal fade" id="modal_ajax">
        <div class="modal-dialog">
            <div class="modal-content" style="width: 800px; margin-left: -150px;">
                
                <div class="modal-header" style="background:#4C74E3;">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title"><?php echo "Edit Credit Attachment"; ?></h4>
                </div>
                
                <div class="modal-body" style="height:550px; overflow:auto;">
                
                    
                    
                </div>
               <!-- 
                <div class="modal-footer" style="background:#fbeed5;">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
				-->
            </div>
        </div>
    </div>
    
