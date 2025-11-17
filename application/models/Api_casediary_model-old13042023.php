<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Api_casediary_model extends APP_Model
{
    private $project_settings;

    public function __construct()
    {
        parent::__construct();

        $project_settings       = array(
            'available_features',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'comment_on_tasks',
            'view_task_comments',
            'view_task_attachments',
            'view_task_checklist_items',
            'upload_on_tasks',
            'view_task_total_logged_time',
            'view_finance_overview',
            'upload_files',
            'open_discussions',
            'view_milestones',
            'view_gantt',
            'view_timesheets',
            'view_activity_log',
            'view_team_members',
            'hide_tasks_on_main_tasks_table',
        );
        //$this->load->model('litigation_model');
        //$this->load->model('hallnumber_model');
       // $this->load->model('casestatus_model');
       // $this->load->model('partytype_model');
       // $this->load->model('area_description_model');
        //$this->load->model('oppositeparty_model');
       // $this->load->model('lawyer_attending_model');
        $this->project_settings = '';//do_action('project_settings', $project_settings);
    }

    
    function create_token ($customer_id) {
        $this->load->database();

        // ***** Generate Token *****
        $char = "bcdfghjkmnpqrstvzBCDFGHJKLMNPQRSTVWXZaeiouyAEIOUY!@#%";
        $token = '';
        for ($i = 0; $i < 47; $i++) $token .= $char[(rand() % strlen($char))];

        // ***** Insert into Database *****
        //$sql = "INSERT INTO api_tokens SET `token` = ?, customer_id = ?;";
        //$this->db->query($sql, [$token, $customer_id];
        $this->db->where('staffid',$customer_id);
        $this->db->update('tblstaff',array('auth_token'=>$token));    
        //return array('http_code' => 200, 'token' => $token);
        return $token;
    }   

    public function get($client_id,$where = array())
    {
        
        $this->db->select('tblprojects.id,tblprojects.name,tblclients.company as client');
        $this->db->from('tblprojects');
        $this->db->join('tblclients', 'tblclients.userid=tblprojects.clientid');
        $this->db->join('tblproject_members', 'tblproject_members.project_id=tblprojects.id','left');
        $staff_id = get_staff_user_id();
        $this->db->where('tblproject_members.staff_id',$staff_id);
        $this->db->where('tblprojects.clientid',$client_id);
        $this->db->order_by('tblprojects.name', 'ASC');

        return $this->db->get()->result_array();
    }

    public function _search_projects($q, $limit = 0, $where = false, $rel_type = null, $api = false)
    {
        $result = [
            'result'         => [],
            'type'           => 'projects',
            'search_heading' => _l('projects'),
        ];

        $projects = has_permission('projects', '', 'view');
        // Projects
        $this->db->select('tblprojects.*');
        $this->db->from('tblprojects');
        
        $this->db->join('tblclients', 'tblclients.userid = tblprojects.clientid','LEFT'); 
           
        if ($where != false) {
            $this->db->where($where);
        }
        if (!_startsWith($q, '#')) {
            $this->db->where('(tblclients.company LIKE "%' . $q . '%"
                OR tblprojects.case_number LIKE "%' . $q . '%"
                OR tblprojects.name LIKE "%' . $q . '%"
                )');
        } else {
            $this->db->where('id IN
                (SELECT rel_id FROM tbltags_in WHERE tag_id IN
                (SELECT id FROM tbltags WHERE name="' . strafter($q, '#') . '")
                AND tbltags_in.rel_type=\'projects\' GROUP BY rel_id HAVING COUNT(tag_id) = 1)
                ');
        }

        if ($limit != 0) {
            $this->db->limit($limit);
        }

        $this->db->order_by(db_prefix() . 'projects.name', 'ASC');
        $result['result'] = $this->db->get()->result_array();

        return $result;
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
        $data['case_id'] = intval($data['project_id']);
        $data['date'] = to_sql_date($data['date'],true);
        //$data['mode'] = 'Email';

        unset($data['project_id']);

        $this->db->insert('tblcase_communication_center', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            
            return $insert_id;
        }

        return false;
    }

     public function get_clients($id = '', $where = array())
    {
        $this->db->select('tblclients.userid as id,tblclients.company as name');

        $this->db->join('tblcountries', 'tblcountries.country_id = tblclients.country', 'left');
        $this->db->join('tblcontacts', 'tblcontacts.userid = tblclients.userid AND is_primary = 1', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblclients.userid', $id);
            $client = $this->db->get('tblclients')->row();

            if (get_option('company_requires_vat_number_field') == 0) {
                $client->vat = null;
            }

            return $client;
        }

        $this->db->order_by('company', 'asc');

        return $this->db->get('tblclients')->result_array();
    }


    public function get_communication_files($communication_id)
    {
        $this->db->select('file_name');
        $this->db->where('communication_id', $communication_id);
        $s = $this->db->get('tblproject_files')->result_array();
        return $s;
    }

}