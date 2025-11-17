<?php defined('BASEPATH') or exit('No direct script access allowed');
ob_start();
?>
<li id="top_search" class="dropdown" data-toggle="tooltip" data-placement="bottom" data-title="<?php echo _l('search_by_tags'); ?>">
   <input type="search" id="search_input" class="form-control" placeholder="<?php echo _l('top_search_placeholder'); ?>">
   <div id="search_results">
   </div>
   <ul class="dropdown-menu search-results animated fadeIn no-mtop search-history" id="search-history">
   </ul>
</li>
<li id="top_search_button">
   <button class="btn"><i class="fa fa-search"></i></button>
</li>
<?php
$top_search_area = ob_get_contents();
ob_end_clean();
?>
<div id="header">
   <div class="hide-menu"><i class="fa fa-align-left"></i></div>
   <div id="logo">
      <?php get_company_logo(get_admin_uri().'/') ?>
   </div>
   <nav>
      <div class="small-logo">
         <span class="text-primary">
            <?php get_company_logo(get_admin_uri().'/') ?>
         </span>
      </div>
      <div class="mobile-menu">
         <button type="button" class="navbar-toggle visible-md visible-sm visible-xs mobile-menu-toggle collapsed" data-toggle="collapse" data-target="#mobile-collapse" aria-expanded="false">
            <i class="fa fa-chevron-down"></i>
         </button>
         <ul class="mobile-icon-menu">
            <?php
               // To prevent not loading the timers twice
            if(is_mobile()){ ?>
               <li class="dropdown notifications-wrapper header-notifications">
                  <?php $this->load->view('admin/includes/notifications'); ?>
               </li>
               <li class="header-timers">
                  <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown"><i class="fa fa-clock-o fa-fw fa-lg"></i>
                     <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0){ echo ' hide'; }?>"><?php echo count($startedTimers); ?></span>
                  </a>
                  <ul class="dropdown-menu animated fadeIn started-timers-top width300" id="started-timers-top">
                     <?php $this->load->view('admin/tasks/started_timers',array('startedTimers'=>$startedTimers)); ?>
                  </ul>
               </li>
            <?php } ?>
         </ul>
         <div class="mobile-navbar collapse" id="mobile-collapse" aria-expanded="false" style="height: 0px;" role="navigation">
            <ul class="nav navbar-nav">
               <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
               <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a></li>
               <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
               <?php if(is_staff_member()){ ?>
                  <li class="header-newsfeed">
                   <a href="#" class="open_newsfeed mobile">
                     <?php echo _l('whats_on_your_mind'); ?>
                  </a>
               </li>
            <?php } ?>
            <li class="header-logout"><a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a></li>
         </ul>
      </div>
   </div>
   <ul class="nav navbar-nav navbar-right">
   <!-------------New menu end ---------------------->
	   <li class="dropdown li_hide">
          <a href="#" style="background: none;/*max-width:104px;*/font-weight: bold;font-size: 12px;" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">  <?php echo _l('dashboard_string') ?> <span class="caret"></span></a>
          <ul class="dropdown-menu">
            <li class="hide"><a href="<?php echo admin_url('dashboard/index/my_dashboard') ?>"><?php echo _l('my_dashboard'); ?></a></li>
          <?php if(get_option('enable_legaldashboard')==1){ ?>
            <li><a href="<?php echo admin_url('dashboard/index/legal') ?>"><?php echo _l('legal_dashboard'); ?></a></li>
			  <?php }?>
            <li><a href="<?php echo admin_url('dashboard/index/contract') ?>"><?php echo _l('contract_dashboard'); ?></a></li>
                           <li><a href="<?php echo admin_url('dashboard/index/approval') ?>"><?php echo _l('approval_dashboard'); ?></a></li>

           
          </ul>
        </li>
   <li class="icon header-user-profile li_hide" data-toggle="tooltip" title="" data-placement="bottom">
      <a href="#" class=" dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
          <span class=" btn btn-success  font-medium-xs btn-sm text-primary">
         <?php //echo _l('add_new'); ?> <i class="fa fa-plus" aria-hidden="true"></i>
		  </span>
      </a>
      <ul class="dropdown-menu animated fadeIn li_hide">
          <?php if(has_permission('customers','','create')){ ?>
         <li class="header-languages">
               <a href="<?php echo admin_url('clients/client') ?>" tabindex="-1"><?php echo _l('client'); ?></a>
            </li>
          <?php } ?>
         <div class="clearfix"></div>
          <?php if(has_permission('customers','','create')){ ?>
             <li class="divider"></li>
         <li class="header-my-timesheets"><a href="#" onclick="new_quick_opposite_party();return false;"><?php echo _l('q_my_opponent'); ?></a></li>
         
         <?php } ?>
          <?php if(has_permission('contracts','','create')&& !isset($contract)){ ?>
         <li class="divider"></li>
         <!--<li class="header-my-timesheets"><a href="<?php echo admin_url('contracts/contract') ?>"><?php echo _l('contracts'); ?></a></li>-->
         <li class="header-my-timesheets"><a href="#" onclick="new_quick_contract();return false;"><?php echo _l('contracts'); ?></a></li>
         

         <?php } ?>
         <?php if(has_permission('tickets','','create')){ ?>
         <li class="divider"></li>
         <li class="header-my-timesheets"><a href="<?php echo admin_url('tickets/add') ?>"><?php echo _l('ticket'); ?></a></li>
        
         

         <?php } ?>
          <?php if(has_permission('tasks','','create')){ ?>
         <li class="divider"></li>
         <li class="header-my-timesheets"><a href="#" onclick="new_task();return false;"><?php echo _l('task'); ?></a></li>
         <?php } ?>
         <?php if(has_permission('staffs','','create')){ ?>
         <li class="divider"></li>
         <li><a href="<?php echo admin_url('staff/member') ?>"><?php echo _l('als_staff'); ?></a></li>
        <?php } ?>
         <li class="divider"></li>
         <li class="header-my-timesheets"> <a href="#" onclick="new_quick_reminder();return false;"><?php echo _l('reminder'); ?></a></li>
         <li class="divider"></li>

         <li class="header-my-timesheets hide"> <a href="#" onclick="new_ocr();return false;"><?php echo _l('ocr'); ?></a></li>
         <li class="divider"></li>

		  <li class="header-my-timesheets hide"> <a href="#" onclick="new_quick_chatgpt();return false;"><?php echo _l('ai_prompt'); ?></a></li>
		    <?php if(get_option('enable_legaldashboard')==1){ ?>
		    <?php if(has_permission('projects','','create')){ ?>
         <li class="divider"></li>
         <?php $casetypes = get_case_client_types(); ?>
         <li class="dropdown-submenu pull-left header-languages">
            <a href="#" tabindex="-1"><?php echo _l('als_casediary'); ?></a>
               <ul class="dropdown-menu dropdown-menu">
                  <?php foreach($casetypes as $casetype){ ?>
                  <li><a href="<?php echo admin_url('projects/project/?case_type='.$casetype['id']) ?>"><?php echo _l($casetype['id']); ?></a></li>
        		      <?php } ?>
               </ul>
         </li>
         <?php } ?>
          <?php } ?>
        </ul>
      </li>
