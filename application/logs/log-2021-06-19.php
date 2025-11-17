<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-06-19 15:16:29 --> Query error: Table 'beveryzv_smartlawyer_db.tblitems_in' doesn't exist - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS number, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, YEAR(tblinvoices.date) as year, tblinvoices.date as date, duedate, subtotal, total, total_tax, (
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*tblitemstax.taxrate) - (qty*rate/100*tblitemstax.taxrate * discount_percent/100)),2)
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*tblitemstax.taxrate) - (qty*rate/100*tblitemstax.taxrate * (discount_total/subtotal*100) / 100)),2)
                    ELSE ROUND(SUM(qty*rate/100*tblitemstax.taxrate),2)
                    END
                    FROM tblitems_in
                    INNER JOIN tblitemstax ON tblitemstax.itemid=tblitems_in.id
                    WHERE tblitems_in.rel_type="invoice" AND taxname="VAT" AND taxrate="5.00" AND tblitems_in.rel_id=tblinvoices.id) as total_tax_single_0, discount_total, adjustment, (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id) as credits_applied, (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id) - (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id)) as amount_open, status, project_id, refered_employee_id, sale_agent, lawyer_assigned ,userid,clientid,tblinvoices.id,discount_percent,tblinvoicepaymentrecords.date as collected_date
    FROM tblinvoices
    JOIN tblclients ON tblclients.userid = tblinvoices.clientid LEFT JOIN tblinvoicepaymentrecords ON tblinvoicepaymentrecords.invoiceid = tblinvoices.id
    
    WHERE  status != 5 AND (tblinvoicepaymentrecords.date BETWEEN "2021-06-01" AND "2021-06-30")
    
    ORDER BY YEAR(tblinvoices.date) DESC, number DESC
    LIMIT 0, 25
    
ERROR - 2021-06-19 15:18:12 --> Severity: Notice --> Undefined index: userid /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/relation_helper.php 176
ERROR - 2021-06-19 15:18:12 --> Severity: Notice --> Undefined index: company /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/relation_helper.php 177
ERROR - 2021-06-19 15:18:12 --> Could not find the language line "casediary"
ERROR - 2021-06-19 15:18:12 --> Could not find the language line "report_invoice_collected_amount"
ERROR - 2021-06-19 15:18:12 --> Could not find the language line "total_referral"
ERROR - 2021-06-19 15:18:26 --> Query error: Table 'beveryzv_smartlawyer_db.tblitems_in' doesn't exist - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS number, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, YEAR(tblinvoices.date) as year, tblinvoices.date as date, duedate, subtotal, total, total_tax, (
                    SELECT CASE
                    WHEN discount_percent != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*tblitemstax.taxrate) - (qty*rate/100*tblitemstax.taxrate * discount_percent/100)),2)
                    WHEN discount_total != 0 AND discount_type = "before_tax" THEN ROUND(SUM((qty*rate/100*tblitemstax.taxrate) - (qty*rate/100*tblitemstax.taxrate * (discount_total/subtotal*100) / 100)),2)
                    ELSE ROUND(SUM(qty*rate/100*tblitemstax.taxrate),2)
                    END
                    FROM tblitems_in
                    INNER JOIN tblitemstax ON tblitemstax.itemid=tblitems_in.id
                    WHERE tblitems_in.rel_type="invoice" AND taxname="VAT" AND taxrate="5.00" AND tblitems_in.rel_id=tblinvoices.id) as total_tax_single_0, discount_total, adjustment, (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id) as credits_applied, (SELECT total - (SELECT COALESCE(SUM(amount),0) FROM tblinvoicepaymentrecords WHERE invoiceid = tblinvoices.id) - (SELECT COALESCE(SUM(amount),0) FROM tblcredits WHERE tblcredits.invoice_id=tblinvoices.id)) as amount_open, status, project_id, refered_employee_id, sale_agent, lawyer_assigned ,userid,clientid,tblinvoices.id,discount_percent,tblinvoicepaymentrecords.date as collected_date
    FROM tblinvoices
    JOIN tblclients ON tblclients.userid = tblinvoices.clientid LEFT JOIN tblinvoicepaymentrecords ON tblinvoicepaymentrecords.invoiceid = tblinvoices.id
    
    WHERE  status != 5 AND (tblinvoicepaymentrecords.date BETWEEN "2021-06-01" AND "2021-06-30")
    
    ORDER BY YEAR(tblinvoices.date) DESC, number DESC
    LIMIT 0, 25
    
ERROR - 2021-06-19 15:22:29 --> Severity: Notice --> Undefined index: userid /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/relation_helper.php 176
ERROR - 2021-06-19 15:22:29 --> Severity: Notice --> Undefined index: company /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/relation_helper.php 177
ERROR - 2021-06-19 15:22:29 --> Could not find the language line "casediary"
ERROR - 2021-06-19 15:22:29 --> Could not find the language line "report_invoice_collected_amount"
ERROR - 2021-06-19 15:22:29 --> Could not find the language line "total_referral"
ERROR - 2021-06-19 15:22:39 --> Severity: Notice --> Undefined index: case_id /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2018
ERROR - 2021-06-19 15:22:39 --> Severity: Notice --> Undefined index: case_id /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2018
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:39 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: case_id /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2272
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: collected_amount /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2338
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: referral_1 /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2343
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: referral_2 /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2348
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: total_referral /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2355
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: referral_3 /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2359
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: Notice --> Undefined index: referral_4 /home1/beveryzv/smartlawyer.beveroncloud.com/application/controllers/admin/Reports.php 2364
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
ERROR - 2021-06-19 15:22:44 --> Severity: User Notice --> format_money is <strong>deprecated</strong> since version 2.3.2! Use app_format_money instead. /home1/beveryzv/smartlawyer.beveroncloud.com/application/helpers/deprecated_helper.php 25
