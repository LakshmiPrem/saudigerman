<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                <div class="row _buttons">
                     <div class="col-md-8">
                        <?php if(has_permission('tasks','','create')){ ?>
                        <a href="#" onclick="new_task(<?php if($this->input->get('project_id')){ echo "'".admin_url('tasks/task?rel_id='.$this->input->get('project_id').'&rel_type=project')."'";} ?>); return false;" class="btn btn-info pull-left new"><?php echo _l('new_task'); ?></a>
                        <?php } ?>
                        <a href="<?php if(!$this->input->get('project_id')){ echo admin_url('tasks/switch_kanban/'.$switch_kanban); } else { echo admin_url('projects/view/'.$this->input->get('project_id').'?group=project_tasks'); }; ?>" class="btn btn-default mleft10 pull-left hidden-xs">
                           <?php if($switch_kanban == 1){ echo _l('switch_to_list_view');}else{echo _l('leads_switch_to_kanban');}; ?>
                        </a>
                     </div>
                     <div class="col-md-4">
                        <?php if($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                        <div data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
                           <?php echo render_input('search','','','search',array('data-name'=>'search','onkeyup'=>'tasks_kanban();','placeholder'=>_l('search_tasks')),array(),'no-margin') ?>
                        </div>
                        <?php } else { ?>
                        <?php $this->load->view('admin/tasks/tasks_filter_by',array('view_table_name'=>'.table-tasks')); ?>
                        <a href="<?php echo admin_url('tasks/detailed_overview'); ?>" class="btn btn-success pull-right mright5"><?php echo _l('detailed_overview'); ?></a>
                        <?php } ?>
                     </div>
                  </div>
                  <hr class="hr-panel-heading hr-10" />
                  <div class="clearfix"></div>
                  <?php
                  if($this->session->has_userdata('tasks_kanban_view') && $this->session->userdata('tasks_kanban_view') == 'true') { ?>
                  <div class="kan-ban-tab" id="kan-ban-tab" style="overflow:auto;">
                     <div class="row">
                        <div id="kanban-params">
                           <?php echo form_hidden('project_id',$this->input->get('project_id')); ?>
                        </div>
                        <div class="container-fluid">
                           <div id="kan-ban"></div>
                        </div>
                     </div>
                  </div>
                  <?php } else { ?>
                  <?php $this->load->view('admin/tasks/_summary',array('table'=>'.table-tasks')); ?>
				      <div class="row">
                       <div class="col-md-3">
                     <div class="form-group">
                        <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                        <select name="taskrel_type" class="selectpicker" id="taskrel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""></option>
                            <option value="general"><?php echo _l('general'); ?></option>
                         
                           <option value="customer"> <?php echo _l('client'); ?> </option>
                          
                           <option value="contract" ><?php echo _l('contract'); ?></option>
                           <option value="ticket"> <?php echo _l('ticket'); ?> </option>
                          
                          <!--  <option value="trade_license"> <?php echo _l('trade_licenses'); ?></option>
                            <option value="legalrisk"> <?php echo _l('legalrisk'); ?></option>
                           <option value="document"><?php echo _l('safe_register'); ?></option>
                            <option value="chequebounce"><?php echo _l('chequebounce'); ?></option>-->
                         
                        </select>
                     </div>
                  </div>
                 <div class="col-md-3">
                     <div class="form-group hide" id="taskrel_id_wrapper">
                        <label for="rel_id" class="control-label"><span class="taskrel_id_label"></span></label>
                        <div id="taskrel_id_select">
                           <select name="taskrel_id" id="taskrel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           
                           </select>
                        </div>
                     </div>
                  </div>
                   
              
               </div>
                  <a href="#" data-toggle="modal" data-target="#tasks_bulk_actions" class="hide bulk-actions-btn table-btn" data-table=".table-tasks"><?php echo _l('bulk_actions'); ?></a>
                  <?php $this->load->view('admin/tasks/_table',array('bulk_actions'=>true)); ?>
               <?php $this->load->view('admin/tasks/_bulk_actions'); ?>
               <?php } ?>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<script>
   taskid = '<?php echo $taskid; ?>';
   $(function(){
       tasks_kanban();
   });
