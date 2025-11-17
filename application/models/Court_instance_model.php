<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Court_instance_model extends App_Model
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
        $insert_data['instance_name'] = $data['name'];
        $insert_data['instance_slug'] = create_slug($data['name']);
		 $insert_data['other_name'] = $data['other_name'];
        $this->db->insert('tblproject_instances', $insert_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Court Instance Added [' . $data['name'] . ']');
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
        $update_data['instance_name'] = $data['name'];
        $update_data['instance_slug'] = create_slug($data['name']);
		$update_data['other_name'] = $data['other_name'];
        $this->db->where('id', $id);
        $this->db->update('tblproject_instances', $update_data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Court Instance  Updated [' . $data['name'] . ', ID:' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get($id = '',$type='')
    {
        $this->db->where('active', '1');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblproject_instances')->row();
        }
		if($type!=''){
			 $this->db->where('type', $type);
		}
        $types = $this->db->get('tblproject_instances')->result_array();
        return $types;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('instance_id', db_prefix() . 'case_details', $id)) {
            return array(
                'referenced' => true,
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblproject_instances');
        if ($this->db->affected_rows() > 0) {
            log_activity('Court Instance Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

   
}
