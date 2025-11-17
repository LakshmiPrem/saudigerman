<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
              <div class="_buttons">
                  <?php if(has_permission('projects','','create')){ ?>
              <a href="<?php echo admin_url('casetemplates/casetemplate/'); ?>" class="btn btn-info pull-left display-block">
                <?php echo _l('new_casetemplate'); ?>
              </a>
              <?php } ?>
              <div class="btn-group pull-right mleft4 btn-with-tooltip-group _filter_data hide" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <i class="fa fa-filter" aria-hidden="true"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-right width300">
                  <!-- <li>
                    <a href="#" data-cview="all" onclick="dt_custom_view('','.table-casetemplates',''); return false;">
                      <?php echo _l('expenses_list_all'); ?>
                    </a>
                  </li> -->
                  <?php
                  // Only show this filter if user has permission for projects view otherwisde wont need this becuase by default this filter will be applied
                  if(has_permission('casediary','','view')){ ?>
                  <li>
                    <a href="#" data-cview="my_projects" onclick="dt_custom_view('my_projects','.table-casetemplates','my_projects'); return false;">
                      <?php echo _l('my_matters'); ?>
                    </a>
                  </li>
                  <?php } ?>
                  <li class="divider"></li>
                  <?php foreach($statuses as $status){ ?>
                    <li class="<?php if($status['filter_default'] == true && !$this->input->get('status') || $this->input->get('status') == $status['id']){echo 'active';} ?>">
                      <a href="#" data-cview="<?php echo 'project_status_'.$status['id']; ?>" onclick="dt_custom_view('project_status_<?php echo $status['id']; ?>','.table-casetemplates','project_status_<?php echo $status['id']; ?>'); return false;">
                        <?php echo $status['name']; ?>
                      </a>
                    </li>
                    <?php } ?>
                  </ul>
                </div>
                <div class="clearfix"></div>
<!--                 <hr class="hr-panel-heading" />
 -->              </div>
               
             <div class="clearfix"></div>
              <hr class="hr-panel-heading" />
             <?php echo form_hidden('custom_view'); ?>
             <?php
             
             $table_data = array(
              ['name'=>'#','th_attrs'=>['class'=>'not_visible']],
              //_l('project_customer'),
             _l('case_title'),
              //_l('casediary_oppositeparty'),
              //_l('casediary_file_no'),
              //_l('project_start_date'),
              array(
                'name'=>_l('project_deadline'),
                'th_attrs'=>array('class'=>'not_visible')
              ),
              //_l('case_number'),
              );

              
              array_push($table_data,array(
                'name'=>_l('case_type')));
              

              array_push($table_data,_l('project_status'));

              $custom_fields = get_custom_fields('casediary',array('show_on_table'=>1));
               foreach($custom_fields as $field){
                  array_push($table_data,$field['name']);
              }

              //$table_data = do_action('projects_table_columns',$table_data);
              array_push($table_data, _l('options'));

            render_datatable($table_data,'casetemplates'); ?>
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
     var projects_not_sortable = $('.table-casetemplates').find('th').length - 1;
     initDataTable('.table-casetemplates', admin_url+'casetemplates/table', [projects_not_sortable], [projects_not_sortable], ProjectsServerParams,[0,'DESC']);
     init_ajax_search('customer', '#clientid_copy_project.ajax-search');
});
</script>
</body>
</html>
