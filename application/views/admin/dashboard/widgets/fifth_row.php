<?php #################My Reminders######################## 

            $where = 'meeting_date BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 10 DAY)';
              

              $my_meetings  = $this->db->order_by('meeting_date','asc')->limit(5)->select('tblprojectdiscussions.*,tblprojects.name')->from('tblprojectdiscussions')->where($where)->join('tblprojects','tblprojects.id = tblprojectdiscussions.project_id','left')->get()->result_array(); 
              $my_meetings_count  = $this->db->from('tblprojectdiscussions')->where($where)->count_all_results();
     
                ?>
              <div class="col-md-4 <?php if(!in_array(13, $active_boxes)) echo 'hide';  ?> ">
                <div class="panel panel-default">
                  <!-- Default panel contents -->
                  <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> <?php echo _l('my').' '._l('meetings') ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $my_meetings_count ?></a> </div>
                    <div class="panel-body" style="max-height: 499px;overflow: hidden; height: 499px;" >

                      <ul class="list-group">
                        <?php 
                          if(sizeof($my_meetings) > 0){  
                            foreach ($my_meetings as $key => $value) { 
                              //$rel_data   = get_relation_data($value['rel_type'], $value['rel_id']);
                              //$rel_values = get_relation_values($rel_data, $value['rel_type']);
                              //$_data      = '<a href="' . $rel_values['link'] . '">' . $rel_values['name'] . '</a>';
                           ?>
                            <li class="list-group-item <?php //echo $li_class; ?>">
                              
                              <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Meeting Date"><?php echo date('Y M d',strtotime($value['meeting_date'])); ?><?php //echo $value['phonenumber']; ?></span>
                              <a  href="<?php echo admin_url('projects/view/'.$value['project_id'].'?group=project_discussions') ?>" onclick="#"><?php echo $value['name']; ?></a><br>
                              <p data-toggle="tooltip" data-placement="left" title="Subject"><?php echo $value['subject']; ?></p>
                              <p ><?php echo $value['location']; ?></p>

                              
                              
                              
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
                    <div class="panel-footer">
                       <span class="" > 
                        <!--<a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('misc/reminders') ?>"><?php echo _l('view_all_reminders'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>-->
                       </span> 
                    </div>


                    <!-- Table -->
                     
                     <!-- <div class="panel-footer">Panel footer</div> -->
                </div>

              </div>  

              </div>
   </div>   
   <!-- </div>
   </div>    -->