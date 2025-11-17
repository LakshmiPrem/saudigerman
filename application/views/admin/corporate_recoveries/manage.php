<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        
        <div class="row">
            <div class="col-md-12">
                <div class="_filters _hidden_inputs hidden">
                    <?php
                    echo form_hidden('my_customers');

/*                  $assigned_dates = array(
                  array('id'=>'today','name'=>'Today'),
                  array('id'=>'this_week','name'=>'This Week'),
                  array('id'=>'this_month','name'=>'This Month'),
                  array('id'=>'last_month','name'=>'Last Month'),
                  array('id'=>'last_week','name'=>'Last Week')
                ); */

                  //foreach($assigned_dates as $assigned_date){
                      // echo form_hidden('assigned_date_'.$assigned_date['id']);
                 // }

                   /* foreach($groups as $group){
                       echo form_hidden('customer_group_'.$group['id']);
                   }
                   foreach($contract_types as $type){
                       echo form_hidden('contract_type_'.$type['id']);
                   }
                   foreach($invoice_statuses as $status){
                       echo form_hidden('invoices_'.$status);
                   }
                   foreach($estimate_statuses as $status){
                       echo form_hidden('estimates_'.$status);
                   }
                   foreach($project_statuses as $status){
                    echo form_hidden('projects_'.$status['id']);
                }
                foreach($proposal_statuses as $status){
                    echo form_hidden('proposals_'.$status);
                }*/
                foreach($customer_admins as $cadmin){
                    echo form_hidden('responsible_admin_'.$cadmin['staff_id']);
                }
                ?>
            </div>
            <div class="panel_s">
                <div class="panel-body">
                    <div class="_buttons">
                        <?php if (has_permission('corporate_recovery','','create')) { ?>
                        <a href="<?php echo admin_url('corporate_recoveries/corporate_recovery'); ?>" class="btn btn-info mright5 test pull-left display-block">
                            <?php echo _l('new_recovery'); ?></a>
                            <a href="<?php echo admin_url('corporate_recoveries/import'); ?>" class="btn btn-info pull-left display-block mright5 hidden-xs">
                                <?php echo _l('import'); ?></a> 
                                <?php } ?>
                                
                                    <div class="visible-xs">
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>" style="">
                                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <i class="fa fa-filter" aria-hidden="true"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-left" style="width:300px;">
                                            <li class="active"><a href="#" data-cview="all" onclick="dt_custom_view('','.table-recoveries',''); return false;"><?php echo _l('customers_sort_all'); ?></a>
                                            </li>
                                             <li class="divider"></li>
                                             <li>
                                                  <a href="#" data-cview="my_customers" onclick="dt_custom_view('my_customers','.table-recoveries','my_customers'); return false;">
                                                           <?php echo _l('customers_assigned_to_me'); ?>
                                                        </a>
                                             </li>
                                            <!-- <li class="divider"></li> -->
                                            <!--  <li class="dropdown-submenu pull-left assigned_date">
                                              <a href="#" tabindex="-1"><?php echo _l('Assigned Date'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                            <?php foreach($assigned_dates as $assigned_date){ ?>

                                                <li>
                                                  <a href="#" data-cview="assigned_date<?php echo $assigned_date['id']; ?>" onclick="dt_custom_view('assigned_date<?php echo $assigned_date['id']; ?>','.table-recoveries','assigned_date<?php echo $assigned_date['id']; ?>'); return false;">
                                                      <?php echo $assigned_date['name']; ?>
                                                  </a>
                                                </li>
                                              <?php } ?>
                                            </ul>
                                          </li> -->

                                           
                                      
                                            <?php if(count($customer_admins) > 0 && (has_permission('corporate_recovery','','create') || has_permission('corporate_recovery','','view'))){ ?>
                                            <div class="clearfix"></div>
                                            <li class="divider"></li>
                                            <li class="dropdown-submenu pull-left responsible_admin">
                                                <a href="#" tabindex="-1"><?php echo _l('agents'); ?></a>
                                                <ul class="dropdown-menu dropdown-menu-left">
                                                    <?php foreach($customer_admins as $cadmin){ ?>
                                                    <li>
                                                        <a href="#" data-cview="responsible_admin_<?php echo $cadmin['staff_id']; ?>" onclick="dt_custom_view('responsible_admin_<?php echo $cadmin['staff_id']; ?>','.table-recoveries','responsible_admin_<?php echo $cadmin['staff_id']; ?>'); return false;">
                                                            <?php echo get_staff_full_name($cadmin['staff_id']); ?>
                                                        </a>
                                                    </li>
                                                    <?php } ?>
                                                </ul>
                                            </li>
                                            <?php } ?>
                                        </ul>
                                    </div>
                                </div>
                                <div class="clearfix"></div>
                                <?php if(has_permission('corporate_recovery','','view') || have_assigned_customers()) {
                                    $where_summary = '1=1';
                                    if(!has_permission('corporate_recovery','','view')){
                                        $where_summary = ' id IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id='.get_staff_user_id().')';
                                    }
                                    ?>
                                    <hr class="hr-panel-heading" />
                                    <div class="row mbot15">
            <div class="col-md-12">
                <h4 class="no-margin"><?php echo _l('summary'); ?></h4>
            </div>
            <div class="col-md-4 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows('tblcorporate_recoveries',$where_summary); ?></h3>
                <span class="text-dark"><?php echo _l('total'); ?></span>
            </div>
            <div class="col-md-4 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows('tblcorporate_recoveries','active=1 AND '.$where_summary); ?></h3>
                <span class="text-success"><?php echo _l('active'); ?></span>
            </div>
            <div class="col-md-4 col-xs-6 border-right">
                <h3 class="bold"><?php echo total_rows('tblcorporate_recoveries','active=0 AND '.$where_summary); ?></h3>
                <span class="text-danger"><?php echo _l('inactive'); ?></span>
            </div>
           
           
           
            </div>
                                        <?php } ?>
                                        <hr class="hr-panel-heading" />
                                        <a href="#" data-toggle="modal" data-target="#customers_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-recoveries"><?php echo _l('bulk_actions'); ?></a>
                                        <div class="modal fade bulk_actions" id="customers_bulk_action" tabindex="-1" role="dialog">
                                            <div class="modal-dialog" role="document">
                                             <div class="modal-content">
                                              <div class="modal-header">
                                               <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                               <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                                           </div>
                                           <div class="modal-body">
                                              <?php if(has_permission('corporate_recovery','','delete')){ ?>
                                              <div class="checkbox checkbox-danger">
                                                <input type="checkbox" name="mass_delete" id="mass_delete">
                                                <label for="mass_delete"><?php echo _l('mass_delete'); ?></label>
                                            </div>
                                            <hr class="mass_delete_separator" />
                                            <?php } ?>

                                          <!----------- Mass Assign Start ------------>     
                                            <?php if(is_admin()){ ?>
                                              <!-- <div class="checkbox checkbox-danger">
                                                <input type="checkbox" name="mass_assign" id="mass_assign">
                                                <label for="mass_assign"><?php echo _l('mass_assign'); ?></label>
                                             </div> -->
                                              <?php
                                               $selected = array();
                                               
                                               //echo render_select('mass_assign_staff_id',$staff,array('staffid',array('firstname','lastname')),'',$selected,array(),'','',false); ?>
                                              <?php } ?> 
                                          <!----------- Mass Assign End-------------------->    

                                          <!--   <div id="bulk_change">
                                               <?php echo render_select('move_to_groups_customers_bulk[]',$groups,array('id','name'),'customer_groups','', array('multiple'=>true),array(),'','',false); ?>
                                               <p class="text-danger"><?php echo _l('bulk_action_customers_groups_warning'); ?></p>
                                           </div> -->
                                       </div>
                                       <div class="modal-footer">
                                           <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                                           <a href="#" class="btn btn-info" onclick="customers_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                                       </div>
                                   </div><!-- /.modal-content -->
                               </div><!-- /.modal-dialog -->
                           </div><!-- /.modal -->
                              <div class="col-md-2">
                            <div class="checkbox">
                                <input type="checkbox" checked id="exclude_inactive" name="exclude_inactive">
                                <label for="exclude_inactive"><?php echo _l('exclude_inactive'); ?> <?php echo _l('recovery'); ?></label>

                            </div>

                            </div>
                           <?php if(is_admin()){ ?>
                            <div class="col-md-3">
                              <div class="form-group select-placeholder">
                               <!--  <select id="client_position" name="client_position" data-live-search="true" data-width="100%" class="selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="all">All Clients</option>
                                    <option value="next_7_days"  >Next 7 Days</option>
                                    <option value="all">All</option>
                    
                                </select> -->
                                <?php echo render_select( 'client_position',$clients,array( 'userid',array( 'company')), 'clients','',array('data-none-selected-text'=>_l('All Clients'))); ?>

                               </div>
                            </div>
                          <?php } ?>
                            <!--  <div class="col-md-3">
                              <div class="form-group select-placeholder">
                                 <select id="segment_type" name="segment_type" data-live-search="true" data-width="100%" class="selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value="" selected>Corporate / Individual / SME /Retail </option>
                                    <option value="Individual">Individual</option>
                                    <option value="Corporate">Corporate</option>
                                    <option value="SME">SME</option>
                                    <option value="Retail">Retail</option>
                                </select> 
                                

                               </div>
                            </div> -->
                         

                            <div class="col-md-3"> 
                    <?php $contact_code_arr = get_contact_codes(); ?>
    <?php echo render_select('contact_code',$contact_code_arr,array('id','name'),'contact_code');?>
                  </div>


                           <?php if($this->input->get('range')) {
                                $range = $this->input->get('range');
                            } 
                            $selected = (isset($range)) ? $range : '';
                            ?>

                              <div class="col-md-2">
                                <label><?php echo _l('added'); ?></label>
                               <div class="select-placeholder">
                                <select name="range" id="range" class="selectpicker" data-width="100%">
                                 <option value="" <?php if($selected == '')  echo 'selected'; ?> ><?php echo _l('report_sales_months_all_time'); ?></option>   
                                 <option value="today" <?php if($selected == 'today')  echo 'selected'; ?> ><?php echo _l('today'); ?></option>
                                 <option value="this_month" <?php if($selected == 'this_month')  echo 'selected'; ?> ><?php echo _l('this_month'); ?></option>
                                 <option value="last_month" <?php if($selected == 'last_month')  echo 'selected'; ?> ><?php echo _l('last_month'); ?></option>
                                 <option value="this_week" <?php if($selected == 'this_week')  echo 'selected'; ?>><?php echo _l('this_week'); ?></option>
                                 <option value="last_week" <?php if($selected == 'last_week')  echo 'selected'; ?>><?php echo _l('last_week'); ?></option>
                               </select>
                               </div>
                               
                            </div>

                             <?php if($this->input->get('assigned_date')) {
                                $assigned_date = $this->input->get('assigned_date');
                            } 
                            $selected = (isset($assigned_date)) ? $assigned_date : '';
                            ?>

                              <div class="col-md-2">
                                <label><?php echo _l('assigned_date'); ?></label>
                               <div class="select-placeholder">
                                <select name="assigned_date" id="assigned_date" class="selectpicker" data-width="100%">
                                 <option value="" <?php if($selected == '')  echo 'selected'; ?> ><?php echo _l('report_sales_months_all_time'); ?></option>   
                                 <option value="today" <?php if($selected == 'today')  echo 'selected'; ?> ><?php echo _l('today'); ?></option>
                                 <option value="this_month" <?php if($selected == 'this_month')  echo 'selected'; ?> ><?php echo _l('this_month'); ?></option>
                                 <option value="last_month" <?php if($selected == 'last_month')  echo 'selected'; ?> ><?php echo _l('last_month'); ?></option>
                                 <option value="this_week" <?php if($selected == 'this_week')  echo 'selected'; ?>><?php echo _l('this_week'); ?></option>
                                 <option value="last_week" <?php if($selected == 'last_week')  echo 'selected'; ?>><?php echo _l('last_week'); ?></option>
                               </select>
                               </div>
                               
                            </div>

                           <div class="clearfix mtop20"></div>
                           <?php
                           $table_data = array();
                           $_table_data = array(
                             '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="recoveries"><label></label></div>',
                            // '#',
                            _l('file_no'),
                            _l('debtor_title'),
                            _l('client'),
                            _l('city'),
                            _l('email'),
                            _l('clients_list_phone'),
                            _l('added'),
                            _l('latest_update'),
                            _l('customer_active'),
                            );

                           foreach($_table_data as $_t){
                            array_push($table_data,$_t);
                        }
 
                        $table_data = hooks()->apply_filters('customers_table_columns',$table_data);

                        $_op = _l('options');

                        array_push($table_data, $_op);
                        render_datatable($table_data,'recoveries');
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<script>
    $(function(){
        var CustomersServerParams = {};

         
        $.each($('._hidden_inputs._filters input'),function(){

           CustomersServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });
        CustomersServerParams['exclude_inactive'] = '[name="exclude_inactive"]:checked';
        CustomersServerParams['reassigned'] = '[name="reassigned"]:checked';

        CustomersServerParams['client_position'] = '[name="client_position"]';
        CustomersServerParams['segment_type'] = '[name="segment_type"]';
        /*CustomersServerParams = {
            "client_position": "[name='client_position']",
            "segment_type" : "[name='segment_type']",

        }*/
        CustomersServerParams['contact_code'] = '[name="contact_code"]';
        CustomersServerParams['range'] = '[name="range"]';
        CustomersServerParams['assigned_date'] = '[name="assigned_date"]';

        var headers_clients = $('.table-recoveries').find('th');
        var not_sortable_clients = (headers_clients.length - 1);
        var tAPI = initDataTable('.table-recoveries', admin_url+'corporate_recoveries/table',[not_sortable_clients], [0,not_sortable_clients], CustomersServerParams,[3,'DESC']);
        $('input[name="exclude_inactive"]').on('change',function(){
            tAPI.ajax.reload();
        });
        $('input[name="reassigned"]').on('change',function(){
            tAPI.ajax.reload();
        });
        var  table_recoveries = $('.table-recoveries');

        $.each(CustomersServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                 tAPI.ajax.reload();
            });
        });

    });
    function customers_bulk_action(event) {
        var r = confirm(appLang.confirm_action_prompt);
        if (r == false) {
            return false;
        } else {
            var mass_delete = $('#mass_delete').prop('checked');
            var mass_assign = $('#mass_assign').prop('checked');

            var ids = [];
            var data = {};
            if(mass_delete == false || typeof(mass_delete) == 'undefined'){
                data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
                if (data.groups.length == 0) {
                    data.groups = 'remove_all';
                }
            } else {
                data.mass_delete = true;
            }
            var rows = $('.table-recoveries').find('tbody tr');
            $.each(rows, function() {
                var checkbox = $($(this).find('td').eq(0)).find('input');
                if (checkbox.prop('checked') == true) {
                    ids.push(checkbox.val());
                }
            });
            data.ids = ids;
            $(event).addClass('disabled');
            setTimeout(function(){
              if(mass_assign == true){
                data.mass_assign = true;
                var mass_assign_staff_id = $('#mass_assign_staff_id').val();
                data.staff_id = mass_assign_staff_id;
                $.post(admin_url + 'corporate_recoveries/bulk_assign', data).done(function() {
                  window.location.reload();
                });
              }else{
                $.post(admin_url + 'corporate_recoveries/bulk_action', data).done(function() {
                 window.location.reload();
                });
              }
          },50);
        }
    }


</script>
</body>
</html>
