<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Partytype_model extends App_Model
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
        $this->db->insert('tblpartytypes', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Party Type  Added [' . $data['name'] . ']');

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
        $this->db->update('tblpartytypes', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Party Type Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblpartytypes')->row();
        }
        $types = $this->db->get('tblpartytypes')->result_array();
        return $types;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete($id)
    {
       
        $this->db->where('id', $id);
        $this->db->delete('tblpartytypes');
        if ($this->db->affected_rows() > 0) {
            log_activity('Party Type Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

   
}
