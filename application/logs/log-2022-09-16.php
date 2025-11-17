<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2022-09-16 08:25:57 --> Could not find the language line "#"
ERROR - 2022-09-16 10:10:24 --> Severity: error --> Exception: Call to undefined method Contracts_model::fetch_project_details_num_rows() D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 458
ERROR - 2022-09-16 10:10:48 --> Severity: error --> Exception: Call to undefined method Contracts_model::fetch_project_details_num_rows() D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 458
ERROR - 2022-09-16 10:12:34 --> Severity: Notice --> Undefined variable: casetype D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 458
ERROR - 2022-09-16 10:12:34 --> Severity: error --> Exception: Call to undefined method Contracts_model::fetch_contractdetails() D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 486
ERROR - 2022-09-16 10:12:59 --> Severity: error --> Exception: Call to undefined method Contracts_model::fetch_contractdetails() D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 486
ERROR - 2022-09-16 10:13:40 --> Severity: Notice --> Undefined variable: contractype D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 486
ERROR - 2022-09-16 10:13:40 --> Query error: Table 'bevera9w_wheeloffate_db.tblcontract_types' doesn't exist - Invalid query: SELECT *, `tblcontracts`.`subject` as `proejct_name`, `tblcontracts`.`id` as `id`, `tbloppositeparty`.`name` as `oppositeparty`
FROM `tblcontracts`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
JOIN `tbloppositeparty` ON `tbloppositeparty`.`id` = `tblcontracts`.`other_party`
LEFT JOIN `tblcontract_types` ON `tblcontract_types`.`id` = `tblcontracts`.`contract_type`
WHERE (`subject` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `company` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `type_stamp` LIKE "%%" ESCAPE '!')
ORDER BY `tblcontracts`.`id` DESC
 LIMIT 12
ERROR - 2022-09-16 10:14:06 --> Severity: Notice --> Undefined variable: contractype D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Contracts.php 486
ERROR - 2022-09-16 10:14:06 --> Query error: Table 'bevera9w_wheeloffate_db.tblcontract_types' doesn't exist - Invalid query: SELECT *, `tblcontracts`.`subject` as `proejct_name`, `tblcontracts`.`id` as `id`, `tbloppositeparty`.`name` as `oppositeparty`
FROM `tblcontracts`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
JOIN `tbloppositeparty` ON `tbloppositeparty`.`id` = `tblcontracts`.`other_party`
LEFT JOIN `tblcontract_types` ON `tblcontract_types`.`id` = `tblcontracts`.`contract_type`
WHERE (`subject` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `company` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `type_stamp` LIKE "%%" ESCAPE '!')
ORDER BY `tblcontracts`.`id` DESC
 LIMIT 12
ERROR - 2022-09-16 10:14:48 --> Query error: Table 'bevera9w_wheeloffate_db.tblcontract_types' doesn't exist - Invalid query: SELECT *, `tblcontracts`.`subject` as `proejct_name`, `tblcontracts`.`id` as `id`, `tbloppositeparty`.`name` as `oppositeparty`
FROM `tblcontracts`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
JOIN `tbloppositeparty` ON `tbloppositeparty`.`id` = `tblcontracts`.`other_party`
LEFT JOIN `tblcontract_types` ON `tblcontract_types`.`id` = `tblcontracts`.`contract_type`
WHERE (`subject` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `company` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `type_stamp` LIKE "%%" ESCAPE '!')
ORDER BY `tblcontracts`.`id` DESC
 LIMIT 12
ERROR - 2022-09-16 10:17:57 --> Query error: Column 'city' in where clause is ambiguous - Invalid query: SELECT *, `tblcontracts`.`subject` as `proejct_name`, `tblcontracts`.`id` as `id`, `tbloppositeparty`.`name` as `oppositeparty`
FROM `tblcontracts`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
JOIN `tbloppositeparty` ON `tbloppositeparty`.`id` = `tblcontracts`.`other_party`
WHERE (`subject` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `company` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `type_stamp` LIKE "%%" ESCAPE '!')
ORDER BY `tblcontracts`.`id` DESC
 LIMIT 12
ERROR - 2022-09-16 10:19:19 --> Query error: Column 'address' in where clause is ambiguous - Invalid query: SELECT *, `tblcontracts`.`subject` as `proejct_name`, `tblcontracts`.`id` as `id`, `tbloppositeparty`.`name` as `oppositeparty`
FROM `tblcontracts`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
JOIN `tbloppositeparty` ON `tbloppositeparty`.`id` = `tblcontracts`.`other_party`
WHERE (`subject` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `company` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `tblclients`.`city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `type_stamp` LIKE "%%" ESCAPE '!')
ORDER BY `tblcontracts`.`id` DESC
 LIMIT 12
ERROR - 2022-09-16 10:19:56 --> Query error: Unknown column 'tblprojects.clientid' in 'on clause' - Invalid query: SELECT *, `tblcontracts`.`subject` as `proejct_name`, `tblcontracts`.`id` as `id`, `tbloppositeparty`.`name` as `oppositeparty`
FROM `tblcontracts`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
JOIN `tbloppositeparty` ON `tbloppositeparty`.`id` = `tblcontracts`.`other_party`
WHERE (`subject` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `company` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `tblclients`.`city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `tblclients`.`address` LIKE "%%" ESCAPE '!'
                OR `type_stamp` LIKE "%%" ESCAPE '!')
ORDER BY `tblcontracts`.`id` DESC
 LIMIT 12
ERROR - 2022-09-16 10:21:28 --> Could not find the language line "#"
ERROR - 2022-09-16 10:21:48 --> Could not find the language line "#"
ERROR - 2022-09-16 10:22:19 --> Query error: Table 'bevera9w_wheeloffate_db.tblcontract_type' doesn't exist - Invalid query: SELECT *
FROM `tblcontract_type`
WHERE `id` = '16'
ERROR - 2022-09-16 10:22:43 --> Could not find the language line "#"
ERROR - 2022-09-16 10:22:57 --> Could not find the language line "#"
ERROR - 2022-09-16 10:23:10 --> Query error: Table 'bevera9w_wheeloffate_db.tblcontract_type' doesn't exist - Invalid query: SELECT *
FROM `tblcontract_type`
WHERE `id` = '13'
ERROR - 2022-09-16 10:23:39 --> Query error: Table 'bevera9w_wheeloffate_db.tblcontracts_type' doesn't exist - Invalid query: SELECT *
FROM `tblcontracts_type`
WHERE `id` = '13'
ERROR - 2022-09-16 08:32:47 --> Severity: error --> Exception: syntax error, unexpected '$data' (T_VARIABLE) D:\xampp\htdocs\wheeloffate.beveron.net\application\controllers\admin\Dashboard.php 90
ERROR - 2022-09-16 10:55:46 --> Severity: Notice --> Undefined offset: 10 D:\xampp\htdocs\wheeloffate.beveron.net\application\helpers\datatables_helper.php 162
ERROR - 2022-09-16 10:59:05 --> Could not find the language line "#"
ERROR - 2022-09-16 10:59:23 --> Could not find the language line "#"
ERROR - 2022-09-16 11:01:52 --> Could not find the language line "#"
