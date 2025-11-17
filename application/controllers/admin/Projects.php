<?php
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
defined('BASEPATH') or exit('No direct script access allowed');

class Projects extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');
        $this->load->model('currencies_model');
        $this->load->helper('date');
        $this->load->model('casediary_model');
        $this->load->model('hearing_model');
    }

	public function index($casetype='court_case')
    {
        close_setup_menu();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['title']    = _l('projects');
        $data['case_type'] = $casetype;
        $this->load->view('admin/projects/manage', $data);
    }
    public function legal_consultancy()
    {
        close_setup_menu();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['title']    = _l('projects');
        $data['case_type'] = 'legal_consultancy';
        $this->load->view('admin/projects/manage', $data);
    }

     public function court_cases()
    {
        close_setup_menu();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['title']    = _l('projects');
        $data['case_type'] = 'court_case';
        $this->load->view('admin/projects/manage', $data);
    }

      public function chequebounce()
    {
        close_setup_menu();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['title']    = _l('projects');
        $data['case_type'] = 'chequebounce';
        $this->load->view('admin/projects/manage', $data);
    }
	    public function policecase()
    {
        close_setup_menu();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['title']    = _l('projects');
        $data['case_type'] = 'policecase';
        $this->load->view('admin/projects/manage', $data);
    }
    

    public function table($clientid = '',$opposite_party='')
    {
        $this->app->get_table_data('projects', [
            'clientid' => $clientid,
            'opposite_party'=> $opposite_party
        ]);
    }
	 public function tableoppo($opposite_party='')
    {
		$clientid='';
        $this->app->get_table_data('projects', [
           'clientid' => $clientid,
            'opposite_party'=> $opposite_party
        ]);
    }

    public function staff_projects()
    {
        $this->app->get_table_data('staff_projects');
    }

    public function expenses($id)
    {
        $this->load->model('expenses_model');
        $this->load->model('payment_modes_model');
        $data['payment_modes'] = $this->payment_modes_model->get('', [], true);
        $this->app->get_table_data('project_expenses', [
            'project_id' => $id,
            'data'       => $data,
        ]);
    }

    public function add_expense()
    {
        if ($this->input->post()) {
			$data=$this->input->post();
			$data['balance_amount']=$this->input->post('balance_amount')+$this->input->post('last_amount')+$this->input->post('vat_amount');
			unset($data['appname']);
            $this->load->model('expenses_model');
            $id = $this->expenses_model->add($data);
            if ($id) {
                set_alert('success', _l('added_successfully', _l('expense')));
                echo json_encode([
                    'url'       => admin_url('projects/view/' . $this->input->post('project_id') . '?group=project_expenses'),
                    'expenseid' => $id,
                ]);
                die;
            }
            echo json_encode([
                'url' => admin_url('projects/view/' . $this->input->post('project_id') . '?group=project_expenses'),
            ]);
            die;
        }
    }

    public function project($id = '')
    {
        if (!staff_can('edit', 'projects') && !staff_can('create', 'projects')) {
            access_denied('Projects');
        }

        

        if ($this->input->post()) {
            $data                = $this->input->post();
            $data['description'] = html_purify($this->input->post('description', false));
            if ($id == '') {
                if (!staff_can('create', 'projects')) {
                    access_denied('Projects');
                }
                $id = $this->projects_model->add($data);
                if ($id) {
					if (!empty($_FILES['ip_logo']['name'])) {
						 handle_ipproject_work_file_upload($id);
					}
                    set_alert('success', _l('added_successfully', _l('project')));
                    redirect(admin_url('projects/view/' . $id));
                }
            } else {
                if (!staff_can('edit', 'projects')) {
                    access_denied('Projects');
                }
                $success = $this->projects_model->update($data, $id);
					 $updated          = false;
            if ($success == true) {
				 $updated = true;
			}
				if (!empty($_FILES['ip_logo']['name'])) {
			 if (handle_ipproject_work_file_upload($id) && !$updated) {
				  $success = true;
			 }
				}
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project')));
                }
                redirect(admin_url('projects/view/' . $id));
            }
        }

        if ($this->input->get('case_type')) {
            $data['case_type'] = $this->input->get('case_type');
			$data['work_type'] =$this->db->select('type')->from('tblproject_types')->where('id',$data['case_type'])->get()->row()->type;
        }else{
            $data['case_type'] = 'other_projects';
			$data['work_type'] ='nonlitigation';
        }
		  if ($this->input->get('related_matter')) {
            $data['related_matter'] = $this->input->get('related_matter');
			$rel_project=$this->db->get_where('tblprojects',array('id'=>$data['related_matter']))->row();
			$data['client_posi']=$rel_project->client_position;
			$data['oppo_posi']=$rel_project->oppositeparty_position;
		  }
        if ($id == '') {
            $title                            = _l('add_new', _l($data['case_type']));
            $data['auto_select_billing_type'] = $this->projects_model->get_most_used_billing_type();

            if ($this->input->get('via_estimate_id')) {
                $this->load->model('estimates_model');
                $data['estimate'] = $this->estimates_model->get($this->input->get('via_estimate_id'));
            }
        } else {
            $data['project']                               = $this->projects_model->get($id);
            $data['project']->settings->available_features = unserialize($data['project']->settings->available_features);
			$data['case_type'] =$data['project']->case_type;
            $data['project_members'] = $this->projects_model->get_project_members($id);
            $title                   = _l('edit', _l($data['case_type']));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
		if ($this->input->get('lawyer_id')) {
            $data['lawyer_id'] = $this->input->get('lawyer_id');
        }

        

        $data['last_project_settings'] = $this->projects_model->get_last_project_settings();

        if (count($data['last_project_settings'])) {
            $key                                          = array_search('available_features', array_column($data['last_project_settings'], 'name'));
            $data['last_project_settings'][$key]['value'] = unserialize($data['last_project_settings'][$key]['value']);
        }

        $data['settings'] = $this->projects_model->get_settings();
        $data['statuses'] = $this->projects_model->get_project_statuses();
        $data['staff']    = $this->staff_model->get('', ['active' => 1]);
		$data['lawyer_staffs'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);
        $data['casetemplates'] = get_case_templates();
		 $this->load->model('tickets_model');
		$data['civils']=$this->tickets_model->get();
		$data['countries']=get_countryproject();
        $data['position_arr'] = get_client_positions();
        $data['project_stages'] = get_project_instances();
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();
		$data['related_matters']=$this->db->select('id,name')->from('tblprojects')->where('parent_sub','main')->order_by('id','desc')->get()->result_array();
		$data['other_clients'] = $this->clients_model->get('',['tblclients.active'=>1]);
        $data['title'] = $title;
        $this->load->view('admin/projects/project', $data);
    }

    public function gantt()
    {
        $data['title'] = _l('project_gant');

        $selected_statuses = [];
        $selectedMember    = null;
        $data['statuses']  = $this->projects_model->get_project_statuses();

        $appliedStatuses = $this->input->get('status');
        $appliedMember   = $this->input->get('member');

        $allStatusesIds = [];
        foreach ($data['statuses'] as $status) {
            if (
                !isset($status['filter_default'])
                || (isset($status['filter_default']) && $status['filter_default'])
                && !$appliedStatuses
            ) {
                $selected_statuses[] = $status['id'];
            } elseif ($appliedStatuses) {
                if (in_array($status['id'], $appliedStatuses)) {
                    $selected_statuses[] = $status['id'];
                }
            } else {
                // All statuses
                $allStatusesIds[] = $status['id'];
            }
        }

        if (count($selected_statuses) == 0) {
            $selected_statuses = $allStatusesIds;
        }


        $data['selected_statuses'] = $selected_statuses;

        if (staff_can('view', 'projects')) {
            $selectedMember          = $appliedMember;
            $data['selectedMember']  = $selectedMember;
            $data['project_members'] = $this->projects_model->get_distinct_projects_members();
        }

        $data['gantt_data'] = $this->projects_model->get_all_projects_gantt_data([
            'status' => $selected_statuses,
            'member' => $selectedMember,
        ]);

        $this->load->view('admin/projects/gantt', $data);
    }

    public function view($id)
    {
        if (staff_can('view', 'projects') || $this->projects_model->is_member($id)) {
            close_setup_menu();
            $project = $this->projects_model->get($id);

            if (!$project) {
                blank_page(_l('project_not_found'));
            }

            $project->settings->available_features = unserialize($project->settings->available_features);
            $data['statuses']                      = $this->projects_model->get_project_statuses();
            
            $data['hearing_type_tab']= !$this->input->get('type') ? 'all' : $this->input->get('type');
            $group = !$this->input->get('group') ? 'project_overview' : $this->input->get('group');

            // Unable to load the requested file: admin/projects/project_tasks#.php - FIX
            if (strpos($group, '#') !== false) {
                $group = str_replace('#', '', $group);
            }
			$data['group1']=getfirststage_byproject($id);
            $data['tabs'] = get_project_tabs_admin();
            $data['tab']  = $this->app_tabs->filter_tab($data['tabs'], $group);

            if (!$data['tab']) {
                show_404();
            }

            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [], true);

            $data['project']  = $project;
            $data['currency'] = $this->projects_model->get_currency($id);
			$data['work_type'] =$this->db->select('type')->from('tblproject_types')->where('id',$project->case_type)->get()->row()->type;
            $data['project_total_logged_time'] = $this->projects_model->total_logged_time($id);

            $data['staff']     = $this->staff_model->get('', ['active' => 1]);
            $percent           = $this->projects_model->calc_progress($id);
            $data['bodyclass'] = '';

            $this->app_scripts->add(
                'projects-js',
                base_url($this->app_scripts->core_file('assets/js', 'projects.js')) . '?v=' . $this->app_scripts->core_version(),
                'admin',
                ['app-js', 'jquery-comments-js', 'frappe-gantt-js', 'circle-progress-js']
            );
			$data['case_overview']=$this->casediary_model->get_details('',$id,$project->project_stage);
            if ($group == 'project_overview') {
                $data['members'] = $this->projects_model->get_project_members($id);
                foreach ($data['members'] as $key => $member) {
                    $data['members'][$key]['total_logged_time'] = 0;
                    $member_timesheets                          = $this->tasks_model->get_unique_member_logged_task_ids($member['staff_id'], ' AND task_id IN (SELECT id FROM ' . db_prefix() . 'tasks WHERE rel_type="project" AND rel_id="' . $this->db->escape_str($id) . '")');

                    foreach ($member_timesheets as $member_task) {
                        $data['members'][$key]['total_logged_time'] += $this->tasks_model->calc_task_total_time($member_task->task_id, ' AND staff_id=' . $member['staff_id']);
                    }
                }
				$data['stages'] = $this->casediary_model->get_project_instances_by_project_id($id,['instance_id!='=>$project->project_stage]);
                $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left']         = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left']         = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);
                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                        $data['project_time_left_percent'] = round($data['project_time_left_percent'], 2);
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left']         = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }

                $__total_where_tasks = 'rel_type = "project" AND rel_id=' . $this->db->escape_str($id);
                if (!staff_can('view', 'tasks')) {
                    $__total_where_tasks .= ' AND ' . db_prefix() . 'tasks.id IN (SELECT taskid FROM ' . db_prefix() . 'task_assigned WHERE staffid = ' . get_staff_user_id() . ')';

                    if (get_option('show_all_tasks_for_project_member') == 1) {
                        $__total_where_tasks .= ' AND (rel_type="project" AND rel_id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . '))';
                    }
                }

                $__total_where_tasks = hooks()->apply_filters('admin_total_project_tasks_where', $__total_where_tasks, $id);

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status != ' . Tasks_model::STATUS_COMPLETE;

                $data['tasks_not_completed'] = total_rows(db_prefix() . 'tasks', $where);
                $total_tasks                 = total_rows(db_prefix() . 'tasks', $__total_where_tasks);
                $data['total_tasks']         = $total_tasks;

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status = ' . Tasks_model::STATUS_COMPLETE . ' AND rel_type="project" AND rel_id="' . $id . '"';

                $data['tasks_completed'] = total_rows(db_prefix() . 'tasks', $where);

                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);
                $data['tasks_not_completed_progress'] = round($data['tasks_not_completed_progress'], 2);

                @$percent_circle        = $percent / 100;
                $data['percent_circle'] = $percent_circle;
				$data['asslawyers']= get_all_assignees_byproject($id);
				$data['legals']=get_all_legal_byproject($id);
				$data['court_order']       = $this->casediary_model->get_courtorders($id,'1');
				$data['court_instances'] = $this->casediary_model->get_project_instances_by_project_id($id);
                $data['project_overview_chart'] = $this->projects_model->get_project_overview_weekly_chart_data($id, ($this->input->get('overview_chart') ? $this->input->get('overview_chart') : 'this_week'));
				  $data['proejct_instances'] = get_project_instances();
            } elseif ($group == 'project_invoices') {
                $this->load->model('invoices_model');

                $data['invoiceid']   = '';
                $data['status']      = '';
                $data['custom_view'] = '';

                $data['invoices_years']       = $this->invoices_model->get_invoices_years();
                $data['invoices_sale_agents'] = $this->invoices_model->get_sale_agents();
                $data['invoices_statuses']    = $this->invoices_model->get_statuses();
            } elseif ($group == 'project_gantt') {
                $gantt_type         = (!$this->input->get('gantt_type') ? 'milestones' : $this->input->get('gantt_type'));
                $taskStatus         = (!$this->input->get('gantt_task_status') ? null : $this->input->get('gantt_task_status'));
                $data['gantt_data'] = $this->projects_model->get_gantt_data($id, $gantt_type, $taskStatus);
            } elseif ($group == 'project_milestones') {
                $data['bodyclass'] .= 'project-milestones ';
                $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed') && $this->input->get('exclude_completed') == 'yes' || !$this->input->get('exclude_completed');

                $data['total_milestones'] = total_rows(db_prefix() . 'milestones', ['project_id' => $id]);
                $data['milestones_found'] = $data['total_milestones'] > 0 || (!$data['total_milestones'] && total_rows(db_prefix() . 'tasks', ['rel_id' => $id, 'rel_type' => 'project', 'milestone' => 0]) > 0);
            } elseif ($group == 'project_files') {
                $data['files'] = $this->projects_model->get_files($id);
				$data['document_types']     = $this->casediary_model->get_document_typesproject();
                $data['category']    = $this->casediary_model->get_courtordernames();
            }elseif ($group == 'project_subfiles') {
				 $data['files'] = $this->projects_model->get_files($id,1);
              //  $data['files'] = $this->projects_model->get_subversionfiles($id);
				$data['document_types']     = $this->casediary_model->get_document_typesproject();
				//$data['court_instances'] = $this->casediary_model->get_project_instances_by_project_id($id);
            } elseif ($group == 'project_expenses') {
                $this->load->model('taxes_model');
                $this->load->model('expenses_model');
                $data['taxes']              = $this->taxes_model->get();
                $data['expense_categories'] = $this->expenses_model->get_category();
                $data['currencies']         = $this->currencies_model->get();
				$data['lawyers'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);
               $data['approvals']         = get_common_approvals($project->id,'expense');
              //  $this->load->model('tickets_model');
				$lastrefno=get_latestexpense_approvalsname($project->id);
				$data['refno']=$this->input->get('reference')?$this->input->get('reference'):$lastrefno;
                $data['appro_statuses']  = $this->expenses_model-> get_expenses_status();
				$data['refund_status']=get_refund_status();
				$data['budgets']=$this->db->get_where('tblproject_budget',['project_id'=>$id])->row();
            } elseif ($group == 'project_activity') {
                $data['activity'] = $this->projects_model->get_activity($id);
            } elseif ($group == 'project_notes') {
                $data['staff_notes'] = $this->projects_model->get_staff_notes($id);
            } elseif ($group == 'project_contracts') {
                $this->load->model('contracts_model');
                $data['contract_types'] = $this->contracts_model->get_contract_types();
                $data['years']          = $this->contracts_model->get_contracts_years();
            } elseif ($group == 'project_estimates') {
                $this->load->model('estimates_model');
                $data['estimates_years']       = $this->estimates_model->get_estimates_years();
                $data['estimates_sale_agents'] = $this->estimates_model->get_sale_agents();
                $data['estimate_statuses']     = $this->estimates_model->get_statuses();
                $data['estimateid']            = '';
                $data['switch_pipeline']       = '';
            } elseif ($group == 'project_tickets') {
                $data['chosen_ticket_status'] = '';
                $this->load->model('tickets_model');
                $data['ticket_assignees'] = $this->tickets_model->get_tickets_assignes_disctinct();
				$data['services'] = $this->tickets_model->get_service();

                $this->load->model('departments_model');
                $data['staff_deparments_ids']          = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                $data['default_tickets_list_statuses'] = hooks()->apply_filters('default_tickets_list_statuses', [1, 2, 4]);
            } elseif ($group == 'project_timesheets') {
                // Tasks are used in the timesheet dropdown
                // Completed tasks are excluded from this list because you can't add timesheet on completed task.
                $data['tasks']                = $this->projects_model->get_tasks($id, 'status != ' . Tasks_model::STATUS_COMPLETE . ' AND billed=0');
                $data['timesheets_staff_ids'] = $this->projects_model->get_distinct_tasks_timesheets_staff($id);
            }elseif ($group == 'project_case_details') {

              $data['proejct_instances'] = get_project_instances();

                $data['court_instances'] = $this->casediary_model->get_project_instances_by_project_id($id);
				
                $data['courts']      = $this->casediary_model->get_courts();
                $data['case_natures']      = $this->casediary_model->get_casenatures();
                $data['staff']       = $this->staff_model->get('', ['active' => 1]);
				$data['countries']=get_countryproject();
				
            	}elseif ($group == 'project_details') {
				$data['staff'] = $this->staff_model->get('', ['active' => 1]);
			// Get all active staff members (used to add reminder)
			$data['members'] = $data['staff'];
					$result=$this->casediary_model->get_project_details_by_project_id($id);
				if($result!='')
                 $data['project_instances'] = $result;
				
             
                $data['staff']       = $this->staff_model->get('', ['active' => 1]);
				
            	}elseif ($group == 'hearings') {
				 $data['courts']      = $this->casediary_model->get_courts();
               $data['hearing_types'] = get_project_instances_added_for_project($id);
                $data['hearings']          = $this->hearing_model->get_hearings_by_project_id($id, array(), true);
                $data['arr_hearinig_references']  = $this->hearing_model->get_hearinig_references();
                $data['arr_court_regions']= $this->hearing_model->get_court_regions();
                $data['hallnumber_types'] = $this->hearing_model->get_hallnumbers();
				 $data['courts']      = $this->casediary_model->get_courts();
                //$data['hearing_types']     =  get_hearing_types();
                              
                

            }elseif ($group == 'scope') {
                $data['scopes']       = $this->casediary_model->get_scopes($id);
            }elseif ($group == 'communication_center') { 
                $data['communication_center']       = $this->casediary_model->get_communication_center($id);
            }elseif ($group == 'project_updates') {
				$data['staff'] = $this->staff_model->get('', ['active' => 1]);
			// Get all active staff members (used to add reminder)
			$data['members'] = $data['staff'];
                $data['case_updates'] = $this->casediary_model->get_case_updates($id,'project');
            }elseif ($group == 'project_handover') {
				$data['staff'] = $this->staff_model->get('', ['active' => 1]);
			// Get all active staff members (used to add reminder)
			$data['members'] = $data['staff'];
                $data['case_handover'] = $this->casediary_model->get_case_handover($id);
            }elseif ($group == 'court_order') {
                $data['court_order']       = $this->casediary_model->get_courtorders($id);
				$data['document_types']    = $this->casediary_model->get_document_types_bycategory('2');
				
            }elseif ($group == 'project_settlement') {
                $data['settlefiles'] = $this->projects_model->get_files_bycategory($id,1);
			//	$data['document_types']    = $this->casediary_model->get_document_types_bycategory('1');// $this->casediary_model->get_courtordernames();
				
            }elseif ($group == 'project_judgement') {

                $data['proejct_instances'] = get_project_instances();
              }elseif($group=='project_submatter'){
				 $data['project_statuses'] = $this->projects_model->get_project_statuses();
			}

            // Discussions
            if ($this->input->get('discussion_id')) {
                $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
                $data['discussion']                        = $this->projects_model->get_discussion($this->input->get('discussion_id'), $id);
                $data['current_user_is_admin']             = is_admin();
            }

            $data['percent'] = $percent;

            $this->app_scripts->add('circle-progress-js', 'assets/plugins/jquery-circle-progress/circle-progress.min.js');

            $other_projects       = [];
            $other_projects_where = 'id != ' . $id;

            $statuses = $this->projects_model->get_project_statuses();

            $other_projects_where .= ' AND (';
            foreach ($statuses as $status) {
                if (isset($status['filter_default']) && $status['filter_default']) {
                    $other_projects_where .= 'status = ' . $status['id'] . ' OR ';
                }
            }

            $other_projects_where = rtrim($other_projects_where, ' OR ');

            $other_projects_where .= ')';

            if (!staff_can('view', 'projects')) {
                $other_projects_where .= ' AND ' . db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')';
            }
			$data['settle_type']=get_settlement_type();
		    $data['settle_nature']=get_settlement_nature();
            $data['other_projects'] = $this->projects_model->get('', $other_projects_where);
            $data['title']          = $data['project']->name;
            $data['bodyclass'] .= 'project invoices-total-manual estimates-total-manual';
            $data['project_status'] = get_project_status_by_id($project->status);
            $data['lawyer_staffs'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);

            $this->load->view('admin/projects/view', $data);
        } else {
            access_denied('Project View');
        }
    }

    public function mark_as()
    {
        $success = false;
        $message = '';
        if ($this->input->is_ajax_request()) {
            if (staff_can('create', 'projects') || staff_can('edit', 'projects')) {
                $status = get_project_status_by_id($this->input->post('status_id'));

                $message = _l('project_marked_as_failed', $status['name']);
                $success = $this->projects_model->mark_as($this->input->post());

                if ($success) {
                    $message = _l('project_marked_as_success', $status['name']);
                }
            }
        }
        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function file($id, $project_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();

       $data['file'] = $this->projects_model->get_file($id, $project_id);
       $data['document_types']     = $this->casediary_model->get_document_typesproject();

        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        $this->load->view('admin/projects/_file', $data);
    }

    public function update_file_data()
    {
        if ($this->input->post()) {
            $this->projects_model->update_file_data($this->input->post());
        }
    }

    public function add_external_file()
    {
        if ($this->input->post()) {
            $data                        = [];
            $data['project_id']          = $this->input->post('project_id');
            $data['files']               = $this->input->post('files');
            $data['external']            = $this->input->post('external');
            $data['visible_to_customer'] = ($this->input->post('visible_to_customer') == 'true' ? 1 : 0);
            $data['staffid']             = get_staff_user_id();
            $this->projects_model->add_external_file($data);
        }
    }

    public function download_all_files($id)
    {
        if ($this->projects_model->is_member($id) || staff_can('view', 'projects')) {
            $files = $this->projects_model->get_files($id);
            if (count($files) == 0) {
                set_alert('warning', _l('no_files_found'));
                redirect(admin_url('projects/view/' . $id . '?group=project_files'));
            }
            $path = get_upload_path_by_type('project') . $id;
            $this->load->library('zip');
            foreach ($files as $file) {
                $this->zip->read_file($path . '/' . $file['file_name']);
            }
            $this->zip->download(slug_it(get_project_name_by_id($id)) . '-files.zip');
            $this->zip->clear_data();
        }
    }

    public function export_project_data($id)
    {
        if (staff_can('create', 'projects')) {
            app_pdf('project-data', LIBSPATH . 'pdf/Project_data_pdf', $id);
        }
    }

    public function update_task_milestone()
    {
        if ($this->input->post()) {
            $this->projects_model->update_task_milestone($this->input->post());
        }
    }

    public function update_milestones_order()
    {
        if ($post_data = $this->input->post()) {
            $this->projects_model->update_milestones_order($post_data);
        }
    }

    public function pin_action($project_id)
    {
        $this->projects_model->pin_action($project_id);
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function add_edit_members($project_id)
    {
        if (staff_can('edit', 'projects')) {
            $this->projects_model->add_edit_members($this->input->post(), $project_id);
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function discussions($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('project_discussions', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function discussion($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $id = $this->projects_model->add_discussion($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('project_discussion'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->edit_discussion($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('project_discussion'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
            die;
        }
    }

    public function get_discussion_comments($id, $type)
    {
        echo json_encode($this->projects_model->get_discussion_comments($id, $type));
    }

    public function add_discussion_comment($discussion_id, $type)
    {
        echo json_encode($this->projects_model->add_discussion_comment(
            $this->input->post(null, false),
            $discussion_id,
            $type
        ));
    }

    public function update_discussion_comment()
    {
        echo json_encode($this->projects_model->update_discussion_comment($this->input->post(null, false)));
    }

    public function delete_discussion_comment($id)
    {
        echo json_encode($this->projects_model->delete_discussion_comment($id));
    }

    public function delete_discussion($id)
    {
        $success = false;
        if (staff_can('delete', 'projects')) {
            $success = $this->projects_model->delete_discussion($id);
        }
        $alert_type = 'warning';
        $message    = _l('project_discussion_failed_to_delete');
        if ($success) {
            $alert_type = 'success';
            $message    = _l('project_discussion_deleted');
        }
        echo json_encode([
            'alert_type' => $alert_type,
            'message'    => $message,
        ]);
    }

    public function change_milestone_color()
    {
        if ($this->input->post()) {
            $this->projects_model->update_milestone_color($this->input->post());
        }
    }

    public function upload_file($project_id)
    {
        handle_project_file_uploads($project_id);
    }

    public function change_file_visibility($id, $visible)
    {
        if ($this->input->is_ajax_request()) {
            $this->projects_model->change_file_visibility($id, $visible);
        }
    }

    public function change_activity_visibility($id, $visible)
    {
        if (staff_can('create', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->projects_model->change_activity_visibility($id, $visible);
            }
        }
    }

    public function remove_file($project_id, $id)
    { 
		$finaldoc=$this->db->get_where('tblproject_files',array('id'=>$id))->row()->final_doc;
        $this->projects_model->remove_file($id);
		if($finaldoc==1)
        redirect(admin_url('projects/view/' . $project_id . '?group=project_subfiles'));
		else
        redirect(admin_url('projects/view/' . $project_id . '?group=project_files'));
    }
	public function remove_trade_mark_logo($project_id)
    {
        $this->casediary_model->remove_trade_mark_logo($project_id);
        set_alert('success', _l('deleted'));
        redirect(admin_url('projects/project/' . $project_id));
    }
    public function milestones_kanban()
    {
        $data['milestones_exclude_completed_tasks'] = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $data['project_id'] = $this->input->get('project_id');
        $data['milestones'] = [];

        $data['milestones'][] = [
            'name'              => _l('milestones_uncategorized'),
            'id'                => 0,
            'total_logged_time' => $this->projects_model->calc_milestone_logged_time($data['project_id'], 0),
            'color'             => null,
        ];

        $_milestones = $this->projects_model->get_milestones($data['project_id']);

        foreach ($_milestones as $m) {
            $data['milestones'][] = $m;
        }

        echo $this->load->view('admin/projects/milestones_kan_ban', $data, true);
    }

    public function milestones_kanban_load_more()
    {
        $milestones_exclude_completed_tasks = $this->input->get('exclude_completed_tasks') && $this->input->get('exclude_completed_tasks') == 'yes';

        $status     = $this->input->get('status');
        $page       = $this->input->get('page');
        $project_id = $this->input->get('project_id');
        $where      = [];
        if ($milestones_exclude_completed_tasks) {
            $where['status !='] = Tasks_model::STATUS_COMPLETE;
        }
        $tasks = $this->projects_model->do_milestones_kanban_query($status, $project_id, $page, $where);
        foreach ($tasks as $task) {
            $this->load->view('admin/projects/_milestone_kanban_card', ['task' => $task, 'milestone' => $status]);
        }
    }

    public function milestones($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('milestones', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function milestone($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                if (!staff_can('create_milestones', 'projects')) {
                    access_denied();
                }

                $id = $this->projects_model->add_milestone($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project_milestone')));
                }
            } else {
                if (!staff_can('edit_milestones', 'projects')) {
                    access_denied();
                }

                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->update_milestone($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('project_milestone')));
                }
            }
        }

        redirect(admin_url('projects/view/' . $this->input->post('project_id') . '?group=project_milestones'));
    }

    public function delete_milestone($project_id, $id)
    {
        if (staff_can('delete_milestones', 'projects')) {
            if ($this->projects_model->delete_milestone($id)) {
                set_alert('deleted', 'project_milestone');
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=project_milestones'));
    }

    public function bulk_action_files()
    {
        hooks()->do_action('before_do_bulk_action_for_project_files');
        $total_deleted       = 0;
        $hasPermissionDelete = staff_can('delete', 'projects');
        // bulk action for projects currently only have delete button
        if ($this->input->post()) {
            $fVisibility = $this->input->post('visible_to_customer') == 'true' ? 1 : 0;
            $ids         = $this->input->post('ids');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($hasPermissionDelete && $this->input->post('mass_delete') && $this->projects_model->remove_file($id)) {
                        $total_deleted++;
                    } else {
                        $this->projects_model->change_file_visibility($id, $fVisibility);
                    }
                }
            }
        }
        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_files_deleted', $total_deleted));
        }
    }

    public function timesheets($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('timesheets', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

    public function timesheet()
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            $success = $this->tasks_model->timesheet($this->input->post());
            if ($success === true) {
                $message = _l('added_successfully', _l('project_timesheet'));
            } elseif (is_array($success) && isset($success['end_time_smaller'])) {
                $message = _l('failed_to_add_project_timesheet_end_time_smaller');
            } else {
                $message = _l('project_timesheet_not_updated');
            }
            echo json_encode([
                'success' => $success,
                'message' => $message,
            ]);
            die;
        }
    }

    public function timesheet_task_assignees($task_id, $project_id, $staff_id = 'undefined')
    {
        $assignees             = $this->tasks_model->get_task_assignees($task_id);
        $data                  = '';
        $has_permission_edit   = staff_can('edit', 'projects');
        $has_permission_create = staff_can('edit', 'projects');
        // The second condition if staff member edit their own timesheet
        if ($staff_id == 'undefined' || $staff_id != 'undefined' && (!$has_permission_edit || !$has_permission_create)) {
            $staff_id     = get_staff_user_id();
            $current_user = true;
        }
        foreach ($assignees as $staff) {
            $selected = '';
            // maybe is admin and not project member
            if ($staff['assigneeid'] == $staff_id && $this->projects_model->is_member($project_id, $staff_id)) {
                $selected = ' selected';
            }
            if ((!$has_permission_edit || !$has_permission_create) && isset($current_user)) {
                if ($staff['assigneeid'] != $staff_id) {
                    continue;
                }
            }
            $data .= '<option value="' . $staff['assigneeid'] . '"' . $selected . '>' . get_staff_full_name($staff['assigneeid']) . '</option>';
        }
        echo $data;
    }

    public function remove_team_member($project_id, $staff_id)
    {
        if (staff_can('edit', 'projects')) {
            if ($this->projects_model->remove_team_member($project_id, $staff_id)) {
                set_alert('success', _l('project_member_removed'));
            }
        }

        redirect(admin_url('projects/view/' . $project_id));
    }

    


    public function save_note($project_id)
    {
        if ($this->input->post()) {
            $success = $this->projects_model->save_note($this->input->post(null, false), $project_id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project_note')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_notes'));
        }
    }

    public function delete($project_id)
    {
        if (staff_can('delete', 'projects')) {
            $project = $this->projects_model->get($project_id);
            $success = $this->projects_model->delete($project_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('project')));
                if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
                    redirect($_SERVER['HTTP_REFERER']);
                } else {
                    redirect(admin_url('projects'));
                }
            } else {
                set_alert('warning', _l('problem_deleting', _l('project_lowercase')));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function copy($project_id)
    {
        if (staff_can('create', 'projects')) {
            $id = $this->projects_model->copy($project_id, $this->input->post());
            if ($id) {
                set_alert('success', _l('project_copied_successfully'));
                redirect(admin_url('projects/view/' . $id));
            } else {
                set_alert('danger', _l('failed_to_copy_project'));
                redirect(admin_url('projects/view/' . $project_id));
            }
        }
    }

    public function mass_stop_timers($project_id, $billable = 'false')
    {
        if (staff_can('create', 'invoices')) {
            $where = [
                'billed'       => 0,
                'startdate <=' => date('Y-m-d'),
            ];
            if ($billable == 'true') {
                $where['billable'] = true;
            }
            $tasks                = $this->projects_model->get_tasks($project_id, $where);
            $total_timers_stopped = 0;
            foreach ($tasks as $task) {
                $this->db->where('task_id', $task['id']);
                $this->db->where('end_time IS NULL');
                $this->db->update(db_prefix() . 'taskstimers', [
                    'end_time' => time(),
                ]);
                $total_timers_stopped += $this->db->affected_rows();
            }
            $message = _l('project_tasks_total_timers_stopped', $total_timers_stopped);
            $type    = 'success';
            if ($total_timers_stopped == 0) {
                $type = 'warning';
            }
            echo json_encode([
                'type'    => $type,
                'message' => $message,
            ]);
        }
    }

    public function get_pre_invoice_project_info($project_id)
    {
        if (staff_can('create', 'invoices')) {
            $data['billable_tasks'] = $this->projects_model->get_tasks($project_id, [
                'billable'     => 1,
                'billed'       => 0,
                'startdate <=' => date('Y-m-d'),
            ]);

            $data['not_billable_tasks'] = $this->projects_model->get_tasks($project_id, [
                'billable'    => 1,
                'billed'      => 0,
                'startdate >' => date('Y-m-d'),
            ]);

            $data['project_id']   = $project_id;
            $data['billing_type'] = get_project_billing_type($project_id);

            $this->load->model('expenses_model');
            $this->db->where('invoiceid IS NULL');
            $data['expenses'] = $this->expenses_model->get('', [
                'project_id' => $project_id,
                'billable'   => 1,
            ]);

            $this->load->view('admin/projects/project_pre_invoice_settings', $data);
        }
    }

    public function get_invoice_project_data()
    {
        if (staff_can('create', 'invoices')) {
            $type       = $this->input->post('type');
            $project_id = $this->input->post('project_id');
            // Check for all cases
            if ($type == '') {
                $type == 'single_line';
            }
            $this->load->model('payment_modes_model');
            $data['payment_modes'] = $this->payment_modes_model->get('', [
                'expenses_only !=' => 1,
            ]);
            $this->load->model('taxes_model');
            $data['taxes']         = $this->taxes_model->get();
            $data['currencies']    = $this->currencies_model->get();
            $data['base_currency'] = $this->currencies_model->get_base_currency();
            $this->load->model('invoice_items_model');

            $data['ajaxItems'] = false;
            if (total_rows(db_prefix() . 'items') <= ajax_on_total_items()) {
                $data['items'] = $this->invoice_items_model->get_grouped();
            } else {
                $data['items']     = [];
                $data['ajaxItems'] = true;
            }

            $data['items_groups'] = $this->invoice_items_model->get_groups();
            $data['staff']        = $this->staff_model->get('', ['active' => 1]);
            $project              = $this->projects_model->get($project_id);
            $data['project']      = $project;
            $items                = [];

            $project    = $this->projects_model->get($project_id);
            $item['id'] = 0;

            $default_tax     = unserialize(get_option('default_tax'));
            $item['taxname'] = $default_tax;

            $tasks = $this->input->post('tasks');
            if ($tasks) {
                $item['long_description'] = '';
                $item['qty']              = 0;
                $item['task_id']          = [];
                if ($type == 'single_line') {
                    $item['description'] = $project->name;
                    foreach ($tasks as $task_id) {
                        $task = $this->tasks_model->get($task_id);
                        $sec  = $this->tasks_model->calc_task_total_time($task_id);
                        $item['long_description'] .= $task->name . ' - ' . seconds_to_time_format(task_timer_round($sec)) . ' ' . _l('hours') . "\r\n";
                        $item['task_id'][] = $task_id;
                        if ($project->billing_type == 2) {
                            if ($sec < 60) {
                                $sec = 0;
                            }
                            $item['qty'] += sec2qty(task_timer_round($sec));
                        }
                    }
                    if ($project->billing_type == 1) {
                        $item['qty']  = 1;
                        $item['rate'] = $project->project_cost;
                    } elseif ($project->billing_type == 2) {
                        $item['rate'] = $project->project_rate_per_hour;
                    }
                    $item['unit'] = '';
                    $items[]      = $item;
                } elseif ($type == 'task_per_item') {
                    foreach ($tasks as $task_id) {
                        $task                     = $this->tasks_model->get($task_id);
                        $sec                      = $this->tasks_model->calc_task_total_time($task_id);
                        $item['description']      = $project->name . ' - ' . $task->name;
                        $item['qty']              = floatVal(sec2qty(task_timer_round($sec)));
                        $item['long_description'] = seconds_to_time_format(task_timer_round($sec)) . ' ' . _l('hours');
                        if ($project->billing_type == 2) {
                            $item['rate'] = $project->project_rate_per_hour;
                        } elseif ($project->billing_type == 3) {
                            $item['rate'] = $task->hourly_rate;
                        }
                        $item['task_id'] = $task_id;
                        $item['unit']    = '';
                        $items[]         = $item;
                    }
                } elseif ($type == 'timesheets_individualy') {
                    $timesheets     = $this->projects_model->get_timesheets($project_id, $tasks);
                    $added_task_ids = [];
                    foreach ($timesheets as $timesheet) {
                        if ($timesheet['task_data']->billed == 0 && $timesheet['task_data']->billable == 1) {
                            $item['description'] = $project->name . ' - ' . $timesheet['task_data']->name;
                            if (!in_array($timesheet['task_id'], $added_task_ids)) {
                                $item['task_id'] = $timesheet['task_id'];
                            }

                            array_push($added_task_ids, $timesheet['task_id']);

                            $item['qty']              = floatVal(sec2qty(task_timer_round($timesheet['total_spent'])));
                            $item['long_description'] = _l('project_invoice_timesheet_start_time', _dt($timesheet['start_time'], true)) . "\r\n" . _l('project_invoice_timesheet_end_time', _dt($timesheet['end_time'], true)) . "\r\n" . _l('project_invoice_timesheet_total_logged_time', seconds_to_time_format(task_timer_round($timesheet['total_spent']))) . ' ' . _l('hours');

                            if ($this->input->post('timesheets_include_notes') && $timesheet['note']) {
                                $item['long_description'] .= "\r\n\r\n" . _l('note') . ': ' . $timesheet['note'];
                            }

                            if ($project->billing_type == 2) {
                                $item['rate'] = $project->project_rate_per_hour;
                            } elseif ($project->billing_type == 3) {
                                $item['rate'] = $timesheet['task_data']->hourly_rate;
                            }
                            $item['unit'] = '';
                            $items[]      = $item;
                        }
                    }
                }
            }
            if ($project->billing_type != 1) {
                $data['hours_quantity'] = true;
            }
            if ($this->input->post('expenses')) {
                if (isset($data['hours_quantity'])) {
                    unset($data['hours_quantity']);
                }
                if (count($tasks) > 0) {
                    $data['qty_hrs_quantity'] = true;
                }
                $expenses       = $this->input->post('expenses');
                $addExpenseNote = $this->input->post('expenses_add_note');
                $addExpenseName = $this->input->post('expenses_add_name');

                if (!$addExpenseNote) {
                    $addExpenseNote = [];
                }

                if (!$addExpenseName) {
                    $addExpenseName = [];
                }

                $this->load->model('expenses_model');
                foreach ($expenses as $expense_id) {
                    // reset item array
                    $item                     = [];
                    $item['id']               = 0;
                    $expense                  = $this->expenses_model->get($expense_id);
                    $item['expense_id']       = $expense->expenseid;
                    $item['description']      = _l('item_as_expense') . ' ' . $expense->name;
                    $item['long_description'] = $expense->description;

                    if (in_array($expense_id, $addExpenseNote) && !empty($expense->note)) {
                        $item['long_description'] .= PHP_EOL . $expense->note;
                    }

                    if (in_array($expense_id, $addExpenseName) && !empty($expense->expense_name)) {
                        $item['long_description'] .= PHP_EOL . $expense->expense_name;
                    }

                    $item['qty'] = 1;

                    $item['taxname'] = [];
                    if ($expense->tax != 0) {
                        array_push($item['taxname'], $expense->tax_name . '|' . $expense->taxrate);
                    }
                    if ($expense->tax2 != 0) {
                        array_push($item['taxname'], $expense->tax_name2 . '|' . $expense->taxrate2);
                    }
                    $item['rate']  = $expense->amount;
                    $item['order'] = 1;
                    $item['unit']  = '';
                    $items[]       = $item;
                }
            }
            $data['customer_id']          = $project->clientid;
            $data['invoice_from_project'] = true;
            $data['add_items']            = $items;
            $this->load->view('admin/projects/invoice_project', $data);
        }
    }

    public function get_rel_project_data($id, $task_id = '')
    {
        if ($this->input->is_ajax_request()) {
            $selected_milestone = '';
            if ($task_id != '' && $task_id != 'undefined') {
                $task               = $this->tasks_model->get($task_id);
                $selected_milestone = $task->milestone;
            }

            $allow_to_view_tasks = 0;
            $this->db->where('project_id', $id);
            $this->db->where('name', 'view_tasks');
            $project_settings = $this->db->get(db_prefix() . 'project_settings')->row();
            if ($project_settings) {
                $allow_to_view_tasks = $project_settings->value;
            }

            $deadline = get_project_deadline($id);

            echo json_encode([
                'deadline'            => $deadline,
                'deadline_formatted'  => $deadline ? _d($deadline) : null,
                'allow_to_view_tasks' => $allow_to_view_tasks,
                'billing_type'        => get_project_billing_type($id),
                'milestones'          => render_select('milestone', $this->projects_model->get_milestones($id), [
                    'id',
                    'name',
                ], 'task_milestone', $selected_milestone),
            ]);
        }
    }

    public function invoice_project($project_id)
    {
        if (staff_can('create', 'invoices')) {
            $this->load->model('invoices_model');
            $data               = $this->input->post();
            $data['project_id'] = $project_id;
            $invoice_id         = $this->invoices_model->add($data);
            if ($invoice_id) {
                $this->projects_model->log_activity($project_id, 'project_activity_invoiced_project', format_invoice_number($invoice_id));
                set_alert('success', _l('project_invoiced_successfully'));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_invoices'));
        }
    }

    public function view_project_as_client($id, $clientid)
    {
        if (is_admin()) {
            login_as_client($clientid);
            redirect(site_url('clients/project/' . $id));
        }
    }

    public function get_staff_names_for_mentions($projectId)
    {
        if ($this->input->is_ajax_request()) {
            $projectId = $this->db->escape_str($projectId);

            $members = $this->projects_model->get_project_members($projectId);
            $members = array_map(function ($member) {
                $staff = $this->staff_model->get($member['staff_id']);

                $_member['id'] = $member['staff_id'];
                $_member['name'] = $staff->firstname . ' ' . $staff->lastname;
                return $_member;
            }, $members);

            echo json_encode($members);
        }
    }
    public function save_case_details($project_id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();

            if(isset($data['file_no']) && $data['file_no'] != ''){
                $project_data['file_no'] = $data['file_no'];
            }
            if(isset($data['clients_makani']) && $data['clients_makani'] != ''){
                $project_data['clients_makani'] = $data['clients_makani'];
            }
            if(isset($data['opposite_makani']) && $data['opposite_makani'] != ''){
                $project_data['opposite_makani'] = $data['opposite_makani'];
            }
            if(isset($data['case_nature']) && $data['case_nature'] != ''){
                $project_data['case_nature'] = $data['case_nature'];
            }
            if(isset($data['opposite_party']) && $data['opposite_party'] != ''){
                $project_data['opposite_party'] = $data['opposite_party'];
            }
            if(isset($data['referred_by']) && $data['referred_by'] != ''){
                $project_data['referred_by'] = $data['referred_by'];
            }
            if(isset($data['claiming_amount']) && $data['claiming_amount'] != ''){
                $project_data['claiming_amount'] = $data['claiming_amount'];
            }



            unset($data['file_no']);
            unset($data['clients_makani']);
            unset($data['opposite_makani']);
            unset($data['case_nature']);
            unset($data['opposite_party']);
            unset($data['referred_by']);
            unset($data['claiming_amount']);
            

           
            if(isset($data['id'])){ 
                $success = $this->casediary_model->update_case_details_data($data, $project_id);
            }else{
                $success = $this->casediary_model->add_case_details_data($data, $project_id);
            }

            $this->casediary_model->update_project_table_data($project_data, $project_id);
            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project_case_details')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_case_details'));
        }
    }

     public function save_project_table_details($project_id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
			//unset($data['close_attachment']);
            $success = $this->casediary_model->update_project_table_data($data, $project_id);
            if ($success) {
				handle_project_close_file_upload($project_id);	
                set_alert('success', _l('updated_successfully', _l('project_case_details')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_case_details'));
        }
    }

    /* public function hearing($id = '')
    {
        if ($this->input->post()) {
            $hearing_type =    $this->input->post('hearing_type'); 
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $project_id = $this->input->post('project_id');
                $id = $this->hearing_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l($hearing_type)));
                     redirect(admin_url('projects/view/' . $project_id . '?group=hearings&type='.$hearing_type));
                }
                
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $project_id = $this->input->post('project_id');
                $success = $this->hearing_model->update($data, $id);
                if ($success) {
                  set_alert('success', _l('updated_successfully', _l($hearing_type)));
                     redirect(admin_url('projects/view/' . $project_id . '?group=hearings&type='.$hearing_type));
                }
                
            }
       
            redirect(admin_url('projects/view/' . $project_id . '?group=hearings&type='.$hearing_type));     
        }

    }*/

    /* Add or update lead */
    public function hearing($id = '')
    {
        /*if (!is_staff_member() || ($id != '' && !$this->leads_model->staff_can_access_lead($id))) {
            ajax_access_denied();
        }*/

        if ($this->input->post()) {
            $hearing_type  =    $this->input->post('hearing_type'); 
            if ($id == '') {
                $id = $this->hearing_model->add($this->input->post());
                $message = $id ? _l('added_successfully', _l('hearing')) : '';

                echo json_encode([
                    'success'  => $id ? true : false,
                    'id'       => $id,
                    'message'  => $message,
                    'leadView' => $id ? $this->_get_hearing_data($id) : [],
                    'hearing_type'=>$hearing_type,
                ]);
            } else {
                
                $message         = '';
                $success = $this->hearing_model->update($this->input->post(), $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('hearing'));
                }
                echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
                    'leadView'         => $this->_get_hearing_data($id),
                    'hearing_type'     =>$hearing_type,
                ]);
            }
            die;
        }

        echo json_encode([
            'leadView' => $this->_get_hearing_data($id),
        ]);
    }

     private function _get_hearing_data($id = '')
    {
        $reminder_data         = '';
        if (is_numeric($id)) {
            

            $hearing = $this->hearing_model->get($id);

            if (!$hearing) {
                header('HTTP/1.0 404 Not Found');
                echo _l('not_found');
                die;
            }

            $data['hearing'] = $hearing;   
        }
        $data['hearing_types'] = get_project_instances();
        $data['arr_hearinig_references']  = $this->hearing_model->get_hearinig_references();
        $data['arr_court_regions']= $this->hearing_model->get_court_regions();
        $data['hallnumber_types'] = $this->hearing_model->get_hallnumbers();
        $data['lawyer_staffs'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);
        $data['courts']      = $this->casediary_model->get_courts();
        $data['case_natures']      = $this->casediary_model->get_casenatures();
		$data['projects']     = $this->projects_model->get();
        return [
            'data'          => $this->load->view('admin/projects/project_edit_hearing', $data, true),
            //'reminder_data' => $reminder_data,
        ];
    }

    public function delete_hearing($project_id, $id)
    {
        if (has_permission('projects', '', 'delete')) {
            if ($this->hearing_model->delete($id)) {
                  set_alert('success', _l('deleted',_l('hearing')));
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=hearings'));
    }
    public function hearing_notice($id)
    {
        if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }
        if (!$id) {
            redirect(admin_url('projects'));
        }
        $hearing =  $this->hearing_model->get($id, array(), true);

        try {
            $pdf = hearing_notice_pdf($hearing);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($hearing->subject) . '.pdf', $type);
    }   

     public function send_hearing_notice_to_email($id)
    {

        if ($this->input->post()) {
            $hearing_id = $this->input->post('hid_hearing_id');
            $success    = $this->hearing_model->send_hearing_to_email($hearing_id,$this->input->post('email_template_custom'), $this->input->post('attach_pdf'), $this->input->post('cc'),$id);
            if ($success) {
                set_alert('success', _l('hearing_sent_to_email_success'));
            } else {
                set_alert('danger', _l('hearing_sent_to_email_fail'));
            }

            $project_id = $id;
            redirect(admin_url('projects/view/' . $project_id . '?group=hearings'));
        
        }
    }

    public function delete_communications($project_id, $id)
    {
        if (has_permission('casediary', '', 'delete')) {
            if ($this->casediary_model->delete_communication($id)) {
                  set_alert('success', _l('deleted'));
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=communication_center'));
    }

    public function get_communication($id){
        $data['id']    = $id;
        $data['title'] = 'Matter-Communication Center';
        $data['data']  = $this->casediary_model->get_communication_center_by_id($id);
        $this->load->view('admin/projects/communication_center_view', $data);
    }

   

   public function communication_center_print($id,$communication_id)
    {
        if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }
        if (!$id) {
            redirect(admin_url('projects'));
        }
        $project = $this->projects_model->get($id);
        $project->communication = $this->casediary_model->get_communication_center_by_id($communication_id);

        try {
            $pdf = communication_center_pdf($project);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($project->name) . '.pdf', $type);
    }

     public function bulk_email_files()
    {
        //do_action('before_do_bulk_action_for_project_files');
        $total_deleted = 0;
        $hasPermissionDelete = has_permission('projects', '', 'delete');
        // bulk action for projects currently only have delete button
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            if (is_array($ids)) {
                $this->load->model('emails_model');
                foreach ($ids as $id) {
                    $file = $this->projects_model->get_file($id);
                    $this->emails_model->add_attachment(array(
                        'attachment' => PROJECT_ATTACHMENTS_FOLDER .$file->project_id.'/'.$file->file_name,
                        'filename' => $file->file_name,
                        'type' => $file->filetype,
                        'read' => true,
                    ));
                }
            }

            $message = $this->input->post('send_file_message');
            //$bcc = $this->input->post('bcc');
            $message = nl2br($message);
            $success = $this->emails_model->send_simple_email($this->input->post('send_file_email'), $this->input->post('send_file_subject'), $message);
            if ($success) {
                set_alert('success', _l('custom_file_success_send', $this->input->post('send_file_email')));
            } else {
                set_alert('warning', _l('custom_file_fail_send'));
            }

        }
        /*if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_files_deleted', $total_deleted));
        }*/
    }

    public function hearings_tables($projectID,$type)
    {
        $this->app->get_table_data('hearings_tables', array(
            'project_id' => $projectID ,'hearing_type'=>$type
        ));
    }

    public function pagination()
    {


      $q='';
      $casetype = $status = '';
      $where= false;
      if($this->input->post()){ /*print_r($_POST);*/
        $q= $this->input->post('q');
        if($this->input->post('case_type') != ' '){
            $casetype =$this->input->post('case_type');
        }
        if($this->input->post('status') != ''){
            $status = $this->input->post('status');
        }
      }  
      $this->load->library("pagination");
      $config = array();
      $config["base_url"] = "#";
      $config["total_rows"] = $this->projects_model->fetch_project_details_num_rows($q,$casetype,$status);
      $config["per_page"] = 12;
      $config["uri_segment"] = 4;
      $config["use_page_numbers"] = TRUE;
      $config["full_tag_open"] = '<ul class="pagination project-page">';
      $config["full_tag_close"] = '</ul>';
      $config["first_tag_open"] = '<li>';
      $config["first_tag_close"] = '</li>';
      $config["last_tag_open"] = '<li>';
      $config["last_tag_close"] = '</li>';
      $config['next_link'] = '&gt;';
      $config["next_tag_open"] = '<li>';
      $config["next_tag_close"] = '</li>';
      $config["prev_link"] = "&lt;";
      $config["prev_tag_open"] = "<li>";
      $config["prev_tag_close"] = "</li>";
      $config["cur_tag_open"] = "<li class='active'><a href='#'>";
      $config["cur_tag_close"] = "</a></li>";
      $config["num_tag_open"] = "<li>";
      $config["num_tag_close"] = "</li>";
      $config["num_links"] = 1;
      $this->pagination->initialize($config);
      $page = $this->uri->segment(4);
      $start = ($page - 1) * $config["per_page"];
      
      
      $output = array(
       'pagination_link'  => $this->pagination->create_links(),
       'project_data'   => $this->projects_model->fetch_project_details($q,$config["per_page"], $start,$casetype,$status),
       'total_cases'=> '<span class="badge badge-success" style="padding: 10px;
    font-size: 15px;"><b>'.$config["total_rows"].'  Cases</b></span>',
      );
      echo json_encode($output);
    }


     public function update_expense_approvals()
    {
        if ($this->input->post()) {
            $success = $this->projects_model->update_expense_approvals($this->input->post());
            if ($success) {
                set_alert('success', _l('updated_successfully'));
            }
            echo json_encode([
                'success' => $success,
            ]);
            die();
        }
    }

    public function change_approval_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->projects_model->change_approval_status($id, $status));
        }
    }
	 public function change_approval_date_ajax($id,$appdate)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->projects_model->change_approval_date($id,$appdate));
        }
    }
	 public function change_approval_remarks_ajax($id)
    {
          if ($this->input->is_ajax_request()) {
            echo json_encode($this->projects_model->change_approval_remarks($id, $this->input->post('remarks')));
        }
    }

     public function expense_statement($nameid,$id)
    {
		
        if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }
        if (!$id) {
            redirect(admin_url('projects'));
        }
        $expenseapprove =  $this->projects_model->get($id);
        $expenseapprove->approval = get_approvals($id,'expense','',$nameid);//get_expense_approvals_pdf($nameid,$id,3);
		 $expenseapprove->referenceno = get_expense_approvalsname($nameid,$id);
        $this->load->model('expenses_model');
        $expenseapprove->expenses =  $this->expenses_model->get('',array('tblexpenses.project_id'=>$id,'tblexpenses.approvalid'=>$nameid));//,'approve_status !='=>3
		$cqry = $this->db->select('claiming_amount')->from('tblcase_details')->where('project_id',$id)->order_by('id','desc')->limit(1)->get();
		$expenseapprove->lastclaim='';
		if($cqry->num_rows()>0){
			$expenseapprove->lastclaim=$cqry->row()->claiming_amount;
		}
		
        $qry = $this->db->select('lawyer_id')->from('tblcase_details')->where('project_id',$id)->order_by('id','desc')->limit(1)->get();
        $expenseapprove->lawyername= '';
        if($qry->num_rows() > 0){
            $lawyers =json_decode($qry->row()->lawyer_id);
			 foreach ($lawyers as $lawyername) {
            $expenseapprove->lawyername .= get_staff_full_name($lawyername);
			 }
        }
      
        try {
            $pdf = expense_approval_pdf($expenseapprove);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($expenseapprove->name) . '.pdf', $type);
    } 
	   public function expense_statementall($id)
    {
         if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }
        if (!$id) {
            redirect(admin_url('projects'));
        }
        $expenseapprove =  $this->projects_model->get($id);
       // $expenseapprove->approval = get_approvals($id,'expense',3);
        $this->load->model('expenses_model');
        $expenseapprove->expenses =  $this->expenses_model->get_expenses_cat_total($id);
		 $expenseapprove->expensesall =  $this->expenses_model->get('',array('tblexpenses.project_id'=>$id));
		$cqry = $this->db->select('claiming_amount')->from('tblcase_details')->where('project_id',$id)->order_by('id','desc')->limit(1)->get();
		$expenseapprove->lastclaim='';
		if($cqry->num_rows()>0){
			$expenseapprove->lastclaim=$cqry->row()->claiming_amount;
		}
		
        $qry = $this->db->select('lawyer_id')->from('tblcase_details')->where('project_id',$id)->order_by('id','desc')->limit(1)->get();
        $expenseapprove->lawyername= '';
        if($qry->num_rows() > 0){
            $lawyers =json_decode($qry->row()->lawyer_id);
			 foreach ($lawyers as $lawyername) {
            $expenseapprove->lawyername .= get_staff_full_name($lawyername);
			 }
        }
         
        try {
            $pdf = expense_approvalall_pdf($expenseapprove);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($expenseapprove->name) . '.pdf', $type);
    } 

    public function save_court_instance($project_id)
    {
        if ($this->input->post()) {
            $data = $this->input->post();
            if(isset($data['id'])){
                $success = $this->casediary_model->update_case_details_data($data, $project_id);
            }else{
                $success = $this->casediary_model->add_case_details_data($data, $project_id);
            }

            if ($success) {
                set_alert('success', _l('updated_successfully', _l('project_case_details')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_case_details'));
        }
    }

    public function change_expense_approval_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->projects_model->change_expense_approval_status($id, $status));
        }
    }
	/*==========project installment====================*/
	 public function installments($client_id)
    {
        $this->app->get_table_data('my_project_installments', array(
            'client_id' => $client_id,
        ));
    }

		 public function receiptinstallment($project_id, $contact_id = '')
    {
        if (!has_permission('projects', '', 'view')) {
            if (!is_recovery_admin($project_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['project_id'] = $project_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();

            unset($data['contactid']);
               if (!has_permission('projects', '', 'edit')) {
                    
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;

                }
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                $success= handle_installment_receipt_image_upload($contact_id,$project_id,'installment');
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('installment'));
                }
                
                $totalpaid = $this->projects_model->get_installment_totalpaid($project_id);
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email,
                    'has_primary_contact'=>true,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            
			  
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('installment_lowercase'));
        } else {
            $data['contact'] = $this->projects_model->get_installment($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = _l('add_new', _l('installment_receipt'));
        }


       // $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/projects/modals/installment', $data);
    }
	 public function installment($project_id, $contact_id = '')
    {
        if (!has_permission('projects', '', 'view')) {
            if (!is_recovery_admin($project_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['project_id'] = $project_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();

            unset($data['contactid']);
            if ($contact_id == '') {
               /* if (!has_permission('projects', '', 'create')) {
                    if (!is_recovery_admin($project_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    }
                }*/
                $id      = $this->projects_model->add_installment($data, $project_id);
                $message = '';
                $success = false;
                if ($id) {
					  handle_installment_receipt_image_upload($id,$project_id,'installment');
                    $success = true;
                    $message = _l('added_successfully', _l('installment'));
                }
                $totalpaid = $this->projects_model->get_installment_totalpaid($project_id);
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'has_primary_contact'=>(total_rows('tblcontacts', array('userid'=>$project_id, 'is_primary'=>1)) > 0 ? true : false),
                    'is_individual'=>is_empty_customer_company($project_id) && total_rows('tblcontacts',array('userid'=>$project_id)) == 1,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            } else {
                if (!has_permission('projects', '', 'edit')) {
                    
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;

                }
                $original_contact = $this->projects_model->get_installment($contact_id);
                $success          = $this->projects_model->update_installment($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                 handle_installment_receipt_image_upload($contact_id,$project_id,'installment');
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('installment'));
                }
                
                $totalpaid = $this->projects_model->get_installment_totalpaid($project_id);
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email,
                    'has_primary_contact'=>true,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            }
			  
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('installment_lowercase'));
        } else {
            $data['contact'] = $this->projects_model->get_installment($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = _l('add_new', _l('installment_receipt'));
        }

       // $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/projects/modals/installment', $data);
    }
	function reset_installments($project_id){


        $this->db->where('recovery_id',$project_id);
        $this->db->where('recovery_type','project_recovery');
        $this->db->where('installment_status','not_paid');
        $this->db->delete('tblrecoveries_installments');
        
		$edt=to_sql_date($this->input->post('execution_duedate'),true);


        $settlement_data = $this->input->post();
        $settlement_data['installment_start_date']=to_sql_date($this->input->post('installment_start_date'));
		 $settlement_data['installment_enddate']=to_sql_date($this->input->post('installment_enddate'));
        $settlement_data['execution_duedate']=to_sql_date($this->input->post('execution_duedate'));
        unset($settlement_data['projectid']);
        //$this->db->where('id',$project_id);
        //$this->db->update('tblprojects',$settlement_data);

        $this->casediary_model->update_project_table_data($settlement_data,$project_id);

        $credit_limit = $this->db->get_where('tblprojects',array('id'=>$project_id))->row()->outstanding_amount;
        $totalpaid = 0;
        $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblrecoveries_installments` WHERE recovery_id = ? AND installment_status = ? AND recovery_type = ?',array($project_id,'paid','project_recovery'))->row();
        if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
        }

        $output = trim(str_replace(',', '', $credit_limit));
        $output = $output - $totalpaid;
        $installment_amount = round($output / $settlement_data['no_of_installment']);
        $installment_start_date = date('Y-m-d',strtotime($settlement_data['installment_start_date']));
        for ($i=1; $i <= $settlement_data['no_of_installment']; $i++) { 
                     
         $installment['installment_date']  =  $installment_start_date;
         $installment['installment_amount']  = $installment_amount;
         $installment['installment_status']  = 'not_paid';
         $this->projects_model->add_installment($installment,$project_id);

         $installment_start_date = date('Y-m-d',strtotime("+1 months",strtotime($installment['installment_date'])));

        } 
        return true;
        //redirect(admin_url('projects/view/' . $project_id . '?group=project_settlement'));

    }
	
	 public function delete_installment($customer_id, $id)
    {
        if (!has_permission('projects', '', 'delete')) {
            if (!is_recovery_admin($customer_id)) {
                access_denied('projects');
            }
        }

        $this->projects_model->delete_installment($id);
        redirect(admin_url('projects/view/' . $customer_id . '?group=project_settlement'));
    }
	 public function verify_installment($id, $status){
        if ($this->input->is_ajax_request()) {
            $this->projects_model->verify_installment($id, $status);
        }
    }
    /*======================Payment-schedule=====================*/
		 public function payinstallments($client_id)
    {
        $this->app->get_table_data('my_payment_schedules', array(
            'client_id' => $client_id,
        ));
    }

	 public function payinstallment($project_id, $contact_id = '')
    {
        if (!has_permission('projects', '', 'view')) {
            if (!is_recovery_admin($project_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['project_id'] = $project_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();

            unset($data['contactid']);
            if ($contact_id == '') {
               /* if (!has_permission('projects', '', 'create')) {
                    if (!is_recovery_admin($project_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    }
                }*/
                $id      = $this->projects_model->add_payinstallment($data, $project_id);
                $message = '';
                $success = false;
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('installment'));
                }
                $totalpaid = $this->projects_model->get_payinstallment_totalpaid($project_id);
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'has_primary_contact'=>(total_rows('tblcontacts', array('userid'=>$project_id, 'is_primary'=>1)) > 0 ? true : false),
                    'is_individual'=>is_empty_customer_company($project_id) && total_rows('tblcontacts',array('userid'=>$project_id)) == 1,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            } else {
                if (!has_permission('projects', '', 'edit')) {
                    
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;

                }
                $original_contact = $this->projects_model->get_payinstallment($contact_id);
                $success          = $this->projects_model->update_payinstallment($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('installment'));
                }
                
                $totalpaid = $this->projects_model->get_payinstallment_totalpaid($project_id);
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email,
                    'has_primary_contact'=>true,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            }
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('installment_lowercase'));
        } else {
            $data['contact'] = $this->projects_model->get_payinstallment($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = _l('edit', _l('installment_lowercase'));
        }

       // $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/projects/modals/payinstallment', $data);
    }
	function reset_payinstallments($out_amount,$defaulterID){
        
        $this->db->where('project_id',$defaulterID);
        $this->db->where('project_type','project');
        $this->db->where('installment_status','not_paid');
        $this->db->delete('tblpayment_schedule');
        

        $this->db->where('id',$defaulterID);
        $this->db->update('tblprojects',array('claiming_amount'=>$out_amount));
        $credit_limit = $this->db->get_where('tblprojects',array('id'=>$defaulterID))->row()->claiming_amount;
        $totalpaid = 0;
        $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblpayment_schedule` WHERE project_id = ? AND installment_status = ? AND project_type = ?',array($defaulterID,'paid','project'))->row();
        if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
        }

    /*    $output = trim(str_replace(',', '', $credit_limit));
        $output = $output - $totalpaid;
        $installment_amount = round($output / $number_of_installments);
        for ($i=1; $i <= $number_of_installments; $i++) { 
                     
         $installment['installment_date']  =  $installment_start_date;
         $installment['installment_amount']  = $installment_amount;
         $installment['installment_status']  = 'not_paid';

         $installment_start_date = date('Y-m-d',strtotime("+1 months",strtotime($installment_start_date)));
         $this->projects_model->add_installment($installment,$defaulterID);

        } */ 
        return false;

    }
	 public function delete_payinstallment($customer_id, $id)
    {
        if (!has_permission('projects', '', 'delete')) {
            if (!is_recovery_admin($customer_id)) {
                access_denied('projects');
            }
        }

        $this->projects_model->delete_payinstallment($id);
        redirect(admin_url('projects/view/' . $customer_id . '?group=payment_schedule'));
    }
	 public function verify_payinstallment($id, $status){
        if ($this->input->is_ajax_request()) {
            $this->projects_model->verify_payinstallment($id, $status);
        }
    }
	     /* Add or update lead */
    public function court_instance($id = '')
    {
        /*if (!is_staff_member() || ($id != '' && !$this->leads_model->staff_can_access_lead($id))) {
            ajax_access_denied();
        }*/

        if ($this->input->post()) {
            $hearing_type  =    $this->input->post('details_type'); 
            if ($id == '') {
                $id = $this->casediary_model->add_case_details_data($this->input->post());
                $message = $id ? _l('added_successfully', _l('court_instance')) : '';

                echo json_encode([
                    'success'  => $id ? true : false,
                    'id'       => $id,
                    'message'  => $message,
                    'leadView' => $id ? $this->_get_court_instance_data($id) : [],
                    'hearing_type'=>$hearing_type,
                ]);
            } else {
                
                $message         = '';
                $success = $this->casediary_model->update_case_details_data($this->input->post(), $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('court_instance'));
                }
                echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
                    'leadView'         => $this->_get_court_instance_data($id),
                    'hearing_type'     =>$hearing_type,
                ]);
            }
            die;
        }

        echo json_encode([
            'leadView' => $this->_get_court_instance_data($id),
        ]);
    }

     private function _get_court_instance_data($id = '')
    {
        $reminder_data         = '';
        if (is_numeric($id)) {
            $hearing = $this->casediary_model->get_project_instances_by_id($id);
            if (!$hearing) {
                header('HTTP/1.0 404 Not Found');
                echo _l('not_found');
                die;
            }
            $data['court_instance'] = $hearing;   
        }
        if ($this->input->get('hearing_type')) {
            $hearing_type =  $this->input->get('hearing_type');
        }
        $data['hearing_types']     =  get_hearing_types();
        $data['arr_hearinig_references']  = $this->hearing_model->get_hearinig_references();
        $data['arr_court_regions']= $this->hearing_model->get_court_regions();
        $data['hallnumber_types'] = $this->hearing_model->get_hallnumbers();
        $data['lawyer_staffs'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);
        $data['courts']      = $this->casediary_model->get_courts();
        $data['case_natures']      = $this->casediary_model->get_casenatures();
        $data['proejct_instances'] = get_project_instances();
		//$data['lastclaim']=get_instance_claim($id);

        return [
            'data'          => $this->load->view('admin/projects/project_court_instance', $data, true),
            //'reminder_data' => $reminder_data,
        ];
    }
	public function update_settlements_table($project_id){
        if ($this->input->is_ajax_request()) {
            $data = $this->input->post();
            $table_id_array = $data['id'];
            $installment_amount = $data['installment_amount'];
            $amount_received = $data['amount_received'];
            $installment_date = $data['installment_date'];
            $installment_status = $data['installment_status'];
            $installment_remarks = $data['remarks'];
            for($i=0;$i<sizeof($table_id_array);$i++){
                $insert['installment_amount'] = $installment_amount[$i];
                $insert['installment_date']   = to_sql_date($installment_date[$i],false);
                $insert['installment_status'] = $installment_status[$i];
                $insert['remarks'] = $installment_remarks[$i];
                $insert['amount_received'] = $amount_received[$i];
                //print_r($insert);
                //echo $table_id_array[$i];
                $this->projects_model->update_installment($insert,$table_id_array[$i] );
            }
        }
    }
	public function bulk_assign()
    {
        hooks()->do_action('before_do_bulk_action_for_project_files');
        $total_assigned       = 0;
        if ($this->input->post()) {
           $assigned_user  = $this->input->post('assigned_user');
           $cases = $this->input->post('ids');
           foreach ($cases as  $case) {
                $project_members_in = $this->projects_model->get_project_members($case);
                $existing_members = [];
                if (sizeof($project_members_in) > 0) {
                    $existing_members = array_column($project_members_in,'staff_id');
                    if (!in_array($assigned_user,$existing_members)) {
                        $this->db->insert(db_prefix() . 'project_members', [
                            'project_id' => $case,
                            'staff_id'   => $assigned_user,
                        ]);
                        $this->projects_model->log_activity($case, 'project_activity_added_team_member', get_staff_full_name($assigned_user));
                        $total_assigned++;
                    }else{
                        continue;
                    }
                }
           }
        }
        
        set_alert('success', _l('total_files_assigned', $total_assigned));
        
    }
	public function all_cases($work_type='')
    {
        //close_setup_menu();
        $data['statuses']  = $this->projects_model->get_project_statuses();
        $data['title']     = _l('projects');
        $data['case_type'] = 'other_projects';
        $data['staff']    = $this->staff_model->get('', ['active' => 1]);
        $data['work_type']    = $work_type;
        $this->load->view('admin/projects/all_cases', $data);
    }
	public function all_cases_table($clientid = '',$opposite_party='')
    {
        $this->app->get_table_data('my_all_cases', [
            'clientid' => $clientid,
            'opposite_party'=> $opposite_party
        ]);
    }
	public function abscound_writeoff_case($project_id,$field, $status){
        if ($this->input->is_ajax_request()) {
            $this->projects_model->change_abscound_writeoff_case_status($project_id,$field,$status);
        }
 }
  /* List all ticket services */
  public function mattertypes()
  {
      if (!is_admin()) {
          access_denied('Matter Types');
      }
      if ($this->input->is_ajax_request()) {
          $aColumns = [
              'id','type',
          ];
          $sIndexColumn = 'type_id';
          $sTable       = db_prefix().'project_types';
          $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
              'type_id',
          ]);
          $output  = $result['output'];
          $rResult = $result['rResult'];
          foreach ($rResult as $aRow) {
              $row = [];
              for ($i = 0; $i < count($aColumns); $i++) {
                  $_data = $aRow[$aColumns[$i]];
                  if ($aColumns[$i] == 'id') {
                      $_data = '<a href="#" onclick="edit_service(this,' . $aRow['type_id'] . ');return false" data-name="' . $aRow['id'] . '" data-type="' . $aRow['type'] . '">' . $_data . '</a>';
                  }
                  $row[] = $_data;
              }
              $options = icon_btn('#', 'pencil-square-o', 'btn-default', [
                  'data-id' => $aRow['id'],
                  'data-type' => $aRow['type'],
                  'onclick'   => 'edit_service(this,' . $aRow['type_id'] . '); return false;',
              ]);
              
             $options .= icon_btn('projects/delete_mattertype/' . $aRow['type_id'], 'remove', 'btn-danger _delete');
              
              $row[]              = $options;
              $output['aaData'][] = $row;
          }
          echo json_encode($output);
          die();
      }
      
      $data['title'] = _l('mattertypes');
      $this->load->view('admin/projects/casetypes/manage', $data);
  }

  /* Add new service od delete existing one */
  public function mattertype($id = '')
  {
      if (!is_admin()) {
          access_denied('Matter Types');
      }

      if ($this->input->post()) {
          $post_data = $this->input->post();
          $post_data['id']=str_replace(' ','_',strtolower($this->input->post('id')));
              $name=str_replace(' ','_',strtolower($this->input->post('id')));
              $post_data['name']="_l('$name')";
            //  $post_data['name']="_l('$name')";
            //   print_r($post_data['name']);
          if (!$this->input->post('type_id')) {
            
              $id = $this->projects_model->add_mattertype($post_data);
              if (!$requestFromTicketArea) {
                  if ($id) {
                      set_alert('success', _l('added_successfully', _l('mattertype')));
                  }
              } else {
                  echo json_encode(['success' => $id ? true : false, 'type_id' => $id, 'name' => $post_data['type_id']]);
              }
          } else {
              $id = $post_data['type_id'];
              unset($post_data['type_id']);
              $success = $this->projects_model->update_mattertype($post_data, $id);
              if ($success) {
                  set_alert('success', _l('updated_successfully', _l('mattertype')));
              }
          }
          die;
      }
  }

  /* Delete ticket service from database */
  public function delete_mattertype($id)
  {
      if (!is_admin()) {
          access_denied('Matter Types');
      }
      if (!$id) {
          redirect(admin_url('projects/mattertype'));
      }
      $response = $this->projects_model->delete_mattertype($id);
      if (is_array($response) && isset($response['referenced'])) {
          set_alert('warning', _l('is_referenced', _l('mattertype_lowercase')));
      } elseif ($response == true) {
          set_alert('success', _l('deleted', _l('mattertype')));
      } else {
          set_alert('warning', _l('problem_deleting', _l('mattertype_lowercase')));
      }
      redirect(admin_url('projects/mattertypes'));
  }

  public function judgement_table($project_id)
    {
        if ($this->projects_model->is_member($project_id) || staff_can('view', 'projects')) {
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('project_judgements', [
                    'project_id' => $project_id,
                ]);
            }
        }
    }

     /* Add or update hearing_judgement */

     public function hearing_judgement($id = '')

     {
         if ($this->input->post()) {
             $project_id    =  $this->input->post('project_id');
			  $stageid   =  $this->input->post('stage_id');
             $data=$this->input->post();
             unset($data['judge_attachment']);
             if ($id == '') {
		 		$this->db->select('id');
	    $this->db->where('project_id', $project_id);
		$this->db->where('h_instance_id',$stageid);
		$this->db->where('DATE(hearing_date) <=',date('Y-m-d'));
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $data['stage_hearing_id'] = $this->db->get(db_prefix() . 'hearings')->row()->id;
                 $id = $this->casediary_model->add_hearing_judgement($data);
                 $update=handle_project_judgement_file_upload($id,$project_id);
                 $message = $id ? _l('added_successfully', _l('judgement_ruling')) : '';
 
                 echo json_encode([
 
                     'success'  => $id ? true : false,
 
                     'id'       => $id,
 
                     'message'  => $message,
 
                    'leadView' => $id ? $this->_get_hearing_judgement_data($id) : [],
 
                 ]);
 
             } else {
 
                 
 
                 $message         = '';
 
                 $success = $this->casediary_model->update_hearing_judgement($this->input->post(), $id);
                 $update=handle_project_judgement_file_upload($id,$project_id);
                 if ($success|| $update) {
 
                     $message = _l('updated_successfully', _l('judgement_ruling'));
 
                 }
 
                 echo json_encode([
 
                     'success'          => $success,
 
                     'message'          => $message,
 
                     'id'               => $id,
 
                     'leadView'         => $this->_get_hearing_judgement_data($id),
 
                 ]);
 
             }
 
             die;
 
         }
 
 
 
         echo json_encode([
 
             'leadView' => $this->_get_hearing_judgement_data($id),
 
         ]);
 
     }
 
 
 
      private function _get_hearing_judgement_data($id = '')
 
     {
 
         $reminder_data         = '';
 
         $data = [];
 
         if (is_numeric($id)) {
 
             $hearing =  $this->db->get_where('tblproject_judgement',array('id'=>$id))->row();
 
             if (!$hearing) {
 
                 header('HTTP/1.0 404 Not Found');
 
                 echo _l('not_found');
 
                 die;
 
             }
 
             $data['hearing_judgement'] = $hearing;   
 
         }
 
         if ($this->input->get('hearing_type')) {
 
             $hearing_type =  $this->input->get('hearing_type');
 
         }
         $data['proejct_instances'] = get_project_instances();
         $data['judgement_statuses']=get_judgement_status();
         $data['order_statuses']=get_decree_order_status();
 
         return [
 
             'data'          => $this->load->view('admin/projects/hearing_judgement', $data, true),
 
             //'reminder_data' => $reminder_data,
 
         ];
 
     }
     public function delete_hearing_judgement($project_id, $id)
    {
        if (has_permission('projects', '', 'delete')) {
            if ($this->casediary_model->delete_judgement($id)) {
                  set_alert('success', _l('deleted',_l('judgement_ruling')));
            }
        }
        redirect(admin_url('projects/view/' . $project_id . '?group=project_judgment'));
    }
		 /* Add or update project POA Details */
    public function project_subfile_gag($id = '')
    {
       		
        if ($this->input->post()) {
         $project_id=$this->input->post('project_id');
            if ($id == '') {
				$updated=false;
				$vcount=1;
				$ctype=$this->input->post('document_type');
				$data=$this->input->post();
				$doccount=$this->db->get_where('tblproject_versionfiles',array('project_id'=>$project_id,'document_type'=>$ctype))->num_rows();
				if($doccount>0)
					$vcount=$doccount+1;
				$project_data   = $this->db->get_where('tblprojects',array('id'=>$project_id))->row();
				$matter_refno=$project_data->file_no;
				$psubcategory= $this->db->get_where('tbldocument_types',array('id'=>$ctype))->row()->name;
				$filedt=date('ymd',strtotime(to_sql_date($this->input->post('subfiling_date'))));
				$matter_subrefno=$matter_refno.'##'.strtok($psubcategory,' ').'-'.$filedt.'-'.$vcount;
				$data['matter_subrefno']=$matter_subrefno;
                $id = $this->projects_model->add_internal_file($data);
				$message = $id ? _l('added_successfully', _l('customer_attachments1')) : '';
				if (!empty($_FILES['poa_attachment']['name'])) {
				$success=handle_project_subfile_file_upload($id,$project_id);
				 if ($success == true) {
                
					  $updated          = true;
					$userfile1= $this->db->get_where('tblproject_versionfiles',array('id'=>$id))->row()->file_name;
					$contentfile=$this->projectvesioncontents($userfile1,$project_id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'project_versionfiles', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );
					 }else{
					 
					  $message = 'Chaeck Image Image extension not allowed';
					  $updated          = false;
				 }
				}
					 $this->db->where( 'id', $project_id );
					 $this->db->update( db_prefix() . 'projects', [
					 	'matter_subrefno_last' => $matter_subrefno,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );
					$datau['rel_type']='project';
					$datau['content']='Matter Subfiling added with matter reference no:'.$matter_subrefno;
					//$this->casediary_model->save_case_update($datau, $project_id);
					echo json_encode([
                    'success'  => $updated,
                    'id'       => $id,
                    'message'  => $message,
					'url'       => admin_url('projects/view/' . $project_id . '?group=project_subfiles'),
                   
                ]);
				 
            }
            die;
        }

        echo json_encode([
          'url'       => admin_url('projects/view/' . $project_id . '?group=project_subfiles'),
        ]);
    }
	public function versionfile($id, $project_id)
    {
        $data['discussion_user_profile_image_url'] = staff_profile_image_url(get_staff_user_id());
        $data['current_user_is_admin']             = is_admin();

       $data['file'] = $this->projects_model->get_subfile($id, $project_id);
       $data['document_types']     = $this->casediary_model->get_document_typesproject();

        if (!$data['file']) {
            header('HTTP/1.0 404 Not Found');
            die;
        }

        $this->load->view('admin/projects/_subversionfile', $data);
    }
	public function update_subfile_data()
    {
        if ($this->input->post()) {
            $this->projects_model->update_subfile_data($this->input->post());
        }
    }
	 public function remove_subfile($project_id, $id)
    {
        $this->projects_model->remove_subfile($id);
        redirect(admin_url('projects/view/' . $project_id . '?group=project_subfiles'));
    }
   public function change_project_subfile($id=''){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			  $id=$this->input->post('id');
			  $project_id   = $data['projectid'];
              unset($data['projectid']);
			 
             $message         = '';
           $success=handle_project_subfile_file_upload($id,$project_id);
				 if ($success == true) {
                $message = $id ? _l('updated_successfully', _l('customer_attachments1')) : '';
					  $updated          = true;
					 $userfile1= $this->db->get_where('tblproject_files',array('id'=>$id))->row()->file_name;
					/*$contentfile=$this->projectvesioncontents($userfile1,$project_id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'project_versionfiles', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );*/
				 }else{
					 
					  $message = 'Check Image Image extension not allowed';
					  $updated          = false;
				 }
                if ($success) {
					$message= _l('contract_latest_uploaded');
					
				}
			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}
	public function projectvesioncontents($userfile1,$project_id)
	{
		//require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
	    require_once  APPPATH . '/vendor/smalot/pdfparser/alt_autoload.php-dist';
		$contentfile='';
		$userfile= get_upload_path_by_type('project') . $project_id . '/'.$userfile1;
		$filename_without_ext = pathinfo($userfile1, PATHINFO_FILENAME);
		$newhtmlfilepath=get_upload_path_by_type('project') . $project_id . '/'.$filename_without_ext.'.html';
		$extension = pathinfo ($userfile1 , PATHINFO_EXTENSION);
		  if($extension=='doc' || $extension=='docx'){
        $phpWord1 = \PhpOffice\PhpWord\IOFactory::load($userfile);
        $properties = $phpWord1->getDocInfo();
        $properties->setTitle($filename_without_ext);
        $htmlWriter = new \PhpOffice\PhpWord\Writer\HTML($phpWord1);
        $htmlWriter->save($newhtmlfilepath);
        }
		if($extension=='doc'){
			 $phpWord = \PhpOffice\PhpWord\IOFactory::createReader('MsDoc')->load($userfile);

			  foreach($phpWord->getSections() as $section) {
				 foreach($section->getElements() as $element) {
						if(method_exists($element,'getText')) {
                  //  echo($element->getText(). "<br>");
					$contentfile .=$element->getText() . "<br>";
                }
            }
        }
		}else if($extension=='docx'){
			
						  $phpWord = \PhpOffice\PhpWord\IOFactory::createReader('Word2007')->load($userfile);
					  foreach($phpWord->getSections() as $section) {
        foreach($section->getElements() as $element) {
            if (method_exists($element, 'getElements')) {
                foreach($element->getElements() as $childElement) {
                    if (method_exists($childElement, 'getText')) {
                        $contentfile .= $childElement->getText() . ' ';
                    }
                    else if (method_exists($childElement, 'getContent')) {
                        $contentfile .= $childElement->getContent() . ' ';
                    }
                }
            }
            else if (method_exists($element, 'getText')) {
               // $contentfile .= $element->getText() . ' ';
            }
        }
					  }
					 }
					 else if($extension=='pdf'){
						 $parser = new \Smalot\PdfParser\Parser();
						 $pdf = $parser->parseFile($userfile);

						 $content1 = $pdf->getText();
                         $contentfile= strip_tags(preg_replace('/[^A-Za-z0-9 ]/', '',$content1));
					 }
		return $contentfile;
	}
	public function get_matters_of_client($clientid)

    {

        if ($this->input->is_ajax_request()) {

            echo json_encode($this->projects_model->get_matters_of_client($clientid));

        }

    }
	public function tablesubmatter($related_matter = '',$clientid='',$opposite_party='')
    {
        $this->app->get_table_data('my_projects_submatter', [
            'clientid' => $clientid,
            'related_matter'=> $related_matter,
			'opposite_party'=> $opposite_party
        ]);
    }
	 public function hearings_overview_tables($projectID='',$type='',$clientid='')
    {
		$this->app->get_table_data('hearings_overview_tables', array(
            'project_id' => $projectID ,'hearing_instance'=>$type,'clientid'=>$clientid
        ));
		
    }
		 /* Add or update project POA Details */
    public function project_subfile($id = '')
    {
       		
        if ($this->input->post()) {
         $project_id=$this->input->post('project_id');
            if ($id == '') {
				$updated=false;
				$vcount=1;
				$ctype=$this->input->post('document_type');
				$data=$this->input->post();				/*$doccount=$this->db->get_where('tblproject_versionfiles',array('project_id'=>$project_id,'document_type'=>$ctype))->num_rows();
				if($doccount>0)
					$vcount=$doccount+1;
				$project_data   = $this->db->get_where('tblprojects',array('id'=>$project_id))->row();
				$matter_refno=$project_data->file_no;
				$psubcategory= $this->db->get_where('tbldocument_types',array('id'=>$ctype))->row()->name;
				$filedt=date('ymd',strtotime(to_sql_date($this->input->post('subfiling_date'))));
				$matter_subrefno=$matter_refno.'##'.strtok($psubcategory,' ').'-'.$filedt.'-'.$vcount;
				$data['matter_subrefno']=$matter_subrefno;*/
                $id = $this->projects_model->add_internal_file($data);
				$message = $id ? _l('added_successfully', _l('customer_attachments1')) : '';
				if (!empty($_FILES['pop_attachment']['name'])) {
				$success=handle_project_subfile_file_upload($id,$project_id);
				 if ($success == true) {
                
					  $updated          = true;
				/*	$userfile1= $this->db->get_where('tblproject_versionfiles',array('id'=>$id))->row()->file_name;
					$contentfile=$this->projectvesioncontents($userfile1,$project_id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'project_versionfiles', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );*/
					 }else{
					 
					  $message = 'Check Image Image extension not allowed';
					  $updated          = false;
				 }
				}
					/* $this->db->where( 'id', $project_id );
					 $this->db->update( db_prefix() . 'projects', [
					 	'matter_subrefno_last' => $matter_subrefno,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );*/
					$datau['rel_type']='project';
					//$datau['content']='Matter Subfiling added with matter reference no:'.$matter_subrefno;
					//$this->casediary_model->save_case_update($datau, $project_id);
					echo json_encode([
                    'success'  => $updated,
                    'id'       => $id,
                    'message'  => $message,
					'url'       => admin_url('projects/view/' . $project_id . '?group=project_subfiles'),
                   
                ]);
				 
            }
            die;
        }

        echo json_encode([
          'url'       => admin_url('projects/view/' . $project_id . '?group=project_subfiles'),
        ]);
    }
 /* Add or update project budget Details */
    public function project_budget($id = '')
    {
      	
        if ($this->input->post()) {
         $project_id=$this->input->post('project_id');
         $id=$this->input->post('id');
			
			$success='';
			if (!$this->input->post('id')) {
          		$data=$this->input->post();
                $id = $this->projects_model->add_project_budget($data);
				 if ($id) {
                  $message = $id ? _l('added_successfully', _l('project_budget')) : '';
					  $success          = true;
					
					 }
				
					echo json_encode([
                    'success'  => $success,
                    'id'       => $id,
                    'message'  => $message,
					'url'       => admin_url('projects/view/' . $project_id . '?group=project_expenses'),
                   
                ]);
				 
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->projects_model->update_project_budget($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('project_budget'));
                }
                echo json_encode(array(
                    'success' => $success,
                     'id'       => $id,
                    'message'  => $message,
					'url'       => admin_url('projects/view/' . $project_id . '?group=project_expenses'),
                ));
            }
            die;
        }

        echo json_encode([
          'url'       => admin_url('projects/view/' . $project_id . '?group=project_expenses'),
        ]);
    }
	
 public function changebudget_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->projects_model->change_budget_status($id, $status));
        }
    }
	public function add_budgetrequest($id=''){
		
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 
			 $id   = $data['id'];
                unset($data['id']);
			 $budget=$this->db->select('id,budget_reviewer_id,budget_approvalid')->from(db_prefix() . 'project_budget')->where('id', $id)->get()->row();
			
			 $data['budget_status']=$data['budget_status'];
			 if($data['budget_status']==1){
			 $data['budget_review_date']=date('Y-m-d h:i:s');
			 $data['budget_review_remark']=$data['budget_status_remark'];
		 	}
		 if($data['budget_status']==2){
			 $data['budget_approve_date']=date('Y-m-d h:i:s');
			 $data['budget_approve_remark']=$data['budget_status_remark'];
		 	}
			 if($data['budget_status']==3){
			$data['budget_reject_id']=get_staff_user_id();
			 $data['budget_reject_date']=date('Y-m-d h:i:s');
				if($budget->budget_reviewer_id==get_staff_user_id())
				 $data['budget_review_remark']=$data['budget_status_remark'];
				 if($budget->budget_approvalid==get_staff_user_id())
			 $data['budget_approve_remark']=$data['budget_status_remark'];
		 	}
			 unset($data['budget_status_remark']);
            
             $success         = $this->projects_model->change_budget_status($data, $id);

                if ($success) {
				
					
					$message= _l('budget_status_updated_successfully');
					
				}
			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}
	
	public function bulk_budget_status()
    {
        hooks()->do_action('before_do_bulk_action_for_project_files');
        $total_assigned       = 0;
        if ($this->input->post()) {
            $budget_status  = $this->input->post('budget_status');
		   $budget_status_remark  = $this->input->post('budget_status_remark');
		
           $cases = $this->input->post('ids');
		
           foreach ($cases as $case) {
			 $data['budget_status']=$budget_status;  
          $budget=$this->db->select('id,budget_reviewer_id,budget_approvalid')->from(db_prefix() . 'project_budget')->where('id', $case)->get()->row();
			
			 if($data['budget_status']==1){
			 $data['budget_review_date']=date('Y-m-d h:i:s');
			 $data['budget_review_remark']=$budget_status_remark;
		 	}
		 if($data['budget_status']==2){
			 $data['budget_approve_date']=date('Y-m-d h:i:s');
			 $data['budget_approve_remark']=$budget_status_remark;
		 	}
			 if($data['budget_status']==3){
				$data['budget_reject_id']=get_staff_user_id();
				$data['budget_reject_date']=date('Y-m-d h:i:s');
			if($budget->budget_reviewer_id==get_staff_user_id())
				 $data['budget_review_remark']=$budget_status_remark;
			if($budget->budget_approvalid==get_staff_user_id())
				$data['budget_approve_remark']=$budget_status_remark;
		 	}
			 unset($data['budget_status_remark']);
            
             $success         = $this->projects_model->change_budget_status($data, $case);
			    $total_assigned++;
           }
				 set_alert('success', _l('total_budget_status_changed', $total_assigned));
			
        }
        
     //  set_alert('success', _l('total_files_removed', $total_assigned));
        
    }
	public function get_clients_of_case($clientid)

    {

        if ($this->input->is_ajax_request()) {

            echo json_encode($this->projects_model->get_clients_of_case($clientid));

        }

    }
	
 public function send_matter_overview_to_email($id)
    {

        if ($this->input->post()) {
          $success    = $this->projects_model->send_matteroverview_to_email($this->input->post('email_template_custom'), $this->input->post('attach_pdf'), $this->input->post('cc'),$id);
            if ($success) {
                set_alert('success', _l('matter_sent_to_email_success'));
            } else {
                set_alert('danger', _l('matter_sent_to_email_fail'));
            }

            $project_id = $id;
            redirect(admin_url('projects/view/' . $project_id ));
        
        }
    }

}
