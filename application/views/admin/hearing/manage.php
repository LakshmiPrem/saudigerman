<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $case_type = (isset($case_type)) ? $case_type : 'other_cases';?>
<?php $filter = ($this->input->get('filter') ? $this->input->get('filter')  : '' ); ?> 

<div id="wrapper">
  <div class="content">
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

        <div class="col-md-2 hide">
            <?php echo render_select('court_id',$courts,array('id','name'),'hearing_court'); ?>
        </div>

        <div class="col-md-2">
           <?php  echo render_select('hearing_lawyer_id',$lawyer_staffs,array('staffid',array('firstname','lastname')),'lawyer_attending');?>
        </div>

       <div class="col-md-2 hide">
            <?php $path = base_url('uploads/reports/'._l('hearings').'.pdf'); ?>
         <div class="col-md-2 text-right mtop25">
            <a download href="<?php echo $path;?>"  class="btn btn-default"><i class="fa fa-file-pdf-o"></i> PDF</a>
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
              <div class="col-md-2 pull-right mbot10">
          
          <?php if(has_permission('projects','','create')){ ?>
           <a href="#" id="btn_add_hearing" class="btn btn-info mtop25 hide" onclick="init_hearing(''); return false;"><?php echo _l('add_new').' '._l('hearings'); ?></a>
       <?php } ?>
        </div>
		</div>
        <?php $this->load->view('admin/reports/includes/matter_hearing_table_html'); ?>
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
    matter_hearing_report();
});
</script>
<script type="text/javascript">  
	function matter_tomorrow_hearings(){
	var tomorrow=<?php echo "'".date("Y-m-d",strtotime ( '+1 day' , strtotime ( date('Y-m-d') ) ))."'"?>
		
		$('input[name="report-from"]').val(tomorrow);
		$('input[name="report-to"]').val(tomorrow);
		document.getElementById("exclude_unattend").checked = true;
		matter_hearing_report();
		$('ul.nav.nav-pills li a').parent().removeClass('active');  
		 $('.h_menu2').trigger("click");
		  $('.h_menu2').addClass("active");
		  $('#menu2').addClass("active in");
		//  $('#tabs a[href=#menu2]').tab('show');
		
	}
	function matter_unattended_hearings(){
		$('input[name="report-from"]').val(<?php echo "'".date("Y-m-d",strtotime (getfirsthearingdate()))."'"?>);
		$('input[name="report-to"]').val(<?php echo "'".date("Y-m-d")."'"?>);
		$('input[name="hearing_time"]').val(<?php echo "'".date("Y-m-d h:i:s")."'"?>);
		//$('input[name="report-to"]').val(<?php echo "'".date("Y-m-d",strtotime ( '-1 day' , strtotime ( date('Y-m-d') ) ))."'"?>);
		document.getElementById("exclude_unattend").checked = true;
		matter_hearing_report();
		$('ul.nav.nav-pills li a').parent().removeClass('active');  
		 $('.h_menu2').trigger("click");
		  $('.h_menu2').addClass("active");
		  $('#menu2').addClass("active in");
		//  $('#tabs a[href=#menu2]').tab('show');
		
	}
 function matter_hearing_report() {
    
    //document.title = '<?php echo _l('hearings') ?>';
	 // alert($('input[name="hearing_time"]').val());
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
	         
    } 
	 fnServerParams['exclude_unattend'] = '[name="exclude_unattend"]:checked';
	/*if($('#exclude_unattend').is(':checked')==true)
		fnServerParams['exclude_unattend'] =1 ;
	 else
		 fnServerParams['exclude_unattend'] =0 ;*/
    if ($.fn.DataTable.isDataTable('.table-matter-hearing-report')) {
       $('.table-matter-hearing-report').DataTable().destroy();
    }
    
      _table_api = initDataTable('.table-matter-hearing-report', admin_url + 'reports/matter_allhearing_report', false, false, fnServerParams, [
       [8, 'ASC'],
       [0, 'ASC']
       ]);//.column(3).visible(false, false).columns.adjust();
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
