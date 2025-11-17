<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                <div class="panel-body _buttons">
                    <?php if(has_permission('chequebounces','','create')){ ?>
                    <a href="<?php echo admin_url('chequebounces/chequebounce'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_chequebounces'); ?></a>
                      <?php } ?>
                   <?php// if(has_permission('chequebounces','','view')) {?>
                    <?php //$this->load->view('admin/chequebounces/filters');|| have_assigned_chequebounces() ?>
                     <a href="<?php echo admin_url('chequebounces/detailed_overview'); ?>" class="btn btn-success pull-right mright5"><?php echo _l('chequebounce_report'); ?></a>
                      
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
  
                    <div class="row" id="contract_summary">
                       <div class="col-md-12">
                            <h4 class="no-margin text-success"><?php echo _l('chequebounce_summary_heading'); ?></h4>
                             <?php
                   $_where = '';
                  if(!has_permission('chequebounces','','view')){
                    $_where = 'id IN (SELECT bounceid FROM '.db_prefix().'chequebounces_assigned WHERE staff_id='.get_staff_user_id().')';
                  }
                  ?>
                        </div>
                         <div class="_filters _hidden_inputs">
                  <?php
                  echo form_hidden('my_projects');
                  foreach($cheque_statuses as $status){
                   $value = $status['chequestatusid'];
                     if(!$this->input->get('status')){
                        $value = '';
                     } else if($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['chequestatusid'] ? $status['chequestatusid'] : "");
                     }
                     echo form_hidden('project_status_'.$status['chequestatusid'],$value);
                    ?>
                   <div class="col-md-2 col-xs-6 border-right">
                    <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['chequestatusid']; ?>
                    <a href="#" onclick="dt_custom_view('project_status_<?php echo $status['chequestatusid']; ?>','.table-chequebounces','project_status_<?php echo $status['chequestatusid']; ?>',true); return false;">
                     <h3 class="bold"><?php echo total_rows(db_prefix().'chequebounces',$where); ?></h3>
                     <span style="color:<?php echo $status['statuscolor']; ?>" project-status-<?php echo $status['chequestatusid']; ?>>
                     <?php echo $status['name']; ?>
                     </span>
                   </a>
                 </div>
                 <?php } ?>
               </div>
                       <!-- 
                        <div class="col-md-12">
                            <h4 class="no-margin text-success"><?php echo _l('chequebounce_summary_heading'); ?></h4>
                        </div>
                          
                        <div class="col-md-2 col-xs-6 border-right">
                            <h3 class="bold"><?php echo $count_active; ?></h3>
                            <span class="text-info"><?php echo _l('contract_summary_active'); ?></span>
                        </div>
                     <div class="col-md-2 col-xs-6 border-right">
                             <h3 class="bold"><?php echo $expiring; ?></h3>
                            <span class="text-danger"><?php echo _l('checkbounce_summary_hold'); ?></span>
                        </div>
                        <div class="col-md-2 col-xs-6 border-right">
                          
                                <h3 class="bold"><?php echo $count_expired; ?></h3>
                                <span class="text-warning"><?php echo _l('chequebounce_summary_clear'); ?></span>
                            </div>-->
                            <div class="col-md-2 col-xs-6 border-right">
                                <h3 class="bold"><?php echo $count_recent_retcheque; ?></h3>
                                    <span class="text-success"><?php echo _l('contract_summary_recently_return'); ?></span>
                                </div>
                              <!--  <div class="col-md-2 col-xs-6">
                                    <h3 class="bold"><?php echo $count_trash; ?></h3>
                                    <span class="text-muted"><?php echo _l('contract_summary_trash'); ?></span>
                                </div>-->
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
                         
                           <?php $this->load->view('admin/chequebounces/table_html'); ?>
                          
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>
   <?php init_tail(); ?>
   <script>
    $(function(){

        var ContractsServerParams = {};
        $.each($('._hidden_inputs._filters input'),function(){
            ContractsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });
var tAPI = initDataTable('.table-chequebounces', admin_url+'chequebounces/table', undefined, undefined, ContractsServerParams,<?php echo hooks()->apply_filters('chequebounces_table_default_order', json_encode(array(0,'desc'))); ?>);
	
     tAPI.page.len(-1).draw();
     $.each(ContractsServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                 tAPI.ajax.reload();
                 tAPI.page.len(-1).draw();
            });
      });
        

    });
</script>
</body>
</html>
