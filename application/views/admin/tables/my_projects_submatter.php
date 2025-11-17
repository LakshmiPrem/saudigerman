<?php

defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit   = has_permission('projects', '', 'edit');
$hasPermissionDelete = has_permission('projects', '', 'delete');
$hasPermissionCreate = has_permission('projects', '', 'create');

$aColumns = [
	'1',
    db_prefix() . 'projects.id as id',
    'name',
   
	'opposite_party',
	 'start_date',
	
	get_sql_select_client_company(),
   
	
  //  '(SELECT GROUP_CONCAT(name SEPARATOR ",") FROM ' . db_prefix() . 'taggables JOIN ' . db_prefix() . 'tags ON ' . db_prefix() . 'taggables.tag_id = ' . db_prefix() . 'tags.id WHERE rel_id = ' . db_prefix() . 'projects.id and rel_type="project" ORDER by tag_order ASC) as tags',
  
  // 'deadline',
	'claiming_amount',
   // '(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM ' . db_prefix() . 'project_members JOIN ' . db_prefix() . 'staff on ' . db_prefix() . 'staff.staffid = ' . db_prefix() . 'project_members.staff_id WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members',
    'status',
	
    ];


$sIndexColumn = 'id';
$sTable       = db_prefix() . 'projects';

$join = [
    'JOIN ' . db_prefix() . 'clients ON ' . db_prefix() . 'clients.userid = ' . db_prefix() . 'projects.clientid',
];

$where  = [];
$filter = [];
$case1='companies';
if ($clientid != '') {
    array_push($where, ' AND clientid=' . $this->ci->db->escape_str($clientid));
//	array_push($where, 'AND case_type!="' . $case1.'"');
//	array_push($where, 'AND case_type NOT IN("companies","submatter")');
}

if ($opposite_party != '') {
    array_push($where, ' AND opposite_party=' . $this->ci->db->escape_str($opposite_party));
}
if ($related_matter != '' && $related_matter != 0) {
    array_push($where, ' AND related_matter=' . $this->ci->db->escape_str($related_matter));
	
}
if($this->ci->input->post('sub_sale_status') && $this->ci->input->post('sub_sale_status') != null ){
    array_push($where, ' AND tblprojects.sub_sale_status ="' . $this->ci->input->post('sub_sale_status').'"');
}
if($this->ci->input->post('template_id') && $this->ci->input->post('template_id') != null ){
     array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT rel_id FROM ' . db_prefix() . 'tasks WHERE status=4 AND task_templateid=' .$this->ci->input->post('template_id') . ')');
}
if (!has_permission('projects', '', 'view') || $this->ci->input->post('my_projects')) {
    array_push($where, ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
}

$statusIds = [];

foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR status IN (' . implode(', ', $statusIds) . ')');
}

if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

if($this->ci->input->post('case_type'))
array_push($where, 'AND case_type="' . $this->ci->input->post('case_type').'"');

$custom_fields = get_table_custom_fields('projects');

foreach ($custom_fields as $key => $field) {
    $selectAs = (is_cf_date($field) ? 'date_picker_cvalue_' . $key : 'cvalue_' . $key);
    array_push($customFieldsColumns, $selectAs);
    array_push($aColumns, 'ctable_' . $key . '.value as ' . $selectAs);
    array_push($join, 'LEFT JOIN ' . db_prefix() . 'customfieldsvalues as ctable_' . $key . ' ON ' . db_prefix() . 'projects.id = ctable_' . $key . '.relid AND ctable_' . $key . '.fieldto="' . $field['fieldto'] . '" AND ctable_' . $key . '.fieldid=' . $field['id']);
}

$aColumns = hooks()->apply_filters('projects_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, [
    'clientid', 'case_type',
    '(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM ' . db_prefix() . 'project_members WHERE project_id=' . db_prefix() . 'projects.id ORDER BY staff_id) as members_ids',
]);

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {
    $row = [];

    $link = admin_url('projects/view/' . $aRow['id']);
 	$row[] = '<div class="checkbox"><input type="checkbox" value="' . $aRow['id'] . '"><label></label></div>';
    $row[] = '<a href="' . $link . '">' . $aRow['id'] . '</a>';

    $name = '<a target="_blank" href="' . $link . '">' . $aRow['name'] . '</a>';

    $name .= '<div class="row-options">';

    $name .= '<a target="_blank" href="' . $link . '">' . _l('view') . '</a>';

   
    if ($hasPermissionEdit) {
        $name .= ' | <a href="' . admin_url('projects/project/' . $aRow['id']) . '">' . _l('edit') . '</a>';
    }

    if ($hasPermissionDelete) {
        $name .= ' | <a href="' . admin_url('projects/delete/' . $aRow['id']) . '" class="text-danger _delete">' . _l('delete') . '</a>';
    }

    $name .= '</div>';

    $row[] = $name;

   /* $row[] = $aRow['sub_plot_no'];
	$row[] = $aRow['sub_title_no'];
	$row[] = $aRow['sub_plot_acre'];*/
	$row[] =get_opposite_party_name($aRow['opposite_party']);
  //  $row[] =ucwords(str_replace('_',' ',$aRow['sub_sale_status']));
    //$row[] =get_task_progress_update($aRow['id'],true);
//	 $row[] =get_task_latest_update($aRow['id'],true);
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

	
 //   $row[] = render_tags($aRow['tags']);

    $row[] = _d($aRow['start_date']);

  //  $row[] = $aRow['claiming_amount'];//_d($aRow['deadline']);

/*    $membersOutput = '';

    $members       = explode(',', $aRow['members']);
    $exportMembers = '';
    foreach ($members as $key => $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['members_ids']);
            $member_id   = $members_ids[$key];
            $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, [
                'staff-profile-image-small mright5',
                ], 'small', [
                'data-toggle' => 'tooltip',
                'data-title'  => $member,
                ]) . '</a>';
            // For exporting
            $exportMembers .= $member . ', ';
        }
    }

    $membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    $row[] = $membersOutput;*/

    $status = get_project_status_by_id($aRow['status']);
    $row[]  = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:' . $status['color'] . ';border:1px solid ' . $status['color'] . '">' . $status['name'] . '</span>';
	$row[] = $aRow['claiming_amount'];
    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    $row['DT_RowClass'] = 'has-row-options';

    $row = hooks()->apply_filters('projects_table_row_data', $row, $aRow);

    $output['aaData'][] = $row;
}
