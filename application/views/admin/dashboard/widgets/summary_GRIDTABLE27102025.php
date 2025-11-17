<div class="row" style="margin-left:0px; margin-right: 0px;"> 
   <div class="panel_s">
      <div class="panel-body">
         <ul class="nav nav-pills">
            <?php if(has_permission('customers','','view')|| have_assigned_customers()){?>
             <li <?php if((has_permission('customers','','view')|| have_assigned_customers()) && $confirmapproval=='client'){ ?> class="active" <?php } ?> style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#home"><?php echo _l('customers') ?></a></li>
             <?php } ?>
			 <?php if($dashtype=='contract'){?>
             <li style="background-color: #84C529;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu3" onclick="load_legalrequest_report();return false;" style="color:#000;"><?php echo _l('legal_reviews') ?></a></li>
               <?php if(has_permission('contracts','','view')|| has_permission('contracts', '', 'view_own')){?>
            <li style="background-color: #ff6f00;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu11" onclick="load_contract_data(1); return false;" style="color:#000;"><?php echo _l('contracts') ?></a></li>
		  
			 <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menuact" onclick="load_contractactivity_report();return false;" style="color:#000;"><?php echo _l('contract_activity') ?></a></li>
              <?php if(is_approver()){
              	 $total_ticketapprove = total_rows(db_prefix().'approvals',array('staffid'=>get_staff_user_id(),'rel_type'=>'ticket','approval_status'=>2));
	$total_contractapprove = total_rows(db_prefix().'approvals',array('staffid'=>get_staff_user_id(),'rel_type'=>'contract','approval_status'=>2));
              ?>
              <?php if($total_ticketapprove>0){ ?>
              <li <?php if($confirmapproval=='legal'){ ?> class="active" <?php } ?> style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu5" onclick="load_ticketapprover_report();return false;"><?php echo _l('legal_approval_await') ?></a></li>
			 <?php } ?>
			 <?php if($total_contractapprove>0){?>
              <li <?php if($confirmapproval=='contract'){ ?> class="active" <?php } ?> style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu6" onclick="load_contractapprover_report();return false;"><?php echo _l('contract_approval_await') ?></a></li>
              <?php } ?>
              <?php } ?>
              <?php } ?>
			 <?php } ?>
			 <?php if($dashtype=='legal'){?>
             <li style="background-color:#a5dee673;color:black;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu1" onclick="load_project_data(1); return false;"><?php echo _l('projects') ?></a></li>
          
             <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu2" onclick="matter_hearing_report();return false;" style="color:#000;"><?php echo _l('hearings') ?></a></li>
			  <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu2ex" onclick="matter_execution_report();return false;" style="color:#000;"><?php echo _l('execution') ?></a></li>
			  <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu_judge" onclick="matter_judgement_report();return false;" style="color:#000;"><?php echo _l('judgement') ?></a></li>
            <?php } ?>
             <?php if(has_permission('projects','','view')){?>
              <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu4" onclick="load_oppositeparty_data(1);return false;"><?php echo _l('opposite_parties') ?></a></li>
              <?php } ?>
             <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu7" onclick="load_summary_data('<?=$dashtype?>');return false;" style="color:#000;"><?php echo _l('total_summary') ?></a></li>
          
         </ul>

         <div class="tab-content">
