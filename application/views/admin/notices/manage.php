<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                <div class="panel-body _buttons">
                    <?php if(has_permission('notices','','create')){ ?>
                    <!--<a href="<?php echo admin_url('notices/notice'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_notice'); ?></a>-->
                    <a href="#" onclick="new_quick_notice();return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_notice'); ?></a>
                    
                    <?php } ?>
                    <?php $this->load->view('admin/notices/filters'); ?>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="row" id="notice_summary">
                        <div class="col-md-12">
                            <h4 class="no-margin text-success"><?php echo _l('notice_summary_heading'); ?></h4>
                        </div>
						     <div class="_filters _hidden_inputs">
                  <?php
                  $_where = '';
                  foreach($notice_statuses as $status){
                   $value = $status['id'];
                  if($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['id'] ? $status['id'] : "");
                     }
                     echo form_hidden('notices_by_status_'.$status['id'],$value);
                    
                    ?>
                   <div class="col-md-2 col-xs-6 border-right">
                    <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id']; ?>
                    <a href="#" onclick="dt_custom_view('notices_by_status_<?php echo $status['id']; ?>','.table-notices','notices_by_status_<?php echo $status['id']; ?>',true); return false;">
                     <h3 class="bold"><?php echo total_rows(db_prefix().'notices',$where); ?></h3>
                     <span style="color:<?php echo $status['statuscolor']; ?>" project-status-<?php echo $status['id']; ?>>
                     <?php echo $status['name']; ?>
                     </span>
                   </a>
		        </div>
                 <?php } ?>
               </div>
                        <div class="col-md-2 col-xs-6 border-right hide">
                            <h3 class="bold"><?php echo $count_active; ?></h3>
                            <span class="text-info"><?php echo _l('notice_summary_active'); ?></span>
                        </div>
                        <div class="col-md-2 col-xs-6 border-right hide">
                            <h3 class="bold"><?php echo $count_expired; ?></h3>
                            <span class="text-danger"><?php echo _l('notice_summary_expired'); ?></span>
                        </div>
                        <div class="col-md-2 col-xs-6 border-right hide">
                            <h3 class="bold"><?php echo count($expiring); ?></h3>
                                <span class="text-warning"><?php echo _l('notice_summary_about_to_expire'); ?></span>
                            </div>
                            <div class="col-md-2 col-xs-6 border-right hide">
                                <h3 class="bold"><?php echo $count_recently_created; ?></h3>
                                    <span class="text-success"><?php echo _l('notice_summary_recently_added'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6 hide">
                                    <h3 class="bold"><?php echo $count_trash; ?></h3>
                                    <span class="text-muted"><?php echo _l('notice_summary_trash'); ?></span>
                                </div>
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" />
                                <div class="col-md-6 border-right hide">
                                    <h4><?php echo _l('notice_summary_by_type'); ?></h4>
                                    <div class="relative" style="max-height:400px">
                                        <canvas class="chart" height="400" id="notices-by-type-chart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6 hide">
                                    <h4>
                                        <?php echo _l('notice_summary_by_type_value'); ?>
                                        (<span data-toggle="tooltip"
                                            data-title="<?php echo _l('base_currency_string'); ?>"
                                            class="text-has-action">
                                        <?php echo $base_currency->name; ?></span>)
                                    </h4>
                                    <div class="relative" style="max-height:400px">
                                        <canvas class="chart" height="400" id="notices-value-by-type-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <?php echo form_hidden('custom_view'); ?>
                        <div class="panel-body">
                           <?php $this->load->view('admin/notices/table_html'); ?>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <?php init_tail(); ?>
   <script>
    $(function(){

        var noticesServerParams = {};
        $.each($('._hidden_inputs._filters input'),function(){
            noticesServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });

        initDataTable('.table-notices', admin_url+'notices/table', undefined, undefined, noticesServerParams,<?php echo hooks()->apply_filters('notices_table_default_order', json_encode(array(0,'desc'))); ?>);

        new Chart($('#notices-by-type-chart'), {
            type: 'bar',
            data: <?php echo $chart_types; ?>,
            options: {
                legend: {
                    display: false,
                },
                responsive: true,
                maintainAspectRatio:false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                }
            }
        });
    /*    new Chart($('#notices-value-by-type-chart'), {
            type: 'line',
            data: <?php echo $chart_types_values; ?>,
            options: {
                responsive: true,
                legend: {
                    display: false,
                },
                maintainAspectRatio:false,
                scales: {
                    yAxes: [{
                        display: true,
                        ticks: {
                            suggestedMin: 0,
                        }
                    }]
                }
            }
        });*/
    });
</script>
<script>
function notice_bulk_action(event) { 
	
if($('select[name="mass_notice_status"]').val()==''){
	 return false;
}

var r = confirm(app.lang.confirm_action_prompt);
if (r == false) {
    return false;
} else {
    
    var mass_notice = $('#mass_notice').prop('checked');
   
    var ids = [];
    var data = {};
 //remove bd
    data.mass_notice = true;

    var rows = $('.table-notices').find('tbody tr');
    $.each(rows, function() {
        var checkbox = $($(this).find('td').eq(0)).find('input');
        if (checkbox.prop('checked') == true) {
            ids.push(checkbox.val());
        }
    });
    data.ids = ids;
    data.notice_status = $('select[name="mass_notice_status"]').val(); 
    $(event).addClass('disabled');
    setTimeout(function(){
      
    $.post(admin_url + 'notices/bulk_action', data).done(function() {
       window.location.reload();
   });
  },50);
}
}
</script>
</body>
</html>
<?php //$this->load->view('admin/notices/quick_notice_modal'); ?>