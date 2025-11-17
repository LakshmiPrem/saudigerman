<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Litigation_collective_report_pdf extends App_pdf
{
     protected $litigations;

    public function __construct($litigations)
    {
        //$this->load_language($hearing->client);
        $litigations                = hooks()->apply_filters('contract_html_pdf_data', $litigations);
        $GLOBALS['litigations'] = $litigations;

        parent::__construct();

        $this->litigations = $litigations;
        $this->SetTitle(_l('litigation_collective_report'));

    }

    public function prepare()
    {
        $this->set_view_vars('litigations', $this->litigations);

        return $this->build();
    }

    protected function type()
    {
        return 'litigations';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_litigation_collective_report_pdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_litigation_collective_report_pdf.php';
        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }
        return $actualPath;
    }

    public function get_format_array()
    {
        return  [
            'orientation' => 'L',
            'format'      => 'landscape',
        ];
    }
}
