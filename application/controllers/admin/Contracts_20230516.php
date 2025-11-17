<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Contracts extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('contracts_model');
    }

    /* List all contracts */
    public function index()
    {
        close_setup_menu();

        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        $data['expiring']               = $this->contracts_model->get_contracts_about_to_expire(get_staff_user_id());
        $data['count_active']           = count_active_contracts();
        $data['count_expired']          = count_expired_contracts();
        $data['count_recently_created'] = count_recently_created_contracts();
        $data['count_trash']            = count_trash_contracts();
        $data['chart_types']            = json_encode($this->contracts_model->get_contracts_types_chart_data());
        $data['chart_types_values']     = json_encode($this->contracts_model->get_contracts_types_values_chart_data());
        $data['contract_types']         = $this->contracts_model->get_contract_types();
        $data['years']                  = $this->contracts_model->get_contracts_years();
        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['title']         = _l('contracts');
        $this->load->view('admin/contracts/manage', $data);
    }

    public function table($clientid = '',$opposite_party='')
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('contracts', [
            'clientid' => $clientid,
			'opposite_party'=> $opposite_party
        ]);
    }

    /* Edit contract or add new contract */
    public function contract($id = '')
    {
        if ($this->input->post()) {
            if ($id == '') {
                if (!has_permission('contracts', '', 'create')) {
                    access_denied('contracts');
                }
                $id = $this->contracts_model->add($this->input->post());
                if ($id) {
                    set_alert('success', _l('added_successfully', _l('contract')));
                    redirect(admin_url('contracts/contract/' . $id));
                }
            } else {
                if (!has_permission('contracts', '', 'edit')) {
                    access_denied('contracts');
                }
                $success = $this->contracts_model->update($this->input->post(), $id);
                if ($success) {
                    set_alert('success', _l('updated_successfully', _l('contract')));
                }
                redirect(admin_url('contracts/contract/' . $id));
            }
        }
        if ($id == '') {
            $title = _l('add_new', _l('contract_lowercase'));
        } else {
            $data['contract']                 = $this->contracts_model->get($id, [], true);
            $data['contract_renewal_history'] = $this->contracts_model->get_contract_renewal_history($id);
            $data['totalNotes']               = total_rows(db_prefix() . 'notes', ['rel_id' => $id, 'rel_type' => 'contract']);
            if (!$data['contract'] || (!has_permission('contracts', '', 'view') && $data['contract']->addedfrom != get_staff_user_id())) {
                blank_page(_l('contract_not_found'));
            }

            $data['contract_merge_fields'] = $this->app_merge_fields->get_flat('contract', ['other', 'client'], '{email_signature}');

            $title = $data['contract']->subject;

            $data = array_merge($data, prepare_mail_preview_data('contract_send_to_customer', $data['contract']->client));
            
        }
		
        $data['tab'] = $this->input->get('tab')?$this->input->get('tab'):'tab_content';
    
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
		if ($this->input->get('party_id')) {
            $data['party_id'] = $this->input->get('party_id');
        }
        $this->load->model('casediary_model');
        $data['oppositeparty_names'] = $this->casediary_model->get_oppositeparty();

        $this->load->model('currencies_model');
        $data['base_currency'] = $this->currencies_model->get_base_currency();
        $data['types']         = $this->contracts_model->get_contract_types();
		$data['clients']		=$this->clients_model->get('',['tblclients.active'=>1]);
		$this->load->model('expenses_model');
		$data['statuses']  = $this->expenses_model-> get_expenses_status();
		$data['project_members'] = $this->contracts_model->get_contract_members($id);
		$data['staff']    = $this->staff_model->get('', ['active' => 1]);
        $data['title']         = $title;
        $data['bodyclass']     = 'contract';
		$this->load->model('tickets_model');
		$data['requests']=$this->tickets_model->get();
		$data['service']='contract';
        $this->load->view('admin/contracts/contract', $data);
    }

    public function get_template()
    {
        $name = $this->input->get('name');
        echo $this->load->view('admin/contracts/templates/' . $name, [], true);
    }

    public function mark_as_signed($id)
    {
        if (!staff_can('edit', 'contracts')) {
            access_denied('mark contract as signed');
        }

        $this->contracts_model->mark_as_signed($id);

        redirect(admin_url('contracts/contract/' . $id));
    }

    public function unmark_as_signed($id)
    {
        if (!staff_can('edit', 'contracts')) {
            access_denied('mark contract as signed');
        }

        $this->contracts_model->unmark_as_signed($id);

        redirect(admin_url('contracts/contract/' . $id));
    }
		 public function legalcontract_approval($id)
    {
       if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('contracts'));
        }
        $legalapprove =   $this->contracts_model->get($id, [], true);
        $legalapprove->approval =  get_approvals($id,'contract',3);
 	
	    try {
            $pdf = legalcontract_approval_pdf($legalapprove);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type     = 'I';
        if ($this->input->get('print')) {
            $type = 'I';
        }
        $pdf->Output(slug_it($legalapprove->subject) . '.pdf', $type);
    } 

    public function pdf($id)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }

        if (!$id) {
            redirect(admin_url('contracts'));
        }

        $contract = $this->contracts_model->get($id);

        try {
            $pdf = contract_pdf($contract);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it($contract->subject) . '.pdf', $type);
    }

    public function send_to_email($id)
    {
        if (!has_permission('contracts', '', 'view') && !has_permission('contracts', '', 'view_own')) {
            access_denied('contracts');
        }
        $success = $this->contracts_model->send_contract_to_client($id, $this->input->post('attach_pdf'), $this->input->post('cc'));
        if ($success) {
            set_alert('success', _l('contract_sent_to_client_success'));
        } else {
            set_alert('danger', _l('contract_sent_to_client_fail'));
        }
        redirect(admin_url('contracts/contract/' . $id));
    }

    public function add_note($rel_id)
    {
        if ($this->input->post() && (has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own'))) {
            $this->misc_model->add_note($this->input->post(), 'contract', $rel_id);
            echo $rel_id;
        }
    }

    public function get_notes($id)
    {
        if ((has_permission('contracts', '', 'view') || has_permission('contracts', '', 'view_own'))) {
            $data['notes'] = $this->misc_model->get_notes($id, 'contract');
            $this->load->view('admin/includes/sales_notes_template', $data);
        }
    }

    public function clear_signature($id)
    {
        if (has_permission('contracts', '', 'delete')) {
            $this->contracts_model->clear_signature($id);
        }

        redirect(admin_url('contracts/contract/' . $id));
    }

    public function save_contract_data()
    {
        if (!has_permission('contracts', '', 'edit')) {
            header('HTTP/1.0 400 Bad error');
            echo json_encode([
                'success' => false,
                'message' => _l('access_denied'),
            ]);
            die;
        }

        $success = false;
        $message = '';

        $this->db->where('id', $this->input->post('contract_id'));
        $this->db->update(db_prefix() . 'contracts', [
                'content' => html_purify($this->input->post('content', false)),
        ]);

        $success = $this->db->affected_rows() > 0;
        $message = _l('updated_successfully', _l('contract'));

        echo json_encode([
            'success' => $success,
            'message' => $message,
        ]);
    }

    public function add_comment()
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->contracts_model->add_comment($this->input->post()),
            ]);
        }
    }

    public function edit_comment($id)
    {
        if ($this->input->post()) {
            echo json_encode([
                'success' => $this->contracts_model->edit_comment($this->input->post(), $id),
                'message' => _l('comment_updated_successfully'),
            ]);
        }
    }

    public function get_comments($id)
    {
        $data['comments'] = $this->contracts_model->get_comments($id);
        $this->load->view('admin/contracts/comments_template', $data);
    }

    public function remove_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get(db_prefix() . 'contract_comments')->row();
        if ($comment) {
            if ($comment->staffid != get_staff_user_id() && !is_admin()) {
                echo json_encode([
                    'success' => false,
                ]);
                die;
            }
            echo json_encode([
                'success' => $this->contracts_model->remove_comment($id),
            ]);
        } else {
            echo json_encode([
                'success' => false,
            ]);
        }
    }

    public function renew()
    {
        if (!has_permission('contracts', '', 'create') && !has_permission('contracts', '', 'edit')) {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            $data    = $this->input->post();
            $success = $this->contracts_model->renew($data);
            if ($success) {
			
                set_alert('success', _l('contract_renewed_successfully'));
            } else {
                set_alert('warning', _l('contract_renewed_fail'));
            }
            redirect(admin_url('contracts/contract/' . $data['contractid'] . '?tab=renewals'));
        }
    }

    public function delete_renewal($renewal_id, $contractid)
    {
        $success = $this->contracts_model->delete_renewal($renewal_id, $contractid);
        if ($success) {
            set_alert('success', _l('contract_renewal_deleted'));
        } else {
            set_alert('warning', _l('contract_renewal_delete_fail'));
        }
        redirect(admin_url('contracts/contract/' . $contractid . '?tab=renewals'));
    }

    public function copy($id)
    {
        if (!has_permission('contracts', '', 'create')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url('contracts'));
        }
        $newId = $this->contracts_model->copy($id);
        if ($newId) {
            set_alert('success', _l('contract_copied_successfully'));
        } else {
            set_alert('warning', _l('contract_copied_fail'));
        }
        redirect(admin_url('contracts/contract/' . $newId));
    }

    /* Delete contract from database */
    public function delete($id)
    {
        if (!has_permission('contracts', '', 'delete')) {
            access_denied('contracts');
        }
        if (!$id) {
            redirect(admin_url('contracts'));
        }
        $response = $this->contracts_model->delete($id);
        if ($response == true) {
            set_alert('success', _l('deleted', _l('contract')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_lowercase')));
        }
        if (strpos($_SERVER['HTTP_REFERER'], 'clients/') !== false) {
            redirect($_SERVER['HTTP_REFERER']);
        } else {
            redirect(admin_url('contracts'));
        }
    }

    /* Manage contract types Since Version 1.0.3 */
    public function type($id = '')
    {
        if (!is_admin() && get_option('staff_members_create_inline_contract_types') == '0') {
            access_denied('contracts');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->contracts_model->add_contract_type($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('contract_type'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
                ]);
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->contracts_model->update_contract_type($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('contract_type'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                ]);
            }
        }
    }

    public function types()
    {
        if (!is_admin()) {
            access_denied('contracts');
        }
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('contract_types');
        }
        $data['title'] = _l('contract_types');
        $this->load->view('admin/contracts/manage_types', $data);
    }

    /* Delete announcement from database */
    public function delete_contract_type($id)
    {
        if (!$id) {
            redirect(admin_url('contracts/types'));
        }
        if (!is_admin()) {
            access_denied('contracts');
        }
        $response = $this->contracts_model->delete_contract_type($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('contract_type_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('contract_type')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('contract_type_lowercase')));
        }
        redirect(admin_url('contracts/types'));
    }

    public function add_contract_attachment($id)
    {
        handle_contract_attachment($id);
    }

    public function add_external_attachment()
    {
        if ($this->input->post()) {
            $this->misc_model->add_attachment_to_database(
                $this->input->post('contract_id'),
                'contract',
                $this->input->post('files'),
                $this->input->post('external')
            );
        }
    }

    public function delete_contract_attachment($attachment_id)
    {
        $file = $this->misc_model->get_file($attachment_id);
        if ($file->staffid == get_staff_user_id() || is_admin()) {
            echo json_encode([
                'success' => $this->contracts_model->delete_contract_attachment($attachment_id),
            ]);
        }
    }
	    public function pagination()
    {

      $q='';
      $contracttype = $status = '';
      $where= false;
      if($this->input->post()){ /*print_r($_POST);*/
        $q= $this->input->post('q');
        if($this->input->post('contract_type') != ' '){
            $contracttype =$this->input->post('contract_type');
        }
        if($this->input->post('status') != ''){
            $status = $this->input->post('status');
        }
      }  
      $this->load->library("pagination");
      $config = array();
      $config["base_url"] = "#";
      $config["total_rows"] = $this->contracts_model->fetch_contract_details_num_rows($q,$contracttype,$status);
      $config["per_page"] = 12;
      $config["uri_segment"] = 4;
      $config["use_page_numbers"] = TRUE;
      $config["full_tag_open"] = '<ul class="pagination contract-page">';
      $config["full_tag_close"] = '</ul>';
      $config["first_tag_open"] = '<li>';
      $config["first_tag_close"] = '</li>';
      $config["last_tag_open"] = '<li>';
      $config["last_tag_close"] = '</li>';
      $config['next_link'] = '&gt;';
      $config["next_tag_open"] = '<li>';
      $config["next_tag_close"] = '</li>';
      $config["prev_link"] = "&lt;";
      $config["prev_tag_open"] = "<li>";
      $config["prev_tag_close"] = "</li>";
      $config["cur_tag_open"] = "<li class='active'><a href='#'>";
      $config["cur_tag_close"] = "</a></li>";
      $config["num_tag_open"] = "<li>";
      $config["num_tag_close"] = "</li>";
      $config["num_links"] = 1;
      $this->pagination->initialize($config);
      $page = $this->uri->segment(4);
      $start = ($page - 1) * $config["per_page"];
      
      
      $output = array(
       'pagination_link'  => $this->pagination->create_links(),
       'project_data'   => $this->contracts_model->fetch_contract_details($q,$config["per_page"], $start,$contracttype,$status),
       'total_cases'=> '<span class="badge badge-success" style="padding: 10px;
    font-size: 15px;"><b>'.$config["total_rows"].'  '._l('contracts').'</b></span>',
      );
      echo json_encode($output);
    }
public function add_contractpdf($id=''){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['contractid'];
                unset($data['contractid']);
			 
             $message         = '';
            $success=handle_project_contract_file_upload($id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('contract')) : '';
					  $updated          = true;
					 $contentfile='';
					$userfile1= $this->db->get_where('tblcontracts',array('id'=>$id))->row()->contract_filename;
					$contentfile=$this->projectvesioncontents($userfile1,$id);

					  $this->db->where( 'id', $id );
					 $this->db->update( db_prefix() . 'contracts', [
					 	'content' => $contentfile,
					 //	'dateapproved' => date( 'Y-m-d H:i:s' ),
					 ] );
					 }else{
					 
					  $message = 'Chaeck Image Image extension not allowed';
					  $updated          = false;
				 }
                if ($success) {
					$message= _l('contract_latest_uploaded');
					
				}
			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}
		public function projectvesioncontents($userfile1,$contract_id)
	{
		require_once  APPPATH . '/vendor/phpoffice/phpword/bootstrap.php';
	    require_once  APPPATH . '/vendor/smalot/pdfparser/alt_autoload.php-dist';
		$contentfile='';
		$userfile= get_upload_path_by_type('contract') . $contract_id . '/'.$userfile1;
		 $extension = pathinfo ($userfile1 , PATHINFO_EXTENSION);
		if($extension=='doc'){
			 $phpWord = \PhpOffice\PhpWord\IOFactory::createReader('MsDoc')->load($userfile);

			  foreach($phpWord->getSections() as $section) {
				 foreach($section->getElements() as $element) {
						if(method_exists($element,'getText')) {
                  //  echo($element->getText(). "<br>");
					$contentfile .=$element->getText() . "<br>";
                }
            }
        }
		}else if($extension=='docx'){
						  $phpWord = \PhpOffice\PhpWord\IOFactory::createReader('Word2007')->load($userfile);
					  foreach($phpWord->getSections() as $section) {
        foreach($section->getElements() as $element) {
            if (method_exists($element, 'getElements')) {
                foreach($element->getElements() as $childElement) {
                    if (method_exists($childElement, 'getText')) {
                        $contentfile .= $childElement->getText() . ' ';
                    }
                    else if (method_exists($childElement, 'getContent')) {
                        $contentfile .= $childElement->getContent() . ' ';
                    }
                }
            }
            else if (method_exists($element, 'getText')) {
               // $contentfile .= $element->getText() . ' ';
            }
        }
					  }
					 }
					 else if($extension=='pdf'){
						 $parser = new \Smalot\PdfParser\Parser();
						 $pdf = $parser->parseFile($userfile);
						 $finalpdf=$pdf->getText();
						 $contentfile = nl2br($finalpdf);
					 }
		return $contentfile;
	}
	public function delete_contract_document($contract_id)
    {
        $this->contracts_model->delete_contract_document($contract_id);
    }
	
	public function  update_sharepoint1($contract_id,$versioncount)
    {
		$this->load->library('sharegraph');
		$sharegraph=new Sharegraph();    
        if ($this->input->is_ajax_request()) {
			 $this->db->where('id', $contract_id);
             $attachment = $this->db->get(db_prefix() . 'contracts')->row();
			$versioncount=$this->db->get_where('tblcontract_versions',array('contractid'=>$contract_id))->num_rows();
			
		if($versioncount==0){
			$sharegraph->download_updatefile($contract_id,$attachment->contract_filename);
		}
		else{
			$latestversion=get_current_contract_version($contract_id);
			if($data['is_newversion']==0){
			$sharegraph->download_updateversionfile($latestversion,$contract_id,$attachment->contract_filename);
			}else{
				
			}
			
		}
		//	$this->projectvesioncontents($attachment->file_name,$project_id);
			/*$contentfile=projectdocumentcontents($attachment->file_name,$project_id);
			 $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'project_files', [
            'content' => $contentfile,
        ]);*/
        $alert   = 'success';
        $message = _l('updated_successfully');
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('updated_successfully');
        }
       $result= [
            'alert'   => $alert,
            'message' => $message,
        ];
    }
			echo json_encode($result);
        
    }
