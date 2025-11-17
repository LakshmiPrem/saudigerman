<?php
defined('BASEPATH') or exit('No direct script access allowed');

class App_home extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('dashboard_model');
    }

    /* This is admin dashboard view */
    public function index()
    {
        close_setup_menu();
        $data['title'] = get_option('companyname');
        $data['conracts_count_active']           = count_active_contracts();
        $data['count_expired']          = count_expired_contracts();
        $data['contracts_count_recently_created'] = count_recently_created_contracts();
		 $this->load->model('tickets_model');

        $data['statuses']                       = $this->tickets_model->get_ticket_status();
        $data['statuses']['callback_translate'] = 'ticket_status_translate';
        $data['legal_approval_awaits'] = $this->db->where('rel_type','ticket')->where('staffid',get_staff_user_id())->where('approval_status',2)->count_all_results('tblapprovals');
        $data['contract_approval_awaits'] = $this->db->where('rel_type','contract')->where('staffid',get_staff_user_id())->where('approval_status',2)->count_all_results('tblapprovals');
        
        $this->load->view('admin/app_home/index.php', $data);
    }

}
