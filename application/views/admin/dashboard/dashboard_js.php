<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<script>
    var weekly_payments_statistics;
    var user_dashboard_visibility = <?php echo $user_dashboard_visibility; ?>;
    $(function() {
        $( "[data-container]" ).sortable({
            connectWith: "[data-container]",
            helper:'clone',
            handle:'.widget-dragger',
            tolerance:'pointer',
            forcePlaceholderSize: true,
            placeholder: 'placeholder-dashboard-widgets',
            start:function(event,ui) {
                $("body,#wrapper").addClass('noscroll');
                $('body').find('[data-container]').css('min-height','20px');
            },
            stop:function(event,ui) {
                $("body,#wrapper").removeClass('noscroll');
                $('body').find('[data-container]').removeAttr('style');
            },
            update: function(event, ui) {
                if (this === ui.item.parent()[0]) {
                    var data = {};
                    $.each($("[data-container]"),function(){
                        var cId = $(this).attr('data-container');
                        data[cId] = $(this).sortable('toArray');
                        if(data[cId].length == 0) {
                            data[cId] = 'empty';
                        }
                    });
                    $.post(admin_url+'staff/save_dashboard_widgets_order', data, "json");
                }
            }
        });

        // Read more for dashboard todo items
        $('.read-more').readmore({
            collapsedHeight:150,
            moreLink: "<a href=\"#\"><?php echo _l('read_more'); ?></a>",
            lessLink: "<a href=\"#\"><?php echo _l('show_less'); ?></a>",
        });

        $('body').on('click','#viewWidgetableArea',function(e){
            e.preventDefault();

            if(!$(this).hasClass('preview')) {
                $(this).html("<?php echo _l('hide_widgetable_area'); ?>");
                $('[data-container]').append('<div class="placeholder-dashboard-widgets pl-preview"></div>');
            } else {
                $(this).html("<?php echo _l('view_widgetable_area'); ?>");
                $('[data-container]').find('.pl-preview').remove();
            }

            $('[data-container]').toggleClass('preview-widgets');
            $(this).toggleClass('preview');
        });

        var $widgets = $('.widget');
        var widgetsOptionsHTML = '';
        widgetsOptionsHTML += '<div id="dashboard-options">';
        widgetsOptionsHTML += "<h4><i class='fa fa-question-circle' data-toggle='tooltip' data-placement=\"bottom\" data-title=\"<?php echo _l('widgets_visibility_help_text'); ?>\"></i> <?php echo _l('widgets'); ?></h4><a href=\"<?php echo admin_url('staff/reset_dashboard'); ?>\"><?php echo _l('reset_dashboard'); ?></a>";

        widgetsOptionsHTML += ' | <a href=\"#\" id="viewWidgetableArea"><?php echo _l('view_widgetable_area'); ?></a>';
        widgetsOptionsHTML += '<hr class=\"hr-10\">';

        $.each($widgets,function(){
            var widget = $(this);
            var widgetOptionsHTML = '';
            if(widget.data('name') && widget.html().trim().length > 0) {
                widgetOptionsHTML += '<div class="checkbox checkbox-inline">';
                var wID = widget.attr('id');
                wID = wID.split('widget-');
                wID = wID[wID.length-1];
                var checked= ' ';
                var db_result = $.grep(user_dashboard_visibility, function(e){ return e.id == wID; });
                if(db_result.length >= 0) {
                    // no options saved or really visible
                    if(typeof(db_result[0]) == 'undefined' || db_result[0]['visible'] == 1) {
                        checked = ' checked ';
                    }
                }
                widgetOptionsHTML += '<input type="checkbox" class="widget-visibility" value="'+wID+'"'+checked+'id="widget_option_'+wID+'" name="dashboard_widgets['+wID+']">';
                widgetOptionsHTML += '<label for="widget_option_'+wID+'">'+widget.data('name')+'</label>';
                widgetOptionsHTML += '</div>';
            }
            widgetsOptionsHTML += widgetOptionsHTML;
        });

        $('.screen-options-area').append(widgetsOptionsHTML);
        $('body').find('#dashboard-options input.widget-visibility').on('change',function(){
          if($(this).prop('checked') == false) {
            $('#widget-'+$(this).val()).addClass('hide');
        } else {
            $('#widget-'+$(this).val()).removeClass('hide');
        }

        var data = {};
        var options = $('#dashboard-options input[type="checkbox"]').map(function() {
            return { id: this.value, visible: this.checked ? 1 : 0 };
        }).get();

        data.widgets = options;
/*
        if (typeof(csrfData) !== 'undefined') {
            data[csrfData['token_name']] = csrfData['hash'];
        }
*/
        $.post(admin_url+'staff/save_dashboard_widgets_visibility',data).fail(function(data) {
            // Demo usage, prevent multiple alerts
            if($('body').find('.float-alert').length == 0) {
                alert_float('danger', data.responseText);
            }
        });
    });

        var tickets_chart_departments = $('#tickets-awaiting-reply-by-department');
        var tickets_chart_status = $('#tickets-awaiting-reply-by-status');
        var leads_chart = $('#leads_status_stats');
        var projects_chart = $('#projects_status_stats');
        var contracts_chart = $('#contracts_status_stats');

        if (tickets_chart_departments.length > 0) {
            // Tickets awaiting reply by department chart
            var tickets_dep_chart = new Chart(tickets_chart_departments, {
                type: 'doughnut',
                data: <?php echo $tickets_awaiting_reply_by_department; ?>,
            });
        }
        if (tickets_chart_status.length > 0) {
            // Tickets awaiting reply by department chart
            new Chart(tickets_chart_status, {
                type: 'doughnut',
                data: <?php echo $tickets_reply_by_status; ?>,
                options: {
                   onClick:function(evt){
                    onChartClickRedirect(evt,this);
                }
            },
        });
        }
        if (leads_chart.length > 0) {
            // Leads overview status
            new Chart(leads_chart, {
                type: 'doughnut',
                data: <?php echo $leads_status_stats; ?>,
                options:{
                    maintainAspectRatio:false,
                    onClick:function(evt){
                        onChartClickRedirect(evt,this);
                    }
                }
            });
        }
        if(projects_chart.length > 0){
            // Projects statuses
            new Chart(projects_chart, {
                type: 'doughnut',
                data: <?php echo $projects_status_stats; ?>,
                options: {
                    maintainAspectRatio:false,
                    onClick:function(evt){
                       onChartClickRedirect(evt,this);
                   }
               }
           });
        }
		if(contracts_chart.length > 0){
            // Projects statuses
            new Chart(contracts_chart, {
                type: 'doughnut',
                data: <?php echo $contracts_status_stats; ?>,
                options: {
                    maintainAspectRatio:false,
                    onClick:function(evt){
                       onChartClickRedirect(evt,this);
                   }
               }
           });
        }
        if($(window).width() < 500) {
            // Fix for small devices weekly payment statistics
            $('#weekly-payment-statistics').attr('height', '250');
        }

        fix_user_data_widget_tabs();
        $(window).on('resize', function(){
            $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').removeAttr('style');
            fix_user_data_widget_tabs();
        });
        // Payments statistics
        init_weekly_payment_statistics( <?php echo $weekly_payment_stats; ?> );
        $('select[name="currency"]').on('change', function() {
            init_weekly_payment_statistics();
        });
    });
    function fix_user_data_widget_tabs(){
        if((app.browser != 'firefox'
                && isRTL == 'false' && is_mobile()) || (app.browser == 'firefox'
                && isRTL == 'false' && is_mobile())){
                $('.horizontal-scrollable-tabs ul.nav-tabs-horizontal').css('margin-bottom','26px');
        }
    }
    function init_weekly_payment_statistics(data) {
        if ($('#weekly-payment-statistics').length > 0) {

            if (typeof(weekly_payments_statistics) !== 'undefined') {
                weekly_payments_statistics.destroy();
            }
            if (typeof(data) == 'undefined') {
                var currency = $('select[name="currency"]').val();
                $.get(admin_url + 'home/weekly_payments_statistics/' + currency, function(response) {
                    weekly_payments_statistics = new Chart($('#weekly-payment-statistics'), {
                        type: 'bar',
                        data: response,
                        options: {
                            responsive:true,
                            scales: {
                                yAxes: [{
                                  ticks: {
                                    beginAtZero: true,
                                }
                            }]
                        },
                    },
                });
                }, 'json');
            } else {
                weekly_payments_statistics = new Chart($('#weekly-payment-statistics'), {
                    type: 'bar',
                    data: data,
                    options: {
                        responsive: true,
                        scales: {
                            yAxes: [{
                              ticks: {
                                beginAtZero: true,
                            }
                        }]
                    },
                },
            });
            }

        }
    }
