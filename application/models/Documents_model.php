<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Documents_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('document_types_model');
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
        $this->db->select('*,tbldocument_types.name as type_name,tbldocuments.id as id, tbldocuments.addedfrom');
        $this->db->where($where);
        $this->db->join('tbldocument_types', 'tbldocument_types.id = tbldocuments.contract_type', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbldocuments.client');
        if (is_numeric($id)) {
            $this->db->where('tbldocuments.id', $id);
            $contract = $this->db->get('tbldocuments')->row();

            $contract->attachments = $this->get_contract_attachments('', $contract->id);
     /*       if ($for_editor == false) {
                $merge_fields = array();
                //$merge_fields = array_merge($merge_fields, get_contract_merge_fields($id));
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($contract->client));
                $merge_fields = array_merge($merge_fields, get_other_merge_fields());
                foreach ($merge_fields as $key => $val) {
                    if (stripos($contract->content, $key) !== false) {
                        $contract->content = str_ireplace($key, $val, $contract->content);
                    } else {
                        $contract->content = str_ireplace($key, '', $contract->content);
                    }
                }
            }*/

            return $contract;
        }
        $contracts = $this->db->get('tbldocuments')->result_array();
        $i         = 0;
        foreach ($contracts as $contract) {
            $contracts[$i]['attachments'] = $this->get_contract_attachments('', $contract['id']);
            $i++;
        }

        return $contracts;
    }

        public function get_in($id = '', $where = array(), $for_editor = false)
    {
        $this->db->select('*,tbldocument_types.name as type_name,tbldocuments_in.id as id, tbldocuments_in.addedfrom');
        $this->db->where($where);
        $this->db->join('tbldocument_types', 'tbldocument_types.id = tbldocuments_in.document_type', 'left');
        $this->db->join('tblclients', 'tblclients.userid = tbldocuments_in.client');
        if (is_numeric($id)) {
            $this->db->where('tbldocuments_in.id', $id);
            $contract = $this->db->get('tbldocuments_in')->row();

            $contract->attachments = $this->get_contract_attachments('', $contract->id,'documents_in');
            
                
            //$contract->content = str_ireplace($key, $val, $contract->content);
                    

            return $contract;
        }
        $contracts = $this->db->get('tbldocuments_in')->result_array();
        $i         = 0;
        foreach ($contracts as $contract) {
            $contracts[$i]['attachments'] = $this->get_contract_attachments('', $contract['id'],'documents_in');
            $i++;
        }

        return $contracts;
    }

      public function get_out($id = '', $where = array(), $for_editor = false)
    {
        $this->db->select('*,tbldocument_types.name as type_name,tblcommunication.id as id, tblcommunication.addedfrom');
        $this->db->where($where);
        $this->db->join('tbldocument_types', 'tbldocument_types.id = tblcommunication.document_type', 'left');
        // $this->db->join('tblclients', 'tblclients.userid = tblcommunication.client');
        if (is_numeric($id)) {
            $this->db->where('tblcommunication.id', $id);
            $contract = $this->db->get('tblcommunication')->row();

            $contract->attachments = $this->get_contract_attachments('', $contract->id,'communication');
            

            return $contract;
        }
        $contracts = $this->db->get('tblcommunication')->result_array();
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
        return $this->db->query('SELECT DISTINCT(YEAR(datestart)) as year FROM tbldocuments')->result_array();
    }

    /**
     * @param  integer ID
     * @return object
     * Retrieve contract attachments from database
     */
    public function get_contract_attachments($attachment_id = '', $id = '',$rel_name = 'document')
    {
        if (is_numeric($attachment_id)) {
            $this->db->where('id', $attachment_id);

            return $this->db->get('tblfiles')->row();
        }
        $this->db->order_by('dateadded', 'desc');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', $rel_name);

        return $this->db->get('tblfiles')->result_array();
    }

    /**
     * @param   array $_POST data
     * @return  integer Insert ID
     * Add new contract
     */
    public function add($data)
    {
        unset($data['selectedcategory']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();
        $doc_other_party=$data['doc_other_party'];
        unset($data['doc_other_party']);
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

        if (isset($data['not_visible_to_client']) && ($data['not_visible_to_client'] == 1 || $data['not_visible_to_client'] === 'on')) {
            $data['not_visible_to_client'] = 1;
        } else {
            $data['not_visible_to_client'] = 0;
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        $data = hooks()->apply_filters('before_contract_added', $data);
        $this->db->insert('tbldocuments', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
			$this->db->where('name', 'next_safefile_number');
            $this->db->set('value', 'value+1', false);
            $this->db->update('tbloptions');
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            hooks()->do_action('after_contract_added', $insert_id);
            log_activity('New Safe Document Added [' . $data['subject'] . ']');
            // adding doc_other_party to table
            $data_other_party['document_id']=$insert_id;
            foreach($doc_other_party as $other_party){
                $data_other_party['other_party_id']= $other_party;
            $this->db->insert('tbldocuments_other_party', $data_other_party);
            }
            return $insert_id;
        }

        return false;
    }

     public function add_in($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $data['sent_date'] = to_sql_date($data['sent_date']);
        unset($data['attachment']);
        if ($data['received_date'] == '') {
            unset($data['received_date']);
        } else {
            $data['received_date'] = to_sql_date($data['received_date']);
        }

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
        $data = hooks()->apply_filters('before_contract_added', $data);
        $this->db->insert('tbldocuments_in', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            hooks()->do_action('after_contract_added', $insert_id);
            log_activity('New Document In Added [' . $data['subject'] . ']');

            return $insert_id;
        }

        return false;
    }

    public function add_out($data)
    {
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();

        $data['date'] = to_sql_date($data['date']);
        $data['date_received'] = to_sql_date($data['date_received']);
        unset($data['attachment']);
        // if ($data['dateend'] == '') {
        //     unset($data['dateend']);
        // } else {
        //     $data['dateend'] = to_sql_date($data['dateend']);
        // }

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
        $data = hooks()->apply_filters('before_contract_added', $data);
        $this->db->insert('tblcommunication', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($insert_id, $custom_fields);
            }
            hooks()->do_action('after_contract_added', $insert_id);
            log_activity('New Document Out Added [' . $data['subject'] . ']');

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
        unset($data['selectedcategory']);
        $affectedRows      = 0;
        $doc_other_party=$data['doc_other_party'];
        unset($data['doc_other_party']);
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
        $this->db->update('tbldocuments', $data);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_contract_updated', $id);
            log_activity('Contract Updated [' . $data['subject'] . ']');
            
            return true;
        }
        //delete old data from dc_party type
        $this->db->where('document_id', $id);
        $this->db->delete('tbldocuments_other_party');
        // adding doc_other_party to table
        $data_other_party['document_id']=$id;
        foreach($doc_other_party as $other_party){
            $data_other_party['other_party_id']= $other_party;
        $this->db->insert('tbldocuments_other_party', $data_other_party);
        }
        if ($affectedRows > 0) {
            
            return true;
        }

        return false;
    }

        public function update_in($data, $id)
    {
        $affectedRows      = 0;
        $data['sent_date'] = to_sql_date($data['sent_date']);
         if ($data['received_date'] == '') {
            unset($data['received_date']);
        } else {
            $data['received_date'] = to_sql_date($data['received_date']);
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
        $this->db->update('tbldocuments_in', $data);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_contract_updated', $id);
            log_activity('Document In Updated [' . $data['subject'] . ']');
           
            return true;
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function update_out($data, $id)
    {
        $affectedRows      = 0;
        $data['date'] = to_sql_date($data['date']);
        $data['date_received'] = to_sql_date($data['date_received']);
        // if ($data['dateend'] == '') {
        //     $data['dateend'] = null;
        // } else {
        //     $data['dateend'] = to_sql_date($data['dateend']);
        // }
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
        $this->db->update('tblcommunication', $data);
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('after_contract_updated', $id);
            log_activity('Document Out Updated [' . $data['subject'] . ']');

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
        $fields = $this->db->list_fields('tbldocuments');
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
            $custom_fields = get_custom_fields('documents');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($id, $field['id'], 'documents');
                if ($value != '') {
                    $this->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $newId,
                    'fieldid' => $field['id'],
                    'fieldto' => 'document',
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
   public function delete($id,$type)
    {
        hooks()->do_action('before_contract_deleted', $id);
        $this->db->where('id', $id);
        if($type == 'in'){
            $this->db->delete('tbldocuments_in');
            $type ='document_in';
        }elseif ($type == 'out') {
            $this->db->delete('tblcommunication');
            $type ='document_out';
        }else{
            $this->db->delete('tbldocuments');
        }
        if ($this->db->affected_rows() > 0) {
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', $type);
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', $type);
            $attachments = $this->db->get('tblfiles')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_contract_attachment($attachment['id']);
            }
            // Get related tasks
            $this->db->where('rel_type', $type);
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get('tbltasks')->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            log_activity('Document Deleted [' . $id . ']');

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
                unlink(get_upload_path_by_type('document') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                log_activity('Document Attachment Deleted [Document ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('document') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('document') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    // okey only index.html so we can delete the folder also
                    delete_dir(get_upload_path_by_type('document') . $attachment->rel_id);
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
        $_contract                         = $this->get($data['Document ID']);
        $data['is_on_old_expiry_notified'] = $_contract->isexpirynotified;
        $this->db->insert('tbldocumentrenewals', $data);
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
            $this->db->update('tbldocuments', $_data);
            if ($this->db->affected_rows() > 0) {
                log_activity('Document Renewed [ID: ' . $data['contractid'] . ']');

                return true;
            } else {
                // delete the previous entry
                $this->db->where('id', $insert_id);
                $this->db->delete('tbldocumentrenewals');

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
        $this->db->select('id')->from('tbldocumentrenewals')->where('contractid', $contractid)->order_by('id', 'desc')->limit(1);
        $query                 = $this->db->get();
        $last_contract_renewal = $query->row()->id;
        $is_last               = false;
        if ($last_contract_renewal == $id) {
            $is_last = true;
            $this->db->where('id', $id);
            $original_renewal = $this->db->get('tbldocumentrenewals')->row();
        }
        $this->db->where('id', $id);
        $this->db->delete('tbldocumentrenewals');
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
                $this->db->update('tbldocuments', $data);
            }
            log_activity('Document Renewed [RenewalID: ' . $id . ', DocumentID: ' . $contractid . ']');

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

        return $this->db->get('tbldocumentrenewals')->result_array();
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get_contract_types($id = '')
    {
        return $this->document_types_model->get($id);
    }
    


    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete_contract_type($id)
    {
        return $this->document_types_model->delete($id);
    }

    /**
     * Add new contract type
     * @param mixed $data All $_POST data
     */
    public function add_contract_type($data)
    {
        return $this->document_types_model->add($data);
    }

    /**
     * Edit contract type
     * @param mixed $data All $_POST data
     * @param mixed $id Contract type id
     */
    public function update_contract_type($data, $id)
    {
        return $this->document_types_model->update($data, $id);
    }
        /**
     * Get contract types data for chart
     * @return array
     */
    public function get_contracts_types_chart_data()
    {
        return $this->document_types_model->get_chart_data();
    }

    public function get_in_types_chart_data()
    {
        return $this->document_types_model->get_in_chart_data();
    }
    public function get_out_types_chart_data()
    {
        return $this->document_types_model->get_in_chart_data();
    }
     /**
     * Get contract types values for chart
     * @return array
     */
    public function get_contracts_types_values_chart_data()
    {
        return $this->document_types_model->get_values_chart_data();
    }
    /**
     * Get Attached documents
     * @return array
     */

    public function get_attached_documents($rel_id='')
    {
		if($rel_id!='')
			 $this->db->where('rel_id',$rel_id);
        $this->db->where('rel_type','document');
        return $this->db->get('tblfiles')->result_array();
    }

      public function get_attached_documents_in()
    {
        $this->db->where('rel_type','documents_in');
        return $this->db->get('tblfiles')->result_array();
    }

     public function get_attached_documents_out()
    {
        $this->db->where('rel_type','documents_out');
        return $this->db->get('tblfiles')->result_array();
    }
	
  	public function approve_document($document_id)
    {
		$original_document = $this->db->get_where('tbldocuments_in',array('id'=>$document_id))->row();
        $this->db->where('id',$document_id);
        $adata['approved_by'] = get_staff_user_id();
		 $adata['is_approve'] =1;
        $adata['approved_date'] = date('Y-m-d H:i:s');
        $this->db->update('tbldocuments_in',$adata);
        if($this->db->affected_rows() > 0 ){
			$this->db->where('id',$original_document->safe_documentid);
            $ddata['document_inout'] =1;
       // $adata['approved_date'] = date('Y-m-d H:i:s');
        $this->db->update('tbldocuments',$ddata);
			                if ($original_document->sent_by!=0) {
					
					// $accounts_team = $this->staff_model->get('',['active'=>1,'is_account'=>1]);
      //  foreach ($accounts_team as  $accountant) {
            $assigned = $original_document->assigned_to;
            if ((!empty($assigned) && $assigned != 0)) {
			  $notification_data = [
                   'fromcompany'     => true,
                            'touserid'        => $assigned,
                            'description'     => get_staff_full_name($original_document->sent_by).'not_document_request_approved',
                            'link'            => 'documents/document_in/' . $document_id,
                            'additional_data' => serialize([
                                get_safedocumentname($original_document->safe_documentid)]),
                ];

                if (add_notification($notification_data)) {
                    pusher_trigger_notification($assigned);
                }

               /* $this->db->select('email');
                $this->db->where('staffid', $assigned);
                $email = $this->db->get(db_prefix() . 'staff')->row()->email;
				send_mail_template('proposal_accepted_to_staff', $original_proposal, $email);*/
                    }
	//	}
				}
           
           return true; 
        }
        return false;
    }


     /**
     * Add new mode of correspondence
     * @param mixed $data All $_POST data
     */
    public function add_mode_of_msg($data)
    {
        $this->db->insert(db_prefix().'mode_of_msg', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Mode of Correspondence Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Edit mode of correspondence
     * @param mixed $data All $_POST data
     * @param mixed $id mode of correspondence id
     */
    public function update_mode_of_msg($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'mode_of_msg', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Mode of Correspondence Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

       /**
     * @param  integer ID
     * @return mixed
     * Delete mode of correspondence from database, if used return array with key referenced
     */
    public function delete_mode_of_msg($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'mode_of_msg');
        if ($this->db->affected_rows() > 0) {
            log_activity('Mode of Correspondence Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    	/* get mode of correspondence */
	public function get_mode_of_msg($id = '')
    {
		 if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'mode_of_msg')->row();
        }
        else{
            return $this->db->get(db_prefix().'mode_of_msg')->result_array();
           
        }

        
       
    }
	/* get mode of correspondence */


/**
     * Get contract renewals
     * @param  mixed $id document id
     * @return array
     */
    public function get_document_otherparty($id)
    {
        $this->db->where('document_id', $id);

        return $this->db->get('tbldocuments_other_party')->result_array();
    }
}
