<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2023-01-04 13:15:16 --> Could not find the language line "#"
ERROR - 2023-01-04 13:15:16 --> Could not find the language line "search"
ERROR - 2023-01-04 13:15:16 --> Could not find the language line "#"
ERROR - 2023-01-04 13:15:16 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:15:16 --> Could not find the language line "#"
ERROR - 2023-01-04 13:15:16 --> Could not find the language line "search"
ERROR - 2023-01-04 13:23:00 --> Could not find the language line "#"
ERROR - 2023-01-04 13:23:00 --> Could not find the language line "search"
ERROR - 2023-01-04 13:23:00 --> Could not find the language line "#"
ERROR - 2023-01-04 13:23:00 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:23:00 --> Could not find the language line "#"
ERROR - 2023-01-04 13:23:00 --> Could not find the language line "search"
ERROR - 2023-01-04 13:34:01 --> Could not find the language line "#"
ERROR - 2023-01-04 13:34:01 --> Could not find the language line "search"
ERROR - 2023-01-04 13:34:01 --> Could not find the language line "#"
ERROR - 2023-01-04 13:34:01 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:34:01 --> Could not find the language line "#"
ERROR - 2023-01-04 13:34:01 --> Could not find the language line "search"
ERROR - 2023-01-04 13:34:04 --> Could not find the language line "#"
ERROR - 2023-01-04 13:34:04 --> Could not find the language line "search"
ERROR - 2023-01-04 13:34:04 --> Could not find the language line "#"
ERROR - 2023-01-04 13:34:04 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:34:04 --> Could not find the language line "#"
ERROR - 2023-01-04 13:34:04 --> Could not find the language line "search"
ERROR - 2023-01-04 13:35:12 --> Could not find the language line "#"
ERROR - 2023-01-04 13:35:12 --> Could not find the language line "search"
ERROR - 2023-01-04 13:35:12 --> Could not find the language line "#"
ERROR - 2023-01-04 13:35:12 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:35:12 --> Could not find the language line "#"
ERROR - 2023-01-04 13:35:12 --> Could not find the language line "search"
ERROR - 2023-01-04 13:35:44 --> Could not find the language line "#"
ERROR - 2023-01-04 13:35:44 --> Could not find the language line "search"
ERROR - 2023-01-04 13:35:44 --> Could not find the language line "#"
ERROR - 2023-01-04 13:35:44 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:35:44 --> Could not find the language line "#"
ERROR - 2023-01-04 13:35:44 --> Could not find the language line "search"
ERROR - 2023-01-04 13:36:53 --> Could not find the language line "#"
ERROR - 2023-01-04 13:36:53 --> Could not find the language line "search"
ERROR - 2023-01-04 13:36:53 --> Could not find the language line "#"
ERROR - 2023-01-04 13:36:53 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:36:53 --> Could not find the language line "#"
ERROR - 2023-01-04 13:36:53 --> Could not find the language line "search"
ERROR - 2023-01-04 13:46:14 --> Could not find the language line "#"
ERROR - 2023-01-04 13:46:14 --> Could not find the language line "search"
ERROR - 2023-01-04 13:46:14 --> Could not find the language line "#"
ERROR - 2023-01-04 13:46:14 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:46:14 --> Could not find the language line "#"
ERROR - 2023-01-04 13:46:14 --> Could not find the language line "search"
ERROR - 2023-01-04 13:48:26 --> Could not find the language line "#"
ERROR - 2023-01-04 13:48:26 --> Could not find the language line "search"
ERROR - 2023-01-04 13:48:26 --> Could not find the language line "#"
ERROR - 2023-01-04 13:48:26 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:48:26 --> Could not find the language line "#"
ERROR - 2023-01-04 13:48:26 --> Could not find the language line "search"
ERROR - 2023-01-04 13:49:17 --> Could not find the language line "#"
ERROR - 2023-01-04 13:49:17 --> Could not find the language line "search"
ERROR - 2023-01-04 13:49:17 --> Could not find the language line "#"
ERROR - 2023-01-04 13:49:17 --> Could not find the language line "Priority"
ERROR - 2023-01-04 13:49:17 --> Could not find the language line "#"
ERROR - 2023-01-04 13:49:17 --> Could not find the language line "search"
ERROR - 2023-01-04 13:57:40 --> Could not find the language line "comments"
ERROR - 2023-01-04 15:20:30 --> Could not find the language line "#"
ERROR - 2023-01-04 15:22:07 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ')
    
    ORDER BY tbltickets.date DESC
    LIMIT 0, 25' at line 5 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, ticketid, subject, CONCAT(tblcontacts.firstname, ' ', tblcontacts.lastname) as contact_full_name, tblservices.name as service_name, tbldepartments.name as department_name, opposteparty, status, priority, lastreply, `tbltickets`.`date` AS `tbltickets.date`, `tbltickets`.`estimated_date` AS `tbltickets.estimated_date` ,adminread,ticketkey,tbltickets.userid,statuscolor,tbltickets.name as ticket_opened_by_name,tbltickets.email,tbltickets.userid,assigned,tblclients.company
    FROM tbltickets
    LEFT JOIN tblcontacts ON tblcontacts.id = tbltickets.contactid LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department LEFT JOIN tbltickets_status ON tbltickets_status.ticketstatusid = tbltickets.status LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid LEFT JOIN tbltickets_priorities ON tbltickets_priorities.priorityid = tbltickets.priority
    
    WHERE  ( status IN (1, 2, 4)) AND tbltickets.admin =23)
    
    ORDER BY tbltickets.date DESC
    LIMIT 0, 25
    