</script>
<script type="text/javascript">
  function load_project_data(page)
 {
  
   var case_type = $('#case_type').val();
   var status = $('#c_status').val();
   var q = $('#search_').val();
   var data = { "q":q,"case_type" : case_type,"status" : status };

  $.ajax({
   url:"<?php echo admin_url(); ?>projects/pagination/"+page,
   method:"POST",
   dataType:"json",
   data:data,
   success:function(data)
   {
    $('#div_ajax_project').html(data.project_data);
    $('#pagination_link').html(data.pagination_link);
    $('#total_cases').html(data.total_cases);
   }
  });

  matter_project_report();
 }
  function load_contract_data(page)
 {
   matter_agreement_report();
    
    }
          function load_po_data(page)
 {
   matter_po_report();
    
    }
	 function load_contract_data_old(page)
 {
	 matter_agreement_report();
	  
   var case_type = $('#contract_type').val(); 
   var status = $('#c_status').val();
   var q = $('#searchc_').val();
   var data = { "q":q,"contract_type" : case_type,"status" : status };

  $.ajax({
   url:"<?php echo admin_url(); ?>contracts/pagination/"+page,
   method:"POST",
   dataType:"json",
   data:data,
   success:function(data)
   {
    $('#div_ajax_contract').html(data.project_data);
    $('#pagination_linkc').html(data.pagination_link);
    $('#total_contracts').html(data.total_cases);
   }
  });
 }
