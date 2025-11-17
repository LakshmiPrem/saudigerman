<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Customer_data_pdf extends App_pdf
{
    protected $client_id;

    public function __construct($client_id)
    {
        parent::__construct();

        $this->client_id = $client_id;
		$this->SetFont('aealarabiya', '', 10);
    }
 
    public function prepare()
    {
        $customer = $this->ci->clients_model->get($this->client_id);
       
        $this->SetTitle($customer->company);
		$data['stakeeholders'] = $this->ci->clients_model->get_contacts($this->client_id);
      	$data['shareholders'] = $this->ci->clients_model->get_clientshareholders($this->client_id);
	    $data['user_notes'] = $this->ci->misc_model->get_notes($this->client_id, 'customer');
		$data['client_subfiles']=$this->ci->clients_model->get_clientconstitution('',['userid'=>$this->client_id]);
		$data['client_owners']=$this->ci->clients_model->get_contacts($this->client_id,['is_owner'=>1,'active'=>1]);
		$data['client_managers']=$this->ci->clients_model->get_contacts($this->client_id,['is_manager'=>1,'active'=>1]);
		$data['client_directors']=$this->ci->clients_model->get_contacts($this->client_id,['is_director'=>1,'active'=>1]);
		$data['client_secretarys']=$this->ci->clients_model->get_contacts($this->client_id,['is_secretary'=>1,'active'=>1]);
        $data['client']    = $customer;
      //  $data['tasks']             = $this->ci->clients_model->get_tasks($project->id, [], false);
       
          $data['total_files_attached'] = total_rows(db_prefix().'files', [
                'rel_id' => $this->client_id,'rel_type'=>'customer'
            ]);
       $this->set_view_vars($data);

        return $this->build();
    }

    protected function type()
    {
        return 'client-data';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/admin/clients/my_export_data_pdf.php';
        $actualPath = APPPATH . 'views/admin/clients/export_data_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }

    public function get_format_array()
    {
        return  [
            'orientation' => 'P',
			 'format'      => 'potrait',
           // 'format'      => 'landscape',
        ];
    }
}
