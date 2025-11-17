<script> 
 var salesChart;
 var groupsChart;
 var paymentMethodsChart;
 var customersTable;
 var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
 var date_range = $('#date-range');
 var report_from_choose = $('#report-time');
 var report_matter_client = $('#matter-clients-report');
 var report_matter_lawyer = $('#matter-lawyers-report');
 var report_matter_hearing = $('#matter-hearings');
 var report_matter_execution = $('#matter-execution');
 var report_matter_updates = $('#matter-updates');
 var report_matter_settlement = $('#matter-settlement');
 var report_matter_lawyer_timesheets = $('#matter-lawyers-timesheets');
 var report_documents_expiry = $('#documents-expiry');
 var report_matter_casenature = $('#matter-casenature');
	var report_matter_activecase = $('#matter-activecase');
	var report_matter_closecase = $('#matter-closecase');
 var report_matter_detailed = $('#matter-detailed-report');
 var report_matter_litigation = $('#matter-litigation-report');
 var report_matter_others = $('#matter-others-report');
 var report_agreements = $('#agreements-report');
 var report_cheque_bounce = $('#matter-cheque-bounce-report');
	 var report_police_case = $('#matter-police-case-report');
 var report_matter_age_wise = $('#matter-age-wise-report');



 var fnServerParams = {
   "report_months": '[name="months-report"]',
   "report_from": '[name="report-from"]',
   "report_to": '[name="report-to"]',
   "report_currency": '[name="currency"]',
   "invoice_status": '[name="invoice_status"]',
   "estimate_status": '[name="estimate_status"]',
   "sale_agent_invoices": '[name="sale_agent_invoices"]',
   "sale_agent_items": '[name="sale_agent_items"]',
   "sale_agent_estimates": '[name="sale_agent_estimates"]',
   "proposals_sale_agents": '[name="proposals_sale_agents"]',
   "proposal_status": '[name="proposal_status"]',
   "credit_note_status": '[name="credit_note_status"]',
   "client": '[name="client"]',
   "proposal_fee_status": '[name="proposal_fee_status"]',
   'clientid2':'[name="clientid2"]',
   'lawyerid2':'[name="lawyerid2"]',
	 'lawyerid3':'[name="lawyerid3"]',
   'hearing_type':'[name="hearing_type"]',
	'nature_type':'[name="nature_type"]',
   'case_id':'[name="case_id"]',
  'clientid3':'[name="clientid3"]',
	 'clientid4':'[name="clientid4"]',
   'p_status' : '[name="p_status"]',
   'p_status1' : '[name="p_status1"]',
   'opposite_party':'[name="opposite_party"]' ,
	  'clientid5':'[name="clientid5"]',
	 'clientid6':'[name="clientid6"]',
	 'clientid7':'[name="clientid7"]',
	 'clientid8':'[name="clientid8"]',
	  'clientid9':'[name="clientid9"]',
	  'clientid10':'[name="clientid10"]',
	 'case_nature':'[name="case_nature"]',


 }
 $(function() {
   $('select[name="currency"],select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_items"],select[name="sale_agent_estimates"],select[name="payments_years"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"],select[name="client"],select[name="proposal_fee_status"],select[name="clientid2"],select[name="lawyerid2"],select[name="lawyerid3"],[name="hearing_type"],[name="nature_type"],[name="case_id"],[name="clientid3"],[name="clientid4"],[name="clientid5"],[name="clientid6"],[name="clientid9"],[name="clientid10"],[name="clientid7"],[name="clientid8"],[name="case_nature"],[name="p_status"],[name="opposite_party"],[name="p_status1"]').on('change', function() {
     gen_reports();
   });

   $('select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_items"],select[name="sale_agent_estimates"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"],select[name="client"],select[name="proposal_fee_status"],select[name="clientid2"],select[name="lawyerid2"],select[name="lawyerid3"],[name="clientid3"],[name="clientid4"],[name="clientid5"],[name="p_status"],[name="opposite_party"],[name="nature_type"],[name="clientid6"],[name="case_nature"],[name="clientid7"],[name="clientid8"],[name="clientid10"],[name="clientid9"],[name="p_status1"]').on('change', function() {
     var value = $(this).val();
     if (value != null) {
       if (value.indexOf('') > -1) {
         if (value.length > 1) {
           value.splice(0, 1);
           $(this).selectpicker('val', value);
         }
       }
     }
   });
   report_from.on('change', function() {
     var val = $(this).val();
     var report_to_val = report_to.val();
     if (val != '') {
       report_to.attr('disabled', false);
       if (report_to_val != '') {
         gen_reports();
       }
     } else {
       report_to.attr('disabled', true);
     }
   });

   report_to.on('change', function() {
      var val = $(this).val();
     if (val != '') {
       gen_reports();
     }
   });

   $('select[name="months-report"]').on('change', function() {
     var val = $(this).val();
     report_to.attr('disabled', true);
     report_to.val('');
     report_from.val('');
     if (val == 'custom') {
       date_range.addClass('fadeIn').removeClass('hide');
       return;
     } else {
       if (!date_range.hasClass('hide')) {
         date_range.removeClass('fadeIn').addClass('hide');
       }
     }
     gen_reports();
   });

  

  /* $('.table-proposals-report').on('draw.dt', function() {
     var proposalsReportTable = $(this).DataTable();
     var sums = proposalsReportTable.ajax.json().sums;
      add_common_footer_sums($(this),sums);
      <?php foreach($proposal_taxes as $key => $tax){ ?>
        $(this).find('tfoot td.total_tax_single_<?php echo $key; ?>').html(sums['total_tax_single_<?php echo $key; ?>']);
     <?php } ?>
   });*/

  /* $('.table-matter-client-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     //add_common_footer_sums($(this),sums);
     //$(this).find('tfoot td.amount_open').html(sums.amount_open);
     //$(this).find('tfoot td.applied_credits').html(sums.applied_credits);
     <?php foreach($invoice_taxes as $key => $tax){ ?>
        //$(this).find('tfoot td.total_tax_single_<?php echo $key; ?>').html(sums['total_tax_single_<?php echo $key; ?>']);
     <?php } ?>
   });*/

  /* $('.table-matter-lawyer-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     //add_common_footer_sums($(this),sums);
     //$(this).find('tfoot td.amount_open').html(sums.amount_open);
     //$(this).find('tfoot td.applied_credits').html(sums.applied_credits);
     <?php foreach($invoice_taxes as $key => $tax){ ?>
        //$(this).find('tfoot td.total_tax_single_<?php echo $key; ?>').html(sums['total_tax_single_<?php echo $key; ?>']);
     <?php } ?>
   });*/


    

 });

  function add_common_footer_sums(table,sums) {
       table.find('tfoot').addClass('bold');
       table.find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
       table.find('tfoot td.subtotal').html(sums.subtotal);
       table.find('tfoot td.total').html(sums.total);
       table.find('tfoot td.total_tax').html(sums.total_tax);
       table.find('tfoot td.discount_total').html(sums.discount_total);
       table.find('tfoot td.adjustment').html(sums.adjustment);
  }

 function init_report(e, type) {
   var report_wrapper = $('#report');

   if (report_wrapper.hasClass('hide')) {
        report_wrapper.removeClass('hide');
   }

   $('head title').html($(e).text());
   $('.customers-group-gen').addClass('hide');

 
   report_matter_client.addClass('hide');
   report_matter_lawyer.addClass('hide');
   report_matter_hearing.addClass('hide');
   report_matter_execution.addClass('hide');
   report_matter_casenature.addClass('hide');
   report_matter_updates.addClass('hide');
   report_matter_settlement.addClass('hide');
   report_matter_lawyer_timesheets.addClass('hide');
   report_documents_expiry.addClass('hide');
   report_matter_detailed.addClass('hide');
   report_matter_litigation.addClass('hide');
   report_matter_others.addClass('hide');
   report_agreements.addClass('hide');
   report_cheque_bounce.addClass('hide');
   report_matter_age_wise.addClass('hide');
   report_police_case.addClass('hide');
   report_matter_activecase.addClass('hide');
   report_matter_closecase.addClass('hide');
   $('#income-years').addClass('hide');
   $('.chart-income').addClass('hide');
   $('.chart-payment-modes').addClass('hide');


   report_from_choose.addClass('hide');

   $('select[name="months-report"]').selectpicker('val', '');
   // Clear custom date picker
       report_to.val('');
       report_from.val('');
       $('#currency').removeClass('hide');

       if (type != 'total-income' && type != 'payment-modes') {
         report_from_choose.removeClass('hide');
       }

      if (type == 'matter-clients-report') {
         report_matter_client.removeClass('hide');
       }else if (type == 'matter-lawyers-report') {
         report_matter_lawyer.removeClass('hide');
       }else if (type == 'hearings-report') {
         report_matter_hearing.removeClass('hide');
       }else if (type == 'settlement-report') {
         report_matter_settlement.removeClass('hide');
       } else if(type == 'lawyer-timesheets'){
          report_matter_lawyer_timesheets.removeClass('hide');
      }else if(type == 'documents-expiry'){
          report_documents_expiry.removeClass('hide');
      }else if(type == 'matter-detailed-report'){
          report_matter_detailed.removeClass('hide');
      }else if(type == 'matter-litigation-report'){
          report_matter_litigation.removeClass('hide');
      }else if(type == 'matter-others-report'){
          report_matter_others.removeClass('hide');
      }else if(type == 'agreements-report'){
          report_agreements.removeClass('hide');
      }else if(type == 'cheque-bounce-report'){
          report_cheque_bounce.removeClass('hide');
      }else if (type == 'matter-age-wise-report') {
         report_matter_age_wise.removeClass('hide');
      }else if (type == 'matter-update-report') {
         report_matter_updates.removeClass('hide');
       }else if (type == 'matter-execution-report') {
         report_matter_execution.removeClass('hide');
       }else if(type == 'police-case-report'){
          report_police_case.removeClass('hide');
      }else if (type == 'matter-casenature-report') {
         report_matter_casenature.removeClass('hide');
       }else if (type == 'matter-activecase-report') {
         report_matter_activecase.removeClass('hide');
       }else if (type == 'matter-closecase-report') {
         report_matter_closecase.removeClass('hide');
       }
	 
	      gen_reports();
    }


  
   function invoices_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-client-report')) {
       $('.table-matter-client-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-client-report', admin_url + 'reports/invoice_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]).column(2).visible(false, false).columns.adjust();
   }

   function matter_client_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-client-report')) {
       $('.table-matter-client-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-client-report', admin_url + 'reports/matter_client_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

   function matter_age_wise_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-age-wise-report')) {
       $('.table-matter-age-wise-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-age-wise-report', admin_url + 'reports/matter_age_wise_report', false, false, fnServerParams, [
       [6, 'DESC'],
       [6, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

   function matter_litigation_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-litigation-report')) {
       $('.table-matter-litigation-report').DataTable({
      columnDefs: [
	{
                    targets: "_all",
                            createdCell: function(cell, cellData, rowData, rowIndex, colIndex) {
                                //https://stackoverflow.com/a/51242920/14226613
                                //https://datatables.net/forums/discussion/58336/how-to-know-the-height-of-a-cell
                                var $cell = $(cell)
                                if (cellData != null) {
                                    var linebreakes = cellData.split(/\r\n|\r|\n|br/).length
                                } else {
                                    var linebreakes = ''
                                }
                                //some debug
                                /*console.log("###cell:")
                                console.log($cell)
                                console.log("###amount line breakes: " + linebreakes)*/
                                
                                //jquery wrap a new class around the html structure
                                $(cell).contents().wrapAll("<div class='box'></div>");
                                //get the new class
                                var $content = $cell.find(".content");
                                //if there are more line as 12
                                if (linebreakes > 2) {
                                    //change class and reduce height
                                    $content.css({
                                        "height": "20px",
                                        "overflow": "hidden"
                                    })
                                    //add button only for this long cells
                                    $(cell).append($("<button>&#8650;&#x21CA;</button>"));
                                }
                                //get IF of this new button
                                $btn = $(cell).find("button");  
                                //store flag
                                $cell.data("isLess", true);
                                //eval click on button
                                $btn.click(function() {
                                  //create local variable and assign prev. stored flag
                                  var isLess = $cell.data("isLess");
                                  //ternary check if this flag is set and manipulte/reverse button
                                  $content.css("height", isLess ? "auto" : "20px")
                                  $(this).text(isLess ? '\u21C8 \u21C8' : '\u21CA \u21CA')
                                  //invert flag
                                  $cell.data("isLess", !isLess)
                                })
                          }
                    }
          
        ]
    }).destroy();
     }
     _table_api = initDataTable('.table-matter-litigation-report', admin_url + 'reports/matter_litigation_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

   function cheque_bounce_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-cheque-bounce-report')) {
       $('.table-matter-cheque-bounce-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-cheque-bounce-report', admin_url + 'reports/matter_cheque_bounce_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

   function police_case_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-police-case-report')) {
       $('.table-matter-police-case-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-police-case-report', admin_url + 'reports/matter_police_case_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

    function matter_others_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-others-report')) {
       $('.table-matter-others-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-others-report', admin_url + 'reports/matter_others_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

   function agreements_report() {
     if ($.fn.DataTable.isDataTable('.table-agreements-report')) {
       $('.table-agreements-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-agreements-report', admin_url + 'reports/agreements_report', false, false, fnServerParams, [
       [0, 'ASC'],
       [1, 'ASC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }

   function matter_detailed() {
    if($('#case_id').val() != ''){
     if ($.fn.DataTable.isDataTable('.table-matter-detailed-report')) {
       $('.table-matter-detailed-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-detailed-report', admin_url + 'reports/matter_detailed_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
    }
   }

   function matter_lawyer_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-lawyer-report')) {
       $('.table-matter-lawyer-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-lawyer-report', admin_url + 'reports/matter_lawyer_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(2).visible(false, false).columns.adjust();
   }

    function matter_lawyer_timesheets() {
     if ($.fn.DataTable.isDataTable('.table-matter-lawyer-timesheets-report')) {
       $('.table-matter-lawyer-timesheets-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-lawyer-timesheets-report', admin_url + 'reports/matter_lawyer_timesheets', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(2).visible(false, false).columns.adjust();
    }

    function documents_expiry() {
     if ($.fn.DataTable.isDataTable('.table-documents-expiry-report')) {
       $('.table-documents-expiry-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-documents-expiry-report', admin_url + 'reports/documents_expiry_report', false, false, fnServerParams,[3, 'ASC']);//.column(2).visible(false, false).columns.adjust();
    }

   function matter_hearing_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-hearing-report')) {
       $('.table-matter-hearing-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-hearing-report', admin_url + 'reports/matter_hearing_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }
	 function matter_update_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-update-report')) {
       $('.table-matter-update-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-update-report', admin_url + 'reports/matter_update_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }
	function matter_execution_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-execution-report')) {
       $('.table-matter-execution-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-execution-report', admin_url + 'reports/matter_execution_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }
	function matter_casenature_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-casenature-report')) {
       $('.table-matter-casenature-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-casenature-report', admin_url + 'reports/matter_casenature_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }
	function matter_activecase_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-activecase-report')) {
       $('.table-matter-activecase-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-activecase-report', admin_url + 'reports/matter_activecase_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }
		function matter_closecase_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-closecase-report')) {
       $('.table-matter-closecase-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-closecase-report', admin_url + 'reports/matter_closecase_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }
function matter_settlement_report() {
     if ($.fn.DataTable.isDataTable('.table-matter-settlement-report')) {
       $('.table-matter-settlement-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-matter-settlement-report', admin_url + 'reports/matter_settlement_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);//.column(3).visible(false, false).columns.adjust();
   }


   function receivables_report() {
     if ($.fn.DataTable.isDataTable('.table-receivables-report')) {
       $('.table-receivables-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-receivables-report', admin_url + 'reports/receivables_report', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]).column(2).visible(false, false).columns.adjust();
   }

   function credit_notes_report(){

     if ($.fn.DataTable.isDataTable('.table-credit-notes-report')) {
       $('.table-credit-notes-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-credit-notes-report', admin_url + 'reports/credit_notes', false, false, fnServerParams,[1, 'DESC']);

   }

   function estimates_report() {
     if ($.fn.DataTable.isDataTable('.table-estimates-report')) {
       $('.table-estimates-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-estimates-report', admin_url + 'reports/estimates_report', false, false, fnServerParams, [
       [3, 'DESC'],
       [0, 'DESC']
       ]).column(3).visible(false, false).columns.adjust();
   }

   function payments_received_reports() {
     if ($.fn.DataTable.isDataTable('.table-payments-received-report')) {
       $('.table-payments-received-report').DataTable().destroy();
     }
     initDataTable('.table-payments-received-report', admin_url + 'reports/payments_received', false, false, fnServerParams, [1, 'DESC']);
   }

   function proposals_report(){
   if ($.fn.DataTable.isDataTable('.table-proposals-report')) {
     $('.table-proposals-report').DataTable().destroy();
   }

   initDataTable('.table-proposals-report', admin_url + 'reports/proposals_report', false, false, fnServerParams, [0, 'DESC']);
 }

 function items_report(){
   if ($.fn.DataTable.isDataTable('.table-items-report')) {
     $('.table-items-report').DataTable().destroy();
   }
   initDataTable('.table-items-report', admin_url + 'reports/items', false, false, fnServerParams, [0, 'ASC']);
 }
$( "#updatebtn1" ).click(function() {
	alert('ss');
  $( "#box" ).animate({
   width: "300px",
   height: "300px",
	  overflow:'scroll',
    }, 1500 );
});
   // Main generate report function
   function gen_reports() {

     if (!report_matter_client.hasClass('hide')) { 
       matter_client_report();
     } else if (!report_matter_lawyer.hasClass('hide')) { 
       matter_lawyer_report();
     } else if (!report_matter_hearing.hasClass('hide')) { 
       matter_hearing_report();
     }else if (!report_matter_lawyer_timesheets.hasClass('hide')) { 
       matter_lawyer_timesheets();
     }else if (!report_documents_expiry.hasClass('hide')) { 
       documents_expiry();
     }else if (!report_matter_detailed.hasClass('hide')) { 
       matter_detailed();
     }else if (!report_matter_litigation.hasClass('hide')) { 
       matter_litigation_report();
     }else if (!report_matter_others.hasClass('hide')) { 
       matter_others_report();
     } else if (!report_agreements.hasClass('hide')) { 
       agreements_report();
     } else if (!report_cheque_bounce.hasClass('hide')) { 
       cheque_bounce_report();
     } else if(!report_matter_age_wise.hasClass('hide')){
      matter_age_wise_report();
     } else if (!report_matter_settlement.hasClass('hide')) { 
       matter_settlement_report();
     } else if (!report_matter_updates.hasClass('hide')) { 
       matter_update_report();
     }else if (!report_matter_execution.hasClass('hide')) { 
       matter_execution_report();
     }else if (!report_police_case.hasClass('hide')) { 
       police_case_report();
     } else if (!report_matter_casenature.hasClass('hide')) { 
       matter_casenature_report();
     }else if (!report_matter_activecase.hasClass('hide')) { 
       matter_activecase_report();
     }else if (!report_matter_closecase.hasClass('hide')) { 
       matter_closecase_report();
     }
	   


  }
</script>
	$row[] = '<button id="updatebtn1">Read more</button><div id="box" style="height:100px;overflow:hidden;">'.get_casedetails_complete_update($aRow['id']).'</div>';