<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Casediary extends AdminController
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
    

   
    public function courts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('courts');
        }
        $data['title'] = _l('courts');
        $this->load->view('admin/casediary/manage_courts', $data);
    }

    /* Manage Court Since Version 1.0.3 */
    public function newCourt($id = '')
    {
        if (!is_admin() ) {
            access_denied('projects');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_court($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('court'));
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
                $success = $this->casediary_model->update_court($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('court'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

      /* Delete announcement from database */
    public function delete_court($id)
    {
        if (!$id) {
            redirect(admin_url('hearing/courts'));
        }
        
        $response = $this->hearing_model->delete_court($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('court')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('court')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('court')));
        }
        redirect(admin_url('hearing/courts'));
    }


    public function court_types()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('court_types');
        }
        $data['title'] = _l('court_types');
        $this->load->view('admin/casediary/manage_court_types', $data);
    }

       /* Manage Court type Since Version 1.0.3 */
    public function newCourtType($id = '')
    {
        if (!is_admin() ) {
            access_denied('hearings');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_CourtType($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('hearing_court_type'));
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
                $success = $this->casediary_model->update_court_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('hearing_court_type'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

     /* Delete announcement from database */
    public function delete_court_type($id)
    {
        if (!$id) {
            redirect(admin_url('hearing/court_types'));
        }
        
        $response = $this->casediary_model->delete_court_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('hearing_court_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('hearing_court_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('hearing_court_type')));
        }
        redirect(admin_url('hearing/court_types'));
    }

     /* Manage Area Descriptions Since Version 1.0.3 */
    public function newCourtDegree($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('hearings');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_court_degree($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('hearing_court_degree'));
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
                $success = $this->contracts_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('hearing_court_degree'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function court_regions()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('court_regions');
        }
        $data['title'] = _l('court_regions');
        $this->load->view('admin/hearing/manage_court_regions', $data);
    }
    

     /* Manage Hallnnumber Since Version 1.0.3 */
    public function new_court_regions($id = '')
    {
        if (!is_admin() ) {
            access_denied('hearings');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_court_regions($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('hearing_court_region'));
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
                $success = $this->casediary_model->update_court_region($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('hearing_court_region'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    /* Delete announcement from database */
    public function delete_court_region($id)
    {
        if (!$id) {
            redirect(admin_url('hearing/court_regions'));
        }
        
        $response = $this->casediary_model->delete_court_region($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('hearing_court_region')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('hearing_court_region')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('hearing_court_region')));
        }
        redirect(admin_url('hearing/court_regions'));
    }

    public function new_opposite_party($id = '')
    {
        if (!is_admin() ) {
            access_denied('casediary');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_oppositeparty($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('casediary_oppositeparty'));
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
                $success = $this->casediary_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('casediary_oppositeparty'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function new_partytypes($id = '')
    {
        if (!is_admin() ) {
            access_denied('casediary');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_partytype($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('casediary_partytype'));
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
                $success = $this->casediary_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('casediary_partytype'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function hearing_references()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('hearing_references');
        }
        $data['title'] = _l('hearing_refererences');
        $this->load->view('admin/hearing/manage_hearing_refererences', $data);
    }

    /* Manage contract types Since Version 1.0.3 */
    public function newhearing_reference($id = '')
    {
        if (!is_admin() ) {
            access_denied('hearings');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_hearing_reference($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('hearing_reference'));
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
                $success = $this->casediary_model->update_hearing_reference($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('hearing_reference'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    /* Delete announcement from database */
    public function delete_hearing_reference($id)
    {
        if (!$id) {
            redirect(admin_url('hearing/hearing_references'));
        }
        
        $response = $this->hearing_model->delete_hearing_reference($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('hearing_reference')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('hearing_reference')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('hearing_reference')));
        }
        redirect(admin_url('hearing/hearing_references'));
    }

     public function newhallnumber($id = '')
    {
        if (!is_admin() ) {
            access_denied('casediary');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_hallnumber($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('casediary_hallnumber'));
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
                $success = $this->casediary_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('casediary_hallnumber'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

   /* public function export_project_data($id)
    {
        if (staff_can('create', 'projects')) {
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

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it($hearing->subject) . '.pdf', $type);
        }
    }*/

    public function get_case_data_ajax($id, $to_return = false)
    {
        if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own') ) {
            echo _l('access_denied');
            die;
        }

        $hearing = $this->hearing_model->get($id);

        if (!$hearing ) {
            echo _l('hearing_not_found');
            die;
        }
        $data['hearing_types']     =  get_hearing_types();
        $data['hearing_preview']           = $hearing;
        if ($to_return == false) {
            $this->load->view('admin/projects/hearing_preview_template', $data);
        } else {
            return $this->load->view('admin/projects/hearing_preview_template', $data, true);
        }
    }



    public function upload_hearing_attachments($hearingid,$project_id)
    {
        handle_hearing_attachment($hearingid,$project_id);
    }


   


      /* Add new task or update existing */
    public function task_temp($id = '')
    {
        if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
            access_denied('Tasks');
        }

        $data = array();
        // FOr new task add directly from the projects milestones
        if ($this->input->get('milestone_id')) {
            $this->db->where('id', $this->input->get('milestone_id'));
            $milestone = $this->db->get('tblmilestones')->row();
            if ($milestone) {
                $data['_milestone_selected_data'] = array(
                    'id' => $milestone->id,
                    'due_date' => _d($milestone->due_date),
                );
            }
        }
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
                $id      = $this->tasks_model->add_template_task($data);
                $_id     = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success = true;
                    $_id     = $id;
                    $message = _l('added_successfully', _l('task'));
                    $uploadedFiles = handle_task_attachments_array($id);
                    if ($uploadedFiles && is_array($uploadedFiles)) {
                        foreach ($uploadedFiles as $file) {
                            $this->misc_model->add_attachment_to_database($id, 'task', array($file));
                        }
                    }
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
                $success = $this->tasks_model->update_template_task($data, $id);
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
        $data['checklistTemplates'] = $this->tasks_model->get_checklist_templates();
        if ($id == '') {
            $title = _l('add_new', _l('task_lowercase'));
        } else {
            $data['task'] = $this->tasks_model->get_temp_task($id);
            /*if ($data['task']->rel_type == 'project') {
                $data['milestones'] = $this->projects_model->get_milestones($data['task']->rel_id);
            }*/
            //if ($data['task']->rel_type == 'project') {
            $this->load->model('casetemplate_model');
            $data['milestones'] = $this->casetemplate_model->get_milestones($data['task']->rel_id);
            //}
            $title = _l('edit', _l('task_lowercase')) . ' ' . $data['task']->name;
        }
        $data['project_end_date_attrs'] = array();
        if ($this->input->get('rel_type') == 'project' && $this->input->get('rel_id')) {
            $project = $this->projects_model->get($this->input->get('rel_id'));
            if ($project->deadline) {
                $data['project_end_date_attrs'] = array(
                    'data-date-end-date' => $project->deadline,
                );
            }
        }
        $data['id']    = $id;
        $data['title'] = $title;
        $this->load->view('admin/tasks/task_template', $data);
    }

    public function get_matter_templates_by_case_type($casetype){
        if($casetype){
            $matter_templates = $this->casediary_model->get_matter_templates_by_case_type($casetype);
            echo json_encode($matter_templates);
        }else{
            return false;
        }
    }

      public function scope($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $project_id = $this->input->post('case_id');
                $id = $this->casediary_model->add_scope($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('scope')));
                    redirect(admin_url('projects/view/' . $project_id . '?group=scope'));
                }
                
            }
            die;
        }
    }

     public function edit_scope($id)
    {
        if ($this->input->post()) {
            $success = $this->casediary_model->edit_scope($this->input->post(), $id);
            echo json_encode(array(
                'success' => $success,
                'message' => _l('scope_updated_successfully'),
            ));
        }
    }

    public function delete_scope($project_id, $id)
    {
        
        if ($this->casediary_model->delete_scope($id)) {
            set_alert('success', _l('deleted'));
        }
        
        redirect(admin_url('projects/view/' . $project_id . '?group=scope'));
    }

    public function designations()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('designations');
        }
        $data['title'] = _l('designations');
        $this->load->view('admin/staff/manage_designations', $data);
    }

    
    
    /* Manage Designation Since Version 1.0.3 */
    public function designation($id = '')
    {
        if (!is_admin()) {
            access_denied('staff/designations');
        }
        $this->load->model('designations_model');

        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->designations_model->add($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('designation'));
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
                $success = $this->designations_model->update($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('designation'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }
      /* Delete announcement from database */
    public function delete_designation($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/designations'));
        }
        $this->load->model('designations_model');
        $response = $this->designations_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('designation')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('designation')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('designation')));
        }
        redirect(admin_url('casediary/designations'));
    }

   
}
?>