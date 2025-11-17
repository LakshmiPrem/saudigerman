<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Communication_center_pdf extends App_pdf
{
     protected $project;

    public function __construct($project)
    {
        $this->load_language($project->clientid);
        $project                = hooks()->apply_filters('contract_html_pdf_data', $project);
        $GLOBALS['communication_email_pdf'] = $project;

        parent::__construct();

        $this->project = $project;
        $this->SetTitle($this->project->name);

    }

    public function prepare()
    {
        $this->set_view_vars('project', $this->project);

        return $this->build();
    }

    protected function type()
    {
        return 'project_communication';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_project_communication_pdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/project_communication_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }
}


