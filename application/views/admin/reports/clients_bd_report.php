<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row"> 
            
        <div class="col-md-12">
            <div class="panel_s">
                <div class="panel-body">
                     <h4 class="no-margin font-medium"><i class="fa fa-tty menu-icon" aria-hidden="true"></i> <?php echo _l('als_clients_bd_report'); ?></h4>
                      <hr />

                    <?php echo form_open($this->uri->uri_string()); ?>
                    <div class="row">
                         <div class="col-md-2">
                        <div class="select-placeholder">
                            <label for="clientid" ><?php echo _l('customers'); ?></label>
                           <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-empty-title="<?php echo _l('client'); ?>" data-none-selected-text="<?php echo _l('client'); ?>">
                           </select>
                        </div>
         </div>

          <?php //if(count($lawyer_assigned_arr) > 0 ) { ?>
         <div class="col-md-2">
          <label><?=_l('leads_dt_assigned')?></label>
            <?php $selected = ($this->input->post('view_assigned') ? $this->input->post('view_assigned') : ''); ?> 
             <?php echo render_select('view_assigned',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_dt_assigned')),array(),'no-mbot'); ?>
         </div>
         <?php //} ?>

                        <div class="col-md-2 leads-filter-column ">
                            <label><?php echo _l('status') ?></label>
                            <?php
                            $selected = array();
    
                            if(isset($selected_statuses)){
                                foreach($selected_statuses as $key => $status) {
                                    $selected[]= $status;
                                }    
                            }else{
                            if($this->input->get('status')) {
                            $selected[] = $this->input->get('status');
                            } else {
                            foreach($statuses as $key => $status) {
                            if($status['isdefault'] == 0) {
                              $selected[] = $status['id'];
                            } else {
                              $statuses[$key]['option_attributes'] = array('data-subtext'=>_l('leads_converted_to_client'));
                            }
                            }
                            }}
                            echo '<div id="leads-filter-status">';
                            echo render_select('view_status[]',$statuses,array('id','name'),'',$selected,array('data-width'=>'100%','data-none-selected-text'=>_l('leads_all'),'multiple'=>true,'data-actions-box'=>true),array(),'no-mbot','',false);
                            echo '</div>';
                            ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo render_date_input('staff_report_from_date','from_date',$this->input->post('staff_report_from_date')); ?>
                        </div>
                        <div class="col-md-2">
                            <?php echo render_date_input('staff_report_to_date','to_date',$this->input->post('staff_report_to_date')); ?>
                        </div>
                        <div class="col-md-2 text-left">
                            <button type="submit" class="btn btn-info label-margin"><?php echo _l('generate'); ?></button>
                        </div>
                    </div>
                    <?php echo form_close(); ?>
                    <hr />

                    <table class="table dt-table table-clients-bd-report scroll-responsive">
                        <thead>
                           <tr>
                              <th><?php echo _l('client'); ?></th>
                              <th><?php echo _l('lawyer');?></th>
                              <th><?php echo _l('status');?></th>
                              <!-- <th class="not_visible"><?php echo _l('price_quoted'); ?></th> -->
                              <th><?php echo _l('date_of_enquiry'); ?></th>
                           </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($clients_bd_report as $row) {  ?>
                                <tr>
                                    <td><?=get_company_name($row['client_id'])?></td>
                                    <td><?php 
                                      foreach ($row['assignees'] as  $member) {
                                      if ($member['assigneeid'] != '') {
                                          echo '<a href="' . admin_url('profile/' . $member['assigneeid']) . '">' .
                                          staff_profile_image($member['assigneeid'], array(
                                              'staff-profile-image-small mright5'
                                              ), 'small', array(
                                              'data-toggle' => 'tooltip',
                                              'data-title' => get_staff_full_name($member['assigneeid'])
                                              )) . '</a>';
                                                  // For exporting
                                          //$exportMembers .= $member . ', ';
                                        }
                                      }
                                    ?></td>
                                    <td><?=$row['status_name']?></td>
                                    <!-- <td class="not_visible"><?=format_money($row['price_quoted'])?></td> -->
                                    <td><?=_dt($row['dateadded'])?></td>
                                </tr>
                            <?php }
                            ?>
                        </tbody>
                        <tfoot>
                           <tr>
                              <td></td>
                              <td></td>
                              <td></td>
                              <!-- <td class="not_visible"></td> -->
                              <td></td>
                           </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
<?php init_tail(); ?>

</body>
</html>
