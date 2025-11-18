<?php
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
use SebastianBergmann\Diff\Differ;
use Smalot\PdfParser\Parser;
use PhpOffice\PhpWord\IOFactory;
 require_once  APPPATH . '/vendor/smalot/pdfparser/alt_autoload.php-dist';
defined('BASEPATH') or exit('No direct script access allowed');

class Contracts extends AdminController
{
    public function __construct()
    {
        parent::__construct();
           $this->openai_api_key = get_option('openai_apikey');
        $this->load->model('contracts_model');
    }

    /* List all contracts */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        $type = $this->input->get('type') ?? 'contracts';
        $data['type'] = $type;
        if($type=='contracts')
        $data['clients']		=$this->clients_model->get('',['tblclients.active'=>1]);
        else
            $data['clients'] = $this->clients_model->get('', [
                'tblclients.active' => 1,
                'tblclients.ctype'  => 'po'
            ]);

        $data['expiring']               = $this->contracts_model->get_contracts_about_to_expire(get_staff_user_id());
        $data['count_active']           = count_active_contracts($type);
        $data['count_expired']          = count_expired_contracts();
        $data['count_recently_created'] = count_recently_created_contracts();
        $data['count_trash']            = count_trash_contracts();
         $data['count_receivable']      = count_receivable_contracts();
          $data['count_payable']        = count_payable_contracts();
        $data['chart_types']            = json_encode($this->contracts_model->get_contracts_types_chart_data());
        $data['contract_statuses']  = $this->contracts_model-> get_contract_status();
        $data['chart_types_values']     = json_encode($this->contracts_model->get_contracts_types_values_chart_data());
        $data['contract_types']         = $this->contracts_model->get_contract_types();
        $data['years']                  = $this->contracts_model->get_contracts_years();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title'] = _l($type);
        $this->load->view('admin/contracts/manage', $data);
    }

     /* List all signcontracts */
    public function signcontracts()
    {
        close_setup_menu();

        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        $data['expiring']               = $this->contracts_model->get_contracts_about_to_expire(get_staff_user_id());
        $data['count_active']           = count_active_contracts();
        $data['count_expired']          = count_expired_contracts();
        $data['count_recently_created'] = count_recently_created_contracts();
        $data['count_trash']            = count_trash_contracts();
        $data['chart_types']            = json_encode($this->contracts_model->get_contracts_types_chart_data());
        $data['chart_types_values']     = json_encode($this->contracts_model->get_contracts_types_values_chart_data());
        $data['contract_types']         = $this->contracts_model->get_contract_types();
        $data['years']                  = $this->contracts_model->get_contracts_years();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title']         = _l('contracts');
        $this->load->view('admin/contracts/manage_postcontract', $data);
    }

    
    public function signtable($clientid = '',$opposite_party='')
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('sign_contracts', [
            'clientid' => $clientid,
			'opposite_party'=> $opposite_party
        ]);
    }
    public function table($clientid = '',$opposite_party='')
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('contracts', [
            'clientid' => $clientid,
			'opposite_party'=> $opposite_party
        ]);
    }
   

    /* Edit contract or add new contract */
    public function contract($id = '')
    {
        if ($this->input->post()) {
			
            if ($id == '') {
                if (!has_permission('contracts', '', 'create')) {
                    access_denied('contracts');
                }
                $id = $this->contracts_model->add($this->input->post());
                if ($id) {
					if(!empty($this->input->post('contract_template_id'))){
						 $contracttemp_name=$this->db->get_where('tbltemplates',array('id'=>$this->input->post('contract_template_id')))->row()->temp_filename;
					if(!empty($contracttemp_name)){
					$this->generate_contract_agreement_word($id);
					}else{
					$this->generateword_contract($id,1);
					}
					}
					
				
                    set_alert('success', _l('added_successfully', _l('contract')));
                    redirect(admin_url('contracts/contract/' . $id));
                }
            } else {
                if (!has_permission('contracts', '', 'edit')) {
                    access_denied('contracts');
                }
				
                $success = $this->contracts_model->update($this->input->post(), $id);
                if ($success) {
					if(!empty($this->input->post('contract_template_id'))){
						 $contracttemp_name=$this->db->get_where('tbltemplates',array('id'=>$this->input->post('contract_template_id')))->row()->temp_filename;
					if(!empty($contracttemp_name)){
					$this->generate_contract_agreement_word($id);
					}else{
					$this->generateword_contract($id);
					}
					}
				
                    set_alert('success', _l('updated_successfully', _l('contract')));
                }
                redirect(admin_url('contracts/contract/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('contract_lowercase'));
        } else {
            $data['latestversionid']='';
            $data['latest_version_contract']='';
            $data['contract']                 = $this->contracts_model->get($id, [], true);
            $data['contract_renewal_history'] = $this->contracts_model->get_contract_renewal_history($id);
            $data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'contract']);
			$data['contract_risklist'] = $this->contracts_model->get_contract_risklistbyperson($id);
			$data['risklists']=$this->contracts_model->get_contract_risklist();
				if($data['contract']->type=='po'){
                $data['contract_approvals']=get_approvals($id,'po');
            }else{
                $data['contract_approvals']=get_approvals($id,'contract');
            }
            $versiondoccount=total_rows(db_prefix() . 'contract_versions', ['contractid' => $id]);
            if($versiondoccount>0){
			$latest_version=get_current_contract_versioninfo($id);
			$data['latestversionid']=$latest_version->id;
			$data['latest_version_contract']=$this->extractText($latest_version->version_internal_file_path,$id);
        }
            if (!$data['contract'] || (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('contract_not_found'));
            }
			
            $data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract', [ 'client'], '{email_signature}');

            $title = $data['contract']->subject;

            $data = array_merge($data, prepare_mail_preview_data('contract_send_to_customer', $data['contract']->client));
            $data['template1'] =  prepare_mail_preview_data('contract_send_to_otherparty', $data['contract']->client);
            $data['staffs']                 = $this->staff_model->get();
           $this->load->model('templates_model');
		   $data['contract_closure_fields']= $this->templates_model->get('', ['is_legalclause'=>1]); 
			$data['view_compareurl'] =$data['contract']->comparison_view_url;
			  $data['contract_amendments']=$this->contracts_model->get_amendments($id);
		   $data['contract_postactions']=$this->contracts_model->get_postactions($id);
		   $data['contract_negotiations']=$this->contracts_model->get_comments($id,'negotiation');
        }
		
        $data['tab'] = $this->input->get('tab')?$this->input->get('tab'):'tab_content';
    
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
		
		/*if ($this->input->get('viewurl')) {
            $data['view_compareurl'] = $this->input->get('viewurl');
        }*/
		if ($this->input->get('party_id')) {
            $data['party_id'] = $this->input->get('party_id');
        }
        $this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->contracts_model->get_contract_types();
		//$data['clients']		=$this->clients_model->get('',['tblclients.active'=>1]);
		 if($data['contract']->type=='contracts')
        $data['clients']		=$this->clients_model->get('',['tblclients.active'=>1]);
        else
            $data['clients'] = $this->clients_model->get('', [
                'tblclients.active' => 1,
                'tblclients.ctype'  => 'po'
            ]);
		$data['statuses']  = $this->contracts_model-> get_contract_status();
		$data['project_members'] = $this->contracts_model->get_contract_members($id);
		$data['staff']    = $this->staff_model->get('', ['active' => 1]);
        $data['title']         = $title;
        $data['bodyclass']     = 'contract';
		$this->load->model('tickets_model');
		$data['requests']=$this->tickets_model->get();
		$data['service']='contract';
		$data['service_providers']=$this->db->get_where('tblserviceprovider',array('active'=>1))->result_array();
        $data['activity_log']  = $this->contracts_model->get_contract_activity_log($id);
		$data['templates']=$this->contracts_model->get_templates_of_contract();
		$this->load->model('departments_model');
        $data['departments']=$this->departments_model->get();
        $this->load->view('admin/contracts/contract', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/contracts/templates/' . $name, [], true);
    }

    public function mark_as_signed($id)
    {
        if (!staff_can('edit', 'contracts')) {
            access_denied('mark contract as signed');
        }

        $this->contracts_model->mark_as_signed($id);

        redirect(admin_url('contracts/contract/' . $id));
    }

    public function unmark_as_signed($id)
    {
        if (!staff_can('edit', 'contracts')) {
            access_denied('mark contract as signed');
        }

        $this->contracts_model->unmark_as_signed($id);

        redirect(admin_url('contracts/contract/' . $id));
    }
    public function mark_as_send($id)
    {
        if (!staff_can('edit', 'contracts')) {
            access_denied('mark contract as sended');
        }
		$contract                = $this->contracts_model->get($id, [], true);
        $result=$this->contracts_model->mark_as_send($id);
		if($result>0){
		$sms_sent = false;
		$sms_success=false;
        $sms_reminder_log = [];
			
		$this->load->model('casediary_model');
		$contacts = $this->contracts_model->get_contracts_of_oppositeparty($id);
		foreach($contacts as $contact){
       	$otherparty_det = $this->casediary_model->get_oppositeparty($contact['id']);
		$other_party_phonenummber=$otherparty_det->mobile;
		$other_party_email=$otherparty_det->email;
    //  $sms_success= CURLsendsms($other_party_phonenummber);
        $sms_insert['email_subject'] = SMS_TRIGGER_CONTRACT_NEW_INVITE_TO_CUSTOMER;
        $sms_insert['sms_to']        = $other_party_phonenummber;
		//$template = mail_template('contract_send_to_customer', $contract, $other_party_email);
		$template = mail_template('contract_invitation_to_sign_transfers', $contract, $other_party_email);
		$merge_fields = $template->get_merge_fields();
         $sent= $template->send();
			if($sent){
			$sms_sent = true;	
			}
        
		 }

        if ($sms_sent) {
            set_alert('success', _l('contract_invitation_sent_to_customer'));
        } else {
            //set_alert('danger', _l('failed'));
        }

       // redirect($_SERVER['HTTP_REFERER']);
		}

        redirect(admin_url('contracts/contract/' . $id));
    }
	 public function mark_as_send_sms($id)
    {
        if (!staff_can('edit', 'contracts')) {
            access_denied('mark contract as sended');
        }
		$contract                = $this->contracts_model->get($id, [], true);
        $result=$this->contracts_model->mark_as_send($id);
		if($result>0){
		$sms_sent = false;
		$sms_success=false;
        $sms_reminder_log = [];
			
		$this->load->model('casediary_model');
		$contacts = $this->contracts_model->get_contracts_of_oppositeparty($id);
		foreach($contacts as $contact){
       	$otherparty_det = $this->casediary_model->get_oppositeparty($contact['id']);
		$other_party_phonenummber=$otherparty_det->mobile; 
		$other_party_email=$otherparty_det->email;
    //   $sms_success= CURLsendsms($other_party_phonenummber,$id);
    //   if($sms_success=='success'){
	// 			 $sms_sent = true;
	// 	}
		}
        // if ($sms_sent) {
        //     set_alert('success', _l('contract_invitation_sent_to_customer'));
        // } else {
        //     set_alert('danger', $sms_success);
        // }

       // redirect($_SERVER['HTTP_REFERER']);
		}

        redirect(admin_url('contracts/contract/' . $id));
    }
		 public function legalcontract_approval($id)
    {
       if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('contracts'));
        }
        $legalapprove =   $this->contracts_model->get($id, [], true);
        $legalapprove->approval =  get_approvals($id,'contract',3);
 	
	    try {
            $pdf = legalcontract_approval_pdf($legalapprove);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($legalapprove->subject) . '.pdf', $type);
    } 

    public function pdf($id)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('contracts'));
        }

        $contract = $this->contracts_model->get($id);

        try {
            $pdf = contract_pdf($contract);
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

        $pdf->Output(slug_it($contract->subject) . '.pdf', $type);
    }

    public function send_to_email($id)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }
        $success = $this->contracts_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('contract_sent_to_client_success'));
        } else {
            set_alert('danger', _l('contract_sent_to_client_fail'));
        }
        redirect(admin_url('contracts/contract/' . $id));
    }
    
     public function send_to_email_otherparty($id)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }
        $success = $this->contracts_model->send_contract_to_otherparty($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('contract_sent_to_client_success'));
        } else {
            set_alert('danger', _l('contract_sent_to_client_fail'));
        }
        redirect(admin_url('contracts/contract/' . $id));
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own'))) {
            $this->misc_model->add_note($this->input->post(), 'contract', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if ((has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own'))) {
            $data['notes'] = $this->misc_model->get_notes($id, 'contract');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('contracts', '', 'delete')) {
            $this->contracts_model->clear_signature($id);
        }

        redirect(admin_url('contracts/contract/' . $id));
    }
   public function clear_partysignature($id)
    {
        if (has_permission('contracts', '', 'delete')) {
            $this->contracts_model->clear_partysignature($id);
        }

        redirect(admin_url('contracts/contract/' . $id));
    }
    public function save_contract_data()
    {
        if (!has_permission('contracts', '', 'edit')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update(db_prefix() . 'contracts', [
                'content' => html_purify($this->input->post('content', false)),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('contract'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->contracts_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->contracts_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_comments($id)
    {
        $data['comments'] = $this->contracts_model->get_comments($id);
        $this->load->view('admin/contracts/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'contract_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->contracts_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function renew()
    {
        if (!has_permission('contracts', '', 'create') && !has_permission('contracts', '', 'edit')) {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->contracts_model->renew($data);
            if ($success) {
			
                set_alert('success', _l('contract_renewed_successfully'));
            } else {
                set_alert('warning', _l('contract_renewed_fail'));
            }
            redirect(admin_url('contracts/contract/' . $data['contractid'] . '?tab=renewals'));
        }
    }

    public function delete_renewal($renewal_id, $contractid)
    {
        $success = $this->contracts_model->delete_renewal($renewal_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_renewal_deleted'));
        } else {
            set_alert('warning', _l('contract_renewal_delete_fail'));
        }
        redirect(admin_url('contracts/contract/' . $contractid . '?tab=renewals'));
    }

    public function copy($id)
    {
        if (!has_permission('contracts', '', 'create')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url('contracts'));
        }
        $newId = $this->contracts_model->copy($id);
        if ($newId) {
            set_alert('success', _l('contract_copied_successfully'));
        } else {
            set_alert('warning', _l('contract_copied_fail'));
        }
        redirect(admin_url('contracts/contract/' . $newId));
    }

    /* Delete contract from database */
    public function delete($id)
    {
        if (!has_permission('contracts', '', 'delete')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url('contracts'));
        }
        $response = $this->contracts_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('Deleted'));
        } else {
            set_alert('warning', _l('problem_deleting'));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
             redirect($_SERVER['HTTP_REFERER']);
        }
    }

    /* Manage contract types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->contracts_model->add_contract_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('contract_type'));
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
                $success = $this->contracts_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('contract_type'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }

    public function types()
    {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('contract_types');
        }
        $data['title'] = _l('contract_types');
        $this->load->view('admin/contracts/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url('contracts/types'));
        }
        if (!is_admin()) {
            access_denied('contracts');
        }
        $response = $this->contracts_model->delete_contract_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract_type_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_type_lowercase')));
        }
        redirect(admin_url('contracts/types'));
    }
    public function contract_status()
    {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('contract_status');
        }
        $data['title'] = _l('contract_status');
        $this->load->view('admin/contracts/manage_status', $data);
    }

    public function status($id = '')
        {
            if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
                access_denied('contracts');
            }
            if ($this->input->post()) {
                if (!$this->input->post('id')) {
                    $id = $this->contracts_model->add_contract_status($this->input->post());
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('contract_status'));
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
                    $success = $this->contracts_model->update_contract_status($data, $id);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('contract_status'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
        /* Delete status from database */
    public function delete_contract_status($id)
    {
        if (!$id) {
            redirect(admin_url('contracts/contract_status'));
        }
        if (!is_admin()) {
            access_denied('contracts');
        }
        $response = $this->contracts_model->delete_contract_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_status_lowercase')));
        }
        redirect(admin_url('contracts/contract_status'));
    }
    
    public function add_contract_attachment($id)
    {
        handle_contract_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database(
                $this->input->post('contract_id'),
                'contract',
                $this->input->post('files'),
                $this->input->post('external')
            );
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode([
                'success' => $this->contracts_model->delete_contract_attachment($attachment_id),
            ]);
        }
    }
	    public function pagination()
    {

      $q='';
      $contracttype = $status = '';
      $where= false;
      if($this->input->post()){ /*print_r($_POST);*/
        $q= $this->input->post('q');
        if($this->input->post('contract_type') != ' '){
            $contracttype =$this->input->post('contract_type');
        }
        if($this->input->post('status') != ''){
            $status = $this->input->post('status');
        }
      }  
      $this->load->library("pagination");
      $config = array();
      $config["base_url"] = "#";
      $config["total_rows"] = $this->contracts_model->fetch_contract_details_num_rows($q,$contracttype,$status);
      $config["per_page"] = 12;
      $config["uri_segment"] = 4;
      $config["use_page_numbers"] = TRUE;
      $config["full_tag_open"] = '<ul class="pagination contract-page">';
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
       'project_data'   => $this->contracts_model->fetch_contract_details($q,$config["per_page"], $start,$contracttype,$status),
       'total_cases'=> '<span class="badge badge-success" style="padding: 10px;
    font-size: 15px;"><b>'.$config["total_rows"].'  '._l('contracts').'</b></span>',
      );
      echo json_encode($output);
    }
public function add_contractpdf($id=''){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['contractid'];
                unset($data['contractid']);
			 
             $message         = '';
            $success=handle_project_contract_file_upload($id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('contract')) : '';
					 log_activity('Signed Contract Uploaded [ContractID: ' . $id . ']');
			$this->contracts_model->log_contract_activity($id, 'not_signed_contract_renewed');
					  $updated          = true;
					 $contentfile='';
					$userfile1= $this->db->get_where('tblcontracts',array('id'=>$id))->row()->contract_filename;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'contracts', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );
					 }else{
					 
					  $message = 'Chaeck Image Image extension not allowed';
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

    public function add_signed_contractpdf($id=''){
        if ($this->input->post()) {
            $data=$this->input->post();
            $id   = $data['contractid'];
               unset($data['contractid']);
            
            $message         = '';
           $success=handle_signedLOE_contract_file_upload($id);
                if ($success == true) {
               $message = $id ? _l('added_successfully', _l('contract')) : '';
                     $updated          = true;
                   
                    }else{
                    
                     $message = 'Check Image extension not allowed';
                     $updated          = false;
                }
               if ($success) {
                    $contract=$this->db->select('id,signed_contract_filename')->get_where('tblcontracts',['id'=>$id])->row();
                $vers=get_current_contract_version($id)+1;
                $version_data['version']=$vers;
            $version_data['contractid']  = $id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
            $version_data['version_internal_file_path']=$contract->signed_contract_filename;
             $version_data['version_content']=$this->projectvesioncontents($contract->signed_contract_filename,$id);
            $this->db->insert('tblcontract_versions',$version_data);
                   $message= _l('contract_latest_uploaded');
                     log_activity('Signed Contract Uploaded [ContractID: ' . $id . ']');
           $this->contracts_model->log_contract_activity($id, 'not_signed_contract_uploded');
                   
               }
            
               echo json_encode(array(
                   'success' => $success,
                   'message' => $message,
               ));
       }
   }

		public function projectvesioncontents($userfile1,$contract_id)
	{
		//require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
	    require_once  APPPATH . '/vendor/smalot/pdfparser/alt_autoload.php-dist';
		$contentfile='';
		$userfile= get_upload_path_by_type('contract') . $contract_id . '/'.$userfile1;
		 $extension = pathinfo ($userfile1 , PATHINFO_EXTENSION);
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
						 $finalpdf=$pdf->getText();
						 $contentfile = nl2br($finalpdf);
					 }
		return $contentfile;
	}
	public function delete_contract_document($contract_id)
    {
        $this->contracts_model->delete_contract_document($contract_id);
    }

    public function delete_signed_contract_document($contract_id)
    {
        $this->contracts_model->delete_signed_contract_document($contract_id);
    }
	
	public function add_contractversionpdf(){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['contractid'];
                unset($data['contractid']);
			$current_version = get_current_contract_version($id);
			// Already version exists
            if($current_version>=0){
                $version_data['version'] = $current_version+1;
            }else{
			$version_data['version']=1;	
			}

            $version_data['contractid']  = $id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
			$this->db->insert('tblcontract_versions',$version_data);
			  $insert_id = $this->db->insert_id();
            //create_new_contract_version($version_data); 
			 
             $message         = '';
            $success=handle_project_contract_version_file_upload($id,$insert_id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('contract_version')) : '';
					  $updated          = true;
					  log_activity('Contract Version added [VersionID: ' . $insert_id . ', ContractID: ' . $id . ']');
			$this->contracts_model->log_contract_activity($id, 'not_contract_version_added');
					$contentfile='';
					$userfile1= $this->db->get_where('tblcontract_versions',array('id'=>$insert_id))->row()->version_internal_file_path;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $insert_id );
					 $this->db->update( db_prefix() . 'contract_versions', [
					 	'version_content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );
					 }else{
					 
					  $message = 'Check  Image extension not allowed';
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
    public function mark_as_final_doc($contract_id,$version){
        if (!$contract_id) {
            redirect(admin_url('contracts'));
        }
        $response = $this->contracts_model->make_final_doc($contract_id,$version);
        if ($response == true) {
            set_alert('success', _l('success'));
			log_activity('Contract Version Finalized [VersionID: ' . $version . ', ContractID: ' . $contract_id . ']');
			$this->contracts_model->log_contract_activity($contract_id, 'not_contract_version_finalized');
        }else{
            set_alert('danger', _l('failed'));
        }
        redirect(admin_url('contracts/contract/'.$contract_id));
    }
	
	public function  update_sharepoint1($contract_id,$versioncount)
    {
		$this->load->library('sharegraph');
		$sharegraph=new Sharegraph();    
        if ($this->input->is_ajax_request()) {
			 $this->db->where('id', $contract_id);
             $attachment = $this->db->get(db_prefix() . 'contracts')->row();
			$versioncount=$this->db->get_where('tblcontract_versions',array('contractid'=>$contract_id))->num_rows();
			
		if($versioncount==0){
			$sharegraph->download_updatefile($contract_id,$attachment->contract_filename);
		}
		else{
			$latestversion=get_current_contract_version($contract_id);
			if($data['is_newversion']==0){
			$sharegraph->download_updateversionfile($latestversion,$contract_id,$attachment->contract_filename);
			}else{
				
			}
			
		}
		//	$this->projectvesioncontents($attachment->file_name,$project_id);
			/*$contentfile=projectdocumentcontents($attachment->file_name,$project_id);
			 $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'project_files', [
            'content' => $contentfile,
        ]);*/
        $alert   = 'success';
        $message = _l('updated_successfully');
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('updated_successfully');
        }
       $result= [
            'alert'   => $alert,
            'message' => $message,
        ];
    }
			echo json_encode($result);
        
    }
public function update_contractfile_version($contract_id,$create_new_version_enabled){
	$this->load->library('sharegraph');
		$sharegraph=new Sharegraph();
	if ($this->input->is_ajax_request()) {
		$this->db->where('id', $contract_id);
             $attachment = $this->db->get(db_prefix() . 'contracts')->row();
			//$versioncount=$this->db->get_where('tblcontract_versions',array('contractid'=>$contract_id))->num_rows();
         //--------- if new version enabled
        if($create_new_version_enabled=='yes'){
            $current_version = get_current_contract_version($contract_id);
			//print_r($current_version);
            // Already version exists
            if($current_version>=0){
                $version_data['version'] = $current_version+1;
				
                $path = get_upload_path_by_type('contract') . $contract_id . '/';
                _maybe_create_upload_path($path);
                $version_path = get_upload_path_by_type('contract') . $contract_id . '/'.$version_data['version'];
                _maybe_create_upload_path($version_path);
				 // Getting file extension
                    $extension = strtolower(pathinfo($attachment->contract_filename, PATHINFO_EXTENSION));
				if($current_version!=0)
				$oldfilename = basename($attachment->contract_filename,".".$extension).'-'.$current_version.'.'.$extension;
				else
					$oldfilename = $attachment->contract_filename;
				$newfilename = basename($attachment->contract_filename,".".$extension).'-'.$version_data['version'].'.'.$extension;
				
				 $newFilePath = $path . $newfilename;
				//create file using downlod sharelink
				$sharegraph->download_updateversionfile($version_data['version'],$contract_id,$oldfilename,$newfilename);
             // 	$sharegrah->rungraphversionuser($newfilename,$path,$contract_id,$version_data['version']);	
				$sharegraph->rungraphuser($newfilename,$newFilePath,$contract_id);
			  $sharelink=$sharegraph->getweburl($contract_id,$newfilename);
              $version_data['version_sharpoint_link']=$sharelink;  
               $version_data['version_internal_file_path']=$newfilename; 
            }

            $version_data['contractid']  = $contract_id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
			
            create_new_contract_version($version_data);    
             $alert   = 'success';
        $message = _l('newversion_created');

        }else{
            
            // replace current contract
			$sharegraph->download_updatefile($contract_id,$attachment->contract_filename);
             $alert   = 'success';
        $message = _l('updated_successfully');
        }
        
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('updated_successfully');
        }
       $result= [
            'alert'   => $alert,
            'message' => $message,
        ];
    }
			echo json_encode($result); 


    }
	
	/* Change contractversion status / active / inactive */
    public function change_version_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->contracts_model->change_version_status($id, $status);
           
        }

    }

	public function generateword_contract($id,$version=0)
    {
		
		
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('contracts'));
        }

        $contract = $this->contracts_model->get($id);

		
        $htmlTemplate =str_replace('<br>', '<br/>', $contract->content);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $footer_sub = $section->addFooter();
        //$footer_sub->addText(htmlspecialchars($footer));
        $footer_sub->addPreserveText('Smart Legal Counsel                                      {PAGE} ');

        Html::addHtml($section, $htmlTemplate);
		$filename= preg_replace('/[^a-zA-Z0-9_]/s','',$contract->subject).'.docx';
		//$filename=str_replace(' ','',$contract->subject).'.docx';
        //Html::addHtml('ssdadsa', view($footerTemplate), false, false);
        $path        = get_upload_path_by_type('contract').$id.'/';
		_maybe_create_upload_path($path);
        $targetFile = $path . $filename;
        $phpWord->save($targetFile, 'Word2007');
		$sharelink='';
		if(get_option('enable_sharepoint')==1){
			$this->load->library('sharegraph');
			$sharegraph=new Sharegraph();
		$sharegraph->rungraphuser($filename,$targetFile,$id);	
		$sharelink=$sharegraph->getweburl($id,$filename);
		}
		$this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contracts', [
                'contract_filename' =>$filename ,
				'file_type' =>pathinfo($filename, PATHINFO_EXTENSION),
				'sharepoint_link'=>$sharelink,
        ]);
        if($version==1){
         $version_data['version']=1;
            $version_data['contractid']  = $id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
            $version_data['version_internal_file_path']=$filename;
             $version_data['version_content']=$contract->content;
            $this->db->insert('tblcontract_versions',$version_data);
             $insert_id = $this->db->insert_id();
         }
	}
	
  public function get_templates_of_contract($contractid)

    {

        if ($this->input->is_ajax_request()) {

            echo json_encode($this->contracts_model->get_templates_of_contract($contractid));

        }

    }
	   public function generate_contract_agreement_word($id){
        
        $filename='temp_sale_agreement_final.docx';
		
       // require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
     
        $contract = $this->contracts_model->get($id);//print_r($contract->id);
		$contract_name=$this->db->get_where('tbltemplates',array('id'=>$contract->contract_template_id))->row()->temp_filename;
		$contractid=$contract->id;
		$filename=$contract_name;//str_replace(' ','_',$contract_name).'.docx';
        
      
        $clients_data = $this->clients_model->get($contract->client);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('uploads/templates/'.$filename);
        
        $clientsign=!empty($contract->signature) ? $contract->signature : NULL; 
        $partysign=!empty($contract->party_signature) ? $contract->party_signature : NULL; 
        $date = date('d-m-Y');
        $client_company_name = $clients_data->company;
        $address     = nl2br($clients_data->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $address);
        $address = str_replace('&','&amp;', $address) ;
        $contactno   = $clients_data->phonenumber;
        $email       = $clients_data->email_id;
        $contract_amount = $contract->contract_value;
        $client_company_name = str_replace('&','&amp;', $clients_data->company) ;
		$client_contact= get_primary_contact_user_id($contract->client);
        $emirate   = $clients_data->state;
        $country   = get_country_name($clients_data->country);
		$contract_start_date=date('F d , Y', strtotime($contract->datestart));
        $this->load->library('app_number_to_word', [ 'clientid' => $contract->client ], 'numberword');
        $amount_in_words = $this->numberword->convert($contract->contract_value,'','Fils');
		 $this->load->model('casediary_model');
       	$otherparty_det = $this->casediary_model->get_oppositeparty($contract->other_party);
        $other_party_name= str_replace('&','&amp;',$otherparty_det->name);
		$other_party_address=  str_ireplace($breaks,'</w:t><w:br/><w:t>',$otherparty_det->address).' '.$otherparty_det->city;
        $ref_no = '';//$contract->contract_refno;
        $templateProcessor->setValue('date',$date);
        $templateProcessor->setValue('client_contact_name',get_contact_full_name($client_contact));
        $templateProcessor->setValue('client_address',$address);
        $templateProcessor->setValue('contactno',$contactno);
        $templateProcessor->setValue('email',$email);
        $templateProcessor->setValue('contract_amount',$contract_amount);
        $templateProcessor->setValue('client_company_name',$client_company_name);
        $templateProcessor->setValue('emirate',$emirate);
        $templateProcessor->setValue('country',$country);
        $templateProcessor->setValue('amount_in_words',$amount_in_words);
        $templateProcessor->setValue('contract_refno',$ref_no);
		$templateProcessor->setValue('contract_start_date',$contract_start_date);
		$templateProcessor->setValue('other_party',$other_party_name);
		$templateProcessor->setValue('other_party_address',$other_party_address);
    	//$templateProcessor->setImageValue('CompanyLogo', 'path/to/company/logo.png');
//$templateProcessor->setImageValue('CompanyLogo', 'path/to/company/logo.png');
//$templateProcessor->setImageValue('UserLogo', array('path' => 'path/to/logo.png', 'width' => 100, 'height' => 100, 'ratio' => false));
/*$templateProcessor->setImageValue('Signature', function () {
    // Closure will only be executed if the replacement tag is found in the template
    return array('path' => 'path/to/signature.png', 'width' => 100, 'height' => 100, 'ratio' => false);
});*/
		    /* $path        = get_upload_path_by_type('contract').$id.'/';
$templateProcessor->setImageValue('clientSignature', function () {
    // Closure will only be executed if the replacement tag is found in the template
    return array('path' => get_upload_path_by_type('contract').$contractid.'/'.$clientsign, 'width' => 100, 'height' => 100, 'ratio' => false);
});
$templateProcessor->setImageValue('Signature', function () {
    // Closure will only be executed if the replacement tag is found in the template
    return array('path' => get_upload_path_by_type('contract').$contractid.'/'.$partysign, 'width' => 100, 'height' => 100, 'ratio' => false);
});*/
        // file upload
     $path        = get_upload_path_by_type('contract').$id.'/';
		$newFilePath=$path.$filename;
        _maybe_create_upload_path($path);
        if(file_exists($path.$filename)){
            unlink($path.$filename);
        }
		$templateProcessor->saveAs($path.$filename);
			$sharelink='';
		if(get_option('enable_sharepoint')==1){
			$this->load->library('sharegraph');
			$sharegraph=new Sharegraph();
		$sharegraph->rungraphuser($filename,$targetFile,$id);	
		$sharelink=$sharegraph->getweburl($id,$filename);
		}
		$this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contracts', [
                'contract_filename' =>$filename ,
				'file_type' =>pathinfo($filename, PATHINFO_EXTENSION),
				'sharepoint_link'=>$sharelink,
        ]);
           
    }

	/* Get Emailtemplate by id */
	public function get_prompt_of_contract($contractid)

    {

        if ($this->input->is_ajax_request()) {
			$contract = $this->contracts_model->get($contractid);//print_r($contract->id);
			  $clients_data = $this->clients_model->get($contract->client);
 		$client_company_name = $clients_data->company;
        $address     = nl2br($clients_data->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $address);
        $address = str_replace('&','&amp;', $address) ;
        $contactno   = $clients_data->phonenumber;
        $email       = $clients_data->email_id;
        $contract_amount = $contract->contract_value;
        $client_company_name = str_replace('&','&amp;', $clients_data->company) ;
		$client_contact= get_primary_contact_user_id($contract->client);
        $emirate   = $clients_data->state;
        $country   = get_country_name($clients_data->country);
		$contract_start_date=date('F d , Y', strtotime($contract->datestart));
 		$this->load->model('casediary_model');
       	$otherparty_det = $this->casediary_model->get_oppositeparty($contract->other_party);
        $other_party_name= str_replace('&','&amp;',$otherparty_det->name);
		$other_party_address=  str_ireplace($breaks,'</w:t><w:br/><w:t>',$otherparty_det->address).' '.$otherparty_det->city;
		$prompt ='Generate '.$contract->type_name.'  template between parties . First Party : '.$client_company_name.' Address : '.$address.' Second Party '.$other_party_name.' Address : '.$other_party_address;
           
 	echo json_encode($prompt);
        }

    }
	
	
    public function add_quick_contract(){
        $this->load->model('casediary_model');
            if ($this->input->post()) {
                if (!$this->input->post('id')) {
					 if (!has_permission('contracts', '', 'create')) {
                    access_denied('contracts');
                }
                $data = $this->input->post();
					$data['project_id']=$this->input->post('projectid');
					unset($data['projectid']);
               
				$id = $this->contracts_model->add($data);
                if($id){
					if(!empty($this->input->post('contract_template_id'))){
						 $contracttemp_name=$this->db->get_where('tbltemplates',array('id'=>$this->input->post('contract_template_id')))->row()->temp_filename;
                     if(!empty($contracttemp_name)){
					$this->generate_contract_agreement_word($id);
					}else{
					$this->generateword_contract($id,1);
					}  
					}
					else{
                        if (!empty($_FILES['agree_attachment']['name'])) { 
                            $res = handle_project_contract_file_upload($id);
                            if($res){ 
                                $contentfile='';
            					$userfile1= $this->db->get_where('tblcontracts',array('id'=>$id))->row()->contract_filename;
            					$contentfile=$this->projectvesioncontents($userfile1,$id);
           
            					  $this->db->where( 'id', $id );
            					 $this->db->update( db_prefix() . 'contracts', [
            					 	'content' => $contentfile,
            					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
            					 ] );
            					 
            					 
            					 $current_version = get_current_contract_version($id);
                    			// Already version exists
                                if($current_version>=0){
                                    $version_data['version'] = $current_version+1;
                                }else{
                    			$version_data['version']=1;	
                    			}
                    
                                $version_data['contractid']  =$id;  
                                $version_data['version_content']  =nl2br($contentfile);  
                                $version_data['version_internal_file_path']  =$userfile1;  
                                $version_data['dateadded'] = date('Y-m-d H:i:s');
                                $version_data['addedby'] =  get_staff_user_id();
                    			$this->db->insert('tblcontract_versions',$version_data);
                            }
                        }
                    }
                    
                        $success = true;
                        $message = _l('Added_successfully');
                    }
                    echo json_encode([
                        'success' => $success,
                         'message' => $message,
                         'id'      => $id,
                        'name'    => $this->input->post('subject'),
                        'link'	  =>'contracts/contract/'.$id,
                         'clientid' =>$this->input->post('client'),
                                         ]);
                }
            }
        }


        public function risk_value_checklist()
        {
            if (!is_admin()) {
                access_denied('contracts');
            }
            if ($this->input->is_ajax_request()) {
                $this->app->get_table_data('risk_value_checklist');
            }
            $data['title'] = _l('risk_value_checklist');
            $this->load->view('admin/contracts/manage_checklist', $data);
        }

        public function checklist($id = '')
        {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->contracts_model->add_risk_checklist($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('risk_value_checklist'));
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
                $success = $this->contracts_model->update_risk_checklist($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('risk_value_checklist'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }
    
        /* Delete announcement from database */
        public function delete_risk_value_checklist($id)
        {
            if (!$id) {
                redirect(admin_url('contracts/risk_value_checklist'));
            }
            if (!is_admin()) {
                access_denied('contracts');
            }
            $response = $this->contracts_model->delete_checklist($id);
            if (is_array($response) && isset($response['referenced'])) {
                set_alert('warning', _l('is_referenced', _l('contract_type_lowercase')));
            } elseif ($response == true) {
                set_alert('success', _l('deleted', _l('checklist')));
            } else {
                set_alert('warning', _l('problem_deleting', _l('checklist_lowercase')));
            }
            redirect(admin_url('contracts/risk_value_checklist'));
        }
	public function save_risk_checklist($contract_id)
    {
        if ($this->input->post()) {
            $success = $this->contracts_model->save_risklist($this->input->post(), $contract_id);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('risklist')));
            }
            redirect(admin_url('contracts/contract/' . $contract_id . '?tab=risklist'));
        }
    }
	  public function change_riskapproval_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->contracts_model->change_riskapproval_status($id, $status));
        }
    }
	 public function change_riskapproval_staff_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->contracts_model->change_riskapproval_staff($id, $status));
        }
    }

    public function change_riskapproval_remarks_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->contracts_model->change_riskapproval_remarks($id, $this->input->post('remarks')));
        }
    }
	public function documentcompare()
	{
	

$differ = new Differ();

$file1 = 'This is the first text.';
$file2 = 'This is the second text.';

$diffResult = $differ->diff($file1, $file2);
echo $diffResult;
		$parser = new Parser();
$path = get_upload_path_by_type('contract') . '11/';
// Parse PDF file 1
$pdf1 = $parser->parseFile($path.'tuala_sale_agreement_final.pdf');
$text1 = $pdf1->getText();

// Parse PDF file 2
$pdf2 = $parser->parseFile($path.'tuala_sale_agreement_final1.pdf');
$text2 = $pdf2->getText();
		
  
    // Use FineDiff or Sebastian Diff
    $differ = new \SebastianBergmann\Diff\Differ();
    $diffResult = $differ->diff($text1,$text2);
print_r($diffResult);
    // Highlight changes in HTML
    $highlightedDiff = nl2br(htmlentities($diffResult));
		print_r($highlightedDiff);
    }
