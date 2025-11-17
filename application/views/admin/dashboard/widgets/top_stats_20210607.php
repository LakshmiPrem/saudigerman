<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget relative" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('quick_stats'); ?>">
      <div class="widget-dragger"></div>
      <div class="row">
      <?php
         $initial_column = 'col-lg-3';
         if(!is_staff_member() && ((!has_permission('invoices','','view') && !has_permission('invoices','','view_own') && (get_option('allow_staff_view_invoices_assigned') == 0
           || (get_option('allow_staff_view_invoices_assigned') == 1 && !staff_has_assigned_invoices()))))) {
            $initial_column = 'col-lg-6';
         } else if(!is_staff_member() || (!has_permission('invoices','','view') && !has_permission('invoices','','view_own') && (get_option('allow_staff_view_invoices_assigned') == 1 && !staff_has_assigned_invoices()) || (get_option('allow_staff_view_invoices_assigned') == 0 && (!has_permission('invoices','','view') && !has_permission('invoices','','view_own'))))) {
            $initial_column = 'col-lg-4';
         }
      ?>
         <?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own') || (get_option('allow_staff_view_invoices_assigned') == '1' && staff_has_assigned_invoices())){ ?>
         <div class="quick-stats-invoices col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $total_invoices = total_rows(db_prefix().'invoices','status NOT IN (5,6)'.(!has_permission('invoices','','view') ? ' AND ' . get_invoices_where_sql_for_staff(get_staff_user_id()) : ''));
                  $total_invoices_awaiting_payment = total_rows(db_prefix().'invoices','status NOT IN (2,5,6)'.(!has_permission('invoices','','view') ? ' AND ' . get_invoices_where_sql_for_staff(get_staff_user_id()) : ''));
                  $percent_total_invoices_awaiting_payment = ($total_invoices > 0 ? number_format(($total_invoices_awaiting_payment * 100) / $total_invoices,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-balance-scale"></i> <?php echo _l('invoices_awaiting_payment'); ?>
                  <span class="pull-right"><?php echo $total_invoices_awaiting_payment; ?> / <?php echo $total_invoices; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_total_invoices_awaiting_payment; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_total_invoices_awaiting_payment; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <?php if(is_staff_member()){ ?>
         <div class="quick-stats-leads col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $where = '';
                  if(!is_admin()){
                    $where .= '(addedfrom = '.get_staff_user_id().' OR assigned = '.get_staff_user_id().')';
                  }
                  // Junk leads are excluded from total
                  $total_leads = total_rows(db_prefix().'leads',($where == '' ? 'junk=0' : $where .= ' AND junk =0'));
                  if($where == ''){
                   $where .= 'status=1';
                  } else {
                   $where .= ' AND status =1';
                  }
                  $total_leads_converted = total_rows(db_prefix().'leads',$where);
                  $percent_total_leads_converted = ($total_leads > 0 ? number_format(($total_leads_converted * 100) / $total_leads,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-tty"></i> <?php echo _l('leads_converted_to_client'); ?>
                  <span class="pull-right"><?php echo $total_leads_converted; ?> / <?php echo $total_leads; ?></span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_total_leads_converted; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_total_leads_converted; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(2);
                  if(!has_permission('projects','','view')){
                    $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
                  }
                  $total_projects = total_rows(db_prefix().'projects',$_where);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status = 2';
                  $total_projects_in_progress = total_rows(db_prefix().'projects',$where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('projects') . ' ' . $project_status['name']; ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  if (!has_permission('tasks', '', 'view')) {
                    $_where = db_prefix().'tasks.id IN (SELECT taskid FROM '.db_prefix().'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';
                  }
                  $total_tasks = total_rows(db_prefix().'tasks',$_where);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status != '.Tasks_model::STATUS_COMPLETE;
                  $total_not_finished_tasks = total_rows(db_prefix().'tasks',$where);
                  $percent_not_finished_tasks = ($total_tasks > 0 ? number_format(($total_not_finished_tasks * 100) / $total_tasks,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-tasks"></i> <?php echo _l('tasks_not_finished'); ?> <span class="pull-right">
                  <?php echo $total_not_finished_tasks; ?> / <?php echo $total_tasks; ?>
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-default no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_tasks; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_tasks; ?>">
                  </div>
               </div>
            </div>
         </div>
      </div>

      <?php //print_r($projects_); ?>
      <div class="row" style="margin-left:0px; margin-right: 0px;"> 
         <div class="panel_s">
            <div class="panel-body">




               <div class="row">
                  <div class="col-md-12">


                     <div class="form-group has-search">
                      <input type="text" id="search_"  class="form-control" placeholder="Search">
                     </div>

                      <div class="no_result hide"><h5>No result found..</h5></div>
                    <div class="paginate">
                      <div class="items">
                     <?php foreach ($projects_ as $project_) { ?>
                        <div>
                     <div class="col-sm-3 searchCard"  data-string="<?php echo $project_['name'];  ?> <?php echo $project_['company']; ?> <?php echo _l($project_['case_type']); ?> ">
                        <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.16), 0 2px 10px 0 rgba(0, 0, 0, 0.12); padding: 10px; margin: 8px;" >
                           <div class="card-body">
                             <h4 class="card-title" ><strong><?php echo $project_['name']; ?></strong></h4>
                             <p class="card-text" style="margin:  0 0 4px;">Case No :<?php echo $project_['name']; ?> </p>
                             <p class="card-text" style="margin:  0 0 4px;">Client Name :<?php echo $project_['company']; ?> </p>
                             <p class="card-text" style="margin:  0 0 4px;">Start Date :<?php echo _d($project_['start_date']); ?> </p>
                             <p class="card-text" style="margin:  0 0 4px;">
                              <span class="label label inline-block" style="color: red;border: 1px solid red;"><?php echo _l($project_['case_type']); ?></span> | <?php $status = get_project_status_by_id($project_['status']);?> <span class="label label inline-block project-status-"<?php $project_['status'] ?> style="color:<?=$status['color']?>;border:1px solid <?=$status['color']?>"> <?=$status['name']?></span> </p>
                            <img style="float: right;position: absolute; bottom: 42px;
    right: 36px;" src="<?php echo contact_profile_image_url(1,'thumb'); ?>" id="contact-img" class="staff-profile-image-small">
                             <!-- <img style="height:50px;width:100px;position: relative;
        bottom: 16; 
        right: 32;
        float: right;" class="card-img-bottom" src="https://static.vecteezy.com/system/resources/thumbnails/000/623/239/small/auto_car-16.jpg" alt="Card image cap"> -->               
                           </div>
                         </div>
                     </div>  
                  </div>
                     <?php } ?>
                  </div>
                  
                      <div class="col-md-12" style="display:inline-block" > 
                       <div class="pager" style=" display: inline-block;">
            <div class="previousPage">&lsaquo;</div>
            <div class="pageNumbers" style=" color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;"></div>
            <div class="nextPage">&rsaquo;</div>
         </div>
                     </div> 
                     </div>
                  </div><!-- col-12 -->

               </div><!-- row --> 
            </div><!-- panel_s body--> 
         </div> <!-- panel_s -->    
      </div>
   </div>

<style type="text/css">

.items div 
         {
            border: 1px solid gray;
            margin: 5px;
            padding: 10px;
         }

         .pager div
         {
            float: left;
            border: 1px solid gray;
            margin: 5px;
            padding: 10px;
         }

         .pager div.disabled
         {
            opacity: 0.25;
         }

         .pager .pageNumbers a
         {
            display: inline-block;
            padding: 0 10px;
            color: gray;
         }

         .pager .pageNumbers a.active
         {
            color: orange;
         }

         .pager 
         {
            overflow: hidden;
         }

         .paginate-no-scroll .items div
         {
            height: 250px;
         }
</style>
<style type="text/css">

./*pagination {
  display: inline-block;
}
   .pagination a  {
  color: black;
  float: left;
  padding: 8px 16px;
  text-decoration: none;
}*/

.has-search .form-control {
    padding-left: 2.375rem;
}

.has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 2.375rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
}



</style>

