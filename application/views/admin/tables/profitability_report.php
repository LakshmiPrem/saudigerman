<?php
defined('BASEPATH') or exit('No direct script access allowed');
/*$aColumns     = array(
    'name',
    'start_time',
    'end_time',
    'note',
    tasks_rel_name_select_query(),
    'end_time - start_time',
    'end_time - start_time',
    );*/

$v = $this->ci->db->query('SELECT VERSION() as version')->row();
// 5.6 mysql version don't have the ANY_VALUE function implemented.

  $additionalSelect = array(
    'tbltaskstimers.id',
    'task_id',
    'rel_type',
    'rel_id',
    'tbltasks.status as status'
    );

$staffIdSelect = '';
if ($v && strpos($v->version, '5.7') !== false) {

    $staffIdSelect = 'ANY_VALUE(staff_id) as staff_id';
    foreach ($additionalSelect as $key=>$column) {
        if ($key !== 0) {
            $additionalSelect[$key] = 'ANY_VALUE('.$column.') as '.$column;
        } else {
            // causing errors for ambigious column
            $additionalSelect[$key] = 'ANY_VALUE('.$column.') as id';
        }
    }
    $aColumns = array(
        'ANY_VALUE(name) as name',
        'ANY_VALUE(start_time) as start_time',
        'ANY_VALUE(end_time) as end_time',
        'ANY_VALUE(note) as note',
        'ANY_VALUE('.tasks_rel_name_select_query().') as rel_name',
        'ANY_VALUE(end_time - start_time) as time_h',
        'ANY_VALUE(end_time - start_time) as time_d'
        );

} else {

    $staffIdSelect = 'staff_id';

    $aColumns = array(

        'tbltasks.name as name',
        'start_time as start_time',
        'end_time as end_time',
        'note as note',
        tasks_rel_name_select_query() . ' as rel_name',
        'SUM(end_time - start_time) as time_h',
        'SUM(end_time - start_time) as time_d',
        //'tbltaskstimers.case_id as case_id'
    );

}

$time_h_column = 6;
$time_d_column = 7;

if ($view_all == true) {
    array_unshift($aColumns, $staffIdSelect);

    $time_h_column = 7;
    $time_d_column = 8;

}



$sIndexColumn = "id";
$sTable       = 'tbltaskstimers';

$join         = array(
    'LEFT JOIN tbltasks ON tbltasks.id = tbltaskstimers.task_id',
);

$where = array();

$staff_id = false;

/*if ($this->ci->input->post('staff_id')) {
    $staff_id = $this->ci->input->post('staff_id');
} elseif ($view_all == false) {
    $staff_id = get_staff_user_id();
}

if ($staff_id != false) {
    $where        = array(
        'AND staff_id=' . $staff_id
        );
}*/

if ($this->ci->input->post('project_id')) {
    array_push($where, 'AND task_id IN (SELECT id FROM tbltasks WHERE rel_type = "project" AND rel_id = "'.$this->ci->input->post('project_id').'")');
}

if ($this->ci->input->post('clientid')) {
    array_push($where, 'AND tbltaskstimers.clientid = "'.$this->ci->input->post('clientid').'"');
}



if ($this->ci->input->post('task_id')) {
  //  array_push($where, 'AND task_id = "'.$this->ci->input->post('task_id').'"');
}else{
  // array_push($where,'AND task_id != 0'); 
}

$where2 = $where;
//
//print_r($where);

