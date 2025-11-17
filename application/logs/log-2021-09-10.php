<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

ERROR - 2021-09-10 16:36:36 --> Could not find the language line "You assigned a corporate recovery [1-O/s from JK Trading LLC] to Shalet D"
ERROR - 2021-09-10 16:36:36 --> Severity: Notice --> Undefined variable: case_types /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 79
ERROR - 2021-09-10 16:36:36 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 79
ERROR - 2021-09-10 16:36:36 --> Severity: Notice --> Undefined variable: proj_statuses /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 87
ERROR - 2021-09-10 16:36:36 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 87
ERROR - 2021-09-10 16:36:36 --> Severity: Notice --> Undefined variable: projects_ /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 127
ERROR - 2021-09-10 16:36:36 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/helpers/fields_helper.php 332
ERROR - 2021-09-10 16:36:36 --> Severity: Notice --> Undefined variable: clients_ /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 133
ERROR - 2021-09-10 16:36:36 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/helpers/fields_helper.php 332
ERROR - 2021-09-10 16:36:43 --> Query error: Table 'bevera9w_legal_counsel_db.tblcase_natures' doesn't exist - Invalid query: SELECT *, `tblprojects`.`name` as `proejct_name`, `tblprojects`.`id` as `id`
FROM `tblprojects`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
LEFT JOIN `tblcase_natures` ON `tblcase_natures`.`id` = `tblprojects`.`case_nature`
WHERE (`company` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `tblprojects`.`name` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `case_type` LIKE "%%" ESCAPE '!'
                OR `tblcase_natures`.`name` LIKE "%%" ESCAPE '!')
ORDER BY `tblprojects`.`id` DESC
 LIMIT 12
ERROR - 2021-09-10 16:36:51 --> Could not find the language line "You assigned a corporate recovery [1-O/s from JK Trading LLC] to Shalet D"
ERROR - 2021-09-10 16:36:51 --> Severity: Notice --> Undefined variable: case_types /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 79
ERROR - 2021-09-10 16:36:51 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 79
ERROR - 2021-09-10 16:36:51 --> Severity: Notice --> Undefined variable: proj_statuses /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 87
ERROR - 2021-09-10 16:36:51 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 87
ERROR - 2021-09-10 16:36:51 --> Severity: Notice --> Undefined variable: projects_ /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 127
ERROR - 2021-09-10 16:36:51 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/helpers/fields_helper.php 332
ERROR - 2021-09-10 16:36:51 --> Severity: Notice --> Undefined variable: clients_ /home2/bevera9w/legalcounsel.beveron.net/application/views/admin/dashboard/widgets/summary.php 133
ERROR - 2021-09-10 16:36:51 --> Severity: Warning --> Invalid argument supplied for foreach() /home2/bevera9w/legalcounsel.beveron.net/application/helpers/fields_helper.php 332
ERROR - 2021-09-10 16:36:55 --> Query error: Table 'bevera9w_legal_counsel_db.tblcase_natures' doesn't exist - Invalid query: SELECT *, `tblprojects`.`name` as `proejct_name`, `tblprojects`.`id` as `id`
FROM `tblprojects`
JOIN `tblclients` ON `tblclients`.`userid` = `tblprojects`.`clientid`
LEFT JOIN `tblcase_natures` ON `tblcase_natures`.`id` = `tblprojects`.`case_nature`
WHERE (`company` LIKE "%%" ESCAPE '!'
                OR `description` LIKE "%%" ESCAPE '!'
                OR `tblprojects`.`name` LIKE "%%" ESCAPE '!'
                OR `vat` LIKE "%%" ESCAPE '!'
                OR `phonenumber` LIKE "%%" ESCAPE '!'
                OR `city` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `state` LIKE "%%" ESCAPE '!'
                OR `zip` LIKE "%%" ESCAPE '!'
                OR `address` LIKE "%%" ESCAPE '!'
                OR `case_type` LIKE "%%" ESCAPE '!'
                OR `tblcase_natures`.`name` LIKE "%%" ESCAPE '!')
ORDER BY `tblprojects`.`id` DESC
 LIMIT 12
