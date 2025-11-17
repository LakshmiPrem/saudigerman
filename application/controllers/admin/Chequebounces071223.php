<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Chequebounces extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('chequebounces_model');
		 $this->load->model('contracts_model');
    }

    /* List all contracts */
    public function index()
    {
        close_setup_menu();
		 if (!has_permission('chequebounces', '', 'view') && !has_permission('chequebounces', '', 'view_own')) {
            if (!have_assigned_chequebounces() && !has_permission('chequebounces', '', 'create')) {
                access_denied('chequebounces');
            }
        }
       
        $data['expiring']               = count_hold_chequebounces();//$this->chequebounces_model->get_contracts_about_to_expire(get_staff_user_id());
        $data['count_active']           = count_active_chequebounces();
        $data['count_expired']          = count_expired_chequebounces();
        $data['count_recently_created'] = count_open_chequebounces();
		$data['count_recent_retcheque']=count_recently_created_chequebounces();
        $data['count_trash']            = count_trash_chequebounces();
        $data['chart_types']            = json_encode($this->chequebounces_model->get_contracts_types_chart_data());
        $data['chart_types_values']     = json_encode($this->chequebounces_model->get_contracts_types_values_chart_data());
       // $data['contract_types']         = $this->contracts_model->get_contract_types();
        $data['years']                  = $this->chequebounces_model->get_chequebounces_years();
		$data['cheque_statuses']    = $this->chequebounces_model->get_cheque_status();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title']         = _l('chequebounces');
        $this->load->view('admin/chequebounces/manage', $data);
    }

    public function table($clientid = '')
    {
		
		 if (!has_permission('chequebounces', '', 'view') && !has_permission('chequebounces', '', 'view_own') && !have_assigned_chequebounces()) {
          //  if (!have_assigned_chequebounces() && !has_permission('chequebounces', '', 'create')) {
                ajax_access_denied();
          //  }
        }
     

        $this->app->get_table_data('chequebounces', [
            'clientid' => $clientid,
        ]);
    }

    /* Edit contract or add new contract */
    public function chequebounce($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('chequebounces', '', 'create')) {
                    access_denied('chequebounces');
                }
				$data1['customer_code']=$this->input->post('customer_code');
								
				$data1['id']=$id;
				$dcount=$this->chequebounces_model->fetch_customer_numrows($data1);
					if($dcount>0){
						 set_alert('info', _l('dup_successfully', _l('chequebounce')));
                     redirect(admin_url('chequebounces/chequebounce/' . $id));
					}
                $id = $this->chequebounces_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('chequebounce')));
                    redirect(admin_url('chequebounces/chequebounce/' . $id));
                }
            } else {
                if (!has_permission('chequebounces', '', 'edit')) {
                    access_denied('chequebounces');
                }
                $success = $this->chequebounces_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('chequebounce')));
                }
                redirect(admin_url('chequebounces/chequebounce/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('chequebounce_lowercase'));
        } else {
            $data['contract']                 = $this->chequebounces_model->get($id, [], true);
            $data['contract_renewal_history'] = $this->chequebounces_model->get_chequebounces_return($id);
            $data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'chequebounce']);
            if (!$data['contract'] || (!has_permission('chequebounces', '', 'view') && !have_assigned_chequebounces() && $data['chequebounces']->addedfrom != get_staff_user_id())) {
                blank_page(_l('chequebounces_not_found'));
            }

            $data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract', ['other', 'client'], '{email_signature}');

            $title = $data['contract']->subject;

            $data = array_merge($data, prepare_mail_preview_data('contract_send_to_customer', $data['contract']->client));
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
        $this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
		$data['project_members'] = $this->chequebounces_model->get_chequebounce_members($id);
		 $data['staff']    = $this->staff_model->get('', ['active' => 1]);
		$data['cheque_statuses']    = $this->chequebounces_model->get_cheque_status();
       // $data['types']         = $this->contracts_model->get_contract_types();
        $data['title']         = $title;
        $data['bodyclass']     = 'contract';
        $this->load->view('admin/chequebounces/chequebounce', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/chequebounces/templates/' . $name, [], true);
    }

    public function mark_as_signed($id)
    {
        if (!staff_can('edit', 'chequebounces')) {
            access_denied('mark contract as signed');
        }

        $this->chequebounces_model->mark_as_signed($id);

        redirect(admin_url('chequebounces/chequebounce/' . $id));
    }

    public function unmark_as_signed($id)
    {
        if (!staff_can('edit', 'chequebounces')) {
            access_denied('mark contract as signed');
        }

        $this->chequebounces_model->unmark_as_signed($id);

        redirect(admin_url('cchequebounces/chequebounce/' . $id));
    }

    public function pdf($id)
    {
        if (!has_permission('chequebounces', '', 'view') && !has_permission('chequebounces', '', 'view_own')) {
            access_denied('chequebounces');
        }

        if (!$id) {
            redirect(admin_url('chequebounces'));
        }

        $contract = $this->chequebounces_model->get($id);

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

  /*  public function send_to_email($id)
    {
        if (!has_permission('chequebounces', '', 'view') && !has_permission('chequebounces', '', 'view_own')) {
            access_denied('chequebounces');
        }
        $success = $this->chequebounces_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('chequebounces_sent_to_client_success'));
        } else {
            set_alert('danger', _l('chequebounces_sent_to_client_fail'));
        }
        redirect(admin_url('chequebounces/chequebounce/' . $id));
    }*/

    public function add_note($rel_id)
    {
        if ($this->input->post() && (has_permission('chequebounces', '', 'view') || has_permission('chequebounces', '', 'view_own') || have_assigned_chequebounces())) {
            $this->misc_model->add_note($this->input->post(), 'chequebounce', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if ((has_permission('chequebounces', '', 'view') || has_permission('chequebounces', '', 'view_own')|| have_assigned_chequebounces())) {
            $data['notes'] = $this->misc_model->get_notes($id, 'chequebounce');
			
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('chequebounces', '', 'delete')) {
            $this->chequebounces_model->clear_signature($id);
        }

        redirect(admin_url('chequebounces/chequebounce/' . $id));
    }

   
    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->chequebounces_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->chequebounces_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_comments($id)
    {
        $data['comments'] = $this->chequebounces_model->get_comments($id);
        $this->load->view('admin/chequebounces/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'chequebounce_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->chequebounces_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }


    public function copy($id)
    {
        if (!has_permission('chequebounces', '', 'create')) {
            access_denied('chequebounces');
        }
        if (!$id) {
            redirect(admin_url('chequebounces'));
        }
        $newId = $this->chequebounces_model->copy($id);
        if ($newId) {
            set_alert('success', _l('contract_copied_successfully'));
        } else {
            set_alert('warning', _l('contract_copied_fail'));
        }
        redirect(admin_url('chequebounces/chequebounce/' . $newId));
    }

    /* Delete contract from database */
    public function delete($id)
    {
        if (!has_permission('chequebounces', '', 'delete')) {
            access_denied('chequebounces');
        }
        if (!$id) {
            redirect(admin_url('chequebounces'));
        }
        $response = $this->chequebounces_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('chequebounce')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('chequebounce_lowercase')));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('chequebounces'));
        }
    }

 
    public function add_contract_attachment($id)
    {
        handle_chequebounce_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database(
                $this->input->post('contract_id'),
                'chequebounce',
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
  public function renew($party_id, $contact_id = '')
    {
        if (!has_permission('chequebounces', '', 'create') && !has_permission('chequebounces', '', 'edit')) {
            access_denied('chequebounces');
        }
          $data['bounce_id'] = $party_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();
			
            unset($data['contactid']);
            if ($contact_id == '') {
            if (!has_permission('chequebounces', '', 'create')) {
                   
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    
                }
                $id      = $this->chequebounces_model->add_renew($data, $party_id);
                $message = '';
                $success = false;
                if ($id) {
					 $success = true;
                    $message = _l('added_successfully', _l('chequebounces_return'));
                }
               
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    ));
                die;
            } else {
                if (!has_permission('chequebounces', '', 'edit')) {
                    
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;

                }
               // $original_contact = $this->projects_model->get_installment($contact_id);
                $success          = $this->chequebounces_model->update_renew($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('chequebounces_return'));
                }
                
          
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                 //   'original_email' => $original_email,
                    'has_primary_contact'=>true,
                 //   'totalpaid'=>$totalpaid,
                ));
                die;
            }
			  
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('chequebounces_return'));
        } else {
            $data['contact'] = $this->chequebounces_model->get_renew($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = _l('edit', _l('chequebounces_return'));
        }
		//$data['party_contacts'] = $this->oppositeparty_model->get_contacts($id);
		 // $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/chequebounces/renew_chequebounces', $data);
    }

	  public function delete_renewal($renewal_id, $contractid)
    {
		 if (!has_permission('chequebounces', '', 'delete')) {
               access_denied('chequebounces');
        }
        $success = $this->chequebounces_model->delete_renewal($renewal_id);
        if ($success) {
            set_alert('success', _l('chequebounces_renewal_deleted'));
        } else {
            set_alert('warning', _l('chequebounces_renewal_delete_fail'));
        }
        redirect(admin_url('chequebounces/chequebounce/' . $contractid . '?tab=renewals'));
    }
	    public function detailed_overview()
    {
        $overview = [];

        $has_permission_create = has_permission('chequebounces', '', 'create');
        $has_permission_view   = has_permission('chequebounces', '', 'view');

        if (!$has_permission_view) {
            $staff_id = get_staff_user_id();
        } elseif ($this->input->post('member')) {
            $staff_id = $this->input->post('member');
        } else {
            $staff_id = '';
        }
		 $client_id = ($this->input->post('clientid') ? $this->input->post('clientid') : '');
       
         
     
        $status = ($this->input->post('status') ? $this->input->post('status'):'');

            // Task rel_name
            $sqlTasksSelect = '*,(SELECT sum(cheque_amount) FROM ' . db_prefix() . 'chequebounces_return WHERE bounce_id=' . db_prefix() . 'chequebounces.id) as total_amount';

            // Task logged time
         //   $selectLoggedTime = get_sql_calc_task_logged_time('tmp-task-id');
            // Replace tmp-task-id to be the same like tasks.id
          //  $selectLoggedTime = str_replace('tmp-task-id', db_prefix() . 'tasks.id', $selectLoggedTime);

         /*  

            // Task assignees
            $sqlTasksSelect .= ',' . get_sql_select_task_asignees_full_names() . ' as assignees' . ',' . get_sql_select_task_assignees_ids() . ' as assignees_ids';
*/
            $this->db->select($sqlTasksSelect);
		 if ($client_id && $client_id != '') {
                $this->db->where('client', $client_id);
              
            }
		 if ($status && $status != '') {
                $this->db->where('status', $status);
              
            }

         /*   $this->db->where('MONTH(' . $fetch_month_from . ')', $m);
            $this->db->where('YEAR(' . $fetch_month_from . ')', $year);

            if ($project_id && $project_id != '') {
                $this->db->where('rel_id', $project_id);
                $this->db->where('rel_type', 'project');
            }
			  if ($status) {
                $this->db->where('status', $status);
            }

            $this->db->order_by($fetch_month_from, 'ASC');*/
			
            if (!$has_permission_view) {
                $sqlWhereStaff = '(id IN (SELECT bounceid FROM ' . db_prefix() . 'chequebounces_assigned WHERE staff_id=' . $staff_id . ')';

                // User dont have permission for view but have for create
                // Only show tasks createad by this user.
                if ($has_permission_create) {
                    $sqlWhereStaff .= ' OR addedfrom=' . get_staff_user_id();
                }

                $sqlWhereStaff .= ')';
                $this->db->where($sqlWhereStaff);
            } elseif ($has_permission_view) {
                if (is_numeric($staff_id)) {
                    $this->db->where('(id IN (SELECT bounceid FROM ' . db_prefix() . 'chequebounces_assigned WHERE staff_id=' . $staff_id . '))');
                }
            }
			

          
          
            $overview = $this->db->get(db_prefix() . 'chequebounces')->result_array();
       
       

        $data['members']  = $this->staff_model->get();
        $data['overview'] = $overview;
        $data['years']    = $this->chequebounces_model->get_chequebounces_years();//($this->input->post('month_from') ? $this->input->post('month_from') : 'startdate'));
		$data['cheque_statuses']    = $this->chequebounces_model->get_cheque_status();
       $data['staff_id'] = get_staff_user_id();
        $data['title']    = _l('chequebounce_overview');
        $this->load->view('admin/chequebounces/detailed_overview', $data);
    }  
  
}