<!------- Clients ------------------------------------------------------------------>

           <div id="home" class="tab-pane fade <?php if((has_permission('customers','','view') || have_assigned_customers()) && $confirmapproval=='client') echo 'in active';?>">
 <div class="row">
            
         <div class="col-md-12 mtop10">
             <div class="clearfix"></div>
             <ul class="nav nav-tabs" role="tablist" >
          <li role="presentation" class="active">
            <a href="#tab_content_table1" aria-controls="tab_content" role="tab" data-toggle="tab" style="color: #098326;font-weight: bold;font-size:14px;">
              <?php echo _l('default_view'); ?>
            </a>
          </li>
          <li role="presentation">
            <a href="#tab_content_grid1" aria-controls="tab_grid" role="tab" data-toggle="tab" style="color: #3416B0;font-weight: bold;font-size:14px;">
              <?php echo _l('gridview'); ?>
            </a>
          </li>
           <div class="col-md-2 pull-right"> <?php if (has_permission('customers','','create')) { ?>
                     <a  href="<?php echo admin_url('clients/client'); ?>" class="btn btn-info mright5 test pull-right display-block">
                     <?php echo _l('new_client'); ?></a> <?php } ?></div>
    </ul>
             <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="tab_content_table1">
                              <div class="row">
                 
         <?php echo form_hidden('months-reportc1','custom'); ?>
         <div class="col-md-2">
          <label for="report-fromc" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
              <?php $beginMonth = date('01/01/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-fromc1" name="report-fromc1" value="">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-toc1" name="report-toc1" value="">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
		
         <div class="clearfix"></div>
      </div> 
            <div class="panel_s mtop25">
                        <div class="panel-body">
              <table class="table table-clients-report table-striped scroll-responsive" id="tabledash">
              <thead>
               <tr>
                  <th class="not_sortable"><?php echo _l('#'); ?></th>
                  <th><?php echo _l('clients_list_company'); ?></th>
                  <th><?php echo _l('client_no'); ?></th>
                  <th><?php echo _l('contact_primary'); ?></th>
                   <th><?php echo _l('clients_list_phone'); ?></th>
                   <th><?php echo _l('customer_groups'); ?></th>
                  <th><?php echo _l('summary'); ?></th>
               </tr>
            </thead>
            <tbody>
            	 <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                 </tr>
            </tbody>
           
         </table>
                       </div>
                   </div>
		</div>
             <div role="tabpanel" class="tab-pane" id="tab_content_grid1">
             <div class="row">

               <div class="col-md-12">
                  <div class="row">
                <div class="col-sm-6">
                   <div class="form-group has-search">
                <input type="text" id="search_2"  class="form-control" placeholder="<?php echo _l('search'); ?> <?php echo _l('customers') ?>">
               </div>
                </div> 
                <div class="col-md-4 mtop10 hide">
                    <label class="radio-inline">
                    <input type="radio" name="optradio" id="today_rad" >Today
                    </label>

                    <label class="radio-inline">
                    <input type="radio" name="optradio" id="this_month_rad">This Month 
                    </label>

                    <label class="radio-inline">
                    <input type="radio" name="optradio" id="last_month_rad">Last Month
                    </label>

                    <label class="radio-inline">
                    <input type="radio" name="optradio" id="all_rad" checked>All
                    </label>
                </div>
                <div class="col-md-2"><div id="total_clients" ></div></div>
               


                <div class="no_result2 hide"><h5>No result found..</h5></div>
                
                </div>

                <div id="div_ajax_client"></div>
            
               <div class="col-md-12 " > 
                 <div align="right" id="pagination_link1"></div>
               </div> 

               </div><!-- col-12 -->

               </div><!-- row --> 
				 </div>
			 </div>
				 </div>

			   </div>

           </div>
<!------- Case ------------------------------------------------------------------>
<div id="menu1" class="tab-pane fade">
             

                  <div class="row ">
                     
                  <div class="col-md-12 mtop10">
                  <div class="clearfix"></div>
                     <ul class="nav nav-tabs" role="tablist" >
                        <li role="presentation" class="active">
                           <a href="#tab_content_table_case" aria-controls="tab_content" role="tab" data-toggle="tab" style="color: #098326;font-weight: bold;font-size:14px;">
                           <?php echo _l('default_view'); ?>
                           </a>
                        </li>
                        <li role="presentation">
                           <a href="#tab_content_grid_case" aria-controls="tab_attachments" role="tab" data-toggle="tab" style="color: #3416B0;font-weight: bold;font-size:14px;">
                           <?php echo _l('gridview'); ?>
                           </a>
                        </li>
                  
                     </ul>
                     <div class="tab-content">

                           <div role="tabpanel" class="tab-pane active" id="tab_content_table_case">

                           <div class="row">
         
                                 <div class="col-md-3">
                                    <div class="form-group">
                                       <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                                       <select id="clientid" name="clientid3" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
                                       </select>
                                    </div>
                                 </div>
                                 <div class="col-md-3">
                                    <label for="status"><?php echo _l('status'); ?></label>
                                 <select class="form-control selectpicker" id="p_status" name="p_status" >
                                       <option value=""><?php  echo _l('all');?></option>
                                       <?php foreach($proj_statuses as $proj_statuse){ ?>
                                          <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
                                       <?php } ?>
                                 </select>
                                 </div> 
                                 <div class="clearfix"></div>
                           </div>

                              <div class="row">
                                 <div class="panel_s mtop25">
                                 <div class="panel-body">
                                    <table class="table table-case-report table-striped scroll-responsive" id="tabledash1">
                                    
                                       <thead>
                                          <tr>
                                             <th class="not_sortable"><?php echo _l('#'); ?></th>
                                             
                                             <th><?php echo _l('client'); ?></th>
                                             <th><?php echo _l('other_party'); ?></th>
                                             <th><?php echo _l('project_start_date'); ?></th>
                                             <th><?php echo _l('casediary_file_no'); ?></th>
                                             <th><?php echo _l('lawyer_attending'); ?></th>
                                             <th><?php echo _l('status'); ?></th>
                                          </tr>
                                       </thead>
                                       <tbody>
                                          <tr>
                                             <td></td>
                                             
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                             <td></td>
                                          </tr>
                                       </tbody>
               
                                    </table>
                                 </div>
                                 </div>
                              </div>
                           </div>


                           <div role="tabpanel" class="tab-pane" id="tab_content_grid_case">
                              <div class="row">
                                                   <div class="col-md-12">
                                       <div class="row">
                                    <div class="col-sm-4">
                                    <div class="form-group has-search">
                                    <input type="text" id="search_" name="search_p"  class="form-control" placeholder="Search <?php echo _l('project') ?>">
                                    </div>
                                    </div>

                                    <div class="col-md-2">
                                       <select class="form-control selectpicker" id="case_type" name="case_type" >
                                             <option value=" "><?=_l('case_type')?></option>
                                             <?php foreach($case_types as $case_type){ ?>
                                                <option value="<?=$case_type['id']?>"><?=_l($case_type['id'])?></option>
                                             <?php } ?>
                                       </select>
                                    </div>
                                    <div class="col-md-2">
                                       <select class="form-control selectpicker" id="c_status" name="status" >
                                             <option value=""><?=_l('status')?></option>
                                             <?php foreach($proj_statuses as $proj_statuse){ ?>
                                                <option value="<?=$proj_statuse['id']?>"><?=$proj_statuse['name']?></option>
                                             <?php } ?>
                                       </select>
                                    </div>
                                    <div class="col-md-2">
                                          <div id="total_cases"></div>
                                    </div>
                                 </div>
                                 <div class="row">
                                    <div class="no_result hide mleft30"><h5>No result found..</h5></div>
                                    
                                    </div>
                                    
                                    <div id="div_ajax_project"></div>

                                    <div class="col-md-12" > 
                                    <div align="right" id="pagination_link"></div>
                                    </div>  

                                    </div><!-- col-12 -->
                              </div>
                           </div>
                     </div>
                  </div><!-- row --> 

               </div>
           </div>
           <!------- Contracts ------------------------------------------------------------------>
           <div id="menu11" class="tab-pane fade">
           <div class="row">
            
         <div class="col-md-12 mtop10">
             <div class="clearfix"></div>
             <ul class="nav nav-tabs" role="tablist" >
          <li role="presentation" class="active">
            <a href="#tab_content_table" aria-controls="tab_content" role="tab" data-toggle="tab" style="color: #098326;font-weight: bold;font-size:14px;">
              <?php echo _l('default_view'); ?>
            </a>
          </li>
          <li role="presentation">
            <a href="#tab_content_grid" aria-controls="tab_attachments" role="tab" data-toggle="tab" style="color: #3416B0;font-weight: bold;font-size:14px;">
              <?php echo _l('gridview'); ?>
            </a>
          </li>
          
        </ul>
             <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab_content_table">
                   <div class="row">
         
         <div class="col-md-2">
            <div class="form-group">
               
                <?php echo render_select('contract_type1',$contract_types,array('id','name'),'contract_type','');?>
            </div>
         </div>
         <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('client_id22',$clients_,array('userid','company'),'customers','');?>
            </div>
         </div>
         
         <?php echo form_hidden('months-reportc','custom'); ?>
         <div class="col-md-2">
          <label for="report-fromc" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
              <?php $beginMonth = date('01/m/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-fromc" name="report-fromc" value="">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-toc" name="report-toc" value="">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
                    <label for="report-to" class="control-label"><?php echo _l('status'); ?></label>
                    <select class="form-control selectpicker" id="contract_status" name="contract_status" >
                        <option value="">Status</option>
                        <?php foreach($statuses as $proj_statuse){ ?>
                            <option value="<?=$proj_statuse['ticketstatusid']?>"><?=$proj_statuse['name']?></option>
                        <?php } ?>
                    </select>
                </div>
		  <div class="col-md-2 pull-right"> <?php if (has_permission('contracts','','create')) { ?>
                     <a onclick="new_quick_contract();return false;" href="#" class="btn btn-info mright5 test pull-right display-block">
                     <?php echo _l('new_contract'); ?></a> <?php } ?></div>
         <div class="clearfix"></div>
      </div> 
              
               <div class="panel_s mtop25">
                        <div class="panel-body">
               <table class="table table-agreements-report table-striped scroll-responsive" id="tabledash">                    
          
            <thead>
               <tr>
                  <th class="not_sortable"><?php echo _l('#'); ?></th>
                  <th><?php echo _l('particulars'); ?></th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('other_party'); ?></th>
                  <th><?php echo _l('agreement_type'); ?></th>
                  <th><?php echo _l('contract_value'); ?></th>
                  <th><?php echo _l('contract_list_start_date'); ?></th>
                  <th><?php echo _l('contract_list_end_date'); ?></th>
                  <th><?php echo _l('status'); ?></th>
               </tr>
            </thead>
            <tbody>
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
               </tr>
            </tbody>
          
         </table>
                       </div>
                   </div>
  
</div>
    <div role="tabpanel" class="tab-pane" id="tab_content_grid">
                 <div class="row ">
               <div class="col-md-12">
                   <div class="row">
                <div class="col-sm-4">
               <div class="form-group has-search">
                <input type="text" id="searchc_" name="search_pc"  class="form-control" placeholder="Search <?php echo _l('contract') ?>">
               </div>
                </div>

                 <div class="col-md-2">
                    <select class="form-control selectpicker" id="contract_type" name="contract_type" >
                        <option value=" ">Contract Type</option>
                        <?php foreach($contract_types as $case_type){ ?>
                            <option value="<?=$case_type['id']?>"><?=$case_type['name']?></option>
                        <?php } ?>
                    </select>
                </div>
             
                <div class="col-md-2">
                     <div id="total_contracts"></div>
                </div>
            </div>
            <div class="row">
                <div class="no_result hide mleft30"><h5>No result found..</h5></div>
               
                </div>
                
                <div id="div_ajax_contract"></div>

                <div class="col-md-12" > 
                 <div align="right" id="pagination_linkc"></div>
                </div>  

               </div><!-- col-12 -->

               </div><!-- row --> 
				 </div>
			   </div>
			   </div></div>


           </div>
<!------- Hearing ------------------------------------------------------------------>
           <div id="menu2" class="tab-pane fade">
             <hr>
             <div class="row">
         
         <div class="col-md-2">
            <div class="form-group">
                <?php  $hearing_types =  get_project_instances(); ?>
                <?php echo render_select('hearing_type',$hearing_types,array('id','name'),'hearing_type','');?>
            </div>
         </div>

          <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('case_id',$projects_,array('id',array('name','file_no')),'projects','');?>
            </div>
         </div>

         <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('client_idh',$clients_,array('userid','company'),'customers','');?>
            </div>
         </div>
     
         <?php echo form_hidden('months-report','custom'); ?>
         <div class="col-md-2">
          <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
            <?php $beginMonth = date('01/m/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-from" name="report-from" value="<?=$beginMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-to" name="report-to" value="<?=$endMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
        <div class="col-md-2">

            <div class="form-group">

              <?php  
					$mention_hearings=get_hearing_mention();
                   
               echo render_select('mention_hearingh',$mention_hearings,array('id','name'),'mention_hearing');?>

            </div>

         </div>
		<div class="col-md-2 mtop5 mot5">
            <div class="checkbox ">
                     <input type="checkbox" id="exclude_unattend_h" name="exclude_unattend_h">
                     <label for="exclude_inactive"><?php echo _l('unattended_hearings'); ?> </label>
                  </div>
   
        </div>
         <div class="clearfix"></div>
      </div> 
             <table class="table dt-table table-matter-hearing-report scroll-responsive">
            <thead>
               <tr>
                  <th><?php echo _l('hearing_date'); ?></th>
                  <th><?php echo _l('hearing_list_subject'); ?></th>
                  <th><?php echo _l('hearing_type'); ?></th>
                  <th><?php echo _l('hearing_no'); ?></th>
                  <th><?php echo _l('client');?></th>
                  <th><?php echo _l('casediary_casenumber'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                  <th><?php echo _l('casediary_hallnumber'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>
                  <th><?php echo _l('proceedings'); ?></th>
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
<!------- execution ------------------------------------------------------------------>
           <div id="menu2ex" class="tab-pane fade">
             <hr>
             <div class="row">
         
         

          <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('case_id',$projects_,array('id',array('name','file_no')),'projects','');?>
            </div>
         </div>

         <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('client_id5',$clients_,array('userid','company'),'customers','');?>
            </div>
         </div>
     
         <?php echo form_hidden('months-reportex','custom'); ?>
         <div class="col-md-2">
          <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
            <?php $beginMonth = date('01/m/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-from" name="report-fromex" value="<?=$beginMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-to" name="report-toex" value="<?=$endMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       
         <div class="clearfix"></div>
      </div> 
  <table class="table table-matter-execution-report scroll-responsive">
            <thead>
               <tr>
                    <th class="not_sortable">Sr. No.</th>
                    <th><?php echo _l('client');?></th>
                     <th><?php echo _l('ledger_code'); ?></th>
                   <th><?php echo _l('case_title'); ?></th>
                   <!--  <th><?php echo _l('law_firm'); ?></th>
                 <th><?php echo _l('legal_coordinator'); ?></th>-->
                  <th><?php echo _l('lawyer_name'); ?></th>
                  <th><?php echo _l('case_no'); ?></th>
                    
                  <th><?php echo _l('hearing_court'); ?></th>
                 <th><?php echo _l('claiming_amount'); ?></th>
                   <th><?php echo _l('court_expenses'); ?></th>
                    <th><?php echo _l('total_amount'); ?></th>
                    <th><?php echo _l('judgement_amount'); ?></th>
                     <th><?php echo _l('execution_amount'); ?></th>
                       <th><?php echo _l('settlement_amount'); ?></th>
                         <th><?php echo _l('amount_received'); ?></th>
                    <th><?php echo _l('balance'); ?></th>
                   <!--  <th><?php echo _l('status'); ?></th>
                     <th><?php echo _l('project_court_attach'); ?></th>-->
                    <th  width="25%" class="not_visible"><?php echo _l('case_update'); ?></th>
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
                 
				 <!-- <td></td>
                 <td></td>-->
                  </tr>
            </tfoot>
         </table>
           </div>
<!------- Judgement ------------------------------------------------------------------>
           <div id="menu_judge" class="tab-pane fade">
             <hr>
             <div class="row">
         
         

          <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('case_idjudg',$projects_,array('id',array('name','file_no')),'projects','');?>
            </div>
         </div>

         <div class="col-md-2">
            <div class="form-group">
                <?php echo render_select('client_idjudg',$clients_,array('userid','company'),'customers','');?>
            </div>
         </div>
     
         <?php echo form_hidden('months-reportjudg','custom'); ?>
         <div class="col-md-2">
          <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
            <?php $beginMonth = date('01/m/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-from" name="report-fromjudg" value="<?=$beginMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-to" name="report-tojudg" value="<?=$endMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       
         <div class="clearfix"></div>
      </div> 
  <table class="table table-matter-judgement-report scroll-responsive">
            <thead>
               <tr>
                    <th class="not_sortable"><?php echo _l('sl_no');?></th>
                    <th><?php echo _l('client');?></th>
				   <th><?php echo _l('opposite_party');?></th>
                   <th><?php echo _l('case_title'); ?></th>
				   <th><?php echo _l('casediary_casenumber'); ?></th>
                  <th><?php echo _l('judge_date'); ?></th>
                 <th><?php echo _l('project_stage'); ?></th>
                  <th><?php echo _l('current_stage_outcome'); ?></th>
                  <th><?php echo _l('directions'); ?></th>
                  <th><?php echo _l('attachment'); ?></th>
                
                  
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
                                   
				 <!-- <td></td>
                 <td></td>-->
                  </tr>
            </tfoot>
         </table>
           </div>
      <!---- Legal Reviews------------------------>
      <div id="menu3" class="tab-pane fade <?php if(!has_permission('customers','','view') && $confirmapproval=='client') echo 'in active';?>">
             <hr>
             <div class="row ">
                  
         <div class="col-md-3">
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
         
           <div class="col-md-2 pull-right mtop20 mbot20"> <?php if (has_permission('contracts','','create')) { ?>
                     <a  href="<?php echo admin_url('tickets/add'); ?>" class="btn btn-info mright5 test pull-right display-block">
                     <?php echo _l('new_ticket'); ?></a> <?php } ?></div>

                <div class="clearfix"></div>
            </div> 
            <?php $this->load->view('admin/reports/includes/legalrequests_report_table_html'); ?>

            </div>
           
      <!------------------------------------------>  
            <!---- Opposite party------------------------>
            <div id="menu4" class="tab-pane fade">
                         <div class="row">
            
         <div class="col-md-12 mtop10">
             <div class="clearfix"></div>
             <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tab_content_table11" aria-controls="tab_content" role="tab" data-toggle="tab" style="color: #098326;font-weight: bold;font-size:14px;">
              <?php echo _l('default_view'); ?>
            </a>
          </li>
          <li role="presentation">
            <a href="#tab_content_grid11" aria-controls="tab_grid" role="tab" data-toggle="tab" style="color: #312994;font-weight: bold;font-size:14px;">
              <?php echo _l('gridview'); ?>
            </a>
          </li>
           </ul>
           <div class="col-md-2 pull-right">
                     <a href="<?php echo admin_url('opposite_parties/opposite_party') ?>"  class="btn btn-info pull-right display-block"><?php echo _l('new_opposite_party'); ?></a>
                </div>
   
             <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="tab_content_table11">
                                         <div class="row">
                 
         <?php echo form_hidden('months-reportc2','custom'); ?>
         <div class="col-md-2">
          <label for="report-fromc" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
              <?php $beginMonth = date('01/01/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-fromc2" name="report-fromc2" value="">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-toc2" name="report-toc2" value="">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
		
         <div class="clearfix"></div>
      </div> 
            <div class="panel_s mtop25">
                        <div class="panel-body">
                                  <table class="table table-opposites-report table-striped scroll-responsive" id="tabledash">
            <thead>
               <tr>
                  <th class="not_sortable"><?php echo _l('#'); ?></th>
                  <th><?php echo _l('opposite_company'); ?></th>
                  <th><?php echo _l('firstname'); ?></th>
                   <th><?php echo _l('lastname'); ?></th>
                   <th><?php echo _l('email'); ?></th>
                  <th><?php echo _l('mobile'); ?></th>
                  <th><?php echo _l('city'); ?></th>
                  <th><?php echo _l('no_of_contracts'); ?></th>
               </tr>
            </thead>
            <tbody><tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
               </tr></tbody>
           
         </table>
                       </div>
                   </div>
		</div>
             <div role="tabpanel" class="tab-pane" id="tab_content_grid11">
                 <div class="row">

                <div class="col-sm-6">
                   <div class="form-group has-search">
                <input type="text" id="search_3"  class="form-control" placeholder="<?php echo _l('search'); ?> <?php echo _l('opposite_parties') ?>">
               </div>
                </div> 
                <div class="col-md-4 mtop10 hide">
                   
                </div>
                <div class="col-md-2"><div id="total_oppositeparties" ></div></div>
               


                <div class="no_result2 hide"><h5>No result found..</h5></div>
                
                </div>

                <div class="row">
                <div class="col-md-12">    
                     <div id="div_ajax_opposite_party"></div>

                <div class="col-md-12" > 
                 <div align="right" id="pagination_link2"></div>
                </div>  

               </div><!-- col-12 -->

               </div><!-- row --> 
				 </div>
			 </div>
            </div>
			</div>
               
            </div>   
             <!-------------------------------->
                                    <!---- Bosco Summary------------------------>
            <div id="menu7" class="tab-pane fade">
                <hr>
                 <div class="row">
             
                <?php echo form_hidden('months-report','custom'); ?>
         <div class="col-md-2">
          <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
            <?php $beginMonth = date('01/m/Y');
                    $endMonth   = date('t/m/Y'); ?>
             <input type="text" class="form-control datepicker" id="report-from1" name="report-from1" value="<?=$beginMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-to1" name="report-to1" value="<?=$endMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>  <div class="clearfix"></div><br>

         <div class="clearfix"></div>
                <div class="col-md-4 mtop10 hide">
                   
                </div>
               
                
                </div>

                <div class="row">
                <div class="col-md-12">    
                     <div id="div_ajax_summary_party"></div>

                <div class="col-md-12" > 
                
                </div>  

               </div><!-- col-12 -->

               </div><!-- row --> 
                
            </div>  
                  <!---- Legal Approvaal------------------------>
      <div id="menu5" class="tab-pane fade <?php if($confirmapproval=='legal') echo 'in active';?>">
             <hr>
    
            <?php $this->load->view('admin/reports/includes/legalapprovals_report_table_html'); ?>

            </div>
                          <!---- Legal Approvaal------------------------>
      <div id="menu6" class="tab-pane fade <?php if(is_approver() && $confirmapproval=='contract') echo 'in active';?>">
             <hr>
    
            <?php $this->load->view('admin/reports/includes/contractapprovals_report_table_html'); ?>

            </div>
		<!-- Contract Activity---------------->
			 <div id="menuact" class="tab-pane fade <?php if(is_approver() && $confirmapproval=='contract') echo 'in active';?>">
             <hr>
    
            <?php $this->load->view('admin/reports/includes/contractactivity_report_table_html'); ?>

            </div>
         </div>
      </div>
   </div>
</div>
