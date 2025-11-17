<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2022-02-11 13:41:17 --> Could not find the language line "lawyer_timesheets"
ERROR - 2022-02-11 13:41:17 --> Severity: Notice --> Undefined variable: payments_years /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:41:17 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:41:17 --> Could not find the language line "billable"
ERROR - 2022-02-11 13:41:17 --> Could not find the language line "non_billable"
ERROR - 2022-02-11 13:41:17 --> Could not find the language line "remark"
ERROR - 2022-02-11 13:41:17 --> Could not find the language line "update_date"
ERROR - 2022-02-11 13:41:17 --> Severity: Notice --> Undefined variable: proposal_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:41:17 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:41:17 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:41:17 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:41:17 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:41:17 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:41:32 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'boscoleg_legal_counsel_db.tblcase_details.id' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblcase_details.id as id, tblprojects.name as case_title, tblprojects.execution_amount as execution_amount, tblprojects.outstanding_amount as outstanding_amount, tblprojects.start_date as start_date, tblprojects.opposite_party as opposite_party, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.execution_amount) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id and tblcase_details.instance_id!="5") as judgement_amount, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.claiming_amount) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as allclaim_amount, tblprojects.claiming_amount as claiming_amount, (SELECT SUM(tblexpenses.paid_amount) FROM tblexpenses WHERE tblexpenses.project_id=tblprojects.id ) as expenses, (SELECT  sum(tblrecoveries_installments.installment_amount) FROM tblrecoveries_installments WHERE tblrecoveries_installments.recovery_id=tblprojects.id and recovery_type="project_recovery" and installment_status="paid") as paid_amount, (SELECT  GROUP_CONCAT( DISTINCT tblall_assignees.staff_id SEPARATOR ",") FROM tblall_assignees WHERE tblall_assignees.project_id=tblprojects.id  ORDER BY tblall_assignees.id) as lawyer_ids, (SELECT  GROUP_CONCAT( DISTINCT tblcase_details.legal_cordinator SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as legal_ids, (SELECT GROUP_CONCAT(tblcase_details.court_id SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id ) as court_id, tblclients.userid as userid, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.case_number) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as case_number, (SELECT GROUP_CONCAT(tblcase_details.instance_casenature SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id ORDER BY tblcase_details.id desc limit 1) as casenature_id, tblprojects.status as status, (SELECT   GROUP_CONCAT(CONCAT_WS(" - ",tbldocument_types.name,tblcourt_orders.order_date,tblcourt_orders.end_date,"Active") SEPARATOR "<br>" )  FROM tblcourt_orders join tbldocument_types ON tbldocument_types.id=tblcourt_orders.documentid WHERE tblcourt_orders.project_id=tblprojects.id AND tblcourt_orders.active="1" ) as courtorders ,tblcase_details.project_id,tblclients.company as company
    FROM tblcase_details
    INNER JOIN tblprojects ON tblprojects.id = tblcase_details.project_id INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid
    
    WHERE  tblcase_details.instance_id =5 AND tblprojects.case_type ="court_case"
    GROUP BY tblprojects.id
    ORDER BY tblprojects.execution_amount DESC, tblcase_details.id DESC
    LIMIT 0, 25
    
ERROR - 2022-02-11 13:43:00 --> Could not find the language line "lawyer_timesheets"
ERROR - 2022-02-11 13:43:00 --> Severity: Notice --> Undefined variable: payments_years /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:43:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:43:00 --> Could not find the language line "billable"
ERROR - 2022-02-11 13:43:00 --> Could not find the language line "non_billable"
ERROR - 2022-02-11 13:43:00 --> Could not find the language line "remark"
ERROR - 2022-02-11 13:43:00 --> Could not find the language line "update_date"
ERROR - 2022-02-11 13:43:00 --> Severity: Notice --> Undefined variable: proposal_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:43:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:43:00 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:43:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:43:00 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:43:00 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:43:12 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'boscoleg_legal_counsel_db.tblcase_details.id' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblcase_details.id as id, tblprojects.name as case_title, tblprojects.outstanding_amount as outstanding_amount, tblprojects.opposite_party as opposite_party, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.claiming_amount) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as allclaim_amount, tblprojects.claiming_amount as claiming_amount, (SELECT SUM(tblexpenses.paid_amount) FROM tblexpenses WHERE tblexpenses.project_id=tblprojects.id ) as expenses, (SELECT  sum(tblrecoveries_installments.installment_amount) FROM tblrecoveries_installments WHERE tblrecoveries_installments.recovery_id=tblprojects.id and recovery_type="project_recovery" and installment_status="paid") as paid_amount, (SELECT  GROUP_CONCAT( DISTINCT tblall_assignees.staff_id SEPARATOR ",") FROM tblall_assignees WHERE tblall_assignees.project_id=tblprojects.id  ORDER BY tblall_assignees.id) as lawyer_ids, (SELECT  GROUP_CONCAT( DISTINCT tblcase_details.legal_cordinator SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as legal_ids, tblclients.userid as userid, (SELECT GROUP_CONCAT(tblcase_details.instance_casenature SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id ORDER BY tblcase_details.id desc limit 1) as casenature_id, tblprojects.status as status ,tblcase_details.project_id,tblclients.company as company
    FROM tblcase_details
    INNER JOIN tblprojects ON tblprojects.id = tblcase_details.project_id INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid
    
    WHERE  tblcase_details.instance_id =19 AND tblprojects.case_type ="court_case"
    GROUP BY tblprojects.id
    ORDER BY tblprojects.outstanding_amount DESC, tblcase_details.id DESC
    LIMIT 0, 25
    
ERROR - 2022-02-11 13:43:30 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'boscoleg_legal_counsel_db.tblcase_details.id' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblcase_details.id as id, tblprojects.name as case_title, tblprojects.outstanding_amount as outstanding_amount, tblprojects.opposite_party as opposite_party, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.claiming_amount) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as allclaim_amount, tblprojects.claiming_amount as claiming_amount, (SELECT SUM(tblexpenses.paid_amount) FROM tblexpenses WHERE tblexpenses.project_id=tblprojects.id ) as expenses, (SELECT  sum(tblrecoveries_installments.installment_amount) FROM tblrecoveries_installments WHERE tblrecoveries_installments.recovery_id=tblprojects.id and recovery_type="project_recovery" and installment_status="paid") as paid_amount, (SELECT  GROUP_CONCAT( DISTINCT tblall_assignees.staff_id SEPARATOR ",") FROM tblall_assignees WHERE tblall_assignees.project_id=tblprojects.id  ORDER BY tblall_assignees.id) as lawyer_ids, (SELECT  GROUP_CONCAT( DISTINCT tblcase_details.legal_cordinator SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as legal_ids, tblclients.userid as userid, (SELECT GROUP_CONCAT(tblcase_details.instance_casenature SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id ORDER BY tblcase_details.id desc limit 1) as casenature_id, tblprojects.status as status ,tblcase_details.project_id,tblclients.company as company
    FROM tblcase_details
    INNER JOIN tblprojects ON tblprojects.id = tblcase_details.project_id INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid
    
    WHERE  tblcase_details.instance_id =19 AND tblprojects.case_type ="court_case"
    GROUP BY tblprojects.id
    ORDER BY tblprojects.outstanding_amount DESC, tblcase_details.id DESC
    LIMIT 0, 25
    
ERROR - 2022-02-11 13:44:21 --> Could not find the language line "lawyer_timesheets"
ERROR - 2022-02-11 13:44:21 --> Severity: Notice --> Undefined variable: payments_years /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:44:21 --> Could not find the language line "billable"
ERROR - 2022-02-11 13:44:21 --> Could not find the language line "non_billable"
ERROR - 2022-02-11 13:44:21 --> Could not find the language line "remark"
ERROR - 2022-02-11 13:44:21 --> Could not find the language line "update_date"
ERROR - 2022-02-11 13:44:21 --> Severity: Notice --> Undefined variable: proposal_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:44:21 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:44:21 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:44:21 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:44:29 --> Query error: Expression #1 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'boscoleg_legal_counsel_db.tblcase_details.id' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblcase_details.id as id, tblprojects.name as case_title, tblprojects.execution_amount as execution_amount, tblprojects.outstanding_amount as outstanding_amount, tblprojects.start_date as start_date, tblprojects.opposite_party as opposite_party, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.execution_amount) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id and tblcase_details.instance_id!="5") as judgement_amount, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.claiming_amount) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as allclaim_amount, tblprojects.claiming_amount as claiming_amount, (SELECT SUM(tblexpenses.paid_amount) FROM tblexpenses WHERE tblexpenses.project_id=tblprojects.id ) as expenses, (SELECT  GROUP_CONCAT( DISTINCT tblall_assignees.staff_id SEPARATOR ",") FROM tblall_assignees WHERE tblall_assignees.project_id=tblprojects.id  ORDER BY tblall_assignees.id) as lawyer_ids, (SELECT  GROUP_CONCAT( DISTINCT tblcase_details.legal_cordinator SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as legal_ids, (SELECT GROUP_CONCAT(tblcase_details.court_id SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id ) as court_id, tblclients.userid as userid, (SELECT GROUP_CONCAT(CONCAT(details_type," ^ ",tblcase_details.case_number) SEPARATOR "~") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id  ORDER BY tblcase_details.id) as case_number, (SELECT GROUP_CONCAT(tblcase_details.instance_casenature SEPARATOR ",") FROM tblcase_details WHERE tblcase_details.project_id=tblprojects.id ORDER BY tblcase_details.id desc limit 1 ) as casenature_id, tblprojects.status as status ,tblcase_details.project_id,tblclients.company as company
    FROM tblcase_details
    INNER JOIN tblprojects ON tblprojects.id = tblcase_details.project_id INNER JOIN tblclients ON tblclients.userid = tblprojects.clientid
    
    WHERE  tblprojects.case_type ="court_case"
    GROUP BY tblprojects.id
    ORDER BY tblprojects.execution_amount DESC, tblcase_details.id DESC
    LIMIT 0, 25
    
ERROR - 2022-02-11 13:45:45 --> Could not find the language line "lawyer_timesheets"
ERROR - 2022-02-11 13:45:45 --> Severity: Notice --> Undefined variable: payments_years /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:45:45 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:45:45 --> Could not find the language line "billable"
ERROR - 2022-02-11 13:45:45 --> Could not find the language line "non_billable"
ERROR - 2022-02-11 13:45:45 --> Could not find the language line "remark"
ERROR - 2022-02-11 13:45:45 --> Could not find the language line "update_date"
ERROR - 2022-02-11 13:45:45 --> Severity: Notice --> Undefined variable: proposal_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:45:45 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:45:45 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:45:45 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:45:45 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:45:45 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:45:51 --> Could not find the language line ""
ERROR - 2022-02-11 13:52:54 --> Could not find the language line "lawyer_timesheets"
ERROR - 2022-02-11 13:52:54 --> Severity: Notice --> Undefined variable: payments_years /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:52:54 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/matters.php 193
ERROR - 2022-02-11 13:52:54 --> Could not find the language line "billable"
ERROR - 2022-02-11 13:52:54 --> Could not find the language line "non_billable"
ERROR - 2022-02-11 13:52:54 --> Could not find the language line "remark"
ERROR - 2022-02-11 13:52:54 --> Could not find the language line "update_date"
ERROR - 2022-02-11 13:52:54 --> Severity: Notice --> Undefined variable: proposal_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:52:54 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 139
ERROR - 2022-02-11 13:52:54 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:52:54 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 150
ERROR - 2022-02-11 13:52:54 --> Severity: Notice --> Undefined variable: invoice_taxes /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
ERROR - 2022-02-11 13:52:54 --> Severity: Warning --> Invalid argument supplied for foreach() /home/boscolegal/public_html/smartlegal/application/views/admin/reports/includes/matter_js.php 161
