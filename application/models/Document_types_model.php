<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Document_types_model extends App_Model
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
        $this->db->insert('tbldocument_types', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New Document Type Added [' . $data['name'] . ']');

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
        $this->db->update('tbldocument_types', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('Document Type Updated [' . $data['name'] . ', ID:' . $id . ']');

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

            return $this->db->get('tbldocument_types')->row();
        }
        $types = $this->db->get('tbldocument_types')->result_array();
        return $types;
    }
	public function getprojecttypes($id = '')
    {
		 $this->db->where('active', '1');
		 $this->db->where('category NOT IN (6,7)');
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tbldocument_types')->row();
        }
        $types = $this->db->get('tbldocument_types')->result_array();
        return $types;
    }
	public function getbycategory($cid = '')
    {
		 $this->db->where('active', '1');
       // if (is_numeric($id)) {
            $this->db->where('category', $cid);

        //    return $this->db->get('tbldocument_types')->row();
       // }
        $types = $this->db->get('tbldocument_types')->result_array();
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
        $this->db->delete('tbldocument_types');
        if ($this->db->affected_rows() > 0) {
            log_activity('Document Type Deleted [' . $id . ']');

            return true;
        }

        return false;
    }
      /**
     * Get contract types data for chart
     * @return array
     */
    public function get_chart_data()
    {
        $labels = array();
        $totals = array();
        $types  = $this->get();
        foreach ($types as $type) {
            $total_rows_where = array(
                'contract_type' => $type['id'],
                'trash' => 0,
            );
            if (is_client_logged_in()) {
                $total_rows_where['client']                = get_client_user_id();
                $total_rows_where['not_visible_to_client'] = 0;
            } else {
                if (!has_permission('documents', '', 'view')) {
                    $total_rows_where['addedfrom'] = get_staff_user_id();
                }
            }
            $total_rows = total_rows('tbldocuments', $total_rows_where);
            if ($total_rows == 0 && is_client_logged_in()) {
                continue;
            }
            array_push($labels, $type['name']);
            array_push($totals, $total_rows);
        }
        $chart = array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => _l('contract_summary_by_type'),
                    'backgroundColor' => 'rgba(3,169,244,0.2)',
                    'borderColor' => "#03a9f4",
                    'borderWidth' => 1,
                    'data' => $totals,
                ),
            ),
        );

        return $chart;
    }

    /**
     * Get contract types data for chart
     * @return array
     */
    public function get_in_chart_data()
    {
        $labels = array();
        $totals = array();
        $types  = $this->get();
        foreach ($types as $type) {
            $total_rows_where = array(
                'document_type' => $type['id'],
                'trash' => 0,
            );
            if (is_client_logged_in()) {
                $total_rows_where['client']                = get_client_user_id();
                $total_rows_where['not_visible_to_client'] = 0;
            } else {
                if (!has_permission('documents', '', 'view')) {
                    $total_rows_where['addedfrom'] = get_staff_user_id();
                }
            }
            $total_rows = total_rows('tbldocuments_in', $total_rows_where);
            if ($total_rows == 0 && is_client_logged_in()) {
                continue;
            }
            array_push($labels, $type['name']);
            array_push($totals, $total_rows);
        }
        $chart = array(
            'labels' => $labels,
            'datasets' => array(
                array(
                    'label' => _l('documents_summary_by_type'),
                    'backgroundColor' => 'rgba(3,169,244,0.2)',
                    'borderColor' => "#03a9f4",
                    'borderWidth' => 1,
                    'data' => $totals,
                ),
            ),
        );

        return $chart;
    }
}