public function update_contractfile_version($contract_id,$create_new_version_enabled){
	$this->load->library('sharegraph');
		$sharegraph=new Sharegraph();
	if ($this->input->is_ajax_request()) {
		$this->db->where('id', $contract_id);
             $attachment = $this->db->get(db_prefix() . 'contracts')->row();
			//$versioncount=$this->db->get_where('tblcontract_versions',array('contractid'=>$contract_id))->num_rows();
         //--------- if new version enabled
        if($create_new_version_enabled=='yes'){
            $current_version = get_current_contract_version($contract_id);
			print_r($current_version);
            // Already version exists
            if($current_version>=0){
                $version_data['version'] = $current_version+1;
				
                $path = get_upload_path_by_type('contract') . $contract_id . '/';
                _maybe_create_upload_path($path);
                $version_path = get_upload_path_by_type('contract') . $contract_id . '/'.$version_data['version'];
                _maybe_create_upload_path($version_path);
				 // Getting file extension
                    $extension = strtolower(pathinfo($attachment->contract_filename, PATHINFO_EXTENSION));
				if($current_version!=0)
				$oldfilename = basename($attachment->contract_filename,".".$extension).'-'.$current_version.'.'.$extension;
				else
					$oldfilename = $attachment->contract_filename;
				$newfilename = basename($attachment->contract_filename,".".$extension).'-'.$version_data['version'].'.'.$extension;
				
				 $newFilePath = $path . $newfilename;
				//create file using downlod sharelink
				$sharegraph->download_updateversionfile($version_data['version'],$contract_id,$oldfilename,$newfilename);
             // 	$sharegrah->rungraphversionuser($newfilename,$path,$contract_id,$version_data['version']);	
				$sharegraph->rungraphuser($newfilename,$newFilePath,$contract_id);
			  $sharelink=$sharegraph->getweburl($contract_id,$newfilename);
              $version_data['version_sharpoint_link']=$sharelink;  
            }

            $version_data['contractid']  = $contract_id;  
            $version_data['dateadded'] = date('Y-m-d H:i:s');
            $version_data['addedby'] =  get_staff_user_id();
			
            create_new_contract_version($version_data);    

        }else{
            
            // replace current contract
			$sharegraph->download_updatefile($contract_id,$attachment->contract_filename);
        }
              $alert   = 'success';
        $message = _l('updated_successfully');
        if ($this->db->affected_rows() > 0) {
            $alert   = 'success';
            $message = _l('updated_successfully');
        }
       $result= [
            'alert'   => $alert,
            'message' => $message,
        ];
    }
			echo json_encode($result); 


    }
}
