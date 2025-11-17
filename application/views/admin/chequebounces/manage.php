<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">

              <div class="col-md-4">
              <?php if(has_permission('chequebounces','','create')){ ?>
                    <a href="<?php echo admin_url('chequebounces/chequebounce'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_chequebounces'); ?></a>
                      <?php } ?>
              </div>
              

              <div class="_buttons">
               
              
              <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data  pull-right " data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right width300">
                  <li>
                    <a href="#" data-cview="all" onclick="dt_custom_view('','.table-chequebounces',''); return false;">
                      <?php echo _l('expenses_list_all'); ?>
                    </a>
                  </li>
                  <?php
                  // Only show this filter if user has permission for projects view otherwise wont need this becuase by default this filter will be applied
                  if(has_permission('chequebounces','','view')){ ?>
                  <!-- <li>
                    <a href="#" data-cview="my_projects" onclick="dt_custom_view('my_projects','.table-all-projects','my_projects'); return false;">
                      <?php echo _l('home_my_projects'); ?>
                    </a>
                  </li> -->
                  <?php } ?>
                  <li class="divider"></li>
                  <?php foreach($cheque_statuses as $status){ ?>
                    <li class="<?php if(!$this->input->get('status') || $this->input->get('status') == $status['chequestatusid']){echo 'active';} ?>">
                      <a href="#" data-cview="<?php echo 'project_status_'.$status['chequestatusid']; ?>" onclick="dt_custom_view('project_status_<?php echo $status['chequestatusid']; ?>','.table-chequebounces','project_status_<?php echo $status['chequestatusid']; ?>'); return false;">
                        <?php echo $status['name']; ?>
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </div>
                 <?php if(has_permission('chequebounces','','view')) {?>
                <a href="<?php echo admin_url('chequebounces/detailed_overview'); ?>" class="btn btn-success  pull-right mright5"><?php echo _l('chequebounce_report'); ?></a>
                <?php } ?>
                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
              </div>
               <div class="row mbot15">
                <div class="col-md-12">
                  <h4 class="no-margin"><?php echo _l('chequebounce_summary_heading'); ?></h4>
                  <?php
                   $_where = '';
                   if(!has_permission('chequebounces','','view')){
                    $_where = 'id IN (SELECT bounceid FROM '.db_prefix().'chequebounces_assigned WHERE staff_id='.get_staff_user_id().')';
                  }
                  ?>
                </div>
                <div class="_filters _hidden_inputs">
                  <?php
                  echo form_hidden('my_projects');
                  foreach($cheque_statuses as $status){
                   $value = $status['chequestatusid'];
                     if(!$this->input->get('status')){
                        $value = '';
                     } else if($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['chequestatusid'] ? $status['chequestatusid'] : "");
                     }
                     echo form_hidden('project_status_'.$status['chequestatusid'],$value);
                    ?>
                   <div class="col-md-2 col-xs-6 border-right">
                    <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['chequestatusid']; ?>
                    <a href="#" onclick="dt_custom_view('project_status_<?php echo $status['chequestatusid']; ?>','.table-chequebounces','project_status_<?php echo $status['chequestatusid']; ?>',true); return false;">
                     <h3 class="bold"><?php echo total_rows(db_prefix().'chequebounces',$where); ?></h3>
                     <span style="color:<?php echo $status['statuscolor']; ?>" project-status-<?php echo $status['chequestatusid']; ?>>
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
                       
                           <?php $this->load->view('admin/chequebounces/table_html'); ?>
                          
                      
    
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
$(function(){
     var ProjectsServerParams = {};

     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });

        
     var tAPI = initDataTable('.table-chequebounces', admin_url+'chequebounces/table', [0], [0], ProjectsServerParams, <?php echo hooks()->apply_filters('chequebounces_table_default_order', json_encode(array(0,'desc'))); ?>);
	
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
           var rows = $('.table-all-chequebounces').find('tbody tr');
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
             $.post(admin_url + 'chequebounces/bulk_assign', data).done(function() {
              window.location.reload();
          });
         },50);
       }
   }
</script>
</body>
</html>
