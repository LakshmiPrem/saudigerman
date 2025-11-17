<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">

              <div class="col-md-4">
                <div class="select-placeholder">
                    <label><?php echo _l('client'); ?></label>
                   <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-empty-title="<?php echo _l('client'); ?>" data-none-selected-text="<?php echo _l('client'); ?>">
                   </select>
                </div>
              </div>
              <div class="col-md-4">
                <?php $case_types = get_case_client_types();?>
                <?php echo render_select('case_type',$case_types,array('id','name'),'case_type');?>

              </div>

              <div class="_buttons">
             
              
              <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right width300">
                  <li>
                    <a href="#" data-cview="all" onclick="dt_custom_view('','.table-all-projects',''); return false;">
                      <?php echo _l('expenses_list_all'); ?>
                    </a>
                  </li>
                  <?php
                  // Only show this filter if user has permission for projects view otherwise wont need this becuase by default this filter will be applied
                  if(has_permission('projects','','view')){ ?>
                  <!-- <li>
                    <a href="#" data-cview="my_projects" onclick="dt_custom_view('my_projects','.table-all-projects','my_projects'); return false;">
                      <?php echo _l('home_my_projects'); ?>
                    </a>
                  </li> -->
                  <?php } ?>
                  <li class="divider"></li>
                  <?php foreach($statuses as $status){ ?>
                    <li class="<?php if($status['filter_default'] == true && !$this->input->get('status') || $this->input->get('status') == $status['id']){echo 'active';} ?>">
                      <a href="#" data-cview="<?php echo 'project_status_'.$status['id']; ?>" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-all-projects','project_status_<?php echo $status['id']; ?>'); return false;">
                        <?php echo $status['name']; ?>
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </div>
                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
              </div>
               <div class="row mbot15">
                <div class="col-md-12">
                  <h4 class="no-margin"><?php echo _l('cases').' '._l('summary'); ?></h4>
                  <?php
                   $_where = '';
                  if(!has_permission('projects','','view')){
                    $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
                  }
                  ?>
                </div>
                <div class="_filters _hidden_inputs">
                  <?php
                  echo form_hidden('my_projects');
                  foreach($statuses as $status){
                   $value = $status['id'];
                     if($status['filter_default'] == false && !$this->input->get('status')){
                        $value = '';
                     } else if($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['id'] ? $status['id'] : "");
                     }
                     echo form_hidden('project_status_'.$status['id'],$value);
                    ?>
                   <div class="col-md-2 col-xs-6 border-right">
                    <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id']; ?>
                    <a href="#" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-all-projects','project_status_<?php echo $status['id']; ?>',true); return false;">
                     <h3 class="bold"><?php echo total_rows(db_prefix().'projects',$where); ?></h3>
                     <span style="color:<?php echo $status['color']; ?>" project-status-<?php echo $status['id']; ?>">
                     <?php echo $status['name']; ?>
                     </span>
                   </a>
                 </div>
                 <?php } ?>
               </div>
             </div>
             <div class="clearfix"></div>
              <hr class="hr-panel-heading" />
             <?php echo form_hidden('custom_view'); ?>
              <a href="#" data-toggle="modal" data-target="#cases_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-all-projects"><?php echo _l('bulk_actions'); ?></a>
                  <div class="modal fade bulk_actions" id="cases_bulk_action" tabindex="-1" role="dialog">
                     <div class="modal-dialog" role="document">
                        <div class="modal-content">
                           <div class="modal-header">
                              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
                           </div>
                           <div class="modal-body">
                              <?php if(has_permission('customers','','delete')){ ?>
                              <!-- <div class="checkbox checkbox-danger">
                                 <input type="checkbox" name="mass_delete" id="mass_delete">
                                 <label for="mass_delete"><?php echo _l('mass_assign'); ?></label>
                              </div>
                              <hr class="mass_delete_separator" /> -->
                              <?php } ?>
                              <div id="bulk_change">
                                 <?php  echo render_select('mass_assign_staff_id',$staff,array('staffid',array('firstname','lastname')),'assign_to'); ?>
                                 <!-- <p class="text-danger"><?php echo _l('bulk_action_customers_groups_warning'); ?></p> -->
                              </div>
                           </div>
                           <div class="modal-footer">
                              <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                              <a href="#" class="btn btn-info" onclick="case_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
                           </div>
                        </div>
                        <!-- /.modal-content -->
                     </div>
                     <!-- /.modal-dialog -->
                  </div>
                  <!-- /.modal -->
             <?php  $table_data = [
                                    '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="all-projects"><label></label></div>',
                                   
                                    [
                                        'name'     =>  _l('#'),
                                        'th_attrs' => ['class' =>  'not_visible'],
                                    ],
                                   _l('project_name'),
                                   _l('casediary_file_no'),
                                    [
                                         'name'     => _l('case_type'),
                                         //'th_attrs' => ['class' => isset($client) ? '' : 'not_visible'],
                                    ],
                                   
                                    [
                                         'name'     => _l('project_customer'),
                                         'th_attrs' => ['class' => isset($client) ? 'not_visible' : ''],
                                    ],
                                   //_l('tags'),
                                   _l('project_start_date'),
                                   _l('project_members'),
                                   _l('project_status'),
                                ];

                    $custom_fields = get_custom_fields('projects', ['show_on_table' => 1]);
                    foreach ($custom_fields as $field) {
                        array_push($table_data, $field['name']);
                    }

                    $table_data = hooks()->apply_filters('projects_table_columns', $table_data);

                    render_datatable($table_data, isset($class) ?  $class : 'all-projects', [], [
                      'data-last-order-identifier' => 'projects',
                      'data-default-order'  => get_table_last_order('projects'),
                      'data-display-length'=>-1,
                    ]);
              ?>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('admin/projects/copy_settings'); ?>
<?php init_tail(); ?>
<script>
$(function(){
     var ProjectsServerParams = {};

     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });

     ProjectsServerParams['clientid']  = '[name="clientid"]';
     ProjectsServerParams['case_type'] = '[name="case_type"]';
     
     var tAPI = initDataTable('.table-all-projects', admin_url+'projects/all_cases_table', [0], [0], ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array(1,'desc'))); ?>);
     tAPI.page.len(-1).draw();
     $.each(ProjectsServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                 tAPI.ajax.reload();
                 tAPI.page.len(-1).draw();
            });
      });

     /*$('.table-all-projects').on('xhr.dt', function(e, settings, json, xhr) {
      tAPI.page.len(100).draw();
    })*/

     init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});

 function case_bulk_action(event) {
       var r = confirm(app.lang.confirm_action_prompt);
       if (r == false) {
           return false;
       } else {
           var mass_delete = $('#mass_delete').prop('checked');
           var ids = [];
           var data = {};
           /*if(mass_delete == false || typeof(mass_delete) == 'undefined'){
               data.groups = $('select[name="move_to_groups_customers_bulk[]"]').selectpicker('val');
               if (data.groups.length == 0) {
                   data.groups = 'remove_all';
               }
           } else {
               data.mass_delete = true;
           }*/
           var rows = $('.table-all-projects').find('tbody tr');
           $.each(rows, function() {
               var checkbox = $($(this).find('td').eq(0)).find('input');
               if (checkbox.prop('checked') == true) {
                   ids.push(checkbox.val());
               }
           });
           data.ids = ids;
           data.assigned_user = $('select[name="mass_assign_staff_id"]').val(); 
           $(event).addClass('disabled');
           setTimeout(function(){
             $.post(admin_url + 'projects/bulk_assign', data).done(function() {
              window.location.reload();
          });
         },50);
       }
   }
</script>
</body>
</html>
