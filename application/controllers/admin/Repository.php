<?php

use app\services\imap\Imap;
use app\services\imap\ConnectionErrorException;
use Ddeboer\Imap\Exception\MailboxDoesNotExistException;

defined('BASEPATH') or exit('No direct script access allowed');

class Repository extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model');

        if (!is_admin()) {
            access_denied('documents');
        }
    }

    /* List all departments */
    public function index()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('repository_files');
        }
        $data['related_to'] = array("0"=>array("id"=>'document',"type"=>"Document"),
                                        "1"=>array('id'=>'contract','type'=>'Contract'),
                                        "2"=>array('id'=>'task','type'=>'Task'),
                                        "3"=>array('id'=>'customer','type'=>'Customer'),
                                        "4"=>array('id'=>'expense','type'=>'Expense'),
                                        "5"=>array('id'=>'communication','type'=>'Correspondance'),
                                        "6"=>array('id'=>'oppositeparty','type'=>'Oppositeparty'));
        $data['title']                = _l('documents');
        $this->load->view('admin/repository/manage_files', $data);  
    }
    public function repository_project_files()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('repository_project_files');
        }
        $data['related_to'] = array("0"=>array("id"=>'document',"type"=>"Document"),
                                        "1"=>array('id'=>'contract','type'=>'Contract'),
                                        "2"=>array('id'=>'task','type'=>'Task'),
                                        "3"=>array('id'=>'customer','type'=>'Customer'),
                                        "4"=>array('id'=>'expense','type'=>'Expense'),
                                        "5"=>array('id'=>'communication','type'=>'Communication'),
                                        "6"=>array('id'=>'oppositeparty','type'=>'Oppositeparty'));
        $data['title']                = _l('documents');
        $this->load->view('admin/repository/manage_project_files', $data);  
    }

   

   
}