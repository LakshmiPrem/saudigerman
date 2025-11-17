<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('client_meetings_tab'); ?></h4>
<?php //if(has_permission('expenses','','create')){ ?>

<button type="button" data-toggle="modal" data-target="#team_meeting" class="btn btn-info btn-icon mbot25"><?php echo _l('schedule_meeting'); ?></button>

<?php ///} ?>


<?php $this->load->view('admin/clients/modals/teams_meetings'); ?>

<?php

$table_data = array(
 _l('#'),
 _l('subject'),
 _l('start_date'),
 _l('end_date'),
 _l('meeting_id'),
 _l('meeting_url'),
 _l('dateadded'),
);

render_datatable($table_data,'team_meeting');

?>
<?php } ?>