</script>
</body>
</html>
<script>
   var _rel_id = $('#taskrel_id'),
   _rel_type = $('#taskrel_type'),
   _rel_id_wrapper = $('#taskrel_id_wrapper'),
   data = {};
   $('.taskrel_id_label').html(_rel_type.find('option:selected').text());
     _rel_type.on('change', function() {

     var clonedSelect = _rel_id.html('').clone();
     _rel_id.selectpicker('destroy').remove();
     _rel_id = clonedSelect;
     $('#taskrel_id_select').append(clonedSelect);
     $('.taskrel_id_label').html(_rel_type.find('option:selected').text());

     task_rel_select1();
     if($(this).val() != ''){
      _rel_id_wrapper.removeClass('hide');
    } else {
      _rel_id_wrapper.addClass('hide');
    }
    init_project_details(_rel_type.val());
   });

      $('body').on('change','#taskrel_id',function(){
     if($(this).val() != ''){
       if(_rel_type.val() == 'project'){
         $.get(admin_url + 'projects/get_rel_project_data/'+$(this).val()+'/'+taskid,function(project){
           $("select[name='milestone']").html(project.milestones);
           if(typeof(_milestone_selected_data) != 'undefined'){
            $("select[name='milestone']").val(_milestone_selected_data.id);
            $('input[name="duedate"]').val(_milestone_selected_data.due_date)
          }
          $("select[name='milestone']").selectpicker('refresh');
          if(project.billing_type == 3){
           $('.task-hours').addClass('project-task-hours');
         } else {
           $('.task-hours').removeClass('project-task-hours');
         }

         if(project.deadline) {
            var $duedate = $('#_task_modal #duedate');
            var currentSelectedTaskDate = $duedate.val();
            $duedate.attr('data-date-end-date', project.deadline);
            $duedate.datetimepicker('destroy');
            init_datepicker($duedate);

            if(currentSelectedTaskDate) {
               var dateTask = new Date(unformat_date(currentSelectedTaskDate));
               var projectDeadline = new Date(project.deadline);
               if(dateTask > projectDeadline) {
                  $duedate.val(project.deadline_formatted);
               }
            }
         } else {
           // reset_task_duedate_input();
         }
         init_project_details(_rel_type.val(),project.allow_to_view_tasks);
       },'json');
       } else {
         //reset_task_duedate_input();
       }
     }
   });

    <?php if(!isset($task)){ ?>
      _rel_id.change();
      <?php } ?>
   function task_rel_select1(){
      var serverData = {};
      serverData.rel_id = _rel_id.val();
      data.type = _rel_type.val();
      init_ajax_search(_rel_type.val(),_rel_id,serverData);
     }

     function init_project_details(type,tasks_visible_to_customer){
      var wrap = $('.non-project-details');
      var wrap_task_hours = $('.task-hours');
      if(type == 'project'){
        if(wrap_task_hours.hasClass('project-task-hours') == true){
          wrap_task_hours.removeClass('hide');
        } else {
          wrap_task_hours.addClass('hide');
        }
        wrap.addClass('hide');
        $('.project-details').removeClass('hide');
      } else {
        wrap_task_hours.removeClass('hide');
        wrap.removeClass('hide');
        $('.project-details').addClass('hide');
        $('.task-visible-to-customer').addClass('hide').prop('checked',false);
      }
      if(typeof(tasks_visible_to_customer) != 'undefined'){
        if(tasks_visible_to_customer == 1){
          $('.task-visible-to-customer').removeClass('hide');
          $('.task-visible-to-customer input').prop('checked',true);
        } else {
          $('.task-visible-to-customer').addClass('hide')
          $('.task-visible-to-customer input').prop('checked',false);
        }
      }
    }
</script>