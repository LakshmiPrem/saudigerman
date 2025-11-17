<?php// if(has_permission('customers') && has_permission('projects')){ ?>

<div class="row" style="margin-left:0px; margin-right: 0px;"> 
   <div class="panel_s">
      <div class="panel-body">
         <ul class="nav nav-pills">
             <li class="active" style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#home"><?php echo _l('customers') ?></a></li>
             <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu1" onclick="load_project_data(1); return false;"><?php echo _l('projects') ?></a></li>
             <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu2" onclick="matter_hearing_report();return false;"><?php echo _l('hearings') ?></a></li>
             <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu3" onclick="init_table_tickets(true);return false;"><?php echo _l('legal_reviews') ?></a></li>
              <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu4" onclick="load_oppositeparty_data(1);return false;"><?php echo _l('opposite_parties') ?></a></li>
               <li style="background-color: #a5dee673;border: #77c6e1 1px solid;border-radius: 6px;"><a data-toggle="pill" href="#menu5" onclick="load_summary_data();return false;"><?php echo _l('total_summary') ?></a></li>
         </ul>

         <div class="tab-content">
<!------- Clients ------------------------------------------------------------------>

           <div id="home" class="tab-pane fade in active">
            <hr>
             <div class="row">
               <div class="col-md-12">
                  <div class="row">
                <div class="col-sm-6">
                   <div class="form-group has-search">
                <input type="text" id="search_2"  class="form-control" placeholder="Search <?php echo _l('customers') ?>">
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
<!------- Case ------------------------------------------------------------------>
           <div id="menu1" class="tab-pane fade">
             <hr>

             <div class="row ">
               <div class="col-md-12">
                   <div class="row">
                <div class="col-sm-4">
               <div class="form-group has-search">
                <input type="text" id="search_" name="search_p"  class="form-control" placeholder="Search <?php echo _l('project') ?>">
               </div>
                </div>

                 <div class="col-md-2">
                    <select class="form-control selectpicker" id="case_type" name="case_type" >
                        <option value=" ">Case Type</option>
                        <?php foreach($case_types as $case_type){ ?>
                            <option value="<?=$case_type['id']?>"><?=$case_type['name']?></option>
                        <?php } ?>
                    </select>
                </div>
                 <div class="col-md-2">
                    <select class="form-control selectpicker" id="c_status" name="status" >
                        <option value="">Status</option>
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

               </div><!-- row --> 

           </div>
<!------- Leads ------------------------------------------------------------------>

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
                <?php echo render_select('client_id',$clients_,array('userid','company'),'customers','');?>
            </div>
         </div>
         
         <?php echo form_hidden('months-report','custom'); ?>
         <div class="col-md-2">
          <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
            <?php $beginMonth = date('Y-m-01');
                    $endMonth   = date('Y-m-t'); ?>
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

      <!---- Legal Reviews------------------------>
      <div id="menu3" class="tab-pane fade">
             <hr>
             <div class="row ">
                   <div class="_buttons">
          
            <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
              <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fa fa-filter" aria-hidden="true"></i>
              </button>
              <ul class="dropdown-menu dropdown-menu-right width300">
                <li>
                  <a href="#" data-cview="all" onclick="dt_custom_view('','.tickets-table',''); return false;">
                    <?php echo _l('task_list_all'); ?>
                  </a>
                </li>
                <li>
                  <a href="" data-cview="my_tickets" onclick="dt_custom_view('my_tickets','.tickets-table','my_tickets'); return false;"><?php echo _l('my_tickets_assigned'); ?></a>
                </li>
                <li class="divider"></li>
                <?php foreach($statuses as $status){ ?>
                <li class="<?php if($status['ticketstatusid'] == $chosen_ticket_status || $chosen_ticket_status == '' && in_array($status['ticketstatusid'], $default_tickets_list_statuses)){echo 'active';} ?>">
                  <a href="#" data-cview="ticket_status_<?php echo $status['ticketstatusid']; ?>" onclick="dt_custom_view('ticket_status_<?php echo $status['ticketstatusid']; ?>','.tickets-table','ticket_status_<?php echo $status['ticketstatusid']; ?>'); return false;">
                    <?php echo ticket_status_translate($status['ticketstatusid']); ?>
                  </a>
                </li>
                <?php } ?>
               
                              
               <?php if(count($ticket_services) > 0 && is_admin()){ ?>
                <div class="clearfix"></div>
                <li class="divider"></li>
                <li class="dropdown-submenu pull-left">
                 <a href="#" tabindex="-1"><?php echo _l('filter_by_services'); ?></a>
                 <ul class="dropdown-menu dropdown-menu-left">
                  <?php foreach($ticket_services as $as){ ?>
                  <li>
                    <a href="#" data-cview="ticket_services_<?php echo $as['serviceid']; ?>" onclick="dt_custom_view(<?php echo $as['serviceid']; ?>,'.tickets-table','ticket_services_<?php echo $as['serviceid']; ?>'); return false;"><?php echo get_ticket_servicename($as['serviceid']); ?></a>
                  </li>
                  <?php } ?>
                </ul>
              </li>
              <?php } ?>


    <div class="clearfix"></div>
            </ul>
          </div>
        </div>
         
<?php hooks()->do_action('before_render_tickets_list_table'); ?>
        <?php $this->load->view('admin/tickets/summary'); ?>
                <div class="clearfix"></div>
            </div> 
              <?php echo AdminTicketsTableStructure('', false); ?>
           </div>
           
      <!------------------------------------------>  
            <!---- Opposite party------------------------>
            <div id="menu4" class="tab-pane fade">
                <hr>
                 <div class="row">
                <div class="col-sm-6">
                   <div class="form-group has-search">
                <input type="text" id="search_3"  class="form-control" placeholder="Search <?php echo _l('opposite_parties') ?>">
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
             <!-------------------------------->
                      <!---- Bosco Summary------------------------>
            <div id="menu5" class="tab-pane fade">
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
             <!-------------------------------->
         </div>
      </div>
   </div>
   

</div>
<?php// } ?>
