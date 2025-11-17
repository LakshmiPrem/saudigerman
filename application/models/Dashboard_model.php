<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Dashboard_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @return array
     * Used in home dashboard page
     * Return all upcoming events this week
     */
    public function get_upcoming_events()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday this week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday this week'));

        $this->db->where("(start BETWEEN '$monday_this_week' and '$sunday_this_week')");
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');
        $this->db->order_by('start', 'desc');
        $this->db->limit(6);

        return $this->db->get(db_prefix() . 'events')->result_array();
    }

    /**
     * @param  integer (optional) Limit upcoming events
     * @return integer
     * Used in home dashboard page
     * Return total upcoming events next week
     */
    public function get_upcoming_events_next_week()
    {
        $monday_this_week = date('Y-m-d', strtotime('monday next week'));
        $sunday_this_week = date('Y-m-d', strtotime('sunday next week'));
        $this->db->where("(start BETWEEN '$monday_this_week' and '$sunday_this_week')");
        $this->db->where('(userid = ' . get_staff_user_id() . ' OR public = 1)');

        return $this->db->count_all_results(db_prefix() . 'events');
    }

    /**
     * @param  mixed
     * @return array
     * Used in home dashboard page, currency passed from javascript (undefined or integer)
     * Displays weekly payment statistics (chart)
     */
    public function get_weekly_payments_statistics($currency)
    {
        $all_payments                 = [];
        $has_permission_payments_view = has_permission('payments', '', 'view');
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE)');
        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Current week
        $all_payments[] = $this->db->get()->result_array();
        $this->db->select(db_prefix() . 'invoicepaymentrecords.id, amount,' . db_prefix() . 'invoicepaymentrecords.date');
        $this->db->from(db_prefix() . 'invoicepaymentrecords');
        $this->db->join(db_prefix() . 'invoices', '' . db_prefix() . 'invoices.id = ' . db_prefix() . 'invoicepaymentrecords.invoiceid');
        $this->db->where('YEARWEEK(' . db_prefix() . 'invoicepaymentrecords.date) = YEARWEEK(CURRENT_DATE - INTERVAL 7 DAY) ');

        $this->db->where('' . db_prefix() . 'invoices.status !=', 5);
        if ($currency != 'undefined') {
            $this->db->where('currency', $currency);
        }

        if (!$has_permission_payments_view) {
            $this->db->where('invoiceid IN (SELECT id FROM ' . db_prefix() . 'invoices WHERE addedfrom=' . get_staff_user_id() . ' and addedfrom IN (SELECT staff_id FROM ' . db_prefix() . 'staff_permissions WHERE feature="invoices" AND capability="view_own"))');
        }

        // Last Week
        $all_payments[] = $this->db->get()->result_array();

        $chart = [
            'labels'   => get_weekdays(),
            'datasets' => [
                [
                    'label'           => _l('this_week_payments'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
                [
                    'label'           => _l('last_week_payments'),
                    'backgroundColor' => 'rgba(197, 61, 169, 0.5)',
                    'borderColor'     => '#c53da9',
                    'borderWidth'     => 1,
                    'tension'         => false,
                    'data'            => [
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                        0,
                    ],
                ],
            ],
        ];


        for ($i = 0; $i < count($all_payments); $i++) {
            foreach ($all_payments[$i] as $payment) {
                $payment_day = date('l', strtotime($payment['date']));
                $x           = 0;
                foreach (get_weekdays_original() as $day) {
                    if ($payment_day == $day) {
                        $chart['datasets'][$i]['data'][$x] += $payment['amount'];
                    }
                    $x++;
                }
            }
        }

        return $chart;
    }

    public function projects_status_stats()
    {
        $this->load->model('projects_model');
        $statuses = $this->projects_model->get_project_statuses();
        $colors   = get_system_favourite_colors();

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $has_permission = has_permission('projects', '', 'view');
        $sql            = '';
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'projects';
            $sql .= ' WHERE status=' . $status['id'];
            if (!$has_permission) {
                $sql .= ' AND id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }

        foreach ($statuses as $key => $status) {
            array_push($_data['statusLink'], admin_url('projects?status=' . $status['id']));
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $result[$key]->total);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }

    public function leads_status_stats()
    {
        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        $result = get_leads_summary();

        foreach ($result as $status) {
            if ($status['color'] == '') {
                $status['color'] = '#737373';
            }
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['color']);
            if (!isset($status['junk']) && !isset($status['lost'])) {
                array_push($_data['statusLink'], admin_url('leads?status=' . $status['id']));
            }
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['color'], -20));
            array_push($_data['data'], $status['total']);
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by department (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_department()
    {
        $this->load->model('departments_model');
        $departments = $this->departments_model->get();
        $colors      = get_system_favourite_colors();
        $chart       = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];

        $i = 0;
        foreach ($departments as $department) {
            if (!is_admin()) {
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    $departments_ids      = [];
                    if (count($staff_deparments_ids) == 0) {
                        $departments = $this->departments_model->get();
                        foreach ($departments as $department) {
                            array_push($departments_ids, $department['departmentid']);
                        }
                    } else {
                        $departments_ids = $staff_deparments_ids;
                    }
                    if (count($departments_ids) > 0) {
                        $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                    }
                }
            }
            $this->db->where_in('status', [
                1,
                2,
                4,
            ]);

            $this->db->where('department', $department['departmentid']);
            $total = $this->db->count_all_results(db_prefix() . 'tickets');

            if ($total > 0) {
                $color = '#333';
                if (isset($colors[$i])) {
                    $color = $colors[$i];
                }
                array_push($chart['labels'], $department['name']);
                array_push($_data['backgroundColor'], $color);
                array_push($_data['hoverBackgroundColor'], adjust_color_brightness($color, -20));
                array_push($_data['data'], $total);
            }
            $i++;
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }

    /**
     * Display total tickets awaiting reply by status (chart)
     * @return array
     */
    public function tickets_awaiting_reply_by_status()
    {
        $this->load->model('tickets_model');
        $statuses             = $this->tickets_model->get_ticket_status();
        $_statuses_with_reply = [
            1,
            2,
            4,
        ];

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];

        foreach ($statuses as $status) {
            if (in_array($status['ticketstatusid'], $_statuses_with_reply)) {
                if (!is_admin()) {
                    if (get_option('staff_access_only_assigned_departments') == 1) {
                        $staff_deparments_ids = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                        $departments_ids      = [];
                        if (count($staff_deparments_ids) == 0) {
                            $departments = $this->departments_model->get();
                            foreach ($departments as $department) {
                                array_push($departments_ids, $department['departmentid']);
                            }
                        } else {
                            $departments_ids = $staff_deparments_ids;
                        }
                        if (count($departments_ids) > 0) {
                            $this->db->where('department IN (SELECT departmentid FROM ' . db_prefix() . 'staff_departments WHERE departmentid IN (' . implode(',', $departments_ids) . ') AND staffid="' . get_staff_user_id() . '")');
                        }
                    }
                }

                $this->db->where('status', $status['ticketstatusid']);
                $total = $this->db->count_all_results(db_prefix() . 'tickets');
                if ($total > 0) {
                    array_push($chart['labels'], ticket_status_translate($status['ticketstatusid']));
                    array_push($_data['statusLink'], admin_url('tickets/index/' . $status['ticketstatusid']));
                    array_push($_data['backgroundColor'], $status['statuscolor']);
                    array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
                    array_push($_data['data'], $total);
                }
            }
        }

        $chart['datasets'][] = $_data;

        return $chart;
    }
	public function fetch_details($q,$q1,$dash)
    {
        $have_permission_customers_view = has_permission('projects', '', 'view');
        if ( $have_permission_customers_view) {

           
        $res = '';
        $this->db->select('*');
		$this->db->from(db_prefix() . 'projects');
			$this->db->where('case_type','court_case');
            $this->db->where('start_date>=',$q);
			  $this->db->where('start_date<=',$q1);
           $number_cases1 = $this->db->get()->num_rows();
			
            $this->db->select('*');
		$this->db->from(db_prefix() . 'projects');
			$this->db->where('case_type','labour_case');
            $this->db->where('start_date>=',$q);
			  $this->db->where('start_date<=',$q1);
           $number_cases2 = $this->db->get()->num_rows();
			
			$this->db->from(db_prefix() . 'projects');
			$this->db->where('case_type','police_case');
            $this->db->where('start_date>=',$q);
			  $this->db->where('start_date<=',$q1);
           $number_cases3 = $this->db->get()->num_rows();
			
			$this->db->from(db_prefix() . 'projects');
			$this->db->where('case_type','transfer_case');
            $this->db->where('start_date>=',$q);
			  $this->db->where('start_date<=',$q1);
           $number_cases4 = $this->db->get()->num_rows();
			
			$this->db->from(db_prefix() . 'tickets_civil');
			$this->db->where('datecreated>=',$q);
			 $this->db->where('datecreated<=',$q1);
           $number_cases5 = $this->db->get()->num_rows();
			
			$this->db->from(db_prefix() . 'tickets_police');
		    $this->db->where('datecreated>=',$q);
			 $this->db->where('datecreated<=',$q1);
           $number_cases6 = $this->db->get()->num_rows();
			
			$this->db->from(db_prefix() . 'projects');
			//$this->db->where('case_type','policecase');
            $this->db->where('closed_date>=',$q);
			  $this->db->where('closed_date<=',$q1);
           $number_cases7 = $this->db->get()->num_rows();
			
			 $this->db->select('sum(tblexpenses.paid_amount) as expenses');
    	 $this->db->where('dateadded>=',$q);
			  $this->db->where('dateadded<=',$q1);
			 $number_cases8 = $this->db->get('tblexpenses')->row()->expenses;
			 $this->db->select('sum(tblrecoveries_installments.amount_received) as settlement');
    	 $this->db->where('installment_date>=',$q);
			  $this->db->where('installment_date<=',$q1);
			 $number_cases9 = $this->db->get('tblrecoveries_installments')->row()->settlement;
			 $this->db->select('sum(tblprojects.claiming_amount)as claimamount');
    	 $this->db->where('claiming_date>=',$q);
			  $this->db->where('claiming_date<=',$q1);
			 $number_cases10 = $this->db->get('tblprojects')->row()->claimamount;
			$this->db->from(db_prefix() . 'tickets');
			$this->db->where('service',9);
			$this->db->where('date>=',$q);
			 $this->db->where('date<=',$q1);
           $number_cases11 = $this->db->get()->num_rows();
		 $this->db->select('*');
		$this->db->from(db_prefix() . 'contracts');
			$this->db->where('marked_as_signed',1);
			$this->db->or_where('signed',1);
            $this->db->where('acceptance_date>=',$q);
			  $this->db->where('acceptance_date<=',$q1);
           $number_casesc1 = $this->db->get()->num_rows();
            $this->load->model('contracts_model');
           $contract_types=$this->contracts_model->get_contracts_types();
			if($dash=='contract'){
           $res .='	<div class="col-sm-3 hide">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('civil_case_request').'  <br>'.$number_cases5.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3 hide">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('police_case_request').' <br>'.$number_cases6.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3 hide">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('contract_review').'  <br>'.$number_cases11.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 78px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('signed_contract').'  <br>'.$number_casesc1.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>'; 
                        if(sizeof($contract_types)>0){
                            foreach ($contract_types as  $c_type) {
                               $res .=' <div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 78px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'.get_contracttype_name_by_id($c_type['type']).'  <br>'.total_rows('tblcontracts',['contract_type'=>$c_type['type']]).'</strong></h4>
                            
                                </div>
                            </div>
                        </div>';
                            }
                        }
			}else{
            $res .=  '<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('civil_cases').'<br>'.$number_cases1.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('labour_cases').'<br>'.$number_cases2.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('police_cases').' <br>'.$number_cases3.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('transferred_cases').' <br>'.$number_cases4.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
					
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('closed_cases').' <br>'.$number_cases7.'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('paid_expenses').' <br>'.number_format($number_cases8,2).'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong>'._l('received_amount').' <br>'.number_format($number_cases9,2).'</strong></h4>
                            
                                </div>
                            </div>
                        </div>
						<div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 123, 255, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 75px;"  >
                                <div class="card-body">
                                    <h4 class="card-title" style="text-align:center"><strong> '._l('claim_amount').' <br>'. number_format($number_cases10,2).'</strong></h4>
                            
                                </div>
                            </div>
                        </div>';
			}
               
       

        return $res;
        }
    }
    public function contracts_status_stats()
    {
        $this->load->model('contracts_model');
        $statuses = $this->contracts_model->get_contract_status();
        $colors   = get_system_favourite_colors();

        $chart = [
            'labels'   => [],
            'datasets' => [],
        ];

        $_data                         = [];
        $_data['data']                 = [];
        $_data['backgroundColor']      = [];
        $_data['hoverBackgroundColor'] = [];
        $_data['statusLink']           = [];


        $has_permission = has_permission('contracts', '', 'view');
        $sql            = '';
        foreach ($statuses as $status) {
            $sql .= ' SELECT COUNT(*) as total';
            $sql .= ' FROM ' . db_prefix() . 'contracts';
            $sql .= ' WHERE status=' . $status['id'];
            if (!$has_permission) {
                $sql .= ' AND id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
            }
            $sql .= ' UNION ALL ';
            $sql = trim($sql);
        }

        $result = [];
        if ($sql != '') {
            // Remove the last UNION ALL
            $sql    = substr($sql, 0, -10);
            $result = $this->db->query($sql)->result();
        }

        foreach ($statuses as $key => $status) {
            //array_push($_data['statusLink'], admin_url('projects?status=' . $status['id']));
            array_push($chart['labels'], $status['name']);
            array_push($_data['backgroundColor'], $status['statuscolor']);
            array_push($_data['hoverBackgroundColor'], adjust_color_brightness($status['statuscolor'], -20));
            array_push($_data['data'], $result[$key]->total);
        }

        $chart['datasets'][]           = $_data;
        $chart['datasets'][0]['label'] = _l('home_stats_by_project_status');

        return $chart;
    }
}
