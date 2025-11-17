<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/post_likes.php'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/post_comment_likes.php'); ?>
<?php include_once(APPPATH . 'views/admin/includes/modals/ocr_modal.php'); ?>
<div id="event"></div>
<div id="newsfeed" class="animated fadeIn hide" <?php if($this->session->flashdata('newsfeed_auto')){echo 'data-newsfeed-auto';} ?>>
</div>
<!-- Task modal view -->
<div class="modal fade task-modal-single" id="task-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog <?php echo get_option('task_modal_class'); ?>">
    <div class="modal-content data">

    </div>
  </div>
</div>

<!--Add/edit task modal-->
<div id="_task"></div>
<!--Add/edit approval modal-->
<div id="_approval"></div>

<!-- Lead Data Add/Edit-->
<div class="modal fade lead-modal" id="lead-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog <?php echo get_option('lead_modal_class'); ?>">
    <div class="modal-content data">

    </div>
  </div>
</div>

<div id="timers-logout-template-warning" class="hide">
  <h2 class="bold"><?php echo _l('timers_started_confirm_logout'); ?></h2>
  <hr />
  <a href="<?php echo admin_url('authentication/logout'); ?>" class="btn btn-danger"><?php echo _l('confirm_logout'); ?></a>
</div>

<!--Lead convert to customer modal-->
<div id="lead_convert_to_customer"></div>

<!--Lead reminder modal-->
<div id="lead_reminder_modal"></div>
<div id="_cc"></div>
<!-- Hearing Data Add/Edit-->
<div class="modal fade hearing-modal" id="hearing-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabelHearing">
  <div class="modal-dialog modal-xl<?php //echo get_option('lead_modal_class'); ?>">
    <div class="modal-content data">

    </div>
  </div>
</div>
<!-- Court Instance Data Add/Edit-->
<div class="modal fade court-instance-modal" id="court-instance-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabelCourtInstance">
  <div class="modal-dialog modal-xxl<?php //echo get_option('lead_modal_class'); ?>">
    <div class="modal-content data">

    </div>
  </div>
</div>
<!-- Quick Project Data Add/Edit-->
<div class="modal fade hearing-judgement-modal" id="hearing-judgement-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog">
    <div class="modal-content data">

    </div>
  </div>
</div>
<div class="modal fade dashboard-box-modal" id="dashboard-box-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg">
    <div class="modal-content data">
        
    </div>
  </div>
</div>
<div id="_dashboard_box"></div>