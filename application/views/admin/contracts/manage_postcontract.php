<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s hide">
                <div class="panel-body _buttons">
                 
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="row" id="contract_summary">
                        <div class="col-md-12">
                            <h4 class="no-margin text-success"><?php echo _l('contract_summary_heading'); ?></h4>
                        </div>
                        <div class="col-md-2 col-xs-6 border-right">
                            <h3 class="bold"><?php echo $count_active; ?></h3>
                            <span class="text-info"><?php echo _l('contract_summary_active'); ?></span>
                        </div>
                        <div class="col-md-2 col-xs-6 border-right">
                            <h3 class="bold"><?php echo $count_expired; ?></h3>
                            <span class="text-danger"><?php echo _l('contract_summary_expired'); ?></span>
                        </div>
                        <div class="col-md-2 col-xs-6 border-right">
                            <h3 class="bold"><?php echo count($expiring); ?></h3>
                                <span class="text-warning"><?php echo _l('contract_summary_about_to_expire'); ?></span>
                            </div>
                            <div class="col-md-2 col-xs-6 border-right">
                                <h3 class="bold"><?php echo $count_recently_created; ?></h3>
                                    <span class="text-success"><?php echo _l('contract_summary_recently_added'); ?></span>
                                </div>
                                <div class="col-md-2 col-xs-6">
                                    <h3 class="bold"><?php echo $count_trash; ?></h3>
                                    <span class="text-muted"><?php echo _l('contract_summary_trash'); ?></span>
                                </div>
                                <div class="clearfix"></div>
                                <hr class="hr-panel-heading" />
                                <div class="col-md-6 border-right hide">
                                    <h4><?php echo _l('contract_summary_by_type'); ?></h4>
                                    <div class="relative" style="max-height:400px">
                                        <canvas class="chart" height="400" id="contracts-by-type-chart"></canvas>
                                    </div>
                                </div>
                                <div class="col-md-6 hide">
                                    <h4>
                                        <?php echo _l('contract_summary_by_type_value'); ?>
                                        (<span data-toggle="tooltip"
                                            data-title="<?php echo _l('base_currency_string'); ?>"
                                            class="text-has-action">
                                        <?php echo $base_currency->name; ?></span>)
                                    </h4>
                                    <div class="relative" style="max-height:400px">
                                        <canvas class="chart" height="400" id="contracts-value-by-type-chart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel_s">
                        <?php echo form_hidden('custom_view'); ?>
                        <div class="panel-body">
                           <?php $this->load->view('admin/contracts/posttable_html'); ?>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <?php init_tail(); ?>
 <?php $this->load->view('admin/contracts/post_contract_actionreview'); ?>
   <script>
    $(function(){

        var ContractsServerParams = {};
        $.each($('._hidden_inputs._filters input'),function(){
            ContractsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });

        initDataTable('.table-signcontracts', admin_url+'contracts/signtable', undefined, undefined, ContractsServerParams,<?php echo hooks()->apply_filters('contracts_table_default_order', json_encode(array(0,'desc'))); ?>,9);

        new Chart($('#contracts-by-type-chart'), {
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
        new Chart($('#contracts-value-by-type-chart'), {
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
        });
    });
</script>
</body>
</html>
<?php //$this->load->view('admin/contracts/quick_contract_modal'); ?>