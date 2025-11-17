<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Templates extends AdminController
{
    /**
     * Initialize Templates controller
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('templates_model');
         $this->load->model('contracts_model');
    }

    /**
     * Get the template modal content
     *
     * @return string
     */
    public function modal()
    {
        $data['rel_type'] = $this->input->post('rel_type');

        // When modal is submitted, it returns to the proposal/contract that was being edited.
        $data['rel_id'] = $this->input->post('rel_id');

        if ($this->input->post('slug') == 'new') {
            $data['title'] = _l('add_template');
        } elseif ($this->input->post('slug') == 'edit') {
            $data['title'] = _l('edit_template');
            $data['id']    = $this->input->post('id');
            $this->authorize($data['id']);
            $data['template'] = $this->templates_model->get($data['id']);
        }
           $data['types']         = $this->contracts_model->get_contract_types();
		$data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract','{email_signature}');
        $this->load->view('admin/includes/modals/template', $data);
    }
    public function closuremodal()
    {
        $data['rel_type'] = $this->input->post('rel_type');

        // When modal is submitted, it returns to the proposal/contract that was being edited.
        $data['rel_id'] = $this->input->post('rel_id');

        if ($this->input->post('slug') == 'new') {
            $data['title'] = _l('add_closure');
        } elseif ($this->input->post('slug') == 'edit') {
            $data['title'] = _l('edit_closure');
            $data['id']    = $this->input->post('id');
            $this->authorize($data['id']);
            $data['template'] = $this->templates_model->get($data['id']);
        }
          
		$data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract','{email_signature}');
        $this->load->view('admin/includes/modals/closure', $data);
    }
    /**
     * Get template(s) data
     *
     * @param  int|null $id
     */
    public function index($id = null)
    {
        $data['rel_type'] = $this->input->post('rel_type');
        $data['rel_id']   = $this->input->post('rel_id');

        $where             = ['type' => $data['rel_type']];
        $data['templates'] = $this->templates_model->get($id, $where);

        if (is_numeric($id)) {
            $template = $this->templates_model->get($id);

            echo json_encode([
                'data' => $template,
            ]);
            die;
        }

        $this->load->view('admin/includes/templates', $data);
    }

    /**
     * Manage template
     *
     * @param  int|null $id
     *
     */
    public function template($id = null)
    {
        $content = $this->input->post('content', false);
        $content = html_purify($content);

        $data['name']      = $this->input->post('name');
        $data['content']   = $content;
        $data['addedfrom'] = get_staff_user_id();
        $data['type']      = $this->input->post('rel_type');
        $data['agreement_type']      = $this->input->post('agreement_type');
        

        // so when modal is submitted, it returns to the proposal/contract that was being edited.
        $rel_id = $this->input->post('rel_id');

        if (is_numeric($id)) {
            $this->authorize($id);
            $success = $this->templates_model->update($id, $data);
			//if ($_FILES['file']['name'][0]!='') {
			if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
			handle_template_file_upload($id);
			}
            $message = _l('template_updated');
        } else {
           $templateid= $this->templates_model->create($data);
			$success =true;
			if (isset($_FILES['file']['name']) && $_FILES['file']['name'] != '') {
			$success=handle_template_file_upload($templateid);
			}
            $message = _l('template_added');
        }

        if ($success) {
            set_alert('success', $message);
        }
		 echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		//	redirect( admin_url('utilities/all_templates'));
      /*  redirect(
            $data['type'] == 'contracts' ?
            admin_url('contracts/contract/' . $rel_id) :
            admin_url('proposals/list_proposals/' . $rel_id)
        );*/
    }
        public function closure($id = null)
    {
        $content1 = $this->input->post('content1', false);
      $content = html_purify($content1);

        $data['name']      = $this->input->post('name');
        $data['content']   = $content;
        $data['addedfrom'] = get_staff_user_id();
        $data['type']      = $this->input->post('rel_type');
      $data['is_legalclause']=1;
     if (!empty($this->input->post('general_ai'))) {
            $data['general_ai'] = 1;
      
       //$data['ai_metadata']      = $this->input->post('ai_metadata'); 
        } else {
            $data['general_ai'] = 0;
        }
     $data['ai_constraints']      = $this->input->post('ai_constraints'); 
       $data['ai_jurisdiction']      = $this->input->post('ai_jurisdiction'); 
       
        // so when modal is submitted, it returns to the proposal/contract that was being edited.
        $rel_id = $this->input->post('rel_id');

        if (is_numeric($id)) {
            $this->authorize($id);
            $success = $this->templates_model->update($id, $data);
      
            $message = _l('closure_updated');
        } else {
            
           $templateid= $this->templates_model->create($data);
      $success =true;
      
            $message = _l('closure_added');
        }

        if ($success) {
            set_alert('success', $message);
        }
    /* echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));*/
    redirect( admin_url('utilities/all_clauses'));
      /*  redirect(
            $data['type'] == 'contracts' ?
            admin_url('contracts/contract/' . $rel_id) :
            admin_url('proposals/list_proposals/' . $rel_id)
        );*/
    }
    public function closure_old($id = null)
    {
        $content1 = $this->input->post('content1', false);
      $content = html_purify($content1);

        $data['name']      = $this->input->post('name');
        $data['content']   = $content;
        $data['addedfrom'] = get_staff_user_id();
        $data['type']      = $this->input->post('rel_type');
      $data['is_legalclause']=1;
       //   print_r($data);

        // so when modal is submitted, it returns to the proposal/contract that was being edited.
        $rel_id = $this->input->post('rel_id');

        if (is_numeric($id)) {
            $this->authorize($id);
            $success = $this->templates_model->update($id, $data);
			
            $message = _l('closure_updated');
        } else {
           $templateid= $this->templates_model->create($data);
			$success =true;
			
            $message = _l('closure_added');
        }

        if ($success) {
            set_alert('success', $message);
        }
		/* echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));*/
		redirect( admin_url('utilities/all_clauses'));
      /*  redirect(
            $data['type'] == 'contracts' ?
            admin_url('contracts/contract/' . $rel_id) :
            admin_url('proposals/list_proposals/' . $rel_id)
        );*/
    }
    /**
     * Delete template by given id
     *
     * @param  int $id
     *
     * @return array
     */
    public function delete($id)
    {
        $this->authorize($id);

        $this->templates_model->delete($id);

        echo json_encode([
            'success' => true,
        ]);
    }

    /**
     * Authorize the template for update/delete
     *
     * @param  int $id
     *
     * @return void
     */
    protected function authorize($id)
    {
        $template = $this->templates_model->get($id);

        if ($template->addedfrom != get_staff_user_id() && !is_admin()) {
            ajax_access_denied();
        }
    }

  public function generate_ai_clause()
{
  $this->load->library('Ai_service');
  $aiservice=new Ai_service();
    // Load AI service (you might have a custom library to call OpenAI or local model)
    $type = $this->input->post('type');
    $jurisdiction = $this->input->post('jurisdiction');
    $constraints = $this->input->post('constraints');

    // Example: static clause (replace with AI call)
  //  $clause = "Each Party agrees to maintain confidentiality of all sensitive information for a period of three (3) years...";

    // You can replace above with a call like:
     $clause = $aiservice->generateClause($type,$jurisdiction,$constraints);

    echo json_encode([
        'status' => 'success',
        'clause' => $clause
    ]);
}
}
