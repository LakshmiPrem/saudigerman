<?php defined('BASEPATH') or exit('No direct script access allowed');
$visi='not_visible';
$visi1='';
if(!isset($client)){
if($case_type=='intellectual_property'){
                       $name= 'intellectual_property';
					$visi='';
						$visi1='not_visible';
						
					}else {
						$visi='not_visible';
							$visi1='';
						
						}
}

$table_data = [

   _l('the_number_sign'),

   _l('project_name'),

   _l('casediary_file_no'),

    [

         'name'     => _l('case_type'),

         'th_attrs' => ['class' => isset($client) ? '' : 'not_visible'],

    ],

   

    [

         'name'     => _l('project_customer'),

         'th_attrs' => ['class' => isset($client) ? '' : ''],

    ],

  	[

						'name'     => _l('ledger_code'),

						'th_attrs' => ['class' => $visi1],

						],
	[

						'name'     => _l('trade_mark_logo'),

						'th_attrs' => ['class' => $visi],

						],

   _l('project_start_date'),

  	[

						'name'     => _l('claiming_amount'),

						'th_attrs' => ['class' => $visi1],

						],
	[

						'name'     => _l('class'),

						'th_attrs' => ['class' => $visi],

						],

   _l('project_members'),

   _l('project_status'),

];



$custom_fields = get_custom_fields('projects', ['show_on_table' => 1]);

foreach ($custom_fields as $field) {

    array_push($table_data, $field['name']);

}



$table_data = hooks()->apply_filters('projects_table_columns', $table_data);



render_datatable($table_data, isset($class) ?  $class : 'projects', [], [

  'data-last-order-identifier' => 'projects',

  'data-default-order'  => get_table_last_order('projects'),

]);

