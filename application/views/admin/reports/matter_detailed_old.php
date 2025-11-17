<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                           <?php //echo form_open(admin_url('reports/matter_detailed'),array('id'=>'det-form')); ?>
                              <div class="col-md-4">
                              <?php $selected=( isset($case_id) ? $case_id : ''); ?>
                                 <?php  echo render_select('case_id',$cases,array('id',array('name','company','file_no')),'projects',$selected);?>
                              </div>
                              <div class="clearfix"></div>
                           <?php //echo form_close(); ?>
                           </div>
                        </div>
                    </div>
                     <div class="panel_s">

                        <?php if($selected != ''){?>
                        <div class="panel-body">
                           
                           <div class="table-responsive">
                              <div class="pull-right">
                               <?php  if(has_permission('projects','','create')){ ?>
                              
                                 <a class="btn btn-info" href="<?php echo admin_url('projects/export_project_data/'.$case_id); ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> <?php echo _l('export_project_data'); ?></a>
                        
                              <?php } ?>
                           </div> <br>
                              <h3><?php echo  _l('project_name') . ': ' . $project->name ; ?></h3>
                              <h3><?php echo ucwords(_l('project_overview')) ?></h3>
                              <?php if (!empty($project->description)) { ?>
                                 <p><b style="background-color:#f0f0f0;"><?= _l('project_description');?></b><br /><br /> <?= $project->description ?></p>
                                   <?php  } ?>
                             <?php if ($project->billing_type == 1) {
    $type_name = 'project_billing_type_fixed_cost';
} elseif ($project->billing_type == 2) {
    $type_name = 'project_billing_type_project_hours';
} else {
    $type_name = 'project_billing_type_project_task_hours';
} ?>
                              <table class="table">
                                 <thead>
                                    <tr>
                                       <th><b style="background-color:#f0f0f0;"><?=_l('project_overview')?></b></th>
                                       <th><b style="background-color:#f0f0f0;"><?=ucwords(_l('finance_overview'))?></b></th>
                                       <th><b style="background-color:#f0f0f0;"><?=ucwords(_l('project_customer'))?></b></th>
                                    </tr>
                                 </thead>
                                 <?php
                                 if ($project->billing_type == 1 || $project->billing_type == 2) {
    if ($project->billing_type == 1) {
        $html = '<b>' . _l('project_total_cost') . ': </b>' . app_format_money($project->project_cost, $project->currency_data) . '<br />';
    } else {
        $html = '<b>' . _l('project_rate_per_hour') . ': </b>' . app_format_money($project->project_rate_per_hour, $project->currency_data) . '<br />';
    }
}?>
                                 <tbody>
                                    <tr>
                                       <td><b><?=_l('project_billing_type')?>:</b><?=_l($type_name)?><br><?=$html?>

                                       <?php $status = get_project_status_by_id($project->status); ?>
                                       <b><?=_l('project_status') ?>:</b><?=$status['name']?><br>

                                       <b><?=_l('project_datecreated') ?>:</b><?=_d($project->project_created)?><br>

                                       <b><?=_l('project_start_date') ?>:</b><?=_d($project->start_date)?><br>
                                      

                                      <!--  <b><?=_l('total_project_worked_days') ?>:</b><?=$total_days?><br> -->

                                       <b><?=_l('project_overview_total_logged_hours') ?>:</b><?=$total_logged_time?><br>

                                       <b><?=_l('total_project_members') ?>:</b><?=$total_members?><br>
                                       <b><?=_l('total_project_files') ?>:</b><?=$total_files_attached?><br>
                                       <b><?=_l('total_project_discussions_created') ?>:</b><?=$total_discussion?><br>

                                       <b><?=_l('total_milestones') ?>:</b><?=$total_milestones?><br>
                                       <b><?=_l('total_tickets_related_to_project') ?>:</b><?=$total_tickets?><br>
                                       
                                       </td>


                                       <td>
                                       
                                          <b><?=_l('projects_total_invoices_created') ?>:</b><?=$total_invoices?><br>
                                          <b><?=_l('outstanding_invoices') ?>:</b><?=app_format_money($invoices_total_data['due'], $project->currency_data)?><br>
                                          <b><?=_l('past_due_invoices') ?>:</b><?=app_format_money($invoices_total_data['overdue'], $project->currency_data)?><br>
                                          <b><?=_l('paid_invoices') ?>:</b><?=app_format_money($invoices_total_data['paid'], $project->currency_data)?><br>

                                      <?php 
$this->load->model('projects_model');

                                      if ($project->billing_type == 2 || $project->billing_type == 3) { 
                                       // Total logged time + money
    $logged_time_data = $this->projects_model->total_logged_time_by_billing_type($project->id);
                                       ?>
   
                                          <b><?=_l('project_overview_logged_hours') ?>:</b><?=$logged_time_data['logged_time'] . ' - ' . app_format_money($logged_time_data['total_money'], $project->currency_data)?><br>
            <?php 
             // Total billable time + money
    $logged_time_data = $this->projects_model->data_billable_time($project->id);
            ?>                            

                                        <b><?=_l('project_overview_billable_hours') ?>:</b><?=$logged_time_data['logged_time'] . ' - ' . app_format_money($logged_time_data['total_money'], $project->currency_data)?><br>
            <?php // Total billed time + money
    $logged_time_data = $this->projects_model->data_billed_time($project->id);?>
                                    <b><?=_l('project_overview_billed_hours') ?>:</b><?=$logged_time_data['logged_time'] . ' - ' . app_format_money($logged_time_data['total_money'], $project->currency_data) ?><br>
                                       <?php } ?>

         <?php // Total unbilled time + money
    $logged_time_data = $this->projects_model->data_unbilled_time($project->id); ?>
                                     <b><?=_l('project_overview_unbilled_hours') ?>:</b><?=$logged_time_data['logged_time'] . ' - ' . app_format_money($logged_time_data['total_money'], $project->currency_data)?><br>

                                    <b><?=_l('project_overview_expenses') ?>:</b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id], 'field' => 'amount']), $project->currency_data) ?><br>


                                    <b><?=_l('project_overview_expenses_billable') ?>:</b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'billable' => 1], 'field' => 'amount']), $project->currency_data) ?><br>


                                    <b><?=_l('project_overview_expenses_billed') ?>:</b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'invoiceid !=' => 'NULL', 'billable' => 1], 'field' => 'amount']), $project->currency_data)?><br>

                                     <b><?=_l('project_overview_expenses_unbilled') ?>:</b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'invoiceid IS NULL', 'billable' => 1], 'field' => 'amount']), $project->currency_data)?>

                                       </td>

                                       <td>
                                    
                                          <b><?=$project->client_data->company?></b><br>
                                          <b><?=$project->client_data->address?></b><br>
                                         <?php if (!empty($project->client_data->city)) { 
                                                echo $project->client_data->city;
                                         } ?>

                                         <?php if (!empty($project->client_data->state)) {
                                              echo $project->client_data->state;
                                          }?>
                                          <?php $country = get_country_short_name($project->client_data->country);?>
                                          <?php 
                                          if (!empty($country)) {
                                              echo "<br>".$country;
                                          }?>

                                         <?php  if (!empty($project->client_data->zip)) {
                                              echo $project->client_data->zip;
                                             }

                                          if (!empty($project->client_data->phonenumber)) {
                                              echo  "<br />".$project->client_data->phonenumber;
                                          }

                                          if (!empty($project->client_data->vat)) {
                                             echo  "<br>"._l('client_vat_number') . ': ' . $project->client_data->vat;
                                          } ?>
                                       </td>
                                    </tr>
                                 </tbody>
                              </table>