public function documentcompare_word()
{
			$parser = new Parser();
$path = get_upload_path_by_type('contract') . '11/';
	// Load Word documents
$doc1Path = 'tuala_sale_agreement_final.docx';
$doc2Path ='tuala_sale_agreement_final-1.docx';
// Parse PDF file 1
//$doc1 = IOFactory::load($doc1Path);

// Parse PDF file 2
//$doc2 = IOFactory::load($doc2Path);	
//$text1 = $this->extractText($doc1);
//$text2 = $this->extractText($doc2);	
$text1=$this->extractText($doc1Path,11);
$text2=$this->extractText($doc2Path,11);
 //print_r($text2);  
    // Use FineDiff or Sebastian Diff
    $differ = new \SebastianBergmann\Diff\Differ();
    $diffResult = $differ->diff($text1,$text2);
print_r($diffResult);
    // Highlight changes in HTML
    $highlightedDiff = nl2br(htmlentities($diffResult));
		print_r($highlightedDiff);
    // Generate PDF with highlighted differences

}
// Extract text
function extractText($userfile,$contract_id) {
	 require_once  APPPATH . '/vendor/smalot/pdfparser/alt_autoload.php-dist';
		$contentfile='';
    $text = '';
	$contentfile='';
		$userfile1= get_upload_path_by_type('contract') . $contract_id . '/'.$userfile;
	 $extension = pathinfo ($userfile1 , PATHINFO_EXTENSION);
		if($extension=='doc'){
			 $phpWord = \PhpOffice\PhpWord\IOFactory::createReader('MsDoc')->load($userfile1);

			  foreach($phpWord->getSections() as $section) {
				 foreach($section->getElements() as $element) {
						if(method_exists($element,'getText')) {
                  //  echo($element->getText(). "<br>");
					$text.=$element->getText();
                }
            }
        }
		}else if($extension=='docx'){
	$doc = \PhpOffice\PhpWord\IOFactory::createReader('Word2007')->load($userfile1);
	
    foreach ($doc->getSections() as $section) {
        foreach ($section->getElements() as $element) {
            if (method_exists($element, 'getText')) {
                $text .= $element->getText();
            }
        }
    }
	 foreach($doc->getSections() as $section) {
        foreach($section->getElements() as $element) {
            if (method_exists($element, 'getElements')) {
                foreach($element->getElements() as $childElement) {
                    if (method_exists($childElement, 'getText')) {
                        $text .= $childElement->getText() . ' ';
                    }
                    else if (method_exists($childElement, 'getContent')) {
                        $text .= $childElement->getContent() . ' ';
                    }
                }
            }
            else if (method_exists($element, 'getText')) {
               // $text .= $element->getText() . ' ';
            }
        }
					  }
			} else if($extension=='pdf'){
						 $parser = new \Smalot\PdfParser\Parser();
						 $pdf = $parser->parseFile($userfile1);
						 $finalpdf=$pdf->getText();
						 $text = nl2br($finalpdf);
					 }

    return $text;
}
public function fetch_contractcomparison($contract_id){
		$accountId=get_option('draftable_accountid');//"SuNoJs-test";
		$authToken =get_option('draftable_authToken');//"bdbf462a13bb040fd7e7a22e33729561";// "{auth_token}"; // Replace with your actual auth token
		if ($this->input->post()) {
           // $success = $this->contracts_model->save_risklist($this->input->post(), $contract_id);
		$leftname= $this->db->get_where('tblcontracts',array('id'=>$contract_id))->row()->contract_filename;
		$rightname= $this->db->get_where('tblcontract_versions',array('id'=>$this->input->post('right_version')))->row()->version_internal_file_path;
		
		$leftFile = get_upload_path_by_type('contract') . $contract_id . '/'.$leftname;//"path/to/left.pdf"; // Path to the left PDF
		$rightFile = get_upload_path_by_type('contract') . $contract_id . '/'.$rightname;//"path/to/right.pdf"; // Path to the right PDF

$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://api.draftable.com/v1/comparisons");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);

// Prepare the form data
$formData = [
    "left.file_type" => pathinfo ($leftname , PATHINFO_EXTENSION),//"docx",
    "left.file" => new CURLFile($leftFile),
    "right.file_type" => $extension = pathinfo ($rightname , PATHINFO_EXTENSION),//"docx",
    "right.file" => new CURLFile($rightFile),
    "public" => "true"
];

curl_setopt($ch, CURLOPT_POSTFIELDS, $formData);

// Add authorization header
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token $authToken"
]);

