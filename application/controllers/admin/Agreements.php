<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Agreements extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('agreements_model');
        $this->load->model('currencies_model');
    }

    public function index($proposal_id = '')
    {
        $this->list_agreements($proposal_id);
    }

    public function list_agreements($proposal_id = '')
    {
        close_setup_menu();

        if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && get_option('allow_staff_view_proposals_assigned') == 0) {
            access_denied('proposals');
        }

        $isPipeline = $this->session->userdata('proposals_pipeline') == 'true';

        if ($isPipeline && !$this->input->get('status')) {
            $data['title']           = _l('proposals_pipeline');
            $data['bodyclass']       = 'proposals-pipeline';
            $data['switch_pipeline'] = false;
            // Direct access
            if (is_numeric($proposal_id)) {
                $data['proposalid'] = $proposal_id;
            } else {
                $data['proposalid'] = $this->session->flashdata('proposalid');
            }

            $this->load->view('admin/agreements/pipeline/manage', $data);
        } else {

            // Pipeline was initiated but user click from home page and need to show table only to filter
            if ($this->input->get('status') && $isPipeline) {
                $this->pipeline(0, true);
            }

            $data['proposal_id'] = $proposal_id;
            $data['switch_pipeline']       = true;
            $data['title']                 = _l('agreements');
            $data['statuses']              = $this->agreements_model->get_statuses();
            $data['proposals_sale_agents'] = $this->agreements_model->get_sale_agents();
            $data['years']                 = $this->agreements_model->get_proposals_years();
            $this->load->view('admin/agreements/manage', $data);
        }
    }

    public function table()
    {
        if (!has_permission('proposals', '', 'view')
            && !has_permission('proposals', '', 'view_own')) {
            ajax_access_denied();
        }
        $this->app->get_table_data('agreements');
    }

    public function proposal_relations($rel_id, $rel_type)
    {
        $this->app->get_table_data('proposals_relations', array(
            'rel_id' => $rel_id,
            'rel_type' => $rel_type,
        ));
    }

    public function delete_attachment($id)
    {
        $file = $this->misc_model->get_file($id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo $this->agreements_model->delete_attachment($id);
        } else {
            ajax_access_denied();
        }
    }

    public function sync_data()
    {
        if (has_permission('proposals', '', 'create') || has_permission('proposals', '', 'edit')) {
            $has_permission_view = has_permission('proposals', '', 'view');

            $this->db->where('rel_id', $this->input->post('rel_id'));
            $this->db->where('rel_type', $this->input->post('rel_type'));

            if (!$has_permission_view) {
                $this->db->where('addedfrom', get_staff_user_id());
            }

            $address = trim($this->input->post('address'));
            $address = nl2br($address);
            $this->db->update('tblproposals', array(
                'phone' => $this->input->post('phone'),
                'zip' => $this->input->post('zip'),
                'country' => $this->input->post('country'),
                'state' => $this->input->post('state'),
                'address' => $address,
                'city' => $this->input->post('city'),
            ));

            if ($this->db->affected_rows() > 0) {
                echo json_encode(array(
                    'message' => _l('all_data_synced_successfully'),
                ));
            } else {
                echo json_encode(array(
                    'message' => _l('sync_proposals_up_to_date'),
                ));
            }
        }
    }

    public function agreement($id = '')
    {
        if ($this->input->post()) {
            $proposal_data = $this->input->post();
            if ($id == '') {
                if (!has_permission('proposals', '', 'create')) {
                    access_denied('proposals');
                }
                $id = $this->agreements_model->add($proposal_data);
                if ($id) {
                    //$this->makeWord($id,$proposal_data['rel_type'],$proposal_data['rel_id']);
                  
                    $this->generate_service_agreement($id,$proposal_data['rel_type'],$proposal_data['rel_id']);
                    set_alert('success', _l('added_successfully', _l('service_agreement')));
                    
                    redirect(admin_url('agreements/list_agreements/' . $id));
                
                }
            } else {
                if (!has_permission('proposals', '', 'edit')) {
                    access_denied('agreements');
                }
                $success = $this->agreements_model->update($proposal_data, $id);
                $this->generate_service_agreement($id,$proposal_data['rel_type'],$proposal_data['rel_id']);
                if ($success) {
                    //$this->makeWord($id,$proposal_data['rel_type'],$proposal_data['rel_id']);
                    set_alert('success', _l('updated_successfully', _l('service_agreement')));
                }
        
                redirect(admin_url('agreements/list_agreements/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('agreement'));
        } else {
            $data['proposal'] = $this->agreements_model->get($id);

            if (!$data['proposal'] ) {
                blank_page(_l('proposal_not_found'));
            }

            $data['estimate']    = $data['agreement'];
            $data['is_proposal'] = true;
            $title               = _l('edit', _l('agreement'));
        }

        $this->load->model('taxes_model');
        $data['taxes'] = $this->taxes_model->get();
        $this->load->model('invoice_items_model');
        $data['ajaxItems'] = false;
        if (total_rows('tblitems') <= ajax_on_total_items()) {
            $data['items']        = $this->invoice_items_model->get_grouped();
        } else {
            $data['items'] = array();
            $data['ajaxItems'] = true;
        }
        $data['items_groups'] = $this->invoice_items_model->get_groups();

        $data['statuses']   = $this->agreements_model->get_statuses();
        $data['staff']      = $this->staff_model->get('', 1);
        $data['currencies'] = $this->currencies_model->get();
        $data['base_currency'] = $this->currencies_model->get_base_currency();

        $this->load->model('proposals_model');
        $data['proposals'] = $this->proposals_model->get();


        $data['title']      = $title;
        $this->load->view('admin/agreements/agreement', $data);
    }


    public function generate_service_agreement($proposal_id,$rel_type,$rel_id){
        
        require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
        
        $proposal = $this->agreements_model->get($proposal_id);
        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor('temp/Vetted_Service_Agreement.docx');
    
        

        /*$phpWord = \PhpOffice\PhpWord\IOFactory::load('temp/Fee Proposal[LC].docx');
        $phpWord->setDefaultFontSize(50);
        $phpWord->setDefaultFontName('courier');*/
        $authorized_name = '';
        if($rel_type=='customer'){
            $client_name =  get_company_name($rel_id) ;
            $client_details = $this->clients_model->get($rel_id);
            $client_name = $client_details->company;
            $po_box = $client_details->zip;
            $city   = $client_details->city;
            $country = '';
            $email = '';
            $mobile = $client_details->phonenumber;
            $tel_no = $client_details->phonenumber;
            $authorized_name = $client_details->company;
            
        }else{
            $lead_details = $this->db->get_where('tblleads',array('id'=>$rel_id))->row();
            $client_name = $lead_details->name;
            $po_box      = $lead_details->zip;
            $city        = $lead_details->city;
            $country     = get_coutry_name($lead_details->country);
            $email       = $lead_details->email;
            $mobile      = $lead_details->phonenumber;
            $tel_no      = $lead_details->phonenumber;
        }

        $client_name = str_replace('&','&amp;', $client_name) ;
        $templateProcessor->setValue('client_name',$client_name);
        $templateProcessor->setValue('po_box',$po_box);
        $templateProcessor->setValue('city',$city);
        $templateProcessor->setValue('country',$country);
        $templateProcessor->setValue('email',$email);
        $templateProcessor->setValue('mobile',$mobile);
        $templateProcessor->setValue('tel_no',$tel_no);
        $templateProcessor->setValue('debtor_name',$proposal->debtor_name);
        $templateProcessor->setValue('outstanding_amount',$proposal->outstanding_amount);
        $templateProcessor->setValue('age_of_debt',$proposal->age_of_debt);
        $templateProcessor->setValue('debtor_address',$proposal->debtor_address);
        $templateProcessor->setValue('contact_no',$proposal->debtor_contact_details);
        $templateProcessor->setValue('debtor_email',$proposal->email_id);
        if($proposal->client_contact_name == ''){
            $proposal->client_contact_name = $authorized_name;
        }
        $templateProcessor->setValue('contact_person',$proposal->client_contact_name);
        $templateProcessor->setValue('agreement_validity',$proposal->valid_for);
        $templateProcessor->setValue('registration_fee',app_format_number($proposal->registration_fee));
        //$this->with_number_to_word($rel_id);
       /* $this->load->library('numberword', array(
        'clientid' => $rel_id,
        ));*/
        //$amount_in_words = $this->numberword->convert($proposal->registration_fee,'','Fils');
        //$templateProcessor->setValue('total_in_words',$amount_in_words);
        $pro_fees =  json_decode($proposal->professional_fee);
        $templateProcessor->setValue('fee1',$pro_fees[0]);
        $templateProcessor->setValue('fee2',$pro_fees[1]);
        $templateProcessor->setValue('fee3',$pro_fees[2]);
        $templateProcessor->setValue('fee4',$pro_fees[3]);
        $templateProcessor->setValue('date',_d($proposal->date));
        $templateProcessor->setValue('signed_staff',get_staff_full_name($proposal->assigned));

        $pro_fee_amounts =  json_decode($proposal->professional_fee_amounts);
        $templateProcessor->setValue('fee_amount1',$pro_fee_amounts[0]);
        $templateProcessor->setValue('fee_amount2',$pro_fee_amounts[1]);
        $templateProcessor->setValue('fee_amount3',$pro_fee_amounts[2]);
        $templateProcessor->setValue('fee_amount4',$pro_fee_amounts[3]);


         // Table-----------

        $document_with_table = new \PhpOffice\PhpWord\PhpWord();
        $section = $document_with_table->addSection();
        $tableStyle = array( 'borderSize' => 3, 
            'borderColor' => '000000', 
            //'afterSpacing' => 10, 
            'Spacing'=> 10, 
            'cellMargin'=> 10);

        $myTheadFontStyle = [
            'name' => 'Calibri',
            'size' => '10',
            'color' => 'white',
            //'bold' => true,
            //'italic' => true
            //'bgColor'=>'#D51717'
        ];
        $myFontStyle = [
            'name' => 'Calibri ',
            'size' => '10',
            'color' => '000080',
            'bold' => false,
            //'italic' => true

        ];
        $myParagraphStyle = array();
        $myRow = array('bgColor'=>'#D51717');
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
        $table->addCell($item_width,$myRow)->addText("DEBTOR",$myTheadFontStyle,$align_center);
        $table->addCell($no_width,$myRow)->addText("OUTSTANDING",$myTheadFontStyle,$align_center);
        $table->addCell($no_width,$myRow)->addText("AGE OF THE DEBT",$myTheadFontStyle,$align_center);
        $table->addCell($item_width,$myRow)->addText("DEBTOR's PHYSYCAL ADDRESS",$myTheadFontStyle,$align_center);
        $table->addCell($item_width,$myRow)->addText("CONTACT DETAILS",$myTheadFontStyle,$align_center);
        $table->addCell($item_width,$myRow)->addText("E-MAIL ID",$myTheadFontStyle,$align_center);


        $debtor_name = json_decode($proposal->debtor_name);
        $outstanding_amount = json_decode($proposal->outstanding_amount);
        $age_of_debt = json_decode($proposal->age_of_debt);
        $debtor_address = json_decode($proposal->debtor_address);
        $debtor_contact_details =  json_decode($proposal->debtor_contact_details);
        $email_id =json_decode($proposal->email_id);
        $t=0;
        foreach ($debtor_name as $value) {
            $table->addRow();
            $table->addCell($item_width)->addText($debtor_name[$t],'',$align_center);
            $table->addCell($item_width)->addText($outstanding_amount[$t],'',$align_center);
            $table->addCell($item_width)->addText($age_of_debt[$t],'',$align_center);
            $table->addCell($item_width)->addText($debtor_address[$t],'',$align_center);
            $table->addCell($item_width)->addText($debtor_contact_details[$t],'',$align_center);
            $table->addCell($item_width)->addText($email_id[$t],'',$align_center);
            $t++;
        }
        
         // Create writer to convert document to xml
        $objWriter = \PhpOffice\PhpWord\IOFactory::createWriter($document_with_table, 'Word2007');
        // Get all document xml code
        $fullxml = $objWriter->getWriterPart('Document')->write();
        // Get only table xml code
        $tablexml = preg_replace('/^[\s\S]*(<w:tbl\b.*<\/w:tbl>).*/', '$1', $fullxml);
        // Replace mark by xml code of table
        $templateProcessor->setValue('debtor_details_table', $tablexml);
        //debtor_details_table
        $path        = get_upload_path_by_type('service_agreement').$proposal_id.'/';
        _maybe_create_upload_path($path);
        if(file_exists($path.'Service_Agreement.docx'))
            unlink($path.'Service_Agreement.docx');
        $templateProcessor->saveAs($path.'Service_Agreement.docx');

    }

    public function get_proposal_data_ajax($id, $to_return = false)
    {
        if (!has_permission('proposals', '', 'view') && !has_permission('proposals', '', 'view_own') && get_option('allow_staff_view_proposals_assigned') == 0) {
            echo _l('access_denied');
            die;
        }

        $proposal = $this->agreements_model->get($id, array(), true);

        if (!$proposal) {
            echo _l('proposal_not_found');
            die;
        } else {
            if (!$this->user_can_view_proposal($id)) {
                echo _l('proposal_not_found');
                die;
            }
        }

        $template_name         = 'proposal-send-to-customer';
        $data['template_name'] = $template_name;

        $this->db->where('slug', $template_name);
        $this->db->where('language', 'english');
        $template_result = $this->db->get('tblemailtemplates')->row();

        $data['template_system_name'] = $template_result->name;
        $data['template_id'] = $template_result->emailtemplateid;

        $data['template_disabled'] = false;
        if (total_rows('tblemailtemplates', array('slug'=>$data['template_name'], 'active'=>0)) > 0) {
            $data['template_disabled'] = true;
        }

        define('EMAIL_TEMPLATE_PROPOSAL_ID_HELP', $proposal->id);

        $data['template']      = get_email_template_for_sending($template_name, $proposal->email);

        $proposal_merge_fields  = get_available_merge_fields();
        $_proposal_merge_fields = array();
        array_push($_proposal_merge_fields, array(
            array(
                'name' => 'Items Table',
                'key' => '{proposal_items}',
            ),
        ));
        foreach ($proposal_merge_fields as $key => $val) {
            foreach ($val as $type => $f) {
                if ($type == 'proposals') {
                    foreach ($f as $available) {
                        foreach ($available['available'] as $av) {
                            if ($av == 'proposals') {
                                array_push($_proposal_merge_fields, $f);
                                break;
                            }
                        }
                        break;
                    }
                } elseif ($type == 'other') {
                    array_push($_proposal_merge_fields, $f);
                }
            }
        }
        $data['proposal_statuses']     = $this->agreements_model->get_statuses();
        $data['members']               = $this->staff_model->get('', 1);
        $data['proposal_merge_fields'] = $_proposal_merge_fields;
        $data['proposal']              = $proposal;
        if ($to_return == false) {
            $this->load->view('admin/agreements/proposals_preview_template', $data);
        } else {
            return $this->load->view('admin/agreements/proposals_preview_template', $data, true);
        }
    }


   
    public function delete($id)
    {
        if (!has_permission('proposals', '', 'delete')) {
            access_denied('proposals');
        }
        $response = $this->agreements_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('proposal')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('proposal_lowercase')));
        }
        redirect(admin_url('proposals'));
    }

    public function get_relation_data_values($rel_id, $rel_type)
    {
        echo json_encode($this->agreements_model->get_relation_data_values($rel_id, $rel_type));
    }
    public function get_sales_managr_ph_email($rel_id)
    {
        echo json_encode($this->agreements_model->get_sales_managr_ph_email($rel_id));
    }


    public function save_proposal_data()
    {
        if (!has_permission('proposals', '', 'edit') && !has_permission('proposals', '', 'create')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode(array(
                'success' => false,
                'message' => _l('access_denied'),
            ));
            die;
        }
        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('proposal_id'));
        $this->db->update('tblproposals', array(
            'content' => $this->input->post('content', false),
        ));

        if ($this->db->affected_rows() > 0) {
            $success = true;
            $message = _l('updated_successfully', _l('proposal'));
        }

        echo json_encode(array(
            'success' => $success,
            'message' => $message,
        ));
    }
    
}
