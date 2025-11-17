<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Designations_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param  integer ID (optional)
     * @param  boolean (optional)
     * @return mixed
     * Get designation object based on passed id if not passed id return array of all designations
     * Second parameter is to check if the request is coming from clientarea, so if any designations are hidden from client to exclude
     */
    public function get($id = false, $clientarea = false)
    {
        
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tbldesignations')->row();
        }

        $designations = $this->db->get('tbldesignations')->result_array();
        return $designations;
    }

    /**
     * @param array $_POST data
     * @return integer
     * Add new department
     */
    public function add($data)
    {
       

        $this->db->insert('tbldesignations', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Designation Added [' . $data['name'] . ', ID: ' . $insert_id . ']');
        }

        return $insert_id;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update department to database
     */
    public function update($data, $id)
    {
    
        $this->db->where('id', $id);
        $this->db->update('tbldesignations', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Designation Updated [Name: ' . $data['name'] . ', ID: ' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete department from database, if used return array with key referenced
     */
    public function delete($id)
    {
        /*$id      = do_action('before_delete_department', $id);
        $current = $this->get($id);
        if (is_reference_in_table('designation', 'tblstaff', $id)) {
            return array(
                'referenced' => true
            );
        }
        do_action('before_department_deleted', $id);*/
        $this->db->where('id', $id);
        $this->db->delete('tbldesignations');
        if ($this->db->affected_rows() > 0) {
            log_activity('Designation Deleted [ID: ' . $id . ']');

            return true;
        }

        return false;
    }

}
