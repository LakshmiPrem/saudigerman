<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Legalrisk_model extends App_Model
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
    public function get($id = '', $where = array(), $for_editor = false)
    {
        $this->db->select('*,tbllegal_risk.id as id, tbllegal_risk.addedfrom');
        $this->db->where($where);
        //$this->db->join('tblcontracttypes', 'tblcontracttypes.id = tbltrade_licenses.contract_type', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbllegal_risk.branchid');
        if (is_numeric($id)) {
            $this->db->where('tbllegal_risk.id', $id);
            $contract = $this->db->get('tbllegal_risk')->row();

            $contract->attachments = $this->get_contract_attachments('', $contract->id);
          

            return $contract;
        }
        $contracts = $this->db->get('tbllegal_risk')->result_array();
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
    public function get_trade_license_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(issue_date)) as year FROM tbltrade_licenses')->result_array();
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

            return $this->db->get('tblfiles')->row();
        }
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'legalrisk');

        return $this->db->get('tblfiles')->result_array();
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

      //  $data['issue_date'] = to_sql_date($data['issue_date']);
        unset($data['attachment']);
        
        if (isset($data['trash']) && ($data['trash'] == 1 || $data['trash'] === 'on')) {
            $data['trash'] = 1;
        } else {
            $data['trash'] = 0;
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
        $data =hooks()->apply_filters('before_contract_added', $data);
        $this->db->insert('tbllegal_risk', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            hooks()->apply_filters('after_contract_added', $insert_id);
            log_activity('New Legal Risk Added [' . $data['risktitle'] . ']');

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
        $affectedRows      = 0;
      //  $data['issue_date'] = to_sql_date($data['issue_date']);
      
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
        $_data = hooks()->apply_filters('before_contract_updated', array(
            'data' => $data,
            'id' => $id,
        ));
        $data  = $_data['data'];
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        $this->db->where('id', $id);
        $this->db->update('tbllegal_risk', $data);
        if ($this->db->affected_rows() > 0) {
          hooks()->apply_filters('after_contract_updated', $id);
            log_activity('Legal Risk Updated [' . $data['risktitle'] . ']');

            return true;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function copy($id)
    {
        $contract = $this->get($id, array(), true);
        $fields = $this->db->list_fields('tbllegal_risk');
        $newContactData = array();

        foreach ($fields as $field) {
            if (isset($contract->$field)) {
                $newContactData[$field] = $contract->$field;
            }
        }

        unset($newContactData['id']);

        $newContactData['trash'] = 0;
        $newContactData['isexpirynotified'] = 0;
        $newContactData['datestart'] = _d(date('Y-m-d'));

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
                $value = get_custom_field_value($id, $field['id'], 'contracts');
                if ($value != '') {
                    $this->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $newId,
                    'fieldid' => $field['id'],
                    'fieldto' => 'contracts',
                    'value' => $value,
                    ));
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
        
         $this->db->where('id', $id);
        $this->db->delete('tbllegal_risk');
        if ($this->db->affected_rows() > 0) {
            

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'legalrisk');
            $attachments = $this->db->get('tblfiles')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_contract_attachment($attachment['id']);
            }
          $this->db->where('rel_type', 'legalrisk');
            $this->db->where('rel_id', $id);
            $this->db->delete('tblreminders'); 
			
			$this->db->where('rel_type', 'legalrisk');
            $this->db->where('rel_id', $id);
            $this->db->delete('tblnotes');
            // Get related tasks
            $this->db->where('rel_type', 'legalrisk');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get('tblstafftasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            log_activity('Legal Risk Deleted [' . $id . ']');
            return true;
        }

        return false;
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
        $this->load->model('emails_model');
        $contract = $this->get($id);

        if ($attachpdf) {
            $pdf    = contract_pdf($contract);
            $attach = $pdf->Output(slug_it($contract->subject) . '.pdf', 'S');
        }
        $sent_to = $this->input->post('sent_to');
        $sent    = false;
        if (is_array($sent_to)) {
            $i = 0;
            foreach ($sent_to as $contact_id) {
                if ($contact_id != '') {
                    if ($attachpdf) {
                        $this->emails_model->add_attachment(array(
                            'attachment' => $attach,
                            'filename' => slug_it($contract->subject) . '.pdf',
                            'type' => 'application/pdf',
                        ));
                    }
                    if ($this->input->post('email_attachments')) {
                        $_other_attachments = $this->input->post('email_attachments');
                        foreach ($_other_attachments as $attachment) {
                            $_attachment = $this->get_contract_attachments($attachment);
                            $this->emails_model->add_attachment(array(
                                'attachment' => get_upload_path_by_type('contract') . $id . '/' . $_attachment->file_name,
                                'filename' => $_attachment->file_name,
                                'type' => $_attachment->filetype,
                                'read' => true,
                            ));
                        }
                    }
                    $contact      = $this->clients_model->get_contact($contact_id);
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($contract->client, $contact_id));
                    $merge_fields = array_merge($merge_fields, get_contract_merge_fields($id));
                    // Send cc only for the first contact
                    if (!empty($cc) && $i > 0) {
                        $cc = '';
                    }
                    if ($this->emails_model->send_email_template('send-contract', $contact->email, $merge_fields, '', $cc)) {
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
                unlink(get_upload_path_by_type('legalrisk') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Legal risk Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('legalrisk') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('legalrisk') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('legalrisk') . $attachment->rel_id);
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
        $this->db->insert('tblcontractrenewals', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->db->where('id', $data['contractid']);
            $_data = array(
                'datestart' => $data['new_start_date'],
                'contract_value' => $data['new_value'],
                'isexpirynotified' => 0,
            );
            if (isset($data['new_end_date'])) {
                $_data['dateend'] = $data['new_end_date'];
            }
            $this->db->update('tbltrade_licenses', $_data);
            if ($this->db->affected_rows() > 0) {
                log_activity('Contract Renewed [ID: ' . $data['contractid'] . ']');

                return true;
            } else {
                // delete the previous entry
                $this->db->where('id', $insert_id);
                $this->db->delete('tblcontractrenewals');

                return false;
            }
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
        $this->db->select('id')->from('tblcontractrenewals')->where('contractid', $contractid)->order_by('id', 'desc')->limit(1);
        $query                 = $this->db->get();
        $last_contract_renewal = $query->row()->id;
        $is_last               = false;
        if ($last_contract_renewal == $id) {
            $is_last = true;
            $this->db->where('id', $id);
            $original_renewal = $this->db->get('tblcontractrenewals')->row();
        }
        $this->db->where('id', $id);
        $this->db->delete('tblcontractrenewals');
        if ($this->db->affected_rows() > 0) {
            if ($is_last == true) {
                $this->db->where('id', $contractid);
                $data = array(
                    'datestart' => $original_renewal->old_start_date,
                    'contract_value' => $original_renewal->old_value,
                    'isexpirynotified' => $original_renewal->is_on_old_expiry_notified,
                );
                if ($original_renewal->old_end_date != '0000-00-00') {
                    $data['dateend'] = $original_renewal->old_end_date;
                }
                $this->db->update('tbltrade_licenses', $data);
            }
            log_activity('Contract Renewed [RenewalID: ' . $id . ', ContractID: ' . $contractid . ']');

            return true;
        }

        return false;
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

        return $this->db->get('tblcontractrenewals')->result_array();
    }

   // Legal risk statuses

    /**
     * Get Legal risk by id
     * @param  mixed $id status id
     * @return mixed     if id passed return object else array
     */
    public function get_legalrisk_status($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'riskstatus')->row();
        }
        $this->db->order_by('id', 'asc');

        return $this->db->get(db_prefix() . 'riskstatus')->result_array();
    }

    /**
     * Add new Legal risk status
     * @param arrayLegal risk status $_POST data
     * @return mixed
     */
    public function add_legalrisk_status($data)
    {
        $this->db->insert(db_prefix() . 'riskstatus', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Legal Risk Status Added [ID: ' . $insert_id . ', ' . $data['statusname'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update Legal risk status
     * @param  array $data Legal risk status $_POST data
     * @param  mixed $id   Legal risk status id
     * @return boolean
     */
    public function update_legalrisk_status($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'riskstatus', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Legal risk Status Updated [ID: ' . $id . ' Name: ' . $data['statusname'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete Legal risk status
     * @param  mixed $id Legal risk status id
     * @return mixed
     */
    public function delete_legalrisk_status($id)
    {
        $current = $this->get_legalrisk_status($id);
        // Default statuses cant be deleted
        if ($current->isdefault == 1) {
            return [
                'default' => true,
            ];
        // Not default check if if used in table
        } elseif (is_reference_in_table('risk_status', db_prefix() . 'legal_risk', $id)) {
            return [
                'referenced' => true,
            ];
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'riskstatus');
        if ($this->db->affected_rows() > 0) {
            log_activity('Risk Status Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

// Legal risk statuses

    /**
     * Get Legal risk by id
     * @param  mixed $id types id
     * @return mixed     if id passed return object else array
     */
    public function get_legalrisk_type($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'risktypes')->row();
        }
        $this->db->order_by('id', 'asc');

        return $this->db->get(db_prefix() . 'risktypes')->result_array();
    }

    /**
     * Add new Legal risk type
     * @param arrayLegal risk status $_POST data
     * @return mixed
     */
    public function add_legalrisk_type($data)
    {
        $this->db->insert(db_prefix() . 'risktypes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Legal Risk Type Added [ID: ' . $insert_id . ', ' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Update Legal risk types
     * @param  array $data Legal risk types $_POST data
     * @param  mixed $id   Legal risk types id
     * @return boolean
     */
    public function update_legalrisk_type($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'risktypes', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Legal risk types Updated [ID: ' . $id . ' Name: ' . $data['name'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete Legal risk types
     * @param  mixed $id Legal risk types id
     * @return mixed
     */
    public function delete_legalrisk_type($id)
    {
        $current = $this->get_legalrisk_type($id);
        // Default statuses cant be deleted
        if ($current->isdefault == 1) {
            return [
                'default' => true,
            ];
        // Not default check if if used in table
        } elseif (is_reference_in_table('risktype', db_prefix() . 'legal_risk', $id)) {
            return [
                'referenced' => true,
            ];
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix() . 'risktypes');
        if ($this->db->affected_rows() > 0) {
            log_activity('Risk Type Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }
}