ERROR - 2023-01-04 15:23:04 --> Query error: You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near ')
    
    ORDER BY tbltickets.date DESC
    LIMIT 0, 25' at line 5 - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS 1, ticketid, subject, CONCAT(tblcontacts.firstname, ' ', tblcontacts.lastname) as contact_full_name, tblservices.name as service_name, tbldepartments.name as department_name, opposteparty, status, priority, lastreply, `tbltickets`.`date` AS `tbltickets.date`, `tbltickets`.`estimated_date` AS `tbltickets.estimated_date` ,adminread,ticketkey,tbltickets.userid,statuscolor,tbltickets.name as ticket_opened_by_name,tbltickets.email,tbltickets.userid,assigned,tblclients.company
    FROM tbltickets
    LEFT JOIN tblcontacts ON tblcontacts.id = tbltickets.contactid LEFT JOIN tblservices ON tblservices.serviceid = tbltickets.service LEFT JOIN tbldepartments ON tbldepartments.departmentid = tbltickets.department LEFT JOIN tbltickets_status ON tbltickets_status.ticketstatusid = tbltickets.status LEFT JOIN tblclients ON tblclients.userid = tbltickets.userid LEFT JOIN tbltickets_priorities ON tbltickets_priorities.priorityid = tbltickets.priority
    
    WHERE  ( status IN (1, 2, 4)) AND tbltickets.admin =23)
    
    ORDER BY tbltickets.date DESC
    LIMIT 0, 25
    
ERROR - 2023-01-04 15:40:23 --> Could not find the language line "#"
ERROR - 2023-01-04 15:40:23 --> Could not find the language line "search"
ERROR - 2023-01-04 15:40:23 --> Could not find the language line "#"
ERROR - 2023-01-04 15:40:23 --> Could not find the language line "Priority"
ERROR - 2023-01-04 15:40:23 --> Could not find the language line "#"
ERROR - 2023-01-04 15:40:23 --> Could not find the language line "search"
ERROR - 2023-01-04 15:43:55 --> Could not find the language line "#"
ERROR - 2023-01-04 15:43:55 --> Could not find the language line "search"
ERROR - 2023-01-04 15:43:55 --> Could not find the language line "#"
ERROR - 2023-01-04 15:43:55 --> Could not find the language line "Priority"
ERROR - 2023-01-04 15:43:55 --> Could not find the language line "#"
ERROR - 2023-01-04 15:43:55 --> Could not find the language line "search"
ERROR - 2023-01-04 15:44:31 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-04 15:44:31 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-04 15:44:31 --> Could not find the language line "#"
ERROR - 2023-01-04 15:44:31 --> Could not find the language line "search"
ERROR - 2023-01-04 15:44:32 --> Could not find the language line "#"
ERROR - 2023-01-04 15:44:32 --> Could not find the language line "Priority"
ERROR - 2023-01-04 15:44:32 --> Could not find the language line "#"
ERROR - 2023-01-04 15:44:32 --> Could not find the language line "search"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "#"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "search"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "#"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "#"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "search"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:30:47 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:30:50 --> Query error: Unknown column 'clients.userid' in 'IN/ALL/ANY subquery' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblclients.company as company, client_no, phonenumber, city, tblclients.datecreated as datecreated ,userid,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    
    
    WHERE  clients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id=23)
    
    ORDER BY client_no ASC, client_no ASC
    LIMIT 0, 25
    
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "#"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "search"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "#"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "#"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "search"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:31:06 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:31:08 --> Query error: Unknown column 'clients.userid' in 'IN/ALL/ANY subquery' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblclients.company as company, client_no, phonenumber, city, tblclients.datecreated as datecreated ,userid,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    
    
    WHERE  clients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id=23)
    
    ORDER BY client_no ASC, client_no ASC
    LIMIT 0, 25
    
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "#"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "search"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "#"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "#"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "search"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:31:19 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:31:21 --> Query error: Unknown column 'clients.userid' in 'IN/ALL/ANY subquery' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblclients.company as company, client_no, phonenumber, city, tblclients.datecreated as datecreated ,userid,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    
    
    WHERE  clients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id=23)
    
    ORDER BY client_no ASC, client_no ASC
    LIMIT 0, 25
    
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "#"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "search"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "#"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "#"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "search"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:49:53 --> Could not find the language line "Priority"
ERROR - 2023-01-04 16:49:55 --> Query error: Unknown column 'clients.userid' in 'IN/ALL/ANY subquery' - Invalid query: 
    SELECT SQL_CALC_FOUND_ROWS tblclients.company as company, client_no, phonenumber, city, tblclients.datecreated as datecreated ,userid,tblclients.zip as zip,registration_confirmed
    FROM tblclients
    
    
    WHERE  clients.userid IN (SELECT customer_id FROM tblcustomer_admins WHERE staff_id=23)
    
    ORDER BY client_no ASC, client_no ASC
    LIMIT 0, 25
    
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "legal_approval_await"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "contract_approval_await"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "#"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "search"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "#"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "Priority"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "#"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "search"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "Priority"
ERROR - 2023-01-04 17:33:21 --> Could not find the language line "Priority"
