<?php

defined('BASEPATH') or exit('No direct script access allowed');
require_once(APPPATH . 'libraries/import/App_import.php');

class Import_cases extends App_import
{
    private $uniqueValidationFields = [];

    protected $notImportableFields = [];

    protected $requiredFields = ['name'];

    public function __construct()
    {
        $this->notImportableFields = hooks()->apply_filters('not_importable_leads_fields', ['id', 'source', 'status', 'dateadded', 'last_status_change', 'addedfrom', 'leadorder', 'date_converted', 'lost', 'junk', 'is_imported_from_email_integration', 'email_integration_uid', 'is_public', 'dateassigned', 'client_id', 'lastcontact', 'last_lead_status', 'from_form_id', 'default_language', 'hash','matter_template_id','referred_staff_id','enquiry_title','referal']);

         $this->notImportableFieldsForAll = hooks()->apply_filters('not_importable_leads_fields', ['last_status_change', 'addedfrom', 'leadorder', 'date_converted', 'lost', 'junk', 'is_imported_from_email_integration', 'email_integration_uid', 'is_public', 'dateassigned', 'client_id', 'lastcontact', 'last_lead_status', 'from_form_id', 'default_language', 'hash','matter_template_id','referred_staff_id','enquiry_title','referal']);

        

        

        parent::__construct();
    }

    public function perform()
    {
        $this->initialize();

        $databaseFields      = $this->getImportableDatabaseFields();
        $totalDatabaseFields = count($databaseFields);

        foreach ($this->getRows() as $rowNumber => $row) {
            $insert = [];
            for ($i = 0; $i < $totalDatabaseFields; $i++) {
                $row[$i] = $this->checkNullValueAddedByUser($row[$i]);

                if ($databaseFields[$i] == 'name' && empty($row[$i])) {
                    $row[$i] = '/';
                } elseif ($databaseFields[$i] == 'clientid') {
                    $row[$i] = $this->clientValue($row[$i]);
                }elseif ($databaseFields[$i] == 'opposite_party') {
                    $row[$i] = $this->oppositeValue($row[$i]);
                }

                $insert[$databaseFields[$i]] = $row[$i];
            }

            $insert = $this->trimInsertValues($insert);

            if (count($insert) > 0) {
                if ($this->isDuplicateLead($insert)) {
                    continue;
                }

                $this->incrementImported();

                $id = null;

                if (!$this->isSimulation()) {
                    

                    if (!isset($insert['addedfrom'])) {
                        $insert['addedfrom'] = get_staff_user_id();
                    }
                   
                    /*$tags = '';
                    if (isset($insert['tags']) || is_null($insert['tags'])) {
                        if (!is_null($insert['tags'])) {
                            $tags = $insert['tags'];
                        }
                        unset($insert['tags']);
                    }*/
                    $insert['project_created']     =  date('Y-m-d H:i:s');
                    $insert['progress']            = 
                    $insert['progress_from_tasks'] =  1;
                    $insert['billing_type']        =  0;
                    $insert['status']        =  1;
                    $this->ci->db->insert(db_prefix() . 'projects', $insert);
                    $id = $this->ci->db->insert_id();
                    if ($id) {
                        //handle_tags_save($tags, $id, 'project');
                        $field_names = ['available_features','view_tasks','create_tasks','edit_tasks','comment_on_tasks','view_task_comments','view_task_attachments','view_task_checklist_items','upload_on_tasks','view_task_total_logged_time','view_finance_overview','upload_files','open_discussions','view_milestones','view_gantt','view_timesheets','view_activity_log','view_team_members','hide_tasks_on_main_tasks_table'];
                        $field_values = ['{s:16:"project_overview";i:1;s:15:"project_updates";i:1;s:13:"project_files";i:1;s:13:"project_tasks";i:1;s:18:"project_timesheets";i:1;s:25:"project_payment_schedules";i:1;s:16:"project_invoices";i:1;s:17:"project_estimates";i:1;s:16:"project_expenses";i:1;s:20:"project_credit_notes";i:1;s:17:"project_reminders";i:1;s:18:"project_settlement";i:1;s:18:"project_milestones";i:1;s:5:"scope";i:1;s:11:"case_emails";i:1;s:11:"court_order";i:1;s:19:"project_discussions";i:1;s:13:"project_gantt";i:1;s:15:"project_tickets";i:1;s:17:"project_contracts";i:1;s:13:"project_notes";i:1;s:16:"project_activity";i:1;}','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','1','0'];
                        $settings['project_id'] = $id;
                        foreach($field_names as $key=> $f_name){
                            $settings['name']  = $f_name;
                            $settings['value'] = $field_values[$key];
                            $this->ci->db->insert('tblproject_settings',$settings);     
                        }     
                    }
                } else {
                    $this->simulationData[$rowNumber] = $this->formatValuesForSimulation($insert);
                }

                //$this->handleCustomFieldsInsert($id, $row, $i, $rowNumber, 'leads');
            }

            if ($this->isSimulation() && $rowNumber >= $this->maxSimulationRows) {
                break;
            }
        }

        if(!$this->isSimulation()){
            $this->move_opposite_party_to_multiple_rel_table_case();
            $this->move_opposite_party_to_multiple_rel_table_client();
        }
    }

