<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Hearings extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('agreements_model');
        // $this->load->model('currencies_model');
    }

    public function index()
    {
        $data['lawyer_staffs'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);
        $this->load->model('casediary_model');
        $data['courts']        = $this->casediary_model->get_courts();
        $data['title'] = _l('hearings');
        $this->load->view('admin/hearing/manage', $data);
    }
    public function hearing()
    {
        $data['lawyer_staffs'] = $this->staff_model->get('', ['active' => 1,'is_lawyer'=>'1']);
        $this->load->model('casediary_model');
        $data['courts']        = $this->casediary_model->get_courts();
        $data['emirates_arr'] = get_emirates();
        $data['title'] = _l('hearings');
        $this->load->view('admin/hearing/manage_new', $data);
    }
    public function hearings_tables()
    {
        $this->app->get_table_data('hearings_overview');

    }
}
?>