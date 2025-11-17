<?php
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\Shared\Html;
defined('BASEPATH') or exit('No direct script access allowed');

class Notices extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notices_model');
    }

    /* List all notices */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            access_denied('notices');
        }

        $data['expiring']               = $this->notices_model->get_notices_about_to_expire(get_staff_user_id());
        $data['count_active']           = count_active_notices();
        $data['count_expired']          = count_expired_notices();
        $data['count_recently_created'] = count_recently_created_notices();
        $data['count_trash']            = count_trash_notices();
        $data['chart_types']            = json_encode($this->notices_model->get_notices_types_chart_data());
       // $data['chart_types_values']     = json_encode($this->notices_model->get_notices_types_values_chart_data());
        $data['notice_types']         = $this->notices_model->get_notice_types();
		$data['notice_statuses']  = $this->notices_model->get_notice_status();
        $data['years']                  = $this->notices_model->get_notices_years();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title']         = _l('notices');
        $this->load->view('admin/notices/manage', $data);
    }

    
    public function table($clientid = '',$opposite_party='')
    {
        if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('notices', [
            'clientid' => $clientid,
			'opposite_party'=> $opposite_party
        ]);
    }
   

    /* Edit notice or add new notice */
    public function notice($id = '')
    {
        if ($this->input->post()) {
			$data=$this->input->post();
			// $noticetemp_name=$this->db->get_where('tbltemplates',array('id'=>$this->input->post('notice_template_id')))->row()->temp_filename;
            if ($id == '') {
                if (!has_permission('notices', '', 'create')) {
                    access_denied('notices');
                }
				if (isset($data['is_nonstandard']) && ($data['is_nonstandard'] == 1 || $data['is_nonstandard'] === 'on')) {
            $data['is_nonstandard'] = 1;
        } else {
            $data['is_nonstandard'] = 0;
        }
				if(isset($data['qnotice_type'])&& !empty($data['qnotice_type'])){
					$data['notice_type']=$data['qnotice_type'];
					unset($data['qnotice_type']);
				}
				if(isset($data['quick_projectid'])&& !empty($data['quick_projectid'])){
					$data['project_id']=$data['quick_projectid'];
				
				}
					unset($data['quick_projectid']);
				$data['is_first_borrower']=1;
                $id = $this->notices_model->add($data);
                if ($id) {
				if (($data['is_nonstandard'] == 0 )) {
				$this->generateword_notice($id);	
				}
					 $borrower_data=$this->db->select('id,name,email,mobile,city,address,state,district,mobile,pincode,co_borrower_hindi,shreni_nam,survey_borrower_name,trans_relative_name')->from('tbloppositeparty')->where('id',$data['other_party'])->get()->row();
					if(!empty($borrower_data->trans_relative_name)){
						$data['is_trans_relative']=1;
						$data['is_first_borrower']=0;
						$data['is_coborrower']=0;
						$data['is_survey_borrower']=0;
						$this->notices_model->add($data);
					}
					if(!empty($borrower_data->survey_borrower_name)){
						$this->notices_model->add($data);
						$data['is_trans_relative']=0;
						$data['is_first_borrower']=0;
						$data['is_coborrower']=0;
						$data['is_survey_borrower']=1;
					}
					if(!empty($borrower_data->co_borrower_hindi)){
						$co_borrowers=explode(',',$borrower_data->co_borrower_hindi);
						foreach($co_borrowers as $borrower){
							$data['is_trans_relative']=0;
						$data['is_first_borrower']=0;
						$data['is_coborrower']=1;
						$data['is_survey_borrower']=0;
						$this->notices_model->add($data);
						}
					}
					
					/*if(!empty($noticetemp_name)){
					$this->generate_notice_agreement_word($id);
					}else{
					$this->generateword_notice($id);
					}*/
                    set_alert('success', _l('added_successfully', _l('notice')));
                    //redirect(admin_url('notices/notice/' . $id));
                    redirect($_SERVER['HTTP_REFERER']);
                }
            } else {
                if (!has_permission('notices', '', 'edit')) {
                    access_denied('notices');
                }
				
                $success = $this->notices_model->update($data, $id);
                if ($success) {
					$this->generateword_notice($id);
					/*if(!empty($noticetemp_name)){
					$this->generate_notice_agreement_word($id);
					}else{
					$this->generateword_notice($id);
					}*/
                    set_alert('success', _l('updated_successfully', _l('notice')));
                }
               // redirect(admin_url('notices/notice/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('notice_lowercase'));
        } else {
            $data['notice']                 = $this->notices_model->get($id, [], true);
            $data['notice_renewal_history'] = $this->notices_model->get_notice_renewal_history($id);
			$data['notice_trackings']=$this->notices_model->get_notice_trakings($id,$data['notice']->tracking_number);
            $data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'notice']);
            if (!$data['notice'] || (!has_permission('notices', '', 'view') && $data['notice']->addedfrom != get_staff_user_id())) {
                blank_page(_l('notice_not_found'));
            }

            $data['notice_merge_fields'] = $this->app_merge_fields->get_flat('notice', [ 'client','borrower'], '{email_signature}');

            $title = $data['notice']->subject;

            $data = array_merge($data, prepare_mail_preview_data('notice_send_to_customer', $data['notice']->client));
           $this->load->model('templates_model');
		   $data['notice_closure_fields']= $this->templates_model->get('', ['is_legalclause'=>1]); 
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
        $data['types']         = $this->notices_model->get_notice_types();
		$data['clients']		=$this->clients_model->get('',['tblclients.active'=>1]);
		$data['statuses']  = $this->notices_model-> get_notice_status();
		$data['project_members'] = $this->notices_model->get_notice_members($id);
		$data['staff']    = $this->staff_model->get('', ['active' => 1]);
        $data['title']         = $title;
        $data['bodyclass']     = 'notice';
		$this->load->model('tickets_model');
		$data['requests']=$this->tickets_model->get();
		$data['service']='notice';
		$data['activity_log']  = $this->notices_model->get_notice_activity_log($id);
		$data['templates']=$this->notices_model->get_templates_of_notice();
        $this->load->view('admin/notices/notice', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/notices/templates/' . $name, [], true);
    }

    public function mark_as_signed($id)
    {
        if (!staff_can('edit', 'notices')) {
            access_denied('mark notice as signed');
        }

        $this->notices_model->mark_as_signed($id);

        redirect(admin_url('notices/notice/' . $id));
    }

    public function unmark_as_signed($id)
    {
        if (!staff_can('edit', 'notices')) {
            access_denied('mark notice as signed');
        }

        $this->notices_model->unmark_as_signed($id);

        redirect(admin_url('notices/notice/' . $id));
    }
    public function mark_as_send($id)
    {
        if (!staff_can('edit', 'notices')) {
            access_denied('mark notice as sended');
        }
		$notice                = $this->notices_model->get($id, [], true);
        $result=$this->notices_model->mark_as_send($id);
		if($result>0){
		$sms_sent = false;
		$sms_success=false;
        $sms_reminder_log = [];
			
		$this->load->model('casediary_model');
		$contacts = $this->notices_model->get_notices_of_oppositeparty($id);
		foreach($contacts as $contact){
       	$otherparty_det = $this->casediary_model->get_oppositeparty($contact['id']);
		$other_party_phonenummber=$otherparty_det->mobile;
		$other_party_email=$otherparty_det->email;
    //  $sms_success= CURLsendsms($other_party_phonenummber);
        $sms_insert['email_subject'] = SMS_TRIGGER_notice_NEW_INVITE_TO_CUSTOMER;
        $sms_insert['sms_to']        = $other_party_phonenummber;
		//$template = mail_template('notice_send_to_customer', $notice, $other_party_email);
		$template = mail_template('notice_invitation_to_sign_transfers', $notice, $other_party_email);
		$merge_fields = $template->get_merge_fields();
         $sent= $template->send();
			if($sent){
			$sms_sent = true;	
			}
        
		 }

        if ($sms_sent) {
            set_alert('success', _l('notice_invitation_sent_to_customer'));
        } else {
            //set_alert('danger', _l('failed'));
        }

       // redirect($_SERVER['HTTP_REFERER']);
		}

        redirect(admin_url('notices/notice/' . $id));
    }
	 public function mark_as_send_sms($id)
    {
        if (!staff_can('edit', 'notices')) {
            access_denied('mark notice as sended');
        }
		$notice                = $this->notices_model->get($id, [], true);
        $result=$this->notices_model->mark_as_send($id);
		if($result>0){
		$sms_sent = false;
		$sms_success=false;
        $sms_reminder_log = [];
			
		$this->load->model('casediary_model');
		$contacts = $this->notices_model->get_notices_of_oppositeparty($id);
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
        //     set_alert('success', _l('notice_invitation_sent_to_customer'));
        // } else {
        //     set_alert('danger', $sms_success);
        // }

       // redirect($_SERVER['HTTP_REFERER']);
		}

        redirect(admin_url('notices/notice/' . $id));
    }
		 public function legalnotice_approval($id)
    {
       if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            access_denied('notices');
        }

        if (!$id) {
            redirect(admin_url('notices'));
        }
        $legalapprove =   $this->notices_model->get($id, [], true);
        $legalapprove->approval =  get_approvals($id,'notice',3);
 	
	    try {
            $pdf = legalnotice_approval_pdf($legalapprove);
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
        if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            access_denied('notices');
        }

        if (!$id) {
            redirect(admin_url('notices'));
        }

        $notice = $this->notices_model->get($id);

        try {
            $pdf = notice_pdf($notice);
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

        $pdf->Output(slug_it($notice->subject) . '.pdf', $type);
    }

    public function send_to_email($id)
    {
        if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            access_denied('notices');
        }
        $success = $this->notices_model->send_notice_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('notice_sent_to_client_success'));
        } else {
            set_alert('danger', _l('notice_sent_to_client_fail'));
        }
        redirect(admin_url('notices/notice/' . $id));
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && (has_permission('notices', '', 'view') || has_permission('notices', '', 'view_own'))) {
            $this->misc_model->add_note($this->input->post(), 'notice', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if ((has_permission('notices', '', 'view') || has_permission('notices', '', 'view_own'))) {
            $data['notes'] = $this->misc_model->get_notes($id, 'notice');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('notices', '', 'delete')) {
            $this->notices_model->clear_signature($id);
        }

        redirect(admin_url('notices/notice/' . $id));
    }

    public function save_notice_data()
    {
        if (!has_permission('notices', '', 'edit')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('notice_id'));
        $this->db->update(db_prefix() . 'notices', [
                'content' => html_purify($this->input->post('content', false)),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('notice'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->notices_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->notices_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_comments($id)
    {
        $data['comments'] = $this->notices_model->get_comments($id);
        $this->load->view('admin/notices/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'notice_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->notices_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function renew()
    {
        if (!has_permission('notices', '', 'create') && !has_permission('notices', '', 'edit')) {
            access_denied('notices');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->notices_model->renew($data);
            if ($success) {
			
                set_alert('success', _l('notice_renewed_successfully'));
            } else {
                set_alert('warning', _l('notice_renewed_fail'));
            }
            redirect(admin_url('notices/notice/' . $data['noticeid'] . '?tab=renewals'));
        }
    }

    public function delete_renewal($renewal_id, $noticeid)
    {
        $success = $this->notices_model->delete_renewal($renewal_id, $noticeid);
        if ($success) {
            set_alert('success', _l('notice_renewal_deleted'));
        } else {
            set_alert('warning', _l('notice_renewal_delete_fail'));
        }
        redirect(admin_url('notices/notice/' . $noticeid . '?tab=renewals'));
    }

    public function copy($id)
    {
        if (!has_permission('notices', '', 'create')) {
            access_denied('notices');
        }
        if (!$id) {
            redirect(admin_url('notices'));
        }
        $newId = $this->notices_model->copy($id);
        if ($newId) {
            set_alert('success', _l('notice_copied_successfully'));
        } else {
            set_alert('warning', _l('notice_copied_fail'));
        }
        redirect(admin_url('notices/notice/' . $newId));
    }

    /* Delete notice from database */
    public function delete($id)
    {
        if (!has_permission('notices', '', 'delete')) {
            access_denied('notices');
        }
        if (!$id) {
            redirect(admin_url('notices'));
        }
        $response = $this->notices_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('notice')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('notice_lowercase')));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        }else if (strpos($_SERVER['HTTP_REFERER'], 'opposite_parties/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('notices'));
        }
    }

    /* Manage notice types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_notice_types') == '0') {
            access_denied('notices');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->notices_model->add_notice_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('notice_type'));
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
                $success = $this->notices_model->update_notice_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('notice_type'));
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
            access_denied('notices');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('notice_types');
        }
        $data['title'] = _l('notice_types');
        $this->load->view('admin/notices/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_notice_type($id)
    {
        if (!$id) {
            redirect(admin_url('notices/types'));
        }
        if (!is_admin()) {
            access_denied('notices');
        }
        $response = $this->notices_model->delete_notice_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('notice_type_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('notice_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('notice_type_lowercase')));
        }
        redirect(admin_url('notices/types'));
    }
    public function notice_status()
    {
        if (!is_admin()) {
            access_denied('notices');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('notice_status');
        }
        $data['title'] = _l('notice_status');
        $this->load->view('admin/notices/manage_status', $data);
    }

    public function status($id = '')
        {
            if (!is_admin() && get_option('staff_members_create_inline_notice_types') == '0') {
                access_denied('notices');
            }
            if ($this->input->post()) {
                if (!$this->input->post('id')) {
                    $id = $this->notices_model->add_notice_status($this->input->post());
                    if ($id) {
                        $success = true;
                        $message = _l('added_successfully', _l('notice_status'));
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
                    $success = $this->notices_model->update_notice_status($data, $id);
                    $message = '';
                    if ($success) {
                        $message = _l('updated_successfully', _l('notice_status'));
                    }
                    echo json_encode([
                        'success' => $success,
                        'message' => $message,
                    ]);
                }
            }
        }
        /* Delete status from database */
    public function delete_notice_status($id)
    {
        if (!$id) {
            redirect(admin_url('notices/notice_status'));
        }
        if (!is_admin()) {
            access_denied('notices');
        }
        $response = $this->notices_model->delete_notice_status($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('notice_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('notice_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('notice_status_lowercase')));
        }
        redirect(admin_url('notices/notice_status'));
    }
    
    public function add_notice_attachment($id)
    {
        handle_notice_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database(
                $this->input->post('notice_id'),
                'notice',
                $this->input->post('files'),
                $this->input->post('external')
            );
        }
    }

    public function delete_notice_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode([
                'success' => $this->notices_model->delete_notice_attachment($attachment_id),
            ]);
        }
    }
	    public function pagination()
    {

      $q='';
      $noticetype = $status = '';
      $where= false;
      if($this->input->post()){ /*print_r($_POST);*/
        $q= $this->input->post('q');
        if($this->input->post('notice_type') != ' '){
            $noticetype =$this->input->post('notice_type');
        }
        if($this->input->post('status') != ''){
            $status = $this->input->post('status');
        }
      }  
      $this->load->library("pagination");
      $config = array();
      $config["base_url"] = "#";
      $config["total_rows"] = $this->notices_model->fetch_notice_details_num_rows($q,$noticetype,$status);
      $config["per_page"] = 12;
      $config["uri_segment"] = 4;
      $config["use_page_numbers"] = TRUE;
      $config["full_tag_open"] = '<ul class="pagination notice-page">';
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
       'project_data'   => $this->notices_model->fetch_notice_details($q,$config["per_page"], $start,$noticetype,$status),
       'total_cases'=> '<span class="badge badge-success" style="padding: 10px;
    font-size: 15px;"><b>'.$config["total_rows"].'  '._l('notices').'</b></span>',
      );
      echo json_encode($output);
    }
