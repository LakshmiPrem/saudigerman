<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="_filters _hidden_inputs hidden">
    <?php
	 echo form_hidden('my_projects');
    echo form_hidden('exclude_trashed_notices',true);
    echo form_hidden('expired');
    echo form_hidden('without_dateend');
    echo form_hidden('trash');
    echo form_hidden('unsigned');
    foreach($years as $year){
       echo form_hidden('year_'.$year['year'],$year['year']);
   }
   for ($m = 1; $m <= 12; $m++) {
    echo form_hidden('notices_by_month_'.$m);
}
foreach($notice_types as $type){
    echo form_hidden('notices_by_type_'.$type['id']);
}
foreach($notice_statuses as $status){
    echo form_hidden('notices_by_status_'.$status['id']);
}
?>
</div>
<div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-left width300 height500">
        <li class="active filter-group" data-filter-group="trash">
            <a href="#" data-cview="exclude_trashed_notices" onclick="dt_custom_view('exclude_trashed_notices','.table-notices','exclude_trashed_notices'); return false;">
                <?php echo _l('notices_view_exclude_trashed'); ?>
            </a>
        </li>
        <li>
            <a href="#" data-cview="all" onclick="dt_custom_view('','.table-notices',''); return false;">
                <?php echo _l('notices_view_all'); ?>
            </a>
        </li>
        <li class="filter-group" data-filter-group="date">
            <a href="#" data-cview="expired"  onclick="dt_custom_view('expired','.table-notices','expired'); return false;">
                <?php echo _l('notices_view_expired'); ?>
            </a>
        </li>
        <li class="filter-group" data-filter-group="date">
            <a href="#" data-cview="without_dateend"  onclick="dt_custom_view('without_dateend','.table-notices','without_dateend'); return false;">
                <?php echo _l('notices_view_without_dateend'); ?>
            </a>
        </li>
        <li class="filter-group" data-filter-group="trash">
            <a href="#" data-cview="trash"  onclick="dt_custom_view('trash','.table-notices','trash'); return false;">
                <?php echo _l('notices_view_trash'); ?>
            </a>
        </li>
        <li class="filter-group" data-filter-group="unsigned">
            <a href="#" data-cview="unsigned"  onclick="dt_custom_view('unsigned','.table-notices','unsigned'); return false;">
                <?php echo _l('unsigned_notices'); ?>
            </a>
        </li>
        <?php if(count($years) > 0){ ?>
            <li class="divider"></li>
            <?php foreach($years as $year){ ?>
                <li class="active">
                    <a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-notices','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
    <div class="clearfix"></div>
    <li class="divider"></li>
    <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('months'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <?php for ($m = 1; $m <= 12; $m++) { ?>
                <li><a href="#" data-cview="notices_by_month_<?php echo $m; ?>" onclick="dt_custom_view(<?php echo $m; ?>,'.table-notices','notices_by_month_<?php echo $m; ?>'); return false;"><?php echo _l(date('F', mktime(0, 0, 0, $m, 1))); ?></a></li>
            <?php } ?>
        </ul>
    </li>
	<div class="clearfix"></div>
     <?php if(count($notice_statuses) > 0){ ?>
    <li class="divider"></li>
    <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('notice_status'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <?php foreach($notice_statuses as $status){ ?>           
                <li> <a href="#" data-cview="notices_by_status_<?php echo $status['id']; ?>" onclick="dt_custom_view('notices_by_status_<?php echo $status['id']; ?>','.table-notices','notices_by_status_<?php echo $status['id']; ?>'); return false;">
                    <?php echo $status['name']; ?>
                </a></li>
            <?php } ?>
			<?php } ?>
        </ul>
    </li>
    <div class="clearfix"></div>
    <?php if(count($notice_types) > 0){ ?>
        <li class="divider"></li>
		 <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('notice_type'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
        <?php foreach($notice_types as $type){ ?>
            <li>
                <a href="#" data-cview="notices_by_type_<?php echo $type['id']; ?>" onclick="dt_custom_view('notices_by_type_<?php echo $type['id']; ?>','.table-notices','notices_by_type_<?php echo $type['id']; ?>'); return false;">
                    <?php echo $type['name']; ?>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
			 </ul>
    </li>
</ul>
</div>
