<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2022-02-01 11:31:51 --> Query error: Expression #6 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'boscoleg_legal_counsel_db.tblexpenses.date' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT sum(amount)as amount, sum(paid_amount) as paid_amount, sum(last_amount)as last_amount, sum(balance_amount) as balance_amount, sum(vat_amount) as vat_amount, `tblexpenses`.`date` as `date`, `tblexpenses_categories`.`name` as `category_name`, `tblexpenses`.`note`
FROM `tblexpenses`
LEFT JOIN `tblclients` ON `tblclients`.`userid` = `tblexpenses`.`clientid`
JOIN `tblexpenses_categories` ON `tblexpenses_categories`.`id` = `tblexpenses`.`category`
WHERE `project_id` = '417'
GROUP BY `category`
ERROR - 2022-02-01 11:33:30 --> Query error: FUNCTION boscoleg_legal_counsel_db.tblmax does not exist - Invalid query: SELECT sum(amount)as amount, sum(paid_amount) as paid_amount, sum(last_amount)as last_amount, sum(balance_amount) as balance_amount, sum(vat_amount) as vat_amount, tblmax(expenses.date) as date, `tblexpenses_categories`.`name` as `category_name`, `tblexpenses`.`note`
FROM `tblexpenses`
LEFT JOIN `tblclients` ON `tblclients`.`userid` = `tblexpenses`.`clientid`
JOIN `tblexpenses_categories` ON `tblexpenses_categories`.`id` = `tblexpenses`.`category`
WHERE `project_id` = '417'
GROUP BY `category`
ERROR - 2022-02-01 11:34:09 --> Query error: FUNCTION boscoleg_legal_counsel_db.tbl does not exist - Invalid query: SELECT sum(amount)as amount, sum(paid_amount) as paid_amount, sum(last_amount)as last_amount, sum(balance_amount) as balance_amount, sum(vat_amount) as vat_amount, tbl(max(expenses.date)) as date, `tblexpenses_categories`.`name` as `category_name`, `tblexpenses`.`note`
FROM `tblexpenses`
LEFT JOIN `tblclients` ON `tblclients`.`userid` = `tblexpenses`.`clientid`
JOIN `tblexpenses_categories` ON `tblexpenses_categories`.`id` = `tblexpenses`.`category`
WHERE `project_id` = '417'
GROUP BY `category`
ERROR - 2022-02-01 11:35:21 --> Query error: Expression #8 of SELECT list is not in GROUP BY clause and contains nonaggregated column 'boscoleg_legal_counsel_db.tblexpenses.note' which is not functionally dependent on columns in GROUP BY clause; this is incompatible with sql_mode=only_full_group_by - Invalid query: SELECT sum(amount)as amount, sum(paid_amount) as paid_amount, sum(last_amount)as last_amount, sum(balance_amount) as balance_amount, sum(vat_amount) as vat_amount, max(tblexpenses.date) as date, `tblexpenses_categories`.`name` as `category_name`, `tblexpenses`.`note`
FROM `tblexpenses`
LEFT JOIN `tblclients` ON `tblclients`.`userid` = `tblexpenses`.`clientid`
JOIN `tblexpenses_categories` ON `tblexpenses_categories`.`id` = `tblexpenses`.`category`
WHERE `project_id` = '417'
GROUP BY `category`
