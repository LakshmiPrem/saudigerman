<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contracts_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contract_types_model');
		$this->load->model('contract_risklist_model');
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
        $this->db->select('*,' . db_prefix() . 'contracts_types.name as type_name,' . db_prefix() . 'contracts.id as id, ' . db_prefix() . 'contracts.addedfrom');
        $this->db->where($where);
        $this->db->join(db_prefix() . 'contracts_types', '' . db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type', 'left');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.userid = ' . db_prefix() . 'contracts.client');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'contracts.id', $id);
            $contract = $this->db->get(db_prefix() . 'contracts')->row();
            if ($contract) {
                $contract->attachments = $this->get_contract_attachments('', $contract->id);
                if ($for_editor == false) {
                    $this->load->library('merge_fields/client_merge_fields');
                    $this->load->library('merge_fields/contract_merge_fields');
                    $this->load->library('merge_fields/other_merge_fields');

                    $merge_fields = [];
                    $merge_fields = array_merge($merge_fields, $this->contract_merge_fields->format($id));
                    $merge_fields = array_merge($merge_fields, $this->client_merge_fields->format($contract->client));
                    $merge_fields = array_merge($merge_fields, $this->other_merge_fields->format());
                    foreach ($merge_fields as $key => $val) {
                        if (stripos($contract->content, $key) !== false) {
                            $contract->content = str_ireplace($key, $val, $contract->content);
                        } else {
                            $contract->content = str_ireplace($key, '', $contract->content);
                        }
                    }
                }
            }

            return $contract;
        }
        $contracts = $this->db->get(db_prefix() . 'contracts')->result_array();
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
    public function get_contracts_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(datestart)) as year FROM ' . db_prefix() . 'contracts')->result_array();
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
        $this->db->where('rel_type', 'contract');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    /**
     * @param   array $_POST data
     * @return  integer Insert ID
     * Add new contract
     */
    public function addold($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $data['datestart'] = to_sql_date($data['datestart']);
        $data['final_expiry_date'] = to_sql_date($data['final_expiry_date']);
        $data['default_effective_date'] = to_sql_date($data['default_effective_date']);
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
         if (isset($data['is_receivable']) && ($data['is_receivable'] == 1 || $data['is_receivable'] === 'on')) {
            $data['is_receivable'] = 1;
        } else {
            $data['is_receivable'] = 0;
        }
         if (isset($data['is_payable']) && ($data['is_payable'] == 1 || $data['is_payable'] === 'on')) {
            $data['is_payable'] = 1;
        } else {
            $data['is_payable'] = 0;
        }
        
           if (isset($data['is_non_std']) && ($data['is_non_std'] == 1 || $data['is_non_std'] === 'on')) {
            $data['is_non_std'] = 1;
        } else {
            $data['is_non_std'] = 0;
        }
		 $data['is_autorenewal'] = isset($data['is_autorenewal']) ? 1 : 0;
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
		 
		
		if(!empty($data['contract_template_id'])){
        $data['content'] = $this->db->select('content')->from(db_prefix() . 'templates')->where('id', $data['contract_template_id'])->get()->row()->content;
		 }else{
			  $query                 = $this->db->select('content')->from(db_prefix() . 'templates')->where('agreement_type', $data['contract_type'])->get();
		if($query->num_rows()>0)
        $data['content'] = $query->row()->content;
		else
        $data['content'] = ' ';
		
		 }
       /* $query                 = $this->db->get();
		if($query->num_rows()>0)
        $data['content'] = $query->row()->content;
		else
        $data['content'] = ' ';*/
        $data['hash'] = app_generate_hash();
        $data['otherparty_hash']=app_generate_hash();


        $data = hooks()->apply_filters('before_contract_added', $data);

        $this->db->insert(db_prefix() . 'contracts', $data);
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
            log_activity('New Contract Added [' . $data['subject'] . ']');
			$this->log_contract_activity($insert_id, 'not_contract_activity_created');
            return $insert_id;
        }

        return false;
    }
    public function add($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        if (!empty($data['datestart'])) {
            $data['datestart'] = to_sql_date($data['datestart']);
        }

        if (!empty($data['final_expiry_date'])) {
            $data['final_expiry_date'] = to_sql_date($data['final_expiry_date']);
        }

        if (!empty($data['default_effective_date'])) {
            $data['default_effective_date'] = to_sql_date($data['default_effective_date']);
        }
        unset($data['attachment']);
        if (empty($data['dateend'])) {
            unset($data['dateend']);
        } else {
            $data['dateend'] = to_sql_date($data['dateend']);
        }


        if (isset($data['trash']) && ($data['trash'] == 1 || $data['trash'] === 'on')) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
        }
         if (isset($data['is_receivable']) && ($data['is_receivable'] == 1 || $data['is_receivable'] === 'on')) {
            $data['is_receivable'] = 1;
        } else {
            $data['is_receivable'] = 0;
        }
         if (isset($data['is_payable']) && ($data['is_payable'] == 1 || $data['is_payable'] === 'on')) {
            $data['is_payable'] = 1;
        } else {
            $data['is_payable'] = 0;
        }
        
           if (isset($data['is_non_std']) && ($data['is_non_std'] == 1 || $data['is_non_std'] === 'on')) {
            $data['is_non_std'] = 1;
        } else {
            $data['is_non_std'] = 0;
        }
		 $data['is_autorenewal'] = isset($data['is_autorenewal']) ? 1 : 0;
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
		 
		
		if(!empty($data['contract_template_id'])){
        $data['content'] = $this->db->select('content')->from(db_prefix() . 'templates')->where('id', $data['contract_template_id'])->get()->row()->content;
		 }else{
            
			  if (!empty($data['contract_type'])) {
                $query                 = $this->db->select('content')->from(db_prefix() . 'templates')->where('agreement_type', $data['contract_type'])->get();
                if($query->num_rows()>0)
                $data['content'] = $query->row()->content;
                else
                $data['content'] = ' ';
              }else{
                $data['content'] = ' ';
              }
		
		 }
       /* $query                 = $this->db->get();
		if($query->num_rows()>0)
        $data['content'] = $query->row()->content;
		else
        $data['content'] = ' ';*/
        $data['hash'] = app_generate_hash();
        $data['otherparty_hash']=app_generate_hash();


        $data = hooks()->apply_filters('before_contract_added', $data);

        $this->db->insert(db_prefix() . 'contracts', $data);
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
            $subject = $data['subject'] ?? '';
