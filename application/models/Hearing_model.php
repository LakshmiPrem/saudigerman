<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hearing_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('court_degree_model');
        $this->load->model('hearing_reference_model');
        $this->load->model('court_region_model');
        $this->load->model('hallnumber_model');

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
        $this->db->select('*,tblhearings.id as id,tblclients.company as client_name,tbloppositeparty.name as opposite_party_name,tblprojects.name as case_name,tblclients.userid as client');
        $this->db->join('tblprojects', 'tblprojects.id = tblhearings.project_id','left');
        //$this->db->join('tblcase_details', 'tblcase_details.project_id = tblprojects.id','left');
        $this->db->join('tbloppositeparty', 'tbloppositeparty.id = tblprojects.opposite_party', 'left'); 
        $this->db->join('tblclients', 'tblclients.userid = tblprojects.clientid', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblhearings.id', $id);
            $contract = $this->db->get('tblhearings')->row();

            $contract->attachments = $this->get_hearing_files($id, $contract->project_id);


            return $contract;
        }
        $contracts = $this->db->get('tblhearings')->result_array();
        $i         = 0;
        foreach ($contracts as $contract) {
            $contracts[$i]['attachments'] = $this->get_hearing_files($contract['id'],$contract['project_id']);
            $i++;
        }

        return $contracts;
    }
    public function getold($id = '', $where = array(), $for_editor = false)
    {
        $this->db->select('*,tblhearings.id as id,tblclients.company as client_name,tbloppositeparty.name as opposite_party_name,tblprojects.name as case_name,tblclients.userid as client');
        $this->db->join('tblprojects', 'tblprojects.id = tblhearings.project_id','left');
        $this->db->join('tblcase_details', 'tblcase_details.project_id = tblprojects.id','left');
        $this->db->join('tbloppositeparty', 'tbloppositeparty.id = tblprojects.opposite_party', 'left'); 
        $this->db->join('tblclients', 'tblclients.userid = tblprojects.clientid', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblhearings.id', $id);
            $contract = $this->db->get('tblhearings')->row();

            $contract->attachments = $this->get_hearing_files($id, $contract->project_id);


            return $contract;
        }
        $contracts = $this->db->get('tblhearings')->result_array();
        $i         = 0;
        foreach ($contracts as $contract) {
            $contracts[$i]['attachments'] = $this->get_hearing_files($contract['id'],$contract['project_id']);
            $i++;
        }

        return $contracts;
    }

    public function get_hearings_by_project_id($id = '', $where = array(), $for_editor = false)
    {
        $this->db->select('tblhearings.*,tblhearings.id as id,tblclients.company as client_name,tbloppositeparty.name as opposite_party_name');
        
        $this->db->join('tblprojects', 'tblprojects.id = tblhearings.project_id','left');
        $this->db->join('tblclients', 'tblclients.userid = tblprojects.clientid', 'left');
		$this->db->join('tbloppositeparty', 'tbloppositeparty.id = tblprojects.opposite_party', 'left');
		
        
        $this->db->where($where);

        $this->db->where('tblhearings.project_id', $id);
        $contracts = $this->db->get('tblhearings')->result();
        $i         = 0;
        foreach ($contracts as $contract) {
            $contract->attachments = $this->get_hearing_files($contract->id,$id);
            $i++;
        }
        return $contracts;
    }


     public function get_hearing_files($hearngid,$project_id)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('project_id', $project_id);
        $this->db->where('hearing_id', $hearngid);

        return $this->db->get('tblproject_files')->result_array();
    }


    /**
     * Select unique contracts years
     * @return array
     */
    public function get_hearing_years()
    {
        return $this->db->query('SELECT DISTINCT(YEAR(hearing_date)) as year FROM tblhearings')->result_array();
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
        $data['hearing_date'] = to_sql_date($data['hearing_date'], true);
        $this->db->insert('tblhearings', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
                 log_activity('New Hearing Added [' . $data['subject'] . ']');
		
 $this->projects_model->log_activity($data['project_id'], 'project_activity_hearing_added', $data['hearing_date'].'-'._l(get_court_instance_name_by_id($data['h_instance_id'])));
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
        $hearing_sts = $this->get($id);
       if(!empty($data['postponed_until'])){
            if($hearing_sts->postponed =='n'){
                $add_data = $data;    
                $add_data['hearing_date']  = $data['postponed_until'];
				$add_data['mention_hearing']  = $data['next_hearing_mention'];
                unset($add_data['postponed_until']);
                unset($add_data['proceedings']); 
                unset($add_data['comments']);
				unset($add_data['next_hearing_mention']);
                $data['postponed_until'] =  to_sql_date($add_data['hearing_date'],true);
                $data['postponed']   =  'y'; 

                $this->add($add_data);
            }else{
				   $data['postponed_until'] =  to_sql_date($data['postponed_until'],true);
			}
        }
        $data['hearing_date'] = to_sql_date($data['hearing_date'],true);
        $this->db->where('id', $id);
        $this->db->update('tblhearings', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Hearing Updated [' . $data['subject'] . ']');
			$this->projects_model->log_activity($data['project_id'], 'project_activity_hearing_updated', $data['hearing_date'].'-'._l(get_court_instance_name_by_id($data['h_instance_id'])));
            return true;
        }

        return false;
    }

    public function copy($id)
    {
        $contract = $this->get($id, array(), true);
        $fields = $this->db->list_fields('tblhearings');
        $newContactData = array();

        foreach ($fields as $field) {
            if (isset($contract->$field)) {
                $newContactData[$field] = $contract->$field;
            }
        }

        unset($newContactData['id']);

        //$newContactData['trash'] = 0;
        //$newContactData['isexpirynotified'] = 0;
        //$newContactData['hearing_date'] = _d(date('Y-m-d'));

       /* if ($contract->dateend) {
            $dStart                    = new DateTime($contract->datestart);
            $dEnd                      = new DateTime($contract->dateend);
            $dDiff                     = $dStart->diff($dEnd);
            $newContactData['dateend'] = _d(date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY')))));
        } else {
            $newContactData['dateend'] = '';
        }*/

        $newId = $this->add($newContactData);

        if ($newId) {
            $custom_fields = get_custom_fields('hearings');
            foreach ($custom_fields as $field) {
                $value = get_custom_field_value($id, $field['id'], 'hearings');
                if ($value != '') {
                    $this->db->insert('tblcustomfieldsvalues', array(
                    'relid' => $newId,
                    'fieldid' => $field['id'],
                    'fieldto' => 'hearings',
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
        $this->db->delete('tblhearings');
        if ($this->db->affected_rows() > 0) {
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'hearings');
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'hearings');
            $this->db->delete('tblfiles');
            //$attachments = $this->db->get('tblfiles')->result_array();
            //foreach ($attachments as $attachment) {
                //$this->delete_hearing_attachment($attachment['id']);
           // }
            
            log_activity('Hearing Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

     public function delete_hearing_attachment($attachment_id)
    {
        $deleted    = false;
        $attachment = $this->get_contract_attachments($attachment_id);

        if ($attachment) {
            if (empty($attachment->external)) {
                unlink(get_upload_path_by_type('hearing') . $attachment->rel_id . '/' . $attachment->file_name);
            }
            $this->db->where('id', $attachment->id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                logActivity('Contract Attachment Deleted [ContractID: ' . $attachment->rel_id . ']');
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



    public function get_court_degrees($id = '')
    {
        return $this->court_degree_model->get($id);
    }
    public function get_hearinig_references($id = '')
    {
        return $this->hearing_reference_model->get($id);
    }
    public function get_court_regions($id = '')
    {
        return $this->court_region_model->get($id);
	}
    public function get_hallnumbers($id = '')
    {
        return $this->hallnumber_model->get($id);
    }

     public function send_hearing_to_email($id, $content = '', $attachpdf = true, $cc = '',$project_id='')
    {
        $this->load->model('emails_model');

        //$this->emails_model->set_rel_id($id);
       //$this->emails_model->set_rel_type('hearing');

        $proposal = $this->get($id);
        

        if ($attachpdf) {
            $pdf    = hearing_notice_pdf($proposal);
            $attach = $pdf->Output(slug_it($proposal->subject) . '.pdf', 'S');
            $this->emails_model->add_attachment(array(
                'attachment' => $attach,
                'filename' => slug_it($proposal->subject) . '.pdf',
                'type' => 'application/pdf',
            ));
        }
        $proposal->email = $cc;
        $merge_fields = array();
        //$merge_fields = array_merge($merge_fields, get_hearing_merge_fields($proposal->id));
        //$sent         = $this->emails_model->send_email_template($template, $proposal->email, $merge_fields, '', $cc);

        $sent         = $this->emails_model->send_simple_email($proposal->email,'Hearing Notice',$content);

        if ($sent) {

            /*// Set to status sent
            $this->db->where('id', $id);
            $this->db->update('tblproposals', array(
                'status' => 4,
            ));

            do_action('proposal_sent', $id);*/

            return true;
        }

        return false;
    }
	  public function get_hearing_bystage($id = '', $where = array())
    {
        $this->db->select('tblhearings.*,tblhearings.id as id,court_no');
      //  $this->db->join('tblprojects', 'tblprojects.id = tblhearings.project_id','left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblhearings.id', $id);
            $hearings = $this->db->get('tblhearings')->row();
            return $hearings;
        }
		  $this->db->order_by('tblhearings.id', 'desc');
        $hearings = $this->db->get('tblhearings')->result_array();
        return $hearings;
    }
}