$for_invoice_query = '';
$filter = $this->ci->input->post('range');
if ($filter != 'period') {
    if ($filter == 'today') {
        $beginOfDay = strtotime("midnight");
        $today   = date('Y-m-d');
        array_push($where, ' AND date = "'.$today. '"');
        $for_invoice_query = ' AND date = "'.$today. '"';
    } elseif ($filter == 'this_month') {
        $beginThisMonth = date('Y-m-01');
        $endThisMonth   = date('Y-m-t');
        array_push($where, ' AND date BETWEEN "' . $beginThisMonth . '" AND  "' .$endThisMonth.'"');
        $for_invoice_query = ' AND date BETWEEN "' . $beginThisMonth . '" AND  "' .$endThisMonth.'"'; 
    } elseif ($filter == 'last_month') {
        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
        $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
        array_push($where, ' AND date  BETWEEN "' . $beginLastMonth . '" AND  "' . $endLastMonth.'"');
        $for_invoice_query = ' AND date  BETWEEN "' . $beginLastMonth . '" AND  "' . $endLastMonth.'"';
    } elseif ($filter == 'this_week') {
        $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
        $endThisWeek   = date('Y-m-d', strtotime('sunday this week'));
        array_push($where, ' AND date BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
        $for_invoice_query = ' AND date BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"';
    } elseif ($filter == 'last_week') {
        $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
        $endLastWeek   = date('Y-m-d', strtotime('sunday last week'));
        array_push($where, ' AND date BETWEEN "' . $beginLastWeek . '" AND "' .$endLastWeek.'"');
        $for_invoice_query = ' AND date BETWEEN "' . $beginLastWeek . '" AND "' .$endLastWeek.'"';
    }
} else {
    $start_date = to_sql_date($this->ci->input->post('period-from'));
    $end_date   = to_sql_date($this->ci->input->post('period-to'));
    array_push($where, ' AND date BETWEEN "' .$start_date . '" AND  "'.$end_date.'"');
    $for_invoice_query = ' AND date BETWEEN "' .$start_date . '" AND  "'.$end_date.'"';
}

/*$filter2 = $this->ci->input->post('range2');
if ($filter2 != 'period2') {
    if ($filter == 'today') {
        $beginOfDay = strtotime("midnight");
        $today   = date('Y-m-d');
        array_push($where, ' AND DATE(date_finished) = "'.$today. '"');
    } elseif ($filter == 'this_month') {
        $beginThisMonth = date('Y-m-01');
        $endThisMonth   = date('Y-m-t');
        array_push($where, ' AND DATE(date_finished) BETWEEN "' . $beginThisMonth . '" AND  "' .$endThisMonth.'"');
    } elseif ($filter == 'last_month') {
        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
        $endLastMonth   = date('Y-m-t', strtotime('-1 MONTH'));
        array_push($where, ' AND DATE(date_finished)  BETWEEN "' . $beginLastMonth . '" AND  "' . $endLastMonth.'"');
    } elseif ($filter == 'this_week') {
        $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
        $endThisWeek   = date('Y-m-d', strtotime('sunday this week'));
        array_push($where, ' AND DATE(date_finished) BETWEEN "' . $beginThisWeek . '" AND "' . $endThisWeek.'"');
    } elseif ($filter == 'last_week') {
        $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
        $endLastWeek   = date('Y-m-d', strtotime('sunday last week'));
        array_push($where, ' AND DATE(date_finished) BETWEEN "' . $beginLastWeek . '" AND "' .$endLastWeek.'"');
    }
} else {
    $start_date = to_sql_date($this->ci->input->post('end-period-from'));
    $end_date   = to_sql_date($this->ci->input->post('end-period-to'));
    array_push($where, ' AND DATE(tblcaseprojects.date_finished) BETWEEN "' .$start_date . '" AND  "'.$end_date.'"');
}*/

$result = data_tables_init(
    $aColumns,
    $sIndexColumn,
    $sTable,
    $join,
    $where,
    $additionalSelect,
    'GROUP BY case_id',
);

$output                 = $result['output'];
$rResult                = $result['rResult'];

$footer_data['total_logged_time_h'] = 0;
$footer_data['total_logged_time_d'] = 0;

$footer_data['chart'] = array();
$footer_data['chart']['labels']   = array();
$footer_data['chart']['data']     = array();

$temp_weekdays_data = array();
$temp_months_data = array();
$chart_type = 'today';
$chart_type_month_from_filter = false;
$weekDay = date('w', strtotime(date('Y-m-d H:i:s')));
if ($filter == 'today') {
    $footer_data['chart']['labels'] = array(_l('today'));
} elseif ($filter == 'this_week' || $filter == 'last_week') {
    foreach (get_weekdays() as $day) {
        array_push($footer_data['chart']['labels'], $day);
    }
    $i = 0;
    foreach (get_weekdays_original() as $day) {
        if ($weekDay != "0") {
            $footer_data['chart']['labels'][$i] = date('d', strtotime($day. ' ' . str_replace('_', ' ', $filter))). ' - ' .$footer_data['chart']['labels'][$i];
        } else {
            if ($filter == 'this_week') {
                $strtotime = 'last '.$day;
                if ($day == 'Sunday') {
                    $strtotime = 'sunday this week';
                }
                $footer_data['chart']['labels'][$i] = date('d', strtotime($strtotime)). ' - ' .$footer_data['chart']['labels'][$i];
            } else {
                $strtotime = $day .' last week';
                $footer_data['chart']['labels'][$i] = date('d', strtotime($strtotime)). ' - ' .$footer_data['chart']['labels'][$i];
            }
        }
        $i++;
    }

    $chart_type = 'week';
} elseif ($filter == 'this_month' || $filter == 'last_month') {
    $month = ($filter == 'this_month') ? date('m') : date('m', strtotime('first day last month'));
    $month_year = ($filter == 'this_month') ? date('Y') : date('Y', strtotime('first day last month'));

    for ($d = 1; $d <= 31; $d++) {
        $time = mktime(12, 0, 0, $month, $d, $month_year);
        if (date('m', $time) == $month) {
            array_push($footer_data['chart']['labels'], date('Y-m-d', $time));
        }
    }
    $chart_type = 'month';
} else {
    $_start_time = new DateTime(date('Y-m-d', strtotime($start_date)));
    $_end_time = new DateTime(date('Y-m-d', strtotime($end_date)));

    $chart_type = 'weeks_split';
    $weeks = get_weekdays_between_dates($_start_time, $_end_time);
    $total_weeks = count($weeks);
    for ($i = 1; $i<=$total_weeks; $i++) {
        array_push($footer_data['chart']['labels'], split_weeks_chart_label($weeks, $i));
    }
}
//chart where;
$filter = $this->ci->input->post('range');
if ($filter != 'period') {
    if ($filter == 'today') {
        $beginOfDay = strtotime("midnight");
        $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;
        array_push($where2, ' AND start_time BETWEEN ' . $beginOfDay . ' AND ' . $endOfDay);
    } elseif ($filter == 'this_month') {
        $beginThisMonth = date('Y-m-01');
        $endThisMonth   = date('Y-m-t 23:59:59');
        array_push($where2, ' AND start_time BETWEEN ' . strtotime($beginThisMonth) . ' AND ' . strtotime($endThisMonth));
    } elseif ($filter == 'last_month') {
        $beginLastMonth = date('Y-m-01', strtotime('-1 MONTH'));
        $endLastMonth   = date('Y-m-t 23:59:59', strtotime('-1 MONTH'));
        array_push($where2, ' AND start_time BETWEEN ' . strtotime($beginLastMonth) . ' AND ' . strtotime($endLastMonth));
    } elseif ($filter == 'this_week') {
        $beginThisWeek = date('Y-m-d', strtotime('monday this week'));
        $endThisWeek   = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        array_push($where2, ' AND start_time BETWEEN ' . strtotime($beginThisWeek) . ' AND ' . strtotime($endThisWeek));
    } elseif ($filter == 'last_week') {
        $beginLastWeek = date('Y-m-d', strtotime('monday last week'));
        $endLastWeek   = date('Y-m-d 23:59:59', strtotime('sunday last week'));
        array_push($where2, ' AND start_time BETWEEN ' . strtotime($beginLastWeek) . ' AND ' . strtotime($endLastWeek));
    }
} else {
    $start_date = to_sql_date($this->ci->input->post('period-from'));
    $end_date   = to_sql_date($this->ci->input->post('period-to'));
    array_push($where2, ' AND start_time BETWEEN ' . strtotime($start_date. ' 00:00:00') . ' AND ' . strtotime($end_date.' 23:59:00'));
}

$chartWhere = implode(' ', $where2);
$chartWhere = ltrim($chartWhere, 'AND ');

$chartData = $this->ci->db->query('SELECT end_time - start_time logged_time_h,
    end_time - start_time logged_time_d,start_time,end_time,timesheet_billable FROM tbltaskstimers WHERE '.trim($chartWhere))->result_array();

foreach ($chartData as $timer) {

    if ($timer['logged_time_h'] == null) {
        $footer_data['total_logged_time_h'] += (time() - $timer['start_time']);
    } else {
        $footer_data['total_logged_time_h'] += $timer['logged_time_h'];
    }

    if ($timer['logged_time_d'] == null) {
        $total_logged_time_d = time() - $timer['start_time'];
    } else {
        $total_logged_time_d = $timer['logged_time_d'];
    }

    // Billable Total Time
    if($timer['timesheet_billable'] == 1){
        if ($timer['logged_time_h'] == null) {
        $footer_data['total_billable_time_h'] += (time() - $timer['start_time']);
        } else {
            $footer_data['total_billable_time_h'] += $timer['logged_time_h'];
        }
    }else{
        if ($timer['logged_time_h'] == null) {
        $footer_data['total_non_billable_time_h'] += (time() - $timer['start_time']);
        } else {
            $footer_data['total_non_billable_time_h'] += $timer['logged_time_h'];
        }
    }
    if ($chart_type == 'today') {
        array_push($footer_data['chart']['data'], $total_logged_time_d);
    } elseif ($chart_type == 'week') {
        $weekday = date('N', $timer['start_time']);
        if (!isset($temp_weekdays_data[$weekday])) {
            $temp_weekdays_data[$weekday] = 0;
        }
        $temp_weekdays_data[$weekday] += $total_logged_time_d;
    } elseif ($chart_type == 'month') {
        $month = intval(strftime('%d', $timer['start_time']));

        if (!isset($temp_months_data[$month])) {
            $temp_months_data[$month] = 0;
        }

        $temp_months_data[$month] += $total_logged_time_d;
    } elseif ($chart_type == 'weeks_split') {
        $w = 1;
        foreach ($weeks as $week) {
            $start_time_date = strftime('%Y-%m-%d', $timer['start_time']);
            if (!isset($weeks[$w]['total'])) {
                $weeks[$w]['total'] = 0;
            }
            if (in_array($start_time_date, $week)) {
                $weeks[$w]['total'] += $total_logged_time_d;
            }
            $w++;
        }
    }
    $footer_data['total_logged_time_d'] += $total_logged_time_d;
}


foreach ($rResult as $aRow) {
    $row = array();

    if($view_all === true){
        $row[] = '<a href="'.admin_url('staff/member/'.$aRow['staff_id']).'" target="_blank">'.get_staff_full_name($aRow['staff_id']).'</a>';
    }
    
    $row[] = '<a href="' . admin_url('clients/client/' . $aRow['clientid']) . '">' .get_company_name($aRow['clientid']). '</a>';

    if($aRow['rel_name']){

         $relName = task_rel_name($aRow['rel_name'], $aRow['rel_id'], $aRow['rel_type']);
         $link = task_rel_link($aRow['rel_id'], $aRow['rel_type']);
         $row[] = '<a href="' . $link . '">' . $relName . '</a>';

    } else {
        $row[] = '';
    }
    $row[] = '';//_l($aRow['case_type']);
    /*if($aRow['billing_type'] == 1){
        $type_name = 'project_billing_type_fixed_cost';
    } else if($aRow['billing_type'] == 2){
       $type_name = 'project_billing_type_project_hours';
    } else if($aRow['billing_type'] == 3){
       $type_name = 'project_billing_type_project_task_hours';
    }else if($aRow['billing_type'] == 4) {
       $type_name = 'retainer';
    }else if($aRow['billing_type'] == 5) {
       $type_name = 'probono';
    }else if($aRow['billing_type'] == 6) {
       $type_name = 'success_fee';
    }else if($aRow['billing_type'] == 7) {
       $type_name = 'no_fee_arrangement_yet';
    }*/
        
    $row[] = '-';//_l($type_name);

    $taskName = '<a href="'.admin_url('tasks/view/'.$aRow['task_id']).'" onclick="init_task_modal(' . $aRow['task_id'] . '); return false;">' . $aRow['name'] . '</a>';

    $status = seconds_to_time_format($aRow['time_h']);

    //$taskName .= '<span class="hidden"> - </span><span class="inline-block pull-right mright5 label" style="border:1px solid '.$status['color'].';color:'.$status['color'].'" task-status-table="' . $aRow['status'] . '">' . $status['name'] . '</span>';

    $row[] = $aRow['client_total_hours'];
    $row[] = seconds_to_time_format($aRow['time_h']);
 
    $row[] = '-';//format_money($aRow['project_cost']);
    $fees_billed = $this->ci->db->query('SELECT SUM(subtotal) as fees_billed FROM  tblinvoices  WHERE tblinvoices.project_id = '.$aRow['rel_id'] .' '.$for_invoice_query)->row()->fees_billed;
    $row[] = format_money($fees_billed);//,'AED');

    $for_payment_query = str_replace('date','tblinvoicepaymentrecords.date', $for_invoice_query);
    $fees_collected = $this->ci->db->query('SELECT SUM(amount) as fees_collected FROM  tblinvoicepaymentrecords JOIN tblinvoices ON tblinvoices.id = tblinvoicepaymentrecords.invoiceid WHERE tblinvoices.project_id = '.$aRow['rel_id'].' '.$for_payment_query)->row()->fees_collected;

    $row[] = format_money($fees_collected);


    $total_costs_rates = [1,1];//explode('-', get_staff_wise_cost_rate($aRow['rel_id']));

    $row[] = format_money($total_costs_rates[0]);
    $row[] = format_money($total_costs_rates[1]);

    $total_logged_time = 0;
    if ($aRow['time_h'] == null) {
        $total_logged_time = time() - $aRow['start_time'];
    } else {
        $total_logged_time = $aRow['time_h'];
    }
    /*if($aRow['timesheet_billable'] == 1){
        $row[] = seconds_to_time_format($total_logged_time);
        $row[] = '-';
    
    }else{
        $row[] = '-';
        $row[] = seconds_to_time_format($total_logged_time);
    }*/
    
    $total_logged_time = 0;
    if ($aRow['time_d'] == null) {
        $total_logged_time = time() - $aRow['start_time'];
    } else {
        $total_logged_time = $aRow['time_d'];
    }
  
    $row[] = format_money($fees_billed - $total_costs_rates[0]);
    
    $row[] = format_money($fees_billed - $total_costs_rates[1]);
    $invoice_info = '';
    $lawyers = explode(',',$total_costs_rates[2]);
    foreach ($lawyers as $value) {
        $invoice_info .= '-'; //get_staff_initial($value).' / ';
    }
    $row[] = rtrim($invoice_info, ' /');

    $output['aaData'][]    = $row;
}

if ($chart_type == 'today') {
    $footer_data['chart']['data'] = array(sec2qty(array_sum($footer_data['chart']['data'])));
} elseif ($chart_type == 'week') {
    ksort($temp_weekdays_data);
    for ($i = 1; $i<=7; $i++) {
        $total_logged_time = 0;
        if (isset($temp_weekdays_data[$i])) {
            $total_logged_time = $temp_weekdays_data[$i];
        }
        array_push($footer_data['chart']['data'], sec2qty($total_logged_time));
    }
} elseif ($chart_type == 'month') {
    ksort($temp_months_data);

    for ($i = 1; $i<=31; $i++) {
        $total_logged_time = 0;
        if (isset($temp_months_data[$i])) {
            $total_logged_time = $temp_months_data[$i];
        }
        array_push($footer_data['chart']['data'], sec2qty($total_logged_time));
    }
} elseif ($chart_type == 'weeks_split') {
    foreach ($weeks as $week) {
        $total = 0;
        if (isset($week['total'])) {
            $total = $week['total'];
        }
        $total_logged_time = $total;
        array_push($footer_data['chart']['data'], sec2qty($total_logged_time));
    }
}

$output['chart'] = $footer_data['chart'];
$output['chart_type'] = $chart_type;
unset($footer_data['chart']);
$footer_data['total_billable_time_h'] = seconds_to_time_format($footer_data['total_billable_time_h']);
$footer_data['total_non_billable_time_h'] = seconds_to_time_format($footer_data['total_non_billable_time_h']);
$footer_data['total_billable_cost'] = format_money($footer_data['total_billable_cost']);
$footer_data['total_hourly_rate'] = format_money($footer_data['total_hourly_rate']);

$footer_data['total_logged_time_h'] = seconds_to_time_format($footer_data['total_logged_time_h']);
$footer_data['total_logged_time_d'] = sec2qty($footer_data['total_logged_time_d']);
$output['logged_time'] = $footer_data;
