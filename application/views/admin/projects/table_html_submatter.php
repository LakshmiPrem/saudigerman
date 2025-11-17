<?php defined('BASEPATH') or exit('No direct script access allowed');

$table_data = [
	 '<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="projects-submatter"><label></label></div>',
	[
         'name'     => _l('the_number_sign'),
         'th_attrs' => ['class' => 'not_visible'],
    ],
   _l('project_name'),
  
	 _l('opposite_party'),
	/* _l('sub_plot_no'),
	 _l('sub_title_no'),
	 _l('sub_plot_acre'),
	 _l('sub_sale_status'),
	  _l('current_task'),
	 _l('completed_task'),*/
      
    [
         'name'     => _l('project_customer'),
         'th_attrs' => ['class' =>'not_visible'],
    ],
 
   _l('project_start_date'),
  [
         'name'     => _l('project_status'),
         'th_attrs' => ['class' =>'not_visible'],
    ],
 //  _l('project_members'),
  
	 [
         'name'     => _l('agreement_amount'),
         'th_attrs' => ['class' =>'not_visible'],
    ],
  
];

$custom_fields = get_custom_fields('projects', ['show_on_table' => 1]);
foreach ($custom_fields as $field) {
    array_push($table_data, $field['name']);
}

$table_data = hooks()->apply_filters('projects_table_columns', $table_data);

render_datatable($table_data, isset($class) ?  $class : 'projects-submatter', [], [
  'data-last-order-identifier' => 'projects',
  'data-default-order'  => get_table_last_order('projects'),
]);
