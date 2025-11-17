<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $case_type = (isset($case_type)) ? $case_type : 'other_cases';?>
<?php $filter = ($this->input->get('filter') ? $this->input->get('filter')  : '' ); ?> 

<div id="wrapper">
  <div class="content">
  <?php breadcrumbs('hearings','hearing');?> 
    <div class="row">
      <div class="col-md-12">
            <div class="panel_s">
              <div class="panel-body">
              <div class="_buttons">
              
              <h4 class="no-margin">
                            <?php echo $title; ?></h4>

              <hr class="hr-panel-heading" />
           <div id="menu2" class="tab-pane">

        <div class="row">
            <div class="col-md-2 hide">
                <div class="form-group">
                    <?php  $hearing_types = get_hearing_types(); ?>
                    <?php echo render_select('hearing_type',$hearing_types,array('id','name'),'hearing_type','');?>
                </div>
            </div>
           <!--  <div class="col-md-2 hide">
                <div class="form-group">
                    <?php //echo render_select('case_id',$projects_,array('id',array('name','file_no')),'projects','');?>
                </div>
            </div> -->

          <?php echo form_hidden('hearing_time',''); ?>
         <?php echo form_hidden('months-report','custom'); ?>
         <div class="col-md-2">
          <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
          <div class="input-group date">
            <?php $beginMonth = _d(date('Y-m-01'));
                  $endMonth   = _d(date('Y-m-t')); ?>
             <input type="text" class="form-control datepicker" id="report-from" name="report-from" value="<?=$beginMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
       <div class="col-md-2">
          <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
          <div class="input-group date">
             <input type="text" class="form-control datepicker" id="report-to" name="report-to" value="<?=$endMonth?>">
             <div class="input-group-addon">
                <i class="fa fa-calendar calendar-icon"></i>
             </div>
          </div>
       </div>
        <div class="col-md-2 ">
            <div class="select-placeholder">
                <label for="client_id" class="control-label"><?php echo _l('customers'); ?></label>
               <select id="clientid" name="h_client_id" data-live-search="true" data-width="100%" class="ajax-search" data-empty-title="<?php echo _l('client'); ?>" data-none-selected-text="<?php echo _l('client'); ?>">
               </select>
            </div>
        </div>

        <div class="col-md-2">
            <?php echo render_select('court_id',$courts,array('id','name'),'hearing_court'); ?>
        </div>

        <div class="col-md-2">
           <?php  echo render_select('hearing_lawyer_id',$lawyer_staffs,array('staffid',array('firstname','lastname')),'lawyer_attending');?>
        </div>
        <div class="col-md-2">
                            <div class="form-group select-placeholder">
                                <?php echo render_select('project_emirates',$emirates_arr,array('id','name'),'city');?>
                            </div>
                        </div>

         <div class="clearfix"></div>
          </div> 
         <div class="row">
               <div class="col-md-2">
                    <div class="checkbox">
                     <input type="checkbox" id="exclude_unattend" name="exclude_unattend">
                     <label for="exclude_inactive"><?php echo _l('unattended_hearings'); ?> </label>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="checkbox">
                     <input type="checkbox" id="without_next_session_date" name="without_next_session_date" <?php if($filter == 'without_next_session') echo 'checked'; ?>>
                     <label for="without_next_session_date"><?php echo _l('without_next_session_date'); ?> </label>
                    </div>
                </div>
                
              <div class="col-md-2 pull-right mbot10 hide">
          
          <?php if(has_permission('projects','','create')){ ?>
           <a href="#" id="btn_add_hearing" class="btn btn-info mtop25 hide" onclick="init_hearing(''); return false;"><?php echo _l('add_new').' '._l('hearings'); ?></a>
       <?php } ?>
        </div>
		</div>
        <table class="table table-matter-hearing-report scroll-responsive">
        <thead>
          <tr> 
            <tr> 
                  <th><?php echo _l('hearing_date'); ?></th>
                  <th><?php echo _l('hearing_list_subject'); ?></th>
                 <!-- <th><?php echo _l('client'); ?></th>-->
                  <th><?php echo _l('court_fee'); ?></th>
                  <th><?php echo _l('casediary_casenumber'); ?></th>
                  <th><?php echo _l('assigned_lawyer'); ?></th>
                  <th><?php echo _l('court_decision'); ?></th>
                  <th><?php echo _l('comments'); ?></th>
                  <th><?php echo _l('hearing_postponed_until'); ?></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php init_tail(); ?>
<script>
$(function(){
    hearingTable();
});
</script>
<script type="text/javascript">  
  function hearingTable() { 
     var fnServerParams = { 
        'court_id':'[name="court_id"]',
        'case_id':'[name="case_id"]',
        "report_to": '[name="report-to"]',
        "report_from": '[name="report-from"]', 
        "h_client_id": '[name="h_client_id"]',
        "report_months": '[name="months-report"]', 
        "hearing_lawyer_id": '[name="hearing_lawyer_id"]', 
		'hearing_time': '[name="hearing_time"]',
        "without_next_session_date" :'[name="without_next_session_date"]:checked'
      };
      fnServerParams['exclude_unattend'] = '[name="exclude_unattend"]:checked';
 
     _table_api = initDataTable('.table-matter-hearing-report', admin_url + 'hearings/hearings_tables/', false, false, fnServerParams, [
       [0, 'ASC'],
       [0, 'ASC']
       ]);


//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
	  $('input[name="exclude_unattend"]').on('change',function(){
		 //  alert($('#exclude_unattend').is(':checked'));
           _table_api.ajax.reload();
       });
      $('input[name="without_next_session_date"]').on('change',function(){
         //  alert($('#exclude_unattend').is(':checked'));
           _table_api.ajax.reload();
       });
     
     $('input[name="report-from"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-to"]').on('change',function(){
        _table_api.ajax.reload();
     });
    }
</script>
</body>
</html>
