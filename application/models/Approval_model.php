<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Approval_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get tax by id
     * @param  mixed $id tax id
     * @return mixed     if id passed return object else array
     */
    public function get($id = '',$where=[])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'approval_headings')->row();
        }
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $this->db->order_by('id', 'ASC');

        return $this->db->get(db_prefix() . 'approval_headings')->result_array();
    }
	
	/*get approvals  *****/
	 public function getapprovaldata($id = '',$where=[])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix() . 'approvals')->row();
        }
		if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $this->db->order_by('id', 'ASC');

        return $this->db->get(db_prefix() . 'approvals')->result_array();
    }
	
	/*get approvals groupby key*/
	public function getapprovalsbykey($rel_type,$rel_id){
		$this->db->where('rel_type',$rel_type);
		$this->db->where('rel_id',$rel_id);
	//	$this->db->group_by('approval_key');
		return $this->db->get(db_prefix() . 'approvals')->result_array();
	}
	 /**
     * Edit contract type
     * @param mixed $data All $_POST data
     * @param mixed $id Contract type id
     */
    public function updateapproval($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblapprovals', $data);
        if ($this->db->affected_rows() > 0) {
          

            return true;
        }

        return false;
    }

    /**
    * Add new contract type
    * @param mixed $data All $_POST data
    */
    public function add($data)
    {
        $this->db->insert('tblapproval_headings', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Approval Heading Added [' . $data['name'] . ']');

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
        $this->db->where('id', $id);
        $this->db->update('tblapproval_headings', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Approval Heading Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function getactive($id = '')
    {
		 $this->db->where('active', '1');
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblapproval_headings')->row();
        }
        $types = $this->db->get('tblapproval_headings')->result_array();
        return $types;
    }
	 /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete($id)
    {
		
        if (is_reference_in_table('document_type', 'tblproject_files', $id)) {
            return array(
                'referenced' => true,
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblapproval_headings');
        if ($this->db->affected_rows() > 0) {
            log_activity('Document Type Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
}
