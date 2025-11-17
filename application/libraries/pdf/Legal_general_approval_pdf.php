<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Legal_general_approval_pdf extends App_pdf
{
     protected $legalapprove;


    public function __construct($legalapprove)
    {
        $this->load_language($legalapprove->userid);
       $legalapprove               = hooks()->apply_filters('contract_html_pdf_data', $legalapprove);
        $GLOBALS['legalapprove_pdf'] = $legalapprove;

        parent::__construct();
 
        $this->legalapprove = $legalapprove;
	
        $this->SetTitle($this->legalapprove->subject);

    }

    public function prepare()
    {
		//	$ticketid = $this->legalapprove->ticketid;
		//	$legaltasks = $this->ci->tickets_model->get_ticket_tasks($ticketid);
		$data['legalapprove']=$this->legalapprove;
		//$data['legaltask']=$legaltasks;
          $this->set_view_vars($data);

        return $this->build();
    }

    protected function type()
    {
        return 'legalapprove';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_legal_general_approval_pdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/legal_general_approval_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
