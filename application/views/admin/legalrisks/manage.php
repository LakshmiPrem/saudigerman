<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>

<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">

              <div class="col-md-4">
             <?php if(has_permission('legal_risks','','create')){ ?>
                    <a href="<?php echo admin_url('legal_risks/legal_risk'); ?>" class="btn btn-info pull-left display-block"><?php echo _l('new_legalrisk'); ?></a>
                    <?php } ?>
              </div>
              

              <div class="_buttons">
               
              
              <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data  pull-right " data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right width300">
                  <li>
                    <a href="#" data-cview="all" onclick="dt_custom_view('','.table-legalrisks',''); return false;">
                      <?php echo _l('expenses_list_all'); ?>
                    </a>
                  </li>
                  <?php
                  // Only show this filter if user has permission for projects view otherwise wont need this becuase by default this filter will be applied
                  if(has_permission('legal_risks','','view')){ ?>
                  <!-- <li>
                    <a href="#" data-cview="my_projects" onclick="dt_custom_view('my_projects','.table-all-projects','my_projects'); return false;">
                      <?php echo _l('home_my_projects'); ?>
                    </a>
                  </li> -->
                  <?php } ?>
                  <li class="divider"></li>
                  <?php foreach($risk_statuses as $status){ ?>
                    <li class="<?php if(!$this->input->get('status') || $this->input->get('status') == $status['id']){echo 'active';} ?>">
                      <a href="#" data-cview="<?php echo 'project_status_'.$status['id']; ?>" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-legalrisks','project_status_<?php echo $status['id']; ?>'); return false;">
                        <?php echo $status['statusname']; ?>
                      </a>
                    </li>
                    <?php } ?>
                    <div class="clearfix"></div>
    <li class="divider"></li>
    <?php if(count($risk_types) > 0){ ?>
    <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('risk_type'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
             <?php foreach($risk_types as $type){ ?>
            <li>
                <a href="#" data-cview="contracts_by_type_<?php echo $type['id']; ?>" onclick="dt_custom_view('contracts_by_type_<?php echo $type['id']; ?>','.table-legalrisks','contracts_by_type_<?php echo $type['id']; ?>'); return false;">
                    <?php echo $type['name']; ?>
                </a>
            </li>
        <?php } ?>
        </ul>
    </li>
     <?php } ?>
                  </ul>
                </div>
              
                <div class="clearfix"></div>
                <hr class="hr-panel-heading" />
              </div>
               <div class="row mbot15">
                <div class="col-md-12">
                  <h4 class="no-margin"><?php echo _l('legalrisk_summary_heading'); ?></h4>
                  <?php
                  $_where = '';
                        if(!has_permission('legal_risks','','view')){
                            $_where  = array('addedfrom'=>get_staff_user_id());
                  }
                  ?>
                </div>
                <div class="_filters _hidden_inputs">
                  <?php
                  echo form_hidden('my_projects');
                  foreach($risk_statuses as $status){
                   $value = $status['id'];
                     if(!$this->input->get('status')){
                        $value = '';
                     } else if($this->input->get('status')) {
                        $value = ($this->input->get('status') == $status['id'] ? $status['id'] : "");
                     }
                     echo form_hidden('project_status_'.$status['id'],$value);
					  foreach($risk_types as $type){
						  	echo form_hidden('contracts_by_type_'.$type['id']);
					  }
                    ?>
                    
                   <div class="col-md-2 col-xs-6 border-right">
                    <?php $where = ($_where == '' ? '' : $_where.' AND ').'risk_status = '.$status['id']; ?>
                    <a href="#" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-legalrisks','project_status_<?php echo $status['id']; ?>',true); return false;">
                     <h3 class="bold"><?php echo total_rows(db_prefix().'legal_risk',$where); ?></h3>
                     <span style="color:<?php echo $status['statuscolor']; ?>" project-status-<?php echo $status['id']; ?>>
                     <?php echo $status['statusname']; ?>
                     </span>
                   </a>
                 </div>
                 <?php } ?>
               </div>
             </div>
             <div class="clearfix"></div>
              <hr class="hr-panel-heading" />
             
                                 <?php echo form_hidden('custom_view'); ?>
                       
                           <?php 
				  	$table_data = array(
                              _l('the_number_sign'),
						                  _l('risk_title'),
                              _l('client'),
					                   	_l('lead_add_edit_name'),
                              _l('risk_type'),
                              _l('risk_value'),
	        			              _l('probability'),
                              _l('status'),
                         );
                            $custom_fields = get_custom_fields('legalrisks',array('show_on_table'=>1));
                            foreach($custom_fields as $field){
                               array_push($table_data,$field['name']);
                           }
				  

				  	$table_data = hooks()->apply_filters('contracts_table_columns', $table_data);

				  	render_datatable($table_data, (isset($class) ? $class : 'legalrisks'),[],[
					  'data-last-order-identifier' => 'legalrisks',
					  'data-default-order'         => get_table_last_order('legalrisks'),
					]);
				  ?>

      
    
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<?php init_tail(); ?>
<script>
$(function(){
     var ProjectsServerParams = {};

     $.each($('._hidden_inputs._filters input'),function(){
         ProjectsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });

        
     var tAPI = initDataTable('.table-legalrisks', admin_url+'legal_risks/table', [0], [0], ProjectsServerParams, <?php echo hooks()->apply_filters('legalrisks_table_default_order', json_encode(array(0,'desc'))); ?>);
	
    // tAPI.page.len(-1).draw();
     $.each(ProjectsServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                 tAPI.ajax.reload();
                 tAPI.page.len(-1).draw();
            });
      });

     /*$('.table-all-projects').on('xhr.dt', function(e, settings, json, xhr) {
      tAPI.page.len(100).draw();
    })*/

     
});


</script>
</body>
</html>
