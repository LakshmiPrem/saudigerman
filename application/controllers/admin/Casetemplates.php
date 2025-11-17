<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Casetemplates extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('casetemplate_model');
        $this->load->model('currencies_model');
        $this->load->helper('date');
    }

    public function index()
    {
        close_setup_menu();
        $data['statuses'] = $this->casetemplate_model->get_project_statuses();
        $data['title']    = _l('casediary');
        $data['case_type'] = 'court_case';
        $this->load->view('admin/casetemplates/manage', $data);
    }

    public function table($clientid = '',$defaulter_id = '')
    {
        $this->app->get_table_data('casetemplates', array(
            'clientid' => $clientid , 'defaulter_id'=>$defaulter_id,'lawyer_id' => '', 
        ));
    }

    public function casetemplate($id = '')
    {
        if (!has_permission('projects', '', 'edit') && !has_permission('projects', '', 'create')) {
            access_denied('casetemplates');
        }
        if ($this->input->post()) {
            
            $data                = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            if ($id == '') {
                if (!has_permission('projects', '', 'create')) {
                    access_denied('casetemplates');
                }
                $id = $this->casetemplate_model->add($data);
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('matter_template_lowercase')));
                    redirect(admin_url('casetemplates/view/' . $id));
                }
            } else {
                if (!has_permission('projects', '', 'edit')) {
                    access_denied('casetemplates');
                }
                $success = $this->casetemplate_model->update($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('matter_template_lowercase')));
                }
                redirect(admin_url('casetemplates/view/' . $id));
            }
        }
        if ($id == '') {
            $title                            = _l('add_new', _l('matter_template_lowercase'));
            $data['auto_select_billing_type'] = $this->casetemplate_model->get_most_used_billing_type();
        } else {
            $data['project']         = $this->casetemplate_model->get($id);
            $title                   = _l('edit', _l('matter_template_lowercase'));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }

        $data['settings']              = $this->casetemplate_model->get_settings();
        $data['statuses']              = $this->casetemplate_model->get_project_statuses();
        $data['staff']                 = $this->staff_model->get('', 1);
        $data['case_type'] = 'court_case';
        $data['project_templates']     = $this->casetemplate_model->get_project_templates();
        $data['title'] = $title;
        $this->load->view('admin/casetemplates/project', $data);
    }

     public function get_template_details($id){
        if(is_numeric($id)){
            $data['casetemplate']         = $this->casetemplate_model->get($id);
            $data['success'] = true;
            echo json_encode($data);
        }
        
    }
    public function view($id)
    {
        if ($this->casetemplate_model->is_member($id) || has_permission('projects', '', 'view')) {
            close_setup_menu();
            $project = $this->casetemplate_model->get($id);

            if (!$project) {
                blank_page(_l('project_not_found'));
            }
            $data['statuses'] = $this->casetemplate_model->get_project_statuses();

            if (!$this->input->get('group')) {
                $view ='case_overview';
            } else {
                $view = $this->input->get('group');
            }

            $data['project']              = $project;
            $data['currency'] = $this->casetemplate_model->get_currency($id);

            $data['project_total_logged_time'] = 0;

            $data['staff']       = $this->staff_model->get('', 1);
            $percent             = $this->casetemplate_model->calc_progress($id);
            $data['bodyclass'] = '';
            if ($view == 'case_overview') {
                
                $data['project_total_days']        = round((human_to_unix($data['project']->deadline . ' 00:00') - human_to_unix($data['project']->start_date . ' 00:00')) / 3600 / 24);
                $data['project_days_left']         = $data['project_total_days'];
                $data['project_time_left_percent'] = 100;
                if ($data['project']->deadline) {
                    if (human_to_unix($data['project']->start_date . ' 00:00') < time() && human_to_unix($data['project']->deadline . ' 00:00') > time()) {
                        $data['project_days_left']         = round((human_to_unix($data['project']->deadline . ' 00:00') - time()) / 3600 / 24);
                        $data['project_time_left_percent'] = $data['project_days_left'] / $data['project_total_days'] * 100;
                    }
                    if (human_to_unix($data['project']->deadline . ' 00:00') < time()) {
                        $data['project_days_left']         = 0;
                        $data['project_time_left_percent'] = 0;
                    }
                }

                $__total_where_tasks = 'rel_type = "casetemplates" AND rel_id=' . $id;
               
                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status != 5';

                $data['tasks_not_completed'] = total_rows('tblstafftasks_templates', $where);
                $total_tasks                 = total_rows('tblstafftasks_templates', $__total_where_tasks);
                $data['total_tasks']         = $total_tasks;

                $where = ($__total_where_tasks == '' ? '' : $__total_where_tasks . ' AND ') . 'status = 5 AND rel_type="casetemplates" AND rel_id="' . $id . '"';

                $data['tasks_completed'] = total_rows('tblstafftasks_templates', $where);

                $data['tasks_not_completed_progress'] = ($total_tasks > 0 ? number_format(($data['tasks_completed'] * 100) / $total_tasks, 2) : 0);

                @$percent_circle = $percent / 100;
                $data['percent_circle'] = $percent_circle;

            }elseif ($view == 'scope') {
                $data['scopes']       = $this->casetemplate_model->get_scopes($id);
            }elseif ($view == 'document_checklists') { 
                $data['all_document_checklists']       = get_document_masters();
                //$data['sel_document_checklists']       = $this->casetemplate_model->get_sel_document_checklists($id);

            }

            $data['percent']              = $percent;

            $data['projects_assets']       = true;
            $data['circle_progress_asset'] = true;

            $other_projects = array();
            $other_projects_where = 'id !='.$id. ' and status = 2';


            $data['other_projects'] =  $this->casetemplate_model->get('', $other_projects_where);
            $data['title']       = $data['project']->name;
            $data['bodyclass']  .= 'project invoices_total_manual estimates_total_manual';
            $data['project_status'] =  get_project_status_by_id($project->status);

           

            // Unable to load the requested file: admin/projects/project_tasks#.php - FIX
            if (strpos($view, '#') !== false) {
                $view = str_replace('#', '', $view);
            }

            $view = trim($view);
            $data['view'] = $view;
            $data['group_view']            = $this->load->view('admin/casetemplates/' . $view, $data, true);

            $this->load->view('admin/casetemplates/view', $data);
        } else {
            access_denied('Project View');
        }
    }

    public function scope($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $project_id = $this->input->post('casetemplate_id');
                $id = $this->casetemplate_model->add_scope($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('scope')));
                    redirect(admin_url('casetemplates/view/' . $project_id . '?group=scope'));
                }
                
            } 
            die;
        }
    }

     public function edit_scope($id)
    {
        if ($this->input->post()) {
            $success = $this->casetemplate_model->edit_scope($this->input->post(), $id);
            echo json_encode(array(
                'success' => $success,
                'message' => _l('scope_updated_successfully'),
            ));
        }
    }

    public function delete_scope($project_id, $id)
    {
        
        if ($this->casetemplate_model->delete_scope($id)) {
            set_alert('success', _l('deleted'));
        }
        
        redirect(admin_url('casetemplates/view/' . $project_id . '?group=scope'));
    }


     public function new_document_checklist($id = '')
    {
        if (!is_admin() ) {
            access_denied('casediary');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casetemplate_model->new_document_checklist($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('document_checklist'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'id'=>$id,
                    'name'=>$this->input->post('name'),
                ));
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->casetemplate_model->update_document_checklist($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('document_checklist'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function add_to_template()
    {
        if($this->input->post()){ 
            $project_id = $this->input->post('casetemplate_id');
            $id = $this->casetemplate_model->add_to_template($this->input->post());
            if($id){
                set_alert('success', _l('success'));
                redirect(admin_url('casetemplates/view/' . $project_id . '?group=document_checklists'));
            }
        }
    }

    public function init_relation_temp_tasks($rel_id, $rel_type)
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('tasks_template_relations', array(
                'rel_id' => $rel_id,
                'rel_type' => $rel_type,
            ));
        }
    }

      /* Add new task or update existing */
    public function task_temp($id = '')
    {
        if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
            access_denied('Tasks');
        }

        $data = array();

        if ($this->input->get('start_date')) {
            $data['start_date'] = $this->input->get('start_date');
        }
        if ($this->input->post()) {
            $data                = $this->input->post();
            $data['description'] = $this->input->post('description', false);
            if ($id == '') {
                if (!has_permission('tasks', '', 'create')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('access_denied'),
                    ));
                    die;
                }
                $id      = $this->casetemplate_model->add_template_task($data);
                $_id     = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success = true;
                    $_id     = $id;
                    $message = _l('added_successfully', _l('task'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'id' => $_id,
                    'message' => $message,
                ));
            } else {
                if (!has_permission('tasks', '', 'edit')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('access_denied'),
                    ));
                    die;
                }
                $success = $this->casetemplate_model->update_template_task($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('task'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'id' => $id,
                ));
            }
            die;
        }

        $data['milestones'] = array();
        $data['checklistTemplates'] = [];
        if ($id == '') {
            $title = _l('add_new', _l('task_lowercase'));
        } else {
            $data['task'] = $this->casetemplate_model->get_temp_task($id);
            $data['milestones'] = [];
            $title = _l('edit', _l('task_lowercase')) . ' ' . $data['task']->name;
        }
        $data['project_end_date_attrs'] = array();
        $data['id']    = $id;
        $data['title'] = $title;
        $this->load->view('admin/casetemplates/task_template', $data);
    }

     public function get_matter_scopes($matter_id){
        if(is_numeric($matter_id)){
            $matter_scopes = $this->casetemplate_model->get_scopes($matter_id);
            echo json_encode($matter_scopes);
        }else{
            return false;
        }
    }

     public function delete($project_id)
    {
        if (has_permission('projects', '', 'delete')) {
            $project = $this->casetemplate_model->get($project_id);
            $success = $this->casetemplate_model->delete($project_id);
            if ($success) {
                set_alert('success', _l('deleted', _l('matter_template_lowercase')));
                redirect(admin_url('casetemplates'));
            } else {
                set_alert('warning', _l('problem_deleting', _l('matter_template_lowercase')));
                redirect(admin_url('casetemplates/view/' . $project_id));
            }
        }
    }
}