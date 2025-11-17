<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Documents extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('documents_model');
        $this->load->model('oppositeparty_model');
    }
    
    /* List all contracts */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            access_denied('documents');
        }

        $data['chart_types']        = json_encode($this->documents_model->get_contracts_types_chart_data());
        //$data['chart_types_values'] = json_encode($this->documents_model->get_contracts_types_values_chart_data());
        $data['contract_types']     = $this->documents_model->get_contract_types();
        $data['years']              = $this->documents_model->get_contracts_years();
        $data['title']              = _l('documents');
        $this->load->view('admin/documents/manage', $data);
    }

    public function table($clientid = '')
    {
        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('my_documents', array(
            'clientid' => $clientid,
        ));
    }

    /* Edit contract or add new contract */
    public function document($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('documents', '', 'create')) {
                    access_denied('documents');
                }
               
                $id = $this->documents_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('safe_document')));
                    redirect(admin_url('documents/document/' . $id));
                }
            } else {
                if (!has_permission('documents', '', 'edit')) {
                    access_denied('documents');
                }
                // print_r($this->input->post());
                $success = $this->documents_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('safe_document')));
                }
                redirect(admin_url('documents/document/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('document_lowercase'));
        } else {
            
            $data['document_other_party']                 = array_column($this->documents_model->get_document_otherparty($id),'other_party_id');
            // print_r($data['document_other_party']);
            $data['contract']                 = $this->documents_model->get($id, array(), true);
            $data['contract_renewal_history'] = $this->documents_model->get_contract_renewal_history($id);
            if (!$data['contract'] || (!has_permission('documents', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('document_not_found'));
            }
			$data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract', ['other', 'client'], '{email_signature}');
          /*  $contract_merge_fields  = get_available_merge_fields();
            $_contract_merge_fields = array();
            foreach ($contract_merge_fields as $key => $val) {
                foreach ($val as $type => $f) {
                    if ($type == 'document') {
                        foreach ($f as $available) {
                            foreach ($available['available'] as $av) {
                                if ($av == 'document') {
                                    array_push($_contract_merge_fields, $f);
                                    break;
                                }
                            }
                            break;
                        }
                    } elseif ($type == 'other') {
                        array_push($_contract_merge_fields, $f);
                    } elseif ($type == 'clients') {
                        array_push($_contract_merge_fields, $f);
                    }
                }
            }
            $data['contract_merge_fields'] = $_contract_merge_fields;*/
            $title                         = _l('edit', _l('document_lowercase'));

            $contact = $this->clients_model->get_contact(get_primary_contact_user_id($data['contract']->client));
            $email   = '';
            if ($contact) {
                $email = $contact->email;
            }

            $template_name         = 'send-contract';
            $data['template']      = get_email_template_for_sending($template_name, $email);
            $data['template_name'] = $template_name;

            $this->db->where('slug', $template_name);
            $this->db->where('language', 'english');
            $template_result = $this->db->get('tblemailtemplates')->row();

            $data['template_system_name'] = $template_result->name;
            $data['template_id'] = $template_result->emailtemplateid;

            $data['template_disabled'] = false;
            if (total_rows('tblemailtemplates', array('slug'=>$data['template_name'], 'active'=>0)) > 0) {
                $data['template_disabled'] = true;
            }
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }
		$this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();
         $data['members']  = $this->staff_model->get('',['active'=>1]);
        $this->load->model('currencies_model');
        $this->load->model('erp_properties_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->documents_model->get_contract_types();
        $data['erpproperties']         = $this->erp_properties_model->get();
        $data['projectnames']         = $this->db->get_where('tblprojects',array('case_type'=>'litigation'))->result_array();
        $data['title'] = $title;
        $data['bodyclass'] = 'contract';
        $this->load->view('admin/documents/document', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/contracts/templates/' . $name, array(), true);
    }

    public function pdf($id)
    {
        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            access_denied('documents');
        }
        if (!$id) {
            redirect(admin_url('documents'));
        }
        $contract = $this->documents_model->get($id);

        try {
            $pdf      = contract_pdf($contract);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($contract->subject) . '.pdf', $type);
    }

    public function send_to_email($id)
    {
        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            access_denied('documents');
        }
        $success = $this->documents_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('document_sent_to_client_success'));
        } else {
            set_alert('danger', _l('document_sent_to_client_fail'));
        }
        redirect(admin_url('documents/document/' . $id));
    }

    public function save_contract_data()
    {
        if (!has_permission('documents', '', 'edit') && !has_permission('documents', '', 'create')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied'),
            ));
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update('tbldocuments', array(
                'content' => $this->input->post('content', false),
            ));

        if ($this->db->affected_rows() > 0) {
            $success = true;
            $message = _l('updated_successfully', _l('safe_document'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        ));
    }

    public function renew()
    {
        if (!has_permission('documents', '', 'create') && !has_permission('documents', '', 'edit')) {
            access_denied('documents');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->documents_model->renew($data);
            if ($success) {
                set_alert('success', _l('document_renewed_successfully'));
            } else {
                set_alert('warning', _l('document_renewed_fail'));
            }
            redirect(admin_url('documents/document/' . $data['contractid'] . '?tab=tab_renewals'));
        }
    }

    public function delete_renewal($renewal_id, $contractid)
    {
        $success = $this->documents_model->delete_renewal($renewal_id, $contractid);
        if ($success) {
            set_alert('success', _l('document_renewal_deleted'));
        } else {
            set_alert('warning', _l('document_renewal_delete_fail'));
        }
        redirect(admin_url('documents/document/' . $contractid . '?tab=tab_renewals'));
    }

    public function copy($id)
    {
        if (!has_permission('documents', '', 'create')) {
            access_denied('documents');
        }
        if (!$id) {
            redirect(admin_url('documents'));
        }
        $newId = $this->documents_model->copy($id);
        if ($newId) {
            set_alert('success', _l('document_copied_successfully'));
        } else {
            set_alert('warning', _l('document_copied_fail'));
        }
        redirect(admin_url('documents/document/'.$newId));
    }

     /* Delete contract from database */
    public function delete($id,$type='document')
    {
        if (!has_permission('documents', '', 'delete')) {
            access_denied('documents');
        }
        if (!$id) {
            redirect(admin_url('documents'));
        }
        $response = $this->documents_model->delete($id,$type);
        if ($response == true) {
			if ($type == 'out')
            set_alert('success', _l('deleted', _l('communication')));
			else
			  set_alert('success', _l('deleted', _l('safe_document')));	
        } else {
            set_alert('warning', _l('problem_deleting', _l('document_lowercase')));
        }
        if($type == 'in'){
            redirect(admin_url('documents/in_list'));
        }elseif ($type == 'out') {
            redirect(admin_url('documents/out_list'));
        }else{
            redirect(admin_url('documents'));
        }
        
    }


    /* Manage contract types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('documents');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->documents_model->add_contract_type($this->input->post());
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
                $success = $this->documents_model->update_contract_type($data, $id);
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

    public function types()
    {
        if (!is_admin()) {
            access_denied('documents');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('document_types');
        }
        $data['title'] = _l('document_types');
        $this->load->view('admin/documents/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url('documents/types'));
        }
        if (!is_admin()) {
            access_denied('documents');
        }
        $response = $this->documents_model->delete_contract_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('document_type_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('document_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('document_type_lowercase')));
        }
        redirect(admin_url('documents/types'));
    }

    public function add_contract_attachment($id)
    {
        handle_document_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('contract_id'), 'document', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode(array(
                'success' => $this->documents_model->delete_contract_attachment($attachment_id),
            ));
        }
    }
    

     /* List all contracts */
    public function in_list()
    {
        close_setup_menu();

        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            access_denied('documents');
        }

        $data['chart_types']        = json_encode($this->documents_model->get_in_types_chart_data());
        //$data['chart_types_values'] = json_encode($this->documents_model->get_contracts_types_values_chart_data());
        $data['contract_types']     = $this->documents_model->get_contract_types();
        $data['years']              = $this->documents_model->get_contracts_years();
        $data['title']              = _l('documents_in');
        $this->load->view('admin/documents/manage_in', $data);
    }

    public function table_in($safeid = '',$clientid='')
    {
        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('my_documents_in', array(
            'clientid' => $clientid,
			'safeid'  =>$safeid,
        ));
    }

    /* Edit contract or add new contract */
    public function document_in($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('documents', '', 'create')) {
                    access_denied('documents');
                }
                $id = $this->documents_model->add_in($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('safe_document')));
                    redirect(admin_url('documents/document_in/' . $id));
                }
            } else {
                if (!has_permission('documents', '', 'edit')) {
                    access_denied('documents');
                }
                $success = $this->documents_model->update_in($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('safe_document')));
                }
                redirect(admin_url('documents/document_in/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('request_return'));
        } else {
            $data['contract']                 = $this->documents_model->get_in($id, array(), true);
            $data['contract_renewal_history'] = $this->documents_model->get_contract_renewal_history($id);
            if (!$data['contract'] || (!has_permission('documents', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('document_not_found'));
            }
            
            $title                         = _l('edit', _l('request_return'));

            $contact = $this->clients_model->get_contact(get_primary_contact_user_id($data['contract']->client));
            $email   = '';
            if ($contact) {
                $email = $contact->email;
            }

        }

        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }
        $data['members']  = $this->staff_model->get('',['active'=>1]);
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->documents_model->get_contract_types();
		$data['safedocuments']=$this->documents_model->get();
        $data['title'] = $title;
        $data['bodyclass'] = 'contract';
        $data['tmp_type']  = 'document_in';
        $this->load->view('admin/documents/document_in', $data);
    }

     

     /* List all contracts */
    public function out_list()
    {
        close_setup_menu();

        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            access_denied('documents');
        }

        $data['chart_types']        = json_encode($this->documents_model->get_out_types_chart_data());
        //$data['chart_types_values'] = json_encode($this->documents_model->get_contracts_types_values_chart_data());
        $data['contract_types']     = $this->documents_model->get_contract_types();
        $data['years']              = $this->documents_model->get_contracts_years();
        $data['title']              = _l('documents_out');
        $this->load->view('admin/documents/manage_out', $data);
    }
    public function table_out($clientid = '')
    {
        if (!has_permission('documents', '', 'view') && !has_permission('documents', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('my_documents_out', array(
            'clientid' => $clientid,
        ));
    }


     /* Edit contract or add new contract */
    public function document_out($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('communcation', '', 'create')) {
                    access_denied('communcation');
                }
                $id = $this->documents_model->add_out($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('document')));
                    redirect(admin_url('documents/document_out/' . $id));
                }
            } else {
                if (!has_permission('communcation', '', 'edit')) {
                    access_denied('communcation');
                }
                $success = $this->documents_model->update_out($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('document')));
                }
                redirect(admin_url('documents/document_out/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('communication'));
        } else {
            $data['contract']                 = $this->documents_model->get_out($id, array(), true);
            $data['contract_renewal_history'] = $this->documents_model->get_contract_renewal_history($id);
            if (!$data['contract'] || (!has_permission('documents', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('document_not_found'));
            }
            
            $title                         = _l('edit', _l('communication'));

            $contact = $this->clients_model->get_contact(get_primary_contact_user_id($data['contract']->client));
            $email   = '';
            if ($contact) {
                $email = $contact->email;
            }

        }

        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }
        $data['members']  = $this->staff_model->get('', ['active' => 1]);
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->documents_model->get_contract_types();
        $data['title'] = $title;
        $data['bodyclass'] = 'contract';
        $data['tmp_type']  = 'document_out';
        $data['litigation']  = $this->projects_model->get("","  case_type='litigation'");
        $data['acquisition']  = $this->projects_model->get("","  case_type='acquisition'");
        $data['other_client']  =$this->oppositeparty_model->get("","  party_type='other_client'");
        $data['provider']  =$this->oppositeparty_model->get("","  party_type='provider'");
        //  print_r($data['provider']);
        $data['mode_of_msg']         =$this->documents_model->get_mode_of_msg();
        // $data['mode_of_msg']         =array("0"=>array("id"=>1,"name"=>"Email"),
        // "1"=>array('id'=>2,'name'=>'Courior'),
        // "2"=>array('id'=>3,'name'=>'Post'),
        // "3"=>array('id'=>4,'name'=>'Hand'),
        // );
        $data['related_to']         =array("0"=>array("id"=>1,"name"=>"Litigation"),
        "1"=>array('id'=>2,'name'=>'Acquisition'),
        "2"=>array('id'=>3,'name'=>'Sales'),
        "3"=>array('id'=>4,'name'=>'Serviece Providers'),
        "4"=>array('id'=>5,'name'=>'Client Parties'),
        "5"=>array('id'=>6,'name'=>'Others'),
        );
        // print_r($data['mode_of_msg']);
        $this->load->view('admin/documents/document_out', $data);
    }


    public function save_in_data($in_out_type='in')
    {
        if (!has_permission('documents', '', 'edit') && !has_permission('documents', '', 'create')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied'),
            ));
            die;
        }

        $success = false;
        $message = '';

        if($in_out_type == 'in'){
            $tbl = 'tbldocuments_in';
        }else{
            $tbl = 'tblcommunication';
        }

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update($tbl, array(
                'content' => $this->input->post('content', false),
            ));

        if ($this->db->affected_rows() > 0) {
            $success = true;
            $message = _l('updated_successfully', _l('safe_document'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        ));
    }

     public function add_document_in_attachment($id)
    {
        handle_document_in_attachment($id);
    }

    public function add_document_out_attachment($id)
    {
        handle_document_out_attachment($id);
    }
	public function approve($document_id='')
    {
        if (!staff_can('approve', 'documents')) {
            access_denied('documents');
        }
        if($this->documents_model->approve_document($document_id)){
            set_alert('success', _l('Request approved successfully'));
           redirect(admin_url('documents/document_in/' . $document_id));
        }else{
            set_alert('warning', _l('problem_approving', _l('expense')));
            redirect(admin_url('documents/document_in/' . $document_id));
        }
    }
    public function mode_of_msg()
        {
            if (!is_admin()) {
                access_denied('communication');
            }
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('mode_of_msg');
            }
            $data['title'] = _l('mode_of_msg');
            $this->load->view('admin/documents/manage_mode_of_msg', $data);
        }

         /*add contract status */
         public function mode_of_msg_manage($id = '')
         {
             if (!is_admin() ) {
                 access_denied('communication');
             }
             if ($this->input->post()) {
                 if (!$this->input->post('id')) {
                     $id = $this->documents_model->add_mode_of_msg($this->input->post());
                     if ($id) {
                         $success = true;
                         $message = _l('added_successfully', _l('mode_of_msg'));
                     }
                     echo json_encode([
                         'success' => $success,
                         'message' => $message,
                         'id'      => $id,
                         'name'    => $this->input->post('name'),
                     ]);
                 } else {
                     $data = $this->input->post();
                     $id   = $data['id'];
                     unset($data['id']);
                     $success = $this->documents_model->update_mode_of_msg($data, $id);
                     $message = '';
                     if ($success) {
                         $message = _l('updated_successfully', _l('mode_of_msg'));
                     }
                     echo json_encode([
                         'success' => $success,
                         'message' => $message,
                     ]);
                 }
             }
         }
         /* Delete status from database */
     public function delete_mode_of_msg($id)
     {
         if (!$id) {
             redirect(admin_url('documents/mode_of_msg'));
         }
         if (!is_admin()) {
             access_denied('communication');
         }
         $response = $this->documents_model->delete_mode_of_msg($id);
         if (is_array($response) && isset($response['referenced'])) {
             set_alert('warning', _l('is_referenced', _l('mode_of_msg')));
         } elseif ($response == true) {
             set_alert('success', _l('deleted', _l('mode_of_msg')));
         } else {
             set_alert('warning', _l('problem_deleting', _l('mode_of_msg')));
         }
         redirect(admin_url('documents/mode_of_msg'));
     }
}
