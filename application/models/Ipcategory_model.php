<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Ipcategory_model extends App_Model
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
        $this->db->insert('tblip_categories', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New IP Category  Added [' . $data['name'] . ']');

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
        $this->db->update('tblip_categories', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('IP Category  Updated [' . $data['name'] . ', ID:' . $id . ']');

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
		 $this->db->where('active', '1');
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('tblip_categories')->row();
        }
        $this->db->order_by('name', 'asc');
        $types = $this->db->get('tblip_categories')->result_array();
        return $types;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('ip_category', db_prefix() . 'projects', $id)) {
            return array(
                'referenced' => true,
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblip_categories');
        if ($this->db->affected_rows() > 0) {
            log_activity('IP Category Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
    * Add new contract type
    * @param mixed $data All $_POST data
    */
    public function add_subcategory($data)
    {
        $this->db->insert('tblip_subcategory', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New IP Sub Category  Added [' . $data['subcategory_name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Edit contract type
     * @param mixed $data All $_POST data
     * @param mixed $id Contract type id
     */
    public function update_subcategory($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblip_subcategory', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('IP Sub Category  Updated [' . $data['subcategory_name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get_subcategory($id = '',$where=['active'=>1])
    {
		 $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            return $this->db->get('ip_subcategory')->row();
        }
        $this->db->order_by('subcategory_name', 'asc');
        $types = $this->db->get('ip_subcategory')->result_array();
        return $types;
    }
	

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete_subcategory($id)
    {
        if (is_reference_in_table('ip_subcategory', db_prefix() . 'projects', $id)) {
            return array(
                'referenced' => true,
            );
        }
        $this->db->where('id', $id);
        $this->db->delete('tblip_subcategory');
        if ($this->db->affected_rows() > 0) {
            log_activity('IP Sub Category Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
 public function get_ipsubcategory($id = '')
    {
        $this->db->select('tblip_subcategory.*,name as category_name,tblip_subcategory.id as id');
        $this->db->join('tblip_categories','tblip_categories.id = tblip_subcategory.category_id','inner');
        if (is_numeric($id)) {
            $this->db->where('tblip_subcategory.id', $id);

            return $this->db->get(db_prefix() . 'ip_subcategory')->row();
        }
        $this->db->order_by('subcategory_name', 'asc');

        return $this->db->get(db_prefix() . 'ip_subcategory')->result_array();
    }
}
