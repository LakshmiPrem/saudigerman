<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="screen-options-area"></div>
    <?php if(is_admin()) { ?>
      <div class="screen-options-btn">
        <?php echo _l('dashboard_options'); ?>
    </div>
    <div class="pull-right">
        <a href="javascript:void(0);" class="btn btn-default btn-sm mleft5" onclick="init_dash_box();"><i class="fa fa-cog"></i></a>
    </div>
    <?php } ?>
    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <?php hooks()->do_action( 'before_start_render_dashboard_content' ); ?>

            <div class="clearfix"></div>

            <div class="col-md-12 mtop30" data-container="top-12">
                <?php render_dashboard_widgets('top-12'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_top_container'); ?>

            <div class="col-md-6" data-container="middle-left-6">
                <?php render_dashboard_widgets('middle-left-6'); ?>
            </div>
            <div class="col-md-6" data-container="middle-right-6">
                <?php render_dashboard_widgets('middle-right-6'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_half_container'); ?>

            <div class="col-md-8" data-container="left-8">
                <?php render_dashboard_widgets('left-8'); ?>
            </div>
            <div class="col-md-4" data-container="right-4">
                <?php render_dashboard_widgets('right-4'); ?>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-4" data-container="bottom-left-4">
                <?php render_dashboard_widgets('bottom-left-4'); ?>
            </div>
             <div class="col-md-4" data-container="bottom-middle-4">
                <?php render_dashboard_widgets('bottom-middle-4'); ?>
            </div>
            <div class="col-md-4" data-container="bottom-right-4">
                <?php render_dashboard_widgets('bottom-right-4'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard'); ?>
        </div>
    </div>
</div>
<script>
    app.calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
<?php $this->load->view('admin/dashboard/hearing_js'); ?>
</body>
</html>



<script type="text/javascript">
    function matter_hearing_report() {
     var fnServerParams = {
        'hearing_type':'[name="hearing_type"]',
        'case_id':'[name="case_id"]',
        "report_to": "[name='report-to']",
        "report_from": "[name='report-from']",
        "clientid": '[name="client_idh"]',
		"mention_hearing": '[name="mention_hearingh"]',
        "report_months": '[name="months-report"]',       
    } 
	
	 fnServerParams['exclude_unattend'] = '[name="exclude_unattend_h"]:checked';
     if ($.fn.DataTable.isDataTable('.table-matter-hearing-report')) {
       $('.table-matter-hearing-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-hearing-report', admin_url + 'reports/matter_hearing_report', false, false, fnServerParams, [
       [1, 'ASC'],
       [1, 'ASC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
			
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-from"]').on('change',function(){
        _table_api.ajax.reload();
     });
	   $('input[name="exclude_unattend_h"]').on('change',function(){
		  _table_api.ajax.reload();
       });
     $('input[name="report-to"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
    function load_legalrequest_report() {
		 var fnServerParams = {
         'clientid23':'[name="clientid23"]',
		 'service_type':'[name="service_type"]',
	 	't_status':'[name="t_status"]',      
    }
	  if ($.fn.DataTable.isDataTable('.table-legalrequests-report')) {
       $('.table-legalrequests-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-legalrequests-report', admin_url + 'reports/legalrequests_report', false, false, fnServerParams, [
       [5, 'DESC'],
       [0, 'DESC']
       ]);
    
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
    }
</script>
<style>
  
  .center_li{
    font-size: 16px;padding-top: 120px !important;
    padding-bottom: 150px !important;
    padding-left: 75px !important;
  }

  .span-footer{
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    margin-top: 4px !important;
    padding-bottom: 5px!important; font-weight: 500 !important;font-size: 12px !important;
  }
  .panel-footer a{
    display: flex;
    align-items: right;
    justify-content: right;
    margin-top: 4px;
    padding-bottom: 5px; font-weight: 500;font-size: 12px;
  }

  .panel-footer i{
        padding-left: 2px;
        font-size: 20px;
  }
  .panel-footer-height{
    height: 52px;
  }
  .count_link{
    color: white !important;font-size: 14px !important; background: #0f4388 !important;
    background-color: #0f4388 !important; margin-top:-5px !important;   padding: 0.25em 0.4em !important;
  }
  .alen-panel{
    max-height: 480px;overflow: hidden; height: 475px;
  }

  .list-group-item{
    /*padding: 5px 14px !important;*/
  }
  .badge-dashboard{
    background-color: #03a9f4;
    padding: 6px;
    border-radius: 4px;
    font-weight: 544;
  }
  .panel-heading{
    font-size: 14px !important;
    letter-spacing: 2px;
  }
  .center_li p{
    padding-left: 40px !important;
  }
  .center_li p i{
    font-size: 28px;
    margin-left: 5px;
  }

  .center_li a{
    padding: 3px;
  }

  .list-group-item{
    border: none !important;
    border-bottom: 2px solid #ddd !important;
    margin-bottom: 0px !important;
    padding-bottom: 0px !important;
  }
  .li_new_button{
    display: flex!important;
    align-items: center!important;
    justify-content: center!important;
    border: none !important;
  }
  .li_new_button a{
   margin: 3px;
  }
/*  .color_Sun{
    background-color: #c9c9f7  !important;
  }
  .color_Mon{
    background-color: #f5bff4 !important;
  }
  .color_Tue{
    background-color: #f1e0ae   !important;
  }
  .color_Wed{
    background-color: #c9c9f7  !important;
  }
  .color_Thu{
    background-color: #c9f7d7   !important;
  }
  .color_Fri{
    background-color: #ffe0e0   !important;
  }
  .color_Sat{
    background-color: #f0f381  !important;
  }*/
  .panel-heading{
     font-weight: 580;
  }
  .panel-default > .panel-heading {
  background-color: #ddd !important;
  }
  #wrapper{
    min-height: 2000px !important;
  }
</style>