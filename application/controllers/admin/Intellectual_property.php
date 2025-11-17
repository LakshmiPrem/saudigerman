<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Intellectual_property extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('intellectual_property_model');
    }

    /* List all trade_licenses */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('intellectual_property', '', 'view') && !has_permission('intellectual_property', '', 'view_own')) {
            access_denied('trade_licenses');
        }
        $data['years']              = $this->intellectual_property_model->get_trade_license_years();
        $data['title']              = _l('intellectual_property');
        $this->load->view('admin/intellectual_property/manage', $data);
    }

    public function table($clientid = '')
    {
        if (!has_permission('intellectual_property', '', 'view') && !has_permission('intellectual_property', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('intellectual_property', array(
            'clientid' => $clientid,
        ));
    }

    /* Edit contract or add new contract */
    public function intellectual_property($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('intellectual_property', '', 'create')) {
                    access_denied('intellectual_property');
                }
                $id = $this->intellectual_property_model->add($this->input->post());
                if ($id) {
                    
                    set_alert('success', _l('added_successfully', _l('intellectual_property')));
                    redirect(admin_url('intellectual_property/intellectual_property/' . $id));
                }
            } else {
                if (!has_permission('intellectual_property', '', 'edit')) {
                    access_denied('intellectual_property');
                }
                $success = $this->intellectual_property_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('intellectual_property')));
                }
                redirect(admin_url('intellectual_property/intellectual_property/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('intellectual_property_lowercase'));
        } else {
            $data['contract']                 = $this->intellectual_property_model->get($id, array(), true);
            //$data['contract_renewal_history'] = $this->intellectual_property_model->get_contract_renewal_history($id);
            if (!$data['contract'] || (!has_permission('intellectual_property', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('intellectual_property_not_found'));
            }
           
            $title                         = _l('edit', _l('intellectual_property_lowercase'));

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
        $data['types']         = $this->intellectual_property_model->get_contract_types();
        $data['title'] = $title;
        $data['bodyclass'] = 'intellectual_property';
        $this->load->view('admin/intellectual_property/intellectual_property', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/intellectual_property/templates/' . $name, array(), true);
    }

    public function pdf($id)
    {
        if (!has_permission('intellectual_property', '', 'view') && !has_permission('intellectual_property', '', 'view_own')) {
            access_denied('intellectual_property');
        }
        if (!$id) {
            redirect(admin_url('intellectual_property'));
        }
        $contract = $this->intellectual_property_model->get($id);

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
        if (!has_permission('intellectual_property', '', 'view') && !has_permission('intellectual_property', '', 'view_own')) {
            access_denied('intellectual_property');
        }
        $success = $this->intellectual_property_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('contract_sent_to_client_success'));
        } else {
            set_alert('danger', _l('contract_sent_to_client_fail'));
        }
        redirect(admin_url('intellectual_property/contract/' . $id));
    }

    public function save_contract_data()
    {
        if (!has_permission('intellectual_property', '', 'edit') && !has_permission('intellectual_property', '', 'create')) {
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
        $this->db->update('tblintellectual_property', array(
                'content' => $this->input->post('content', false),
            ));

        if ($this->db->affected_rows() > 0) {
            $success = true;
            $message = _l('updated_successfully', _l('intellectual_property_lowercase'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        ));
    }

    public function renew()
    {
        if (!has_permission('intellectual_property', '', 'create') && !has_permission('intellectual_property', '', 'edit')) {
            access_denied('intellectual_property');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->intellectual_property_model->renew($data);
            if ($success) {
                set_alert('success', _l('contract_renewed_successfully'));
            } else {
                set_alert('warning', _l('contract_renewed_fail'));
            }
            redirect(admin_url('intellectual_property/contract/' . $data['contractid'] . '?tab=tab_renewals'));
        }
    }

    public function delete_renewal($renewal_id, $contractid)
    {
        $success = $this->intellectual_property_model->delete_renewal($renewal_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_renewal_deleted'));
        } else {
            set_alert('warning', _l('contract_renewal_delete_fail'));
        }
        redirect(admin_url('intellectual_property/contract/' . $contractid . '?tab=tab_renewals'));
    }

    public function copy($id)
    {
        if (!has_permission('intellectual_property', '', 'create')) {
            access_denied('intellectual_property');
        }
        if (!$id) {
            redirect(admin_url('intellectual_property'));
        }
        $newId = $this->intellectual_property_model->copy($id);
        if ($newId) {
            set_alert('success', _l('contract_copied_successfully'));
        } else {
            set_alert('warning', _l('contract_copied_fail'));
        }
        redirect(admin_url('intellectual_property/contract/'.$newId));
    }

    /* Delete contract from database */
    public function delete($id)
    {
        if (!has_permission('intellectual_property', '', 'delete')) {
            access_denied('intellectual_property');
        }
        if (!$id) {
            redirect(admin_url('intellectual_property'));
        }
        $response = $this->intellectual_property_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('intellectual_property_lowercase')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('intellectual_property_lowercase')));
        }
        redirect(admin_url('intellectual_property'));
    }

    /* Manage contract types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('intellectual_property');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->intellectual_property_model->add_contract_type($this->input->post());
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
                $success = $this->intellectual_property_model->update_contract_type($data, $id);
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
            access_denied('intellectual_property');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('contract_types');
        }
        $data['title'] = _l('contract_types');
        $this->load->view('admin/intellectual_property/manage_types', $data);
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
        $response = $this->intellectual_property_model->delete_contract_type($id);
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
        handle_ip_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('contract_id'), 'intellectual_property', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode(array(
                'success' => $this->intellectual_property_model->delete_contract_attachment($attachment_id),
            ));
        }
    }
}
