<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Hearing_notice_pdf extends App_pdf
{
     protected $hearing;

    public function __construct($hearing)
    {
        $this->load_language($hearing->client);
        $hearing                = hooks()->apply_filters('contract_html_pdf_data', $hearing);
        $GLOBALS['hearing_pdf'] = $hearing;

        parent::__construct();

        $this->hearing = $hearing;
        $this->SetTitle($this->hearing->subject);

    }

    public function prepare()
    {
        $this->set_view_vars('hearing', $this->hearing);

        return $this->build();
    }

    protected function type()
    {
        return 'hearing';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_hearing_notice_pdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/hearing_notice_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}