// Execute the request
$response = curl_exec($ch);

if (curl_errno($ch)) {
    echo "Error: " . curl_error($ch);
} else {
	// Decode the JSON response
$data = json_decode($response, true);

// Extract the identifier
if (isset($data['identifier'])) {
    $identifier = $data['identifier'];
  //  echo "Identifier: " . $identifier;
	// Generate the Comparison Viewer URL
    $viewerUrl = "https://api.draftable.com/v1/comparisons/viewer/$accountId/$identifier";
	 $this->db->where('id', $contract_id);
      $this->db->update(db_prefix() . 'contracts', [
                        'comparison_view_url' =>$viewerUrl,
                        'comparison_identity' =>$identifier,
                    ]);
  //  echo "Comparison Viewer URL: " . $viewerUrl;
} else {
    echo "Identifier not found in the response.";
}
    
}
//	$exporturl=$this->export_comparison($identifier);
//redirect(admin_url('contracts/contract/' . $contract_id . '?tab=comparisons&viewurl='.$viewerUrl));
redirect(admin_url('contracts/contract/' . $contract_id . '?tab=comparisons'));

// Close the cURL session
curl_close($ch);
	}
	}
	
public function export_comparison($identifier){
$export_identifier= $exportUrl='';	
// Your API token (replace with your actual token)
$authToken = "bdbf462a13bb040fd7e7a22e33729561";
// API URL
$posturl = "https://api.draftable.com/v1/exports";


// Data to send in the POST request
$postData = [
    "comparison" => $identifier,
    "kind" => "single_page"
];

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $posturl);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token $authToken",
    "Content-Type: application/json"
]);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData)); // Encode the POST data as JSON

