<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Trade_licenses extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('trade_licenses_model');
    }

    /* List all trade_licenses */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('trade_licenses', '', 'view') && !has_permission('trade_licenses', '', 'view_own')) {
            access_denied('trade_licenses');
        }
        $data['years']              = $this->trade_licenses_model->get_trade_license_years();
        $data['title']              = _l('trade_licenses');
        $this->load->view('admin/trade_licenses/manage', $data);
    }

    public function table($clientid = '')
    {
        if (!has_permission('trade_licenses', '', 'view') && !has_permission('trade_licenses', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('trade_licenses', array(
            'clientid' => $clientid,
        ));
    }

    /* Edit contract or add new contract */
    public function trade_license($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('trade_licenses', '', 'create')) {
                    access_denied('trade_licenses');
                }
                $id = $this->trade_licenses_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('trade_license')));
                    redirect(admin_url('trade_licenses/trade_license/' . $id));
                }
            } else {
                if (!has_permission('trade_licenses', '', 'edit')) {
                    access_denied('trade_licenses');
                }
                $success = $this->trade_licenses_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('trade_license')));
                }
                redirect(admin_url('trade_licenses/trade_license/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('trade_license_lowercase'));
        } else {
            $data['contract']                 = $this->trade_licenses_model->get($id, array(), true);
            //$data['contract_renewal_history'] = $this->trade_licenses_model->get_contract_renewal_history($id);
            if (!$data['contract'] || (!has_permission('trade_licenses', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('trade_license_not_found'));
            }
           
            $title                         = _l('edit', _l('trade_license_lowercase'));

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
        $data['members']  = $this->staff_model->get('', 1);

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->trade_licenses_model->get_contract_types();
        $data['title'] = $title;
        $data['bodyclass'] = 'trade_license';
        $this->load->view('admin/trade_licenses/trade_license', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/trade_licenses/templates/' . $name, array(), true);
    }

    public function pdf($id)
    {
        if (!has_permission('trade_licenses', '', 'view') && !has_permission('trade_licenses', '', 'view_own')) {
            access_denied('trade_licenses');
        }
        if (!$id) {
            redirect(admin_url('trade_licenses'));
        }
        $contract = $this->trade_licenses_model->get($id);

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
        if (!has_permission('trade_licenses', '', 'view') && !has_permission('trade_licenses', '', 'view_own')) {
            access_denied('trade_licenses');
        }
        $success = $this->trade_licenses_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('contract_sent_to_client_success'));
        } else {
            set_alert('danger', _l('contract_sent_to_client_fail'));
        }
        redirect(admin_url('trade_licenses/contract/' . $id));
    }

    public function save_contract_data()
    {
        if (!has_permission('trade_licenses', '', 'edit') && !has_permission('trade_licenses', '', 'create')) {
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
        $this->db->update('tbltrade_licenses', array(
                'content' => $this->input->post('content', false),
            ));

        if ($this->db->affected_rows() > 0) {
            $success = true;
            $message = _l('updated_successfully', _l('trade_license_lowercase'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        ));
    }

    public function renew()
    {
        if (!has_permission('trade_licenses', '', 'create') && !has_permission('trade_licenses', '', 'edit')) {
            access_denied('trade_licenses');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->trade_licenses_model->renew($data);
            if ($success) {
                set_alert('success', _l('contract_renewed_successfully'));
            } else {
                set_alert('warning', _l('contract_renewed_fail'));
            }
            redirect(admin_url('trade_licenses/contract/' . $data['contractid'] . '?tab=tab_renewals'));
        }
    }

    public function delete_renewal($renewal_id, $contractid)
    {
        $success = $this->trade_licenses_model->delete_renewal($renewal_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_renewal_deleted'));
        } else {
            set_alert('warning', _l('contract_renewal_delete_fail'));
        }
        redirect(admin_url('trade_licenses/contract/' . $contractid . '?tab=tab_renewals'));
    }

    public function copy($id)
    {
        if (!has_permission('trade_licenses', '', 'create')) {
            access_denied('trade_licenses');
        }
        if (!$id) {
            redirect(admin_url('trade_licenses'));
        }
        $newId = $this->trade_licenses_model->copy($id);
        if ($newId) {
            set_alert('success', _l('contract_copied_successfully'));
        } else {
            set_alert('warning', _l('contract_copied_fail'));
        }
        redirect(admin_url('trade_licenses/contract/'.$newId));
    }

    /* Delete contract from database */
    public function delete($id)
    {
        if (!has_permission('trade_licenses', '', 'delete')) {
            access_denied('trade_licenses');
        }
        if (!$id) {
            redirect(admin_url('trade_licenses'));
        }
        $response = $this->trade_licenses_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('trade_license_lowercase')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('trade_license_lowercase')));
        }
        redirect(admin_url('trade_licenses'));
    }

    /* Manage contract types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('trade_licenses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->trade_licenses_model->add_contract_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('contract_type'));
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
                $success = $this->trade_licenses_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('contract_type'));
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
            access_denied('trade_licenses');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('contract_types');
        }
        $data['title'] = _l('contract_types');
        $this->load->view('admin/trade_licenses/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url('trade_licenses/types'));
        }
        if (!is_admin()) {
            access_denied('trade_licenses');
        }
        $response = $this->trade_licenses_model->delete_contract_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract_type_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_type_lowercase')));
        }
        redirect(admin_url('trade_licenses/types'));
    }

    public function add_contract_attachment($id)
    {
        handle_trade_license_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('contract_id'), 'trade_license', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode(array(
                'success' => $this->trade_licenses_model->delete_contract_attachment($attachment_id),
            ));
        }
    }
}
