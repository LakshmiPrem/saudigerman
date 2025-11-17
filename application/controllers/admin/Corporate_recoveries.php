<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Corporate_recoveries extends AdminController
{
    private $not_importable_clients_fields;
    public $pdf_zip;
 
    public function __construct()
    {
        parent::__construct();
        $this->load->model('corporate_recoveries_model');
      #############  United Arab Bank #################  
        //$this->not_importable_clients_fields = do_action('not_importable_clients_fields',array('id', /*'datecreated',*/ 'active', 'leadid', 'default_language', 'default_currency', 'show_primary_contact', 'addedfrom', 'customer_ID', 'state', 'full_name', 'city', 'country','zip', 'settlement_type', 'client_id', 'number_of_installments','account_no', 'installment_start_date', 'latitude', 'longitude', 'liability_details', 'total_liability', 'trade_license_no', 'trade_license_authority', 'trade_license_establishment_date','trade_license_last_renewal_date', 'managing_directors', 'guarantors_address_uae', 'guarantors_address_india', 'guarantors_telephone', 'uae_civil_case', 'uae_criminal_case', 'indian_criminal_case', 'indian_civil_case', 'sponser_name', 'sponser_details', 'date_of_classification', 'police_complaint_no', 'complaint_registered_date', 'police_station_name', 'decision','relationship_manager','current_status','reassigned','crn_no','credit_card_no','cif_id','nationality','dob','passportNo','product','segment','agency','product_description','place_of_birth','address_1','eid_expiry','visa','po_box','signatory_name','occupation','employer_name','passport_expiry','amount_in_inr','amount_in_words','new_mobile')); 
        ############# UAB CORPORATE ########################
        
        $this->not_importable_partner = array('id', 'recovery_id','emirates_id', 'adhar_card', 'uae_address','india_address', 'telephone', 'pt_email', 'other_company', 'is_md');

        // last_active_time is from Chattr plugin, causing issue
    }

    /* List all clients */
    public function index()
    {

        if (!has_permission('corporate_recovery', '', 'view_own')) {
            if (!have_assigned_recovery_customers()) { 
                access_denied('corporate_recoveries');
            }
        }

        $this->load->model('contracts_model');
        $data['contract_types'] = $this->contracts_model->get_contract_types();
        //$this->load->model('documents_model');
        //$data['contract_types'] = $this->documents_model->get_contract_types();
        $data['groups']         = $this->clients_model->get_groups();
        $data['title']          = _l('corporate_recoveries');

        $this->load->model('proposals_model');
        $data['proposal_statuses'] = $this->proposals_model->get_statuses();

        $this->load->model('invoices_model');
        $data['invoice_statuses'] = $this->invoices_model->get_statuses();

        $this->load->model('estimates_model');
        $data['estimate_statuses'] = $this->estimates_model->get_statuses();

        $this->load->model('projects_model');
        $data['project_statuses'] = $this->projects_model->get_project_statuses();

        $data['customer_admins'] = $this->corporate_recoveries_model->get_customers_admin_unique_ids();
        $data['staff']           = $this->staff_model->get('', 1);

        $whereContactsLoggedIn = '';
        if (!has_permission('corporate_recovery', '', 'view')) {
            $whereContactsLoggedIn = ' AND userid IN (SELECT customer_id FROM tblrecoveryadmins WHERE staff_id='.get_staff_user_id().')';
        }
        $data['clients'] = $this->clients_model->get();


        $data['contacts_logged_in_today'] = $this->clients_model->get_contacts('', 'last_login LIKE "'.date('Y-m-d').'%"'.$whereContactsLoggedIn);
        $this->load->view('admin/corporate_recoveries/manage', $data);
    }

    

    public function table($clientid = '')
    {
        if (!has_permission('corporate_recovery', '', 'view')) {
            if (!have_assigned_recovery_customers() && !has_permission('corporate_recovery', '', 'create')) {
                ajax_access_denied();
            }
        }

        $this->app->get_table_data('my_corporate_recoveries', array(
            'clientid' => $clientid,
        ));
    }


    /* Edit client or add new client*/
    public function corporate_recovery($id = '')
    {
        
        if (!has_permission('corporate_recovery', '', 'view')) {
            if ($id != '' && !is_recovery_admin($id)) {
                access_denied('corporate_recoveries');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('corporate_recovery', '', 'create')) {
                    access_denied('corporate_recoveries');
                }

                $data                 = $this->input->post();

                $save_and_add_contact = false;
                
                $id = $this->corporate_recoveries_model->add($data);
                //if (!has_permission('corporate_recovery', '', 'view')) {
                    $assign['customer_admins']   = array();
                    $assign['customer_admins'][] = get_staff_user_id();
                    $this->corporate_recoveries_model->assign_admins($assign, $id);
                //}
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('recovery')));
                    redirect(admin_url('corporate_recoveries/corporate_recovery/' . $id));
                    
                }
            } else {
                if (!has_permission('corporate_recovery', '', 'edit')) {
                    if (!is_recovery_admin($id)) {
                        access_denied('corporate_recoveries');
                    }
                }
                $success = $this->corporate_recoveries_model->update($this->input->post(), $id);
                if ($success == true) {
                    set_alert('success', _l('updated_successfully', _l('recovery')));
                }
                redirect(admin_url('corporate_recoveries/corporate_recovery/' . $id));
            }
        }

        if ($this->input->get('customer_id')) {
            $data['customer_id']        = $this->input->get('customer_id');
        }

        if (!$this->input->get('group')) {
            $group = 'profile';
        } else {
            $group = $this->input->get('group');
        }
        // View group
        $data['group']  = $group;
        // Customer groups
        $data['groups'] = $this->clients_model->get_groups();

        if ($id == '') {
            $title = _l('add_new', _l('corporate_recovery'));
        } else {
            $client = $this->corporate_recoveries_model->get($id);
            $client->userid = $client->id;
            if (!$client) {
                blank_page('recovery Not Found');
            }

            //$data['partners']         = $this->corporate_recoveries_model->get_partners($id);
             $data['contacts'] = $this->clients_model->get_contacts($client->client_id);

            // Fetch data based on groups
            if ($group == 'profile') {
               $data['customer_admins'] = $this->corporate_recoveries_model->get_admins($id);
               $data['debt_products'] = $this->corporate_recoveries_model->get_all_debt_products($id);
               //$data['assigned_users_list'] = $this->corporate_recoveries_model->get_assigned_users($id);
            } elseif ($group == 'attachments') {
                $data['attachments']   = get_all_recovery_attachments($id);
            } elseif ($group == 'vault') {
                $data['vault_entries'] = do_action('check_vault_entries_visibility', $this->clients_model->get_vault_entries($id));
                if ($data['vault_entries'] === -1) {
                    $data['vault_entries'] = array();
                }
            } elseif ($group == 'estimates') {
                $this->load->model('estimates_model');
                $data['estimate_statuses'] = $this->estimates_model->get_statuses();
            } elseif ($group == 'invoices') {
                $this->load->model('invoices_model');
                $data['invoice_statuses'] = $this->invoices_model->get_statuses();
            } elseif ($group == 'credit_notes') {
                $this->load->model('credit_notes_model');
                $data['credit_notes_statuses'] = $this->credit_notes_model->get_statuses();
                $data['credits_available'] = $this->credit_notes_model->total_remaining_credits_by_customer($id);
            } elseif ($group == 'payments') {
                $this->load->model('payment_modes_model');
                $data['payment_modes'] = $this->payment_modes_model->get();
            } elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_notes($id, 'corporate');
            } elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            } elseif ($group == 'statement') {
                if (!has_permission('invoices', '', 'view') && !has_permission('payments', '', 'view')) {
                    set_alert('danger', _l('access_denied'));
                    redirect(admin_url('clients/client/'.$id));
                }
                $contact = $this->clients_model->get_contact(get_primary_contact_user_id($id));
                $email   = '';
                if ($contact) {
                    $email = $contact->email;
                }

                $template_name = 'client-statement';
                $data['template'] = get_email_template_for_sending($template_name, $email);

                $data['template_name']     = $template_name;
                $this->db->where('slug', $template_name);
                $this->db->where('language', 'english');
                $template_result = $this->db->get('tblemailtemplates')->row();

                $data['template_system_name'] = $template_result->name;
                $data['template_id'] = $template_result->emailtemplateid;

                $data['template_disabled'] = false;
                if (total_rows('tblemailtemplates', array('slug'=>$data['template_name'], 'active'=>0)) > 0) {
                    $data['template_disabled'] = true;
                }
            }elseif ($group == 'casediary') {
                $this->load->model('casediary_model');
                $where = array('client'=>$id);
                //$data['casediary'] = $this->casediary_model->get('',$where);
            }elseif ($group == 'demand_notice') {
                $data['demand_notice'] = $this->corporate_recoveries_model->get_demand_notice($id);
            } 

            $data['staff'] = $this->staff_model->get('', ['active' => 1]);

            $data['client']        = $client;
            $title                 = $client->debtor_title;

            // Get all active staff members (used to add reminder)
            $data['members'] = $data['staff'];

            /*if (!empty($data['client']->company)) {
                // Check if is realy empty client company so we can set this field to empty
                // The query where fetch the client auto populate firstname and lastname if company is empty
                if (is_empty_customer_company($data['client']->userid)) {
                    $data['client']->company = '';
                }
            }*/
        }

        $this->load->model('currencies_model');
        $data['currencies'] = $this->currencies_model->get();

        if ($id != '') {
            $customer_currency = $data['client']->default_currency;

            foreach ($data['currencies'] as $currency) {
                if ($customer_currency != 0) {
                    if ($currency['id'] == $customer_currency) {
                        $customer_currency = $currency;
                        break;
                    }
                } else {
                    if ($currency['isdefault'] == 1) {
                        $customer_currency = $currency;
                        break;
                    }
                }
            }

            if (is_array($customer_currency)) {
                $customer_currency = (object) $customer_currency;
            }

            $data['customer_currency'] = $customer_currency;
        }
        $data['clients'] = $this->clients_model->get();
        $data['bodyclass'] = 'customer-profile dynamic-create-groups';
        $data['title'] = $title;

        $this->load->view('admin/corporate_recoveries/corporate_recovery', $data);
    }


    public function generate_settlement_document_word($id){
        
        require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
        
        $proposal = $this->corporate_recoveries_model->get($id);

        $clients_data = $this->clients_model->get($proposal->client_id);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('temp/Vetted_Settlement_Agreement.docx');
        $day   = date('d');
        $month = date('F');
        $year  = date('Y');

        $debtor_name = $proposal->company;
        $debtor_address     = nl2br($proposal->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $debtor_address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $debtor_address);
        $debtor_address = str_replace('&','&amp;', $debtor_address) ;
        $debtor_mobile   = $proposal->mobile_no;
        $debtor_email       = $proposal->email_id;
        $outstanding_amount = format_money($proposal->outstanding_amount);
        $client_name = str_replace('&','&amp;', $clients_data->company) ;
        $client_email   = $clients_data->email_id;
        $client_tel   = $clients_data->phonenumber;
        $client_address     = nl2br($clients_data->address);
        $client_address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $client_address);
        $client_address = str_replace('&','&amp;', $client_address) ;

        /*$this->load->library('numberword', array(
        'clientid' => $proposal->client_id,
        ));
        $amount_in_words = $this->numberword->convert($proposal->outstanding_amount,'','Fils');*/
        $totalpaid = 0;
        $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblrecoveries_installments` WHERE recovery_id = ? AND installment_status = ? AND recovery_type = ?',array($id,'paid','corporate'))->row();
             if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
        }
        $settlement_amount = $proposal->outstanding_amount - $totalpaid;
        $settlement_amount = format_money($settlement_amount);
        
        $templateProcessor->setValue('day',$day);
        $templateProcessor->setValue('month',$month);
        $templateProcessor->setValue('year',$year);
        $templateProcessor->setValue('debtor_name',$debtor_name);
        $templateProcessor->setValue('debtor_address',$debtor_address);
        $templateProcessor->setValue('debtor_mobile',$debtor_mobile);
        $templateProcessor->setValue('debtor_email',$debtor_email);

        $templateProcessor->setValue('client_name',$client_name);
        $templateProcessor->setValue('client_email',$client_email);
        $templateProcessor->setValue('client_tel',$client_tel);
        $templateProcessor->setValue('client_address',$client_address);

        $templateProcessor->setValue('outstanding_amount',$outstanding_amount);
        //$templateProcessor->setValue('amount_in_words',$amount_in_words);
        $templateProcessor->setValue('settlement_amount',$settlement_amount);
        

        // Table-----------

        $document_with_table = new \PhpOffice\PhpWord\PhpWord();
        $section = $document_with_table->addSection();
        $tableStyle = array( 'borderSize' => 3, 
            'borderColor' => '000000', 
            //'afterSpacing' => 10, 
            'Spacing'=> 10, 
            'cellMargin'=> 10);

        $myTheadFontStyle = [
            'name' => 'Times New Roman (Headings CS)',
            'size' => '12',
            'color' => '000000',
            'bold' => true,
            //'italic' => true
            //'bgColor'=>'000000'
        ];
        $myFontStyle = [
            'name' => 'Times New Roman (Headings CS)',
            'size' => '10',
            'color' => '000080',
            'bold' => false,
            //'italic' => true

        ];
        $myParagraphStyle = array();
        $myRow = array('bgColor'=>'Black');
        $table = $section->addTable( $tableStyle );
        //$table->setFontStyle($fontStyle);
        $align_center =  ['align' => 'center'];
        $align_right  =  ['align' => 'right'];
        $amount_column_align = array(
            //'space' => array('before' => 360, 'after' => 280), 
            'indentation' => array('left' => 580, 'right' => 80)
        );
        
        $no_width = 2000;
        $item_width = 3000;

        $table->addRow();
        $table->addCell($no_width)->addText("Date",$myTheadFontStyle,$align_center);
        $table->addCell($item_width)->addText("Starting Balance",$myTheadFontStyle,$align_center);
        $table->addCell($item_width)->addText("Payment",$myTheadFontStyle,$align_center);
        $table->addCell($item_width)->addText("Ending Balance",$myTheadFontStyle,$align_center);

        $settlements_data = $this->db->query('SELECT * FROM tblrecoveries_installments WHERE recovery_id = ? AND  recovery_type = ? ORDER BY installment_date ASC',array($id,'corporate'))->result();
        $j=0;
        $outstanding_amount = trim(str_replace(',', '', $proposal->outstanding_amount));

        foreach ($settlements_data as $settle_data) {
            if($j==0){
                $starting_balance = $outstanding_amount;
            }else{
                $starting_balance = $starting_balance - $settle_data->installment_amount;
            }
            $starting_balance = str_replace(',','',$starting_balance);
            $table->addRow();
            $table->addCell($no_width)->addText(_d($settle_data->installment_date),'',$align_center);
            $table->addCell($item_width)->addText(format_money($starting_balance),'',$align_center);
            $table->addCell($item_width)->addText(format_money($settle_data->installment_amount),'',$align_center);
            $table->addCell($item_width)->addText(format_money($starting_balance - $settle_data->installment_amount),'',$align_center);
            $j++;
            //$starting_balance = $proposal->outstanding_amount - $settle_data->installment_amount;
        }

        // Create writer to convert document to xml
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($document_with_table, 'Word2007');
        // Get all document xml code
        $fullxml = $objWriter->getWriterPart('Document')->write();
        // Get only table xml code
        $tablexml = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);
        // Replace mark by xml code of table
        $templateProcessor->setValue('settlement_table', $tablexml);

        //-----------------
        //
        $path        = get_upload_path_by_type('corporate_recovery').$id.'/';
        _maybe_create_upload_path($path);
        $path        = get_upload_path_by_type('corporate_recovery').$id.'/settlement_doc/';
        _maybe_create_upload_path($path);
        if(file_exists($path.'Settlement_Document.docx')){
            unlink($path.'Settlement_Document.docx');
            rmdir($path);
        }
        $templateProcessor->saveAs($path.'Settlement_Document.docx');

        set_alert('success', _l('generated', _l('settlement_document')));
        redirect(admin_url('corporate_recoveries/corporate_recovery/' . $id . '?tab=contacts'));
    }

    public function installment($customer_id, $contact_id = '')
    {
        if (!has_permission('corporate_recovery', '', 'view')) {
            if (!is_recovery_admin($customer_id)) {
                echo _l('access_denied');
                die;
            }
        }
        $data['customer_id'] = $customer_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();

            unset($data['contactid']);
            if ($contact_id == '') {
                if (!has_permission('corporate_recovery', '', 'create')) {
                    if (!is_recovery_admin($customer_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    }
                }
                $id      = $this->corporate_recoveries_model->add_installment($data, $customer_id);
                $message = '';
                $success = false;
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('installment'));
                }
                $totalpaid = $this->corporate_recoveries_model->get_installment_totalpaid($customer_id);
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'has_primary_contact'=>(total_rows('tblcontacts', array('userid'=>$customer_id, 'is_primary'=>1)) > 0 ? true : false),
                    'is_individual'=>is_empty_customer_company($customer_id) && total_rows('tblcontacts',array('userid'=>$customer_id)) == 1,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            } else {
                if (!has_permission('corporate_recovery', '', 'edit')) {
                    
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;

                }
                $original_contact = $this->corporate_recoveries_model->get_installment($contact_id);
                $success          = $this->corporate_recoveries_model->update_installment($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('installment'));
                }
                
                $totalpaid = $this->corporate_recoveries_model->get_installment_totalpaid($customer_id);
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                    'original_email' => $original_email,
                    'has_primary_contact'=>true,
                    'totalpaid'=>$totalpaid,
                ));
                die;
            }
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('installment_lowercase'));
        } else {
            $data['contact'] = $this->corporate_recoveries_model->get_installment($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = _l('edit', _l('installment_lowercase'));
        }

        $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/corporate_recoveries/modals/installment', $data);
    }

    public function mark_as_active($id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblcorporate_recoveries', array(
            'active' => 1,
        ));
        redirect(admin_url('corporate_recoveries/corporate_recovery/' . $id));
    }

   
    public function upload_attachment($id)
    {
        handle_recovery_attachments_upload($id);

    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database($this->input->post('clientid'), 'defaulter', $this->input->post('files'), $this->input->post('external'));
        }
    }

    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('corporate_recovery', '', 'delete') || is_recovery_admin($customer_id)) {
            $this->corporate_recoveries_model->delete_attachment($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    } 

    /* Delete client */
    public function delete($id)
    {
        if (!has_permission('corporate_recovery', '', 'delete')) {
            access_denied('customers');
        }
        if (!$id) {
            redirect(admin_url('recoveries'));
        }
        $response = $this->corporate_recoveries_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('recovery_delete_transactions_warning',_l('invoices').', '._l('estimates').', '._l('credit_notes')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('recovery')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('recovery_lowercase')));
        }
        redirect(admin_url('corporate_recoveries'));
    }

    /* Staff can login as client */
    public function login_as_client($id)
    {
        if (is_admin()) {
            login_as_client($id);
        }
        do_action('after_contact_login');
        redirect(site_url());
    }
  public function import()
    {
        if (!has_permission('corporate_recovery', '', 'create')) {
            access_denied('corporate_recoveries');
        }

        $dbFields =  $this->db->list_fields(db_prefix() . 'corporate_recoveries');

        $this->load->library('import/import_corporate_debtors', [], 'import');
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
        $this->load->view('admin/corporate_recoveries/import_debt', $data);
    }
    public function importold()
    {
        if (!has_permission('corporate_recovery', '', 'create')) {
            access_denied('customers');
        }
        $country_fields = array('country');

        $simulate_data  = array();
        $total_imported = 0;
        if ($this->input->post()) {



            // Used when checking existing company to merge contact
            $contactFields = $this->db->list_fields('tblcontacts');

            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $import_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        while ($row = fgetcsv($fd)) {
                            $rows[] = $row;
                        }


                        //print_r($rows);

                        $data['total_rows_post'] = count($rows);
                        fclose($fd);
                        if (count($rows) <= 1) {
                            set_alert('warning', 'Not enought rows for importing');
                            redirect(admin_url('corporate_recoveries/import'));
                        }
                        unset($rows[0]);
                        if ($this->input->post('simulate')) {
                            if (count($rows) > 500) {
                                set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                            }
                        }
                        
                        $i                      = 0;
                        
                        $db_temp_fields = $this->db->list_fields('tblcorporate_recoveries');
                        
                        $db_fields      = array();
                        foreach ($db_temp_fields as $field) {
                            if (in_array($field, $this->not_importable_clients_fields)) {
                                continue;
                            }
                            $db_fields[] = $field;
                        }

                        //print_r($db_fields);
                        $_row_simulate = 0;

                        $required = array(
                            'company',
                        );

                        /*if (get_option('company_is_required') == 1) {
                            array_push($required, 'company');
                        }*/

                        foreach ($rows as $row) {
                            // do for db fields
                            $insert    = array();
                            $duplicate = false;
                            for ($i = 0; $i < count($db_fields); $i++) {
                                if (!isset($row[$i])) {
                                    continue;
                                }
                                
                                // Avoid errors on required fields;
                                if (in_array($db_fields[$i], $required) && $row[$i] == '' && $db_fields[$i] != 'company') {
                                    $row[$i] = '/';
                                } elseif (in_array($db_fields[$i], $country_fields)) {
                                    if ($row[$i] != '') {
                                        if (!is_numeric($row[$i])) {
                                            $this->db->where('iso2', $row[$i]);
                                            $this->db->or_where('short_name', $row[$i]);
                                            $this->db->or_where('long_name', $row[$i]);
                                            $country = $this->db->get('tblcountries')->row();
                                            if ($country) {
                                                $row[$i] = $country->country_id;
                                            } else {
                                                $row[$i] = 0;
                                            }
                                        }
                                    } else {
                                        $row[$i] = 0;
                                    }
                                }
                                if($row[$i] === 'NULL' || $row[$i] === 'null') {
                                    $row[$i] = '';
                                }
                                $insert[$db_fields[$i]] = $row[$i];
                            }


                            if ($duplicate == true) {
                                continue;
                            }
                            if (count($insert) > 0) {
                                $total_imported++;
                                //$insert['datecreated'] = date('Y-m-d H:i:s');
                                if ($this->input->post('default_pass_all')) {
                                    $insert['password'] = $this->input->post('default_pass_all',false);
                                }
                                if (!$this->input->post('simulate')) {
                                    //$insert['donotsendwelcomeemail'] = true;
                                    foreach ($insert as $key =>$val) {
                                        $insert[$key] = trim($val);
                                    }

                                  /*  if (isset($insert['company']) && $insert['company'] != '' && $insert['company'] != '/') {
                                        if (total_rows('tblclients', array('company'=>$insert['company'])) === 1) {
                                            $this->db->where('company', $insert['company']);
                                            $existingCompany = $this->db->get('tblclients')->row();
                                            $tmpInsert = array();

                                            foreach ($insert as $key=>$val) {
                                                foreach ($contactFields as $tmpContactField) {
                                                    if (isset($insert[$tmpContactField])) {
                                                        $tmpInsert[$tmpContactField] = $insert[$tmpContactField];
                                                    }
                                                }
                                            }
                                            $tmpInsert['donotsendwelcomeemail'] = true;
                                            if (isset($insert['contact_phonenumber'])) {
                                                $tmpInsert['phonenumber'] = $insert['contact_phonenumber'];
                                            }

                                            $contactid = $this->clients_model->add_contact($tmpInsert, $existingCompany->userid, true);

                                            continue;
                                        }
                                    }*/

                                    //$insert['is_primary'] = 1;
                                    $insert['client_id'] = $this->db->get_where('tblclients',array('client_no'=>$insert['client_no']))->row()->userid;//$this->input->post('client_id');
                                    //unset($insert['client_no']);
                                    $clientid      = $this->corporate_recoveries_model->add($insert, true);



                                    if ($clientid) {

                                        if($insert['assigned_to'] != ''){
                                            $assign['customer_admins']   = array();
                                            $assign['customer_admins'][] = $insert['assigned_to'];
                                            $this->corporate_recoveries_model->assign_admins($assign, $clientid);
                                        }

                                        if ($this->input->post('groups_in[]')) {
                                            $groups_in = $this->input->post('groups_in[]');
                                            foreach ($groups_in as $group) {
                                                $this->db->insert('tblcustomergroups_in', array(
                                                    'customer_id' => $clientid,
                                                    'groupid' => $group,
                                                ));
                                            }
                                        }
                                        if (!has_permission('corporate_recovery', '', 'view')) {
                                            $assign['customer_admins']   = array();
                                            $assign['customer_admins'][] = get_staff_user_id();
                                            $this->clients_model->assign_admins($assign, $clientid);
                                        }
                                    }
                                } else {
                                    foreach ($country_fields as $country_field) {
                                        if (array_key_exists($country_field, $insert)) {
                                            if ($insert[$country_field] != 0) {
                                                $c = get_country($insert[$country_field]);
                                                if ($c) {
                                                    $insert[$country_field] = $c->short_name;
                                                }
                                            } elseif ($insert[$country_field] == 0) {
                                                $insert[$country_field] = '';
                                            }
                                        }
                                    }
                                    $simulate_data[$_row_simulate] = $insert;
                                    $clientid                      = true;
                                }
                               
                            }
                            $_row_simulate++;
                            if ($this->input->post('simulate') && $_row_simulate >= 100) {
                                break;
                            }
                        }
                        unlink($newFilePath);
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        if (count($simulate_data) > 0) {
            $data['simulate'] = $simulate_data;
        }
        if (isset($import_result)) {
            set_alert('success', _l('import_total_imported', $total_imported));
        }
        //$data['groups']         = $this->clients_model->get_groups();
        $data['not_importable'] = $this->not_importable_clients_fields;
        $data['title']          = _l('import');
        $data['bodyclass'] = 'dynamic-create-groups';
        $data['bank_clients'] = $this->clients_model->get('');
        $this->load->view('admin/recoveries/import', $data);
    }

   

    public function bulk_action()
    {
        do_action('before_do_bulk_action_for_customers');
        $total_deleted = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            $groups = $this->input->post('groups');

            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_delete')) {
                        if ($this->corporate_recoveries_model->delete($id)) {
                            $total_deleted++;
                        }
                    } else {
                        if (!is_array($groups)) {
                            $groups = false;
                        }
                        //$this->client_groups_model->sync_customer_groups($id, $groups);
                    }
                }
            }
        }

        if ($this->input->post('mass_delete')) {
            set_alert('success', _l('total_recovery_deleted', $total_deleted));
        }
    }

    /* Change client status / active / inactive */
    public function change_defaulter_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->corporate_recoveries_model->change_defaulter_status($id, $status);
        }
    }

    public function delete_installment($customer_id, $id)
    {
        if (!has_permission('corporate_recovery', '', 'delete')) {
            if (!is_recovery_admin($customer_id)) {
                access_denied('recoveries');
            }
        }

        $this->corporate_recoveries_model->delete_installment($id);
        redirect(admin_url('corporate_recoveries/corporate_recovery/' . $customer_id . '?tab=contacts'));
    }

    function update_settlement_type($type,$defaulterID){
        $this->db->where('id',$defaulterID);
        $this->db->update('tblcorporate_recoveries',array('settlement_type'=>$type));
        echo 'success';
    }

    function get_client_commission($client_id,$defaulterid){

        $credit_limit = $this->db->get_where('tbldefaulters',array('id'=>$defaulterid))->row()->curr_os;

        $commission_percentage = $this->db->get_where('tblclients',array('userid'=>$client_id))->row()->commission_percentage;

        $rate = $credit_limit * ($commission_percentage/100);

        $description = $this->db->get_where('tbldefaulters',array('id'=>$defaulterid))->row()->full_name;

        $response['description'] = $description;
        $response['rate'] = $rate;
        echo json_encode($response);


    }

    function reset_installments($number_of_installments,$installment_start_date,$defaulterID){
        
        $this->db->where('recovery_id',$defaulterID);
        $this->db->where('recovery_type','corporate');
        $this->db->where('installment_status','not_paid');
        $this->db->delete('tblrecoveries_installments');
        

        $this->db->where('id',$defaulterID);
        $this->db->update('tblcorporate_recoveries',array('number_of_installments'=>$number_of_installments,'installment_start_date'=>date('Y-m-d',strtotime($installment_start_date))));
        $credit_limit = $this->db->get_where('tblcorporate_recoveries',array('id'=>$defaulterID))->row()->outstanding_amount;
        $totalpaid = 0;
        $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblrecoveries_installments` WHERE recovery_id = ? AND installment_status = ? AND recovery_type = ?',array($defaulterID,'paid','corporate'))->row();
        if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
        }

        $output = trim(str_replace(',', '', $credit_limit));
        $output = $output - $totalpaid;
        $installment_amount = round($output / $number_of_installments);
        for ($i=1; $i <= $number_of_installments; $i++) { 
                     
         $installment['installment_date']  =  $installment_start_date;
         $installment['installment_amount']  = $installment_amount;
         $installment['installment_status']  = 'not_paid';

         $installment_start_date = date('Y-m-d',strtotime("+1 months",strtotime($installment_start_date)));
         $this->corporate_recoveries_model->add_installment($installment,$defaulterID);

        }  
        return false;

    }

    public function change_installment_status($id){
        
        /*if ($this->input->is_ajax_request()) {
            $this->db->where('id',$id);
            $this->db->
        }*/

    }
    public function save_longitude_and_latitude($client_id)
    {
        if (!has_permission('corporate_recovery', '', 'edit')) {
            if (!is_recovery_admin($client_id)) {
                ajax_access_denied();
            }
        }

        $this->db->where('id', $client_id);
        $this->db->update('tblcorporate_recoveries', array(
            'longitude'=>$this->input->post('longitude'),
            'latitude'=>$this->input->post('latitude'),
            'google_map_url'=>$this->input->post('google_map_url'),
        ));
        if ($this->db->affected_rows() > 0) {
            echo 'success';
        } else {
            echo 'false';
        }
    }

        /* Add new task or update existing */
    public function partner($id = '')
    {
        /*if (!has_permission('tasks', '', 'edit') && !has_permission('tasks', '', 'create')) {
            access_denied('Tasks');
        }*/

        $data = array();
       
        if ($this->input->post()) {
            $data                = $this->input->post();
            if ($id == '') {
                
                $id      = $this->corporate_recoveries_model->add_partner($data);
                $_id     = false;
                $success = false;
                $message = '';
                if ($id) {
                    $success = true;
                    $_id     = $id;
                    $message = _l('added_successfully', _l('partner'));
                    
                }
                /*echo json_encode(array(
                    'success' => $success,
                    'id' => $_id,
                    'message' => $message,
                ));*/
                set_alert('success', _l('added_successfully', _l('partner')));
                redirect(admin_url('corporate_recoveries/corporate_recovery/' . $data['recovery_id'].'?tab=partners'));
            } else {
                if (!has_permission('tasks', '', 'edit')) {
                    header('HTTP/1.0 400 Bad error');
                    echo json_encode(array(
                        'success' => false,
                        'message' => _l('access_denied'),
                    ));
                    die;
                }
                $success = $this->corporate_recoveries_model->update_partner($data, $id);
                set_alert('success', _l('updated_successfully', _l('partner')));
                redirect(admin_url('corporate_recoveries/corporate_recovery/' . $data['recovery_id'].'?tab=partners'));
            }
            die;
        }

        
        if ($id == '') {
            $title = _l('add_new', _l('task_lowercase'));
        } else {
            $data['partner'] = $this->corporate_recoveries_model->get_partner($id);
            
            $title = _l('edit', _l('partner'));
        }
        $data['id']    = $id;
        $data['title'] = $title;
        $data['recovery_id'] = 5;
        $this->load->view('admin/recoveries/modals/partner', $data);
    }

    public function delete_partner($customer_id, $id)
    {
        if (!has_permission('corporate_recovery', '', 'delete')) {
            if (!is_recovery_admin($customer_id)) {
                access_denied('recoveries');
            }
        }

        $this->corporate_recoveries_model->delete_partner($id);
         set_alert('success', _l('deleted', _l('partner')));
         redirect(admin_url('corporate_recoveries/corporate_recovery/' . $customer_id.'/'.$id.'?tab=partners'));
    }

     public function installments($client_id)
    {
        $this->app->get_table_data('my_recoveries_installments', array(
            'client_id' => $client_id,
        ));
    }

    public function download_sample(){
$i = 0;

   $not_importable_partner = $this->not_importable_partner;
  $partner_db_fields = $this->db->list_fields('tblrecoveries_partners');
        //if($this->input->post('download_sample') === 'true'){
  $_total_sample_fields = 0;
  header("Pragma: public");
  header("Expires: 0");
  header('Content-Type: application/csv');
  header("Content-Disposition: attachment; filename=\"partner_sample_import_file.csv\";");
  header("Content-Transfer-Encoding: binary");
  
  foreach($partner_db_fields as $field){
    if(in_array($field,$not_importable_partner)){continue;}
    echo '"'.ucfirst(_l($field)).'",';
    $_total_sample_fields++;
  }
  

  echo "\n";
  $sample_data = 'Sample Data';
  for($f = 0;$f<$_total_sample_fields;$f++){
   echo '"'.$sample_data.'",';
 }
 echo "\n";
 exit;
//}
    }


     public function import_partner()
    {
        if (!has_permission('corporate_recovery', '', 'create')) {
            access_denied('corporate_recoveries');
        }
        $country_fields = array('country');
        $simulate_data  = array();
        $total_imported = 0;
        if ($this->input->post()) {
            $recovery_id = $this->input->post('recovery_id');
            if (isset($_FILES['file_csv']['name']) && $_FILES['file_csv']['name'] != '') {
                // Get the temp file path
                $tmpFilePath = $_FILES['file_csv']['tmp_name'];
                // Make sure we have a filepath
                if (!empty($tmpFilePath) && $tmpFilePath != '') {
                    // Setup our new file path
                    $newFilePath = TEMP_FOLDER . $_FILES['file_csv']['name'];
                    if (!file_exists(TEMP_FOLDER)) {
                        mkdir(TEMP_FOLDER, 777);
                    }
                    if (move_uploaded_file($tmpFilePath, $newFilePath)) {
                        $import_result = true;
                        $fd            = fopen($newFilePath, 'r');
                        $rows          = array();
                        while ($row = fgetcsv($fd)) {
                            $rows[] = $row;
                        }

                        $data['total_rows_post'] = count($rows);
                        fclose($fd);
                        if (count($rows) <= 1) {
                            set_alert('warning', 'Not enought rows for importing');
                            redirect(admin_url('corporate_recoveries/import'));
                        }
                        unset($rows[0]);
                        if ($this->input->post('simulate')) {
                            if (count($rows) > 500) {
                                set_alert('warning', 'Recommended splitting the CSV file into smaller files. Our recomendation is 500 row, your CSV file has ' . count($rows));
                            }
                        }
                        
                        $i                      = 0;
                        
                        $db_temp_fields = $this->db->list_fields('tblrecoveries_partners');
                        
                        $db_fields      = array();
                        foreach ($db_temp_fields as $field) {
                            if (in_array($field, $this->not_importable_partner)) {
                                continue;
                            }
                            $db_fields[] = $field;
                        }

                        //print_r($db_fields);
                        $_row_simulate = 0;

                        $required = array(
                            'name',
                        );

                        /*if (get_option('company_is_required') == 1) {
                            array_push($required, 'company');
                        }*/

                        foreach ($rows as $row) {
                            // do for db fields
                            $insert    = array();
                            $duplicate = false;
                            for ($i = 0; $i < count($db_fields); $i++) {
                                if (!isset($row[$i])) {
                                    continue;
                                }


                                
                                // Avoid errors on required fields;
                                if (in_array($db_fields[$i], $required) && $row[$i] == '' && $db_fields[$i] != 'company') {
                                    $row[$i] = '/';
                                } 
                                if($row[$i] === 'NULL' || $row[$i] === 'null') {
                                    $row[$i] = '';
                                }
                                $insert[$db_fields[$i]] = $row[$i];
                            }


                            if ($duplicate == true) {
                                continue;
                            }
                            if (count($insert) > 0) {
                                $total_imported++;
                               
                                if (!$this->input->post('simulate')) {
                                    //$insert['donotsendwelcomeemail'] = true;
                                    foreach ($insert as $key =>$val) {
                                        $insert[$key] = trim($val);
                                    }

                                    $insert['recovery_id']  = $this->db->get_where('tblcorporate_recoveries',array('cif_id'=>$insert['cif_id']))->row()->id;
                                
                                    $clientid      = $this->corporate_recoveries_model->add_partner($insert, true);

                                    
                                } else {
                                    foreach ($country_fields as $country_field) {
                                        if (array_key_exists($country_field, $insert)) {
                                            if ($insert[$country_field] != 0) {
                                                $c = get_country($insert[$country_field]);
                                                if ($c) {
                                                    $insert[$country_field] = $c->short_name;
                                                }
                                            } elseif ($insert[$country_field] == 0) {
                                                $insert[$country_field] = '';
                                            }
                                        }
                                    }
                                    $simulate_data[$_row_simulate] = $insert;
                                    $clientid                      = true;
                                }
                               
                            }
                            $_row_simulate++;
                            if ($this->input->post('simulate') && $_row_simulate >= 100) {
                                break;
                            }
                        }
                        unlink($newFilePath);
                    }
                } else {
                    set_alert('warning', _l('import_upload_failed'));
                }
            }
        }
        if (count($simulate_data) > 0) {
            $data['simulate'] = $simulate_data;
        }
        if (isset($import_result)) {
            set_alert('success', _l('import_total_imported', $total_imported));
        }
        redirect(admin_url('corporate_recoveries/corporate_recovery/' . $recovery_id . '?tab=partners'));

    }


    public function assign_admins($id)
    {
        if (!has_permission('corporate_recovery', '', 'create') && !has_permission('corporate_recovery', '', 'edit')) {
            access_denied('corporate_recoveries');
        }
        $success = $this->corporate_recoveries_model->assign_admins($this->input->post(), $id);
        if ($success == true) {
            set_alert('success', _l('updated_successfully', _l('corporate_recovery')));
        }

        redirect(admin_url('corporate_recoveries/corporate_recovery/' . $id . '?tab=customer_admins'));
    }

    public function delete_customer_admin($customer_id, $staff_id)
    {
        if (!has_permission('corporate_recovery', '', 'create') && !has_permission('corporate_recovery', '', 'edit')) {
            access_denied('corporate_recoveries');
        }

        $this->db->where('customer_id', $customer_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete('tblrecoveryadmins');
        redirect(admin_url('corporate_recoveries/corporate_recovery/'.$customer_id).'?tab=customer_admins');
    }


    public function save_demand_notice($defaulter_id){
       if ($this->input->post()) {
            $success = $this->corporate_recoveries_model->save_demand_notice($this->input->post(), $defaulter_id);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('demand_notice')));
            }
            redirect(admin_url('corporate_recoveries/corporate_recovery/'.$defaulter_id).'?group=demand_notice');
        } 
    }

    public function generate_demand_notice($id){
        if (!has_permission('debt_recovery', '', 'view') && !has_permission('debt_recovery', '', 'view_own')) {
            access_denied('View');
        }
        $payment = $this->corporate_recoveries_model->get($id);
        $payment->demand_notice = $this->corporate_recoveries_model->get_demand_notice($id);
        $this->load->model('clients_model');
        $payment->clientdata = $this->clients_model->get($payment->client_id);
        $payment->clientdata->clientid = $payment->client_id;
        $payment->clientdata->client->company = $payment->clientdata->company;
        //$client_details = format_customer_info($payment->clientdata, 'payment', 'billing');
        
        try {
           $paymentpdf            = demand_notice_pdf($payment);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type                  = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $paymentpdf->Output(mb_strtoupper(slug_it(_l('settlement_document') . '-' . $payment->id)) . '.pdf', $type);
    }


    public function send_demand_notice($id)
    {
        if ($this->input->post('send_file_email')) {
            //if ($this->input->post('file_path')) {
                $this->load->model('emails_model');
                
                $payment = $this->corporate_recoveries_model->get($id);
                //$payment->demand_notice = $this->corporate_recoveries_model->get_demand_notice($id);
                $this->load->model('clients_model');
                $payment->clientdata = $this->clients_model->get($payment->client_id);
                $payment->clientdata->clientid = $payment->client_id;
                $payment->clientdata->client->company = $payment->clientdata->company;

                if ($this->input->post('attach_pdf')) {
                    /*$pdf    = demand_notice_pdf($payment);
                    $attach = $pdf->Output(slug_it($payment->full_name) . '-Demand Notice.pdf', 'S');
                    $this->emails_model->add_attachment(array(
                        'attachment' => $attach,
                        'filename' => slug_it($payment->full_name) . 'Demand Notice.pdf',
                        'type' => 'application/pdf',
                    ));*/
                    $attach = get_upload_path_by_type('recovery').$id.'/Demand Notice.docx';

                    //$attach = 'E:\xampp\htdocs\a2z\uploads/recovery/4/Demand Notice.docx';
                    $this->emails_model->add_attachment(array(
                        'attachment' => $attach,
                        'filename' => 'Demand Notice.docx',
                        'type' => 'application/vnd.openxmlformats-officedoc',
                        'read' => true,
                    ));

                }
                $message = $this->input->post('send_file_message');
                $message = nl2br($message);
                $success = $this->emails_model->send_simple_email($this->input->post('send_file_email'), $this->input->post('send_file_subject'), $message);
                if ($success) {
                    set_alert('success', _l('custom_file_success_send', $this->input->post('send_file_email')));
                } else {
                    set_alert('warning', _l('custom_file_fail_send'));
                }
            //}
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

    public function bulk_assign()
    {
        do_action('before_do_bulk_action_for_customers');
        $total_assigned = 0;
        if ($this->input->post()) {
            $ids    = $this->input->post('ids');
            $staff_id = $this->input->post('staff_id');
            if (is_array($ids)) {
                foreach ($ids as $id) {
                    if ($this->input->post('mass_assign')) {
                        $data['customer_id'] = $id;
                        $data['customer_admins'] = [$staff_id];
                        //$data['customer_admins'] = $this->debt_collections_model->get_customers_admin_unique_ids();
                        if ($this->corporate_recoveries_model->mass_assign_admins($data,$id)) {
                            $total_assigned++;
                        }
                    } 
                }
            }
        }

        if ($this->input->post('mass_assign')) {
            set_alert('success', _l('total_defaulters_assigned', $total_assigned));
        }
    }


    public function verify_installment($id, $status){
        if ($this->input->is_ajax_request()) {
            $this->corporate_recoveries_model->verify_installment($id, $status);
        }
    }

    public function send_notify_email($id)
    {
        if ($this->input->post('send_file_subject')) {
            //if ($this->input->post('file_path')) {
            $this->load->model('emails_model');
            $installment_id = $this->input->post('installment_id');
            $defaulter_name = get_recovers_name($id);
            $installment    = $this->corporate_recoveries_model->get_installment($installment_id);
            $message = 'Full Name :'.$defaulter_name
                    .'<br>Installment Amount :'.$installment->installment_amount
                    .'<br>Installment Date :'.$installment->installment_date
                    .'<br>Remarks :'.nl2br($installment->remarks);
            $staff_ids = $this->input->post('notify_staff');
            $emails = '';
            foreach ($staff_ids as $staff) {
                $staff_email = $this->db->get_where('tblstaff',array('staffid'=>$staff))->row()->email;
                $emails .= $staff_email .',';
                $success = $this->emails_model->send_simple_email($staff_email, $this->input->post('send_file_subject'), $message);
            }       
            
            
            if ($success) {
                set_alert('success', _l('email_send_to', $emails));
            } else {
                set_alert('warning', _l('fail_send'));
            }
            //}
        }
        redirect($_SERVER['HTTP_REFERER']);
    }

     public function settlement_form($id){
        if (!has_permission('Corporate_recoveries', '', 'view') && !has_permission('Corporate_recoveries', '', 'view_own')) {
            access_denied('View');
        }
        $payment = $this->corporate_recoveries_model->get($id);
        $this->load->model('clients_model');
        $payment->clientdata = $this->clients_model->get($payment->client_id);
        $payment->clientdata->clientid = $payment->client_id;
        $payment->clientdata->client->company = $payment->clientdata->company;
        //$client_details = format_customer_info($payment->clientdata, 'payment', 'billing');
        $payment->recovery_type  = 'corporate';
        $payment->curr_os  = $payment->outstanding_amount;
        
        try {
           $paymentpdf            = settlement_pdf($payment);
        } catch (Exception $e) {
            $message = $e->getMessage();
            echo $message;
            if (strpos($message, 'Unable to get the size of the image') !== false) {
                show_pdf_unable_to_get_image_size_error();
            }
            die;
        }

        $type                  = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $paymentpdf->Output(mb_strtoupper(slug_it(_l('settlement_document') . '-' . $payment->id)) . '.pdf', $type);
    }

    public function add_product($defaulter_id){
       if ($this->input->post()) {
            $success = $this->corporate_recoveries_model->add_product($this->input->post(), $defaulter_id);
            if ($success) {
                set_alert('success', _l('added_successfully', _l('product')));
            }
            redirect(admin_url('corporate_recoveries/corporate_recovery/'.$defaulter_id).'?tab=debtproducts');
        } 
    }
    
    public function delete_product($customer_id, $id)
    {
        if (!has_permission('Corporate_recoveries', '', 'delete')) {
            if (!is_debt_admin($customer_id)) {
                access_denied('corporate_recoveries');
            }
        }

        $this->corporate_recoveries_model->delete_product($id);
        set_alert('success', _l('Deleted', _l('product')));
        redirect(admin_url('Corporate_recoveries/Corporate_recovery/' . $customer_id . '?tab=debtproducts'));
    }


    public function generate_demand_notice_word($id){
        
        require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
        
        $proposal = $this->corporate_recoveries_model->get($id);

        $clients_data = $this->clients_model->get($proposal->client_id);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('temp/Vetted_Demand_Letter.docx');
        
        
        $date = date('d-m-Y');
        $debtors_company_name = $proposal->debtor_title;
        $address     = nl2br($proposal->address);
        $breaks = array("<br />","<br>","<br/>"); 
        $address  = str_ireplace($breaks,'</w:t><w:br/><w:t>', $address);
        $address = str_replace('&','&amp;', $address) ;
        $contactno   = $proposal->mobile_no;
        $email       = $proposal->email_id;
        $outstanding_amount = app_format_money($proposal->outstanding_amount,get_option('default_currency'));
        $client_company_name = str_replace('&','&amp;', $clients_data->company) ;
        $emirate   = $clients_data->state;
        $country   = get_country_short_name($clients_data->country);

        /*$this->load->library('numberword', array(
        'clientid' => $proposal->client_id,
        ));
        $amount_in_words = $this->numberword->convert($proposal->outstanding_amount,'','Fils');*/

        //$staff_initial = get_staff_initial();
        $ref_no = 'Ref/Dn/'.$proposal->file_no;
        $templateProcessor->setValue('date',$date);
        $templateProcessor->setValue('debtors_company_name',$debtors_company_name);
        $templateProcessor->setValue('address',$address);
        $templateProcessor->setValue('contactno',$contactno);
        $templateProcessor->setValue('email',$email);
        $templateProcessor->setValue('outstanding_amount',$outstanding_amount);
        $templateProcessor->setValue('client_company_name',$client_company_name);
        $templateProcessor->setValue('emirate',$emirate);
        $templateProcessor->setValue('country',$country);
        //$templateProcessor->setValue('amount_in_words',$amount_in_words);
        $templateProcessor->setValue('ref_no',$ref_no);
        $templateProcessor->setValue('companyname',get_option('invoice_company_name'));

    

        //
        $path  = get_upload_path_by_type('corporate_recovery').$id.'/';
        _maybe_create_upload_path($path);
        if(file_exists($path.'Demand Notice.docx')){
            unlink($path.'Demand Notice.docx');
        }
        $templateProcessor->saveAs($path.'Demand Notice.docx');

        set_alert('success', _l('generated', _l('demand_notice')));
        redirect(admin_url('corporate_recoveries/corporate_recovery/' . $id . '?group=demand_notice'));
    }

    public function autocomplete_debtor(){

        echo json_encode( $this->corporate_recoveries_model->search_debtor($this->input->post('term')));
    }

}
