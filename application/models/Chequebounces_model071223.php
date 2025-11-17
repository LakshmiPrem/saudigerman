<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Chequebounces_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contract_types_model');
    }

    /**
     * Get contract/s
     * @param  mixed  $id         contract id
     * @param  array   $where      perform where
     * @param  boolean $for_editor if for editor is false will replace the field if not will not replace
     * @return mixed
     */
    public function get($id = '', $where = [], $for_editor = false)
    {
        $this->db->select('*,' . db_prefix() . 'chequebounces.id as id, ' . db_prefix() . 'chequebounces.addedfrom');
        $this->db->where($where);
   //     $this->db->join(db_prefix() . 'contracts_types', '' . db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type', 'left');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.userid = ' . db_prefix() . 'chequebounces.client');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'chequebounces.id', $id);
            $contract = $this->db->get(db_prefix() . 'chequebounces')->row();
            if ($contract) {
                $contract->attachments = $this->get_contract_attachments('', $contract->id);
               
            }

            return $contract;
        }
        $contracts = $this->db->get(db_prefix() . 'chequebounces')->result_array();
        $i         = 0;
        foreach ($contracts as $contract) {
            $contracts[$i]['attachments'] = $this->get_contract_attachments('', $contract['id']);
            $i++;
        }

        return $contracts;
    }

    /**
     * Select unique contracts years
     * @return array
     */
    public function get_chequebounces_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(retcheque_date)) as year FROM ' . db_prefix() . 'chequebounces_return order BY YEAR(retcheque_date) asc')->result_array();
    }

    /**
     * @param  integer ID
     * @return object
     * Retrieve contract attachments from database
     */
    public function get_contract_attachments($attachment_id = '', $id = '')
    {
        if (is_numeric($attachment_id)) {
            $this->db->where('id', $attachment_id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'chequebounce');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    /**
     * @param   array $_POST data
     * @return  integer Insert ID
     * Add new contract
     */
    public function add($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $data['datestart'] = to_sql_date($data['datestart']);
        unset($data['attachment']);
        if ($data['dateend'] == '') {
            unset($data['dateend']);
        } else {
            $data['dateend'] = to_sql_date($data['dateend']);
        }

        if (isset($data['trash']) && ($data['trash'] == 1 || $data['trash'] === 'on')) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
        }
		 if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }

        if (isset($data['not_visible_to_client']) && ($data['not_visible_to_client'] == 1 || $data['not_visible_to_client'] === 'on')) {
            $data['not_visible_to_client'] = 1;
        } else {
            $data['not_visible_to_client'] = 0;
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }


        $data['hash'] = app_generate_hash();

        $data = hooks()->apply_filters('before_contract_added', $data);

        $this->db->insert(db_prefix() . 'chequebounces', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
			if (isset($project_members)) {
                $_pm['project_members'] = $project_members;
                $this->add_edit_members($_pm, $insert_id);
            }
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            hooks()->do_action('after_contract_added', $insert_id);
            log_activity('New Chequebounces Added [' . $data['subject'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer Contract ID
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;
$data1['customer_code']=$data['customer_code'];
					
				
				$data1['id']=$id;
				$dcount=$this->chequebounces_model->fetch_customer_numrows($data1);
					if($dcount>0){
						 set_alert('info', _l('dup_successfully', _l('chequebounce')));
                     redirect(admin_url('chequebounces/chequebounce/' . $id));
					}
        $data['datestart'] = to_sql_date($data['datestart']);
        if ($data['dateend'] == '') {
            $data['dateend'] = null;
        } else {
            $data['dateend'] = to_sql_date($data['dateend']);
        }
        if (isset($data['trash'])) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
        }
        if (isset($data['not_visible_to_client'])) {
            $data['not_visible_to_client'] = 1;
        } else {
            $data['not_visible_to_client'] = 0;
        }
		 if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }
        $_pm = [];
        if (isset($project_members)) {
            $_pm['project_members'] = $project_members;
        }
        if ($this->add_edit_members($_pm, $id)) {
            $affectedRows++;
        }

        $data = hooks()->apply_filters('before_chequebounce_updated', $data, $id);

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'chequebounces', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_chequebounce_updated', $id);
            log_activity('Chequebounce Updated [' . $data['subject'] . ']');

            return true;
        }

        return $affectedRows > 0;
    }



    /**
    * Add contract comment
    * @param mixed  $data   $_POST comment data
    * @param boolean $client is request coming from the client side
    */
    public function add_comment($data, $client = false)
    {
        if (is_staff_logged_in()) {
            $client = false;
        }

        if (isset($data['action'])) {
            unset($data['action']);
        }

        $data['dateadded'] = date('Y-m-d H:i:s');

        if ($client == false) {
            $data['staffid'] = get_staff_user_id();
        }

        $data['content'] = nl2br($data['content']);
        $this->db->insert(db_prefix() . 'chequebounce_comments', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $contract = $this->get($data['bounce_id']);

            if (($contract->not_visible_to_client == '1' || $contract->trash == '1') && $client == false) {
                return true;
            }

            if ($client == true) {

                // Get creator
                $this->db->select('staffid, email, phonenumber');
                $this->db->where('staffid', $contract->addedfrom);
                $staff_contract = $this->db->get(db_prefix() . 'staff')->result_array();

                $notifiedUsers = [];

                foreach ($staff_contract as $member) {
                    $notified = add_notification([
                        'description'     => 'not_contract_comment_from_client',
                        'touserid'        => $member['staffid'],
                        'fromcompany'     => 1,
                        'fromuserid'      => 0,
                        'link'            => 'chequebounces/chequebounce/' . $data['contract_id'],
                        'additional_data' => serialize([
                            $contract->subject,
                        ]),
                    ]);

                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }

                    $template     = mail_template('contract_comment_to_staff', $contract, $member);
                    $merge_fields = $template->get_merge_fields();
                    $template->send();

                    // Send email/sms to admin that client commented
                    $this->app_sms->trigger(SMS_TRIGGER_CONTRACT_NEW_COMMENT_TO_STAFF, $member['phonenumber'], $merge_fields);
                }
                pusher_trigger_notification($notifiedUsers);
            } else {
                $contacts = $this->clients_model->get_contacts($contract->client, ['active' => 1, 'contract_emails' => 1]);

                foreach ($contacts as $contact) {
                    $template     = mail_template('contract_comment_to_customer', $contract, $contact);
                    $merge_fields = $template->get_merge_fields();
                    $template->send();

                    $this->app_sms->trigger(SMS_TRIGGER_CONTRACT_NEW_COMMENT_TO_CUSTOMER, $contact['phonenumber'], $merge_fields);
                }
            }

            return true;
        }

        return false;
    }

    public function edit_comment($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'chequebounce_comments', [
            'content' => nl2br($data['content']),
        ]);

        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get contract comments
     * @param  mixed $id contract id
     * @return array
     */
    public function get_comments($id)
    {
        $this->db->where('bounce_id', $id);
        $this->db->order_by('dateadded', 'ASC');

        return $this->db->get(db_prefix() . 'chequebounce_comments')->result_array();
    }

    /**
     * Get contract single comment
     * @param  mixed $id  comment id
     * @return object
     */
    public function get_comment($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'chequebounce_comments')->row();
    }

    /**
     * Remove contract comment
     * @param  mixed $id comment id
     * @return boolean
     */
    public function remove_comment($id)
    {
        $comment = $this->get_comment($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'chequebounce_comments');
        if ($this->db->affected_rows() > 0) {
            log_activity('Chequebounce Comment Removed [BounceID:' . $comment->bounce_id . ', Comment Content: ' . $comment->content . ']');

            return true;
        }

        return false;
    }

    public function copy($id)
    {
        $contract       = $this->get($id, [], true);
        $fields         = $this->db->list_fields(db_prefix() . 'tblchequebounces');
        $newContactData = [];

        foreach ($fields as $field) {
            if (isset($contract->$field)) {
                $newContactData[$field] = $contract->$field;
            }
        }

        unset($newContactData['id']);

        $newContactData['trash']            = 0;
        $newContactData['isexpirynotified'] = 0;
        $newContactData['isexpirynotified'] = 0;
        $newContactData['signed']           = 0;
        $newContactData['signature']        = null;

        $newContactData = array_merge($newContactData, get_acceptance_info_array(true));

        if ($contract->dateend) {
            $dStart                    = new DateTime($contract->datestart);
            $dEnd                      = new DateTime($contract->dateend);
            $dDiff                     = $dStart->diff($dEnd);
            $newContactData['dateend'] = _d(date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY')))));
        } else {
            $newContactData['dateend'] = '';
        }

        $newId = $this->add($newContactData);

        if ($newId) {
            $custom_fields = get_custom_fields('contracts');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($id, $field['id'], 'contracts', false);
                if ($value != '') {
                    $this->db->insert(db_prefix() . 'customfieldsvalues', [
                    'relid'   => $newId,
                    'fieldid' => $field['id'],
                    'fieldto' => 'contracts',
                    'value'   => $value,
                    ]);
                }
            }
        }

        return $newId;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete contract, also attachment will be removed if any found
     */
    public function delete($id)
    {
        hooks()->do_action('before_chequebounce_deleted', $id);
      
        $contract = $this->get($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'chequebounces');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('bounce_id', $id);
            $this->db->delete(db_prefix() . 'chequebounce_comments');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'chequebounce');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'chequebounce');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_contract_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'chequebounces');
            $this->db->delete(db_prefix() . 'notes');


            $this->db->where('bounceid', $id);
            $this->db->delete(db_prefix() . 'chequebounces_assigned');
			
			 $this->db->where('bounce_id', $id);
            $this->db->delete(db_prefix() . 'chequebounces_return');
            // Get related tasks
            $this->db->where('rel_type', 'chequebounce');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }

         //   delete_tracked_emails($id, 'contract');

            log_activity('Chequebounces Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Mark the contract as signed manually
     *
     * @param  int $id contract id
     *
     * @return boolean
     */
    public function mark_as_signed($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblchequebounces', ['marked_as_signed' => 1]);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Unmark the contract as signed manually
     *
     * @param  int $id contract id
     *
     * @return boolean
     */
    public function unmark_as_signed($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblchequebounces', ['marked_as_signed' => 0]);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Function that send contract to customer
     * @param  mixed  $id        contract id
     * @param  boolean $attachpdf to attach pdf or not
     * @param  string  $cc        Email CC
     * @return boolean
     */
    public function send_contract_to_client($id, $attachpdf = true, $cc = '')
    {
        $contract = $this->get($id);

        if ($attachpdf) {
            set_mailing_constant();
            $pdf    = contract_pdf($contract);
            $attach = $pdf->Output(slug_it($contract->subject) . '.pdf', 'S');
        }

        $sent_to = $this->input->post('sent_to');
        $sent    = false;

        if (is_array($sent_to)) {
            $i = 0;
            foreach ($sent_to as $contact_id) {
                if ($contact_id != '') {
                    $contact = $this->clients_model->get_contact($contact_id);

                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }

                    $template = mail_template('contract_send_to_customer', $contract, $contact, $cc);

                    if ($attachpdf) {
                        $template->add_attachment([
                            'attachment' => $attach,
                            'filename'   => slug_it($contract->subject) . '.pdf',
                            'type'       => 'application/pdf',
                        ]);
                    }

                    if ($template->send()) {
                        $sent = true;
                    }
                }
                $i++;
            }
        } else {
            return false;
        }
        if ($sent) {
            return true;
        }

        return false;
    }

    /**
     * Delete contract attachment
     * @param  mixed $attachment_id
     * @return boolean
     */
    public function delete_contract_attachment($attachment_id)
    {
        $deleted    = false;
        $attachment = $this->get_contract_attachments($attachment_id);

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('chequebounce') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Cheque bounce Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('contract') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('contract') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('contract') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Renew contract
     * @param  mixed $data All $_POST data
     * @return mixed
     */

function add_renew($data ,$bounce_id){
       
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedby'] = get_staff_user_id();
      $data['bounce_id']=$bounce_id;
           unset($data['customer_id']);
	if( isset($data['retcheque_date']))
		$data['retcheque_date']=to_sql_date($data['retcheque_date'],true);
	
        $this->db->insert('tblchequebounces_return',$data);
        $a = $this->db->error();
        $insert_id = $this->db->insert_id();
	
		return $insert_id;
    }
		public function update_renew($data,$id){
			
		$data['retcheque_date']=to_sql_date($data['retcheque_date'],true);
	
        $this->db->where('id',$id);
        $this->db->update('tblchequebounces_return',$data);
		 if ($this->db->affected_rows() > 0) {
		
            return true;
        }

        return false;
    }
	 public function get_renew($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblchequebounces_return')->row();
    }
    /**
     * Delete contract renewal
     * @param  mixed $id         renewal id
     * @param  mixed $contractid contract id
     * @return boolean
     */
	 public function delete_renewal($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete('tblchequebounces_return');
        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }
   
    /**
     * Get the contracts about to expired in the given days
     *
     * @param  integer|null $staffId
     * @param  integer $days
     *
     * @return array
     */
    public function get_contracts_about_to_expire($staffId = null, $days = 7)
    {
        $diff1 = date('Y-m-d', strtotime('-' . $days . ' days'));
        $diff2 = date('Y-m-d', strtotime('+' . $days . ' days'));

        if ($staffId && ! staff_can('view', 'chequebounces', $staffId)) {
            $this->db->where('addedfrom', $staffId);
        }

        $this->db->select('id,subject,client,datestart,dateend');

        $this->db->where('dateend IS NOT NULL');
        $this->db->where('trash', 0);
        $this->db->where('dateend >=', $diff1);
        $this->db->where('dateend <=', $diff2);

        return $this->db->get(db_prefix() . 'chequebounces')->result_array();
    }

    /**
     * Get contract renewals
     * @param  mixed $id contract id
     * @return array
     */
    public function get_chequebounces_return($id)
    {
        $this->db->where('bounce_id', $id);
        $this->db->order_by('id', 'DESC');

        return $this->db->get(db_prefix() . 'chequebounces_return')->result_array();
    }

   
    /**
     * Get contract types data for chart
     * @return array
     */
    public function get_contracts_types_chart_data()
    {
        return $this->contract_types_model->get_chart_data();
    }

    /**
    * Get contract types values for chart
    * @return array
    */
    public function get_contracts_types_values_chart_data()
    {
        return $this->contract_types_model->get_values_chart_data();
    }
	 public function get_chequebounce_members($id)
    {
        $this->db->select('email,bounceid,staff_id');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'chequebounces_assigned.staff_id');
        $this->db->where('bounceid', $id);

        return $this->db->get(db_prefix() . 'chequebounces_assigned')->result_array();
    }
	
	   public function add_edit_members($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
        }

        $new_project_members_to_receive_email = [];
        $this->db->select('subject,client');
        $this->db->where('id', $id);
        $project      = $this->db->get(db_prefix() . 'chequebounces')->row();
        $project_name = $project->subject;
        $client_id    = $project->client;

        $project_members_in = $this->get_chequebounce_members($id);
        if (sizeof($project_members_in) > 0) {
            foreach ($project_members_in as $project_member) {
                if (isset($project_members)) {
                    if (!in_array($project_member['staff_id'], $project_members)) {
                        $this->db->where('bounceid', $id);
                        $this->db->where('staff_id', $project_member['staff_id']);
                        $this->db->delete(db_prefix() . 'chequebounces_assigned');
                        if ($this->db->affected_rows() > 0) {
                          // $this->log_activity($id, 'project_activity_removed_team_member', get_staff_full_name($project_member['staff_id']));
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('bounceid', $id);
                    $this->db->delete(db_prefix() . 'chequebounces_assigned');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    $this->db->where('bounceid', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get(db_prefix() . 'chequebounces_assigned')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert(db_prefix() . 'chequebounces_assigned', [
                            'bounceid' => $id,
                            'staff_id'   => $staff_id,
							'assigned_from'=>get_staff_user_id(),
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification([
                                    'fromuserid'      => get_staff_user_id(),
                                    'description'     => 'not_staff_added_as_project_member',
                                    'link'            => 'chequebounces/chequebounce/' . $id,
                                    'touserid'        => $staff_id,
                                    'additional_data' => serialize([
                                        $project_name,
                                    ]),
                                ]);
                                array_push($new_project_members_to_receive_email, $staff_id);
                                if ($notified) {
                                    array_push($notifiedUsers, $staff_id);
                                }
                            }


                         //   $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                            $affectedRows++;
                        }
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        } else {
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    if (empty($staff_id)) {
                        continue;
                    }
                    $this->db->insert(db_prefix() . 'chequebounces_assigned', [
                        'bounceid' => $id,
                        'staff_id'   => $staff_id,
						'assigned_from'=>get_staff_user_id(),
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification([
                                'fromuserid'      => get_staff_user_id(),
                                'description'     => 'not_staff_added_as_project_member',
                                'link'            => 'chequebounces/chequebounce/' . $id,
                                'touserid'        => $staff_id,
                                'additional_data' => serialize([
                                    $project_name,
                                ]),
                            ]);
                            array_push($new_project_members_to_receive_email, $staff_id);
                            if ($notifiedUsers) {
                                array_push($notifiedUsers, $staff_id);
                            }
                        }
                     //   $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                        $affectedRows++;
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        }

        if (count($new_project_members_to_receive_email) > 0) {
            $all_members = $this->get_chequebounce_members($id);
            foreach ($all_members as $data) {
                if (in_array($data['staff_id'], $new_project_members_to_receive_email)) {
                  //  send_mail_template('project_staff_added_as_member', $data, $id, $client_id);
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }
	 public function get_cheque_status($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('chequestatusid', $id);

            return $this->db->get(db_prefix() . 'chequebounces_status')->row();
        }
        $this->db->order_by('statusorder', 'asc');

        return $this->db->get(db_prefix() . 'chequebounces_status')->result_array();
    }
		 public function fetch_customer_numrows($data1)
    {
        $have_permission_customers_view = has_permission('chequebounces', '', 'view');
        if ( $have_permission_customers_view) {

            // Clients
            $this->db->select('*');

            $this->db->from(db_prefix() . 'chequebounces');

            $this->db->where('customer_code',$data1['customer_code']);
			$this->db->where('id!=',$data1['id']);

           return $this->db->get()->num_rows();
        }
    }
}
