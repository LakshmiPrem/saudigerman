
<!--div class="row">
      <div class="col-md-12"-->
         <!-- <div class="panel">
            <div class="panel-body"> -->
            <!-- <div class="row">
      <div class="col-md-12"> -->
              <?php #################Clients######################## 
                  $_where = 'duedate IS NOT NULL AND duedate != " " AND status != '.Tasks_model::STATUS_COMPLETE;
                  if (!has_permission('tasks', '', 'view')) {
                    $_where .= ' AND '.db_prefix().'tasks.id IN (SELECT taskid FROM '.db_prefix().'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';
                  }
                 
                  $tasks_list  = $this->db->order_by('duedate','asc')->limit(5)->select('id,name,status,startdate,duedate')->from('tbltasks')->where($_where)->get()->result_array();
                  $todays_tasks_count  = $this->db->from('tbltasks')->where($_where)->count_all_results();

                    
                ?>
              <div class="col-md-4 <?php if(!in_array(4, $active_boxes)) echo 'hide';  ?>">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-tasks fa-lg" aria-hidden="true"></i> <?php echo _l('tasks') ?> <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $todays_tasks_count ?></a></div>
                    <div class="panel-body alen-panel" >
                      <ul class="list-group">
                        <?php 
                          if(sizeof($tasks_list) > 0){ 
                            foreach ($tasks_list as $key => $value) { 
                               $status          = get_task_status_by_id($value['status']);
                              $outputStatus    = '';
                              $outputStatus .= '<span class="inline-block label" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '" task-status-table="' . $value['status'] . '">';
                              $outputStatus .= $status['name'];
                              $outputStatus .= '</span>';

                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                            
                              
                              <a  href="javascript:void(0);" onclick="init_task_modal('<?php echo $value['id'] ?>'); return false;"><?php echo $value['name']; ?></a>
                              <span class="pull-right"><?php echo $outputStatus; ?></span>
                              <p style="margin:0 0 2px;" ><span class="text-default"> <?php echo _l('task_add_edit_start_date') ?>  : <?php echo date('Y M d',strtotime($value['startdate'])); ?> </span> </p>
                              <p style="margin:0 0 12px;" > <?php echo _l('task_add_edit_due_date') ?> : <span class="text-danger"> <?php echo date('Y M d',strtotime($value['duedate'])); ?></span></p>
                               
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li">
                               <p><?php echo _l('no_data_found'); ?> <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
                               
                            </li>
                            <li class="list-group-item li_new_button">
                              <a onclick="new_task();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> <?php echo _l('new_task') ?></a>
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>
                      
                     
                      
                      
                    </div>

                  <div class="panel-footer panel-footer-height">
                    <span class="" > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('tasks') ?>"><?php echo _l('view_all_tasks'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span>
                  </div>


                    
                </div>

              </div>   

             <?php 
           ############   Box 2 in first row - show hearing data, Hearing Booked /Not Booked ################# ?>
              <?php 
              $_where = 'DATE(hearing_date) BETWEEN NOW() AND DATE_ADD(NOW(), INTERVAL 7 DAY) ';
              if (!has_permission('projects', '', 'view')) {
                  $_where .= ' AND project_id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
                  
              }
             
              $hearings_list      = $this->db->order_by('DATE(hearing_date)','desc')->limit(5)->select('tblhearings.id as id,hearing_date,postponed_until,project_id,subject,proceedings,court_no,case_type,clientid,court_no as case_number')->from('tblhearings')->join('tblprojects','tblprojects.id = tblhearings.project_id','inner')->where($_where)->get()->result_array();
              $next_week_hearings_count  = $this->db->from('tblhearings')->where($_where)->count_all_results();
      
               ?>
              <div class="col-md-4 <?php if(!in_array(11, $active_boxes)) echo 'hide';  ?>">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-calendar-plus-o fa-lg" aria-hidden="true"></i> <?php echo _l('next_week').' '._l('hearings'); ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $next_week_hearings_count ?></a></div>
                  <div class="panel-body alen-panel" >
                    <ul class="list-group">
                        <?php 
                          if(sizeof($hearings_list) > 0){ 
                            foreach ($hearings_list as $key => $value) { 
                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                            
                              <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Hearing Date"><?php echo date('Y M d',strtotime($value['hearing_date'])); ?><?php //echo $value['phonenumber']; ?></span>
                              <a  href="#" onclick="init_hearing(<?php echo $value['id']?>);return false;"><?php echo $value['subject']; ?></a>
                              <p style="margin:0 0 5px;"><?php echo get_project_name_by_id($value['project_id']); ?></p>
                              <!-- <p style="margin:0 0 5px;"><?php echo get_company_name($value['clientid']); ?></p> -->
                              <p style="margin:0 0 5px;"><?php echo _l('casediary_casenumber') ?>: <strong><?php echo $value['case_number']; ?></strong> | <strong><?php echo _l($value['case_type']);?></strong></p>
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li">
                               <p><?php echo _l('no_data_found'); ?> <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
                               
                            </li>
                            <li class="list-group-item li_new_button">
                             <!--  <a onclick="init_hearing();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> <?php echo _l('new_hearing') ?></a> -->
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>
                      
                     
                  </div>
                  <div class="panel-footer panel-footer-height">
                      <span class="" > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('hearings') ?>"><?php echo _l('view_all_hearings'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span>
                    </div>

                </div>
              </div>   

        <?php ################## Box 2 end ####################################################### ?>       

        <?php #################My Reminders######################## 

              $_where = ' rel_type != "project" AND rel_type != "hearing" AND staff =  '.get_staff_user_id().' ';

              $my_reminders  = $this->db->order_by('id','desc')->limit(5)->select('*')->from('tblreminders')->where($_where)->get()->result_array(); 
              $my_reminders_count  = $this->db->from('tblreminders')->where($_where)->count_all_results();
     
                ?>
              <div class="col-md-4 <?php if(!in_array(5, $active_boxes)) echo 'hide';  ?> ">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> <?php echo _l('my').' '._l('reminders') ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $my_reminders_count ?></a> </div>
                    <div class="panel-body alen-panel" >

                      <ul class="list-group">
                        <?php 
                          if(sizeof($my_reminders) > 0){  
                            foreach ($my_reminders as $key => $value) { 
                              $rel_data   = get_relation_data($value['rel_type'], $value['rel_id']);
                              $rel_values = get_relation_values($rel_data, $value['rel_type']);
                              $_data      = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                              
                              <span class="badge badge-dashboard " data-toggle="tooltip" data-placement="top" title="Reminder Date"><?php echo date('Y M d',strtotime($value['date'])); ?><?php //echo $value['phonenumber']; ?></span>
                              <p ><?php echo $value['description']; ?></p>

                              <?php echo $_data; ?>
                              
                              
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li">
                              <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o" aria-hidden="true"></i></p>
                            </li>
                            <li class="list-group-item li_new_button">
                              <a onclick="new_quick_reminder();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> <?php echo _l('reminder') ?></a>
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>
                     
                      
                    </div>
                    <div class="panel-footer panel-footer-height">
                       <span class="" > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('misc/reminders') ?>"><?php echo _l('view_all_reminders'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span> 
                    </div>


                    <!-- Table -->
                     
                     <!-- <div class="panel-footer">Panel footer</div> -->
                </div>

              </div>  
              <!-- </div>

</div>   -->

                 
                
      <!--/div>
   </div-->   

