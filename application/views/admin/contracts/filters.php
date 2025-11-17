<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="_filters _hidden_inputs hidden">
    <?php
     $value = ($this->input->get('type') ? $this->input->get('type')  : '' ); 
	 echo form_hidden('my_projects');
  if( $value!='trash'){
  echo form_hidden('exclude_trashed_contracts',true);
    }else{
    echo form_hidden('exclude_trashed_contracts');
    }
    echo form_hidden('expired');
    echo form_hidden('without_dateend');
    if( $value=='trash'){
 echo form_hidden('trash',true);
    }else{
    echo form_hidden('trash');
    }
    if( $value=='receivable'){
 echo form_hidden('receivable',true);
    }else{
    echo form_hidden('receivable');
    }
      if( $value=='payable'){
 echo form_hidden('payable',true);
    }else{
    echo form_hidden('payable');
    }
      if( $value=='unsigned'){
 echo form_hidden('unsigned',true);
    }else{
    echo form_hidden('unsigned');
    }
    foreach($years as $year){
       echo form_hidden('year_'.$year['year'],$year['year']);
   }
   for ($m = 1; $m <= 12; $m++) {
    echo form_hidden('contracts_by_month_'.$m);
}
foreach($contract_types as $type){
    echo form_hidden('contracts_by_type_'.$type['id']);
}
foreach($contract_statuses as $status){
    echo form_hidden('contracts_by_status_'.$status['id']);
}
 

?>
</div>
<div class="btn-group pull-right btn-with-tooltip-group _filter_data" data-toggle="tooltip" data-title="<?php echo _l('filter_by'); ?>">
    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fa fa-filter" aria-hidden="true"></i>
    </button>
    <ul class="dropdown-menu dropdown-menu-left width300 height500">
           <li class="<?php if($value!='trash'){echo 'active';} ?> filter-group" data-filter-group="trash">
            <a href="#" data-cview="exclude_trashed_contracts" onclick="dt_custom_view('exclude_trashed_contracts','.table-contracts','exclude_trashed_contracts'); return false;">
                <?php echo _l('contracts_view_exclude_trashed'); ?>
            </a>
        </li>
        <li>
            <a href="#" data-cview="all" onclick="dt_custom_view('','.table-contracts',''); return false;">
                <?php echo _l('contracts_view_all'); ?>
            </a>
        </li>
        <li class="filter-group hide" data-filter-group="date">
            <a href="#" data-cview="expired"  onclick="dt_custom_view('expired','.table-contracts','expired'); return false;">
                <?php echo _l('contracts_view_expired');  ?>
            </a>
        </li>
        <li class="filter-group" data-filter-group="date">
            <a href="#" data-cview="without_dateend"  onclick="dt_custom_view('without_dateend','.table-contracts','without_dateend'); return false;">
                <?php echo _l('contracts_view_without_dateend'); ?>
            </a>
        </li>
        <li class="<?php if($value=='trash'){echo 'active';} ?> filter-group" data-filter-group="trash">
            <a href="#" data-cview="trash"  onclick="dt_custom_view('trash','.table-contracts','trash'); return false;">
                <?php echo _l('contracts_view_trash'); ?>
            </a>
        </li>
         <li class=" <?php if($value=='receivable'){echo 'active';} ?> filter-group" data-filter-group="receivable">
            <a href="#" data-cview="receivable"  onclick="dt_custom_view('receivable','.table-contracts','receivable'); return false;">
                <?php echo _l('contracts_view_receivable'); ?>
            </a>
        </li>
         <li class="<?php if($value=='payable'){echo 'active';} ?> filter-group" data-filter-group="payable">
            <a href="#" data-cview="payable"  onclick="dt_custom_view('payable','.table-contracts','payable'); return false;">
                <?php echo _l('contracts_view_payable'); ?>
            </a>
        </li>
        <li class="<?php if($value=='unsigned'){echo 'active';} ?> filter-group" data-filter-group="unsigned">
            <a href="#" data-cview="unsigned"  onclick="dt_custom_view('unsigned','.table-contracts','unsigned'); return false;">
                <?php echo _l('unsigned_contracts'); ?>
            </a>
        </li>
        <?php if(count($years) > 0){ ?>
            <li class="divider"></li>
                <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('years'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <?php foreach($years as $year){ ?>
              <li class="active">
                    <a href="#" data-cview="year_<?php echo $year['year']; ?>" onclick="dt_custom_view(<?php echo $year['year']; ?>,'.table-contracts','year_<?php echo $year['year']; ?>'); return false;"><?php echo $year['year']; ?>
                </a>
            </li>
            <?php } ?>
        </ul>
    </li>
   <?php } ?>
    <div class="clearfix"></div>
    <li class="divider"></li>
    <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('months'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <?php for ($m = 1; $m <= 12; $m++) { ?>
                <li><a href="#" data-cview="contracts_by_month_<?php echo $m; ?>" onclick="dt_custom_view(<?php echo $m; ?>,'.table-contracts','contracts_by_month_<?php echo $m; ?>'); return false;"><?php echo _l(date('F', mktime(0, 0, 0, $m, 1))); ?></a></li>
            <?php } ?>
        </ul>
    </li>

    <div class="clearfix"></div>
     <?php if(count($contract_statuses) > 0){ ?>
    <li class="divider"></li>
    <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('contract_status'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
            <?php foreach($contract_statuses as $status){ ?>           
                <li> <a href="#" data-cview="contracts_by_status_<?php echo $status['id']; ?>" onclick="dt_custom_view('contracts_by_status_<?php echo $status['id']; ?>','.table-contracts','contracts_by_status_<?php echo $status['id']; ?>'); return false;">
                    <?php echo $status['name']; ?>
                </a></li>
            <?php } ?>
            <?php } ?>
        </ul>
    </li>
    <div class="clearfix"></div>
    <?php if(count($contract_types) > 0){ ?>
       <li class="divider"></li>
         <li class="dropdown-submenu pull-left">
        <a href="#" tabindex="-1"><?php echo _l('contract_type'); ?></a>
        <ul class="dropdown-menu dropdown-menu-left">
        <?php foreach($contract_types as $type){ ?>
            <li>
                <a href="#" data-cview="contracts_by_type_<?php echo $type['id']; ?>" onclick="dt_custom_view('contracts_by_type_<?php echo $type['id']; ?>','.table-contracts','contracts_by_type_<?php echo $type['id']; ?>'); return false;">
                    <?php echo $type['name']; ?>
                </a>
            </li>
        <?php } ?>
    <?php } ?>
             </ul>
    </li>
   <div class="clearfix"></div> 
    <li class="divider"></li> 
</ul>
</div>
