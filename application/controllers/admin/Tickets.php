<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Tickets extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        if (get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member()) {
            redirect(admin_url());
        }
        $this->load->model('tickets_model');
    }

    public function index($status = '', $userid = '')
    {
        close_setup_menu();

        if (!is_numeric($status)) {
            $status = '';
        }

        if ($this->input->is_ajax_request()) {
            if (!$this->input->post('filters_ticket_id')) {
                $tableParams = [
                    'status' => $status,
                    'userid' => $userid,
                ];
            } else {
                // request for othes tickets when single ticket is opened
                $tableParams = [
                'userid'              => $this->input->post('filters_userid'),
                'where_not_ticket_id' => $this->input->post('filters_ticket_id'),
            ];
                if ($tableParams['userid'] == 0) {
                    unset($tableParams['userid']);
                    $tableParams['by_email'] = $this->input->post('filters_email');
                }
            }

            $this->app->get_table_data('tickets', $tableParams);
        }

        $data['chosen_ticket_status']              = $status;
        $data['weekly_tickets_opening_statistics'] = json_encode($this->tickets_model->get_weekly_tickets_opening_statistics());
        $data['title']                             = _l('support_tickets');
        $this->load->model('departments_model');
        $data['statuses_reassign']             = $this->tickets_model->get_ticket_reassign_status();
        $data['statuses']             = $this->tickets_model->get_ticket_status('',['ticketstatusid!='=>7]);
        $data['staff_deparments_ids'] = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
        $data['departments']          = $this->departments_model->get();
        $data['priorities']           = $this->tickets_model->get_priority();
        $data['services']             = $this->tickets_model->get_service('',['service_slug!='=>'expense']);
        $data['ticket_assignees']     = $this->tickets_model->get_tickets_assignes_disctinct();
		$data['ticket_branches']     = $this->tickets_model->get_tickets_branch_disctinct();
		$data['ticket_services']     = $this->tickets_model->get_tickets_services_disctinct();
		$data['years']                  = $this->tickets_model->get_tickets_years();
        $data['bodyclass']            = 'tickets-page';
        add_admin_tickets_js_assets();
        $data['default_tickets_list_statuses'] = hooks()->apply_filters('default_tickets_list_statuses', [1, 2, 4]);
        $this->load->view('admin/tickets/list', $data);
    }

    public function add($userid = false)
    {
        if ($this->input->post()) {
            $data            = $this->input->post();
            $data['message'] = html_purify($this->input->post('message', false));
			if($this->input->post('civilcase_fileddet')!=''){
			   $data['civilcase_fileddet'] = json_encode($this->input->post('civilcase_fileddet'));
		  	}
			if($this->input->post('ldc_chequedet')!=''){
			   $data['ldc_chequedet'] = json_encode($this->input->post('ldc_chequedet'));
		  	}
			if($this->input->post('ldc_chequeothers')!=''){
			   $data['ldc_chequeothers'] = json_encode($this->input->post('ldc_chequeothers'));
		  	}
            $id              = $this->tickets_model->add($data, get_staff_user_id());
			$serviceid=$this->input->post('service');
            if ($id) {
				//Civil attachment
					if(($serviceid=='1' || $serviceid=='10')){
				$cvdocnames=$this->input->post('cvdocument_name');
	
				$data11['ticketid']=$id;
					 $data11['file_name'] = '';
			  $data11['filetype']='';
				$path=get_upload_path_by_type('ticket').'/'.$id.'/';
			foreach ($cvdocnames as $key => $value) {
				
				$i=$key+1;
				$fname='cvattachments'.$i;
				
			 // Get the temp file path
        $tmpFilePath = $_FILES[$fname]["tmp_name"];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts         = pathinfo($_FILES[$fname]["name"]);
            $extension          = $path_parts['extension'];
            $extension = strtolower($extension);

            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png','pdf',
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }
            // Setup our new file path

            $filename    =   'CVI'.date('Ymdhis').$key .'.'.$extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {

            // Initialize array
            $data11['file_name'] = $filename;
			  $data11['filetype']=  $_FILES[$fname]['type'];
				 }
			}
			$data11['document_type']=$this->input->post('cvdocument_type')[$key];
					

			$data111['document_name']=$this->input->post('cvdocument_name')[$key];

		
				$data11['dateadded']=date('Y-m-d H:i:s');
				//print_r($data1);
				$this->db->insert('tblticket_attachments', $data11);
				 $doc_ID = $this->db->insert_id();
					
			}
				}
				//Credit attachment
				if(($serviceid==get_option('ticket_creditrevision_service'))|| ($serviceid==get_option('ticket_creditapplication_service'))){
				$docnames=$this->input->post('document_name');
	
				$data1['ticketid']=$id;
					 $data1['file_name'] = '';
			  $data1['filetype']='';
				$path=get_upload_path_by_type('ticket').'/'.$id.'/';
			foreach ($docnames as $key => $value) {
			 // Get the temp file path
        $tmpFilePath = $_FILES['crattachments']['tmp_name'][$key];
        // Make sure we have a filepath
        if (!empty($tmpFilePath) && $tmpFilePath != '') {
            // Getting file extension
            $path_parts         = pathinfo($_FILES["crattachments"]["name"][$key]);
            $extension          = $path_parts['extension'];
            $extension = strtolower($extension);

            $allowed_extensions = array(
                'jpg',
                'jpeg',
                'png','pdf',
            );
            if (!in_array($extension, $allowed_extensions)) {
                set_alert('warning', 'Image extension not allowed.');

                return false;
            }
            // Setup our new file path

            $filename    =   'I'.date('Ymdhis') .'.'.$extension;
            $newFilePath = $path . $filename;
            _maybe_create_upload_path($path);
            // Upload the file into the company uploads dir
            if (move_uploaded_file($tmpFilePath, $newFilePath)) {

            // Initialize array
            $data1['file_name'] = $filename;
			  $data1['filetype']=  $_FILES['crattachments']['type'][$key];
				 }
			}
			$data1['document_type']=$this->input->post('document_type')[$key];
					

			$data1['document_name']=$this->input->post('document_name')[$key];

			$data1['document_number']=$this->input->post('document_number')[$key];
			$data1['nationality']=$this->input->post('nationality')[$key];
			//	$data1['remark']=$this->input->post('remark')[$key];

				$data1['expiry_date']=$this->input->post('expiry_date')[$key];
				$data1['dateadded']=date('Y-m-d H:i:s');
				//print_r($data1);
				$this->db->insert('tblticket_attachments', $data1);
				 $doc_ID = $this->db->insert_id();
				//handle_creditcontrol_attachment($doc_ID,$id,$key);
		 
			
			}
				}
                set_alert('success', _l('new_ticket_added_successfully', $id));
                redirect(admin_url('tickets/ticket/' . $id));
            }
        }
        if ($userid !== false) {
            $data['userid'] = $userid;
            $data['client'] = $this->clients_model->get($userid);
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');
		$this->load->model('casediary_model');
		 $this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();
        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->tickets_model->get_predefined_reply();
        $data['priorities']         = $this->tickets_model->get_priority();
        $data['services']           = $this->tickets_model->get_service('',['service_slug!='=>'expense']);
		$data['nationality']=get_countryproject();
		$data['document_types']    = $this->casediary_model->get_document_types_bycategory('6');
		$data['document_types1']    = $this->casediary_model->get_document_types_bycategory('7');
        $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
        $data['staff']     = $this->staff_model->get('', $whereStaff);
        $data['articles']  = $this->knowledge_base_model->get();
        $data['bodyclass'] = 'ticket';
        $data['title']     = _l('new_ticket');

        if ($this->input->get('project_id') && $this->input->get('project_id') > 0) {
            // request from project area to create new ticket
            $data['project_id'] = $this->input->get('project_id');
            $data['userid']     = get_client_id_by_project_id($data['project_id']);
            if (total_rows(db_prefix().'contacts', ['active' => 1, 'userid' => $data['userid']]) == 1) {
                $contact = $this->clients_model->get_contacts($data['userid']);
                if (isset($contact[0])) {
                    $data['contact'] = $contact[0];
                }
            }
        } elseif ($this->input->get('contact_id') && $this->input->get('contact_id') > 0 && $this->input->get('userid')) {
            $contact_id = $this->input->get('contact_id');
            if (total_rows(db_prefix().'contacts', ['active' => 1, 'id' => $contact_id]) == 1) {
                $contact = $this->clients_model->get_contact($contact_id);
                if ($contact) {
                    $data['contact'] = (array) $contact;
                }
            }
        }
        add_admin_tickets_js_assets();
        $this->load->view('admin/tickets/add', $data);
    }

    public function delete($ticketid)
    {
        if (!$ticketid) {
            redirect(admin_url('tickets'));
        }

        $response = $this->tickets_model->delete($ticketid);

        if ($response == true) {
            set_alert('success', _l('deleted', _l('ticket')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_lowercase')));
        }

        if (strpos($_SERVER['HTTP_REFERER'], 'tickets/ticket') !== false) {
            redirect(admin_url('tickets'));
        } else {
            redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function delete_attachment($id)
    {
        if (is_admin() || (!is_admin() && get_option('allow_non_admin_staff_to_delete_ticket_attachments') == '1')) {
            if (get_option('staff_access_only_assigned_departments') == 1 && !is_admin()) {
                $attachment = $this->tickets_model->get_ticket_attachment($id);
                $ticket     = $this->tickets_model->get_ticket_by_id($attachment->ticketid);

                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($ticket->department, $staff_departments)) {
                    set_alert('danger', _l('ticket_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }

            $this->tickets_model->delete_ticket_attachment($id);
        }

        redirect($_SERVER['HTTP_REFERER']);
    }

    public function ticket($id)
    {
        if (!$id) {
            redirect(admin_url('tickets/add'));
        }

        $data['ticket'] = $this->tickets_model->get_ticket_by_id($id);
        $data['user_reassigns'] = $this->tickets_model->get_reassign($id);
        $data['reassign_latest'] = $this->tickets_model->get_reassign_latest($id);
        
		$result=$this->tickets_model->get_civil_details_by_ticket_id($id);
				if($result!='')
                 $data['civil'] = $result;
		
		$result1=$this->tickets_model->get_police_details_by_ticket_id($id);
				if($result1!='')
                 $data['police'] = $result1;
		$legresult=$this->tickets_model->get_legalapproval_details_by_ticket_id($id);
		if($legresult!='')
			$data['legalapproval']=$legresult;
		$branchresult=$this->tickets_model->get_branchapproval_details_by_ticket_id($id);
		if($branchresult!='')
			$data['branchapproval']=$branchresult;

        if (!$data['ticket']) {
            blank_page(_l('ticket_not_found'));
        }
		$data['creditapp']=$this->tickets_model->get_ticket_crattachments($id);

        if (get_option('staff_access_only_assigned_departments') == 1) {
            if (!is_admin()) {
                $this->load->model('departments_model');
                $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                if (!in_array($data['ticket']->department, $staff_departments)) {
                    set_alert('danger', _l('ticket_access_by_department_denied'));
                    redirect(admin_url('access_denied'));
                }
            }
        }

        if ($this->input->post()) {
            $returnToTicketList = false;
            $data               = $this->input->post();

            if (isset($data['ticket_add_response_and_back_to_list'])) {
                $returnToTicketList = true;
                unset($data['ticket_add_response_and_back_to_list']);
            }

            $data['message'] = html_purify($this->input->post('message', false));
            $replyid         = $this->tickets_model->add_reply($data, $id, get_staff_user_id());

            if ($replyid) {
                set_alert('success', _l('replied_to_ticket_successfully', $id));
            }
            if (!$returnToTicketList) {
                redirect(admin_url('tickets/ticket/' . $id));
            } else {
                set_ticket_open(0, $id);
                redirect(admin_url('tickets'));
            }
        }
        // Load necessary models
        $this->load->model('knowledge_base_model');
        $this->load->model('departments_model');
		 $this->load->model('casediary_model');

        $data['statuses']                       = $this->tickets_model->get_ticket_status();
        $data['statuses_reassign']             = $this->tickets_model->get_ticket_reassign_status();
        $data['statuses']['callback_translate'] = 'ticket_status_translate';

        $data['departments']        = $this->departments_model->get();
        $data['predefined_replies'] = $this->tickets_model->get_predefined_reply();
        $data['priorities']         = $this->tickets_model->get_priority();
        $data['services']           = $this->tickets_model->get_service('',['service_slug!='=>'expense']);
        $data['document_types']    = $this->casediary_model->get_document_types_bycategory('6');
        $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
		$this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();
		$data['credit_payment']		  = get_credit_payment();
		$data['credit_cheque']        = get_credit_cheque();
        $data['staff']                = $this->staff_model->get('', $whereStaff);
        $data['articles']             = $this->knowledge_base_model->get();
        $data['ticket_replies']       = $this->tickets_model->get_ticket_replies($id);
        $data['bodyclass']            = 'top-tabs ticket single-ticket';
        $data['title']                = $data['ticket']->subject;
        $data['ticket']->ticket_notes = $this->misc_model->get_notes($id, 'ticket');
		 if ($this->input->get('confirmation')) {
            $data['confirmapproval'] = $this->input->get('confirmation');
        }
		else{
			 $data['confirmapproval'] ='request';
		 }
        add_admin_tickets_js_assets();
        $this->load->view('admin/tickets/single', $data);
    }

    public function edit_message()
    {
        if ($this->input->post()) {
            $data         = $this->input->post();
            $data['data'] = html_purify($this->input->post('data', false));

            if ($data['type'] == 'reply') {
                $this->db->where('id', $data['id']);
                $this->db->update(db_prefix().'ticket_replies', [
                    'message' => $data['data'],
                ]);
            } elseif ($data['type'] == 'ticket') {
                $this->db->where('ticketid', $data['id']);
                $this->db->update(db_prefix().'tickets', [
                    'message' => $data['data'],
                ]);
            }
            if ($this->db->affected_rows() > 0) {
                set_alert('success', _l('ticket_message_updated_successfully'));
            }
            redirect(admin_url('tickets/ticket/' . $data['main_ticket']));
        }
    }

    public function delete_ticket_reply($ticket_id, $reply_id)
    {
        if (!$reply_id) {
            redirect(admin_url('tickets'));
        }
        $response = $this->tickets_model->delete_ticket_reply($ticket_id, $reply_id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_reply')));
        }
        redirect(admin_url('tickets/ticket/' . $ticket_id));
    }

    public function change_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tickets_model->change_ticket_status($id, $status));
        }
    }

    public function update_single_ticket_settings()
    {
        if ($this->input->post()) {
			$data=$this->input->post();
            $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
			if($this->input->post('civilcase_fileddet')!=''){
			   $data['civilcase_fileddet'] = json_encode($this->input->post('civilcase_fileddet'));
		  	}
			if($this->input->post('ldc_chequedet')!=''){
			   $data['ldc_chequedet'] = json_encode($this->input->post('ldc_chequedet'));
		  	}
			if($this->input->post('ldc_chequeothers')!=''){
			   $data['ldc_chequeothers'] = json_encode($this->input->post('ldc_chequeothers'));
		  	}
            $success = $this->tickets_model->update_single_ticket_settings($data);
            if ($success) {
                $this->session->set_flashdata('active_tab', true);
                $this->session->set_flashdata('active_tab_settings', true);
                if (get_option('staff_access_only_assigned_departments') == 1) {
                    $ticket = $this->tickets_model->get_ticket_by_id($this->input->post('ticketid'));
                    $this->load->model('departments_model');
                    $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    if (!in_array($ticket->department, $staff_departments) && !is_admin()) {
                        set_alert('success', _l('ticket_settings_updated_successfully_and_reassigned', $ticket->department_name));
                        echo json_encode([
                            'success'               => $success,
                            'department_reassigned' => true,
                        ]);
                        die();
                    }
                }
                set_alert('success', _l('ticket_settings_updated_successfully'));
            }
            echo json_encode([
                'success' => $success,
            ]);
            die();
        }
    }

    // Priorities
    /* Get all ticket priorities */
    public function priorities()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        $data['priorities'] = $this->tickets_model->get_priority();
        $data['title']      = _l('ticket_priorities');
        $this->load->view('admin/tickets/priorities/manage', $data);
    }

    /* Add new priority od update existing*/
    public function priority()
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->tickets_model->add_priority($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ticket_priority')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->tickets_model->update_priority($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ticket_priority')));
                }
            }
            die;
        }
    }

    /* Delete ticket priority */
    public function delete_priority($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Priorities');
        }
        if (!$id) {
            redirect(admin_url('tickets/priorities'));
        }
        $response = $this->tickets_model->delete_priority($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('ticket_priority_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_priority')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_priority_lowercase')));
        }
        redirect(admin_url('tickets/priorities'));
    }

    /* List all ticket predefined replies */
    public function predefined_replies()
    {
        if (!is_admin()) {
            access_denied('Predefined Replies');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'name',
            ];
            $sIndexColumn = 'id';
            $sTable       = db_prefix().'tickets_predefined_replies';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
                'id',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="' . admin_url('tickets/predefined_reply/' . $aRow['id']) . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options            = icon_btn('tickets/predefined_reply/' . $aRow['id'], 'pencil-square-o');
                $row[]              = $options .= icon_btn('tickets/delete_predefined_reply/' . $aRow['id'], 'remove', 'btn-danger _delete');
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('predefined_replies');
        $this->load->view('admin/tickets/predefined_replies/manage', $data);
    }

    public function get_predefined_reply_ajax($id)
    {
        echo json_encode($this->tickets_model->get_predefined_reply($id));
    }

    public function ticket_change_data()
    {
        if ($this->input->is_ajax_request()) {
            $contact_id = $this->input->post('contact_id');
            echo json_encode([
                'contact_data'          => $this->clients_model->get_contact($contact_id),
                'customer_has_projects' => customer_has_projects(get_user_id_by_contact_id($contact_id)),
            ]);
        }
    }

    /* Add new reply or edit existing */
    public function predefined_reply($id = '')
    {
        if (!is_admin() && get_option('staff_members_save_tickets_predefined_replies') == '0') {
            access_denied('Predefined Reply');
        }
        if ($this->input->post()) {
            $data              = $this->input->post();
            $data['message']   = html_purify($this->input->post('message', false));
            $ticketAreaRequest = isset($data['ticket_area']);

            if (isset($data['ticket_area'])) {
                unset($data['ticket_area']);
            }

            if ($id == '') {
                $id = $this->tickets_model->add_predefined_reply($data);
                if (!$ticketAreaRequest) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('predefined_reply')));
                        redirect(admin_url('tickets/predefined_reply/' . $id));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : false, 'id' => $id]);
                    die;
                }
            } else {
                $success = $this->tickets_model->update_predefined_reply($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('predefined_reply')));
                }
                redirect(admin_url('tickets/predefined_reply/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('predefined_reply_lowercase'));
        } else {
            $predefined_reply         = $this->tickets_model->get_predefined_reply($id);
            $data['predefined_reply'] = $predefined_reply;
            $title                    = _l('edit', _l('predefined_reply_lowercase')) . ' ' . $predefined_reply->name;
        }
        $data['title'] = $title;
        $this->load->view('admin/tickets/predefined_replies/reply', $data);
    }

    /* Delete ticket reply from database */
    public function delete_predefined_reply($id)
    {
        if (!is_admin()) {
            access_denied('Delete Predefined Reply');
        }
        if (!$id) {
            redirect(admin_url('tickets/predefined_replies'));
        }
        $response = $this->tickets_model->delete_predefined_reply($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('predefined_reply')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('predefined_reply_lowercase')));
        }
        redirect(admin_url('tickets/predefined_replies'));
    }

    // Ticket statuses
    /* Get all ticket statuses */
    public function statuses()
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        $data['statuses'] = $this->tickets_model->get_ticket_status();
        $data['title']    = 'Ticket statuses';
        $this->load->view('admin/tickets/tickets_statuses/manage', $data);
    }

    /* Add new or edit existing status */
    public function status()
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->tickets_model->add_ticket_status($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('ticket_status')));
                }
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->tickets_model->update_ticket_status($data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('ticket_status')));
                }
            }
            die;
        }
    }

    /* Delete ticket status from database */
    public function delete_ticket_status($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Statuses');
        }
        if (!$id) {
            redirect(admin_url('tickets/statuses'));
        }
        $response = $this->tickets_model->delete_ticket_status($id);
        if (is_array($response) && isset($response['default'])) {
            set_alert('warning', _l('cant_delete_default', _l('ticket_status_lowercase')));
        } elseif (is_array($response) && isset($response['referenced'])) {
            set_alert('danger', _l('is_referenced', _l('ticket_status_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('ticket_status')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('ticket_status_lowercase')));
        }
        redirect(admin_url('tickets/statuses'));
    }

    /* List all ticket services */
    public function services()
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if ($this->input->is_ajax_request()) {
            $aColumns = [
                'name','shortname',
            ];
            $sIndexColumn = 'serviceid';
            $sTable       = db_prefix().'services';
            $result       = data_tables_init($aColumns, $sIndexColumn, $sTable, [], [], [
                'serviceid',
            ]);
            $output  = $result['output'];
            $rResult = $result['rResult'];
            foreach ($rResult as $aRow) {
                $row = [];
                for ($i = 0; $i < count($aColumns); $i++) {
                    $_data = $aRow[$aColumns[$i]];
                    if ($aColumns[$i] == 'name') {
                        $_data = '<a href="#" onclick="edit_service(this,' . $aRow['serviceid'] . ');return false" data-name="' . $aRow['name'] . '" data-type="' . $aRow['shortname'] . '">' . $_data . '</a>';
                    }
                    $row[] = $_data;
                }
                $options = icon_btn('#', 'pencil-square-o', 'btn-default', [
                    'data-name' => $aRow['name'],
                     'data-type' => $aRow['shortname'],
                    'onclick'   => 'edit_service(this,' . $aRow['serviceid'] . '); return false;',
                ]);
				if( $aRow['serviceid']>12){
               $options .= icon_btn('tickets/delete_service/' . $aRow['serviceid'], 'remove', 'btn-danger _delete');
				}
				$row[]              = $options;
                $output['aaData'][] = $row;
            }
            echo json_encode($output);
            die();
        }
        $data['title'] = _l('services');
        $this->load->view('admin/tickets/services/manage', $data);
    }

    /* Add new service od delete existing one */
    public function service($id = '')
    {
        if (!is_admin() && get_option('staff_members_save_tickets_predefined_replies') == '0') {
            access_denied('Ticket Services');
        }

        if ($this->input->post()) {
            $post_data = $this->input->post();
            if (!$this->input->post('id')) {
                $requestFromTicketArea = isset($post_data['ticket_area']);
                if (isset($post_data['ticket_area'])) {
                    unset($post_data['ticket_area']);
                }
				$post_data['service_slug']=str_replace('','_',$post_data['name']);
                $id = $this->tickets_model->add_service($post_data);
                if (!$requestFromTicketArea) {
                    if ($id) {
                        set_alert('success', _l('added_successfully', _l('service')));
                    }
                } else {
                    echo json_encode(['success' => $id ? true : false, 'id' => $id, 'name' => $post_data['name']]);
                }
            } else {
                $id = $post_data['id'];
                unset($post_data['id']);
				$post_data['service_slug']=str_replace('','_',$post_data['name']);
                $success = $this->tickets_model->update_service($post_data, $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('service')));
                }
            }
            die;
        }
    }

    /* Delete ticket service from database */
    public function delete_service($id)
    {
        if (!is_admin()) {
            access_denied('Ticket Services');
        }
        if (!$id) {
            redirect(admin_url('tickets/services'));
        }
        $response = $this->tickets_model->delete_service($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('service_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('service')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('service_lowercase')));
        }
        redirect(admin_url('tickets/services'));
    }

    public function block_sender()
    {
        if ($this->input->post()) {
            $this->load->model('spam_filters_model');
            $sender  = $this->input->post('sender');
            $success = $this->spam_filters_model->add(['type' => 'sender', 'value' => $sender], 'tickets');
            if ($success) {
                set_alert('success', _l('sender_blocked_successfully'));
            }
        }
    }

    public function bulk_action()
    {
        hooks()->do_action('before_do_bulk_action_for_tickets');
        if ($this->input->post()) {
            $total_deleted = 0;
            $ids           = $this->input->post('ids');
            $status        = $this->input->post('status');
            $department    = $this->input->post('department');
            $service       = $this->input->post('service');
            $priority      = $this->input->post('priority');
            $tags          = $this->input->post('tags');
            $is_admin      = is_admin();
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($is_admin) {
                            if ($this->tickets_model->delete($id)) {
                                $total_deleted++;
                            }
                        }
                    } else {
                        if ($status) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix().'tickets', [
                                'status' => $status,
                            ]);
                        }
                        if ($department) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix().'tickets', [
                                'department' => $department,
                            ]);
                        }
                        if ($priority) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix().'tickets', [
                                'priority' => $priority,
                            ]);
                        }

                        if ($service) {
                            $this->db->where('ticketid', $id);
                            $this->db->update(db_prefix().'tickets', [
                                'service' => $service,
                            ]);
                        }
                        if ($tags) {
                            handle_tags_save($tags, $id, 'ticket');
                        }
                    }
                }
            }

            if ($this->input->post('mass_delete')) {
                set_alert('success', _l('total_tickets_deleted', $total_deleted));
            }
        }
    }
	 public function legal_approval($id)
    {
      /*  if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }*/
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
        $legalapprove->approval =  get_approvals($id,'ticket');
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		$legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
       	if($legalapprove->service==get_option('ticket_civilcase_service')){
		$legalapprove->civils= $this->tickets_model->get_civil_details_by_ticket_id($id);	
		}
			
 	
	    try {
            $pdf = legal_approval_pdf($legalapprove);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
         ob_end_clean();libxml_use_internal_errors(true);
        $pdf->Output(slug_it($legalapprove->subject) . '.pdf', $type);
    }   
	
	 public function legal_police_approval($id)
    {
      /*  if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }*/
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
        $legalapprove->approval =  get_approvals($id,'ticket');
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		$legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
       	if($legalapprove->service==get_option('ticket_policecase_service')){
		$legalapprove->civils= $this->tickets_model->get_police_details_by_ticket_id($id);	
		}
			
 	
	    try {
            $pdf = legal_police_approval_pdf($legalapprove);
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
	 public function legal_general_approval($id)
    {
      /*  if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }*/
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
        $legalapprove->approval =  get_approvals($id,'ticket');
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		$legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
		$legalapprove->creditapp=$this->tickets_model->get_ticket_crattachments($id);
       
	    try {
            $pdf = legal_general_approval_pdf($legalapprove);
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
		 public function legal_creditrevision_approval($id)
		 {
      
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
        $legalapprove->approval =  get_approvals($id,'ticket');
		$legalapprove->creditapproval=$this->tickets_model->get_legalapproval_details_by_ticket_id($id);
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		$legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
		$legalapprove->creditapp=$this->tickets_model->get_ticket_crattachments($id);
       
	    try {
            $pdf = legal_creditrevision_approval_pdf($legalapprove);
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
		 public function legal_creditapplication_approval($id)
		 {
      
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
        $legalapprove->approval =  get_approvals($id,$legalapprove->service_slug);
		$legalapprove->creditapproval=$this->tickets_model->get_legalapproval_details_by_ticket_id($id);
		$legalapprove->branchapproval=$this->tickets_model->get_branchapproval_details_by_ticket_id($id);
		$legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		$legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
		$legalapprove->creditapp=$this->tickets_model->get_ticket_crattachments($id);
       
	    try {
            $pdf = legal_creditapplication_approval_pdf($legalapprove);
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
	 public function legal_chequehold_approval($id)
    {
      /*  if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }*/
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		  $legalapprove->approval =  get_approvals($id,'ticket');
         $legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
	
       
	    try {
            $pdf = legal_chequehold_approval_pdf($legalapprove);
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
		 public function legal_pdc_approval($id)
    {
      /*  if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }*/
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		  $legalapprove->approval =  get_approvals($id,'ticket');
         $legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
	
       
	    try {
            $pdf = legal_pdc_approval_pdf($legalapprove);
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
	/* Chequehold approval PDF*/
		 public function legal_close_approval($id)
    {
      /*  if (!has_permission('projects', '', 'view') && !has_permission('projects', '', 'view_own')) {
            access_denied('projects');
        }*/
        if (!$id) {
            redirect(admin_url('tickets'));
        }
        $legalapprove =  $this->tickets_model->get_ticket_by_id($id);
		 $legalapprove->bmapproval = get_branchapproval_by_id($id,'ticket',3);
		  $legalapprove->approval =  get_approvals($id,'ticket');
         $legaltasks = $this->tickets_model->get_ticket_tasks($id);
		$legalapprove->legaltask=$legaltasks;
	
       
	    try {
            $pdf = legal_close_approval_pdf($legalapprove);
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
	
    public function update_single_ticket_approvals()
    {
		 $this->load->model('approval_model');
        if ($this->input->post()) {
			 $approvalcount = $this->db->get_where('tblapprovals',array('rel_id'=>$this->input->post('rel_id'),'rel_type'=>$this->input->post('rel_type')))->num_rows();
			if($approvalcount>0){
				 echo json_encode([
                            'success'               => true,
                            'message' => 'Approval Already Exist',
                        ]);
                        die();
			}else{
            $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_approvals');
            $success = $this->tickets_model->update_single_ticket_approvals($this->input->post());
            if ($success) {
			//	$refno_value=$this->input->post('approval_name');
			  $ticket = $this->tickets_model->get_ticket_by_id($this->input->post('rel_id'));
				  $this->db->where('userid', $ticket->userid);
            $this->db->set('legal_count', 'legal_count+1', false);
            $this->db->update('tblclients');
				 /* $this->db->where('userid', $ticket->userid);
                $this->db->update(db_prefix().'tickets', [
                    'message' => $data['data'],
                ]);*/
                $this->session->set_flashdata('active_tab', true);
                $this->session->set_flashdata('active_tab_approvals', true);
                if (get_option('staff_access_only_assigned_departments') == 1) {
                  
                    $this->load->model('departments_model');
                    $staff_departments = $this->departments_model->get_staff_departments(get_staff_user_id(), true);
                    if (!in_array($ticket->department, $staff_departments) && !is_admin()) {
                        set_alert('success', _l('ticket_settings_updated_successfully_and_reassigned', $ticket->department_name));
                        echo json_encode([
                            'success'               => $success,
                            'department_reassigned' => true,
							
                        ]);
                        die();
                    }
                }
                set_alert('success', _l('ticket_settings_updated_successfully'));
            }
            echo json_encode([
                'success' => $success,
				'message' => 'Successfully Added',
            ]);
            die();
			}
        }
		$this->load->model('staff_model'); 
		
		 $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
		echo $data['rel_name']=$this->input->get('rel_name');
		echo $data['rel_id']=$this->input->get('rel_id');
        $data['staffs']                = $this->staff_model->get('', $whereStaff);
		 $data['approval_headings'] = $this->approval_model->get('',['rel_type'=>$data['rel_name'],'active'=>1]);
		 $data['statuses']          = $this->tickets_model->get_ticket_status();
		 $this->load->view('admin/tickets/modal/ticketapproval_modal', $data);
    }

    public function change_approval_status_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tickets_model->change_approval_status($id, $status));
        }
    }
	 public function change_approval_staff_ajax($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tickets_model->change_approval_staff($id, $status));
        }
    }

    public function change_approval_remarks_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
            echo json_encode($this->tickets_model->change_approval_remarks($id, $this->input->post('remarks')));
        }
    }
	public function change_approval_credits_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
			
            echo json_encode($this->tickets_model->change_approval_credits($id, $this->input->post('credit_period')));
        }
    }
	public function change_approval_creditamounts_ajax($id)
    {
        if ($this->input->is_ajax_request()) {
			 echo json_encode($this->tickets_model-> change_approval_creditamounts($id, $this->input->post('credit_amount')));
        }
    }
	
  /* Add or update civil request*/
   public function save_single_ticket_civil($id = '')
    {
        
		  if ($this->input->post()) {
          $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
			$id=  $this->input->post('id');
			   $ticketid = $this->input->post('ticketid');
			   $data=$this->input->post();
		
			   if($this->input->post('overdue_detail')!=''){
			   $data['overdue_detail'] = json_encode($this->input->post('overdue_detail'));
		  	}
			  if($this->input->post('return_chq_details')!=''){
			   $data['return_chq_details'] = json_encode($this->input->post('return_chq_details'));
		  	}
			    if($this->input->post('addreturn_chq_details')!=''){
			   $data['addreturn_chq_details'] = json_encode($this->input->post('addreturn_chq_details'));
		  	}
			   if($this->input->post('pdc_in_hand')!=''){
			   $data['pdc_in_hand'] = json_encode($this->input->post('pdc_in_hand'));
		  	}
			if($this->input->post('guarantee_chequedet')!=''){
			   $data['guarantee_chequedet'] = json_encode($this->input->post('guarantee_chequedet'));
		  	}
			  if($this->input->post('owner_detail')!=''){
			   $data['owner_detail'] = json_encode($this->input->post('owner_detail'));
		  	}
            if ($id == '') {
				
               $rid = $this->tickets_model->add_civilrequest($data);
				
                $message = $rid ? _l('added_successfully', _l('ticket_single_add_civil')) : '';
				//set_alert('success', _l('added_successfully', _l('ticket_single_add_civil')));
                echo json_encode([
                    'success'  => $rid ? true : false,
                    'id'       => $rid,
                    'message'  => $message,
				 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                   
                ]);
            } else {
                
                $message         = '';
                $success = $this->tickets_model->update_civilrequest($data, $id);
                if ($success) {
                    $message = _l('updated_successfully', _l('ticket_single_add_civil'));
					//	set_alert('success', _l('updated_successfully', _l('ticket_single_add_civil')));
               
              /* echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
					 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                ]);*/
            }
			}
            die;
        }

      /*  echo json_encode([
         'url'       => admin_url('tickets/ticket/' . $ticketid ),
        ]);*/
    }
				     /* Add or update police request*/
   public function save_single_ticket_police($id = '')
    {
          $message         = '';
		  if ($this->input->post()) {
          $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
			$id=  $this->input->post('id');
			   $ticketid = $this->input->post('ticketid');
			  $data=$this->input->post();
			  if($this->input->post('return_chq_details')!=''){
			   $data['return_chq_details'] = json_encode($this->input->post('return_chq_details'));
		  	}
			   if($this->input->post('pdc_in_hand')!=''){
			   $data['pdc_in_hand'] = json_encode($this->input->post('pdc_in_hand'));
		  	}
			if($this->input->post('guarantee_chequedet')!=''){
			   $data['guarantee_chequedet'] = json_encode($this->input->post('guarantee_chequedet'));
		  	}
			  if($this->input->post('owner_detail')!=''){
			   $data['owner_detail'] = json_encode($this->input->post('owner_detail'));
		  	}
			//  print_r($this->input->post('return_chq_details'));
            if ($id == '') {
				
               $rid = $this->tickets_model->add_policerequest($data);
				
                $message = $rid ? _l('added_successfully', _l('ticket_single_add_police')) : '';
				//set_alert('success', _l('added_successfully', _l('ticket_single_add_civil')));
                echo json_encode([
                    'success'  => $rid ? true : false,
                    'id'       => $rid,
                    'message'  => $message,
				 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                   
                ]);
            } else {
                
              
                $success = $this->tickets_model->update_policerequest($data, $id);
				
                if ($success) {
                    $message = _l('updated_successfully', _l('ticket_single_add_police'));
						set_alert('success', _l('updated_successfully', _l('ticket_single_add_civil')));
               
                echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
					 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                ]);
            }
			}
           
        }

      /*  echo json_encode([
			  'message'          => _l('updated_successfully'),
         'url'       => admin_url('tickets/ticket/' . $ticketid ),
        ]);*/
    }
	 public function editcredit($docid)
    {
		
		$data['nationality']=get_countryproject();
		$this->load->model('casediary_model');
		$data['document_types']    = $this->casediary_model->get_document_types_bycategory('6');
		$data['creditdoc'] = $this->db->get_where('tblticket_attachments' , array('id' => $docid) )->row();
		 $this->load->view('admin/tickets/creditdocument', $data);

    }
	 public function addcredit($ticketid)
    {
		
		$data['nationality']=get_countryproject();
		$data['ticketid']=$ticketid;
		$this->load->model('casediary_model');
		$data['document_types']    = $this->casediary_model->get_document_types_bycategory('6');
		$this->load->view('admin/tickets/creditdocumentadd', $data);

    }
	public function editcreditapp($docid=''){
		
		$data = $this->input->post();
		if(isset($data['crattachments'])){
			unset($data['crattachments']);
		}
		$ticketid= $this->input->post('ticketid');
		
	   
		if($docid!=''){
			 handle_credit_attach_file_upload($docid,$ticketid);
			$this->db->where('id',$docid);

			if($this->db->update('tblticket_attachments', $data)===TRUE)		// using direct parameter

			{		

			 set_alert('success', _l('credit_ticket_updated_successfully', $ticketid));
             redirect(admin_url('tickets/ticket/' . $ticketid));

			}	
		}else{
			
			$result=$this->db->insert('tblticket_attachments', $data);
			$doc_ID = $this->db->insert_id();
			 if($result>0){
				  handle_credit_attach_file_upload($doc_ID,$ticketid);
				  set_alert('success', _l('credit_ticket_updated_successfully', $ticketid));
				 redirect(admin_url('tickets/ticket/' . $ticketid));
			 }
		}
	}
	 /* Add or update Legal Dept Approval request*/
   public function save_single_ticket_legalapprovals($id = '')
    {
        
		  if ($this->input->post()) {
          $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
			$id=  $this->input->post('id');
			   $ticketid = $this->input->post('ticketid');
			   $data=$this->input->post();
		
            if ($id == '') {
				
               $rid = $this->tickets_model->add_legalapproval($data);
				
                $message = $rid ? _l('added_successfully') : '';
				set_alert('success', _l('added_successfully'));
                echo json_encode([
                    'success'  => $rid ? true : false,
                    'id'       => $rid,
                    'message'  => $message,
				// 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                   
                ]);
            } else {
                
                $message         = '';
                $success = $this->tickets_model->update_legalapproval($data, $id);
                if ($success) {
                    $message = _l('updated_successfully');
						set_alert('success', _l('updated_successfully'));
               
               echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
					// 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                ]);
            }
			}
            die;
        }

      /*  echo json_encode([
         'url'       => admin_url('tickets/ticket/' . $ticketid ),
        ]);*/
    }
	
	 /* Add or update Legal Dept Approval request*/
   public function save_single_ticket_branchapprovals($id = '')
    {
        
		  if ($this->input->post()) {
          $this->session->mark_as_flash('active_tab');
            $this->session->mark_as_flash('active_tab_settings');
			$id=  $this->input->post('id');
			   $ticketid = $this->input->post('ticketid');
			   $data=$this->input->post();
		
            if ($id == '') {
				
               $rid = $this->tickets_model->add_branchapproval($data);
				
                $message = $rid ? _l('added_successfully') : '';
				set_alert('success', _l('added_successfully'));
                echo json_encode([
                    'success'  => $rid ? true : false,
                    'id'       => $rid,
                    'message'  => $message,
				// 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                   
                ]);
            } else {
                
                $message         = '';
                $success = $this->tickets_model->update_branchapproval($data, $id);
                if ($success) {
                    $message = _l('updated_successfully');
						set_alert('success', _l('updated_successfully'));
               
               echo json_encode([
                    'success'          => $success,
                    'message'          => $message,
                    'id'               => $id,
					// 'url'       => admin_url('tickets/ticket/' . $ticketid ),
                ]);
            }
			}
            die;
        }

      /*  echo json_encode([
         'url'       => admin_url('tickets/ticket/' . $ticketid ),
        ]);*/
    }
	public function getTicketInfo() {
		 $postData=$this->input->post('ticketid');
         $data = $this->tickets_model->get($postData);
 
        echo json_encode($data);
    }	  

    public function add_reassign($id = '')
    {
        
		  if ($this->input->post()) {
         
			
			   $ticketid = $this->input->post('ticket_id');
			   $data=$this->input->post();
		
           
				
               $rid = $this->tickets_model->add_reassign($data);
				
                
				set_alert('success', _l('added_successfully'));
                redirect(admin_url('tickets/ticket/' . $ticketid));
           
        }
    
    
}

public function update_reassign_status(){
    $note_id = $this->input->post('note_id');
    $status = $this->input->post('status');
    $this->db->where('id',$note_id);
    $end_date=null;
    if($status==6){
        $end_date= date('Y-m-d H:i:s');
    }
    $this->db->update('ticket_reassign',['status'=>$status,'end_date'=>$end_date]);
    if($this->db->affected_rows() > 0){
        echo json_encode(array('success' => true,'message'=>_l('updated_successfully')));
    }

    return false;
}

public function get_interval($id){
    // echo $date;
    $data = $this->tickets_model->get_reassign_latest($id);
    // print_r($data);
    if(!empty($data->date_added)){
    $current_datetime = new DateTime(); 
    $datetime_a = new DateTime($data->date_added);
     $interval = $current_datetime->diff($datetime_a);
     $difference_in_days = $interval->days;
      $difference_in_hours = $interval->h; 
     $difference_in_minutes = $interval->i;
      $difference_in_seconds = $interval->s; 
     $hours_display = str_pad($difference_in_hours, 2, '0', STR_PAD_LEFT);
      $minutes_display = str_pad($difference_in_minutes, 2, '0', STR_PAD_LEFT); 
      $seconds_display = str_pad($difference_in_seconds, 2, '0', STR_PAD_LEFT);
      echo $difference_in_days . " days, " . $difference_in_hours . "H :" . $difference_in_minutes."M ";
}else{
    echo '';
    // return false;
}
}


}
