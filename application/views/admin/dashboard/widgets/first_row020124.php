
<div class="row">
      <div class="col-md-12">
        
         <!-- <div class="panel">
            <div class="panel-body"> -->
              <?php #################Clients######################## ?>
              <?php //if( has_permission('contracts','','view')){ 

                 $where = 'signed=0 or marked_as_signed=0';
                 //}  
                 if(!has_permission('contracts','','view')){
                    $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
                 }
                 
                 $contract_list  = $this->db->order_by('tblcontracts.id','desc')->limit(5)->group_by('tblcontracts.id')->select('tblcontracts.id,tblcontracts.other_party,tblclients.company,tblcontracts.datestart,tblcontracts.dateend,tblcontracts.subject,tblcontracts.dateadded,tblcontracts.contract_value,tblcontracts.final_expiry_date')->from('tblcontracts')->where($where)->join('tblprojects','tblprojects.clientid = tblcontracts.project_id','left')->join('tblclients','tblclients.userid = tblcontracts.client','left')->get()->result_array();
                 $contracts_count  = $this->db->from('tblcontracts')->where($where,NULL,FALSE)->count_all_results();
                   
                ?>
              <div class="col-md-4 <?php if(!in_array(1, $active_boxes)) echo 'hide';  ?>">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading" ><i class="fa fa-users fa-lg"></i> <?php echo _l('unsigned_contracts') ?> <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $contracts_count ?></a>
                  </div>
                    <div class="panel-body alen-panel" >
                      
                      <ul class="list-group alen-ul" style="margin-bottom: 10px;" >
                        <?php 
                          if(sizeof($contract_list) > 0){ $j= 1;
                            foreach ($contract_list as $key => $value) { 
                            
                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                              
                              
                              <?php if(date($value['dateend'])!="0000-00-00" && $value['dateend']!=NULL){?><span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Contract End Date"><?php echo date($value['dateend']); ?></span><?php } ?>
                               
                               <a  href="<?php echo admin_url('contracts/contract/'.$value['id']); ?>"><?php echo $value['subject']; ?></a>
                              <p class="alen-p" style="margin:0 0 5px;"><?php echo $value['contract_value']; ?> 
                              <p style="margin:0 0 2px;" ><span class="text-default"> <?php echo _l('start_date') ?>  : <?php echo date($value['datestart']); ?> </span> </p>
                              
                              <a  href="<?php echo admin_url('opposite_parties/opposite_party/'.$value['other_party']); ?>"><?php echo get_opposite_party_name($value['other_party']); ?></a>
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li" >
                              <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
                              
                            </li>
                            <li class="list-group-item li_new_button">
                               <a  href="<?php echo admin_url('contracts?filter=unsigned'); ?>" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> <?php echo _l('new_contract') ?></a>
                              
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>
                       
                  
                </div>
                <div class="panel-footer panel-footer-height">
                  <span class=" " > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('contracts') ?>"><?php echo _l('view_contracts'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span>
                </div>

              </div>   
            </div>
              

             <?php 
           ############   Box 2 in first row - show hearing data, Hearing Booked /Not Booked ################# ?>
              <?php 
              // Booked Hearings  - Select past Hearings which has  postponed date //postponed_until IS NOT NULL AND postponed_until != "0000-00-00"
              
              // Not Booked Hearings  - Select past Hearings which is not  postponted 
              $_where = 'DATE(hearing_date) <= "'.date('Y-m-d').'" AND postponed="n" ';
              if (!has_permission('projects', '', 'view')) {
                if(total_rows('tblproject_members',['staff_id'=>get_staff_user_id()]) > 0){
                        $_where .= ' AND  project_id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
                }  
              }
              //office
              if(!is_admin() && get_option('enable_office')==1 && has_permission('projects','','view')){ 
                $_where .= 'AND project_id IN (SELECT id FROM tblprojects WHERE tblprojects.company_entity='.get_office_id().')';
              }
             //office
              $not_booked_hearings_list  = $this->db->order_by('DATE(hearing_date)','desc')->limit(5)->select('tblhearings.id as id,hearing_date,postponed_until,project_id,subject,proceedings,court_no,case_type,clientid,court_no as case_number')->from('tblhearings')->join('tblprojects','tblprojects.id = tblhearings.project_id','inner')->where($_where)->get()->result_array();  
              $total_hearings_count     = $this->db->from('tblhearings')->where($_where)->count_all_results();

              
               ?>
              <div class="col-md-4 <?php if(!in_array(2, $active_boxes)) echo 'hide';  ?>">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-th-large fa-lg"></i>  <?php echo _l('decisions'); ?>  <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $total_hearings_count ?></a></div>
                  <div class="panel-body alen-panel" >
                         <ul class="list-group mt-2" style="margin-bottom: 12px;">
                        <?php 
                          if(sizeof($not_booked_hearings_list) > 0){ 
                            foreach ($not_booked_hearings_list as $key => $value) { 
                            
                           ?>
                            <li class="list-group-item color_<?php echo date('D',strtotime($value['hearing_date'])) ?>">
                              <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Hearing Date" title="<?php echo _l('hearing_date'); ?>"><?php echo date('Y M d , D',strtotime($value['hearing_date'])); ?></span>
                              <button type="button" class="btn btn-default btn-sm btn-icon   pop" data-container="body" data-toggle="popover" data-html="true"  data-placement="bottom" data-content="<?php echo date('Y M d ',strtotime($value['hearing_date'])).'<hr>'.$value['proceedings']; ?>"data-original-title="<?php echo date('Y M d ',strtotime($value['hearing_date'])); ?>" title="<?php echo _l('proceedings') ?>"> <i class="fa fa-tag"></i></button>
                              <a  onclick="init_hearing(<?php echo $value['id']?>);return false;" href="#"><?php echo $value['subject']; ?></a>
                              <p style="margin:0 0 5px;"><a href="<?php echo admin_url('projects/view/'.$value['project_id']) ?>"><?php echo get_project_name_by_id($value['project_id']); ?></a></p>
                              <!-- <p style="margin:0 0 5px;"><?php echo get_company_name($value['clientid']); ?></p> -->
                              <p style="margin:0 0 5px;"><?php echo _l('casediary_casenumber') ?>: <strong><a href="<?php echo admin_url('projects/view/'.$value['project_id']) ?>"><?php echo $value['case_number']; ?></a></strong> | <strong><?php echo _l($value['case_type']);?></strong></p>
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li">
                               <p><?php echo _l('no_data_found'); ?> <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
                               
                            </li>
                            <li class="list-group-item li_new_button">
                              <!-- <a onclick="init_hearing();return false;" href="javascript:void(0);" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> <?php echo _l('new_hearing') ?></a> -->
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>
                       

                     
                    </div>
                    <div class="panel-footer panel-footer-height">
                      <span class="" > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('hearings/hearing?filter=without_next_session') ?>"><?php echo _l('view_all_hearings'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span>
                    </div>
                </div>
              </div>   

         <?php #################################################### ?>
                  
<!--div class="row">
      <div class="col-md-12"-->
      <!-- <div class="row">
      <div class="col-md-12"> -->
              <?php #################My Reminders######################## 

              //if( has_permission('contracts','','view')){ 
              $type='"contract"';
              $where = 'rel_type='.$type.' AND approval_status=2 AND staffid='.get_staff_user_id();
              //}  
              // if(!has_permission('contracts','','view')){
              //   $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
              // }
              

              $my_approvals  = $this->db->order_by('id','desc')->limit(5)->select('tblapprovals.id,tblapprovals.rel_id,tblapprovals.approval_type,tblapprovals.approval_name,tblapprovals.dateadded,tblapprovals.approval_remarks,tblcontracts.subject')->from('tblapprovals')->where($where)->join('tblcontracts','tblcontracts.id = tblapprovals.rel_id','left')->get()->result_array(); 
              $my_approvals_count  = $this->db->from('tblapprovals')->where($where)->count_all_results();
     
                ?>
              <div class="col-md-4 <?php if(!in_array(9, $active_boxes)) echo 'hide';  ?> ">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> <?php echo _l('my').' '._l('contract_approvals') ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $my_approvals_count ?></a> </div>
                    <div class="panel-body alen-panel">

                      <ul class="list-group">
                        <?php 
                          if(sizeof($my_approvals) > 0){  
                            foreach ($my_approvals as $key => $value) { 
                              //$rel_data   = get_relation_data($value['rel_type'], $value['rel_id']);
                              //$rel_values = get_relation_values($rel_data, $value['rel_type']);
                              //$_data      = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                              
                              <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Approval Date Added"><?php echo date('Y M d',strtotime($value['dateadded'])); ?><?php //echo $value['phonenumber']; ?></span>
                              <a  href="<?php echo admin_url('contracts/contract/'.$value['rel_id'].'?tab=approvals') ?>" onclick="#"><?php echo $value['approval_name']; ?></a><br>
                              <a  href="<?php echo admin_url('contracts/contract/'.$value['rel_id']) ?>" onclick="#"><?php echo $value['subject']; ?></a>
                              <p ><?php echo $value['approval_remarks']; ?></p>

                              
                              
                              
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li">
                              <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o" aria-hidden="true"></i></p>
                            </li>
          
                          <?php 
                         } ?>
                        
                      </ul>
                     
                      
                    </div>
                    <div class="panel-footer panel-footer-height">
                       <span class="" > 
                        <a class="btn btn-link btn-sm" style="" target="_blank"  href="#"></a>
                        
                       </span> 
                    </div>


                    <!-- Table -->
                     
                     <!-- <div class="panel-footer">Panel footer</div> -->
                </div>

              </div>   

             <?php 
           ############   Box 2 Papers to be signed ################# ?>
              <?php 
            
              //if( has_permission('contracts','','view')){ 
                $type='"ticket"';
                $where = 'rel_type='.$type.' AND approval_status=2 AND staffid='.get_staff_user_id();
                //}  
                // if(!has_permission('contracts','','view')){
                //   $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
                // }
                
  
                $my_legal_approvals  = $this->db->order_by('id','desc')->limit(5)->select('id,rel_id,rel_type,approval_type,staffid,approval_name,dateadded,approve_expectdt,approval_remarks')->from('tblapprovals')->where($where)->get()->result_array(); 
                $my_legal_approvals_count  = $this->db->from('tblapprovals')->where($where)->count_all_results();
       
                  ?>
                <div class="col-md-4 <?php if(!in_array(10, $active_boxes)) echo 'hide';  ?> ">
                  <div class="panel panel-default">
                    <!-- Default panel contents -->
                    <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> <?php echo _l('my').' '._l('legal_request_approvals') ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $my_legal_approvals_count ?></a> </div>
                      <div class="panel-body alen-panel">
  
                        <ul class="list-group">
                          <?php 
                            if(sizeof($my_legal_approvals) > 0){  
                              foreach ($my_legal_approvals as $key => $value) { 
                                $rel_data   = get_relation_data($value['rel_type'], $value['rel_id']);
                                $rel_values = get_relation_values($rel_data, $value['rel_type']);
                                $_data      = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                             ?>
                              <li class="list-group-item <?php //echo $li_class; ?>">
                                
                                <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Legal Request Approval Added Date"><?php echo date('Y M d',strtotime($value['dateadded'])); ?><?php //echo $value['phonenumber']; ?></span>
                                <a  href="<?php echo admin_url('tickets/ticket/'.$value['rel_id'].'?tab=approvals') ?>" onclick="#"><?php echo $value['approval_name']; ?></a><br>
                                <p ><?php echo $value['approval_remarks']; ?></p>
                                <p ><?php echo $_data;?></p>
                                <?php if($value['approve_expectdt']!='' || $value['approve_expectdt']!=NULL){ ?>
                                  <p ><?php echo _l('approval_expected_date') ?>:<?php echo date($value['approve_expectdt']); ?></p>  
                               <?php  } ?>  
                              </li>
                              <?php }
                           }else{ ?>
                              <li class="list-group-item center_li">
                                <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o" aria-hidden="true"></i></p>
                              </li>
            
                            <?php 
                           } ?>
                          
                        </ul>
                       
                        
                      </div>
                      <div class="panel-footer panel-footer-height">
                         <span class="" > 
                          <a class="btn btn-link btn-sm " style="" target="_blank"  href="#"><p class="hide"><?php echo _l('view_all_reminders'); ?></p></a>
                         </span> 
                      </div>
  
  
                      <!-- Table -->
                       
                       <!-- <div class="panel-footer">Panel footer</div> -->
                  </div>
  
                </div>    
            <?php ?>
        <?php ################## Box 2 end ####################################################### ?>       
        
        <?php ################## Box 3 start ####################################################### ?>      
              <?php //if( has_permission('contracts','','view')){ 

                 $where = 'marked_as_signed=1 OR (signed=1 AND party_signed=1)';
                 //$where = 'signed=0 or marked_as_signed=0';
                 //}  
                 if(!has_permission('contracts','','view')){
                    $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
                 }
                 
                 $contract_list  = $this->db->order_by('tblcontracts.id','desc')->limit(5)->group_by('tblcontracts.id')->select('tblcontracts.id,tblcontracts.other_party,tblclients.company,tblcontracts.datestart,tblcontracts.dateend,tblcontracts.subject,tblcontracts.dateadded,tblcontracts.contract_value,tblcontracts.final_expiry_date,tblcontracts.acceptance_date,tblcontracts.signed_contract_filename')->from('tblcontracts')->where($where)->join('tblprojects','tblprojects.clientid = tblcontracts.project_id','left')->join('tblclients','tblclients.userid = tblcontracts.client','left')->get()->result_array();
                 $contracts_count  = $this->db->from('tblcontracts')->where($where,NULL,FALSE)->count_all_results();
                   
                ?>
              <div class="col-md-4 <?php if(!in_array(12, $active_boxes)) echo 'hide';  ?>">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading" ><i class="fa fa-users fa-lg"></i> <?php echo _l('signed_contracts') ?> <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $contracts_count ?></a>
                  </div>
                    <div class="panel-body alen-panel" >
                      
                      <ul class="list-group alen-ul" style="margin-bottom: 10px;" >
                        <?php 
                          if(sizeof($contract_list) > 0){ $j= 1;
                            foreach ($contract_list as $key => $value) { 
                              $extension = pathinfo($value['signed_contract_filename'], PATHINFO_EXTENSION);
                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                            <a class="badge" style="background-color: #807B7A;padding: 6px;border-radius: 4px;font-weight: 544;" data-toggle="tooltip" data-placement="top" title="Signed Date" href="#"><?php echo date($value['acceptance_date']); ?></a> 
                            <?php if(has_permission('contracts','','view')){
                              //if($extension=='docx' || $extension=='doc') {  ?>
                            <!-- <a class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Download Signed Document" href="<?php echo site_url('download/downloadsigned_agreement/'.$value['id']); ?>"><?php echo _l('download'); ?></a> -->
                            
                            
                            <?php //} 
                          }?>
                             <a  href="<?php echo admin_url('contracts/contract/'.$value['id']); ?>"><?php echo $value['subject']; ?></a>
                              <p class="alen-p" style="margin:0 0 3px;"><?php echo $value['contract_value']; ?> <br>
                              
                              <a  href="<?php echo admin_url('opposite_parties/opposite_party/'.$value['other_party']); ?>"><?php echo get_opposite_party_name($value['other_party']); ?></a><br>
                              <?php //$file_path   = get_upload_path_by_type('contract').$value['id'].'/'.$value['signed_contract_filename']; ?>
                              <?php 
                              //if($extension=='pdf') {  
                               $file_path   =base_url('uploads/contracts/').$value['id'].'/'.$value['signed_contract_filename']; ?>
                              
                              <!-- <a class="badge" data-toggle="tooltip" data-placement="top" title="View Document" href="<?php echo $file_path; ?>" target="_blank"><?php echo _l('view'); ?></a> -->
                              <a class="badge" data-toggle="tooltip" data-placement="top" title="View Document" href="<?php echo admin_url('contracts/pdf/'.$value['id'].'?output_type=I'); ?>" target="_blank"><?php echo _l('view'); ?></a>
                              <?php //} ?>
                            </li>
                            <?php }
                         }else{ ?>
                            <li class="list-group-item center_li" >
                              <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
                              
                            </li>
                            <li class="list-group-item li_new_button">
                               <a  href="<?php echo admin_url('contracts?filter=signed'); ?>" class="btn btn-info btn-sm  mb-4" ><i class="fa fa-plus"></i> <?php echo _l('new_contract') ?></a>
                              
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>
                       
                  
                </div>
                <div class="panel-footer panel-footer-height">
                  <span class=" " > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('contracts') ?>"><?php echo _l('view_contracts'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span>
                </div>

              </div>   
            </div>
      <?php ################## Box 3 end ####################################################### ?>  

      
         <?php ##################################################### ?>
                                    

        <?php ################## Box 2 end ####################################################### ?>    
              <?php // Judgment Cases
              //$_where = 'tblprojects.id = (SELECT project_id FROM tblcase_details WHERE tblcase_details.project_id = tblprojects.id AND stage_status = 1 ORDER BY id DESC LIMIT 1)';
              $_where = 'current_stage_status = 1';
              if (!has_permission('projects', '', 'view')) {
                  $_where .= ' AND  tblprojects.id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id = ' . get_staff_user_id() . ')';
              }   
                
              //(SELECT tblcase_details.case_number FROM tblcase_details  INNER JOIN tblprojects ON tblprojects.id = tblcase_details.project_id GROUP BY project_id ORDER BY tblcase_details.id DESC LIMIT 1) as case_number        
              $judgment_cases_list  = $this->db->query("SELECT id, name,case_type,clientid,start_date,(SELECT hearing_date FROM tblhearings WHERE tblprojects.id = tblhearings.project_id AND hearing_stage_status = 1 GROUP BY project_id ORDER BY tblhearings.id DESC LIMIT 1) as judgement_date,(SELECT tblhearings.court_no FROM tblhearings  INNER JOIN tblprojects ON tblprojects.id = tblhearings.project_id GROUP BY project_id ORDER BY tblhearings.id DESC LIMIT 1) as case_number FROM tblprojects WHERE    ".$_where."  ORDER BY id DESC LIMIT 5")->result_array(); 
              $total_judgment_cases_count     = $this->db->from('tblprojects')->where($_where,NULL,FALSE)->count_all_results();


              ?>
              <div class="col-md-4 <?php if(!in_array(3, $active_boxes)) echo 'hide';  ?>">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-balance-scale fa-lg"></i>  <?php echo _l('judgment_cases'); ?>  <a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $total_judgment_cases_count ?></a></div>
                  <div class="panel-body alen-panel" >
                    <ul class="list-group mt-2">
                        <?php 
                          if(sizeof($judgment_cases_list) > 0){ 
                            foreach ($judgment_cases_list as $key => $value) { 
                           ?>
                            <li class="list-group-item">
                              
                              <span class="badge badge-dashboard"><?php echo date('Y F d',strtotime($value['judgement_date'])); ?></span>

                              <button type="button" class="btn btn-default btn-sm btn-icon   pop" data-container="body" data-toggle="popover" data-html="true"  data-placement="bottom" data-content="<?php echo date('Y M d ',strtotime($value['judgement_date'])).'<hr>' ?>" data-original-title="<?php echo date('Y M d ',strtotime($value['judgement_date'])); ?>" title="<?php echo _l('judgment') ?>"> <i class="fa fa-tag"></i></button>
                              <a href="<?php echo admin_url('projects/view/'.$value['id']); ?>"><?php echo $value['name']; ?></a>
                              <!-- <p class="alen-p "><?php echo get_company_name($value['clientid']); ?></p> -->
                              <p class="alen-p "><?php echo _l('casediary_casenumber') ?>: <strong><?php echo $value['case_number']; ?></strong> | <strong><?php echo _l($value['case_type']);?></strong></p>
                               
                            </li>
                            <?php }
                          }else{ ?>
                            <li class="list-group-item center_li">
                              <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
                              <br>
                            </li>
                          <?php 
                         } ?>
                        
                      </ul>

                      <!--  <span class="label label-info span-footer" > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('cases') ?>"><?php echo _l('view_all').' '._l('cases'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span> -->
                      
                  </div>
                  <div class="panel-footer panel-footer-height">
                    <span class="" > 
                        <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('projects/matters') ?>"><?php echo _l('view_all_cases'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
                       </span>
                  </div>


                </div>
              </div>
              <!-- </div>
              </div> -->
           
<!--       </div>
   </div>   

 -->
