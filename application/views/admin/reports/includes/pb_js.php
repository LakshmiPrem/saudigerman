<script>
 var salesChart;
 var groupsChart;
 var paymentMethodsChart;
 var customersTable;
 var report_from = $('input[name="report-from"]');
 var report_to = $('input[name="report-to"]');
 var report_from_choose = $('#report-time');
 var date_range = $('#date-range');
 var report_invoices = $('#invoices-report');
 

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
   "clientid": '[name="clientid"]',
   "case_type": '[name="case_type"]',
   
 }
 $(function() {
   $('select[name="currency"],select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_items"],select[name="sale_agent_estimates"],select[name="payments_years"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"],select[name="client"],select[name="proposal_fee_status"],select[name="clientid"],select[name="case_type"]').on('change', function() {
     gen_reports();
   });

   $('select[name="invoice_status"],select[name="estimate_status"],select[name="sale_agent_invoices"],select[name="sale_agent_items"],select[name="sale_agent_estimates"],select[name="proposals_sale_agents"],select[name="proposal_status"],select[name="credit_note_status"],select[name="client"],select[name="proposal_fee_status"],select[name="clientid"]').on('change', function() {
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

  

   $('.table-pb-report').on('draw.dt', function() {
     var invoiceReportsTable = $(this).DataTable();
     var sums = invoiceReportsTable.ajax.json().sums;
     add_common_footer_sums($(this),sums);
     $(this).find('tfoot td.amount_open').html(sums.amount_open);
     $(this).find('tfoot td.applied_credits').html(sums.applied_credits);
     <?php foreach($invoice_taxes as $key => $tax){ ?>
        $(this).find('tfoot td.total_tax_single_<?php echo $key; ?>').html(sums['total_tax_single_<?php echo $key; ?>']);
     <?php } ?>
   });

    

    

   

   

 });

  
  

  function add_common_footer_sums(table,sums) {
       table.find('tfoot').addClass('bold');
       table.find('tfoot td').eq(0).html("<?php echo _l('invoice_total'); ?>");
       table.find('tfoot td.subtotal').html(sums.subtotal);
       table.find('tfoot td.total').html(sums.total);
       table.find('tfoot td.total_tax').html(sums.total_tax);
       table.find('tfoot td.discount_total').html(sums.discount_total);
       table.find('tfoot td.adjustment').html(sums.adjustment);
       //table.find('tfoot td.collected_amount').html(sums.collected_amount);
       table.find('tfoot td.referral_1').html(sums.referral_1);
       table.find('tfoot td.referral_2').html(sums.referral_2);
       table.find('tfoot td.referral_3').html(sums.referral_3);
       table.find('tfoot td.referral_4').html(sums.referral_4);

       table.find('tfoot td.total_fees').html(sums.total_fees);
       table.find('tfoot td.pb_cc').html(sums.pb_cc);
       table.find('tfoot td.pb_lc').html(sums.pb_lc);
       table.find('tfoot td.sk_cc').html(sums.sk_cc);
       table.find('tfoot td.sk_lc').html(sums.sk_lc);
       table.find('tfoot td.sk_total').html(sums.sk_total);
       table.find('tfoot td.kf_share').html(sums.kf_share);

       table.find('tfoot td.admin_cc').html(sums.admin_cc);
       table.find('tfoot td.admin_lc').html(sums.admin_lc);
       table.find('tfoot td.admin_total').html(sums.admin_total);
       table.find('tfoot td.lawyer_cc').html(sums.lawyer_cc);
       table.find('tfoot td.lawyer_lc').html(sums.lawyer_lc);
       table.find('tfoot td.lawyer_total').html(sums.lawyer_total);
       var t=0;
       if(sums.staff_share){
         var size = sums.staff_share.length;
        for(var i=0;i<size;i++) { 
          table.find('tfoot td.'+i).html('AED'+sums.staff_share[t]);
          t++;
        }
       }
      table.find('tfoot td.total_admin_share').html(sums.admin_total);

      

  }

 function init_report(text, type) {
   var report_wrapper = $('#report');

   if (report_wrapper.hasClass('hide')) {
        report_wrapper.removeClass('hide');
   }

   $('head title').html(text);
   $('.customers-group-gen').addClass('hide');

   report_invoices.addClass('hide');

   $('#income-years').addClass('hide');
   $('.chart-income').addClass('hide');
   $('.chart-payment-modes').addClass('hide');


   report_from_choose.addClass('hide');

   $('select[name="months-report"]').selectpicker('val', 'this_month');
   // Clear custom date picker
       report_to.val('');
       report_from.val('');
       $('#currency').removeClass('hide');

       if (type != 'total-income' && type != 'payment-modes') {
         report_from_choose.removeClass('hide');
       }

       if (type == 'total-income') {
         $('.chart-income').removeClass('hide');
         $('#income-years').removeClass('hide');
         date_range.addClass('hide');
       } else if (type == 'invoices-report') {
         report_invoices.removeClass('hide');
       } 
      gen_reports();
    }


  

   function invoices_report() {
     if ($.fn.DataTable.isDataTable('.table-pb-report')) {
       $('.table-pb-report').DataTable().destroy();
     }
     _table_api = initDataTable('.table-pb-report', admin_url + 'reports/pb', false, false, fnServerParams, [
       [2, 'DESC'],
       [0, 'DESC']
       ]);
   }

   // Main generate report function
   function gen_reports() {
     if (!report_invoices.hasClass('hide')) {
       invoices_report();
     } 
  }
</script>
