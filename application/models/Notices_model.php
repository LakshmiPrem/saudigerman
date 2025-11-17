<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notices_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('notice_types_model');
    }

    /**
     * Get notice/s
     * @param  mixed  $id         notice id
     * @param  array   $where      perform where
     * @param  boolean $for_editor if for editor is false will replace the field if not will not replace
     * @return mixed
     */
    public function get($id = '', $where = [], $for_editor = false)
    {
        $this->db->select('*,' . db_prefix() . 'notices_types.name as type_name,' . db_prefix() . 'notices.id as id, ' . db_prefix() . 'notices.addedfrom');
        $this->db->where($where);
        $this->db->join(db_prefix() . 'notices_types', '' . db_prefix() . 'notices_types.id = ' . db_prefix() . 'notices.notice_type', 'left');
		  $this->db->join(db_prefix() . 'oppositeparty', '' . db_prefix() . 'oppositeparty.id = ' . db_prefix() . 'notices.other_party', 'left');
        $this->db->join(db_prefix() . 'clients', '' . db_prefix() . 'clients.userid = ' . db_prefix() . 'notices.client');
        if (is_numeric($id)) {
            $this->db->where(db_prefix() . 'notices.id', $id);
            $notice = $this->db->get(db_prefix() . 'notices')->row();
            if ($notice) {
                $notice->attachments = $this->get_notice_attachments('', $notice->id);
                if ($for_editor == false) {
                    $this->load->library('merge_fields/client_merge_fields');
                    $this->load->library('merge_fields/notice_merge_fields');
                    $this->load->library('merge_fields/borrower_merge_fields');
                    $this->load->library('merge_fields/other_merge_fields');

                    $merge_fields = [];
                    $merge_fields = array_merge($merge_fields, $this->notice_merge_fields->format($id));
                    $merge_fields = array_merge($merge_fields, $this->client_merge_fields->format($notice->client));
                    $merge_fields = array_merge($merge_fields, $this->borrower_merge_fields->format($notice->other_party));
                    $merge_fields = array_merge($merge_fields, $this->other_merge_fields->format());
                    foreach ($merge_fields as $key => $val) {
                        if (stripos($notice->content, $key) !== false) {
                            $notice->content = str_ireplace($key, $val, $notice->content);
                        } else {
                            $notice->content = str_ireplace($key, '', $notice->content);
                        }
                    }
                }
            }

            return $notice;
        }
        $notices = $this->db->get(db_prefix() . 'notices')->result_array();
        $i         = 0;
        foreach ($notices as $notice) {
            $notices[$i]['attachments'] = $this->get_notice_attachments('', $notice['id']);
            $i++;
        }

        return $notices;
    }

    /**
     * Select unique notices years
     * @return array
     */
    public function get_notices_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(datestart)) as year FROM ' . db_prefix() . 'notices')->result_array();
    }

    /**
     * @param  integer ID
     * @return object
     * Retrieve notice attachments from database
     */
    public function get_notice_attachments($attachment_id = '', $id = '')
    {
        if (is_numeric($attachment_id)) {
            $this->db->where('id', $attachment_id);

            return $this->db->get(db_prefix() . 'files')->row();
        }
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'notice');

        return $this->db->get(db_prefix() . 'files')->result_array();
    }

    /**
     * @param   array $_POST data
     * @return  integer Insert ID
     * Add new notice
     */
    public function add($data)
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
		if (isset($data['is_nonstandard']) && ($data['is_nonstandard'] == 1 || $data['is_nonstandard'] === 'on')) {
            $data['is_nonstandard'] = 1;
        } else {
            $data['is_nonstandard'] = 0;
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
		 if(!empty($data['notice_template_id']))
        $data['content'] = $this->db->select('content')->from(db_prefix() . 'templates')->where('id', $data['notice_template_id'])->get()->row()->content;
		else
		$data['content']=$this->db->select('content')->from(db_prefix() . 'templates')->where('agreement_type', $data['notice_type'])->order_by('id', 'desc')->limit(1)->get()->row()->content;
       /* $query                 = $this->db->get();
		if($query->num_rows()>0)
        $data['content'] = $query->row()->content;
		else
        $data['content'] = ' ';*/
        $data['hash'] = app_generate_hash();

        $data = hooks()->apply_filters('before_notice_added', $data);

        $this->db->insert(db_prefix() . 'notices', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
        //     $shipwaypushdata=[];
        //     $shipwaypushdata['order_id']=$insert_id;
        //     $shipwaypushdata['product']=$data['subject'];
        //     $shipwaypushdata['price']=$data['notice_value'];
        //     $shipwaypushdata['product_code']=get_noticetype_name_by_id($data['notice_type']);
        //     $shipwaypushdata['order_date']=$data['dateadded'];
        //     $shipwaypushdata['borrower_id']=$data['other_party'];
        //    // print_r($shipwaypushdata);
        //     $this->load->library('shipwayapi');
        // $shipway= new shipwayapi();
        //  //print_r($shipwaypushdata);
        // // $trackingno=$shipway->pushOrderData($shipwaypushdata);
        //   $this->db->where('id', $insert_id);
        // $this->db->update(db_prefix() . 'notices', [
        //     'tracking_number' => $trackingno,
        // ]);
			if (isset($project_members)) {
                $_pm['project_members'] = $project_members;
                $this->add_edit_members($_pm, $insert_id);
            }
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            hooks()->do_action('after_notice_added', $insert_id);
            log_activity('New notice Added [' . $data['subject'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * @param  array $_POST data
     * @param  integer notice ID
     * @return boolean
     */
    public function update($data, $id)
    {
        $affectedRows = 0;

        $data['datestart'] = to_sql_date($data['datestart']);
        $data['final_expiry_date'] = to_sql_date($data['final_expiry_date']);
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
		if (isset($data['is_nonstandard'])) {
            $data['is_nonstandard'] = 1;
        } else {
            $data['is_nonstandard'] = 0;
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

        $data = hooks()->apply_filters('before_notice_updated', $data, $id);

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'notices', $data);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_notice_updated', $id);
            log_activity('notice Updated [' . $data['subject'] . ']');
            $this->log_notice_activity($id, 'not_notice_activity_updated');
            return true;
        }

        return $affectedRows > 0;
    }

    public function clear_signature($id)
    {
        $this->db->select('signature');
        $this->db->where('id', $id);
        $notice = $this->db->get(db_prefix() . 'notices')->row();

        if ($notice) {
            $this->db->where('id', $id);
            $this->db->update(db_prefix() . 'notices', array_merge(get_acceptance_info_array(true), ['signed' => 0]));

            if (!empty($notice->signature)) {
                unlink(get_upload_path_by_type('notice') . $id . '/' . $notice->signature);
            }

            $this->log_notice_activity($id, 'signature_cleared');
            return true;
        }


        return false;
    }

    /**
    * Add notice comment
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
        $this->db->insert(db_prefix() . 'notice_comments', $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            $notice = $this->get($data['notice_id']);
			$this->log_notice_activity($notice->id, 'not_notice_comment_added');
            if (($notice->not_visible_to_client == '1' || $notice->trash == '1') && $client == false) {
                return true;
            }

            if ($client == true) {

                // Get creator
                $this->db->select('staffid, email, phonenumber');
                $this->db->where('staffid', $notice->addedfrom);
                $staff_notice = $this->db->get(db_prefix() . 'staff')->result_array();

                $notifiedUsers = [];

                foreach ($staff_notice as $member) {
                    $notified = add_notification([
                        'description'     => 'not_notice_comment_from_client',
                        'touserid'        => $member['staffid'],
                        'fromcompany'     => 1,
                        'fromuserid'      => 0,
                        'link'            => 'notices/notice/' . $data['notice_id'],
                        'additional_data' => serialize([
                            $notice->subject,
                        ]),
                    ]);

                    if ($notified) {
                        array_push($notifiedUsers, $member['staffid']);
                    }

                    $template     = mail_template('notice_comment_to_staff', $notice, $member);
                    $merge_fields = $template->get_merge_fields();
                    $template->send();

                    // Send email/sms to admin that client commented
                    $this->app_sms->trigger(SMS_TRIGGER_notice_NEW_COMMENT_TO_STAFF, $member['phonenumber'], $merge_fields);
                }
                pusher_trigger_notification($notifiedUsers);
            } else {
                $contacts = $this->clients_model->get_contacts($notice->client, ['active' => 1, 'notice_emails' => 1]);

                foreach ($contacts as $contact) {
                    $template     = mail_template('notice_comment_to_customer', $notice, $contact);
                    $merge_fields = $template->get_merge_fields();
                    $template->send();

                    $this->app_sms->trigger(SMS_TRIGGER_notice_NEW_COMMENT_TO_CUSTOMER, $contact['phonenumber'], $merge_fields);
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
        $this->db->update(db_prefix() . 'notice_comments', [
            'content' => nl2br($data['content']),
        ]);

        if ($this->db->affected_rows() > 0) {
            $this->log_notice_activity($comment->notice_id, 'not_notice_comment_updated');
            return true;
        }

        return false;
    }

    /**
     * Get notice comments
     * @param  mixed $id notice id
     * @return array
     */
    public function get_comments($id)
    {
        $this->db->where('notice_id', $id);
        $this->db->order_by('dateadded', 'ASC');

        return $this->db->get(db_prefix() . 'notice_comments')->result_array();
    }

    /**
     * Get notice single comment
     * @param  mixed $id  comment id
     * @return object
     */
    public function get_comment($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'notice_comments')->row();
    }

    /**
     * Remove notice comment
     * @param  mixed $id comment id
     * @return boolean
     */
    public function remove_comment($id)
    {
        $comment = $this->get_comment($id);

        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'notice_comments');
        if ($this->db->affected_rows() > 0) {
            log_activity('notice Comment Removed [notice ID:' . $comment->notice_id . ', Comment Content: ' . $comment->content . ']');
            $this->log_notice_activity($comment->notice_id, 'not_notice_comment_removed');
            return true;
        }

        return false;
    }

    public function copy($id)
    {
        $notice       = $this->get($id, [], true);
        $fields         = $this->db->list_fields(db_prefix() . 'notices');
        $newContactData = [];

        foreach ($fields as $field) {
            if (isset($notice->$field)) {
                $newContactData[$field] = $notice->$field;
            }
        }

        unset($newContactData['id']);

        $newContactData['trash']            = 0;
        $newContactData['isexpirynotified'] = 0;
        $newContactData['isexpirynotified'] = 0;
        $newContactData['signed']           = 0;
        $newContactData['signature']        = null;

        $newContactData = array_merge($newContactData, get_acceptance_info_array(true));

        if ($notice->dateend) {
            $dStart                    = new DateTime($notice->datestart);
            $dEnd                      = new DateTime($notice->dateend);
            $dDiff                     = $dStart->diff($dEnd);
            $newContactData['dateend'] = _d(date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY')))));
        } else {
            $newContactData['dateend'] = '';
        }

        $newId = $this->add($newContactData);

        if ($newId) {
            $custom_fields = get_custom_fields('notices');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($id, $field['id'], 'notices', false);
                if ($value != '') {
                    $this->db->insert(db_prefix() . 'customfieldsvalues', [
                    'relid'   => $newId,
                    'fieldid' => $field['id'],
                    'fieldto' => 'notices',
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
     * Delete notice, also attachment will be removed if any found
     */
    public function delete($id)
    {
        hooks()->do_action('before_notice_deleted', $id);
        $this->clear_signature($id);
        $notice = $this->get($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'notices');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('notice_id', $id);
            $this->db->delete(db_prefix() . 'notice_comments');

            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'notices');
            $this->db->delete(db_prefix() . 'customfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'notice');
            $attachments = $this->db->get(db_prefix() . 'files')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_notice_attachment($attachment['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'notice');
            $this->db->delete(db_prefix() . 'notes');


            $this->db->where('noticeid', $id);
            $this->db->delete(db_prefix() . 'notice_renewals');
            // Get related tasks
            $this->db->where('rel_type', 'notice');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }

            delete_tracked_emails($id, 'notice');

            log_activity('notice Deleted [' . $id . ']');
            $this->log_notice_activity($id, 'not_notice_removed');

            return true;
        }

        return false;
    }

    /**
     * Mark the notice as signed manually
     *
     * @param  int $id notice id
     *
     * @return boolean
     */
    public function mark_as_signed($id)
    {
        $this->db->where('id', $id);
        $this->db->update('notices', ['marked_as_signed' => 1]);

        $this->log_notice_activity($id, 'notice_mark_as_signed');
        return $this->db->affected_rows() > 0;
    }
    public function mark_as_send($id)
    {
        $this->db->where('id', $id);
        $this->db->update('notices', ['sended' => 1,'mark_senddate'=>date('Y-m-d H:i:s')]);

        $this->log_notice_activity($id, 'notice_mark_as_send');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Unmark the notice as signed manually
     *
     * @param  int $id notice id
     *
     * @return boolean
     */
    public function unmark_as_signed($id)
    {
        $this->db->where('id', $id);
        $this->db->update('notices', ['marked_as_signed' => 0]);

        $this->log_notice_activity($id, 'notice_un_mark_as_signed');
        return $this->db->affected_rows() > 0;
    }

    /**
     * Function that send notice to customer
     * @param  mixed  $id        notice id
     * @param  boolean $attachpdf to attach pdf or not
     * @param  string  $cc        Email CC
     * @return boolean
     */
    public function send_notice_to_client($id, $attachpdf = true, $cc = '')
    {
		$this->load->model('casediary_model');
        $notice = $this->get($id);

        if ($attachpdf) {
            //set_mailing_constant();
           // $pdf    = notice_pdf($notice);
            //$attach = $pdf->Output(slug_it($notice->subject) . '.pdf', 'S');
			$totalversions = total_rows(db_prefix().'notice_versions','noticeid='.$notice->id);
			if($totalversions>0){
					$latest_version=get_current_notice_versioninfo($notice->id);
				$attach=$latest_version->version_internal_file_path;
			}else{
				$attach=$notice->notice_filename;
				
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
                    $template = mail_template('notice_send_to_customer', $notice, $contact->email, $legals);

                    if ($attachpdf) {
                        $template->add_attachment([
                             'attachment' => get_upload_path_by_type('notice') . $notice->id . '/' . $attach,
                              'filename'   => $attach,//slug_it($notice->subject) . '.pdf',
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
                $template = mail_template('notice_send_to_customer', $notice, $secondary_email, $cc='');

                            if ($attachpdf) {
                                $template->add_attachment([
                                    'attachment' => get_upload_path_by_type('notice') . $notice->id . '/' . $attach,
                                    'filename'   => $attach,//slug_it($notice->subject) . '.pdf',
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
     * Delete notice attachment
     * @param  mixed $attachment_id
     * @return boolean
     */
    public function delete_notice_attachment($attachment_id)
    {
        $deleted    = false;
        $attachment = $this->get_notice_attachments($attachment_id);

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('notice') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('notice Attachment Deleted [noticeID: ' . $attachment->rel_id . ']');
                $this->log_notice_activity($attachment->rel_id, 'not_notice_attachment_removed');
            }

            if (is_dir(get_upload_path_by_type('notice') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('notice') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('notice') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * Renew notice
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
        // get the original notice so we can check if is expiry notified on delete the expiry to revert
        $_notice                         = $this->get($data['noticeid']);
        $data['is_on_old_expiry_notified'] = $_notice->isexpirynotified;
        $this->db->insert(db_prefix() . 'notice_renewals', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->where('id', $data['noticeid']);
            $_data = [
                'datestart'        => $data['new_start_date'],
                'notice_value'   => $data['new_value'],
                'isexpirynotified' => 0,
            ];

            if (isset($data['new_end_date'])) {
                $_data['dateend'] = $data['new_end_date'];
            }

            if (!$keepSignature) {
                $_data           = array_merge($_data, get_acceptance_info_array(true));
                $_data['signed'] = 0;
                if (!empty($_notice->signature)) {
                    unlink(get_upload_path_by_type('notice') . $data['noticeid'] . '/' . $_notice->signature);
                }
            }

            $this->db->update(db_prefix() . 'notices', $_data);
            if ($this->db->affected_rows() > 0) {
				handle_noticerenew_file_upload($insert_id,$data['noticeid']);
                log_activity('notice Renewed [ID: ' . $data['noticeid'] . ']');
                $this->log_notice_activity($data['noticeid'], 'not_notice_renewed');

                return true;
            }
            // delete the previous entry
            $this->db->where('id', $insert_id);
            $this->db->delete(db_prefix() . 'notice_renewals');

            return false;
        }

        return false;
    }

    /**
     * Delete notice renewal
     * @param  mixed $id         renewal id
     * @param  mixed $noticeid notice id
     * @return boolean
     */
    public function delete_renewal($id, $noticeid)
    {
        // check if this renewal is last so we can revert back the old values, if is not last we wont do anything
        $this->db->select('id')->from(db_prefix() . 'notice_renewals')->where('noticeid', $noticeid)->order_by('id', 'desc')->limit(1);
        $query                 = $this->db->get();
        $last_notice_renewal = $query->row()->id;
        $is_last               = false;
        if ($last_notice_renewal == $id) {
            $is_last = true;
            $this->db->where('id', $id);
            $original_renewal = $this->db->get(db_prefix() . 'notice_renewals')->row();
        }

        $notice = $this->get($id);
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'notice_renewals');
        if ($this->db->affected_rows() > 0) {
            if (!is_null($notice->short_link)) {
                app_archive_short_link($notice->short_link);
            }

            if ($is_last == true) {
                $this->db->where('id', $noticeid);
                $data = [
                    'datestart'        => $original_renewal->old_start_date,
                    'notice_value'   => $original_renewal->old_value,
                    'isexpirynotified' => $original_renewal->is_on_old_expiry_notified,
                ];
                if ($original_renewal->old_end_date != '0000-00-00') {
                    $data['dateend'] = $original_renewal->old_end_date;
                }
                $this->db->update(db_prefix() . 'notices', $data);
            }
            log_activity('notice Renewed [RenewalID: ' . $id . ', noticeID: ' . $noticeid . ']');
            $this->log_notice_activity($noticeid, 'not_notice_renewed_rollback');

            return true;
        }

        return false;
    }

    /**
     * Get the notices about to expired in the given days
     *
     * @param  integer|null $staffId
     * @param  integer $days
     *
     * @return array
     */
    public function get_notices_about_to_expire($staffId = null, $days = 7)
    {
        $diff1 = date('Y-m-d', strtotime('-' . $days . ' days'));
        $diff2 = date('Y-m-d', strtotime('+' . $days . ' days'));

        if ($staffId && ! staff_can('view', 'notices', $staffId)) {
            $this->db->where('addedfrom', $staffId);
        }

        $this->db->select('id,subject,client,datestart,dateend');

        $this->db->where('dateend IS NOT NULL');
        $this->db->where('trash', 0);
        $this->db->where('dateend >=', $diff1);
        $this->db->where('dateend <=', $diff2);

        return $this->db->get(db_prefix() . 'notices')->result_array();
    }

    /**
     * Get notice renewals
     * @param  mixed $id notice id
     * @return array
     */
    public function get_notice_renewal_history($id)
    {
        $this->db->where('noticeid', $id);
        $this->db->order_by('date_renewed', 'asc');

        return $this->db->get(db_prefix() . 'notice_renewals')->result_array();
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get notice type object based on passed id if not passed id return array of all types
     */
    public function get_notice_types($id = '')
    {
        return $this->notice_types_model->get($id);
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete notice type from database, if used return array with key referenced
     */
    public function delete_notice_type($id)
    {
        return $this->notice_types_model->delete($id);
    }

    /**
     * Add new notice type
     * @param mixed $data All $_POST data
     */
    public function add_notice_type($data)
    {
        return $this->notice_types_model->add($data);
    }

    /**
     * Edit notice type
     * @param mixed $data All $_POST data
     * @param mixed $id notice type id
     */
    public function update_notice_type($data, $id)
    {
        return $this->notice_types_model->update($data, $id);
    }

    /**
     * Get notice types data for chart
     * @return array
     */
    public function get_notices_types_chart_data()
    {
        return $this->notice_types_model->get_chart_data();
    }

    /**
    * Get notice types values for chart
    * @return array
    */
    public function get_notices_types_values_chart_data()
    {
        return $this->notice_types_model->get_values_chart_data();
    }
	
	 public function fetch_notice_details($q, $limit, $start,$noticetype,$status)
    {
        $result = [
            'result'         => [],
            'type'           => 'notices',
            'search_heading' => _l('notices'),
        ];

        $projects = has_permission('notices', '', 'view');
        // Projects
        $this->db->select('*,tblnotices.subject as proejct_name,tblnotices.id as id,tbloppositeparty.name as oppositeparty');
        $this->db->from(db_prefix() . 'notices');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'notices.client');
		  $this->db->join(db_prefix() . 'oppositeparty', db_prefix() . 'oppositeparty.id = ' . db_prefix() . 'notices.other_party');
     //   $this->db->join(db_prefix() . 'notices_types', db_prefix() . 'notices_types.id = ' . db_prefix() . 'notices.notice_type','left');
         /*if (!$projects) {
            $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
        }
        if ($where != false) {
            $this->db->where($where);
        }*/

        if ($noticetype != '') {
            $this->db->where('notice_type',$noticetype);
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
        $this->db->order_by('tblnotices.id', 'desc');
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
                                    <a href="'.admin_url('notices/notice/'.$project_['id']).'" ><h5 class="card-title" ><strong>'.$project_['proejct_name'].'</strong></h5></a>
                                    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('client').':</b>'.$project_['company'].'</p>
									  <p class="card-text" style="margin:  0 0 4px;"><b>'._l('opposite_party').':</b>'.$project_['oppositeparty'].'</p>
									   <p class="card-text" style="margin: 0 0 4px;"><b>'._l('notice_type').' :</b>'. get_noticetype_name_by_id($project_['notice_type']).'</p>
									    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('notice_value').':</b>'.number_format($project_['notice_value']).'</p>
                                    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('project_start_date').':</b>'._d($project_['datestart']).'</span> |   <button type="button" class="btn btn-default btn-sm btn-icon mleft10  pop" data-container="body" data-toggle="popover" data-placement="bottom" data-content="'.$cont.'"
    data-original-title="'.$up[0].'" title="'.$up[0].'"> <i class="fa fa-tag"></i></button> </p>
                                 
                                </div>
                            </div> 
                        </div> 

                        ';

               
        }

        return $res;
    }

     public function fetch_notice_details_num_rows($q, $casetype,$status)
    {
        $result = [
            'result'         => [],
            'type'           => 'notices',
            'search_heading' => _l('notices'),
        ];

        $projects = has_permission('notices', '', 'view');
        // Projects
        $this->db->select();
        $this->db->from(db_prefix() . 'notices');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . db_prefix() . 'notices.client');
        /*   if (!$projects) {
            $this->db->where(db_prefix() . 'projects.id IN (SELECT project_id FROM ' . db_prefix() . 'project_members WHERE staff_id=' . get_staff_user_id() . ')');
        }
     if ($where != false) {
            $this->db->where($where);
        }*/

        if ($casetype != '') {
            $this->db->where('notice_type',$casetype);
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
	 public function get_notice_members($id)
    {
        $this->db->select('email,noticeid,staff_id');
        $this->db->join(db_prefix() . 'staff', db_prefix() . 'staff.staffid=' . db_prefix() . 'notices_assigned.staff_id');
        $this->db->where('noticeid', $id);

        return $this->db->get(db_prefix() . 'notices_assigned')->result_array();
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
        $project      = $this->db->get(db_prefix() . 'notices')->row();
        $project_name = $project->subject;
        $client_id    = $project->client;

        $project_members_in = $this->get_notice_members($id);
        if (sizeof($project_members_in) > 0) {
            foreach ($project_members_in as $project_member) {
                if (isset($project_members)) {
                    if (!in_array($project_member['staff_id'], $project_members)) {
                        $this->db->where('noticeid', $id);
                        $this->db->where('staff_id', $project_member['staff_id']);
                        $this->db->delete(db_prefix() . 'notices_assigned');
                        if ($this->db->affected_rows() > 0) {
                          // $this->log_activity($id, 'project_activity_removed_team_member', get_staff_full_name($project_member['staff_id']));
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('noticeid', $id);
                    $this->db->delete(db_prefix() . 'notices_assigned');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($project_members)) {
                $notifiedUsers = [];
                foreach ($project_members as $staff_id) {
                    $this->db->where('noticeid', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get(db_prefix() . 'notices_assigned')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert(db_prefix() . 'notices_assigned', [
                            'noticeid' => $id,
                            'staff_id'   => $staff_id,
							'assigned_from'=>get_staff_user_id(),
                        ]);
                        if ($this->db->affected_rows() > 0) {
                            if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification([
                                    'fromuserid'      => get_staff_user_id(),
                                    'description'     => 'not_staff_added_as_project_member',
                                    'link'            => 'notices/notice/' . $id,
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
                    $this->db->insert(db_prefix() . 'notices_assigned', [
                        'noticeid' => $id,
                        'staff_id'   => $staff_id,
						'assigned_from'=>get_staff_user_id(),
                    ]);
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification([
                                'fromuserid'      => get_staff_user_id(),
                                'description'     => 'not_staff_added_as_project_member',
                                'link'            => 'notices/notice/' . $id,
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
            $all_members = $this->get_notice_members($id);
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
	 public function delete_notice_document($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'notices')->row();
        $deleted    = false;
        if ($attachment) {
            
                $relPath  = get_upload_path_by_type('notice') . $id . '/';
                $fullPath = $relPath . $attachment->notice_filename;
                unlink($fullPath);
            $this->db->where('id', $id);
			$this->db->update(db_prefix() . 'notices', [
            'notice_filename' => null,
			'file_type' => null,
			'content' =>'',
        	]);
           
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }
    	}
	}

    public function delete_signed_notice_document($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get(db_prefix() . 'notices')->row();
        $deleted    = false;
        if ($attachment) {
            
                $relPath  = get_upload_path_by_type('notice') . $id . '/';
                $fullPath = $relPath . $attachment->signed_notice_filename;
                unlink($fullPath);
            $this->db->where('id', $id);
			$this->db->update(db_prefix() . 'notices', [
            'signed_notice_filename' => null,
			'file_type' => null,
			'content' =>'',
        	]);
           
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
            }
    	}
	}
   public function make_final_doc($notice_id,$version)
    {
        
        $this->db->where('id', $notice_id);
        $this->db->update(db_prefix() . 'notices', [
            'final_doc' => $version,
        ]);
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }

    public function get_notices_of_oppositeparty($id = '')

    {

        $this->db->select('tbloppositeparty.id as id,tbloppositeparty.name as name,tbloppositeparty.email as email,tbloppositeparty.mobile as mobile');

        $this->db->join('tblnotices', 'tbloppositeparty.id=tblnotices.other_party');

        if(is_numeric($id)){

            $this->db->where('tblnotices.id',$id);   

        }



        $leads = $this->db->get('tbloppositeparty')->result_array();



        return $leads;



    }

    public function get_notice_activity_log($id)
    {
        $sorting = hooks()->apply_filters('notice_activity_log_default_sort', 'ASC');

        $this->db->where('noticeid', $id);
        $this->db->order_by('date', $sorting);

        return $this->db->get(db_prefix() . 'notice_activity_log')->result_array();
    }
	public function log_notice_activity($id, $description, $integration = false, $additional_data = '')
    {
        $log = [
            'date'            => date('Y-m-d H:i:s'),
            'description'     => $description,
            'noticeid'          => $id,
            'staffid'         => get_staff_user_id(),
            'additional_data' => $additional_data,
            'full_name'       => get_staff_full_name(get_staff_user_id()),
        ];
        if ($integration == true) {
            $log['staffid']   = 0;
            $log['full_name'] = '[CRON]';
        }

        $this->db->insert(db_prefix() . 'notice_activity_log', $log);

        return $this->db->insert_id();
    }
 public function get_templates_of_notice($id = '')
  {
       $this->db->select('tbltemplates.id as id,tbltemplates.name');

       // $this->db->join('tblclient_oppositeparty_rel', 'tbloppositeparty.id=tblclient_oppositeparty_rel.opposite_party_id', 'left');
         $this->db->where('tbltemplates.type','notices');
        if(is_numeric($id)){

            $this->db->where('tbltemplates.agreement_type',$id);   

        }

        $leads = $this->db->get('tbltemplates')->result_array();

        return $leads;

    }

    public function add_notice_status($data)
    {
        $this->db->insert(db_prefix().'notices_status', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New notice Status Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }
    public function update_notice_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'notices_status', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('notice Status Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_notice_terms($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'notice_terms');
        if ($this->db->affected_rows() > 0) {
            log_activity('notice Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
    public function delete_notice_status($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'notices_status');
        if ($this->db->affected_rows() > 0) {
            log_activity('notice Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
    public function get_activity($id = '', $limit = '', $only_notice_members_activity = false)
    {
        if (!is_client_logged_in()) {
            $has_permission = has_permission('projects', '', 'view');
            if (!$has_permission) {
                $this->db->where('noticeid IN (SELECT noticeid FROM ' . db_prefix() . 'notices_assigned WHERE staff_id=' . get_staff_user_id() . ')');
            }
        }
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        if (is_numeric($id)) {
            $this->db->where('notice_id', $id);
        }
        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }
        $this->db->order_by('date', 'desc');
        $activities = $this->db->get(db_prefix() . 'notice_activity_log')->result_array();
	
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
            $activities[$i]['notice_name']    = get_notice_name_by_id($activity['noticeid']);
          //  unset($activities[$i]['description']);
            $i++;
        }

        return $activities;
    }
	public function get_notice_status($id = '',$where=[])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'notices_status')->row();
        }
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $this->db->order_by('statusorder', 'asc');

        return $this->db->get(db_prefix() . 'notices_status')->result_array();
    }
	
	public function change_version_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'notice_versions', [
            'active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('notice_version_status_changed', [
                'id'     => $id,
                'status' => $status,
            ]);

            log_activity('notice Version Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');
			$version=$this->get_noticeversioninfo($id);
          $this->log_notice_activity($version->noticeid, 'not_noticeversion_status_changed');
            return true;
        }

        return false;
    }
	public function get_noticeversioninfo($id)
    {
        $this->db->where('id', $id);

        return $this->db->get(db_prefix() . 'notice_versions')->row();
    }
	public function get_notice_trakings($id='',$trackingno='')
    {	
		if (is_numeric($id)) {
        $this->db->where('id', $id);
	}
	 if (!empty($trackingno)) {
        $this->db->where('notice_trackno', $trackingno);
	}
        return $this->db->get(db_prefix() . 'notice_tracking')->result_array();
    }
	
}
