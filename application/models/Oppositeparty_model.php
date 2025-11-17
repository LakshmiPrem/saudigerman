<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Oppositeparty_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Add new contract type
    * @param mixed $data All $_POST data
    */
    public function add($data)
    {
        $data['addedfrom'] = get_staff_user_id();
        $data['dateadded'] = date('Y-m-d H:i:s');
		$data['trade_commence_date']= isset($data['trade_commence_date']) ? to_sql_date($data['trade_commence_date']) : '';
		$data['trade_expiry']= isset($data['trade_expiry']) ?  to_sql_date($data['trade_expiry']) : '' ;
		if(isset($data['profile_image']))
			$data['profile_date'] = date('Y-m-d H:i:s');
		if(isset($data['current_maplocation']))
			$data['locationmap_dt'] = date('Y-m-d H:i:s');
        $this->db->insert('tbloppositeparty', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('Opposite Party Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Edit contract type
     * @param mixed $data All $_POST data
     * @param mixed $id Contract type id
     */
    public function update($data, $id)
    {
		$data['trade_commence_date']=to_sql_date($data['trade_commence_date']);
		$data['trade_expiry']=to_sql_date($data['trade_expiry']);
		if(isset($data['profile_image']))
			$data['profile_date'] = date('Y-m-d H:i:s');
		if(isset($data['current_maplocation']))
			$data['locationmap_dt'] = date('Y-m-d H:i:s');
        $this->db->where('id', $id);
        $this->db->update('tbloppositeparty', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Opposite Party Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get($id = '',$where=[])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tbloppositeparty')->row();
        }
	  if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $types = $this->db->get('tbloppositeparty')->result_array();
        return $types;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('opposite_party', db_prefix() . 'projects', $id)) {
            return array(
                'referenced' => true,
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tbloppositeparty');
        if ($this->db->affected_rows() > 0) {
            log_activity('Opposite Party  Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

     public function delete_attachment($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblfiles')->row();
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_by_type('oppositeparty') . $attachment->rel_id . '/';
                $fullPath =$relPath.$attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath.$fname.'_thumb.'.$fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                /*$this->db->where('file_id', $id);
                $this->db->delete('tblcustomerfiles_shares');*/
                logActivity('Opposite Party Attachment Deleted [ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('oppositeparty') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('oppositeparty') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('oppositeparty') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }
	
	public function fetch_details($q,$limit, $start,$where=[])
    {
        $have_permission_customers_view = has_permission('projects', '', 'view');
        if ( $have_permission_customers_view) {

            // Clients
             $this->db->select('*');

            $this->db->from(db_prefix() . 'oppositeparty');

            $this->db->where('(name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'oppositeparty.mobile LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR firstname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR lastname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(firstname, \' \', lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(lastname, \' \', firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');

        if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }

        $this->db->limit($limit, $start);
        $this->db->order_by('name', 'asc');

        $clients = $this->db->get()->result_array();
        //$cl = $clients[0];
        $res = '';
        foreach ($clients as $client_) {
            $number_cases = total_rows('tblcontracts',array('other_party'=>$client_['id']));
                  
            $res .=  ' <a href="'.admin_url('opposite_parties/opposite_party/'.$client_['id']).'" >
                        <div class="col-sm-3">
                            <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(255, 0, 0, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 168px;"  >
                                <div class="card-body">
                                    <h5 class="card-title" ><strong>'.$client_['name'].'</strong></h5>
                                    <p class="card-text" style="margin: 0 0 4px;"><b>'._l('city').' :</b>'.$client_['city'].'</p>
                                    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('client_phonenumber').':</b>'.$client_['mobile'].'</p>
                                    <p class="card-text" style="margin:  0 0 4px;"><b>'._l('email').':</b>'.$client_['email'].'</p>';
                              $res .= '<p class="card-text" style="margin:  0 0 4px;"> <a href="'.admin_url('opposite_parties/opposite_party/'.$client_['id'].'?group=contracts').'" title="Number Of Cases" class="btn btn-info" style="border-radius: 12px;">'._l('contracts').': <b>'.$number_cases.'</b></a></p>
                                </div>
                            </div>
                        </div></a>';

               
        }

        return $res;
        }
    }

	
	     public function fetch_details_numrows($q,$where=[])
    {
        $have_permission_customers_view = has_permission('projects', '', 'view');
        if ( $have_permission_customers_view) {

            // Clients
            $this->db->select('*');

            $this->db->from(db_prefix() . 'oppositeparty');

            $this->db->where('(name LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR ' . db_prefix() . 'oppositeparty.mobile LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR firstname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR lastname LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR address LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR email LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(firstname, \' \', lastname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                OR CONCAT(lastname, \' \', firstname) LIKE "%' . $this->db->escape_like_str($q) . '%" ESCAPE \'!\'
                )');

            if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
                $this->db->where($where);
            }

            $this->db->order_by('name', 'asc');

            return $this->db->get()->num_rows();
        }
    }
	/**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_opponent_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'oppositeparty', [
            'active' => $status,
        ]);

        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('client_status_changed', [
                'id'     => $id,
                'status' => $status,
            ]);

            log_activity('Opponent Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }
	
	/*
	Defendant /Authorized signatory Details
	*/
	public function get_contacts($customer_id = '', $where = ['active' => 1])
    {
        $this->db->where($where);
        if ($customer_id != '') {
            $this->db->where('opposite_id', $customer_id);
        }

        $this->db->order_by('id', 'DESC');

        return $this->db->get(db_prefix() . 'oppsitecontacts')->result_array();
    }
function add_defendar($data ,$defaulter_id){
       
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedby'] = get_staff_user_id();
      $data['opposite_id']=$defaulter_id;
           unset($data['customer_id']);
	if( isset($data['id_expiry']))
		$data['id_expiry']=to_sql_date($data['id_expiry']);
	if( isset($data['passport_expiry']))
		$data['passport_expiry']=to_sql_date($data['passport_expiry']);
        $this->db->insert('tbloppsitecontacts',$data);
        $a = $this->db->error();
        $insert_id = $this->db->insert_id();
	
	/*	if($data['installment_status']=='paid'){
		$all_members = $this->get_project_members($defaulter_id);
            foreach ($all_members as $member) {
               // if (in_array($data['staff_id'], $new_project_members_to_receive_email)) {
                  //  send_mail_template('project_installment_paid', $edata, $id, $client_id);
				 send_mail_template('project_installment_paid_to_staff', $member['email'], $member['staff_id'],  $member['project_id']);
              //  }
            }
		}*/
		return $insert_id;
    }
	public function update_defendar($data,$id){

        $this->db->where('id',$id);
        $this->db->update('tbloppsitecontacts',$data);
		$recovery_id=$this->db->get_where('tblrecoveries_installments',array('id'=>$id))->row()->recovery_id;
        if ($this->db->affected_rows() > 0) {
		
            return true;
        }

        return false;
    }
	 public function delete_defendar($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete('tbloppsitecontacts');
        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }
	 public function get_defendar($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tbloppsitecontacts')->row();
    }


}