// Execute the POST request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Handle the response
    if (isset($data['identifier'])) {
        echo "Export Request Created.\n";
        echo "Identifier: " . $data['identifier'] . "\n";
		$export_identifier=$data['identifier'];
    } else {
        echo "Unexpected response format.\n";
    }
}

// Close cURL
curl_close($ch);
		// Draftable API URL
$url = "https://api.draftable.com/v1/exports/".$export_identifier;

// Initialize cURL
$ch = curl_init();

// Set cURL options
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Authorization: Token $authToken"
]);

// Execute the GET request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo "cURL Error: " . curl_error($ch);
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the response contains the required keys
    if (isset($data['identifier'], $data['ready'], $data['url'])) {
        $identifier = $data['identifier'];
        $ready = $data['ready'];
        $exportUrl = $data['url'];

        echo "Identifier: $identifier\n";
        echo "Ready: " . ($ready ? "Yes" : "No") . "\n";

        if ($ready) {
            echo "Export URL: $exportUrl\n";
        } else {
            echo "The export is not ready yet.\n";
        }
    } else {
        echo "Unexpected response format.\n";
    }
	
}

// Close cURL
curl_close($ch);
 //redirect(admin_url('contracts/contract/' . $contract_id . '?tab=comparisons&viewurl='.$exporturl));	
	}
 public function generate($contractid,$mode='new') {
		 require_once(APPPATH . 'vendor/tecnickcom/tcpdf/tcpdf.php');
        $contract = $this->contracts_model->get($contractid);//print_r($contract->id);
		 $prompt = "";

        if ($mode === 'new') {
              $clients_data = $this->clients_model->get($contract->client);
        $client_company_name = $clients_data->company;
        $address     = nl2br($clients_data->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $address);
        $address = str_replace('&','&amp;', $address) ;
        $contactno   = $clients_data->phonenumber;
        $email       = $clients_data->email_id;
        $contract_amount = $contract->contract_value;
        $client_company_name = str_replace('&','&amp;', $clients_data->company) ;
        $client_contact= get_primary_contact_user_id($contract->client);
        $emirate   = $clients_data->state;
        $country   = get_country_name($clients_data->country);
        $contract_start_date=date('F d , Y', strtotime($contract->datestart));
        $this->load->model('casediary_model');
        $otherparty_det = $this->casediary_model->get_oppositeparty($contract->other_party);
        $other_party_name= str_replace('&','&amp;',$otherparty_det->name);
        $other_party_address=  str_ireplace($breaks,'</w:t><w:br/><w:t>',$otherparty_det->address).' '.$otherparty_det->city;


        $first_party = $client_company_name;
        $second_party = $other_party_name ?: 'N/A';
        $contract_type = get_contracttype_name_by_id($contract->contract_type);//$this->input->post('contract_type');
        $contract_value = $contract_amount;//$this->input->post('contract_value');
		$first_party_address=$address;
		$first_party_contact=$contactno;
		$second_party_address=$other_party_address;
		$second_party_contact	=$otherparty_det->mobile?:'';
        $prompt = "You are a legal document generator. 
		Produce a COMPLETE TCPDF-compatible HTML contract with headings, tables, and proper formatting.
		Draft a {$contract_type} between {$first_party} and {$second_party} 
        for a total value of {$contract_value}.
		Details:  
- First Party: {$first_party}, Address: {$first_party_address}, Contact: {$first_party_contact}, Role: Seller  
- Second Party: {$second_party}, Address: {$second_party_address}, Contact: {$second_party_contact}, Role: Buyer  

        Include:
        - Party details and definitions
        - Scope of work/terms based on contract type
        - Payment terms
        - Duration and termination clauses
        - Confidentiality (if applicable)
        - Dispute resolution and governing law
        - Signature section for all parties
		
		### Output Rules
1. Content must change depending on {$contract_type}:  
   - If “Sales Contract”: Scope must describe goods, payment terms for purchase, delivery obligations.  
   - If “Service Agreement”: Scope must describe services, SLAs, responsibilities, service periods.  
   - If “Software License Agreement”: Scope must include license grant, restrictions, IP ownership, support.  
2. Use numbered clauses and subclauses (manual numbering).  
3. Insert provided variables directly; no placeholders like [Insert …].  
4. Output must be valid HTML, styled with basic inline CSS suitable for TCPDF.  
5. Always include a signature section for both parties. 
        Format the document with clear headings, numbered clauses, and professional legal language.";
		
		
		 } elseif ($mode === 'review') {
			$drafted_document = $contract->content;

			$prompt = "Review the following legal document and highlight improvement suggestions only:\n\n{$drafted_document}\n\n
			Focus on:
			- Ambiguous or unclear language
			- Missing standard clauses
			- Risk areas or imbalanced obligations
			- Formatting/structural issues
			- Compliance concerns
		Provide output as a bullet-point list grouped by category. Do not rewrite the entire document.";

		 } elseif ($mode === 'improve'|| $mode === 'improve_version') {
            $drafted_document = $contract->content;

            $prompt = "Review and improve the following drafted legal document:\n\n{$drafted_document}\n\n
            Instructions:
            - Ensure clarity and remove ambiguity
            - Strengthen enforceability with precise legal language
            - Check consistency in formatting, headings, numbering, and defined terms
            - Add standard missing clauses (confidentiality, termination, governing law, dispute resolution)
            - Make it concise, professional, and legally sound
            - Output as a structured agreement with clear headings and numbered clauses.";
        }
	//	echo 'prompt:-'.$prompt.'<br>';
        // Call OpenAI API
        $contract_text = $this->call_openai_api($prompt);
		if(!empty($contract_text)){
		  // Save PDF using TCPDF
     
		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->SetCreator('Contract Generator '.get_staff_full_name(get_staff_user_id()));
        $pdf->SetAuthor(get_staff_full_name(get_staff_user_id()));
        $pdf->SetTitle('Agreement'. $contract->subject);
        $pdf->SetMargins(15, 15, 15);
        $pdf->AddPage();

        $pdf->SetFont('dejavusans', '', 10);
        $pdf->writeHTML(nl2br($contract_text), true, false, true, false, '');
 

         $path = get_upload_path_by_type('contract') . $contractid . '/';
		 _maybe_create_upload_path($path);
       
		if($mode=='new' || $mode=='improve'){
		// File path
        $file_name = 'agreement_' . time() . '.pdf';
        $file_path = $path . $file_name;	
		 // Save to uploads folder
        $pdf->Output($file_path, 'F');
		 $this->db->where('id', $contractid);
        $this->db->update(db_prefix() . 'contracts', [
            'content' => nl2br($contract_text),
			'contract_filename'=>$file_name
        ]);	
		}elseif($mode=='review'){
			$this->db->where('id', $contractid);
        $this->db->update(db_prefix() . 'contracts', [
            'review_content' => nl2br($contract_text),
			
        ]);	
		}elseif($mode=='improve_version'){
			  $file_name = 'Ver' .date('Ymdhis') . '.pdf';
        $file_path = $path . $file_name;	
			 // Save to uploads folder
        $pdf->Output($file_path, 'F');
			$current_version = get_current_contract_version($contractid);
			// Already version exists
            if($current_version>=0){
                $version_data['version'] = $current_version+1;
            }else{
			$version_data['version']=1;	
			}

            $version_data['contractid']  =$contractid;  
            $version_data['version_content']  =nl2br($contract_text);  
            $version_data['version_internal_file_path']  =$file_name;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
			$this->db->insert('tblcontract_versions',$version_data);
		}
		}else{
			 set_alert('warning', 'Check validity of AI with Administrator');
		}
		 redirect(admin_url('contracts/contract/' . $contractid ));
      
    }

    private function call_openai_api($prompt) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, "https://api.openai.com/v1/chat/completions");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Content-Type: application/json",
            "Authorization: Bearer {$this->openai_api_key}"
        ));
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array(
            "model" => "gpt-4o-mini",
            "messages" => array(
                array("role" => "system", "content" => "You are a legal document creator."),
                array("role" => "user", "content" => $prompt)
            ),
            "temperature" => 0.2
        )));

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);
        return $result['choices'][0]['message']['content'] ?? 'Error generating contract.';
    }
	public function add_contractamendpdf(){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['contract_id'];
                //unset($data['contractid']);
			$next_number = $this->contracts_model->get_next_amendment_number($id);

            $data['amendment_number']= $next_number;
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] =  get_staff_user_id();   
             $insert_id =$this->contracts_model->add_amendment($data);
           		 
             $message         = '';
            $success=handle_project_contract_amendment_file_upload($id,$insert_id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('contract_amendment')) : '';
					  $updated          = true;
					  $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contracts', [
            'amendment_number' => $data['amendment_number'],
		'last_amendmentdt'=>(isset($data['effective_date'])) ? to_sql_date($data['effective_date']):'',
        ]);	
					  log_activity('Contract Version added [AmendmentID: ' . $insert_id . ', ContractID: ' . $id . ']');
			$this->contracts_model->log_contract_activity($id, 'not_contract_amendment_added');
					/* $contentfile='';
					$userfile1= $this->db->get_where('tblcontract_versions',array('id'=>$insert_id))->row()->version_internal_file_path;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'contracts', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );*/
					 }else{
					 
					  $message = 'Check  Image extension not allowed';
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
	
public function add_contractpostaction(){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['contract_id'];
                //unset($data['contractid']);
			

         
            $data['created_at'] = date('Y-m-d H:i:s');
            $data['created_by'] =  get_staff_user_id();   
             $insert_id =$this->contracts_model->add_postaction($data);
           		 
             $message         = '';
			  $success =false;
            $success=handle_project_contract_postaction_file_upload($id,$insert_id);
				 if ($success == true) {

                $message = $id ? _l('added_successfully', _l('contract_postaction')) : '';
					  $success          = true;
					  $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contracts', [
            'postcontract_status' => $data['category_id'],
			
        ]);	
					  log_activity('Contract Post action added [AmendmentID: ' . $insert_id . ', ContractID: ' . $id . ']');
			$this->contracts_model->log_contract_activity($id, 'not_contract_postaction_added');
					/* $contentfile='';
					$userfile1= $this->db->get_where('tblcontract_versions',array('id'=>$insert_id))->row()->version_internal_file_path;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'contracts', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );*/
					
					$message= _l('contract_postreview_added');
					
				}else{
                     
                      $message = 'Check  Image extension not allowed';
                      $updated          = false;
                 }

			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}
	
	public function add_negotiation()
    {
        if ($this->input->post()) {
            // echo json_encode([
            //     'success' => $this->contracts_model->add_comment($this->input->post()),
            // ]);
              $data    = $this->input->post();
            $success = $this->contracts_model->add_comment($data);
            if ($success) {
			
                set_alert('success', _l('negotiation_added_successfully'));
            } else {
                set_alert('warning', _l('negotiation_added_fail'));
            }
            redirect(admin_url('contracts/contract/' . $data['contract_id'] . '?tab=negotiation'));
        }
    }

   public function summarizeWithAI($contractID)
    {
        $contractText=$this->db->select('content')->from('tblcontracts')->where('id',$contractID)->get()->row()->content;
     $prompt = "You are a senior legal counsel. Summarize the following contract text in clean, structured HTML.
Include these sections if available:
1. Parties Involved
2. Key Obligations
3. Payment Terms
4. Duration/Termination
5. Governing Law
6. Risks or Unusual Clauses

Return ONLY clean HTML — do not wrap inside markdown code fences (no ```html``` or ```).

Contract text:\n" . $contractText;

    $payload = [
        "model" => "gpt-4o-mini",
        "temperature" => 0.3,
        "messages" => [
            ["role" => "system", "content" => "You analyze and summarize legal contracts precisely."],
            ["role" => "user", "content" => $prompt]
        ]
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt_array($ch, [
        CURLOPT_HTTPHEADER => [
            "Authorization: Bearer " . get_option('openai_apikey'),
            "Content-Type: application/json"
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POSTFIELDS => json_encode($payload)
    ]);

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
   $analysis= $data['choices'][0]['message']['content'] ?? 'Unable to generate summary.';


    // --- Clean markdown-style wrappers if AI still adds them ---
    $cleaned = preg_replace('/^```(?:html)?\s*/', '', trim($analysis)); // remove ```html
    $cleaned = preg_replace('/```$/', '', $cleaned); // remove closing ```
    $cleaned = trim($cleaned);

    // --- Fallback: If AI didn’t return HTML, wrap it safely ---
    if (stripos($cleaned, '<p>') === false && stripos($cleaned, '<div>') === false) {
        $cleaned = nl2br(htmlspecialchars($cleaned)); // safe formatting
    }
    $cleaned = str_replace(['<h1>', '</h1>'], ['<h3>', '</h3>'], $cleaned);
    $cleaned = str_replace(['<h2>', '</h2>'], ['<h4>', '</h4>'], $cleaned);

     $this->db->where('id', $contractID);
        $success = $this->db->update('tblcontracts', ['ai_agreement_summary' => $cleaned]);
         if($success){
            echo json_encode(['success' => $success]);
        }else{
            echo json_encode(['success' => false]);

        }
}
public function compareAgreementsHighlight()
{
    $oldText = $this->input->post('old_text');
    $newText = $this->input->post('new_text');
    $contractID = $this->input->post('contractID');

    $prompt = "
You are a senior contract lawyer and text comparison expert.

Compare the following two versions of a contract and show the differences using inline color highlights:
- Additions: wrap added words in <span style='color:green;font-weight:bold;'>…</span>
- Deletions: wrap removed words in <span style='color:red;text-decoration:line-through;'>…</span>
- Keep identical text unchanged.
- Preserve clause numbering and headings.
- Return valid HTML only (no markdown, no code blocks).

Old Version:
\"\"\"$oldText\"\"\"

New Version:
\"\"\"$newText\"\"\"
";

    $payload = [
        "model" => "gpt-4o-mini",
        "temperature" => 0,
        "messages" => [
            ["role" => "system", "content" => "You are a senior legal reviewer performing contract diff analysis."],
            ["role" => "user", "content" => $prompt]
        ]
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . get_option('openai_apikey'),
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $html = $data['choices'][0]['message']['content'] ?? '';
    $this->db->where('id', $contractID);
    $success = $this->db->update('tblcontracts', ['comparison_result' => $html]);
    echo json_encode([
        'status' => 'success',
        'highlight_html' => $html
    ]);
}

public function getVersionInfo() {
         $postData=$this->input->post('versionid');
         $data = $this->contracts_model->get_contractversioninfo($postData);
 
        echo json_encode($data);
    }
    
    public function remove_negotiation($id,$contract_id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'contract_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
              set_alert('warning', _l('negotiation_delete_fail'));
            }
           
                $success = $this->contracts_model->remove_comment($id);
                if($success)
             set_alert('success', _l('negotiation_deleted'));
        } else {
            set_alert('warning', _l('negotiation_deletion_fail'));
        }
        redirect(admin_url('contracts/contract/' . $contract_id . '?tab=negotiation'));
    }
    

