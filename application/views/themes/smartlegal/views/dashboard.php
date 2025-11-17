<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="panel_s section-heading section-projects">
   <div class="panel-body">
      <h4 class="no-margin section-text"><?php echo _l('clients_my_dashboard'); ?></h4>
   </div>
</div>
<div class="panel_s">
   <div class="panel-body">
      <div class="row mbot15">
         <div class="col-md-12">
            <h3 class="text-success projects-summary-heading no-mtop mbot15"><?php echo _l('projects_summary'); ?></h3>
         </div>
         <?php get_template_part('projects/project_summary'); ?>
      </div> <hr /><br><br>
      
    <div class="row">
      <div class="col-md-12">
        <h3 class="text-success pull-left no-mtop tickets-summary-heading"><?php echo _l('tickets_summary'); ?></h3>
        <a href="<?php echo site_url('clients/open_ticket'); ?>" class="btn btn-info new-ticket pull-right">
          <?php echo _l('clients_ticket_open_subject'); ?>
        </a>
        <div class="clearfix"></div>
        <hr />
      </div>
      <?php foreach(get_clients_area_tickets_summary($ticket_statuses) as $status){ ?>
        <div class="col-md-2 list-status ticket-status">
         <a href="<?php echo $status['url']; ?>" class="<?php if(in_array($status['ticketstatusid'], $list_statuses)){echo 'active';} ?>">
            <h3 class="bold ticket-status-heading">
              <?php echo $status['total_tickets'] ?>
            </h3>
            <span style="color:<?php echo $status['statuscolor']; ?>">
              <?php echo $status['translated_name']; ?>
            </span>
        </a>
      </div>
    <?php } ?>
  </div><hr /><br><br>
      <div class="col-md-12 mbot15">
      <h3 class="text-success contracts-summary-heading no-mtop mbot15"><?php echo _l('contract_summary_by_type'); ?></h3>
      <div class="relative" style="max-height:300px;">
        <canvas class="chart" height="300" id="contracts-by-type-chart"></canvas>
      </div>
    </div>
      <hr />
         
   </div>
</div>
<script>
  var contracts_by_type = '<?php echo $contracts_by_type_chart; ?>';
</script>
