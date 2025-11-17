<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Lawyers extends AdminController
{
    private $not_importable_clients_fields;
    public $pdf_zip;

    public function __construct()
    {
        parent::__construct();
        $this->not_importable_clients_fields = hooks()->apply_filters('not_importable_clients_fields',array('userid', 'id', 'is_primary', 'password', 'datecreated', 'last_ip', 'last_login', 'last_password_change', 'active', 'new_pass_key', 'new_pass_key_requested', 'leadid', 'default_currency', 'profile_image', 'default_language', 'direction', 'show_primary_contact', 'invoice_emails', 'estimate_emails', 'project_emails', 'task_emails', 'contract_emails', 'credit_note_emails','addedfrom','last_active_time'));
        // last_active_time is from Chattr plugin, causing issue
        $this->load->model('lawyers_model');
    }

    /* List all clients */
    public function index()
    {
        if (!has_permission('lawyers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('lawyers', '', 'create')) {
                access_denied('lawyers');
            }
        }

        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        //$this->load->model('documents_model');
        //$data['contract_types'] = $this->documents_model->get_contract_types();
        $data['groups']         = $this->lawyers_model->get_groups();
        $data['title']          = _l('lawyers');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        //$data['customer_admins'] = $this->lawyers_model->get_customers_admin_unique_ids();

        $whereContactsLoggedIn = '';
        if (!has_permission('lawyers', '', 'view')) {
            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM tblcustomeradmins WHERE staff_id='.get_staff_user_id().')';
        }

        $data['contacts_logged_in_today'] = $this->lawyers_model->get_contacts('', 'last_login LIKE "'.date('Y-m-d').'%"'.$whereContactsLoggedIn);

        $this->load->view('admin/lawyers/manage', $data);
    }

    public function table()
    {
        if (!has_permission('lawyers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('lawyers', '', 'create')) {
                ajax_access_denied();
            }
        }

        $this->app->get_table_data('my_lawyers');
    }

     public function dashboard_table()
    {
        if (!has_permission('lawyers', '', 'view')) {
            if (!have_assigned_customers() && !has_permission('lawyers', '', 'create')) {
                ajax_access_denied();
            }
        }

        $this->app->get_table_data('clients_table');
    }

    public function all_contacts()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('all_contacts');
        }
        $data['title'] = _l('customer_contacts');
        $this->load->view('admin/clients/all_contacts', $data);
    }

    /* Edit client or add new client*/
       public function lawyer($id = '')
    {
        if (!has_permission('lawyers', '', 'view')) {
            if ($id != '') {
                access_denied('lawyers');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('lawyers', '', 'create')) {
                    access_denied('lawyers');
                }

                $data                 = $this->input->post();
                $id = $this->lawyers_model->add($data);
                
                if ($id) {
                    handle_staff_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('lawyer')));
                    redirect(admin_url('lawyers/lawyer/' . $id));
                }
            } else {
                if (!has_permission('lawyers', '', 'edit')) {
                    if (!is_customer_admin($id)) {
                        access_denied('lawyers');
                    }
                }
                $success = $this->lawyers_model->update($this->input->post(), $id);
                handle_staff_profile_image_upload($id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('lawyer')));
                }
                redirect(admin_url('lawyers/lawyer/' . $id));
            }
        }

        if (!$this->input->get('group')) {
            $group = 'profile';
        } else {
            $group = $this->input->get('group');
        }
        // View group
        $data['group']  = $group;
        // Customer groups
        $data['groups'] = $this->lawyers_model->get_groups();

        if ($id == '') {
            $title = _l('add_new', _l('lawyer'));
        } else {
            $client = $this->lawyers_model->get($id);
            if (!$client) {
                blank_page('Client Not Found');
            }


            // Fetch data based on groups
            if ($group == 'profile') {

            } elseif ($group == 'attachments') {
                $data['attachments']   = get_all_lawyers_attachments($id);
            } elseif ($group == 'vault') {
                $data['vault_entries'] = hooks()->apply_filters('check_vault_entries_visibility', $this->lawyers_model->get_vault_entries($id));
                if ($data['vault_entries'] === -1) {
                    $data['vault_entries'] = array();
                }
            } elseif ($group == 'estimates') {
                $this->load->model('estimates_model');
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'invoices') {
                $this->load->model('invoices_model');
                $data['invoice_statuses'] = $this->invoices_model->get_statuses();
            } elseif ($group == 'credit_notes') {
                $this->load->model('credit_notes_model');
                $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);
            } elseif ($group == 'payments') {
                $this->load->model('payment_modes_model');
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_notes($id, 'lawyer');
            } elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            } elseif ($group == 'statement') {
                if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
                    set_alert('danger', _l('access_denied'));
                    redirect(admin_url('clients/client/'.$id));
                }
                $contact = $this->lawyers_model->get_contact(get_primary_contact_user_id($id));
                $email   = '';
                if ($contact) {
                    $email = $contact->email;
                }

                $template_name = 'client-statement';
                $data['template'] = get_email_template_for_sending($template_name, $email);

                $data['template_name']     = $template_name;
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
            $data['staff']           = $this->staff_model->get('', 1);

            $data['client']        = $client;
            $title                 = $client->firstname.' '.$client->lastname;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            
        }

        $data['categories'] = $this->lawyers_model->get_lawyer_categories();
		   
        $where = array('is_lawyer'=>'1');
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title'] = $title;

        $this->load->view('admin/lawyers/lawyer', $data);
	   }
   
    public function upload_attachment($id)
    {
        handle_lawyer_attachments_upload($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'customer', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('lawyers', '', 'delete') || is_customer_admin($customer_id)) {
            $this->lawyers_model->delete_attachment($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    /* Delete client */
    public function delete($id)
    {
        if (!has_permission('lawyers', '', 'delete')) {
            access_denied('lawyers');
        }
        if (!$id) {
            redirect(admin_url('lawyers'));
        }
        $response = $this->lawyers_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('lawyer_delete_transactions_warning',_l('casediary')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('lawyer')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lawyer')));
        }
        redirect(admin_url('lawyers'));
    }


   
    public function statement_pdf()
    {
        $customer_id = $this->input->get('customer_id');

        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/'.$customer_id));
        }

        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['statement'] = $this->lawyers_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
        $this->load->model('expenses_model');

        $where = array('clientid'=>$customer_id);
        $data['statement']['expenses'] = $this->expenses_model->get_expense_in_statement('',$where,to_sql_date($from),to_sql_date($to));
        $param['customer_id'] = $customer_id;
        $total_expenses = $this->expenses_model->get_expenses_total($param);

        $data['statement']['expense_total'] = $total_expenses['all']['total'];
        $data['statement']['expense_billable'] = $total_expenses['billable']['total'];
        $data['statement']['expense_non_billable'] = $total_expenses['non_billable']['total'];
        $data['statement']['expense_invoiced'] = $total_expenses['billed']['total'];
        $data['statement']['expense_unbilled'] = $total_expenses['unbilled']['total'];

        $this->load->model('payments_model');
        $data['statement']['payments'] = $this->payments_model->get_payments_in_statement($customer_id, to_sql_date($from),to_sql_date($to));

        $this->load->model('receipts_model');
        $data['statement']['receipts'] = $this->receipts_model->get_receipts_in_statement($customer_id, to_sql_date($from),to_sql_date($to));

        try {
            $pdf            = statement_pdf($data['statement']);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type           = 'D';
        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it(_l('customer_statement').'-'.$data['statement']['client']->company) . '.pdf', $type);
    }

    public function send_statement()
    {
        $customer_id = $this->input->get('customer_id');

        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            set_alert('danger', _l('access_denied'));
            redirect(admin_url('clients/client/'.$customer_id));
        }

        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $send_to = $this->input->post('send_to');
        $cc = $this->input->post('cc');

        $success = $this->lawyers_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
        // In case client use another language
        load_admin_language();
        if ($success) {
            set_alert('success', _l('statement_sent_to_client_success'));
        } else {
            set_alert('danger', _l('statement_sent_to_client_fail'));
        }

        redirect(admin_url('clients/client/' . $customer_id.'?group=statement'));
    }

    public function statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['statement'] = $this->lawyers_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));

        $data['from'] = $from;
        $data['to'] = $to;

        $viewData['html'] = $this->load->view('admin/clients/groups/_statement', $data, true);

        echo json_encode($viewData);
    }

    public function expense_in_statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['statement'] = $this->lawyers_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
        $data['from'] = $from;
        $data['to'] = $to;

        $this->load->model('expenses_model');

        $where = array('clientid'=>$customer_id);
        $data['expenses'] = $this->expenses_model->get_expense_in_statement('',$where,to_sql_date($from),to_sql_date($to));
        $param['customer_id'] = $customer_id;
        $data['total_expenses'] = $this->expenses_model->get_expenses_total($param);

        //print_r($data['total_expenses']);
        //print_r($data['expenses']);
        $viewData['html'] = $this->load->view('admin/clients/groups/_expense', $data, true);

        echo json_encode($viewData);
    }

    public function payments_in_statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['from'] = $from;
        $data['to'] = $to;

        $data['statement'] = $this->lawyers_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
        $this->load->model('payments_model');
        $data['payments'] = $this->payments_model->get_payments_in_statement($customer_id, to_sql_date($from),to_sql_date($to));

        //print_r($data['payments']);
        //print_r($data['expenses']);
        $viewData['html'] = $this->load->view('admin/clients/groups/_payments', $data, true);

        echo json_encode($viewData);
    }

    public function receipts_in_statement()
    {
        if (!has_permission('invoices', '', 'view') && !has_permission('receipts', '', 'view')) {
            header('HTTP/1.0 400 Bad error');
            echo _l('access_denied');
            die;
        }

        $customer_id = $this->input->get('customer_id');
        $from = $this->input->get('from');
        $to = $this->input->get('to');

        $data['from'] = $from;
        $data['to'] = $to;

        $data['statement'] = $this->lawyers_model->get_statement($customer_id, to_sql_date($from), to_sql_date($to));
        $this->load->model('receipts_model');
        $data['receipts'] = $this->receipts_model->get_receipts_in_statement($customer_id, to_sql_date($from),to_sql_date($to));

        //print_r($data['payments']);
        //print_r($data['expenses']);
        $viewData['html'] = $this->load->view('admin/clients/groups/_receipts', $data, true);

        echo json_encode($viewData);
    }

    public function delete_lawyer_profile_image($contact_id)
    {
        hooks()->apply_filters('before_remove_contact_profile_image');
        if (file_exists(get_upload_path_by_type('lawyer_profile_images') . $contact_id)) {
            delete_dir(get_upload_path_by_type('lawyer_profile_images') . $contact_id);
        }
        $this->db->where('lawyerid', $contact_id);
        $this->db->update('tbllawyers', array(
            'profile_image' => null,
        ));
    }

    
    public function lawyer_category($id = '')
    {
        
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->lawyers_model->add_new_lawyer_category($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('lawyer_category'));
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
                $success = $this->lawyers_model->update_lawyer_category($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('lawyer_category'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function lawyer_categories()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('lawyer_categories');
        }
        $data['title'] = _l('lawyer_categories');
        $this->load->view('admin/lawyers/manage_lawyer_category', $data);
    }


    /* Delete announcement from database */
    public function delete_lawyer_category($id)
    {
        if (!$id) {
            redirect(admin_url('lawyers/lawyer_categories'));
        }
        if (!is_admin()) {
            access_denied('lawyers');
        }
        $response = $this->lawyers_model->delete_lawyer_category($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('lawyer_category')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('lawyer_category')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('lawyer_category')));
        }
        redirect(admin_url('lawyers/lawyer_categories'));
    }

}
