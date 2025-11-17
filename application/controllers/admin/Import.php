<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Import extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('projects_model');

        if (!is_admin()) {
            access_denied();
        }
    }
	

    public function import_cases()
    {
        if (!is_admin()) {
            access_denied();
        }

        $dbFields = ['name','case_type','clientid','opposite_party','claiming_amount','file_no','start_date','status','description'];

       /* $dbFields = $this->db->list_fields(db_prefix() . 'projects');
        foreach ($dbFields as $key => $contactField) {
            if ($contactField == 'phonenumber') {
                $dbFields[$key] = 'contact_phonenumber';
            }
        }*/

        //$dbFields = array_merge($dbFields, $this->db->list_fields(db_prefix() . 'clients'));

        $this->load->library('import/import_cases', [], 'import');

        $this->import->setDatabaseFields($dbFields);

        if ($this->input->post('download_sample') === 'true') {
            $this->import->downloadSample();
        }

        if ($this->input->post()
            && isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
            $this->import->setSimulation($this->input->post('simulate'))
                          ->setTemporaryFileLocation($_FILES['file_csv']['tmp_name'])
                          ->setFilename($_FILES['file_csv']['name'])
                          ->perform();


            $data['total_rows_post'] = $this->import->totalRows();

            if (!$this->import->isSimulation()) {
                set_alert('success', _l('import_total_imported', $this->import->totalImported()));
            }
        }


        $data['title']     = _l('import');
        $data['bodyclass'] = 'dynamic-create-groups';
        $this->load->view('admin/import/import', $data);
    }

   
}
