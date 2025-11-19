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
        $this->load->model('contracts_model');
    }

    /* List all contracts */
    public function index()
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
        $this->load->view('admin/contracts/manage', $data);
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
   
 public function table_single($clientid = '',$opposite_party='')
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('contracts', [
            'clientid' => $clientid,
            'opposite_party'=> $opposite_party,
            'type'=>'po'
        ]);
    }
    /* Edit contract or add new contract */
    public function contract($id = '')
    {
        if ($this->input->post()) {
			 $contracttemp_name=$this->db->get_where('tbltemplates',array('id'=>$this->input->post('contract_template_id')))->row()->temp_filename;
            if ($id == '') {
                if (!has_permission('contracts', '', 'create')) {
                    access_denied('contracts');
                }
                $id = $this->contracts_model->add($this->input->post());
                if ($id) {
					if(!empty($this->input->post('contract_template_id'))){
					if(!empty($contracttemp_name)){
					$this->generate_contract_agreement_word($id);
					}else{
					$this->generateword_contract($id);
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
					if(!empty($contracttemp_name)){
					$this->generate_contract_agreement_word($id);
					}else{
					$this->generateword_contract($id);
					}
					}
                    set_alert('success', _l('updated_successfully', _l('contract')));
                }
               // redirect(admin_url('contracts/contract/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('contract_lowercase'));
        } else {
            $data['contract']                 = $this->contracts_model->get($id, [], true);
            $data['contract_renewal_history'] = $this->contracts_model->get_contract_renewal_history($id);
            $data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'contract']);
			$data['contract_risklist'] = $this->contracts_model->get_contract_risklistbyperson($id);
			$data['risklists']=$this->contracts_model->get_contract_risklist();
            $latest_version=get_current_contract_versioninfo($id);
            $data['latestversionid']=$latest_version->id;
            $data['latest_version_contract']=$this->extractText($latest_version->version_internal_file_path,$id);
            if (!$data['contract'] || (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('contract_not_found'));
            }

            $data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract', [ 'client'], '{email_signature}');

            $title = $data['contract']->subject;

            $data = array_merge($data, prepare_mail_preview_data('contract_send_to_customer', $data['contract']->client));
           $this->load->model('templates_model');
		   $data['contract_closure_fields']= $this->templates_model->get('', ['is_legalclause'=>1]); 
        }
		
        $data['tab'] = $this->input->get('tab')?$this->input->get('tab'):'tab_content';
    
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
		if ($this->input->get('party_id')) {
            $data['party_id'] = $this->input->get('party_id');
        }
        $this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->contracts_model->get_contract_types();
		$data['clients']		=$this->clients_model->get('',['tblclients.active'=>1]);
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
            set_alert('success', _l('deleted', _l('contract')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_lowercase')));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('contracts'));
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

	public function generateword_contract($id)
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
                $contracttemp_name=$this->db->get_where('tbltemplates',array('id'=>$this->input->post('contract_template_id')))->row()->temp_filename;
				$id = $this->contracts_model->add($data);
                if($id){
                     if(!empty($contracttemp_name)){
					$this->generate_contract_agreement_word($id);
					}else{
					$this->generateword_contract($id);
					}       
                        $success = true;
                        $message = _l('added_successfully', _l('contract'));
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
    $text = '';
    $contentfile='';
        $userfile1= get_upload_path_by_type('contract') . $contract_id . '/'.$userfile;
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
    echo "Identifier: " . $identifier;
    // Generate the Comparison Viewer URL
    $viewerUrl = "https://api.draftable.com/v1/comparisons/viewer/$accountId/$identifier";
     $this->db->where('id', $contract_id);
      $this->db->update(db_prefix() . 'contracts', [
                        'comparison_view_url' =>$viewerUrl,
                        'comparison_identity' =>$identifier,
                    ]);
    echo "Comparison Viewer URL: " . $viewerUrl;
} else {
    echo "Identifier not found in the response.";
}
    
}
    $exporturl=$this->export_comparison($identifier);
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
}

