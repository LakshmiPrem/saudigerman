<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contracts_model extends App_Model
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

        $data['content'] = '<h3 style="text-align:center;"><span style="text-decoration:underline;"><strong>Contract Agreement (the &#8220;Agreement&#8221;)</strong></span></h3>
<p style="text-align:left;"><strong>First Party:</strong></p>
<p style="text-align:left;">{client_company} , {client_city} , {client_address}</p>
<p style="text-align:left;">{client_phonenumber},{contact_email}</p>
<p style="text-align:left;"><strong>Second Party:</strong></p>
<p style="text-align:left;">{companyname}</p>
<p style="text-align:left;"><br>Whereas<br>1. First Party wishes to hire the debt collection services of the Second Party to represent it and collect its receivables (the &#8220;Debt Amount&#8221;) from its different debtors (the &#8220;Debtors&#8221;) in the UAE.</p>
<p style="text-align:left;">2. This is an exclusive agreement between the Parties in respect of the Debtors provided by the First Party to the Second Party for its services and First Party hereby confirms and agrees that it will not engage other service provider(s) for the same Debtors allocated to the Second Party for the scope of services agreed in this Agreement, without the written consent of the Second Party. All the accounts hand over to the Second Party for its Service during the validity of this Agreement shall form part of the Annexure-1 and it will be part of this Agreement.<br>Now therefore the Parties agree as follows:</p>
<p style="text-align:left;">I. Preamble is the integral part of this Agreement.</p>
<p style="text-align:left;">II. The First Party agrees to hire the following services (the &#8220;Services&#8221;) of the Second Party.</p>
<p style="text-align:left;">a) Friendly negotiation.<br>b) Email &amp; Telephonic follow-ups.<br>c) Direct meeting and negotiations with Debtors.<br>d) Sending and follow with Legal notice.<br>e) Legal Negotiations.<br>f) Study, advice and drafting of necessary documents for the completion of the Service.<br>g) File, follow up, support and release the cheque bounced criminal cases are subject to the approval of the costs covered in Clause-VIII.<br>h) For avoidance of doubt, all Civil &amp; Commercial legal actions are excluded from the scope of the Service.</p>
<p style="text-align:left;">III. Period: This Agreement is valid for [xxxx] period and may be renewed for another term as per the mutual agreement between the Parties.</p>
<p style="text-align:left;">IV. Obligation of the First Party: First Party shall provide all the details, information, documents related to the debts, contact details and e-mail, physical assistance in field visits, provide an authorization and/or power of attorney (if required by the First Party) for necessary action to provide the Services properly and also follow and obey the other terms of this Agreement.</p>
<p style="text-align:left;">V. Obligations of the Second Party: Second Party shall do all the agreed Service with due care, with proper updates at least twice in a month, frequent follow up with the Debtor(s) and do all possible legal steps to collect the Debt Amounts within the scope of the Services agreed. The Second Party shall not receive any amount of the debts directly from the Debtors of the First Party unless it is approved by the First Party. Second Party always recommends the First Party to collect the Debt Amount directly from the Debtors and reporting to the Second Party within 2(two) business days of such collection.</p>
<p style="text-align:left;">VI. Fees &amp; Costs: The First Party agrees to give AED_-----_____ (________ Dirham only) as non-refundable registration fee (the &#8220;Registration fee&#8221;) to the Second Party to open the files for rendering the Service and this Registration Fee is valid ------(period), and it will be paid at the time of signing this Agreement. The professional fee (the &#8220;Professional Fee) is agreed as stated in the below table and it is calculated on the basis of each debt allocated to the Second Party. If there is no collection, the First Party is not entitled to pay the Professional Fee except as provided in Clause - IX. The Second Party is entitled to claim its Professional Fee as soon as a portion or whole of the Debt Amount has been successfully collected or settled by the Debtor on pro-rate basis; for avoidance of doubt, if the First Party has received Debt Amount either by cash, cheque or wire transfer or adjusted a debt by any other mode of settlement between them, the Second Party is entitled for the pro-rata Professional Fee for such part of the Debt Amount received or settled between Debtor and the First Party as per the terms acceptable to both. Periodical invoices will be sent by the Second Party for the Professional Fee. First Party shall settle all the invoices issued by the Second Party within 3 (three) business days of the issuance of each invoice. All the government fee, costs and VAT are not included in the fee of the Second Party.</p>
<p style="text-align:left;">Professional Fee</p>
<p style="text-align:left;"><br>Debt Amount in AED <br>Percentage<br>Below 50,000 %<br>Between 50,001 to 100,000 %<br>Between 100,001 to 300,000 %<br>Above 300,001 %</p>
<p style="text-align:left;">VII. Confidentiality: Both Parties hereby agree to keep the confidentiality of the data handed over to the other and will not disclose to any third party (except the Debtor) without prior consent of the concerned party. Whereas the information and data will be used for the completion of the Services.<br>VIII. <br>Cheque Case Filing and Support: Upon the request of the First Party to file and follow up cheque bounced cases, the Second Party will agree to Provide the service to file the cheque case(s) with police and to follow up until get a judgment from the criminal court of first instance and the fee for the service will be AED 1000(One Thousand Dirhams Only) per bounced cheque. All the costs related to the service shall be reimbursed/paid to the Second Party as and when invoiced by the Second Party. <br>IX. Termination: The innocent Party has to give minimum 30 (Thirty) days&#8217; notice to the violating party for the termination to cure the violation or breach of a term of this Agreement, and it is not cured within thirty days&#8217; time of such notice by the violator, then Agreement will be terminated. The Second Party has the right to claim all the pending Professional Fee, Registration Fee or other costs related to the Service due at the time of termination and the Second Party shall stop all the actions related to the Services and return all the data to the First Party with immediate effect.</p>
<p style="text-align:left;">Intentional Termination: The First Party in any case shall not terminate the Agreement, before the term of this Agreement. However, in case of termination initiated from the First Party as per Clause IX, the First Party shall liable to clear all the outstanding with the Second Party as per the agreed terms herein. [At the period of termination, if any Debtors have promised to pay any Debt Amount or its part, then the Second Party has to claim the Professional Fee as soon as the payment has been settled with the First Party.]<br>The First Party in any case shall not terminate the Agreement, before the period mentioned in clause III. However, in case of termination initiated from the First Party, the First Party shall liable to pay Second Party as below and the Second Party is always liberty to contact the debtors.</p>
<p style="text-align:left;">(1) 25% of the agreed Professional Fee for each debt after initiation of debt collection measures mentioned in Clause II. <br>(2) 50% of the agreed Professional Fee for each debts after getting a positive response received from debtor(s). <br>(3) 100% of the agreed Professional Fee for each debts final confirmation of the debtor received or debt settled collected or settled either way.</p>
<p style="text-align:left;"><br><br>X. Disputes: All disputes shall be settled amicably and if it has not been settled, it shall be resolved as per the UAE law and subject to the jurisdiction of the Abu Dhabi Court.</p>
<p style="text-align:left;">XI. Liability of the Second Party: The maximum liability of the Second Party to the extent of the Registration Fee that it has been received as per this Agreement.</p>
<p style="text-align:left;">XII. Counterpart: This Agreement may be executed in any number of counterparts, each of which, when executed and delivered, will be an original, and all the counterparts together constitute one and the same instrument.</p>
<p style="text-align:left;">XIII. Amendments: All the amendment shall be written and verbal communications shall not be taking into account for any matters related this Agreement and Services.</p>
<p style="text-align:left;">IN WITNESS WHEREOF, the Parties have caused this Agreement to be executed by their respective duly authorized representatives as of the date first written above.</p>
<p style="text-align:left;">Signed by: <br>Mr. -----------------<br>For the First Party</p>
<p style="text-align:left;">Stamp</p>
<p style="text-align:left;">Date:</p>
<p style="text-align:left;">Signed by: <br>Mr.-------------<br>For the Second Party</p>
<p></p>
<p></p>';
        $data['hash'] = app_generate_hash();

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
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'contract_comments', [
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
            // Get related tasks
            $this->db->where('rel_type', 'contract');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get(db_prefix() . 'tasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }

            delete_tracked_emails($id, 'contract');

            log_activity('Contract Deleted [' . $id . ']');

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
        $this->db->update('contracts', ['marked_as_signed' => 1]);

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
        $this->db->update('contracts', ['marked_as_signed' => 0]);

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
                unlink(get_upload_path_by_type('contract') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete(db_prefix() . 'files');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Contract Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
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
									    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('contract_value').':</b>'.$project_['contract_value'].'</p>
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
}