public function compare_versions()
{
    $oldText = $this->input->post('old_text');
    $newText = $this->input->post('new_text');
    $contractID = $this->input->post('contractID');

 if (!$oldText || !$newText) {
            echo json_encode(['error' => 'Both versions are required']);
            return;
        }
    $prompt = "
You are a senior contract lawyer and text comparison expert.

Compare the following two versions of a contract and show the differences using inline color highlights:
1. A summary table at the top listing:
   - Added Clauses
   - Removed Clauses
   - Modified Clauses (old vs new one-line difference)
2. Then show a two-column comparison:
   - Left: 'Original Version'
   - Right: 'Revised Version'
- Additions: wrap added words in <span style='color:green;font-weight:bold;'>…</span>
- Deletions: wrap removed words in <span style='color:red;text-decoration:line-through;'>…</span>
- Wrap **added text** in <ins style='background:#d4fcdc;'>…</ins>
- Wrap **deleted text** in <del style='background:#ffd6d6;'>…</del>
- Keep identical text unchanged.
- Preserve clause numbering and headings.
- Return valid HTML only (no markdown, no code blocks).
Output two side-by-side HTML sections labeled 'Original Version' and 'Revised Version'.
Keep formatting consistent for clauses.

Old Version:
\"\"\"$oldText\"\"\"

New Version:
\"\"\"$newText\"\"\"
";

    $payload = [
        "model" => "gpt-4o-mini",
        "temperature" => 0,
        "messages" => [
            ["role" => "system", "content" => "You are a senior legal reviewer performing contract diff analysis."],
            ["role" => "user", "content" => $prompt]
        ]
    ];

    $ch = curl_init("https://api.openai.com/v1/chat/completions");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Bearer " . get_option('openai_apikey'),
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    $response = curl_exec($ch);
    curl_close($ch);

    $data = json_decode($response, true);
    $html = $data['choices'][0]['message']['content'] ?? '';
    $this->db->where('id', $contractID);
    $success = $this->db->update('tblcontracts', ['comparison_result' => $html]);
    echo json_encode([
        'status' => 'success',
        'highlight_html' => $html
    ]);
}
//     public function save_placeholders()
// {
//      $contract_id = $this->input->post('contract_id');
//     $positions = json_decode($this->input->post('positions'), true);

//     if (!$contract_id || empty($positions)) {
//         echo json_encode(['message' => 'Invalid data received']);
//         return;
//     }

//     foreach ($positions as $pos) {
//         $placeholder = json_encode([
//             'x' => $pos['x'],
//             'y' => $pos['y'],
//             'page' => $pos['page']
//         ]);
//         $this->db->where([
//             'rel_id' => $contract_id,
//             'rel_type' => 'contract',
//             'staffid' => $pos['approver_id']
//         ])->update('tblapprovals', ['sign_placeholder' => $placeholder]);
//     }

