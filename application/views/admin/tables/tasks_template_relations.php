<?php
defined('BASEPATH') or exit('No direct script access allowed');

$hasPermissionEdit = has_permission('tasks', '', 'edit');
$bulkActions = $this->ci->input->get('bulk_actions');

$aColumns = array(
    'name',
    'startdate',
    'duedate',
    'description',
    'status',
);

if ($bulkActions) {
    array_unshift($aColumns, '1');
}

$sIndexColumn = "id";
$sTable       = 'tblstafftasks_templates';

$where = array();
//include_once(APPPATH.'views/admin/tables/includes/tasks_filter.php');

if (!$this->ci->input->post('tasks_related_to')) { 
    array_push($where, 'AND rel_id="' . $rel_id . '" AND rel_type="' . $rel_type . '"');
}

$join          = array();


$result  = data_tables_init($aColumns, $sIndexColumn, $sTable, $join, $where, array(
    'tblstafftasks_templates.id',
    'billed'
));

$output  = $result['output'];
$rResult = $result['rResult'];

foreach ($rResult as $aRow) {

    $row = array();

    if ($this->ci->input->get('bulk_actions')) {  
        $row[] = '<div class="checkbox"><input type="checkbox" value="'.$aRow['id'].'"><label></label></div>';
    }

    $outputName = '<a href="#" onclick="edit_task_temp('. $aRow['id'].'); return false" class="display-block main-tasks-table-href-name" onclick="init_template_task_modal(' . $aRow['id'] . '); return false;">' . $aRow['name'] . '</a>';

    $row[] = $outputName;

    $row[] = _d($aRow['startdate']);

    $row[] = _d($aRow['duedate']);

    $row[] = '-';

    //$row[] = format_members_by_ids_and_names($aRow['assignees_ids'],$aRow['assignees']);

    $row[] = nl2br($aRow['description']);

    $status = get_task_status_by_id($aRow['status']);
    $outputStatus = '<span class="inline-block label" style="color:'.$status['color'].';border:1px solid '.$status['color'].'" task-status-table="'.$aRow['status'].'">' . $status['name'];

    if ($aRow['status'] == 5) {
        //$outputStatus .= '<a href="#" onclick="unmark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-finished-icon" data-toggle="tooltip" title="' . _l('task_unmark_as_complete') . '"></i></a>';
    } else {
        //$outputStatus .= '<a href="#" onclick="mark_complete(' . $aRow['id'] . '); return false;"><i class="fa fa-check task-icon task-unfinished-icon" data-toggle="tooltip" title="' . _l('task_single_mark_as_complete') . '"></i></a>';
    }

    $outputStatus .= '</span>';
    $row[] = $outputStatus;

    // Custom fields add values
    foreach ($customFieldsColumns as $customFieldColumn) {
        $row[] = (strpos($customFieldColumn, 'date_picker_') !== false ? _d($aRow[$customFieldColumn]) : $aRow[$customFieldColumn]);
    }

    /*$hook = do_action('tasks_related_table_row_data', array(
        'output' => $row,
        'row' => $aRow,
    ));

    $row = $hook['output'];*/

    $options = '';
    if ($hasPermissionEdit) {
        $options .= icon_btn('#', 'pencil-square-o', 'btn-default  mleft5', array(
            'onclick' => 'edit_task_temp(' . $aRow['id'] . '); return false',
        ));
    }

    $class = 'btn-success';

    $tooltip        = '';
    if ($aRow['billed'] == 1 ||  $aRow['status'] == 5) {
        $class = 'btn-default disabled';
        if ($aRow['status'] == 5) {
            $tooltip = ' data-toggle="tooltip" data-title="' . format_task_status($aRow['status'], false, true) . '"';
        } elseif ($aRow['billed'] == 1) {
            $tooltip = ' data-toggle="tooltip" data-title="' . _l('task_billed_cant_start_timer') . '"';
        }
    }

   

    $row[]              = $options;

    $row['DT_RowClass'] = '';
    if ((!empty($aRow['duedate']) && $aRow['duedate'] < date('Y-m-d')) && $aRow['status'] != 5) {
        $row['DT_RowClass'] = 'text-danger bold ';
    }

    $output['aaData'][] = $row;
}
