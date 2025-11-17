<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Notice_types_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Add new notice type
    * @param mixed $data All $_POST data
    */
    public function add($data)
    {
        $this->db->insert(db_prefix().'notices_types', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            log_activity('New notice Type Added [' . $data['name'] . ']');

            return $insert_id;
        }

        return false;
    }

    /**
     * Edit notice type
     * @param mixed $data All $_POST data
     * @param mixed $id notice type id
     */
    public function update($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix().'notices_types', $data);
        if ($this->db->affected_rows() > 0) {
            log_activity('notice Type Updated [' . $data['name'] . ', ID:' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get notice type object based on passed id if not passed id return array of all types
     */
    public function get($id = '')
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get(db_prefix().'notices_types')->row();
        }

        $types = $this->app_object_cache->get('notice-types');

        if (!$types && !is_array($types)) {
            $types = $this->db->get(db_prefix().'notices_types')->result_array();
            $this->app_object_cache->add('notice-types', $types);
        }

        return $types;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete notice type from database, if used return array with key referenced
     */
    public function delete($id)
    {
        if (is_reference_in_table('notice_type', db_prefix().'notices', $id)) {
            return [
                'referenced' => true,
            ];
        }
        $this->db->where('id', $id);
        $this->db->delete(db_prefix().'notices_types');
        if ($this->db->affected_rows() > 0) {
            log_activity('notice Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Get notice types data for chart
     * @return array
     */
    public function get_chart_data()
    {
        $labels = [];
        $totals = [];
        $types  = $this->get();
        foreach ($types as $type) {
            $total_rows_where = [
                'notice_type' => $type['id'],
                'trash'         => 0,
            ];
            if (is_client_logged_in()) {
                $total_rows_where['client']                = get_client_user_id();
                $total_rows_where['not_visible_to_client'] = 0;
            } else {
                if (!has_permission('notices', '', 'view')) {
                    $total_rows_where['addedfrom'] = get_staff_user_id();
                }
            }
            $total_rows = total_rows(db_prefix().'notices', $total_rows_where);
            if ($total_rows == 0 && is_client_logged_in()) {
                continue;
            }
            array_push($labels, $type['name']);
            array_push($totals, $total_rows);
        }
        $chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => _l('notice_summary_by_type'),
                    'backgroundColor' => 'rgba(3,169,244,0.2)',
                    'borderColor'     => '#03a9f4',
                    'borderWidth'     => 1,
                    'data'            => $totals,
                ],
            ],
        ];

        return $chart;
    }

    /**
     * Get notice types values for chart
     * @return array
     */
    public function get_values_chart_data()
    {
        $labels = [];
        $totals = [];
        $types  = $this->get();
        foreach ($types as $type) {
            array_push($labels, $type['name']);

            $where = [
                'where' => [
                    'notice_type' => $type['id'],
                    'trash'         => 0,
                ],
                'field' => 'notice_value',
            ];

            if (!has_permission('notices', '', 'view')) {
                $where['where']['addedfrom'] = get_staff_user_id();
            }

            $total = sum_from_table(db_prefix().'notices', $where);
            if ($total == null) {
                $total = 0;
            }
            array_push($totals, $total);
        }
        $chart = [
            'labels'   => $labels,
            'datasets' => [
                [
                    'label'           => _l('notice_summary_by_type_value'),
                    'backgroundColor' => 'rgba(37,155,35,0.2)',
                    'borderColor'     => '#84c529',
                    'tension'         => false,
                    'borderWidth'     => 1,
                    'data'            => $totals,
                ],
            ],
        ];

        return $chart;
    }
}