//     echo json_encode(['message' => 'Signature positions saved successfully!']);
// }
public function view_uploadpdf($contract_id)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }
        // Load model to fetch contract details
         $contract = $this->contracts_model->get($contract_id);

       /* if (!$contract) {
            show_404();
        }*/


        $file_path = FCPATH . 'uploads/contracts/' . $contract->id . '/' . $contract->contract_filename;

        if (!file_exists($file_path)) {
            show_404();
        }

        // Send headers for PDF view
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $contract->contract_filename . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }
    
    
  
  public function save_placeholders()
{
    
    $contract_id = $this->input->post('contract_id');
    $positions = json_decode($this->input->post('positions'), true);

    if (!$contract_id || empty($positions)) {
        echo json_encode(['message' => 'Invalid data received']);
        return;
    }

    foreach ($positions as $pos) {
        $pagesInput = trim($pos['pages']);
        
        // ✅ If blank, save as "all" marker
        if (empty($pagesInput)) {
            $placeholderJson = json_encode([
                [
                    'x' => $pos['x'],
                    'y' => $pos['y'],
                    'page' => 'all' // Special marker for all pages
                ]
            ]);
        } else {
            // Parse specific pages
            $pages = explode(',', $pagesInput);
            $coords = [];

            foreach ($pages as $p) {
                $pageNum = (int)trim($p);
                if ($pageNum > 0) {
                    $coords[] = [
                        'x' => $pos['x'],
                        'y' => $pos['y'],
                        'page' => $pageNum
                    ];
                }
            }

            $placeholderJson = json_encode($coords);
        }

        $this->db->where([
            'rel_id'   => $contract_id,
            'rel_type' => 'contract',
            'staffid'  => $pos['approver_id']
        ])->update('tblapprovals', [
            'sign_placeholder' => $placeholderJson
        ]);
    }

    echo json_encode(['message' => 'Signature positions saved successfully!']);
}
public function save_signature13112025()
{
    $contract_id = $this->input->post('contract_id');
    $user_id = get_staff_user_id();

    $approver = $this->db->where([
        'rel_id'   => $contract_id,
        'rel_type' => 'contract',
        'staffid'  => $user_id
    ])->get('tblapprovals')->row();

    if (!$approver) {
        die(json_encode(['error' => 'Approver not found for this contract']));
    }

    // Serial signing order verification
    $all_approvers = $this->db->where([
        'rel_id'   => $contract_id,
        'rel_type' => 'contract'
    ])->order_by('id', 'ASC')->get('tblapprovals')->result_array();

    $can_sign = false;
    foreach ($all_approvers as $index => $app) {
        if ((int)$app['staffid'] == (int)$user_id) {
            $all_previous_signed = true;
            for ($i = 0; $i < $index; $i++) {
                if ($all_approvers[$i]['status'] !== 'signed') {
                    $all_previous_signed = false;
                    break;
                }
            }
            if ($all_previous_signed && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
                $can_sign = true;
            }
            break;
        }
    }

    if (!$can_sign) {
        die(json_encode(['error' => 'It is not your turn to sign. Please wait for previous approvers.']));
    }

    $placeholder = json_decode($approver->sign_placeholder, true);
    
    if (!is_array($placeholder) || empty($placeholder)) {
        die(json_encode(['error' => 'Invalid or empty signature placeholder coordinates']));
    }
    
    $filePath = null;
    $inc_app_name = isset($approver->inc_app_name) && $approver->inc_app_name == '1';
    $inc_time_stamp = isset($approver->inc_time_stamp) && $approver->inc_time_stamp == '1';

    if (!empty($_FILES['file']['name'])) {
        $filePath = handle_staff_signature_upload($contract_id, $user_id);
    }
    elseif ($this->input->post('signature')) {
        $path = get_upload_path_by_type('contract') . $contract_id . '/';
        if (process_digital_signature_image_contract($this->input->post('signature', false), $path)) {
            $filePath = $GLOBALS['processed_digital_signature'];
        }
    }

    if (!$filePath) {
        die(json_encode(['error' => 'No signature image provided.']));
    }

    $contract = $this->db->where('id', $contract_id)->get('tblcontracts')->row();
    if (!$contract) {
        die(json_encode(['error' => 'Contract not found']));
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $sourcePath = FCPATH . 'uploads/contracts/' . $contract->id . '/' . $contract->contract_filename;

    if (!file_exists($sourcePath)) {
        die(json_encode(['error' => 'PDF source file not found: ' . $sourcePath]));
    }

    $pageCount = $pdf->setSourceFile($sourcePath);
    
    // 🔧 CRITICAL FIX: Frontend scale factor from JavaScript (scale = 1.4)
    $FRONTEND_SCALE = 1.4;
    
    $staff_name = $inc_app_name ? get_staff_full_name($user_id) : '';
    $timestamp = $inc_time_stamp ? date('Y-m-d H:i:s') : '';

    $signatureWidth = 25; // mm
    $signatureHeight = 10; // mm
    $nameHeight = 5; // mm
    $timestampHeight = 4; // mm
    $spacing = 1; // mm

    $signatureFullPath = FCPATH . 'uploads/contracts/' . $contract->id . '/' . $filePath;
    
    if (!file_exists($signatureFullPath)) {
        die(json_encode(['error' => 'Signature file not found']));
    }

    for ($page = 1; $page <= $pageCount; $page++) {
        $tplIdx = $pdf->importPage($page);
        $size = $pdf->getTemplateSize($tplIdx);
        
        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
        $pdf->useTemplate($tplIdx, 0, 0, $size['width'], $size['height']);

        foreach ($placeholder as $coord) {
            $coordPage = isset($coord['page']) ? $coord['page'] : null;
            $shouldPlace = ($coordPage == 'all' || $coordPage == null || (int)$coordPage == (int)$page);
            
            if ($shouldPlace) {
                // 🔧 CONVERSION FORMULA:
                // Frontend saves: canvas pixels at scale 1.4
                // Backend needs: PDF mm coordinates
                //
                // Step 1: Remove frontend scale to get actual PDF pixels
                // Step 2: Convert pixels to mm using PDF.js default 72 DPI
                //         72 DPI means: 1 inch = 72 pixels = 25.4 mm
                //         So: 1 pixel = 25.4/72 = 0.3528 mm
                
                $pixelToMm = 25.4 / 72; // 0.3528 mm per pixel at 72 DPI
                
                // Remove frontend scale, then convert to mm
                $x = ($coord['x'] / $FRONTEND_SCALE) * $pixelToMm;
                $y = ($coord['y'] / $FRONTEND_SCALE) * $pixelToMm;
                
                log_activity("Signature placement Page $page: Canvas({$coord['x']}, {$coord['y']}) -> PDF($x, $y) mm");
                
                // Boundary validation
                if ($x < 0) $x = 10;
                if ($y < 0) $y = 10;
                if ($x > $size['width'] - $signatureWidth - 5) {
                    $x = $size['width'] - $signatureWidth - 5;
                }
                if ($y > $size['height'] - 40) {
                    $y = $size['height'] - 40;
                }
                
                try {
                    // Place signature
                    $pdf->Image(
                        $signatureFullPath,
                        $x, $y, 
                        $signatureWidth
                    );
                    
                    $currentY = $y + $signatureHeight + $spacing;
                    
                    // Add name
                    if ($inc_app_name && !empty($staff_name)) {
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetXY($x, $currentY);
                        $pdf->Cell($signatureWidth, $nameHeight, $staff_name, 0, 0, 'L');
                        $currentY += $nameHeight + $spacing;
                    }
                    
                    // Add timestamp
                    if ($inc_time_stamp && !empty($timestamp)) {
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetTextColor(80, 80, 80);
                        $pdf->SetXY($x, $currentY);
                        $pdf->Cell($signatureWidth, $timestampHeight, $timestamp, 0, 0, 'L');
                    }
                    
                } catch (Exception $e) {
                    log_activity("Failed to place signature: " . $e->getMessage());
                }
            }
        }
    }

    $signedPath = 'uploads/contracts/' . $contract_id . '/signed_' . $contract_id . '_' . $user_id . '.pdf';
    $pdf->Output(FCPATH . $signedPath, 'F');

    $this->db->where(['id' => $contract_id])
             ->update('tblcontracts', [
                 'contract_filename' => 'signed_' . $contract_id . '_' . $user_id . '.pdf'
             ]);

    $contentfile = '';
    $userfile1 = 'signed_' . $contract_id . '_' . $user_id . '.pdf';
    
    $contentfile = $this->projectvesioncontents($userfile1, $contract_id);
    
    $this->db->where('id', $contract_id);
    $this->db->update(db_prefix() . 'contracts', [
        'content' => $contentfile,
    ]);
    
    $current_version = get_current_contract_version($contract_id);
    if ($current_version >= 0) {
        $version_data['version'] = $current_version + 1;
    } else {
        $version_data['version'] = 1;
    }

    $version_data['contractid'] = $contract_id;
    $version_data['version_content'] = nl2br($contentfile);
    $version_data['version_internal_file_path'] = $userfile1;
    $version_data['dateadded'] = date('Y-m-d H:i:s');
    $version_data['addedby'] = get_staff_user_id();
    $this->db->insert('tblcontract_versions', $version_data);

    $this->db->where([
        'rel_id' => $contract_id,
        'rel_type' => 'contract',
        'staffid' => $user_id
    ])->update('tblapprovals', [
        'signature_path' => $filePath,
        'status' => 'signed',
        'approval_status' => '3',
        'signed_at' => date('Y-m-d H:i:s')
    ]);

    // Find and notify next approver
$next_approver = null;
$found_current = false;

foreach ($all_approvers as $app) {
    if ($found_current && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
        $next_approver = $app;
        break;
    }
    if ((int)$app['staffid'] == (int)$user_id) {
        $found_current = true;
    }
}

if ($next_approver) {
    $this->load->model('emails_model');
    $next_staff = get_staff($next_approver['staffid']);
    
    if ($next_staff) {
        // Check approval_heading_id to determine email subject and link
        $is_external_review = isset($next_approver['approval_heading_id']) && (int)$next_approver['approval_heading_id'] === 11;
        
        if ($is_external_review) {
            $contract_link = admin_url('admin/contracts/contract_external_review/' . $contract_id);
            $email_subject = _l('Contract Ready for Your Review');
        } else {
            $contract_link = admin_url('admin/contracts/contract/' . $contract_id);
            $email_subject = _l('Contract is Ready for Your Signature');
        }
        
        $signer_name = get_staff_full_name($user_id);
        
        $message = "Hello " . $next_staff->firstname . ",<br><br>";
        $message .= "The contract <strong>" . $contract->subject . "</strong> has been signed by " . $signer_name . ".<br><br>";
        
        if ($is_external_review) {
            $message .= "It is now your turn to review the contract.<br><br>";
        } else {
            $message .= "It is now your turn to review and sign the contract.<br><br>";
        }
        
        $message .= "Please click the link below to view ";
        $message .= $is_external_review ? "the contract:<br>" : "and sign the contract:<br>";
        $message .= '<a href="' . $contract_link . '">' . $contract_link . '</a><br><br>';
        $message .= "Thank you.";
        
        $this->emails_model->send_simple_email(
            $next_staff->email,
            $email_subject,
            $message
        );
    }
}

    echo json_encode([
        'success' => true,
        'message' => 'Signature added successfully',
        'file' => $signedPath,
        'next_approver_notified' => $next_approver ? true : false
    ]);
}
public function save_signatureold()
{
    $contract_id = $this->input->post('contract_id');
    $user_id = get_staff_user_id();

    // Get approver data (coords and checkbox preferences)
    $approver = $this->db->where([
        'rel_id'   => $contract_id,
        'rel_type' => 'contract',
        'staffid'  => $user_id
    ])->get('tblapprovals')->row();

    if (!$approver) {
        die(json_encode(['error' => 'Approver not found for this contract']));
    }

    // ✅ Verify serial signing order
    $all_approvers = $this->db->where([
        'rel_id'   => $contract_id,
        'rel_type' => 'contract'
    ])->order_by('id', 'ASC')->get('tblapprovals')->result_array();

    // Find current user's position and check if they can sign
    // $can_sign = false;
    // $user_position = -1;
    
    // foreach ($all_approvers as $index => $app) {
    //     if ((int)$app['staffid'] === (int)$user_id) {
    //         $user_position = $index;
            
    //         // Check if all previous approvers have signed
    //         $all_previous_signed = true;
    //         for ($i = 0; $i < $index; $i++) {
    //             if ($all_approvers[$i]['status'] !== 'signed') {
    //                 $all_previous_signed = false;
    //                 break;
    //             }
    //         }
            
    //         if ($all_previous_signed && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
    //             $can_sign = true;
    //         }
    //         break;
    //     }
    // }

    // if (!$can_sign) {
    //     die(json_encode(['error' => 'It is not your turn to sign. Please wait for previous approvers.']));
    // }

    $placeholder = json_decode($approver->sign_placeholder, true);
    $filePath = null;

    // Get checkbox preferences
    $inc_app_name = isset($approver->inc_app_name) && $approver->inc_app_name == '1';
    $inc_time_stamp = isset($approver->inc_time_stamp) && $approver->inc_time_stamp == '1';

    // Handle uploaded signature image
    if (!empty($_FILES['file']['name'])) {
        $filePath = handle_staff_signature_upload($contract_id, $user_id);
    }
    // Handle drawn signature
    elseif ($this->input->post('signature')) {
        $path = get_upload_path_by_type('contract') . $contract_id . '/';
        if (process_digital_signature_image_contract($this->input->post('signature', false), $path)) {
            $filePath = $GLOBALS['processed_digital_signature'];
        }
    }

    if (!$filePath) {
        die(json_encode(['error' => 'No signature image provided.']));
    }

    // Load contract
    $contract = $this->db->where('id', $contract_id)->get('tblcontracts')->row();
    if (!$contract) {
        die(json_encode(['error' => 'Contract not found']));
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $sourcePath = FCPATH . 'uploads/contracts/' . $contract->id . '/' . $contract->contract_filename;

    if (!file_exists($sourcePath)) {
        die(json_encode(['error' => 'PDF source file not found: ' . $sourcePath]));
    }

    $pageCount = $pdf->setSourceFile($sourcePath);
    $scale = 0.264583; // 1px ≈ 0.264583 mm
    
    // Get staff name if needed
    $staff_name = '';
    if ($inc_app_name) {
        $staff_name = get_staff_full_name($user_id);
    }
    
    // Get current timestamp if needed
    $timestamp = '';
    if ($inc_time_stamp) {
        $timestamp = date('Y-m-d H:i:s');
    }

    // Define dimensions
    $signatureWidth = 25; // mm
    $signatureHeight = 10; // mm (approximate height of signature)
    $nameHeight = 5; // mm (height for name text)
    $timestampHeight = 4; // mm (height for timestamp text)
    $spacing = 1; // mm (spacing between elements)

    for ($page = 1; $page <= $pageCount; $page++) {
        $tplIdx = $pdf->importPage($page);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx, 0, 0, 210, 297);

        // Place the signature on coordinates
        if (is_array($placeholder)) {
            foreach ($placeholder as $coord) {
                if ($coord['page'] === 'all' || (int)$page === (int)$coord['page']) {
                    $x = $coord['x'] * $scale;
                    $y = $coord['y'] * $scale;

                    // 1. Signature at the original position
                    $pdf->Image(
                        FCPATH . 'uploads/contracts/' . $contract->id . '/' . $filePath,
                        $x, $y, 
                        $signatureWidth
                    );
                    
                    $currentY = $y + $signatureHeight + $spacing;
                    
                    // 2. Add approver name below signature if requested
                    if ($inc_app_name && !empty($staff_name)) {
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetXY($x, $currentY);
                        $pdf->Cell($signatureWidth, $nameHeight, $staff_name, 0, 0, 'L');
                        $currentY += $nameHeight + $spacing;
                    }
                    
                    // 3. Add timestamp below name (or below signature if no name) if requested
                    if ($inc_time_stamp && !empty($timestamp)) {
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetTextColor(80, 80, 80);
                        $pdf->SetXY($x, $currentY);
                        $pdf->Cell($signatureWidth, $timestampHeight, $timestamp, 0, 0, 'L');
                    }
                }
            }
        }
    }

    $signedPath = 'uploads/contracts/' . $contract_id . '/signed_' . $contract_id . '_' . $user_id . '.pdf';
    $pdf->Output(FCPATH . $signedPath, 'F');

    // Update contract filename
    $this->db->where(['id' => $contract_id])
             ->update('tblcontracts', [
                 'contract_filename' => 'signed_' . $contract_id . '_' . $user_id . '.pdf'
             ]);

    $contentfile = '';
    $userfile1 = 'signed_' . $contract_id . '_' . $user_id . '.pdf';
    
    $contentfile = $this->projectvesioncontents($userfile1, $contract_id);
    
    $this->db->where('id', $contract_id);
    $this->db->update(db_prefix() . 'contracts', [
        'content' => $contentfile,
    ]);
    
    $current_version = get_current_contract_version($contract_id);
    // Already version exists
    if ($current_version >= 0) {
        $version_data['version'] = $current_version + 1;
    } else {
        $version_data['version'] = 1;
    }

    $version_data['contractid'] = $contract_id;
    $version_data['version_content'] = nl2br($contentfile);
    $version_data['version_internal_file_path'] = $userfile1;
    $version_data['dateadded'] = date('Y-m-d H:i:s');
    $version_data['addedby'] = get_staff_user_id();
    $this->db->insert('tblcontract_versions', $version_data);

    // Update approver record as signed
    $this->db->where([
        'rel_id' => $contract_id,
        'rel_type' => 'contract',
        'staffid' => $user_id
    ])->update('tblapprovals', [
        'signature_path' => $filePath,
        'status' => 'signed',
        'approval_status' => '3',
        'signed_at' => date('Y-m-d H:i:s')
    ]);

      // Find and notify next approver
$next_approver = null;
$found_current = false;

foreach ($all_approvers as $app) {
    if ($found_current && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
        $next_approver = $app;
        break;
    }
    if ((int)$app['staffid'] == (int)$user_id) {
        $found_current = true;
    }
}

if ($next_approver) {
    $this->load->model('emails_model');
    $next_staff = get_staff($next_approver['staffid']);
    
    if ($next_staff) {
        // Check approval_heading_id to determine email subject and link
        $is_external_review = isset($next_approver['approval_heading_id']) && (int)$next_approver['approval_heading_id'] === 11;
        
        if ($is_external_review) {
            $contract_link = admin_url('contracts/contract_external_review/' . $contract_id);
            $email_subject = _l('Contract Ready for Your Review');
        } else {
            $contract_link = admin_url('contracts/contract/' . $contract_id);
            $email_subject = _l('Contract Ready for Your Signature');
        }
        
        $signer_name = get_staff_full_name($user_id);
        
        $message = "Hello " . $next_staff->firstname . ",<br><br>";
        $message .= "The contract <strong>" . $contract->subject . "</strong> has been signed by " . $signer_name . ".<br><br>";
        
        if ($is_external_review) {
            $message .= "It is now your turn to review the contract.<br><br>";
        } else {
            $message .= "It is now your turn to review and sign the contract.<br><br>";
        }
        
        $message .= "Please click the link below to view ";
        $message .= $is_external_review ? "the contract:<br>" : "and sign the contract:<br>";
        $message .= '<a href="' . $contract_link . '">' . $contract_link . '</a><br><br>';
        $message .= "Thank you.";
        
        $this->emails_model->send_simple_email(
            $next_staff->email,
            $email_subject,
            $message
        );
    }
}

    echo json_encode([
        'success' => true,
        'message' => 'Signature added successfully',
        'file' => $signedPath,
        'next_approver_notified' => $next_approver ? true : false
    ]);
}

public function save_signature()
{
    $type=$this->input->post('type');
    if ($type == 'contracts') {
        $clientType = 'contract';   
    } else {
        $clientType = $type;       
    }
    $contract_id = $this->input->post('contract_id');
    $user_id = get_staff_user_id();

    // Get approver data (coords and checkbox preferences)
    $approver = $this->db->where([
        'rel_id'   => $contract_id,
        'rel_type' => $clientType,
        'staffid'  => $user_id
    ])->get('tblapprovals')->row();

    if (!$approver) {
        die(json_encode(['error' => 'Approver not found for this contract']));
    }

    // ✅ Verify serial signing order
    $all_approvers = $this->db->where([
        'rel_id'   => $contract_id,
        'rel_type' => $clientType
    ])->order_by('id', 'ASC')->get('tblapprovals')->result_array();

    // Find current user's position and check if they can sign
    // $can_sign = false;
    // $user_position = -1;
    
    // foreach ($all_approvers as $index => $app) {
    //     if ((int)$app['staffid'] === (int)$user_id) {
    //         $user_position = $index;
            
    //         // Check if all previous approvers have signed
    //         $all_previous_signed = true;
    //         for ($i = 0; $i < $index; $i++) {
    //             if ($all_approvers[$i]['status'] !== 'signed') {
    //                 $all_previous_signed = false;
    //                 break;
    //             }
    //         }
            
    //         if ($all_previous_signed && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
    //             $can_sign = true;
    //         }
    //         break;
    //     }
    // }

    // if (!$can_sign) {
    //     die(json_encode(['error' => 'It is not your turn to sign. Please wait for previous approvers.']));
    // }

    $placeholder = json_decode($approver->sign_placeholder, true);
    $filePath = null;

    // Get checkbox preferences
    $inc_app_name = isset($approver->inc_app_name) && $approver->inc_app_name == '1';
    $inc_time_stamp = isset($approver->inc_time_stamp) && $approver->inc_time_stamp == '1';

    // Handle uploaded signature image
    if (!empty($_FILES['file']['name'])) {
        $filePath = handle_staff_signature_upload($contract_id, $user_id);
    }
    // Handle drawn signature
    elseif ($this->input->post('signature')) {
        $path = get_upload_path_by_type('contract') . $contract_id . '/';
        if (process_digital_signature_image_contract($this->input->post('signature', false), $path)) {
            $filePath = $GLOBALS['processed_digital_signature'];
        }
    }

    if (!$filePath) {
        die(json_encode(['error' => 'No signature image provided.']));
    }

    // Load contract
    $contract = $this->db->where('id', $contract_id)->get('tblcontracts')->row();
    if (!$contract) {
        die(json_encode(['error' => 'Contract not found']));
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $sourcePath = FCPATH . 'uploads/contracts/' . $contract->id . '/' . $contract->contract_filename;

    if (!file_exists($sourcePath)) {
        die(json_encode(['error' => 'PDF source file not found: ' . $sourcePath]));
    }

    $pageCount = $pdf->setSourceFile($sourcePath);
    $scale = 0.264583; // 1px ≈ 0.264583 mm
    
    // Get staff name if needed
    $staff_name = '';
    if ($inc_app_name) {
        $staff_name = get_staff_full_name($user_id);
    }
    
    // Get current timestamp if needed
    $timestamp = '';
    if ($inc_time_stamp) {
        $timestamp = date('Y-m-d H:i:s');
    }

    // Define dimensions
    $signatureWidth = 25; // mm
    $signatureHeight = 10; // mm (approximate height of signature)
    $nameHeight = 5; // mm (height for name text)
    $timestampHeight = 4; // mm (height for timestamp text)
    $spacing = 1; // mm (spacing between elements)

    for ($page = 1; $page <= $pageCount; $page++) {
        $tplIdx = $pdf->importPage($page);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx, 0, 0, 210, 297);

        // Place the signature on coordinates
        if (is_array($placeholder)) {
            foreach ($placeholder as $coord) {
                if ($coord['page'] === 'all' || (int)$page === (int)$coord['page']) {
                    $x = $coord['x'] * $scale;
                    $y = $coord['y'] * $scale;

                    // 1. Signature at the original position
                    $pdf->Image(
                        FCPATH . 'uploads/contracts/' . $contract->id . '/' . $filePath,
                        $x, $y, 
                        $signatureWidth
                    );
                    
                    $currentY = $y + $signatureHeight + $spacing;
                    
                    // 2. Add approver name below signature if requested
                    if ($inc_app_name && !empty($staff_name)) {
                        $pdf->SetFont('Arial', 'B', 9);
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->SetXY($x, $currentY);
                        $pdf->Cell($signatureWidth, $nameHeight, $staff_name, 0, 0, 'L');
                        $currentY += $nameHeight + $spacing;
                    }
                    
                    // 3. Add timestamp below name (or below signature if no name) if requested
                    if ($inc_time_stamp && !empty($timestamp)) {
                        $pdf->SetFont('Arial', '', 8);
                        $pdf->SetTextColor(80, 80, 80);
                        $pdf->SetXY($x, $currentY);
                        $pdf->Cell($signatureWidth, $timestampHeight, $timestamp, 0, 0, 'L');
                    }
                }
            }
        }
    }

    $signedPath = 'uploads/contracts/' . $contract_id . '/signed_' . $contract_id . '_' . $user_id . '.pdf';
    $pdf->Output(FCPATH . $signedPath, 'F');

    // Update contract filename
    $this->db->where(['id' => $contract_id])
             ->update('tblcontracts', [
                 'contract_filename' => 'signed_' . $contract_id . '_' . $user_id . '.pdf'
             ]);

    $contentfile = '';
    $userfile1 = 'signed_' . $contract_id . '_' . $user_id . '.pdf';
    
    $contentfile = $this->projectvesioncontents($userfile1, $contract_id);
    
    $this->db->where('id', $contract_id);
    $this->db->update(db_prefix() . 'contracts', [
        'content' => $contentfile,
    ]);
    
    $current_version = get_current_contract_version($contract_id);
    // Already version exists
    if ($current_version >= 0) {
        $version_data['version'] = $current_version + 1;
    } else {
        $version_data['version'] = 1;
    }

    $version_data['contractid'] = $contract_id;
    $version_data['version_content'] = nl2br($contentfile);
    $version_data['version_internal_file_path'] = $userfile1;
    $version_data['dateadded'] = date('Y-m-d H:i:s');
    $version_data['addedby'] = get_staff_user_id();
    $this->db->insert('tblcontract_versions', $version_data);

    // Update approver record as signed
    $this->db->where([
        'rel_id' => $contract_id,
        'rel_type' => $clientType,
        'staffid' => $user_id
    ])->update('tblapprovals', [
        'signature_path' => $filePath,
        'status' => 'signed',
        'approval_status' => '3',
        'signed_at' => date('Y-m-d H:i:s')
    ]);

      // Find and notify next approver
$next_approver = null;
$found_current = false;

foreach ($all_approvers as $app) {
    if ($found_current && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
        $next_approver = $app;
        break;
    }
    if ((int)$app['staffid'] == (int)$user_id) {
        $found_current = true;
    }
}

if ($next_approver) {
    $this->load->model('emails_model');
    $next_staff = get_staff($next_approver['staffid']);
    
    if ($next_staff) {
        // Check approval_heading_id to determine email subject and link
        $is_external_review = isset($next_approver['approval_heading_id']) && (int)$next_approver['approval_heading_id'] === 11;
        
        if ($is_external_review) {
            $contract_link = admin_url('contracts/contract_external_review/' . $contract_id);
            $email_subject = _l('Contract Ready for Your Review');
        } else {
            $contract_link = admin_url('contracts/contract/' . $contract_id);
            $email_subject = _l('Contract Ready for Your Signature');
        }
        
        $signer_name = get_staff_full_name($user_id);
        
        $message = "Hello " . $next_staff->firstname . ",<br><br>";
        $message .= "The '.$clientType.' <strong>" . $contract->subject . "</strong> has been signed by " . $signer_name . ".<br><br>";
        
        if ($is_external_review) {
            $message .= "It is now your turn to review the '.$clientType.'.<br><br>";
        } else {
            $message .= "It is now your turn to review and sign the .'$clientType.'.<br><br>";
        }
        
        $message .= "Please click the link below to view ";
        $message .= $is_external_review ? "the contract:<br>" : "and sign the contract:<br>";
        $message .= '<a href="' . $contract_link . '">' . $contract_link . '</a><br><br>';
        $message .= "Thank you.";
        
        $this->emails_model->send_simple_email(
            $next_staff->email,
            $email_subject,
            $message
        );
    }
}

    echo json_encode([
        'success' => true,
        'message' => 'Signature added successfully',
        'file' => $signedPath,
        'next_approver_notified' => $next_approver ? true : false
    ]);
}
public function save_stamp_placeholder()
{
    $contract_id = $this->input->post('contract_id');
    $positions = json_decode($this->input->post('positions'), true);

    if (!$contract_id) {
        echo json_encode(['message' => 'Invalid contract ID']);
        return;
    }

    $placeholderJson = null;

    if (!empty($positions)) {
        $pos = $positions[0]; // Only one stamp
        $pagesInput = trim($pos['pages']);
        
        // ✅ If blank, save as "all" marker
        if (empty($pagesInput)) {
            $placeholderJson = json_encode([
                [
                    'x' => $pos['x'],
                    'y' => $pos['y'],
                    'page' => 'all'
                ]
            ]);
        } else {
            // Parse specific pages
            $pages = explode(',', $pagesInput);
            $coords = [];

            foreach ($pages as $p) {
                $pageNum = (int)trim($p);
                if ($pageNum > 0) {
                    $coords[] = [
                        'x' => $pos['x'],
                        'y' => $pos['y'],
                        'page' => $pageNum
                    ];
                }
            }

            $placeholderJson = json_encode($coords);
        }
    }

    $this->db->where('id', $contract_id)
             ->update('tblcontracts', [
                 'stamp_placeholder' => $placeholderJson
             ]);

    echo json_encode(['message' => 'Stamp position saved successfully!']);
}


public function get_contractsub_by_category_id_ajax($cateid='')
 {
     //$res = get_contractsubcategories($cateid);
     //print_r($res);
         echo json_encode(get_contractsubcategories($cateid));
 }
 
// public function save_all_placeholders()
// {
//     $contract_id = $this->input->post('contract_id');
//     $signature_positions = json_decode($this->input->post('signature_positions'), true);
//     $stamp_positions = json_decode($this->input->post('stamp_positions'), true);
//     $stamp_pages = trim($this->input->post('stamp_pages'));

//     if (!$contract_id) {
//         echo json_encode(['message' => 'Invalid contract ID']);
//         return;
//     }

//     // ✅ Save signature positions
//     if (!empty($signature_positions)) {
//         foreach ($signature_positions as $pos) {
//             $pagesInput = trim($pos['pages']);
//             $allCoords = [];
            
//             foreach ($pos['coords'] as $coord) {
//                 if (empty($pagesInput)) {
//                     $allCoords[] = [
//                         'x' => $coord['x'],
//                         'y' => $coord['y'],
//                         'page' => 'all'
//                     ];
//                 } else {
//                     $pages = explode(',', $pagesInput);
//                     foreach ($pages as $p) {
//                         $pageNum = (int)trim($p);
//                         if ($pageNum > 0) {
//                             $allCoords[] = [
//                                 'x' => $coord['x'],
//                                 'y' => $coord['y'],
//                                 'page' => $pageNum
//                             ];
//                         }
//                     }
//                 }
//             }

//             $placeholderJson = json_encode($allCoords);

//             $this->db->where([
//                 'rel_id'   => $contract_id,
//                 'rel_type' => 'contract',
//                 'staffid'  => $pos['approver_id']
//             ])->update('tblapprovals', [
//                 'sign_placeholder' => $placeholderJson
//             ]);
//         }
//     }

//     // ✅ Save stamp positions
//     $stampPlaceholderJson = null;
//     if (!empty($stamp_positions)) {
//         $allStampCoords = [];
        
//         foreach ($stamp_positions as $pos) {
//             if (empty($stamp_pages)) {
//                 $allStampCoords[] = [
//                     'x' => $pos['x'],
//                     'y' => $pos['y'],
//                     'page' => 'all'
//                 ];
//             } else {
//                 $pages = explode(',', $stamp_pages);
//                 foreach ($pages as $p) {
//                     $pageNum = (int)trim($p);
//                     if ($pageNum > 0) {
//                         $allStampCoords[] = [
//                             'x' => $pos['x'],
//                             'y' => $pos['y'],
//                             'page' => $pageNum
//                         ];
//                     }
//                 }
//             }
//         }

//         $stampPlaceholderJson = json_encode($allStampCoords);
//     }

//     $this->db->where('id', $contract_id)
//              ->update('tblcontracts', [
//                  'stamp_placeholder' => $stampPlaceholderJson
//              ]);

//     echo json_encode(['message' => 'All signature and stamp positions saved successfully!']);
// }

public function save_all_placeholders()
{
    $type=$this->input->post('type');
    $contract_id = $this->input->post('contract_id');
    $signature_positions = json_decode($this->input->post('signature_positions'), true);
    $stamp_positions = json_decode($this->input->post('stamp_positions'), true);
    $stamp_pages = trim($this->input->post('stamp_pages'));
    $has_stamp_boxes = $this->input->post('has_stamp_boxes');
    $stamp_removed = $this->input->post('stamp_removed');
    
    if (!$contract_id) {
        echo json_encode(['message' => 'Invalid contract ID']);
        return;
    }
    
    // Save signature positions - Including cleared ones with checkbox states
    if (!empty($signature_positions)) {
        foreach ($signature_positions as $pos) {
            $pagesInput = trim($pos['pages']);
            $allCoords = [];
            
            // Get checkbox values (default to '0' if not provided)
            $inc_app_name = isset($pos['inc_app_name']) ? $pos['inc_app_name'] : '0';
            $inc_time_stamp = isset($pos['inc_time_stamp']) ? $pos['inc_time_stamp'] : '0';
            
            // If coords is empty, we're clearing this approver's placeholder
            if (!empty($pos['coords'])) {
                foreach ($pos['coords'] as $coord) {
                    if (empty($pagesInput)) {
                        $allCoords[] = [
                            'x' => $coord['x'],
                            'y' => $coord['y'],
                            'page' => 'all'
                        ];
                    } else {
                        $pages = explode(',', $pagesInput);
                        foreach ($pages as $p) {
                            $pageNum = (int)trim($p);
                            if ($pageNum > 0) {
                                $allCoords[] = [
                                    'x' => $coord['x'],
                                    'y' => $coord['y'],
                                    'page' => $pageNum
                                ];
                            }
                        }
                    }
                }
            }
            
            // Set to empty array if cleared, or JSON if has coords
            $placeholderJson = empty($allCoords) ? '[]' : json_encode($allCoords);
            
            // Update this specific approver's placeholder with checkbox states
            if($type=='contracts')
                $type='contract';
            $this->db->where([
                'rel_id'   => $contract_id,
                'rel_type' => $type,
                'staffid'  => $pos['approver_id']
            ])->update('tblapprovals', [
                'sign_placeholder' => $placeholderJson,
                'inc_app_name' => $inc_app_name,
                'inc_time_stamp' => $inc_time_stamp
            ]);
        }
    }
    
    // Save stamp positions - Only if stamp was modified
    if ($has_stamp_boxes === 'true') {
        $stampPlaceholderJson = '[]'; // Default to empty if removed
        
        if (!empty($stamp_positions)) {
            $allStampCoords = [];
            
            foreach ($stamp_positions as $pos) {
                if (empty($stamp_pages)) {
                    $allStampCoords[] = [
                        'x' => $pos['x'],
                        'y' => $pos['y'],
                        'page' => 'all'
                    ];
                } else {
                    $pages = explode(',', $stamp_pages);
                    foreach ($pages as $p) {
                        $pageNum = (int)trim($p);
                        if ($pageNum > 0) {
                            $allStampCoords[] = [
                                'x' => $pos['x'],
                                'y' => $pos['y'],
                                'page' => $pageNum
                            ];
                        }
                    }
                }
            }
            if (!empty($allStampCoords)) {
                $stampPlaceholderJson = json_encode($allStampCoords);
            }
        }
        
        // Update stamp (either with new positions or cleared as '[]')
        $this->db->where('id', $contract_id)
                 ->update('tblcontracts', [
                     'stamp_placeholder' => $stampPlaceholderJson
                 ]);
    }
    
    echo json_encode(['message' => 'All signature and stamp positions saved successfully!']);
}

  public function view_upload_versionpdf($contract_id,$versionid)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }
       $versionname=$this->db->get_where('tblcontract_versions', array('contractid'=>$contract_id,'id'=>$versionid))->row()->version_internal_file_path;
        $file_path = FCPATH . 'uploads/contracts/' . $contract_id . '/' . $versionname;

        if (!file_exists($file_path)) {
            show_404();
        }

        // Send headers for PDF view
        header('Content-Type: application/pdf');
        header('Content-Disposition: inline; filename="' . $versionname . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }
    
   public function delete_amendment($amend_id, $contractid)
    {
        $success = $this->contracts_model->delete_amendment($amend_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_amend_deleted'));
        } else {
            set_alert('warning', _l('contract_amend_delete_fail'));
        }
        redirect(admin_url('contracts/contract/' . $contractid . '?tab=tab_amendment'));
    }

     public function delete_postaction($action_id, $contractid)
    {
        $success = $this->contracts_model->delete_postaction($action_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_postaction_deleted'));
        } else {
            set_alert('warning', _l('contract_postaction_delete_fail'));
        }
        redirect(admin_url('contracts/contract/' . $contractid . '?tab=tab_overview'));
    }
    
    public function save_rejection()
{
    // Check if user is logged in
    // if (!is_staff_logged_in()) {
    //     header('Content-Type: application/json');
    //     echo json_encode([
    //         'success' => false,
    //         'message' => 'Unauthorized access.'
    //     ]);
    //     return;
    // }
    
    $contract_id = $this->input->post('contract_id');
    $rejected_reason = $this->input->post('rejected_reason');
    $user_id = get_staff_user_id();
    
    // Validate inputs
    if (empty($contract_id) || empty($rejected_reason)) {
        // header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Contract ID and rejection reason are required.'
        ]);
        return;
    }
    
    // Verify that the user is an approver for this contract
    $existing_approval = $this->db->where([
        'rel_id' => $contract_id,
        'rel_type' => 'contract',
        'staffid' => $user_id
    ])->get('tblapprovals')->row();
    
    if (!$existing_approval) {
        // header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'You are not authorized to reject this contract.'
        ]);
        return;
    }
    
    // Check if already signed or rejected
    if (in_array($existing_approval->status, ['signed', 'rejected'])) {
        // header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'This contract has already been ' . $existing_approval->status . '.'
        ]);
        return;
    }
    
    // Update the approval record
    $update_data = [
        'approval_status'=>5,
        'status' => 'rejected',
        'rejected_reason' => $rejected_reason,
        'rejected_date' => date('Y-m-d H:i:s') // Optional: track when it was rejected
    ];
    
    $this->db->where([
        'rel_id' => $contract_id,
        'rel_type' => 'contract',
        'staffid' => $user_id
    ])->update('tblapprovals', $update_data);
    
    if ($this->db->affected_rows() > 0) {
        // Optional: Log activity
        // $this->load->model('contracts_model');
        // logActivity('Contract Rejection [Contract ID: ' . $contract_id . ']');
        
        // Optional: Send notification or email
        // $this->send_rejection_notification($contract_id, $user_id, $rejected_reason);
        
        // header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Contract has been rejected successfully.'
        ]);
    } else {
        // header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update rejection status.'
        ]);
    }
}

