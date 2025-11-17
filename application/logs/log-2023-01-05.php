<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-01-05 12:03:21 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "#"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "search"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "#"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "#"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "search"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:03:21 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:03:25 --> Severity: Notice --> Undefined offset: 8 D:\xampp\htdocs\wheeloffate.beveron.net\application\helpers\datatables_helper.php 162
ERROR - 2023-01-05 12:03:25 --> Query error: Column 'addedfrom' in field list is ambiguous - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblapprovals.id as id, tbltickets.subject as name, opposteparty, tbltickets.date as submission_date, rel_id, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, addedfrom, dateadded, approval_name ,userid,rel_type
    FROM tblapprovals
    LEFT JOIN tbltickets ON tbltickets.ticketid = tblapprovals.rel_id INNER JOIN tblclients ON tblclients.userid = tbltickets.userid
    
    WHERE  tblapprovals.rel_type = "ticket" AND tblapprovals.approval_status = 2
    
    ORDER BY CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END DESC, tblapprovals.id DESC
    LIMIT 0, 25
    
ERROR - 2023-01-05 12:03:34 --> Severity: Notice --> Undefined offset: 8 D:\xampp\htdocs\wheeloffate.beveron.net\application\helpers\datatables_helper.php 162
ERROR - 2023-01-05 12:03:34 --> Query error: Column 'addedfrom' in field list is ambiguous - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblapprovals.id as id, tbltickets.subject as name, opposteparty, tbltickets.date as submission_date, rel_id, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, addedfrom, dateadded, approval_name ,userid,rel_type
    FROM tblapprovals
    LEFT JOIN tbltickets ON tbltickets.ticketid = tblapprovals.rel_id INNER JOIN tblclients ON tblclients.userid = tbltickets.userid
    
    WHERE  tblapprovals.rel_type = "ticket" AND tblapprovals.approval_status = 2
    
    ORDER BY CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END DESC, tblapprovals.id DESC
    LIMIT 0, 25
    
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "#"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "search"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "#"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "#"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "search"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:04:36 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:04:47 --> Severity: Notice --> Undefined offset: 8 D:\xampp\htdocs\wheeloffate.beveron.net\application\helpers\datatables_helper.php 162
ERROR - 2023-01-05 12:04:47 --> Query error: Column 'userid' in field list is ambiguous - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblapprovals.id as id, tbltickets.subject as name, opposteparty, tbltickets.date as submission_date, rel_id, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, tblapprovals.addedfrom as addedfrom, dateadded, approval_name ,userid,rel_type
    FROM tblapprovals
    LEFT JOIN tbltickets ON tbltickets.ticketid = tblapprovals.rel_id INNER JOIN tblclients ON tblclients.userid = tbltickets.userid
    
    WHERE  tblapprovals.rel_type = "ticket" AND tblapprovals.approval_status = 2
    
    ORDER BY CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END DESC, tblapprovals.id DESC
    LIMIT 0, 25
    
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "#"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "search"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "#"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "#"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "search"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:05:44 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:05:53 --> Severity: Notice --> Undefined offset: 8 D:\xampp\htdocs\wheeloffate.beveron.net\application\helpers\datatables_helper.php 162
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "#"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "search"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "#"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "#"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "search"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:10:53 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:10:59 --> Severity: Notice --> Undefined offset: 8 D:\xampp\htdocs\wheeloffate.beveron.net\application\helpers\datatables_helper.php 162
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "#"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "search"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "#"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "#"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "search"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:13:14 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:13:18 --> Query error: Unknown column 'tbltickets_status.statuscolor' in 'field list' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblapprovals.id as id, tbltickets.subject as name, opposteparty, tbltickets.date as submission_date, CASE company WHEN ' ' THEN (SELECT CONCAT(firstname, ' ', lastname) FROM tblcontacts WHERE userid = tblclients.userid and is_primary = 1) ELSE company END as company, tblapprovals.addedfrom as addedfrom, dateadded, approval_name ,tbltickets.ticketid,tbltickets.userid,tbltickets_status.statuscolor as statuscolor
    FROM tblapprovals
    LEFT JOIN tbltickets ON tbltickets.ticketid = tblapprovals.rel_id INNER JOIN tblclients ON tblclients.userid = tbltickets.userid
    
    WHERE  tblapprovals.rel_type = "ticket" AND tblapprovals.approval_status = 2
    
    ORDER BY tblapprovals.addedfrom DESC, tblapprovals.id DESC
    LIMIT 0, 25
    
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "#"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "search"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "#"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "#"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "search"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:13:44 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "#"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "search"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "#"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "#"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "search"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:15:52 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "#"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "search"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "#"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "#"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "search"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:16:19 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "#"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "search"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "#"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "#"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "search"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:16:51 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "#"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "search"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "#"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "#"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "search"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:50:35 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:50:38 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:50:38 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:50:51 --> Could not find the language line "#"
ERROR - 2023-01-05 12:51:23 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "search"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "search"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:07 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:57:10 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:10 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:12 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "search"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "#"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "search"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "approvaladded"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:24 --> Could not find the language line "Priority"
ERROR - 2023-01-05 12:57:31 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:31 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:40 --> Could not find the language line "approve"
ERROR - 2023-01-05 12:57:40 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:31:00 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:31:00 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:31:03 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:31:03 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:31:30 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:31:30 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "#"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "search"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "#"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "#"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "search"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "addedfrom"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "approvalname"
ERROR - 2023-01-05 13:42:44 --> Could not find the language line "approve"
ERROR - 2023-01-05 13:48:10 --> Could not find the language line "#"
ERROR - 2023-01-05 13:48:10 --> Could not find the language line "search"
ERROR - 2023-01-05 13:48:10 --> Could not find the language line "#"
ERROR - 2023-01-05 13:48:10 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:48:10 --> Could not find the language line "#"
ERROR - 2023-01-05 13:48:10 --> Could not find the language line "search"
ERROR - 2023-01-05 13:48:54 --> Could not find the language line "#"
ERROR - 2023-01-05 13:48:54 --> Could not find the language line "search"
ERROR - 2023-01-05 13:48:54 --> Could not find the language line "#"
ERROR - 2023-01-05 13:48:54 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:48:54 --> Could not find the language line "#"
ERROR - 2023-01-05 13:48:54 --> Could not find the language line "search"
ERROR - 2023-01-05 13:52:06 --> Could not find the language line "#"
ERROR - 2023-01-05 13:52:06 --> Could not find the language line "search"
ERROR - 2023-01-05 13:52:06 --> Could not find the language line "#"
ERROR - 2023-01-05 13:52:06 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:52:06 --> Could not find the language line "#"
ERROR - 2023-01-05 13:52:06 --> Could not find the language line "search"
ERROR - 2023-01-05 13:52:49 --> Could not find the language line "#"
ERROR - 2023-01-05 13:52:49 --> Could not find the language line "search"
ERROR - 2023-01-05 13:52:49 --> Could not find the language line "#"
ERROR - 2023-01-05 13:52:49 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:52:49 --> Could not find the language line "#"
ERROR - 2023-01-05 13:52:49 --> Could not find the language line "search"
ERROR - 2023-01-05 13:53:15 --> Could not find the language line "#"
ERROR - 2023-01-05 13:53:35 --> Could not find the language line "#"
ERROR - 2023-01-05 13:53:35 --> Could not find the language line "search"
ERROR - 2023-01-05 13:53:35 --> Could not find the language line "#"
ERROR - 2023-01-05 13:53:35 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:53:35 --> Could not find the language line "#"
ERROR - 2023-01-05 13:53:35 --> Could not find the language line "search"
ERROR - 2023-01-05 13:59:12 --> Could not find the language line "#"
ERROR - 2023-01-05 13:59:12 --> Could not find the language line "search"
ERROR - 2023-01-05 13:59:12 --> Could not find the language line "#"
ERROR - 2023-01-05 13:59:12 --> Could not find the language line "Priority"
ERROR - 2023-01-05 13:59:12 --> Could not find the language line "#"
ERROR - 2023-01-05 13:59:12 --> Could not find the language line "search"
ERROR - 2023-01-05 11:08:55 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:10:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:47 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:34:49 --> 404 Page Not Found: admin/Dashboard/index%20
ERROR - 2023-01-05 11:35:22 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:35:22 --> 404 Page Not Found: /index
ERROR - 2023-01-05 14:35:24 --> Could not find the language line "#"
ERROR - 2023-01-05 14:35:24 --> Could not find the language line "search"
ERROR - 2023-01-05 14:35:24 --> Could not find the language line "#"
ERROR - 2023-01-05 14:35:24 --> Could not find the language line "Priority"
ERROR - 2023-01-05 14:35:24 --> Could not find the language line "#"
ERROR - 2023-01-05 14:35:24 --> Could not find the language line "search"
ERROR - 2023-01-05 11:49:16 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:49:17 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:09 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:09 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:09 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:50:09 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 11:51:20 --> 404 Page Not Found: /index
ERROR - 2023-01-05 14:51:26 --> Could not find the language line "#"
ERROR - 2023-01-05 14:51:26 --> Could not find the language line "search"
ERROR - 2023-01-05 14:51:26 --> Could not find the language line "#"
ERROR - 2023-01-05 14:51:26 --> Could not find the language line "Priority"
ERROR - 2023-01-05 14:51:26 --> Could not find the language line "#"
ERROR - 2023-01-05 14:51:26 --> Could not find the language line "search"
ERROR - 2023-01-05 14:54:06 --> Could not find the language line "#"
ERROR - 2023-01-05 14:54:06 --> Could not find the language line "search"
ERROR - 2023-01-05 14:54:06 --> Could not find the language line "#"
ERROR - 2023-01-05 14:54:06 --> Could not find the language line "Priority"
ERROR - 2023-01-05 14:54:07 --> Could not find the language line "#"
ERROR - 2023-01-05 14:54:07 --> Could not find the language line "search"
ERROR - 2023-01-05 12:01:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:01:28 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:01:31 --> Could not find the language line "#"
ERROR - 2023-01-05 15:01:31 --> Could not find the language line "search"
ERROR - 2023-01-05 15:01:31 --> Could not find the language line "#"
ERROR - 2023-01-05 15:01:31 --> Could not find the language line "Priority"
ERROR - 2023-01-05 15:01:31 --> Could not find the language line "#"
ERROR - 2023-01-05 15:01:31 --> Could not find the language line "search"
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:44 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:04:45 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:04:46 --> Could not find the language line "#"
ERROR - 2023-01-05 15:04:46 --> Could not find the language line "search"
ERROR - 2023-01-05 15:04:46 --> Could not find the language line "#"
ERROR - 2023-01-05 15:04:46 --> Could not find the language line "Priority"
ERROR - 2023-01-05 15:04:46 --> Could not find the language line "#"
ERROR - 2023-01-05 15:04:46 --> Could not find the language line "search"
ERROR - 2023-01-05 15:06:15 --> Could not find the language line "#"
ERROR - 2023-01-05 15:06:15 --> Could not find the language line "search"
ERROR - 2023-01-05 15:06:15 --> Could not find the language line "#"
ERROR - 2023-01-05 15:06:15 --> Could not find the language line "Priority"
ERROR - 2023-01-05 15:06:15 --> Could not find the language line "#"
ERROR - 2023-01-05 15:06:15 --> Could not find the language line "search"
ERROR - 2023-01-05 12:06:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:06:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:06:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:06:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:06:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:06:26 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:36:45 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:36:45 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:36:45 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:36:45 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:36:45 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:36:45 --> Could not find the language line "comments"
ERROR - 2023-01-05 12:36:49 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:36:49 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:36:57 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:44:37 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:44:37 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:45:01 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:45:01 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:45:01 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:45:01 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:45:01 --> Could not find the language line "comments"
ERROR - 2023-01-05 15:45:01 --> Could not find the language line "comments"
ERROR - 2023-01-05 12:45:05 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:45:05 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Notice --> Undefined variable: where D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:51:01 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 77
ERROR - 2023-01-05 15:51:01 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 84
ERROR - 2023-01-05 15:51:01 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 84
ERROR - 2023-01-05 15:51:01 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 84
ERROR - 2023-01-05 15:51:01 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 12:51:01 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:51:01 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:52:14 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:52:14 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 80
ERROR - 2023-01-05 15:52:14 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 80
ERROR - 2023-01-05 15:52:14 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 80
ERROR - 2023-01-05 15:52:14 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 12:52:15 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:52:15 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:53:48 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 77
ERROR - 2023-01-05 15:53:48 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 81
ERROR - 2023-01-05 15:53:48 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 81
ERROR - 2023-01-05 15:53:48 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 81
ERROR - 2023-01-05 15:53:48 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 12:53:49 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:53:49 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:55:54 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:55:54 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 80
ERROR - 2023-01-05 15:55:54 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 80
ERROR - 2023-01-05 15:55:54 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 80
ERROR - 2023-01-05 15:55:54 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 12:55:54 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:55:54 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:58:04 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 76
ERROR - 2023-01-05 15:58:04 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 12:58:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 12:58:04 --> 404 Page Not Found: /index
ERROR - 2023-01-05 16:00:41 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 82
ERROR - 2023-01-05 16:00:41 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 83
ERROR - 2023-01-05 13:00:42 --> 404 Page Not Found: /index
ERROR - 2023-01-05 13:00:42 --> 404 Page Not Found: /index
ERROR - 2023-01-05 16:08:05 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 75
ERROR - 2023-01-05 16:08:05 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 13:08:05 --> 404 Page Not Found: /index
ERROR - 2023-01-05 13:08:05 --> 404 Page Not Found: /index
ERROR - 2023-01-05 18:15:57 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 75
ERROR - 2023-01-05 18:15:57 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 15:15:58 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:15:58 --> 404 Page Not Found: /index
ERROR - 2023-01-05 18:16:32 --> Severity: Warning --> Illegal string offset 'ticketstatusid' D:\xampp\htdocs\wheeloffate.beveron.net\application\views\admin\app_home\index.php 75
ERROR - 2023-01-05 18:16:32 --> Query error: Unknown column 't' in 'where clause' - Invalid query: SELECT COUNT(*) AS `numrows`
FROM `tbltickets`
WHERE `status` = `t`
ERROR - 2023-01-05 15:16:33 --> 404 Page Not Found: /index
ERROR - 2023-01-05 15:16:33 --> 404 Page Not Found: /index