<!------quickmenu---------------------------->
      <?php
      if(!is_mobile()){
       echo $top_search_area;
    } ?>
    <?php hooks()->do_action('after_render_top_search'); ?>
    
   <?php if(is_staff_member()){ ?>
      <li class="icon header-newsfeed">
         <a href="#" class="open_newsfeed desktop" data-toggle="tooltip" title="<?php echo _l('whats_on_your_mind'); ?>" data-placement="bottom"><i class="fa fa-share fa-fw fa-lg" aria-hidden="true"></i></a>
      </li>
   <?php } ?>
   <li class="icon header-todo">
      <a href="<?php echo admin_url('todo'); ?>" data-toggle="tooltip" title="<?php echo _l('nav_todo_items'); ?>" data-placement="bottom"><i class="fa fa-check-square-o fa-fw fa-lg"></i>
         <span class="label bg-warning icon-total-indicator nav-total-todos<?php if($current_user->total_unfinished_todos == 0){echo ' hide';} ?>"><?php echo $current_user->total_unfinished_todos; ?></span>
      </a>
   </li>
   <li class="icon header-timers timer-button" data-placement="bottom" data-toggle="tooltip" data-title="<?php echo _l('my_timesheets'); ?>">
      <a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown">
         <i class="fa fa-clock-o fa-fw fa-lg" aria-hidden="true"></i>
         <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0){ echo ' hide'; }?>">
            <?php echo count($startedTimers); ?>
         </span>
      </a>
      <ul class="dropdown-menu animated fadeIn started-timers-top width350" id="started-timers-top">
         <?php $this->load->view('admin/tasks/started_timers',array('startedTimers'=>$startedTimers)); ?>
      </ul>
   </li>
   <li class="dropdown notifications-wrapper header-notifications" data-toggle="tooltip" title="<?php echo _l('nav_notifications'); ?>" data-placement="bottom">
      <?php $this->load->view('admin/includes/notifications'); ?>
   </li>

   <li class="icon header-user-profile" data-toggle="tooltip" title="<?php echo get_staff_full_name(); ?>" data-placement="bottom">
      <a href="#" class="dropdown-toggle profile" data-toggle="dropdown" aria-expanded="false">
         <?php echo staff_profile_image($current_user->staffid,array('img','img-responsive','staff-profile-image-small','pull-left')); ?>
      </a>
      <ul class="dropdown-menu animated fadeIn">
         <li class="header-my-profile"><a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_my_profile'); ?></a></li>
         <li class="header-my-timesheets"><a href="<?php echo admin_url('staff/timesheets'); ?>"><?php echo _l('my_timesheets'); ?></a></li>
         <li class="header-edit-profile"><a href="<?php echo admin_url('staff/edit_profile'); ?>"><?php echo _l('nav_edit_profile'); ?></a></li>
         <?php if(!is_language_disabled()){ ?>
            <li class="dropdown-submenu pull-left header-languages">
               <a href="#" tabindex="-1"><?php echo _l('language'); ?></a>
               <ul class="dropdown-menu dropdown-menu">
                  <li class="<?php if($current_user->default_language == ""){echo 'active';} ?>"><a href="<?php echo admin_url('staff/change_language'); ?>"><?php echo _l('system_default_string'); ?></a></li>
                  <?php foreach($this->app->get_available_languages() as $user_lang) { ?>
                     <li<?php if($current_user->default_language == $user_lang){echo ' class="active"';} ?>>
                     <a href="<?php echo admin_url('staff/change_language/'.$user_lang); ?>"><?php echo ucfirst($user_lang); ?></a>
                  <?php } ?>
               </ul>
            </li>
         <?php } ?>
         <li class="header-logout">
            <a href="#" onclick="logout(); return false;"><?php echo _l('nav_logout'); ?></a>
         </li>
      </ul>
   </li>
</ul>
</nav>
</div>
<div id="mobile-search" class="<?php if(!is_mobile()){echo 'hide';} ?>">
   <ul>
      <?php
      if(is_mobile()){
       echo $top_search_area;
    } ?>
 </ul>
</div>
<?php $this->load->view('admin/includes/quick_reminder_modal');?>
<?php $this->load->view('admin/includes/quick_chatgpt_modal');?>
<?php $this->load->view('admin/oppositeparties/quick_opposite_party_modal'); ?>
