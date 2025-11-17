<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="widget relative <?php if($dashtype==''){echo ' hide';} ?>" id="widget-<?php echo create_widget_id(); ?>" data-name="<?php echo _l('quick_stats'); ?>">
      <div class="widget-dragger"></div>
	
	<?php if($dashtype=='legal'){?>
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
        <!--  <?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own') || (get_option('allow_staff_view_invoices_assigned') == '1' && staff_has_assigned_invoices())){ ?>
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
         <?php } ?> -->
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
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('cases') . ' ' . $project_status['name']; ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>

         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(3);
                  if(!has_permission('projects','','view')){
                    $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
                  }
                  $total_projects = total_rows(db_prefix().'projects',$_where);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status = 3';
                  $total_projects_in_progress = total_rows(db_prefix().'projects',$where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('cases') . ' ' . $project_status['name']; ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(1);
                  if(!has_permission('projects','','view')){
                    $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
                  }
                  $total_projects = total_rows(db_prefix().'projects',$_where);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status = 1';
                  $total_projects_in_progress = total_rows(db_prefix().'projects',$where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('cases') . ' ' . $project_status['name']; ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
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
	      <div class="row">
         <!-- hearing and tommorrow hearing -->
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>" id="quick_hearings" onClick="matter_unattended_hearings();return false;" style="cursor: pointer">
            <div class="top_stats_wrapper">
               <?php
                 // $_where = 'DATE(hearing_date) <= "'.date('Y-m-d').'" AND hearing_date< "'.date('Y-m-d h:i:s').'"';
				  $_where = 'DATE(hearing_date) <= "'.date('Y-m-d').'" AND date(hearing_date)<= "'.date('Y-m-d').'"';
                 /* if (!has_permission('projects', '', 'view')) {
                    $_where = db_prefix().'projects.id IN (SELECT project_id FROM '.db_prefix().'hearings WHERE addedfrom = ' . get_staff_user_id() . ')';
                  }*/
			
                  $total_uhearings = total_rows(db_prefix().'hearings',$_where);
				
					 $where = ($_where == '' ? '' : $_where.' AND ').'(proceedings IS NULL OR proceedings =" ") and (mention_hearing="hearing")';
				 // $where2 = ($where == '' ? '' : $where.' AND ').'DATE(hearing_date) < "'.$newdate.'"';
            
                  $total_not_finished_uhearings= total_rows(db_prefix().'hearings',$where);
                  $percent_not_finished_uhearings = ($total_uhearings > 0 ? number_format(($total_not_finished_uhearings * 100) / $total_uhearings,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-bell-o" style="color: red"></i> <?php echo _l('unattended_hearing'); ?> <span class="pull-right label label-danger">
                  <?php echo $total_not_finished_uhearings; ?>
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_uhearings; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_uhearings; ?>">
                  </div>
               </div>
            </div>
         </div> 
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>" id="quick_hearings" onClick="matter_tomorrow_hearings();return false;" style="cursor: pointer">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  if (!has_permission('projects', '', 'view')) {
                    $_where = db_prefix().'projects.id IN (SELECT project_id FROM '.db_prefix().'hearings WHERE addedfrom = ' . get_staff_user_id() . ')';
                  }
				// $where = ($_where == '' ? '' : $_where.' AND ').'(proceedings IS NULL OR proceedings =" ")';
				 $where = ($_where == '' ? '' : $_where);
                  $total_hearings = total_rows(db_prefix().'hearings',$where);
				
				 $newdate = date("Y-m-d",strtotime ( '+1 day' , strtotime ( date('Y-m-d') ) )) ;
                  $where2 = ($where == '' ? '' : $where.' AND ').'DATE(hearing_date) = "'.$newdate.'" and (mention_hearing="hearing")';
                  $total_not_finished_hearings= total_rows(db_prefix().'hearings',$where2);
                  $percent_not_finished_hearings = ($total_hearings > 0 ? number_format(($total_not_finished_hearings * 100) / $total_hearings,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-bell-o" style="color: red"></i> <?php echo _l('tomorrow_hearings'); ?> <span class="pull-right label label-danger">
                  <?php echo $total_not_finished_hearings; ?> 
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_hearings; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_hearings; ?>">
                  </div>
               </div>

            </div>
         </div>
<!-- mention and tommorrow mention -->
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>" id="quick_hearings" onClick="matter_unattended_hearings();return false;" style="cursor: pointer">
            <div class="top_stats_wrapper">
               <?php
                   $_where = 'DATE(hearing_date) <= "'.date('Y-m-d').'" AND date(hearing_date)<= "'.date('Y-m-d').'"';
				
                 /* if (!has_permission('projects', '', 'view')) {
                    $_where = db_prefix().'projects.id IN (SELECT project_id FROM '.db_prefix().'hearings WHERE addedfrom = ' . get_staff_user_id() . ')';
                  }*/
			
                  $total_uhearings = total_rows(db_prefix().'hearings',$_where);
				
					 $where = ($_where == '' ? '' : $_where.' AND ').'(proceedings IS NULL OR proceedings =" ") and (mention_hearing="mention")';
				 // $where2 = ($where == '' ? '' : $where.' AND ').'DATE(hearing_date) < "'.$newdate.'"';
            
                  $total_not_finished_uhearings= total_rows(db_prefix().'hearings',$where);
                  $percent_not_finished_uhearings = ($total_uhearings > 0 ? number_format(($total_not_finished_uhearings * 100) / $total_uhearings,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-bell-o" style="color: red"></i> <?php echo _l('unattended_mention'); ?> <span class="pull-right label label-danger">
                  <?php echo $total_not_finished_uhearings; ?>
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_uhearings; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_uhearings; ?>">
                  </div>
               </div>
            </div>
         </div> 
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>" id="quick_hearings" onClick="matter_tomorrow_hearings();return false;" style="cursor: pointer">
            <div class="top_stats_wrapper">
               <?php
                  $_where = '';
                  if (!has_permission('projects', '', 'view')) {
                    $_where = db_prefix().'projects.id IN (SELECT project_id FROM '.db_prefix().'hearings WHERE addedfrom = ' . get_staff_user_id() . ')';
                  }
				// $where = ($_where == '' ? '' : $_where.' AND ').'(proceedings IS NULL OR proceedings =" ")';
				 $where = ($_where == '' ? '' : $_where);
                  $total_hearings = total_rows(db_prefix().'hearings',$where);
				
				 $newdate = date("Y-m-d",strtotime ( '+1 day' , strtotime ( date('Y-m-d') ) )) ;
                  $where2 = ($where == '' ? '' : $where.' AND ').'DATE(hearing_date) = "'.$newdate.'" and (mention_hearing="mention")';
                  $total_not_finished_hearings= total_rows(db_prefix().'hearings',$where2);
                  $percent_not_finished_hearings = ($total_hearings > 0 ? number_format(($total_not_finished_hearings * 100) / $total_hearings,2) : 0);
                  ?>
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-bell-o" style="color: red"></i> <?php echo _l('tomorrow_mention'); ?> <span class="pull-right label label-danger">
                  <?php echo $total_not_finished_hearings; ?> 
                  </span>
               </p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-danger no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $percent_not_finished_hearings; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_not_finished_hearings; ?>">
                  </div>
               </div>
            </div>
         </div>
         
         <div class="clearfix"></div>
      </div> 
	<?php } ?>
	<?php if($dashtype=='contract'){?>
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
        <!--  <?php if(has_permission('invoices','','view') || has_permission('invoices','','view_own') || (get_option('allow_staff_view_invoices_assigned') == '1' && staff_has_assigned_invoices())){ ?>
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
         <?php } ?> -->
          <?php  if(has_permission('contracts','','view')){?>
         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
                    <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(2);
                  if(!has_permission('contracts','','view')){
                    $_where = 'id IN (SELECT contractid FROM '.db_prefix().'contracts_assigned WHERE staff_id='.get_staff_user_id().')';
                  }
			
                  $total_projects = total_rows(db_prefix().'contracts',$_where);
                 // $where = ($_where == '' ? '' : $_where.' AND ').'status = 2';
                  $total_projects_in_progress =count_active_contracts();// total_rows(db_prefix().'projects',$where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
                    <div class="top_stats_wrapper"  style="border: 1px solid <?php echo $project_status['color']; ?>">
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('contracts') . ' ' . _l('contract_summary_active'); ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>

         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
                  <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(1);
                  if(!has_permission('contracts','','view')){
                    $_where = 'id IN (SELECT contractid FROM '.db_prefix().'contracts_assigned WHERE staff_id='.get_staff_user_id().')';
                  }
                  $total_projects = total_rows(db_prefix().'contracts',$_where);
                 // $where = ($_where == '' ? '' : $_where.' AND ').'status = 3';
                  $total_projects_in_progress =count_expired_contracts();// total_rows(db_prefix().'projects',$where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
                     <div class="top_stats_wrapper"  style="border: 1px solid <?php echo $project_status['color']; ?>">
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('contracts') . ' ' . _l('contract_summary_expired'); ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>
         <div class="quick-stats-projects col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
             <?php
                  $_where = '';
                  $project_status = get_project_status_by_id(1);
                 if(!has_permission('contracts','','view')){
                    $_where = 'id IN (SELECT contractid FROM '.db_prefix().'contracts_assigned WHERE staff_id='.get_staff_user_id().')';
                  }
                  $total_projects = total_rows(db_prefix().'contracts',$_where);
                  $where = ($_where == '' ? '' : $_where.' AND ').'status =4';
                  $total_projects_in_progress = count_recently_created_contracts();//total_rows(db_prefix().'projects',$where);
                  $percent_in_progress_projects = ($total_projects > 0 ? number_format(($total_projects_in_progress * 100) / $total_projects,2) : 0);
                  ?>
              <div class="top_stats_wrapper"  style="border: 1px solid <?php echo $project_status['color']; ?>">
               <p class="text-uppercase mtop5"><i class="hidden-sm fa fa-cubes"></i> <?php echo _l('contracts') . ' ' . _l('contract_summary_recently_added'); ?><span class="pull-right"><?php echo $total_projects_in_progress; ?> / <?php echo $total_projects; ?></span></p>
               <div class="clearfix"></div>
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar no-percent-text not-dynamic" style="background:<?php echo $project_status['color']; ?>" role="progressbar" aria-valuenow="<?php echo $percent_in_progress_projects; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $percent_in_progress_projects; ?>">
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>
         <?php   if(has_permission('task','','view')){?>
         <div class="quick-stats-tasks col-xs-12 col-md-6 col-sm-6 <?php echo $initial_column; ?>">
            <div class="top_stats_wrapper" style="border: 1px solid red;">
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
         <?php }?>
      </div>
<?php }?>
<?php if($dashtype=='approval'){?>
  <div style="background:#f9fafb; border:1px solid #ddd; border-radius:10px; padding:25px 25px 40px 25px; margin-top:20px; box-shadow:0 2px 8px rgba(0,0,0,0.05);">
  <h4 style="margin-bottom:20px; font-weight:600; color:#333;"><?php echo _l('contract_summary_heading'); ?></h4>

  <div class="row" id="contract_summary" style="display:flex; justify-content:center; gap:20px; flex-wrap:wrap;">
    
    <a href="<?php echo admin_url('contracts?type=payable'); ?>" style="text-decoration:none; color:inherit;">
      <div style="flex:1; min-width:150px; max-width:200px; text-align:center; background:#fff; padding:25px 20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); transition:transform 0.2s;">
        <span style="display:block; color:#666; font-weight:500;"><?php echo _l('is_payable'); ?></span>
        <h3 style="color:#3f51b5; font-weight:700; margin:10px 0;"><?php echo $contracts_payable_count; ?></h3>
      </div>
    </a>

    <a href="<?php echo admin_url('contracts?type=receivable'); ?>" style="text-decoration:none; color:inherit;">
      <div style="flex:1; min-width:150px; max-width:200px; text-align:center; background:#fff; padding:25px 20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); transition:transform 0.2s;">
        <span style="display:block; color:#666; font-weight:500;"><?php echo _l('is_receivable'); ?></span>
        <h3 style="color:#4caf50; font-weight:700; margin:10px 0;"><?php echo $contracts_receivable_count; ?></h3>
      </div>
    </a>

    <a href="<?php echo admin_url('contracts?type=trash'); ?>" style="text-decoration:none; color:inherit;">
      <div style="flex:1; min-width:150px; max-width:200px; text-align:center; background:#fff; padding:25px 20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); transition:transform 0.2s;">
        <span style="display:block; color:#666; font-weight:500;"><?php echo _l('trash'); ?></span>
        <h3 style="color:#ff9800; font-weight:700; margin:10px 0;"><?php echo $contracts_others_count; ?></h3>
      </div>
    </a>

    <!-- SIGN NOW button (same height and width as others) -->
    <a href="<?php echo admin_url('contracts?type=unsigned'); ?>" style="text-decoration:none; color:inherit;">
      <div style="flex:1; min-width:150px; max-width:200px; text-align:center; background:#fff; padding:25px 20px; border-radius:10px; box-shadow:0 2px 6px rgba(0,0,0,0.1); transition:transform 0.2s;">
        <div style="width:60px; height:40px; margin:0 auto 10px auto; background:#3f00ff; border-radius:50%; display:flex; align-items:center; justify-content:center;">
          <i class="fa fa-pencil" style="color:#fff; font-size:26px;"></i>
        </div>
        <span style="display:block; font-weight:700; color:#1a1a3c;"><?php echo _l('sign_now'); ?></span>
      </div>
    </a>

  </div>
</div>



   <?php }?>
   
<?php if($dashtype!='legal' && $dashtype!='contract'&& $dashtype!='approval'){?>
<?php ######### first row Start ############## ?>
      <?php  $this->load->view('admin/dashboard/widgets/first_row'); ?>
<?php ######### first row End  ############## ?>

 <?php ######### Second row Start ############## ?>
      <?php  //$this->load->view('admin/dashboard/widgets/second_row'); ?>
<?php ######### Second row End  ############## ?>

<?php ######### Third row Start ############## ?>
      <?php  //$this->load->view('admin/dashboard/widgets/third_row'); ?>
<?php ######### Third row End  ############## ?>

<?php ######### fourth row Start ############## ?>
      <?php  //$this->load->view('admin/dashboard/widgets/fourth_row'); ?>

<?php ######### Third row Start ############## ?>
<?php  //$this->load->view('admin/dashboard/widgets/fifth_row'); ?>
<?php ######### Third row End  ############## ?>
<?php } ?>
   <?php ######### fourth row End  ############## ?>
<?php if($dashtype=='legal' || $dashtype=='contract'){?>
<?php $this->load->view('admin/dashboard/widgets/summary'); ?>
<?php } ?>
<style type="text/css">
  
  .alen-ul{
   margin-bottom: 12px;
  }
  .alen-p{
   margin: 0 0 5px;
  }
</style>
