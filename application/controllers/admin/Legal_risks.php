<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Legal_risks extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('legalrisk_model');
    }

    /* List all trade_licenses */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('legal_risks', '', 'view') && !has_permission('legal_risks', '', 'view_own')) {
            access_denied('legal_risks');
        }
		$data['risk_statuses'] = $this->legalrisk_model->get_legalrisk_status();
		$data['risk_types'] = $this->legalrisk_model->get_legalrisk_type();
     //   $data['years']              = $this->trade_licenses_model->get_trade_license_years();
		$data['title'] = _l('legal_risks');
        $this->load->view('admin/legalrisks/manage', $data);
    }

    public function table($clientid = '')
    {
        if (!has_permission('legal_risks', '', 'view') && !has_permission('legal_risks', '', 'view_own')) {
            ajax_access_denied();
        }

       
        $this->app->get_table_data('my_legal_risks', [
            'clientid' => $clientid,
        ]);
    }

    /* Edit contract or add new contract */
    public function legal_risk($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('legal_risks', '', 'create')) {
                    access_denied('legal_risks');
                }
                $id = $this->legalrisk_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('legal_risks')));
                    redirect(admin_url('legal_risks/legal_risk/' . $id));
                }
            } else {
                if (!has_permission('legal_risks', '', 'edit')) {
                    access_denied('legal_risks');
                }
                $success = $this->legalrisk_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('legal_risks')));
                }
                redirect(admin_url('legal_risks/legal_risk/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('legalrisk'));
        } else {
            $data['contract']                 = $this->legalrisk_model->get($id, array(), true);
            //$data['contract_renewal_history'] = $this->trade_licenses_model->get_contract_renewal_history($id);
            if (!$data['contract'] || (!has_permission('legal_risks', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('legal_risks_not_found'));
            }
           
            $title                         = _l('edit', _l('legalrisk'));

           
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }
        $data['members']  = $this->staff_model->get('', ['active' => 1]);
		$data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'legalrisk']);
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['clients'] = $this->clients_model->get();
		$data['risk_types'] = $this->legalrisk_model->get_legalrisk_type();
		$data['risk_status'] = $this->legalrisk_model->get_legalrisk_status();
		$this->load->model('tickets_model');  
		$data['risk_priority']=$this->tickets_model->get_priority();
        $data['title'] = $title;
        $data['bodyclass'] = 'legalrisk';
        $this->load->view('admin/legalrisks/legalrisk', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/legal_risks/templates/' . $name, array(), true);
    }

    public function pdf($id)
    {
        if (!has_permission('legal_risks', '', 'view') && !has_permission('legal_risks', '', 'view_own')) {
            access_denied('legal_risks');
        }
        if (!$id) {
            redirect(admin_url('legal_risks'));
        }
        $contract = $this->legalrisk_model->get($id);

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



    public function save_contract_data()
    {
        if (!has_permission('legal_risks', '', 'edit') && !has_permission('legal_risks', '', 'create')) {
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
        $this->db->update('tbllegal_risk', array(
                'content' => $this->input->post('content', false),
            ));

        if ($this->db->affected_rows() > 0) {
            $success = true;
            $message = _l('updated_successfully', _l('legalrisk'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        ));
    }

    public function renew()
    {
        if (!has_permission('legal_risks', '', 'create') && !has_permission('legal_risks', '', 'edit')) {
            access_denied('legal_risks');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->legalrisk_model->renew($data);
            if ($success) {
                set_alert('success', _l('contract_renewed_successfully'));
            } else {
                set_alert('warning', _l('contract_renewed_fail'));
            }
            redirect(admin_url('legal_risks/contract/' . $data['contractid'] . '?tab=tab_renewals'));
        }
    }



    /* Delete contract from database */
    public function delete($id)
    {
        if (!has_permission('legal_risks', '', 'delete')) {
            access_denied('legal_risks');
        }
        if (!$id) {
            redirect(admin_url('legal_risks'));
        }
        $response = $this->legalrisk_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('legal_risks')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('legal_risks')));
        }
        redirect(admin_url('legal_risks'));
    }
	public function add_note($rel_id)
    {
        if ($this->input->post() && (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own'))) {
            $this->misc_model->add_note($this->input->post(), 'legalrisk', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if ((has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own'))) {
            $data['notes'] = $this->misc_model->get_notes($id, 'legalrisk');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }
    /* Manage risk types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('legal_risks');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->legalrisk_model->add_legalrisk_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('risk_type'));
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
                $success = $this->legalrisk_model->update_legalrisk_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('risk_type'));
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
            access_denied('legal_risks');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('legalrisk_types');
        }
        $data['title'] = _l('risk_types');
        $this->load->view('admin/legalrisks/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_legalrisk_type($id)
    {
        if (!$id) {
            redirect(admin_url('legal_risks/types'));
        }
        if (!is_admin()) {
            access_denied('legal_risks');
        }
        $response = $this->legalrisk_model->delete_legalrisk_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('legalrisk')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('risk_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('legalrisk')));
        }
        redirect(admin_url('legal_risks/types'));
    }

    public function add_contract_attachment($id)
    {
        handle_legalrisk_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('contract_id'), 'legalrisk', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode(array(
                'success' => $this->legalrisk_model->delete_contract_attachment($attachment_id),
            ));
        }
    }
	 /* Manage risk types Since Version 1.0.3 */
    public function riskstatus($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('legal_risks');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->legalrisk_model->add_legalrisk_status($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('risk_status'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'id'=>$id,
                    'name'=>$this->input->post('statusname'),
                ));
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->legalrisk_model->update_legalrisk_status($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('risk_status'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    }

    public function riskstatuses()
    {
        if (!is_admin()) {
            access_denied('legal_risks');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('legalrisk_statuses');
        }
        $data['title'] = _l('risk_types');
        $this->load->view('admin/legalrisks/manage_statuses', $data);
    }

    /* Delete announcement from database */
    public function delete_legalrisk_status($id)
    {
        if (!$id) {
            redirect(admin_url('legal_risks/riskstatuses'));
        }
        if (!is_admin()) {
            access_denied('legal_risks');
        }
        $response = $this->legalrisk_model->delete_legalrisk_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('legalrisk')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('risk_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('legalrisk')));
        }
        redirect(admin_url('legal_risks/riskstatuses'));
    }

}