$(document).ready(function(){ 

    $(".pop").popover({
    trigger: "manual",
    html: true,
    animation: false
  })
  .on("mouseenter", function() {
    var _this = this;
    $(this).popover("show");
    $(".popover").on("mouseleave", function() {
      $(_this).popover('hide');
    });
  }).on("mouseleave", function() {
    var _this = this;
    setTimeout(function() {
      if (!$(".popover:hover").length) {
        $(_this).popover("hide");
      }
    }, 300);
  });
    
load_client_data(1);
<?php if($confirmapproval=='contract'){?>
load_contractapprover_report();
<?php } ?>
<?php if($confirmapproval=='ticket'){?>
load_ticketapprover_report();
<?php } ?>
 function load_client_data(page)
 {
  matter_clients_report();
  var q = $('#search_2').val();
  var data = { "q":q};
  $.ajax({
   url:"<?php echo admin_url(); ?>clients/pagination/"+page,
   method:"POST",
   dataType:"json",
    data:data,
   success:function(data)
   {
    $('#div_ajax_client').html(data.client_data);
    $('#pagination_link1').html(data.pagination_link);
    $('#total_clients').html(data.total_clients);
    
   }
  });
 }


 
 
 $(document).on("click", ".client-page li a", function(event){
  event.preventDefault();
  var page = $(this).data("ci-pagination-page");
  load_client_data(page);
 });
 $(document).on("click", ".project-page li a", function(event){
  event.preventDefault();
  var page = $(this).data("ci-pagination-page");
  load_project_data(page);
 });
	$(document).on("click", ".contract-page li a", function(event){
  event.preventDefault();
  var page = $(this).data("ci-pagination-page");
  load_contract_data(page);
 });
$("#searchc_").on("keyup", function() {
   load_contract_data(1);
 });

 $("#contract_type").on("change", function() {
    load_contract_data(1);
 });

 $("#search_").on("keyup", function() {
   load_project_data(1);
 });

 $("#case_type,#c_status").on("change", function() {
    load_project_data(1);
 });


  $("#search_2").on("keyup", function() {
   load_client_data(1);
 });

$(document).on("click", ".opposite-page li a", function(event){
  event.preventDefault();
  var page = $(this).data("ci-pagination-page");
  load_oppositeparty_data(page);
 });
$("#search_3").on("keyup", function() {
   load_oppositeparty_data(1);
 });
$("#report-from1").on("change", function() {
	
   load_summary_data();
 });
	$("#report-to1").on("change", function() {
   load_summary_data();
 });
	});
 function load_oppositeparty_data(page)
 {
   matter_opposites_report();
 }
 function load_oppositeparty_data_old(page)
 {
	 matter_opposites_report();
   var q = $('#search_3').val();
  var data = { "q":q};
  $.ajax({
   url:"<?php echo admin_url(); ?>opposite_parties/pagination/"+page,
   method:"POST",
   dataType:"json",
    data:data,
   success:function(data)
   {
    $('#div_ajax_opposite_party').html(data.client_data);
    $('#pagination_link2').html(data.pagination_link);
    $('#total_oppositeparties').html(data.total_clients);
    
   }
  });
 }
	function matter_clients_report() {
		
		//alert($('input[name="report-fromc1"]').val());
     var fnServerParams = {
        "report_to": "[name='report-toc1']",
        "report_from": "[name='report-fromc1']",
       // "clientid22": '[name="client_id22"]',
        "report_months": '[name="months-reportc1"]',       
    }   
     if ($.fn.DataTable.isDataTable('.table-clients-report')) {
       $('.table-clients-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-clients-report', admin_url + 'reports/matter_clients_report', false, false, fnServerParams, [
       [1, 'ASC'],
       [1, 'ASC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc1"]').on('change',function(){
		_table_api.ajax.reload();
     });

     $('input[name="report-toc1"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
	function matter_opposites_report() {
		//alert($('input[name="report-fromc"]').val())
     var fnServerParams = {
        "report_to": "[name='report-toc2']",
        "report_from": "[name='report-fromc2']",
       // "clientid22": '[name="client_id22"]',
        "report_months": '[name="months-reportc"]',       
    }   
     if ($.fn.DataTable.isDataTable('.table-opposites-report')) {
       $('.table-opposites-report').DataTable().destroy();
     }
		 if ($.fn.DataTable.isDataTable('.table-agreements-report')) {
       $('.table-agreements-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-opposites-report', admin_url + 'reports/matter_oppositeparties_report', false, false, fnServerParams, [
       [1, 'ASC'],
       [1, 'ASC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc2"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc2"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
		 function matter_agreement_report() {
		//alert($('input[name="report-fromc"]').val())
     var fnServerParams = {
        'contract_type':'[name="contract_type1"]',
        "report_to": "[name='report-toc']",
        "report_from": "[name='report-fromc']",
        "clientid221": '[name="client_id22"]',
        "report_months": '[name="months-reportc"]',
		'c_status':'[name="contract_status"]',
		 'in_out':'[name="in_out"]',
    }   
     if ($.fn.DataTable.isDataTable('.table-agreements-report')) {
       $('.table-agreements-report').DataTable().destroy();
     }
	 if ($.fn.DataTable.isDataTable('.table-opposites-report')) {
       $('.table-opposites-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-agreements-report', admin_url + 'reports/agreements_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
   function matter_po_report() {
		//alert($('input[name="report-fromc"]').val())
     var fnServerParams = {
        'contract_type':'[name="contract_type1"]',
        "report_to": "[name='report-toc']",
        "report_from": "[name='report-fromc']",
        "client_idpo": '[name="client_idpo"]',
        "report_months": '[name="months-reportc"]',
		'c_status':'[name="contract_status"]',
		 'in_out':'[name="in_out"]',
    }   
     if ($.fn.DataTable.isDataTable('.table-po-report')) {
       $('.table-po-report').DataTable().destroy();
     }
	 
     _table_api = initDataTable('.table-po-report', admin_url + 'reports/po_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
	function load_ticketapprover_report() {
		 var fnServerParams = {
         'clientid23':'[name="clientid23"]',
		 'service_type':'[name="service_type"]',
	 	't_status':'[name="t_status"]',      
    }
	  if ($.fn.DataTable.isDataTable('.table-legalapprovals-report')) {
       $('.table-legalapprovals-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-legalapprovals-report', admin_url + 'reports/legalapproval_report', false, false, fnServerParams, [
       [5, 'DESC'],
       [0, 'DESC']
       ]);
    
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
    }
   function load_contractapprover_report(rel_type='contract') {
   
		 var fnServerParams = {
         'clientid23':'[name="clientid23"]',
		 'service_type':'[name="service_type"]',
	 	't_status':'[name="t_status"]',  
       }
	  if ($.fn.DataTable.isDataTable('.table-contractapprovals-report')) {
       $('.table-contractapprovals-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-contractapprovals-report', admin_url + 'reports/contractapproval_report/'+rel_type, false, false, fnServerParams, [
       [5, 'DESC'],
       [0, 'DESC']
       ]);
    
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
    }

    function matter_project_report() {
        var fnServerParams = {
         'clientid3':'[name="clientid3"]',
	 	'p_status':'[name="p_status"]',      
    }
        if ($.fn.DataTable.isDataTable('.table-case-report')) {
       $('.table-case-report').DataTable().destroy();
     } 
	  
     _table_api = initDataTable('.table-case-report', admin_url + 'reports/project_report', false, false, fnServerParams, [
       [1, 'DESC'],
       [2, 'DESC']
       ]);
    
       $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
    
   }
	
 function load_summary_data(dashtype='legal')
 {

   var q = $('#report-from1').val();
	   var q1 = $('#report-to1').val();
	 var dashtype=dashtype;
  var data = { "q":q,"q1":q1,"dashtype":dashtype};
  $.ajax({
   url:"<?php echo admin_url(); ?>dashboard/pagination/",
   method:"POST",
   dataType:"json",
    data:data,
   success:function(data)
   {
    $('#div_ajax_summary_party').html(data.client_data);
    //$('#pagination_link2').html(data.pagination_link);
  //  $('#total_oppositeparties').html(data.total_clients);
    
   }
  });
 }
function matter_execution_report() {
	 var fnServerParams = {
        'case_id':'[name="case_idex"]',
        "report_to": "[name='report-toex']",
        "report_from": "[name='report-fromex']",
        "clientid": '[name="client_id5"]',
		"report_months": '[name="months-reportex"]',       
    } 
     if ($.fn.DataTable.isDataTable('.table-matter-execution-report')) {
       $('.table-matter-execution-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-execution-report', admin_url + 'reports/matter_execution_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
			
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromex"]').on('change',function(){
        _table_api.ajax.reload();
     });
	 
     $('input[name="report-toex"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
function matter_judgement_report() {
	 var fnServerParams = {
        'case_id':'[name="case_idjudg"]',
        "report_to": "[name='report-tojudg']",
        "report_from": "[name='report-fromjudg']",
        "clientid": '[name="client_idjudg"]',
		"report_months": '[name="months-reportjudg"]',       
    } 
     if ($.fn.DataTable.isDataTable('.table-matter-judgement-report')) {
       $('.table-matter-judgement-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-judgement-report', admin_url + 'reports/matter_judgement_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
			
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromjudg"]').on('change',function(){
        _table_api.ajax.reload();
     });
	 
     $('input[name="report-tojudg"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
   function load_contractactivity_report() {
	 
		 var fnServerParams = {
         'clientid24':'[name="clientid24"]',
		   
    }
	  if ($.fn.DataTable.isDataTable('.table-contractactivity-report')) {
       $('.table-contractactivity-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-contractactivity-report', admin_url + 'reports/contractactivity_report', false, false, fnServerParams, [
       [3, 'DESC'],
       [0, 'DESC']
       ]);
    
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
    }

   function matter_receivable_agreement_report() {
    //alert($('input[name="report-fromc"]').val())
     var fnServerParams = {
        'contract_type':'[name="contract_type1rec"]',
        "report_to": "[name='report-toc-rec']",
        "report_from": "[name='report-fromc-rec']",
        "clientid221": '[name="client_id22rec"]',
        "report_months": '[name="months-reportc-rec"]',
    'c_status':'[name="contract_statusrec"]',
     'in_out':'[name="in_outrec"]',
    }   
     if ($.fn.DataTable.isDataTable('.table-receivable-agreements-report')) {
       $('.table-receivable-agreements-report').DataTable().destroy();
     }
 
     _table_api = initDataTable('.table-receivable-agreements-report', admin_url + 'reports/receivable_agreements_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc-rec"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc-rec"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
       function matter_payable_agreement_report() {
    //alert($('input[name="report-fromc"]').val())
     var fnServerParams = {
        'contract_type':'[name="contract_type1pay"]',
        "report_to": "[name='report-toc-pay']",
        "report_from": "[name='report-fromc-pay']",
        "clientid221": '[name="client_id22pay"]',
        "report_months": '[name="months-reportc-pay"]',
    'c_status':'[name="contract_statuspay"]',
     'in_out':'[name="in_outpay"]',
    }   
     if ($.fn.DataTable.isDataTable('.table-payable-agreements-report')) {
       $('.table-payable-agreements-report').DataTable().destroy();
     }
 
     _table_api = initDataTable('.table-payable-agreements-report', admin_url + 'reports/payable_agreements_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc-pay"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc-pay"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
   
    // Add click handlers for contract status boxes
$(document).ready(function() {
    
    // Active Contracts - Just show table without filters
    $('.top_stats_wrapper').eq(0).css('cursor', 'pointer').on('click', function() {
        // Switch to the contracts tab
        $('a[href="#menu11"]').trigger('click');
        
        // Reset all filters
        resetContractFilters();
        
        // Load the table
        setTimeout(function() {
            load_contract_data(1);
            scrollToTable();
        }, 100);
    });
    
    // Expired Contracts - Filter by status = 3
    $('.top_stats_wrapper').eq(1).css('cursor', 'pointer').on('click', function() {
        // Switch to the contracts tab
        $('a[href="#menu11"]').trigger('click');
        
        // Reset filters first
        resetContractFilters();
        
        // Set status filter to 3 (Expired)
        setTimeout(function() {
            $('select[name="contract_status"]').val('3').trigger('change');
            load_contract_data(1);
            scrollToTable();
        }, 100);
    });
    
    // Ongoing Contracts - Filter by status = 1
    $('.top_stats_wrapper').eq(2).css('cursor', 'pointer').on('click', function() {
        // Switch to the contracts tab
        $('a[href="#menu11"]').trigger('click');
        
        // Reset filters first
        resetContractFilters();
        
        // Set status filter to 1 (Ongoing)
        setTimeout(function() {
            $('select[name="contract_status"]').val('1').trigger('change');
            load_contract_data(1);
            scrollToTable();
        }, 100);
    });
    
    // Expiring in Three Months - Filter by date range
    $('.top_stats_wrapper').eq(3).css('cursor', 'pointer').on('click', function() {
        // Switch to the contracts tab
        $('a[href="#menu11"]').trigger('click');
        
        // Reset filters first
        resetContractFilters();
        
        // Calculate dates
        var today = new Date();
        var threeMonthsLater = new Date();
        threeMonthsLater.setMonth(threeMonthsLater.getMonth() + 3);
        
        // Format dates as dd/mm/yyyy
        var todayFormatted = formatDate(today);
        var threeMonthsFormatted = formatDate(threeMonthsLater);
        
        // Set date filters
        setTimeout(function() {
            $('input[name="report-fromc"]').val(todayFormatted).trigger('change');
            $('input[name="report-toc"]').val(threeMonthsFormatted).trigger('change');
            load_contract_data(1);
            scrollToTable();
        }, 100);
    });
});

// Helper function to reset all contract filters
function resetContractFilters() {
    $('select[name="contract_type1"]').val('').trigger('change');
    $('select[name="client_id22"]').val('').trigger('change');
    $('input[name="report-fromc"]').val('');
    $('input[name="report-toc"]').val('');
    $('select[name="contract_status"]').val('').trigger('change');
    $('select[name="in_out"]').val('').trigger('change');
}

// Helper function to format date as dd/mm/yyyy
function formatDate(date) {
    var day = String(date.getDate()).padStart(2, '0');
    var month = String(date.getMonth() + 1).padStart(2, '0');
    var year = date.getFullYear();
    return day + '/' + month + '/' + year;
}

// Helper function to scroll to the table section
function scrollToTable() {
    var tableSection = $('#menu11');
    if (tableSection.length) {
        $('html, body').animate({
            scrollTop: tableSection.offset().top - 100
        }, 500);
    }
}
 function matter_signed_agreement_report() {
    //alert($('input[name="report-fromc"]').val())
     var fnServerParams = {
        'contract_typesign':'[name="contract_typesign"]',
        "report_to": "[name='report-tosign']",
        "report_from": "[name='report-fromsign']",
        "clientid22sign": '[name="client_id22sign"]',
        "report_months": '[name="months-reportsign"]',
      
    }   
     if ($.fn.DataTable.isDataTable('.table-signed-agreements-report')) {
       $('.table-signed-agreements-report').DataTable().destroy();
     }
 
     _table_api = initDataTable('.table-signed-agreements-report', admin_url + 'reports/signed_agreements_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromsigned"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-tosigned"]').on('change',function(){
        _table_api.ajax.reload();
     });
   }
   
   
   
   function load_approval_data(page)
 { 
   contract_approval_report();
    
}

function contract_approval_report() { 


   var fnServerParams = {
       
        "report_to": "[name='report-toc-app']",
        "report_from": "[name='report-fromc-app']",
        "clientid2211": '[name="client_id22app"]',
        "report_months": '[name="months-reportc-app"]',
    'c_status1':'[name="contract_statusapp"]',
     'contract_po':'[name="contract_po"]',
    }   
     if ($.fn.DataTable.isDataTable('.table-contract-approval-report')) {
       $('.table-contract-approval-report').DataTable().destroy();
     }
 
     _table_api = initDataTable('.table-contract-approval-report', admin_url + 'reports/contract_approval_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc-rec1"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc-rec1"]').on('change',function(){
        _table_api.ajax.reload();
     });

  
   
   }

   function po_approval_report() { 


   var fnServerParams = {
       
        "report_to": "[name='report-toc-app1']",
        "report_from": "[name='report-fromc-app1']",
        "clientid2211": '[name="client_id22app1"]',
        "report_months": '[name="months-reportc-app1"]',
    'c_status1':'[name="contract_statusapp1"]',
     'contract_po':'[name="contract_po1"]',
    }   
     if ($.fn.DataTable.isDataTable('.table-po-approval-report')) {
       $('.table-po-approval-report').DataTable().destroy();
     }
 
     _table_api = initDataTable('.table-po-approval-report', admin_url + 'reports/contract_approval_report', false, false, fnServerParams, [
       [0, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
     $.each(fnServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            _table_api.ajax.reload();
        });
    });
     
     $('input[name="report-fromc-rec1"]').on('change',function(){
        _table_api.ajax.reload();
     });

     $('input[name="report-toc-rec1"]').on('change',function(){
        _table_api.ajax.reload();
     });

  
   
   }
</script>