// Optional: Add this helper method if you want to send notifications
public function send_rejection_notification($contract_id, $user_id, $reason)
{
    // Load necessary models
    $this->load->model('contracts_model');
    $this->load->model('staff_model');
    
    $contract = $this->contracts_model->get($contract_id);
    $staff = $this->staff_model->get($user_id);
    
    // Send email to contract creator or admin
    // Implement your notification logic here
}
public function save_stamp()
{
    $contract_id = $this->input->post('contract_id');
    $user_id = get_staff_user_id();

    // ✅ Load contract
    $contract = $this->db->where('id', $contract_id)->get('tblcontracts')->row();
    if (!$contract) {
        die(json_encode(['error' => 'Contract not found']));
    }

    // ✅ Get stamp placeholder
    $stampPlaceholder = json_decode($contract->stamp_placeholder, true);
    
    if (empty($stampPlaceholder) || !is_array($stampPlaceholder)) {
        die(json_encode(['error' => 'No stamp placeholder found. Please add stamp position first.']));
    }

    // ✅ Check if stamp file exists
    $stampPath = FCPATH . 'uploads/company/signature.png';
    if (!file_exists($stampPath)) {
        die(json_encode(['error' => 'Company stamp file not found at: uploads/company/signature.png']));
    }

    $pdf = new \setasign\Fpdi\Fpdi();
    $sourcePath = FCPATH . 'uploads/contracts/' . $contract->id . '/' . $contract->contract_filename;

    if (!file_exists($sourcePath)) {
        die(json_encode(['error' => 'PDF source file not found']));
    }

    $pageCount = $pdf->setSourceFile($sourcePath);
    $scale = 0.264583; // 1px ≈ 0.264583 mm

    for ($page = 1; $page <= $pageCount; $page++) {
        $tplIdx = $pdf->importPage($page);
        $pdf->AddPage();
        $pdf->useTemplate($tplIdx, 0, 0, 210, 297);

        // ✅ Place the stamp on coordinates
        foreach ($stampPlaceholder as $coord) {
            if ($coord['page'] === 'all' || (int)$page === (int)$coord['page']) {
                $x = $coord['x'] * $scale;
                $y = $coord['y'] * $scale;

                // ✅ Stamp: 23mm width
                $pdf->Image(
                    $stampPath, 
                    $x, $y, 
                    23
                );
            }
        }
    }

    $stampedPath = 'uploads/contracts/' . $contract_id . '/stamped_' . $contract_id . '_' . time() . '.pdf';
    $pdf->Output(FCPATH . $stampedPath, 'F');

    // ✅ Update contract filename
    $stampedFilename = basename($stampedPath);
    $this->db->where(['id' => $contract_id])
             ->update('tblcontracts', [
                 'contract_filename' => $stampedFilename
             ]);

    $contentfile = '';
    $contentfile = $this->projectvesioncontents($stampedFilename, $contract_id);
    
    $this->db->where('id', $contract_id);
    $this->db->update(db_prefix() . 'contracts', [
        'content' => $contentfile,
    ]);
    
    $current_version = get_current_contract_version($contract_id);
    // Already version exists
    if ($current_version >= 0) {
        $version_data['version'] = $current_version + 1;
    } else {
        $version_data['version'] = 1;
    }

    $version_data['contractid'] = $contract_id;
    $version_data['version_content'] = nl2br($contentfile);
    $version_data['version_internal_file_path'] = $stampedFilename;
    $version_data['dateadded'] = date('Y-m-d H:i:s');
    $version_data['addedby'] = get_staff_user_id();
    $this->db->insert('tblcontract_versions', $version_data);

    // ✅ Optional: Log stamp application
    // $this->db->insert('tblcontract_stamp_log', [
    //     'contract_id' => $contract_id,
    //     'applied_by' => $user_id,
    //     'applied_at' => date('Y-m-d H:i:s')
    // ]);

    echo json_encode([
        'success' => true,
        'message' => 'Company stamp applied successfully',
        'file' => $stampedPath
    ]);
}