public function add_noticepdf($id=''){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['noticeid'];
                unset($data['noticeid']);
			 
             $message         = '';
            $success=handle_project_notice_file_upload($id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('notice')) : '';
					  $updated          = true;
					 $contentfile='';
					$userfile1= $this->db->get_where('tblnotices',array('id'=>$id))->row()->notice_filename;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'notices', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );
					 }else{
					 
					  $message = 'Chaeck Image Image extension not allowed';
					  $updated          = false;
				 }
                if ($success) {
					$message= _l('notice_latest_uploaded');
					
				}
			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}

    public function add_signed_noticepdf($id=''){
        if ($this->input->post()) {
            $data=$this->input->post();
            $id   = $data['noticeid'];
               unset($data['noticeid']);
            
            $message         = '';
           $success=handle_signedLOE_notice_file_upload($id);
                if ($success == true) {
               $message = $id ? _l('added_successfully', _l('notice')) : '';
                     $updated          = true;
                   
                    }else{
                    
                     $message = 'Check Image extension not allowed';
                     $updated          = false;
                }
               if ($success) {
                   $message= _l('notice_latest_uploaded');
                     log_activity('Signed notice Uploaded [noticeID: ' . $id . ']');
           $this->notices_model->log_notice_activity($id, 'not_signed_notice_uploded');
                   
               }
            
               echo json_encode(array(
                   'success' => $success,
                   'message' => $message,
               ));
       }
   }

		public function projectvesioncontents($userfile1,$notice_id)
	{
		//require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
	    require_once  APPPATH . '/vendor/smalot/pdfparser/alt_autoload.php-dist';
		$contentfile='';
		$userfile= get_upload_path_by_type('notice') . $notice_id . '/'.$userfile1;
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
	public function delete_notice_document($notice_id)
    {
        $this->notices_model->delete_notice_document($notice_id);
    }

    public function delete_signed_notice_document($notice_id)
    {
        $this->notices_model->delete_signed_notice_document($notice_id);
    }
	
	public function add_noticeversionpdf(){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['noticeid'];
                unset($data['noticeid']);
			$current_version = get_current_notice_version($id);
			// Already version exists
            if($current_version>=0){
                $version_data['version'] = $current_version+1;
            }else{
			$version_data['version']=1;	
			}

            $version_data['noticeid']  = $id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
			$this->db->insert('tblnotice_versions',$version_data);
			  $insert_id = $this->db->insert_id();
            //create_new_notice_version($version_data); 
			 
             $message         = '';
            $success=handle_project_notice_version_file_upload($id,$insert_id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('notice_version')) : '';
					  $updated          = true;
					/* $contentfile='';
					$userfile1= $this->db->get_where('tblnotice_versions',array('id'=>$insert_id))->row()->version_internal_file_path;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'notices', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );*/
					 }else{
					 
					  $message = 'Check  Image extension not allowed';
					  $updated          = false;
				 }
                if ($success) {
					$message= _l('notice_latest_uploaded');
					
				}
			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}
    public function mark_as_final_doc($notice_id,$version){
        if (!$notice_id) {
            redirect(admin_url('notices'));
        }
        $response = $this->notices_model->make_final_doc($notice_id,$version);
        if ($response == true) {
            set_alert('success', _l('success'));
        }else{
            set_alert('danger', _l('failed'));
        }
        redirect(admin_url('notices/notice/'.$notice_id));
    }
	
	public function  update_sharepoint1($notice_id,$versioncount)
    {
		$this->load->library('sharegraph');
		$sharegraph=new Sharegraph();    
        if ($this->input->is_ajax_request()) {
			 $this->db->where('id', $notice_id);
             $attachment = $this->db->get(db_prefix() . 'notices')->row();
			$versioncount=$this->db->get_where('tblnotice_versions',array('noticeid'=>$notice_id))->num_rows();
			
		if($versioncount==0){
			$sharegraph->download_updatefile($notice_id,$attachment->notice_filename);
		}
		else{
			$latestversion=get_current_notice_version($notice_id);
			if($data['is_newversion']==0){
			$sharegraph->download_updateversionfile($latestversion,$notice_id,$attachment->notice_filename);
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
public function update_noticefile_version($notice_id,$create_new_version_enabled){
	$this->load->library('sharegraph');
		$sharegraph=new Sharegraph();
	if ($this->input->is_ajax_request()) {
		$this->db->where('id', $notice_id);
             $attachment = $this->db->get(db_prefix() . 'notices')->row();
			//$versioncount=$this->db->get_where('tblnotice_versions',array('noticeid'=>$notice_id))->num_rows();
         //--------- if new version enabled
        if($create_new_version_enabled=='yes'){
            $current_version = get_current_notice_version($notice_id);
			//print_r($current_version);
            // Already version exists
            if($current_version>=0){
                $version_data['version'] = $current_version+1;
				
                $path = get_upload_path_by_type('notice') . $notice_id . '/';
                _maybe_create_upload_path($path);
                $version_path = get_upload_path_by_type('notice') . $notice_id . '/'.$version_data['version'];
                _maybe_create_upload_path($version_path);
				 // Getting file extension
                    $extension = strtolower(pathinfo($attachment->notice_filename, PATHINFO_EXTENSION));
				if($current_version!=0)
				$oldfilename = basename($attachment->notice_filename,".".$extension).'-'.$current_version.'.'.$extension;
				else
					$oldfilename = $attachment->notice_filename;
				$newfilename = basename($attachment->notice_filename,".".$extension).'-'.$version_data['version'].'.'.$extension;
				
				 $newFilePath = $path . $newfilename;
				//create file using downlod sharelink
				$sharegraph->download_updateversionfile($version_data['version'],$notice_id,$oldfilename,$newfilename);
             // 	$sharegrah->rungraphversionuser($newfilename,$path,$notice_id,$version_data['version']);	
				$sharegraph->rungraphuser($newfilename,$newFilePath,$notice_id);
			  $sharelink=$sharegraph->getweburl($notice_id,$newfilename);
              $version_data['version_sharpoint_link']=$sharelink;  
               $version_data['version_internal_file_path']=$newfilename; 
            }

            $version_data['noticeid']  = $notice_id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
			
            create_new_notice_version($version_data);    
             $alert   = 'success';
        $message = _l('newversion_created');

        }else{
            
            // replace current notice
			$sharegraph->download_updatefile($notice_id,$attachment->notice_filename);
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
	
	/* Change noticeversion status / active / inactive */
    public function change_version_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->notices_model->change_version_status($id, $status);
           
        }

    }

	public function generateword_notice($id)
    {
		
		
        if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            access_denied('notices');
        }

        if (!$id) {
            redirect(admin_url('notices'));
        }

        $notice = $this->notices_model->get($id);

		
        $htmlTemplate =str_replace('<br>', '<br/>', $notice->content);

        $phpWord = new PhpWord();
        $section = $phpWord->addSection();
        $footer_sub = $section->addFooter();
        //$footer_sub->addText(htmlspecialchars($footer));
        $footer_sub->addPreserveText('Smart Legal Counsel                                      {PAGE} ');

        Html::addHtml($section, $htmlTemplate);
		$filename= preg_replace('/[^a-zA-Z0-9_]/s','',$notice->subject).'.docx';
		//$filename=str_replace(' ','',$notice->subject).'.docx';
        //Html::addHtml('ssdadsa', view($footerTemplate), false, false);
        $path        = get_upload_path_by_type('notice').$id.'/';
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
        $this->db->update(db_prefix() . 'notices', [
                'notice_filename' =>$filename ,
				'file_type' =>pathinfo($filename, PATHINFO_EXTENSION),
				'sharepoint_link'=>$sharelink,
        ]);
	}
	
  public function get_templates_of_notice($noticeid)

    {

        if ($this->input->is_ajax_request()) {

            echo json_encode($this->notices_model->get_templates_of_notice($noticeid));

        }

    }
	   public function generate_notice_agreement_word($id){
        
        $filename='temp_sale_agreement_final.docx';
		
       // require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
     
        $notice = $this->notices_model->get($id);//print_r($notice->id);
		$notice_name=$this->db->get_where('tbltemplates',array('id'=>$notice->notice_template_id))->row()->temp_filename;
		$noticeid=$notice->id;
		$filename=$notice_name;//str_replace(' ','_',$notice_name).'.docx';
        
      
        $clients_data = $this->clients_model->get($notice->client);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('uploads/templates/'.$filename);
        
        $clientsign=!empty($notice->signature) ? $notice->signature : NULL; 
        $partysign=!empty($notice->party_signature) ? $notice->party_signature : NULL; 
        $date = date('d-m-Y');
        $client_company_name = $clients_data->company;
        $address     = nl2br($clients_data->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $address);
        $address = str_replace('&','&amp;', $address) ;
        $contactno   = $clients_data->phonenumber;
        $email       = $clients_data->email_id;
        $notice_amount = $notice->notice_value;
        $client_company_name = str_replace('&','&amp;', $clients_data->company) ;
		$client_contact= get_primary_contact_user_id($notice->client);
        $emirate   = $clients_data->state;
        $country   = get_country_name($clients_data->country);
		$notice_start_date=date('F d , Y', strtotime($notice->datestart));
        $this->load->library('app_number_to_word', [ 'clientid' => $notice->client ], 'numberword');
        $amount_in_words = $this->numberword->convert($notice->notice_value,'','Fils');
		 $this->load->model('casediary_model');
       	$otherparty_det = $this->casediary_model->get_oppositeparty($notice->other_party);
        $other_party_name= str_replace('&','&amp;',$otherparty_det->name);
		$other_party_address=  str_ireplace($breaks,'</w:t><w:br/><w:t>',$otherparty_det->address).' '.$otherparty_det->city;
        $ref_no = '';//$notice->notice_refno;
        $templateProcessor->setValue('date',$date);
        $templateProcessor->setValue('client_contact_name',get_contact_full_name($client_contact));
        $templateProcessor->setValue('client_address',$address);
        $templateProcessor->setValue('contactno',$contactno);
        $templateProcessor->setValue('email',$email);
        $templateProcessor->setValue('notice_amount',$notice_amount);
        $templateProcessor->setValue('client_company_name',$client_company_name);
        $templateProcessor->setValue('emirate',$emirate);
        $templateProcessor->setValue('country',$country);
        $templateProcessor->setValue('amount_in_words',$amount_in_words);
        $templateProcessor->setValue('notice_refno',$ref_no);
		$templateProcessor->setValue('notice_start_date',$notice_start_date);
		$templateProcessor->setValue('other_party',$other_party_name);
		$templateProcessor->setValue('other_party_address',$other_party_address);
    	//$templateProcessor->setImageValue('CompanyLogo', 'path/to/company/logo.png');
//$templateProcessor->setImageValue('CompanyLogo', 'path/to/company/logo.png');
//$templateProcessor->setImageValue('UserLogo', array('path' => 'path/to/logo.png', 'width' => 100, 'height' => 100, 'ratio' => false));
/*$templateProcessor->setImageValue('Signature', function () {
    // Closure will only be executed if the replacement tag is found in the template
    return array('path' => 'path/to/signature.png', 'width' => 100, 'height' => 100, 'ratio' => false);
});*/
		    /* $path        = get_upload_path_by_type('notice').$id.'/';
$templateProcessor->setImageValue('clientSignature', function () {
    // Closure will only be executed if the replacement tag is found in the template
    return array('path' => get_upload_path_by_type('notice').$noticeid.'/'.$clientsign, 'width' => 100, 'height' => 100, 'ratio' => false);
});
$templateProcessor->setImageValue('Signature', function () {
    // Closure will only be executed if the replacement tag is found in the template
    return array('path' => get_upload_path_by_type('notice').$noticeid.'/'.$partysign, 'width' => 100, 'height' => 100, 'ratio' => false);
});*/
        // file upload
     $path        = get_upload_path_by_type('notice').$id.'/';
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
        $this->db->update(db_prefix() . 'notices', [
                'notice_filename' =>$filename ,
				'file_type' =>pathinfo($filename, PATHINFO_EXTENSION),
				'sharepoint_link'=>$sharelink,
        ]);
    }

	/* Get Emailtemplate by id */
	public function get_prompt_of_notice($noticeid)

    {

        if ($this->input->is_ajax_request()) {
			$notice = $this->notices_model->get($noticeid);//print_r($notice->id);
			  $clients_data = $this->clients_model->get($notice->client);
 		$client_company_name = $clients_data->company;
        $address     = nl2br($clients_data->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $address);
        $address = str_replace('&','&amp;', $address) ;
        $contactno   = $clients_data->phonenumber;
        $email       = $clients_data->email_id;
        $notice_amount = $notice->notice_value;
        $client_company_name = str_replace('&','&amp;', $clients_data->company) ;
		$client_contact= get_primary_contact_user_id($notice->client);
        $emirate   = $clients_data->state;
        $country   = get_country_name($clients_data->country);
		$notice_start_date=date('F d , Y', strtotime($notice->datestart));
 		$this->load->model('casediary_model');
       	$otherparty_det = $this->casediary_model->get_oppositeparty($notice->other_party);
        $other_party_name= str_replace('&','&amp;',$otherparty_det->name);
		$other_party_address=  str_ireplace($breaks,'</w:t><w:br/><w:t>',$otherparty_det->address).' '.$otherparty_det->city;
		$prompt ='Generate '.$notice->type_name.'  template between parties . First Party : '.$client_company_name.' Address : '.$address.' Second Party '.$other_party_name.' Address : '.$other_party_address;
           
 	echo json_encode($prompt);
        }

    }
	
	public function bulk_action()
{

    if (!is_staff_member()) {
        ajax_access_denied();
    }

    hooks()->do_action('before_do_bulk_action_for_notices');
    $total_assigned = 0;  // Initialize the count for assignments
    $total_removed = 0;   // Initialize the count for removals
    $total_changed = 0;   // Initialize the count for case_typechanged

    if ($this->input->post()) { 
        $notices                   = $this->input->post('ids');
       // $assigned_users              = $this->input->post('assigned_user');
        $assigned_status              = $this->input->post('notice_status');
        if (is_array($notices)) {

 if ($this->input->post('mass_notice')) {
                foreach ($notices as $notice) {
					
                 	$this->db->where('id',$notice);
           
                        $result=$this->db->update('tblnotices',array('status'=>$assigned_status));
                        if ($result) {
							if($assigned_status==4){
								$notice_data=$this->db->select('id,tracking_number,other_party')->from('tblnotices')->where('id',$notice)->get()->row();
								//  $this->load->library('shipwayapi');
								// $shipway= new shipwayapi();//$notice_data->tracking_number
								// $shipway->push_data_tracking($notice,10000003,$notice_data->other_party);
							}
                            $total_changed++;
                        }
                 //   }

                }
            }

        }
       if ($this->input->post('mass_notice')) {
            //print_r($total_assigned);
            set_alert('success', _l('total_notice_status_changed', $total_changed));
        }
        
    }
    
}

public function notice_tracking_pdf($bcode)
{
        if (!has_permission('notices', '', 'view') && !has_permission('notices', '', 'view_own')) {
            access_denied('notices');
        }

        if (!$bcode) {
            redirect(admin_url('notices'));
        }

        $notice = $this->notices_model->get_notice_trakings('',$bcode);
    
        try {
            $pdf = notice_tracking_pdf($notice);
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

        $pdf->Output(slug_it('notice_tracking') . '.pdf', $type);
}


}