log_activity('New Contract Added [' . $subject . ']');
			$this->log_contract_activity($insert_id, 'not_contract_activity_created');
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

        if (isset($data['datestart']) && $data['datestart'] !== '') {
            $data['datestart'] = to_sql_date($data['datestart']);
        }
        
        if (isset($data['final_expiry_date']) && $data['final_expiry_date'] !== '') {
            $data['final_expiry_date'] = to_sql_date($data['final_expiry_date']);
        }
        if (isset($data['dateend'])) {
            if ($data['dateend'] === '') {
                $data['dateend'] = null;
            } else {
                $data['dateend'] = to_sql_date($data['dateend']);
            }
        } else {
                
                unset($data['dateend']);
        }

        if (isset($data['trash'])) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
        }
         if (isset($data['is_receivable'])) {
            $data['is_receivable'] = 1;
        } else {
            $data['is_receivable'] = 0;
        }
         if (isset($data['is_payable'])) {
            $data['is_payable'] = 1;
        } else {
            $data['is_payable'] = 0;
        }
        
         if (isset($data['is_non_std'])) {
            $data['is_non_std'] = 1;
        } else {
            $data['is_non_std'] = 0;
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
		$data['is_autorenewal'] = isset($data['is_autorenewal']) ? 1 : 0;
        $_pm = [];
        if (isset($project_members)) {
            $_pm['project_members'] = $project_members;
        }
        if ($this->add_edit_members($_pm, $id)) {
            $affectedRows++;
        }

        $data = hooks()->apply_filters('before_contract_updated', $data, $id);

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contracts', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_contract_updated', $id);
            log_activity('Contract Updated [' . $data['subject'] . ']');
            $this->log_contract_activity($id, 'not_contract_activity_updated');
            return true;
        }

        return $affectedRows > 0;
    }

    public function clear_signature($id)
    {
        $this->db->select('signature');
        $this->db->where('id', $id);
        $contract = $this->db->get(db_prefix() . 'contracts')->row();

        if ($contract) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'contracts', array_merge(get_acceptance_info_array(true), ['signed' => 0]));

            if (!empty($contract->signature)) {
                unlink(get_upload_path_by_type('contract') . $id . '/' . $contract->signature);
            }

            $this->log_contract_activity($id, 'signature_cleared');
            return true;
        }


        return false;
    }
	 public function clear_partysignature($id)
    {
        $this->db->select('party_signature');
        $this->db->where('id', $id);
        $contract = $this->db->get(db_prefix() . 'contracts')->row();

        if ($contract) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'contracts', array_merge(get_acceptance_party_info_array(true), ['party_signed' => 0]));

            if (!empty($contract->party_signature)) {
                unlink(get_upload_path_by_type('contract') . $id . '/' . $contract->party_signature);
            }

            $this->log_contract_activity($id, 'partysignature_cleared');
            return true;
        }


        return false;
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
        $this->db->insert(db_prefix() . 'contract_comments', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $contract = $this->get($data['contract_id']);
			$this->log_contract_activity($contract->id, 'not_contract_comment_added');
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
                        'link'            => 'contracts/contract/' . $data['contract_id'],
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
        $comment = $this->get_comment($id);
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contract_comments', [
            'content' => nl2br($data['content']),
        ]);

        if ($this->db->affected_rows() > 0) {
            $this->log_contract_activity($comment->contract_id, 'not_contract_comment_updated');
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
        $this->db->where('contract_id', $id);
        $this->db->order_by('dateadded', 'ASC');

        return $this->db->get(db_prefix() . 'contract_comments')->result_array();
    }

    /**
     * Get contract single comment
     * @param  mixed $id  comment id
     * @return object
     */
    public function get_comment($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'contract_comments')->row();
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
        $this->db->delete(db_prefix() . 'contract_comments');
        if ($this->db->affected_rows() > 0) {
            log_activity('Contract Comment Removed [Contract ID:' . $comment->contract_id . ', Comment Content: ' . $comment->content . ']');
            $this->log_contract_activity($comment->contract_id, 'not_contract_comment_removed');
            return true;
        }

        return false;
    }

    public function copy($id)
    {
        $contract       = $this->get($id, [], true);
        $fields         = $this->db->list_fields(db_prefix() . 'contracts');
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
        hooks()->do_action('before_contract_deleted', $id);
        $this->clear_signature($id);
        $contract = $this->get($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'contracts');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('contract_id', $id);
            $this->db->delete(db_prefix() . 'contract_comments');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'contracts');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'contract');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_contract_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'contract');
            $this->db->delete(db_prefix() . 'notes');


            $this->db->where('contractid', $id);
            $this->db->delete(db_prefix() . 'contract_renewals');
            
            
             $this->db->where('contract_id', $id);
            $this->db->delete(db_prefix() . 'contract_amendments');

              $this->db->where('contract_id', $id);
            $this->db->delete(db_prefix() . 'contract_actions');
            
            // Get related tasks
            $this->db->where('rel_type', 'contract');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }

            delete_tracked_emails($id, 'contract');

            log_activity('Contract Deleted [' . $id . ']');
            $this->log_contract_activity($id, 'not_contract_removed');

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
        $this->db->update('contracts', [
            'marked_as_signed' => 1,
            'status'           => 2
        ]);

        $this->log_contract_activity($id, 'contract_mark_as_signed');
        return $this->db->affected_rows() > 0;
    }
    public function mark_as_send($id)
    {
        $this->db->where('id', $id);
        $this->db->update('contracts', ['sended' => 1,'mark_senddate'=>date('Y-m-d H:i:s')]);

        $this->log_contract_activity($id, 'contract_mark_as_send');
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
        $this->db->update('contracts', ['marked_as_signed' => 0,'status'           => 1]);

        $this->log_contract_activity($id, 'contract_un_mark_as_signed');
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
		$this->load->model('casediary_model');
        $contract = $this->get($id);

        if ($attachpdf) {
            //set_mailing_constant();
           // $pdf    = contract_pdf($contract);
            //$attach = $pdf->Output(slug_it($contract->subject) . '.pdf', 'S');
			$totalversions = total_rows(db_prefix().'contract_versions','contractid='.$contract->id);
			if($totalversions>0){
					$latest_version=get_current_contract_versioninfo($contract->id);
				$attach=$latest_version->version_internal_file_path;
			}else{
				$attach=$contract->contract_filename;
				
			}
        }

        $sent_to = $this->input->post('sent_to');
        $secondary_email = $this->input->post('secondary_email');
        $sent    = false;

        if (is_array($sent_to)) {
            $i = 0;
          foreach ($sent_to as $contact_id) {
                if ($sent_to != '') {
                    $contact = $this->casediary_model->get_oppositeparty($contact_id);

                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
						
                    }
					$legals        = explode(';', $cc);
                    $template = mail_template('contract_send_to_customer', $contract, $contact->email, $legals);

                    if ($attachpdf) {
                        $template->add_attachment([
                             'attachment' => get_upload_path_by_type('contract') . $contract->id . '/' . $attach,
                              'filename'   => $attach,//slug_it($contract->subject) . '.pdf',
                              'type'       =>'application/vnd.openxmlformats-officedoc',//strtolower(pathinfo($attach, PATHINFO_EXTENSION)), //'application/pdf',
                              'read'       => true,
                        ]);
                    }

                    if ($template->send()) {
                        $sent = true;
                    }
                }
                $i++;
			  
            }
        }
        if($secondary_email!=''){

            $secondary_emails        = explode(';', $secondary_email);
            foreach($secondary_emails as $secondary_email){
                $template = mail_template('contract_send_to_customer', $contract, $secondary_email, $cc='');

                            if ($attachpdf) {
                                $template->add_attachment([
                                    'attachment' => get_upload_path_by_type('contract') . $contract->id . '/' . $attach,
                                    'filename'   => $attach,//slug_it($contract->subject) . '.pdf',
                                    'type'       =>'application/vnd.openxmlformats-officedoc',//strtolower(pathinfo($attach, PATHINFO_EXTENSION)), //'application/pdf',
                                ]);
                            }

                            if ($template->send()) {
                                $sent = true;
                            }
            }
            
            
        }
        // else {
        //     return false;
        // }
        if ($sent) {
            return true;
        }

        return false;
    }
    
    
     public function send_contract_to_otherparty($id, $attachpdf = true, $cc = '')
    {
        $cc_staffs = $this->input->post('cc'); // now multiple staff IDs
        $cc_emails = [];

        if (!empty($cc_staffs) && is_array($cc_staffs)) {
            foreach ($cc_staffs as $staff_id) {
                $staff = $this->staff_model->get($staff_id);
                if ($staff && !empty($staff->email)) {
                    $cc_emails[] = $staff->email;
                }
            }
        }

        // Convert to string if your mail_template expects string separated by ;
        $cc = implode(';', $cc_emails);


		$this->load->model('casediary_model');
        $contract = $this->get($id);

        if ($attachpdf) {
            //set_mailing_constant();
           // $pdf    = contract_pdf($contract);
            //$attach = $pdf->Output(slug_it($contract->subject) . '.pdf', 'S');
			$totalversions = total_rows(db_prefix().'contract_versions','contractid='.$contract->id);
			if($totalversions>0){
					$latest_version=get_current_contract_versioninfo($contract->id);
				$attach=$latest_version->version_internal_file_path;
			}else{
				$attach=$contract->contract_filename;
				
			}
        }

        $sent_to = $this->input->post('sent_to');
        $secondary_email = $this->input->post('secondary_email');
        $sent    = false;

        if (is_array($sent_to)) {
            $i = 0;
          foreach ($sent_to as $contact_id) {
                if ($sent_to != '') {
                    $contact = $this->casediary_model->get_oppositeparty($contact_id);

                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
						
                    }
					$legals        = explode(';', $cc);
                    $template = mail_template('contract_send_to_otherparty', $contract, $contact->email, $legals);

                    if ($attachpdf) {
                        $template->add_attachment([
                             'attachment' => get_upload_path_by_type('contract') . $contract->id . '/' . $attach,
                              'filename'   => $attach,//slug_it($contract->subject) . '.pdf',
                              'type'       =>'application/vnd.openxmlformats-officedoc',//strtolower(pathinfo($attach, PATHINFO_EXTENSION)), //'application/pdf',
                              'read'       => true,
                        ]);
                    }

                    if ($template->send()) {
                        $sent = true;
                    }
                }
                $i++;
			  
            }
        }
        if($secondary_email!=''){

            $secondary_emails        = explode(';', $secondary_email);
            foreach($secondary_emails as $secondary_email){
                $template = mail_template('contract_send_to_otherparty', $contract, $secondary_email, $cc='');

                            if ($attachpdf) {
                                $template->add_attachment([
                                    'attachment' => get_upload_path_by_type('contract') . $contract->id . '/' . $attach,
                                    'filename'   => $attach,//slug_it($contract->subject) . '.pdf',
                                    'type'       =>'application/vnd.openxmlformats-officedoc',//strtolower(pathinfo($attach, PATHINFO_EXTENSION)), //'application/pdf',
                                ]);
                            }

                            if ($template->send()) {
                                $sent = true;
                            }
            }
            
            
        }
        // else {
        //     return false;
        // }
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
                unlink(get_upload_path_by_type('contract') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Contract Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
                $this->log_contract_activity($attachment->rel_id, 'not_contract_attachment_removed');
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
    public function renew($data)
    {
        $keepSignature = isset($data['renew_keep_signature']);
        if ($keepSignature) {
            unset($data['renew_keep_signature']);
        }
        $data['new_start_date']      = to_sql_date($data['new_start_date']);
        $data['new_end_date']        = to_sql_date($data['new_end_date']);
        $data['date_renewed']        = date('Y-m-d H:i:s');
        $data['renewed_by']          = get_staff_full_name(get_staff_user_id());
        $data['renewed_by_staff_id'] = get_staff_user_id();
        if (!is_date($data['new_end_date'])) {
            unset($data['new_end_date']);
        }
        // get the original contract so we can check if is expiry notified on delete the expiry to revert
        $_contract                         = $this->get($data['contractid']);
        $data['is_on_old_expiry_notified'] = $_contract->isexpirynotified;
        $this->db->insert(db_prefix() . 'contract_renewals', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->where('id', $data['contractid']);
            $_data = [
                'datestart'        => $data['new_start_date'],
                'contract_value'   => $data['new_value'],
                'isexpirynotified' => 0,
            ];

            if (isset($data['new_end_date'])) {
                $_data['dateend'] = $data['new_end_date'];
            }

            if (!$keepSignature) {
                $_data           = array_merge($_data, get_acceptance_info_array(true));
                $_data['signed'] = 0;
                if (!empty($_contract->signature)) {
                    unlink(get_upload_path_by_type('contract') . $data['contractid'] . '/' . $_contract->signature);
                }
            }

            $this->db->update(db_prefix() . 'contracts', $_data);
            if ($this->db->affected_rows() > 0) {
				handle_contractrenew_file_upload($insert_id,$data['contractid']);
                log_activity('Contract Renewed [ID: ' . $data['contractid'] . ']');
                $this->log_contract_activity($data['contractid'], 'not_contract_renewed');

                return true;
            }
            // delete the previous entry
            $this->db->where('id', $insert_id);
            $this->db->delete(db_prefix() . 'contract_renewals');

            return false;
        }

        return false;
    }

    /**
     * Delete contract renewal
     * @param  mixed $id         renewal id
     * @param  mixed $contractid contract id
     * @return boolean
     */
    public function delete_renewal($id, $contractid)
    {
        // check if this renewal is last so we can revert back the old values, if is not last we wont do anything
        $this->db->select('id')->from(db_prefix() . 'contract_renewals')->where('contractid', $contractid)->order_by('id', 'desc')->limit(1);
        $query                 = $this->db->get();
        $last_contract_renewal = $query->row()->id;
        $is_last               = false;
        if ($last_contract_renewal == $id) {
            $is_last = true;
            $this->db->where('id', $id);
            $original_renewal = $this->db->get(db_prefix() . 'contract_renewals')->row();
        }

        $contract = $this->get($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'contract_renewals');
        if ($this->db->affected_rows() > 0) {
            if (!is_null($contract->short_link)) {
                app_archive_short_link($contract->short_link);
            }

            if ($is_last == true) {
                $this->db->where('id', $contractid);
                $data = [
                    'datestart'        => $original_renewal->old_start_date,
                    'contract_value'   => $original_renewal->old_value,
                    'isexpirynotified' => $original_renewal->is_on_old_expiry_notified,
                ];
                if ($original_renewal->old_end_date != '0000-00-00') {
                    $data['dateend'] = $original_renewal->old_end_date;
                }
                $this->db->update(db_prefix() . 'contracts', $data);
            }
            log_activity('Contract Renewed [RenewalID: ' . $id . ', ContractID: ' . $contractid . ']');
            $this->log_contract_activity($contractid, 'not_contract_renewed_rollback');

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

        if ($staffId && ! staff_can('view', 'contracts', $staffId)) {
            $this->db->where('addedfrom', $staffId);
        }

        $this->db->select('id,subject,client,datestart,dateend');

        $this->db->where('dateend IS NOT NULL');
        $this->db->where('trash', 0);
        $this->db->where('dateend >=', $diff1);
        $this->db->where('dateend <=', $diff2);

        return $this->db->get(db_prefix() . 'contracts')->result_array();
    }

    /**
     * Get contract renewals
     * @param  mixed $id contract id
     * @return array
     */
    public function get_contract_renewal_history($id)
    {
        $this->db->where('contractid', $id);
        $this->db->order_by('date_renewed', 'asc');

        return $this->db->get(db_prefix() . 'contract_renewals')->result_array();
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get_contract_types($id = '')
    {
        return $this->contract_types_model->get($id);
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete_contract_type($id)
    {
        return $this->contract_types_model->delete($id);
    }

    /**
     * Add new contract type
     * @param mixed $data All $_POST data
     */
    public function add_contract_type($data)
    {
        return $this->contract_types_model->add($data);
    }

    /**
     * Edit contract type
     * @param mixed $data All $_POST data
     * @param mixed $id Contract type id
     */
    public function update_contract_type($data, $id)
    {
        return $this->contract_types_model->update($data, $id);
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
	
	 public function fetch_contract_details($q, $limit, $start,$contracttype,$status)
    {
        $result = [
            'result'         => [],
            'type'           => 'contracts',
            'search_heading' => _l('contracts'),
        ];

        $projects = has_permission('contracts', '', 'view');
        // Projects
        $this->db->select('*,tblcontracts.subject as proejct_name,tblcontracts.id as id,tbloppositeparty.name as oppositeparty');
        $this->db->from(db_prefix() . 'contracts');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'contracts.client');
		  $this->db->join(db_prefix() . 'oppositeparty', db_prefix() . 'oppositeparty.id = ' . db_prefix() . 'contracts.other_party');
     //   $this->db->join(db_prefix() . 'contracts_types', db_prefix() . 'contracts_types.id = ' . db_prefix() . 'contracts.contract_type','left');
         /*if (!$projects) {
            $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
        }
        if ($where != false) {
            $this->db->where($where);
        }*/

        if ($contracttype != '') {
            $this->db->where('contract_type',$contracttype);
        }
        if ($status != '') {
            $this->db->where('status',$status);
        }

        if (!startsWith($q, '#')) {
            $this->db->where('(subject LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR tblclients.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR tblclients.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR type_stamp LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE tblprojects.name="' . $this->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'project\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        $this->db->limit($limit,$start);
        $this->db->order_by('tblcontracts.id', 'desc');
        $projects = $this->db->get()->result_array();
        $res = '';
        foreach ($projects as $project_) {
            $primary_contact_id = get_primary_contact_user_id($project_['client']);
           // $status = get_project_status_by_id($project_['status']);  
            $up = explode('^',$project_['description']);
            $cont = '';
            $breaks = array("<br />","<br>","<br/>");
            if(isset($up[1]))  
                $cont = str_ireplace($breaks, "\r\n", $up[1]);   
            $res .=  ' 
                        <div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.56), 0 2px 10px 0 rgba(0, 0, 0, 0.52); padding: 10px; margin: 1px; height: 240px; max-height: 240px;"   >
                                <div class="card-body">
                                    <a href="'.admin_url('contracts/contract/'.$project_['id']).'" ><h5 class="card-title" ><strong>'.$project_['proejct_name'].'</strong></h5></a>
                                    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('client').':</b>'.$project_['company'].'</p>
									  <p class="card-text" style="margin:  0 0 4px;"><b>'._l('opposite_party').':</b>'.$project_['oppositeparty'].'</p>
									   <p class="card-text" style="margin: 0 0 4px;"><b>'._l('contract_type').' :</b>'. get_contracttype_name_by_id($project_['contract_type']).'</p>
									    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('contract_value').':</b>'.number_format($project_['contract_value']).'</p>
                                    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('project_start_date').':</b>'._d($project_['datestart']).'</span> |   <button type="button" class="btn btn-default btn-sm btn-icon mleft10  pop" data-container="body" data-toggle="popover" data-placement="bottom" data-content="'.$cont.'"
    data-original-title="'.$up[0].'" title="'.$up[0].'"> <i class="fa fa-tag"></i></button> </p>
                                 
                                </div>
                            </div> 
                        </div> 

                        ';

               
        }

        return $res;
    }

     public function fetch_contract_details_num_rows($q, $casetype,$status)
    {
        $result = [
            'result'         => [],
            'type'           => 'contracts',
            'search_heading' => _l('contracts'),
        ];

        $projects = has_permission('contracts', '', 'view');
        // Projects
        $this->db->select();
        $this->db->from(db_prefix() . 'contracts');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'contracts.client');
        /*   if (!$projects) {
            $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
        }
     if ($where != false) {
            $this->db->where($where);
        }*/

        if ($casetype != '') {
            $this->db->where('contract_type',$casetype);
        }
        if ($status != '') {
            $this->db->where('status',$status);
        }

        if (!startsWith($q, '#')) {
            $this->db->where('(subject LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR description LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR company LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR vat LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR phonenumber LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR tblclients.city LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR state LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR zip LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR tblclients.address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR type_stamp LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM ' . db_prefix() . 'taggables WHERE tag_id IN
                (SELECT id FROM ' . db_prefix() . 'tags WHERE name="' . $this->db->escape_str(strafter($q, '#')) . '")
                AND ' . db_prefix() . 'taggables.rel_type=\'project\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        //$this->db->limit($limit,$start);
        $this->db->order_by('id', 'desc');
        return $this->db->get()->num_rows();
        
    }
	 public function get_contract_members($id)
    {
        $this->db->select('email,contractid,staff_id');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'contracts_assigned.staff_id');
        $this->db->where('contractid', $id);

        return $this->db->get(db_prefix() . 'contracts_assigned')->result_array();
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
        $project      = $this->db->get(db_prefix() . 'contracts')->row();
        $project_name = $project->subject;
        $client_id    = $project->client;

        $project_members_in = $this->get_contract_members($id);
        if (sizeof($project_members_in) > 0) {
            foreach ($project_members_in as $project_member) {
                if (isset($project_members)) {
                    if (!in_array($project_member['staff_id'], $project_members)) {
                        $this->db->where('contractid', $id);
                        $this->db->where('staff_id', $project_member['staff_id']);
                        $this->db->delete(db_prefix() . 'contracts_assigned');
                        if ($this->db->affected_rows() > 0) {
                          // $this->log_activity($id, 'project_activity_removed_team_member', get_staff_full_name($project_member['staff_id']));
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('contractid', $id);
                    $this->db->delete(db_prefix() . 'contracts_assigned');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    $this->db->where('contractid', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get(db_prefix() . 'contracts_assigned')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert(db_prefix() . 'contracts_assigned', [
                            'contractid' => $id,
                            'staff_id'   => $staff_id,
							'assigned_from'=>get_staff_user_id(),
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification([
                                    'fromuserid'      => get_staff_user_id(),
                                    'description'     => 'not_staff_added_as_project_member',
                                    'link'            => 'contracts/contract/' . $id,
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
                    $this->db->insert(db_prefix() . 'contracts_assigned', [
                        'contractid' => $id,
                        'staff_id'   => $staff_id,
						'assigned_from'=>get_staff_user_id(),
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification([
                                'fromuserid'      => get_staff_user_id(),
                                'description'     => 'not_staff_added_as_project_member',
                                'link'            => 'contracts/contract/' . $id,
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
            $all_members = $this->get_contract_members($id);
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
	 public function delete_contract_document($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'contracts')->row();
        $deleted    = false;
        if ($attachment) {
            
                $relPath  = get_upload_path_by_type('contract') . $id . '/';
                $fullPath = $relPath . $attachment->contract_filename;
                unlink($fullPath);
            $this->db->where('id', $id);
			$this->db->update(db_prefix() . 'contracts', [
            'contract_filename' => null,
			'file_type' => null,
			'content' =>'',
        	]);
           
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }
    	}
	}

    public function delete_signed_contract_document($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'contracts')->row();
        $deleted    = false;
        if ($attachment) {
            
                $relPath  = get_upload_path_by_type('contract') . $id . '/';
                $fullPath = $relPath . $attachment->signed_contract_filename;
                unlink($fullPath);
            $this->db->where('id', $id);
			$this->db->update(db_prefix() . 'contracts', [
            'signed_contract_filename' => null,
			'file_type' => null,
			'content' =>'',
        	]);
           
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }
    	}
	}
   public function make_final_doc($contract_id,$version)
    {
        
        $this->db->where('id', $contract_id);
        $this->db->update(db_prefix() . 'contracts', [
            'final_doc' => $version,
        ]);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_contracts_of_oppositeparty($id = '')

    {

        $this->db->select('tbloppositeparty.id as id,tbloppositeparty.name as name,tbloppositeparty.email as email,tbloppositeparty.mobile as mobile');

        $this->db->join('tblcontracts', 'tbloppositeparty.id=tblcontracts.other_party');

        if(is_numeric($id)){

            $this->db->where('tblcontracts.id',$id);   

        }



        $leads = $this->db->get('tbloppositeparty')->result_array();



        return $leads;



    }

    public function get_contract_activity_log($id)
    {
        $sorting = hooks()->apply_filters('contract_activity_log_default_sort', 'ASC');

        $this->db->where('contractid', $id);
        $this->db->order_by('date', $sorting);

        return $this->db->get(db_prefix() . 'contract_activity_log')->result_array();
    }
	public function log_contract_activity($id, $description, $integration = false, $additional_data = '')
    {
        $log = [
            'date'            => date('Y-m-d H:i:s'),
            'description'     => $description,
            'contractid'          => $id,
            'staffid'         => get_staff_user_id(),
            'additional_data' => $additional_data,
            'full_name'       => get_staff_full_name(get_staff_user_id()),
        ];
        if ($integration == true) {
            $log['staffid']   = 0;
            $log['full_name'] = '[CRON]';
        }

        $this->db->insert(db_prefix() . 'contract_activity_log', $log);

        return $this->db->insert_id();
    }
 public function get_templates_of_contract($id = '')
  {
       $this->db->select('tbltemplates.id as id,tbltemplates.name');

       // $this->db->join('tblclient_oppositeparty_rel', 'tbloppositeparty.id=tblclient_oppositeparty_rel.opposite_party_id', 'left');
         $this->db->where('tbltemplates.type','contracts');
        if(is_numeric($id)){

            $this->db->where('tbltemplates.agreement_type',$id);   

        }

        $leads = $this->db->get('tbltemplates')->result_array();

        return $leads;

    }

    public function add_contract_status($data)
    {
        $this->db->insert(db_prefix().'contracts_status', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Contract Status Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }
    public function update_contract_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'contracts_status', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Contract Status Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_contract_terms($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'contract_terms');
        if ($this->db->affected_rows() > 0) {
            log_activity('Contract Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_contract_status($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'contracts_status');
        if ($this->db->affected_rows() > 0) {
            log_activity('Contract Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
    public function get_activity($id = '', $limit = '', $only_contract_members_activity = false)
    {
        if (!is_client_logged_in()) {
            $has_permission = has_permission('projects', '', 'view');
            if (!$has_permission) {
                $this->db->where('contractid IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')');
            }
        }
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        if (is_numeric($id)) {
            $this->db->where('contract_id', $id);
        }
        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }
        $this->db->order_by('date', 'desc');
        $activities = $this->db->get(db_prefix() . 'contract_activity_log')->result_array();
	
        $i          = 0;
        foreach ($activities as $activity) {
				
            $seconds          = get_string_between($activity['additional_data'], '<seconds>', '</seconds>');
            $other_lang_keys  = get_string_between($activity['additional_data'], '<lang>', '</lang>');
            $_additional_data = $activity['additional_data'];

            if ($seconds != '') {
                $_additional_data = str_replace('<seconds>' . $seconds . '</seconds>', seconds_to_time_format($seconds), $_additional_data);
            }

            if ($other_lang_keys != '') {
                $_additional_data = str_replace('<lang>' . $other_lang_keys . '</lang>', _l($other_lang_keys), $_additional_data);
            }

            if (strpos($_additional_data, 'project_status_') !== false) {
                $_additional_data = get_project_status_by_id(strafter($_additional_data, 'project_status_'));

                if (isset($_additional_data['name'])) {
                    $_additional_data = $_additional_data['name'];
                }
            }

            $activities[$i]['description']     = _l($activity['description']);
            $activities[$i]['additional_data'] = $_additional_data;
            $activities[$i]['contract_name']    = get_contract_name_by_id($activity['contractid']);
          //  unset($activities[$i]['description']);
            $i++;
        }

        return $activities;
    }
	public function get_contract_status($id = '',$where=[])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'contracts_status')->row();
        }
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $this->db->order_by('statusorder', 'asc');

        return $this->db->get(db_prefix() . 'contracts_status')->result_array();
    }
        public function change_version_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contract_versions', [
            'active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('contract_version_status_changed', [
                'id'     => $id,
                'status' => $status,
            ]);

            log_activity('Contract Version Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
            $version=$this->get_contractversioninfo($id);
          $this->log_contract_activity($version->contractid, 'not_contractversion_status_changed');
            return true;
        }

        return false;
    }
    public function get_contractversioninfo($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'contract_versions')->row();
    }

    public function add_risk_checklist($data)
    {
        $this->db->insert(db_prefix().'risk_checklists ', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Checklist Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function update_risk_checklist($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'risk_checklists', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Checklist Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

    public function delete_checklist($id)
    {
        // if (is_reference_in_table('riskid', db_prefix().'contracts', $id)) {
        //     return [
        //         'referenced' => true,
        //     ];
        // }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'risk_checklists');
        if ($this->db->affected_rows() > 0) {
            log_activity('Checklist Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
/* get contract risk value list*/
	public function get_contract_risklistbyperson($id)
	{
		$this->db->select('*,'.db_prefix() . 'contract_checklists.id as id,tblrisk_checklists.name as riskname,tblrisk_checklists.key_provision as riskprovision');
        $this->db->join(db_prefix() . 'risk_checklists', db_prefix() . 'risk_checklists.id=' . db_prefix() . 'contract_checklists.riskid');
        $this->db->where('contract_id', $id);
        $this->db->order_by(db_prefix() . 'contract_checklists.id', 'asc');

        return $this->db->get(db_prefix() . 'contract_checklists')->result_array();
	}
	
	 /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get_contract_risklist($id = '')
    {
        return $this->contract_risklist_model->get($id);
    }
	
	public function save_risklist($data, $contract_id)
    {
		 $assignded_ids =[];
		$upadated=false;
	  if(isset($data['risklist'])  && $data['risklist']!=null){
		  $assignded_ids= $data['risklist'];
		//  $data['bus_stakeholder'] = json_encode($data['bus_stakeholder']);
	  }
		  
    $all_old_checklist =$this->db->get_where('tblcontract_checklists',array('contract_id'=>$contract_id))->result_array();
		if(count($all_old_checklist)>0){
      //Delete old assigned staff ids
      $this->db->where('contract_id',$contract_id);
    $this->db->delete('tblcontract_checklists');
		}
	 foreach ($assignded_ids as $key => $value) {
        $this->db->insert('tblcontract_checklists', array(
                'riskid' => $value,
                'addedfrom'=>get_staff_user_id(),
                'datecreated'=>date('Y-m-d H:i:s'),
                'contract_id'=>$contract_id
            ));
		  $insert_id = $this->db->insert_id();
        if ($insert_id) {
           $upadated=true;
        }
    }  
      $insert_id = $this->db->insert_id();
        if ($upadated) {
            return true;
        }

        return false;
    }
	 public function change_riskapproval_remarks($id, $remarks)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contract_checklists', [
            'remarks' => $remarks,
        ]);
        //$alert   = 'warning';
        //$message = '';
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('added_successfully');
        }
        return [
            'alert'   => $alert,
            'message' => $message,
        ];
    }
    
	public function change_riskapproval_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contract_checklists', [
            'approval_status' => $status,
			'dateapproved'=>date('Y-m-d H:i:s'),
        ]);
		  if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('added_successfully');
		  }
		
        return [
            'alert'   => $alert,
            'message' => $message,
        ];
    }
    public function get_contracts_about_to_expire_opposite_party($party,$staffId = null, $days = 7)
    {
        $diff1 = date('Y-m-d', strtotime('-' . $days . ' days'));
        $diff2 = date('Y-m-d', strtotime('+' . $days . ' days'));

        if ($staffId && ! staff_can('view', 'contracts', $staffId)) {
            $this->db->where('addedfrom', $staffId);
        }

        $this->db->select('id,subject,client,datestart,dateend');

        $this->db->where('dateend IS NOT NULL');
        $this->db->where('trash', 0);
        $this->db->where('dateend >=', $diff1);
        $this->db->where('dateend <=', $diff2);
        $this->db->where('other_party', $party);

        return $this->db->get(db_prefix() . 'contracts')->result_array();
    }
    // === Amendments ===
    public function get_amendments($contract_id) {
        return $this->db->get_where('tblcontract_amendments', ['contract_id' => $contract_id])->result_array();
    }

    public function get_amendment($id) {
        return $this->db->get_where('tblcontract_amendments', ['id' => $id])->row();
    }

    public function add_amendment($data) {
		 if ($data['effective_date'] == '') {
            unset($data['effective_date']);
        } else {
            $data['effective_date'] = to_sql_date($data['effective_date']);
        }
        $this->db->insert('tblcontract_amendments', $data);
        return $this->db->insert_id();
    }

    public function update_amendment($id, $data) {
        $this->db->where('id', $id)->update('tblcontract_amendments', $data);
        return $this->db->affected_rows();
    }

    public function get_next_amendment_number($contract_id) {
        $this->db->select_max('amendment_number');
        $this->db->where('contract_id', $contract_id);
        $result = $this->db->get('tblcontract_amendments')->row();
        return ($result && $result->amendment_number) ? $result->amendment_number + 1 : 1;
    }
    // === Post actions ===
   public function get_postactions($contract_id) {
        return $this->db->get_where('tblcontract_actions', ['contract_id' => $contract_id])->result_array();
    }
    public function add_postaction($data) {
		 if ($data['due_date'] == '') {
            unset($data['due_date']);
        } else {
            $data['due_date'] = to_sql_date($data['due_date']);
        }
        $this->db->insert('tblcontract_actions', $data);
        return $this->db->insert_id();
    }
function count_status_contracts($type='is_payable',$staffId = null)
{
    $where_own = [];
    // $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    // if (!has_permission('contracts', '', 'view')) {
    //     $where_own = ['addedfrom' => $staffId];
		
    // }

    return total_rows(db_prefix() . 'contracts', array_merge([$type => 1], $where_own));
}

 public function get_contracts_types()
    {
        return $this->db->query('SELECT DISTINCT(contract_type) as type FROM ' . db_prefix() . 'contracts')->result_array();
    }
    
    
    public function get_contracts_by_hash($id)

    {

        $this->db->where('otherparty_hash', $id);

        $result = $this->db->get('tblcontracts')->row();

        $data=$result;

        return $data;

    }
      public function get_file_otp_by_hash($id)

    {

        $this->db->where('otherparty_hash', $id);

        $result = $this->db->get('tblcontracts')->row();

        $data=$result->party_otp;

        return $data;

    }

     public function update_contract_otp($id, $data) {
        $this->db->where('id', $id)->update('tblcontracts', $data);
        return $this->db->affected_rows();
    }
    
        public function delete_amendment($id)
    {
        // if (is_reference_in_table('riskid', db_prefix().'contracts', $id)) {
        //     return [
        //         'referenced' => true,
        //     ];
        // }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'contract_amendments');
        if ($this->db->affected_rows() > 0) {
            log_activity('Amendments Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
      public function delete_postaction($id)
    {
        // if (is_reference_in_table('riskid', db_prefix().'contracts', $id)) {
        //     return [
        //         'referenced' => true,
        //     ];
        // }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'contract_actions');
        if ($this->db->affected_rows() > 0) {
            log_activity('Contract Post Actions Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
}