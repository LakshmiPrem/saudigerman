<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit = has_permission('projects', '', 'edit');
$hasPermissionDelete = has_permission('projects', '', 'delete');
$hasPermissionCreate = has_permission('projects', '', 'create');

$aColumns = array(
    'tblcasetemplates.id as id',
    'tblcasetemplates.name as name',
    //get_sql_select_client_company(),
    'start_date',
    
    //'(SELECT GROUP_CONCAT(CONCAT(firstname, \' \', lastname) SEPARATOR ",") FROM tblcasetemplatemembers JOIN tblstaff on tblstaff.staffid = tblcasetemplatemembers.staff_id WHERE tblcasetemplatemembers.project_id=tblcasetemplates.id ORDER BY staff_id) as members',
    'case_type',
    //'file_no',
    //'case_number',
    //'tblcourts.name as court_name',
    //'tbloppositeparty.name as opposite_party',
    );

$billingTypeVisible = false;
if (has_permission('projects', '', 'create') || has_permission('projects', '', 'edit')) {
    array_push($aColumns, 'billing_type');
    $billingTypeVisible = true;
}

array_push($aColumns, 'status');

$sIndexColumn = "id";
$sTable       = 'tblcasetemplates';

$join             = array(
    //'INNER JOIN tblclients ON tblclients.userid = tblcasetemplates.clientid',
    //'LEFT JOIN tbloppositeparty ON tbloppositeparty.id = tblcasetemplates.opposite_party',
   // 'LEFT JOIN tblcourts ON tblcourts.id = tblcasetemplates.court',

);

$where  = array();
$filter = array();


/*if (!has_permission('casediary', '', 'view') || $this->ci->input->post('my_projects')) {
    array_push($where, ' AND tblcasetemplates.id IN (SELECT tblcasetemplatemembers.project_id FROM tblcasetemplatemembers WHERE staff_id=' . get_staff_user_id() . ')');
}

if($this->ci->input->post('case_type'))
array_push($where, 'AND tblcasetemplates.case_type="' . $this->ci->input->post('case_type').'"');

*/

$statusIds = array();

foreach ($this->ci->projects_model->get_project_statuses() as $status) {
    if ($this->ci->input->post('project_status_' . $status['id'])) {
        array_push($statusIds, $status['id']);
    }
}

if (count($statusIds) > 0) {
    array_push($filter, 'OR status IN (' . implode(', ', $statusIds) . ')');
}

if ($this->ci->input->post('next_cases')) {
    $next_cases = $this->ci->input->post('next_cases');
    if ($next_cases == 'next_3_days') {
    array_push($filter, 'AND tblcasetemplates.start_date BETWEEN CURDATE() AND  DATE_ADD(CURDATE(), INTERVAL +3 DAY)');
    }elseif ($next_cases=='next_7_days') {
    array_push($filter, 'AND tblcasetemplates.start_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL +7 DAY)');
    }
}


if (count($filter) > 0) {
    array_push($where, 'AND (' . prepare_dt_filter($filter) . ')');
}

$custom_fields = get_table_custom_fields('casetemplates');


//$aColumns = do_action('projects_table_sql_columns', $aColumns);

// Fix for big queries. Some hosting have max_join_limit
if (count($custom_fields) > 4) {
    @$this->ci->db->query('SET SQL_BIG_SELECTS=1');
}

$result = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array('deadline',
    //'clientid',
    //'(SELECT GROUP_CONCAT(staff_id SEPARATOR ",") FROM tblcasetemplatemembers WHERE tblcasetemplatemembers.project_id=tblcasetemplates.id ORDER BY staff_id) as members_ids'
));

$output  = $result['output'];
$rResult = $result['rResult'];
$j=1;

foreach ($rResult as $aRow) {
    $row = array();

    $row[] = '<a href="' . admin_url('casetemplates/view/' . $aRow['id']) . '">' .$aRow['id']. '</a>';
    //$row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' . $aRow['company'] . '</a>';

    $row[] = '<a href="' . admin_url('casetemplates/view/' . $aRow['id']) . '">' . $aRow['name'] . '</a>';

    

    //$row[] = $aRow['opposite_party'];

    //$row[] = render_tags($aRow['tags']);
    //$row[] = $aRow['file_no'];

    //$row[] = _d($aRow['start_date']);

    $row[] = _d($aRow['deadline']);
/*
    $membersOutput = '';

    $members        = explode(',', $aRow['members']);
    $exportMembers = '';
    foreach ($members as $key=> $member) {
        if ($member != '') {
            $members_ids = explode(',', $aRow['members_ids']);
            $member_id   = $members_ids[$key];
            $membersOutput .= '<a href="' . admin_url('profile/' . $member_id) . '">' .
            staff_profile_image($member_id, array(
                'staff-profile-image-small mright5'
                ), 'small', array(
                'data-toggle' => 'tooltip',
                'data-title' => $member
                )) . '</a>';
                    // For exporting
            $exportMembers .= $member . ', ';
        }
    }*/

    //$membersOutput .= '<span class="hide">' . trim($exportMembers, ', ') . '</span>';
    //$row[] = $membersOutput;
    //$row[] = $aRow['case_number'];

    /*if ($billingTypeVisible) {
        if ($aRow['billing_type'] == 1) {
            $type_name = 'project_billing_type_fixed_cost';
        } elseif ($aRow['billing_type'] == 2) {
            $type_name = 'project_billing_type_project_hours';
        } else {
            $type_name = 'project_billing_type_project_task_hours';
        }
        $row[] = _l($type_name);
    }*/

    $row[] = _l($aRow['case_type']);

    $status = get_project_status_by_id($aRow['status']);
    $row[] = '<span class="label label inline-block project-status-' . $aRow['status'] . '" style="color:'.$status['color'].';border:1px solid '.$status['color'].'">' . $status['name'] . '</span>';

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    /*$hook = do_action('projects_table_row_data', array(
        'output' => $row,
        'row' => $aRow
    ));

    $row = $hook['output'];*/

    $options = '';

    

    if ($hasPermissionEdit) {
        $options .= icon_btn('casetemplates/casetemplate/' . $aRow['id'], 'pencil-square-o');
    }

    if ($hasPermissionDelete) {
        $options .= icon_btn('casetemplates/delete/' . $aRow['id'], 'remove', 'btn-danger _delete');
    }

    $row[]              = $options;
    $output['aaData'][] = $row;
}
