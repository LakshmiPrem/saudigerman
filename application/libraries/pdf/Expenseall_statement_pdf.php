<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Expenseall_statement_pdf extends App_pdf
{
     protected $expenseapprove;


    public function __construct($expenseapprove)
    {
        $this->load_language($expenseapprove->clientid);
       $expenseapprove               = hooks()->apply_filters('contract_html_pdf_data', $expenseapprove);
        $GLOBALS['expense_statement_pdf'] = $expenseapprove;

        parent::__construct();
 
        $this->expenseapprove = $expenseapprove;
	
        $this->SetTitle($this->expenseapprove->name);

    }

    public function prepare()
    {
			
		$data['expenseapprove']=$this->expenseapprove;
        $this->set_view_vars($data);

        return $this->build();
    }

    protected function type()
    {
        return 'expenseapprove';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_expenseapproveall_pdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/expenseapproveall.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