    protected function tags_formatSampleData()
    {
        return 'tag1,tag2';
    }

    public function formatFieldNameForHeading($field)
    {
        if (strtolower($field) == 'name') {
            return 'CASE TITLE';
        }

        if (strtolower($field) == 'referal') {
            return 'BD Representative';
        }

        if (strtolower($field) == 'mobile_other') {
            return 'Additional mobile';
        }

        if (strtolower($field) == 'state') {
            return 'Emirate';
        }
        if (strtolower($field) == 'clientid') {
            return 'CLIENT';
        }

        return parent::formatFieldNameForHeading($field);
    }

    protected function email_formatSampleData()
    {
        return uniqid() . '@example.com';
    }

    protected function failureRedirectURL()
    {
        return admin_url('leads/import');
    }

    private function isDuplicateLead($data)
    {
        foreach ($this->uniqueValidationFields as $field) {
            if ((isset($data[$field]) && $data[$field] != '')
                && total_rows(db_prefix() . 'leads', [$field => $data[$field]]) > 0) {
                return true;
            }
        }

        return false;
    }

    private function formatValuesForSimulation($values)
    {
        foreach ($values as $column => $val) {
            if ($column == 'clientid' && !empty($val) && is_numeric($val)) {
                
                
                $country = $this->getClient(null, $val);
                if ($country) {
                    $values[$column] = $country->company;
                }
            }
            else if ($column == 'opposite_party' && !empty($val) && is_numeric($val)) {
                $country = $this->getOpposite(null, $val);
                if ($country) {
                    $values[$column] = $country->name;
                }
            }else if ($column == 'case_type' && !empty($val) && is_numeric($val)) {
                $country = $this->getCasetype(null, $val);
                if ($country) {
                    $values[$column] = $country->name;
                }
            }
        }

        return $values;
    }

    private function getClient($search = null, $id = null)
    {
        if ($search) {
            $this->ci->db->limit(1);
            $this->ci->db->order_by("MATCH(company) AGAINST('".trim($search)."' IN BOOLEAN MODE)", 'DESC');
            $this->ci->db->like('company', trim($search));
            $query = $this->ci->db->get(db_prefix() . 'clients');
            $result = $query->row();
        } else {
            $this->ci->db->where('userid', $id);
            $query = $this->ci->db->get(db_prefix() . 'clients');
            $result = $query->row();
        }
        if($query->num_rows() > 0){
            return $result;
        }
        else{
            
            $this->ci->db->insert('tblclients',['company' => $search]);
            $cientid  = $this->ci->db->insert_id();
            return  $this->ci->db->select('userid')->where('userid',$cientid)->get(db_prefix() . 'clients')->row();
        }
    }

    private function clientValue($value)
    {
        if ($value != '') {
            if (!empty($value)) {
                $client = $this->getClient($value);
                $value   = $client ? $client->userid : 0;
            }
        } else {
            $value = 0;
        }

        return $value;
    }

    private function getCasetype($search = null, $id = null)
    {
        if ($search) {
            $this->ci->db->limit(1);
            //$this->ci->db->order_by("MATCH(name) AGAINST('".trim($search)."' IN BOOLEAN MODE)", 'DESC');
            $this->ci->db->like('id', trim($search));
            $this->ci->db->or_like('name', trim($search));
            $query = $this->ci->db->get(db_prefix() . 'project_types');
            $result = $query->row();
        } else {
            $this->ci->db->where('id', $id);
            $query = $this->ci->db->get(db_prefix() . 'project_types');
            $result = $query->row();
        }
        if($query->num_rows() > 0){
            return $result;
        }
        else{
            /*$this->ci->db->insert('tblclients',['company' => $search]);
            $cientid  = $this->ci->db->insert_id();
            return  $this->ci->db->select('userid')->where('userid',$cientid)->get(db_prefix() . 'clients')->row();*/
            return false;
        }
    }

    private function caseTypeValue($value)
    {
        if ($value != '') {
            if (!empty($value)) {
                $client = $this->getCasetype($value);
                $value   = $client ? $client->id : $value;
            }
        } else {
            $value = 0;
        }

        return $value;
    }



    private function getOpposite($search = null, $id = null)
    {
        if ($search) {
            $this->ci->db->limit(1);
            //$this->ci->db->order_by("MATCH(name) AGAINST('".trim($search)."' IN BOOLEAN MODE)", 'DESC');
            $this->ci->db->like('name', $search);
            $query = $this->ci->db->get(db_prefix() . 'oppositeparty');
            $result = $query->row();
        } else {
            $this->ci->db->where('id', $id);
            $query = $this->ci->db->get(db_prefix() . 'oppositeparty');
            $result = $query->row();
        }
        if($query->num_rows() > 0){
            return  $result;
        }else{
            $this->ci->db->insert('tbloppositeparty',['name' => $search]);
            $cientid  = $this->ci->db->insert_id();
            return  $this->ci->db->select('id')->where('id',$cientid)->get(db_prefix() . 'oppositeparty')->row();
        }
    }

