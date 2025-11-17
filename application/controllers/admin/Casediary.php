<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Casediary extends AdminController
{
    public function __construct()
    {
        parent::__construct();
       
        //$this->load->helper('date');
        $this->load->model('casediary_model');
        $this->load->model('hearing_model');
       // $this->load->model('projects_model');
       // $this->load->model('currencies_model');   
     
    }

     public function edit_case_update($id)
    {
        if ($this->input->post()) { 
            $success = $this->casediary_model->edit_case_update($this->input->post(), $id);
            if ($success) { 
                //set_alert('success', _l('updated_successfully', _l('project_note')));
                echo json_encode(array(
                'success' => $success,
                'message' => _l('updated_successfully')
            ));
            }
            //redirect(admin_url('casediary/view/' . $project_id . '?group=project_notes'));
        }


    }
    public function delete_case_update($id)
    {
        if (!is_staff_member() ) {
            $this->access_denied_ajax();
        }
        echo json_encode(array(
            'success' => $this->casediary_model->delete_case_update($id),
        ));
    }

    public function save_case_update($project_id)
    {
        if ($this->input->post()) {
            $success = $this->casediary_model->save_case_update($this->input->post(null, false), $project_id);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('case_update')));
            }
            redirect(admin_url('projects/view/' . $project_id . '?group=project_updates'));
        }
    }

    public function get_casedetails_table_data_ajax($project_id, $htype)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->casediary_model->get_details('',$project_id,$htype));
        }
    }
    public function courts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_courts');
        }
        $data['title'] = _l('courts');
        $this->load->view('admin/casediary/manage_courts', $data);
    }

   

      /* Delete announcement from database */
    public function delete_court($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/courts'));
        }
        
        $response = $this->casediary_model->delete_court($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('court')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('court')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('court')));
        }
        redirect(admin_url('casediary/courts'));
    }


    public function court_types()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('court_types');
        }
        $data['title'] = _l('court_types');
        $this->load->view('admin/casediary/manage_court_types', $data);
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

  
    /* Delete announcement from database */
    public function delete_court_region($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/court_regions'));
        }
        
        $response = $this->casediary_model->delete_court_region($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('hearing_court_region')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('hearing_court_region')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('hearing_court_region')));
        }
        redirect(admin_url('casediary/court_regions'));
    }

    public function opposite_parties()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_opposite_parties');
        }
        $data['title'] = _l('opposite_parties');
        $this->load->view('admin/casediary/manage_opposite_party', $data);
    }

   

   

    /* Delete announcement from database */
    public function delete_hearing_reference($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/hearing_references'));
        }
        
        $response = $this->casediary_model->delete_hearing_reference($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('hearing_reference')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('hearing_reference')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('hearing_reference')));
        }
        redirect(admin_url('casediary/hearing_references'));
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
            $project = '';//$this->projects_model->get($this->input->get('rel_id'));
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


    public function case_nature()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_case_nature');
        }
        $data['title'] = _l('case_nature');
        $this->load->view('admin/casediary/manage_case_nature', $data);
    }

     public function newCaseNature($id = '')
    {
        if (!is_admin() ) {
            access_denied('projects');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_case_nature($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('case_nature'));
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
                $success = $this->casediary_model->update_case_nature($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('case_nature'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    } 

       /* Delete announcement from database */
    public function delete_case_nature($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/case_nature'));
        }
        
        $response = $this->casediary_model->delete_case_nature($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('case_nature')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('case_nature')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('case_nature')));
        }
        redirect(admin_url('casediary/case_nature'));
    }

      /* Delete announcement from database */
    public function delete_hall_number($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/hallnumbers'));
        }
        
        $response = $this->casediary_model->delete_hall_number($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('casediary_hallnumber')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('casediary_hallnumber')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('casediary_hallnumber')));
        }
        redirect(admin_url('casediary/hallnumbers'));
    }

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
	    public function delete_court_instance($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/court_instances'));
        }
        
        $response = $this->casediary_model->delete_courtinstances($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('court_instance')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('court_instance')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('court_instance')));
        }
        redirect(admin_url('casediary/court_instances'));
    }
    public function court_instances()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_court_instances');
        }
        $data['title'] = _l('court_instance');
        $this->load->view('admin/casediary/manage_courtinstance', $data);
    } 
    public function newCourtInstance($id = '')
    { 
        if (!is_admin() ) {
            access_denied('projects');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
			
                $id = $this->casediary_model->add_new_court_instance($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('court_instance'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'id'=>create_slug($this->input->post('name')),
                    'slug'=>create_slug($this->input->post('name')),
                    'name'=>$this->input->post('name'),
                ));
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->casediary_model->update_court_instance($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('court_instance'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
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
                $success = $this->casediary_model->update_party_type($data, $id);
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
                $success = $this->casediary_model->update_hall_number($data, $id);
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

     public function hearing_references()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_hearing_references');
        }
        $data['title'] = _l('hearing_references');
        $this->load->view('admin/casediary/manage_hearing_references', $data);
    }

     public function court_regions()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_court_regions');
        }
        $data['title'] = _l('court_region');
        $this->load->view('admin/casediary/manage_court_regions', $data);
    }

    public function hallnumbers()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_hallnumbers');
        }
        $data['title'] = _l('hallnumber');
        $this->load->view('admin/casediary/manage_hall_numbers', $data);
    }
	 public function document_types()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_document_types');
        }
		$data['category']    = $this->casediary_model->get_courtordernames();
        $data['title'] = _l('document_types');
        $this->load->view('admin/casediary/manage_document_types', $data);
    }

     public function newDocumentType($id = '')
    {
        if (!is_admin() && !is_client_admin() ) {
            access_denied('projects');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_document_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('document_type'));
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
                $success = $this->casediary_model->update_document_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('document_type'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    } 

       /* Delete announcement from database */
    public function delete_document_type($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/document_types'));
        }
        
        $response = $this->casediary_model->delete_document_type($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('document_type')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('document_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('document_type')));
        }
        redirect(admin_url('casediary/document_types'));
    }
	  public function courtorder($id = '')
    {
        if ($this->input->post()) {
            $message = '';
            $success = false;
            if (!$this->input->post('id')) {
                $project_id = $this->input->post('project_id');
                $id = $this->casediary_model->add_courtorder($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('project_courts')));
                    //redirect(admin_url('projects/view/' . $project_id . '?group=court_order'));
                echo json_encode([
                    'url'       => admin_url('projects/view/' . $project_id . '/?group=court_order'),
                    'orderid' => $id,
                ]);
                die;
            }
            echo json_encode([
                'url' => admin_url('projects/view/' .$project_id . '/?group=court_order'),
            ]);
            die;
        }
    }
}

     public function edit_courtorder($id)
    {
        if ($this->input->post()) {
            $success = $this->casediary_model->edit_courtorder($this->input->post(), $id);
            echo json_encode(array(
                'success' => $success,
                'message' => _l('updated_successfully'),
            ));
        }
    }

    public function delete_courtorder($project_id, $id)
    {
        
        if ($this->casediary_model->delete_courtorder($id)) {
            set_alert('success', _l('deleted'));
        }
        
        redirect(admin_url('projects/view/' . $project_id . '?group=court_order'));
    }
   public function change_status($stable,$id, $status){
        if ($this->input->is_ajax_request()) {
            $this->casediary_model->verify_status($id,$stable,$status);
        }
    }
	 public function verify_courtorder($id, $status){
        if ($this->input->is_ajax_request()) {
            $this->casediary_model->verify_status($id,'tblcourt_orders',$status);
        }
    }
	public function send_whatsappmesg($message,$number){
               // Find your Account SID and Auth Token at twilio.com/console
                // and set the environment variables. See http://twil.io/secure
               
                    $number = $number;
                    // $number = "918157071336";
                    $msg = $message;
                    // $msg = "test";
                    $ins = get_option('whatsapp_instance_key');
                    $api = get_option('whatsapp_api_key');
                    // $ins = "56vD9r0xZiufgbt";
                    // $api = "1d49381490844e9ee26174c5ad63a30ddd434889";



                        $url = "https://app.smartcloudapi.com/api/send-text.php";
                        $data = [
                            "number" => $number,
                            "msg" => $msg,
                            "instance" => $ins,
                            "apikey" => $api
                        ];


                        $ch = curl_init();
                        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($data));
                        curl_setopt($ch, CURLOPT_URL, $url);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        $result = curl_exec($ch);
                        curl_close($ch);
                        echo $result;
                }


                 //party types
    public function partytypes()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_partytypes');
        }
        $data['title'] = _l('casediary_partytype');
        $this->load->view('admin/casediary/manage_party_types', $data);
    }

    public function new_partytype($id = '')
    {
        if (!is_admin() && !is_client_admin() ) {
            access_denied('projects');
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
                $success = $this->casediary_model->update_party_type($data, $id);
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

    public function delete_partytype($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/partytypes'));
        }
        $response = $this->casediary_model->delete_partytype($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('casediary_partytype')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('casediary_partytype')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('casediary_partytype')));
        }
        redirect(admin_url('casediary/partytypes'));
    }
     public function contract_index() {
        $this->load->library('ChatGptApi');

        $prompt = "Sales Agreement Contract";
        $response = $this->chatgptapi->sendRequest($prompt);

       // $data['response'] = $response['choices'][0]['text'];
        print_r($response);

       // $this->load->view('chatgpt_view', $data);
    }
      public function generateText() {
		  	 $clientname='Lakshmi';
        $api_key = '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';//'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';
        $gpt_endpoint = 'https://api.openai.com/v1/engines/davinci-002/completions';

        $prompt =' Buyer (Lakshmi,1458, UAE); Seller (Beveron, 1511, Sharja);generate  sales agreement template between parties '; // Get the user's input
		 // $prompt='Subject: Payment Follow-Up\n\nDear'. $clientname.',\n\nI hope this email finds you well. I wanted to follow up regarding the pending payment for 145892 dated '.date('y-m-d').'.\n\nWe greatly appreciate your business and kindly request your attention to settle the outstanding balance at your earliest convenience.\n\nIf you have any questions or need further clarification regarding the invoice, please feel free to reach out.\n\nThank you for your prompt attention to this matter.\n\nBest regards,\n[Your Name]';
        $data = [
            'prompt' => $prompt,
            'max_tokens' => 500 // Customize based on your needs
        ];

        $headers = [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json'
        ];

        $ch = curl_init($gpt_endpoint);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        // Handle the response, e.g., display it in a view
       // $d = json_decode($response);
       // $data['response'] = json_decode($response, true);
     //  print_r($d);
		  // Get Content
$result = json_decode($response, true);
//print_r($result);
if (isset($result['choices'][0]['text'])) {
    $generatedText = $result['choices'][0]['text'];
    echo nl2br($generatedText); // Output the generated email body
} else {
    echo 'Failed to generate response.';
}
    }
	
 public function generateCompose() {
	 $clientname='Lakshmi';
$apiKey = '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';

$url = 'https://api.openai.com/v1/engines/davinci/completions';
$headers = [
    'Content-Type: application/json',
    'Authorization: Bearer ' . $apiKey,
];
// 'prompt' => 'Subject: Payment Follow-Up\n\nDear [Client Name],\n\nI hope this email finds you well. I wanted to follow up regarding the pending payment for [Invoice Number/Service Name] dated [Date].\n\nWe greatly appreciate your business and kindly request your attention to settle the outstanding balance at your earliest convenience.\n\nIf you have any questions or need further clarification regarding the invoice, please feel free to reach out.\n\nThank you for your prompt attention to this matter.\n\nBest regards,\n[Your Name]',
$data = [
    'prompt' => 'Subject: Payment Follow-Up\n\nDear'. $clientname.',\n\nI hope this email finds you well. I wanted to follow up regarding the pending payment for 145892 dated '.date('y-m-d').'.\n\nWe greatly appreciate your business and kindly request your attention to settle the outstanding balance at your earliest convenience.\n\nIf you have any questions or need further clarification regarding the invoice, please feel free to reach out.\n\nThank you for your prompt attention to this matter.\n\nBest regards,\n[Your Name]',
    'max_tokens' => 150,
    'temperature' => 0.5,
    'n' => 1,
];

$jsonData = json_encode($data);

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);