<!------------------------------->

<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
           <?=ucwords(_l('project_members_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
         <table class="table dt-table table-bordered table-striped" width="100%"  cellspacing="0" cellpadding="5" border="1">
            <thead>
               <tr >
                  <th><b><?=_l('project_member')?></b></th>
                  <th><b><?=_l('staff_total_task_assigned')?></b></th>
                  <th><b><?=_l('staff_total_comments_on_tasks')?></b></th>
                  <th><b><?=_l('total_project_discussions_created')?></b></th>
                  <th><b><?=_l('total_project_discussions_comments')?></b></th>
                  <th><b><?=_l('total_project_files')?></b></th>
                  <th><b><?=_l('time_h')?></b></th>
                  <th><b><?=_l('time_decimal')?></b></th>
               </tr>

            </thead>
            <tbody>
              <?php foreach ($members as $member) {?>

               <tr>
                  <td><?=get_staff_full_name($member['staff_id']) ?></td>
                  <td><?=total_rows(db_prefix() . 'tasks', 'rel_type="project" AND rel_id="' . $project->id . '" AND id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid="' . $member['staff_id'] . '")')?></td>
                  <td><?=total_rows(db_prefix() . 'task_comments', 'staffid = ' . $member['staff_id'] . ' AND taskid IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id="' . $project->id . '")') ?></td>
                  <td><?=total_rows(db_prefix() . 'projectdiscussions', ['staff_id' => $member['staff_id'], 'project_id' => $project->id]) ?></td>
                  <td><?= total_rows(db_prefix() . 'projectdiscussioncomments', 'staff_id=' . $member['staff_id'] . ' AND discussion_id IN (SELECT id FROM ' . db_prefix() . 'projectdiscussions WHERE project_id=' . $project->id . ')')  ?></td>
                  <td><?=total_rows(db_prefix() . 'project_files', ['staffid' => $member['staff_id'], 'project_id' => $project->id]) ?></td>

                  <?php
                  $member_tasks_assigned = $this->tasks_model->get_tasks_by_staff_id($member['staff_id'], ['rel_id' => $project->id, 'rel_type' => 'project']);
                      $seconds               = 0;
                      foreach ($member_tasks_assigned as $member_task) {
                          $seconds += $this->tasks_model->calc_task_total_time($member_task['id'], ' AND staff_id=' . $member['staff_id']);
                      }
                   ?>
                  <td><?=seconds_to_time_format($seconds)  ?></td>
                  <td><?=sec2qty($seconds)  ?></td>
               </tr>
            <?php } ?>
            </tbody>
         </table>
      </div>
    </div>
  </div>

  <!---------------hearings ------------------------------------->

  <?php foreach ($hearing_types as $key => $hearing_type) {
    $num_rows = total_rows('tblhearings',array('project_id'=>$project->id,'h_instance_id'=>$hearing_type['id']));
											
    if($num_rows > 0){ 
      $court_no = $hearing_type['id'].'_no';
   ?>
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$hearing_type['id']?>">
          <?=$hearing_type['name']?>
        </a>
      </h4>
    </div>
    <div id="collapse<?=$hearing_type['id']?>" class="panel-collapse collapse">
      <div class="panel-body">
         <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="asc">
        <thead>
    <tr> 
               
      <th><?php echo _l('hearing_date'); ?></th>
      <th><?php echo _l('hearing_list_subject'); ?></th>
      <th><?php echo _l('client'); ?></th>
      <th><?php echo _l('casediary_casenumber'); ?></th>
    <!--  <th><?php echo _l($court_no); ?></th>-->
      <th><?php echo _l('casediary_oppositeparty'); ?></th>
      <th><?php echo _l('lawyer_attending'); ?></th>
      <th><?php echo _l('court_decision'); ?></th>

    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($hearings as $row_hearing) {
        if($row_hearing->h_instance_id == $hearing_type['id']){
        ?>
        <tr>
        <td><?=_d($row_hearing->hearing_date)?></td>
        <td><?=$row_hearing->subject?></td>
        <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?=$project->client_data->company?></a></td>
        <td><?=$row_hearing->court_no?></td>
        <td><?=$row_hearing->opposite_party_name?></td>
         <td><?=get_staff_full_name($row_hearing->lawyer_id)?></td>
        <td><?=$row_hearing->proceedings?></td>
      </tr>
      <?php }} ?>  
  </tbody>
 </table>
      </div>
    </div>
  </div>
<?php } }?>
<!-------------------------------------------------------------------->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
         <?=ucwords(_l('detailed_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
       <?php 
       $html = '';

         $html .= '<table class="table dt-table table-hover table-bordered table-striped "  width="100%"  cellspacing="0" cellpadding="5" border="1">';
         $html .= '<thead>';
         $html .= '<tr>';
         $html .= '<th width="26.12%"><b>' . _l('tasks_dt_name') . '</b></th>';
         $html .= '<th width="12%"><b>' . _l('total_task_members_assigned') . '</b></th>';
         $html .= '<th width="12%"><b>' . _l('total_task_members_followers') . '</b></th>';
         $html .= '<th width="9.28%"><b>' . _l('task_single_start_date') . '</b></th>';
         $html .= '<th width="9.28%"><b>' . _l('task_single_due_date') . '</b></th>';
         $html .= '<th width="7%"><b>' . _l('task_status') . '</b></th>';
         $html .= '<th width="14.28%"><b>' . _l('time_h') . '</b></th>';
         $html .= '<th width="10%"><b>' . _l('time_decimal') . '</b></th>';
         $html .= '</tr>';
         $html .= '</thead>';
         $html .= '<tbody>';
         foreach ($tasks as $task) {
             $html .= '<tr style="color:#4a4a4a;">';
             $html .= '<td width="26.12%">' . $task['name'] . '</td>';
             $html .= '<td width="12%">' . total_rows(db_prefix() . 'task_assigned', ['taskid' => $task['id']]) . '</td>';
             $html .= '<td width="12%">' . total_rows(db_prefix() . 'task_followers', ['taskid' => $task['id']]) . '</td>';
             $html .= '<td width="9.28%">' . _d($task['startdate']) . '</td>';
             $html .= '<td width="9.28%">' . (is_date($task['duedate']) ? _d($task['duedate']): '') . '</td>';
             $html .= '<td width="7%">' . format_task_status($task['status'], true, true) . '</td>';
             $html .= '<td width="14.28%">' . seconds_to_time_format($task['total_logged_time']) . '</td>';
             $html .= '<td width="10%">' . sec2qty($task['total_logged_time']) . '</td>';

             $html .= '</tr>';
         }
         $html .= '</tbody>';
         $html .= '</table>';
         echo $html;
        ?>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
         <?=ucwords(_l('timesheets_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body">
         <?php 
         $html = '';
         $html .= '<table class="table dt-table table-hover table-bordered table-striped" width="100%"  cellspacing="0" cellpadding="5" border="1">';
         $html .= '<thead>';
         $html .= '<tr >';
         $html .= '<th width="16.66%"><b>' . _l('project_timesheet_user') . '</b></th>';
         $html .= '<th width="16.66%"><b>' . _l('project_timesheet_task') . '</b></th>';
         $html .= '<th width="16.66%"><b>' . _l('project_timesheet_start_time') . '</b></th>';
         $html .= '<th width="16.66%"><b>' . _l('project_timesheet_end_time') . '</b></th>';
         $html .= '<th width="16.66%"><b>' . _l('time_h') . '</b></th>';
         $html .= '<th width="16.66%"><b>' . _l('time_decimal') . '</b></th>';
         $html .= '</tr>';
         $html .= '</thead>';
         $html .= '<tbody>';
         foreach ($timesheets as $timesheet) {
             $html .= '<tr style="color:#4a4a4a;">';
             $html .= '<td>' . get_staff_full_name($timesheet['staff_id']) . '</td>';
             $html .= '<td>' . $timesheet['task_data']->name . '</td>';
             $html .= '<td>' . _dt($timesheet['start_time'], true) . '</td>';
             $html .= '<td>' . (!is_null($timesheet['end_time']) ? _dt($timesheet['end_time'], true) : '') . '</td>';
             $html .= '<td>' . seconds_to_time_format($timesheet['total_spent']) . '</td>';
             $html .= '<td>' . sec2qty($timesheet['total_spent']) . '</td>';

             $html .= '</tr>';
         }
         $html .= '</tbody>';
         $html .= '</table>';
         echo $html;?>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
         <?=ucwords(_l('project_milestones_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse">
      <div class="panel-body">
        <?php 
         $html = '';
         $html .= '<table class="table dt-table table-hover table-bordered table-striped"  width="100%"  cellspacing="0" cellpadding="5" border="1">';
         $html .= '<thead>';
         $html .= '<tr >';
         $html .= '<th width="20%"><b>' . _l('milestone_name') . '</b></th>';
         $html .= '<th width="30%"><b>' . _l('milestone_description') . '</b></th>';
         $html .= '<th width="15%"><b>' . _l('milestone_due_date') . '</b></th>';
         $html .= '<th width="15%"><b>' . _l('total_tasks_in_milestones') . '</b></th>';
         $html .= '<th width="20%"><b>' . _l('milestone_total_logged_time') . '</b></th>';
         $html .= '</tr>';
         $html .= '</thead>';
         $html .= '<tbody>';
         foreach ($milestones as $milestone) {
             $html .= '<tr style="color:#4a4a4a;">';
             $html .= '<td width="20%">' . $milestone['name'] . '</td>';
             $html .= '<td width="30%">' . $milestone['description'] . '</td>';
             $html .= '<td width="15%">' . _d($milestone['due_date']) . '</td>';
             $html .= '<td width="15%">' . total_rows(db_prefix() . 'tasks', ['milestone' => $milestone['id'], 'rel_id' => $project->id, 'rel_type' => 'project']) . '</td>';
             $html .= '<td width="20%">' . seconds_to_time_format($milestone['total_logged_time']) . '</td>';
             $html .= '</tr>';
         }
         $html .= '</tbody>';
         $html .= '</table>';
         echo $html;
         ?>
      </div>
    </div>
  </div>

<!-- 
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
         <?=ucwords(_l('detailed_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseSix" class="panel-collapse collapse">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div> -->


</div>

<!------------------------------->











   

                           </div>
                                    
                                   
                        </div>
                     <?php } ?>
                     </div>
                    </div>
                </div>
            </div>
        </div>
        <?php init_tail(); ?>
</body>
</html>
<script type="text/javascript">
   $('#case_id').change(function(){
        var case_id = $(this).val();
        window.location.href= admin_url+'reports/matter_detailed/'+case_id;
   });
</script>

<style type="text/css">
   .panel-heading .accordion-toggle:after {
    /* symbol for "opening" panels */
    font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
    content: "\e114";    /* adjust as needed, taken from bootstrap.css */
    float: right;        /* adjust as needed */
    color: grey;         /* adjust as needed */
}
.panel-heading .accordion-toggle.collapsed:after {
    /* symbol for "collapsed" panels */
    content: "\e080";    /* adjust as needed, taken from bootstrap.css */
}
</style>