public function contract_external_review($id=''){
    $data['contract']                 = $this->contracts_model->get($id, [], true);
$this->load->view('admin/contracts/contract_external_review', $data);
}

public function review_pdf($id,$type)
{
    $staff_id = get_staff_user_id();
    $type = ($type == 'contracts') ? 'contract' : $type;

    // Update approval_status = 6 for this contract and staff
    $this->db->where('rel_id', $id);
    $this->db->where('staffid', $staff_id);
    $this->db->where('approval_heading_id', 11);
    $this->db->update('tblapprovals', [
        'approval_status' => 7,
        'status' => 'reviewed'
    ]);
     $contract = $this->db->where('id', $id)->get('tblcontracts')->row();
    $all_approvers = $this->db->where([
        'rel_id'   => $id,
        'rel_type' => $type
    ])->order_by('id', 'ASC')->get('tblapprovals')->result_array();// Find and notify next approver
$next_approver = null;
$found_current = false;

foreach ($all_approvers as $app) {
    if ($found_current && $app['status'] !== 'signed' && $app['status'] !== 'rejected') {
        $next_approver = $app;
        break;
    }
    if ((int)$app['staffid'] == (int)$staff_id) {
        $found_current = true;
    }
}

if ($next_approver) {
    $this->load->model('emails_model');
    $next_staff = get_staff($next_approver['staffid']);
    
    if ($next_staff) {
        // Check approval_heading_id to determine email subject and link
        $is_external_review = isset($next_approver['approval_heading_id']) && (int)$next_approver['approval_heading_id'] === 11;
        
        if ($is_external_review) {
            $contract_link = admin_url('contracts/contract_external_review/' . $id);
            if($type=='contract'){
                $email_subject = _l('Contract Ready for Your Review');
            }else{
                $email_subject = _l($type.' Ready for Your Review');
            }
            
        } else {
            $contract_link = admin_url('contracts/contract/' . $id);
            if($type=='contract'){
                $email_subject = _l('Contract Ready for Your Signature');
            }else{
                $email_subject = _l($type.' Ready for Your Signature');
            }
            
        }
        
        $signer_name = get_staff_full_name($staff_id);
        
        $message = "Hello " . $next_staff->firstname . ",<br><br>";
        $message .= "The '.$type.' <strong>" . $contract->subject . "</strong> has been signed by " . $signer_name . ".<br><br>";
        
        if ($is_external_review) {
            $message .= "It is now your turn to review the '.$type.'.<br><br>";
        } else {
            $message .= "It is now your turn to review and sign the '.$type.'.<br><br>";
        }
        
        $message .= "Please click the link below to view ";
        $message .= $is_external_review ? "the '.$type.':<br>" : "and sign the '.$type.' :<br>";
        $message .= '<a href="' . $contract_link . '">' . $contract_link . '</a><br><br>';
        $message .= "Thank you.";
        
        $this->emails_model->send_simple_email(
            $next_staff->email,
            $email_subject,
            $message
        );
    }
}

    // Optional: Add activity log
    if($type=='contract')
    log_activity('Contract PDF reviewed by staff ID: ' . $staff_id . ' for contract ID: ' . $id);

    // Set success message
    set_alert('success', 'You have successfully reviewed this '.$type.'.');

    // Redirect back to the review page
    redirect(admin_url('contracts/contract_external_review/' . $id));
}
}


