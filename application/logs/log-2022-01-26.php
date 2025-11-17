<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2022-01-26 11:33:52 --> Query error: Unknown column 'dateapproved' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS `tblexpenses`.`category` AS `tblexpenses.category`, amount, expense_name, tax, tax2, (SELECT taxrate FROM tbltaxes WHERE id=tblexpenses.tax), amount as amount_with_tax, billable, date, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, tbloppositeparty.name as opposite_party, invoiceid, dateapproved, reference_no, paymentmode ,tblexpenses_categories.name as category_name,tblexpenses.id as id,tblexpenses.clientid,currency
    FROM tblprojects
    INNER JOIN tblexpenses ON tblexpenses.project_id = tblprojects.id LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid LEFT JOIN tblexpenses_categories ON tblexpenses_categories.id = tblexpenses.category
    
    WHERE  ( YEAR(date) IN (2022, 2021)) AND ( YEAR(date) IN (2022, 2021))
    
    ORDER BY date DESC
    LIMIT 0, 25
    
ERROR - 2022-01-26 11:34:03 --> Query error: Unknown column 'dateapproved' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS `tblexpenses`.`category` AS `tblexpenses.category`, amount, expense_name, tax, tax2, (SELECT taxrate FROM tbltaxes WHERE id=tblexpenses.tax), amount as amount_with_tax, billable, date, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, tbloppositeparty.name as opposite_party, invoiceid, dateapproved, reference_no, paymentmode ,tblexpenses_categories.name as category_name,tblexpenses.id as id,tblexpenses.clientid,currency
    FROM tblprojects
    INNER JOIN tblexpenses ON tblexpenses.project_id = tblprojects.id LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblprojects.opposite_party LEFT JOIN tblclients ON tblclients.userid = tblexpenses.clientid LEFT JOIN tblexpenses_categories ON tblexpenses_categories.id = tblexpenses.category
    
    WHERE  ( YEAR(date) IN (2022, 2021)) AND ( YEAR(date) IN (2022, 2021))
    
    ORDER BY date DESC
    LIMIT 0, 25
    
ERROR - 2022-01-26 11:37:32 --> Could not find the language line "hearing"
ERROR - 2022-01-26 11:37:32 --> Could not find the language line "new"
ERROR - 2022-01-26 11:37:52 --> Could not find the language line "hearing"
ERROR - 2022-01-26 11:37:52 --> Could not find the language line "new"
ERROR - 2022-01-26 11:38:05 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/themes/smartlegal/views/my_expenseapproveall_pdf.php 84
ERROR - 2022-01-26 11:38:05 --> Severity: Warning --> Cannot modify header information - headers already sent by (output started at /home/boscolegal/public_html/smartlegal/system/core/Exceptions.php:271) /home/boscolegal/public_html/smartlegal/application/vendor/tecnickcom/tcpdf/tcpdf.php 7690