if (isset($result['choices'][0]['text'])) {
    $generatedText = $result['choices'][0]['text'];
    echo nl2br($generatedText); // Output the generated email body
} else {
    echo 'Failed to generate response.';
}
 }

 public function chat_index() {
        $this->load->library('Chatgpt');

        $prompt = "Sales Agreement Contract";
        $response = $this->chatgpt->get_chat($prompt);

       // $data['response'] = $response['choices'][0]['text'];
        print_r($response);

       // $this->load->view('chatgpt_view', $data);
    }
  

    public function gptindex() {
     $apiKey =  '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';
     $apiEndpoint = 'https://api.openai.com/v1/engines/text-davinci-002-render-sha/completions';
        // Initialize GuzzleHttp Client
        $client = new \GuzzleHttp\Client();

        // Prepare the request data
        $requestData = [
            'headers' => [
                'Authorization' => 'Bearer ' . $apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => [
                'prompt' => 'what is chatgpt',
                'max_tokens' => 50,  // Adjust as needed
            ],
        ];

        // Make the API request
        $response = $client->post($apiEndpoint, $requestData);

        // Handle the API response as needed
        $responseBody = $response->getBody();
        $responseData = json_decode($responseBody, true);

        // Do something with the response data
        echo $responseData['choices'][0]['text'];
    }
	
	public function docusign_envelop ($id=40){
		$path        = get_upload_path_by_type('contract').$id.'/';
		// Load the library
$this->load->library('DocuSign_lib');

// Use the library to create an envelope
$file = $path.'Sign20231204104756.pdf';//'path/to/your/file.pdf'; // Replace with the file path you want to send for signing
$signerName = 'lakshmi';
$signerEmail = 'lakshmi@example.com';

$result = $this->docuSign_lib->createEnvelope($file, $signerName, $signerEmail);

// Handle the result as needed

	}

public function ipcategories()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_ipcategories');
        }
        $data['title'] = _l('ip_categories');
        $this->load->view('admin/casediary/manage_ipcategory', $data);
    }

   

      /* Delete announcement from database */
    public function delete_ipcategory($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/ipcategories'));
        }
        
        $response = $this->casediary_model->delete_ipcategory($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('ip_category')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ip_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ip_category')));
        }
        redirect(admin_url('casediary/ipcategories'));
    }
	
	 public function newIpcategory($id = '')
	  {
        /*if (!is_admin() ) {
            access_denied('projects');
        }*/
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_ipcategory($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('ip_category'));
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
                $success = $this->casediary_model->update_ipcategory($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('ip_category'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }
	
public function ipsubcategories()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_ipsubcategories');
        }
        $data['title'] = _l('ip_subcategories');
        $this->load->view('admin/casediary/manage_ipsubcategory', $data);
    }

   

      /* Delete announcement from database */
    public function delete_ipsubcategory($id)
    {
        if (!$id) {
            redirect(admin_url('casediary/ipcategories'));
        }
        
        $response = $this->casediary_model->delete_ipsubcategory($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('ip_subcategory')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ip_subcategory')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ip_subcategory')));
        }
        redirect(admin_url('casediary/ipsubcategories'));
    }
	
	 public function newIpsubcategory($id = '')
	  {
        /*if (!is_admin() ) {
            access_denied('projects');
        }*/
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->casediary_model->add_new_ipsubcategory($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('ip_subcategory'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'id'=>$id,
                    'name'=>$this->input->post('subcategory_name'),
                ));
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->casediary_model->update_ipsubcategory($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('ip_subcategory'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }
public function get_ipsub_by_category_id_ajax($cateid='')
 {
         echo json_encode(get_ipsubcategories($cateid));
 }
}