    private function oppositeValue($value)
    {
        if ($value != '') {
            if (!empty($value)) {
                $opposite = $this->getOpposite($value);
                $value   = $opposite ? $opposite->id : 0;
            }
        } else {
            $value = 0;
        }

        return $value;
    }

    

        /**
     * Get HTML form for download sample .csv file
     * @return string x
     */
    public function downloadAllFormHtml()
    {
        $form = '';
        $form .= form_open(admin_url('leads/download_all'));
        $form .= form_hidden('download_all', 'true');
        $form .= '<button type="submit" class="btn btn-success">Download All</button>';
        $form .= '';
        $form .= form_close();

        return $form;
    }

        /* Download sample .csv file
     * @return mixed
     */
    public function downloadComplete()
    {
        $totalSampleFields = 0;
        $dbFieldKeys       = [];
        $file_name= "All-Leads-".date('Y-m-d');
        header('Pragma: public');
        header('Expires: 0');
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename="'.$file_name.'.csv";');
        header('Content-Transfer-Encoding: binary');

        foreach ($this->getImportableDatabaseFieldsForDownloadAll() as $field) {
            echo '"' . $this->formatFieldNameForHeading($field) . '",';
            $dbFieldKeys[$totalSampleFields] = $field;
            $totalSampleFields++;
        }

        foreach ($this->getCustomFields() as $field) {
            echo '"' . $field['name'] . '",';
            $totalSampleFields++;
        }

        echo "\n";
        $debtors = $this->ci->leads_model->get();
       
        $totalSampleRows = sizeof($debtors);
        if($totalSampleRows >  0 ){
        for ($row = 0; $row < $totalSampleRows; $row++) {
            for ($f = 0; $f < $totalSampleFields; $f++) {
                //$sampleDataText = $this->getTableRowDataText(isset($dbFieldKeys[$f]) ? $dbFieldKeys[$f] :  null);
                
                $dataValue = $debtors[$row][$dbFieldKeys[$f]];
                if($dbFieldKeys[$f] == 'assigned'){
                    $dataValue = get_staff_full_name($debtors[$row][$dbFieldKeys[$f]]);
                }elseif ($dbFieldKeys[$f] == 'tele_call_executive') {
                    $dataValue = get_staff_full_name($debtors[$row][$dbFieldKeys[$f]]);
                }elseif ($dbFieldKeys[$f] == 'status') {
                    $dataValue = $debtors[$row]['status_name'];
                }elseif($dbFieldKeys[$f] == 'source'){
                    $dataValue = $debtors[$row]['source_name'];
                }elseif($dbFieldKeys[$f] == 'assigned_to'){
                    $dataValue = get_staff_full_name($debtors[$row][$dbFieldKeys[$f]]);
                }elseif($dbFieldKeys[$f] == 'client_id'){
                    $dataValue = get_company_name($debtors[$row][$dbFieldKeys[$f]]);
                }elseif($dbFieldKeys[$f] == 'last_added_agent'){
                    $dataValue = get_staff_full_name($debtors[$row][$dbFieldKeys[$f]]);
                }elseif($dbFieldKeys[$f] == 'last_contact_code'){
                    $dataValue = get_contact_code_by_id($debtors[$row][$dbFieldKeys[$f]]);
                }elseif ($dbFieldKeys[$f] == 'country'){
                    $dataValue = get_country_name($debtors[$row][$dbFieldKeys[$f]]);
                }
                echo '"' . $dataValue . '",';
            }

            // Is not last in for loop
            if ($row < $totalSampleRows - 1) {
                echo "\n";
            }
        }
    }
        echo "\n";
        exit;
    }

    public function move_opposite_party_to_multiple_rel_table_case()
    {
        $projects = $this->ci->db->get('tblprojects')->result_array();
        foreach ($projects as $value) {
            if(is_numeric($value['opposite_party']) && $value['opposite_party'] > 0){
                $check_already_inserted = total_rows('tblproject_opposite_parties',['project_id'=>$value['id'],'opposite_party_id'=>$value['opposite_party']]);
                if($check_already_inserted > 0){
                    continue;
                }else{
                    $this->ci->db->insert('tblproject_opposite_parties',['project_id'=>$value['id'],'opposite_party_id'=>$value['opposite_party']]);
                }
            }
        }
    }
    public function move_opposite_party_to_multiple_rel_table_client()
    {
        $projects = $this->ci->db->get('tblprojects')->result_array();
        foreach ($projects as $value) {
            if(is_numeric($value['opposite_party']) && $value['opposite_party'] > 0){                
                $check_already_inserted = total_rows('tblclient_oppositeparty_rel',['opposite_party_id'=>$value['opposite_party'],'client_id'=>$value['clientid']]);
                if($check_already_inserted > 0){
                    continue;
                }else{
                    $this->ci->db->insert('tblclient_oppositeparty_rel',['opposite_party_id'=>$value['opposite_party'],'client_id'=>$value['clientid']]);
                }


            }
        }
    }

}
