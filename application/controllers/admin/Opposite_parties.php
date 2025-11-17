<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Opposite_parties extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('oppositeparty_model');   
     
    }

    /* List all opposite parties */
    public function index()
    {
        
        close_setup_menu();

        if (!has_permission('opposite_parties', '', 'view') && !has_permission('opposite_parties', '', 'view_own')) {
            access_denied('opposite_parties');
        }

        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_opposite_parties',[
            'clientid' => '',
            ]);
        }
        $data['active']= array( "0" => array( 'id' => 1,'name' => 'Active'),"1" => array ( 'id' => 2, 'name'=> 'Inactive'));
       
        $this->load->model('partytype_model');  
        //$data['party_type']=$this->partytype_model->get();
          $data['party_type']=$this->db->get('tblserviceprovider')->result_array();
        $data['title'] = _l('opposite_parties');
        $this->load->view('admin/oppositeparties/manage', $data);
    }


     public function table($clientid = '')
    {
        if (!has_permission('opposite_parties', '', 'view') && !has_permission('opposite_parties', '', 'view_own')) {
            ajax_access_denied();
        }

        $this->app->get_table_data('my_opposite_parties', [
            'clientid' => $clientid,
        ]);
    }


     /* Edit client or add new client*/
    public function opposite_party($id = '')
    {
        if (!has_permission('opposite_parties', '', 'view')) {
            if ($id != '' && !is_customer_admin($id)) {
                access_denied('opposite_parties');
            }
        }
        if ($this->input->post() && !$this->input->is_ajax_request()) {
            if ($id == '') {
                if (!has_permission('opposite_parties', '', 'create')) {
                    access_denied('opposite_parties');
                }

                $data                 = $this->input->post();
                $id = $this->oppositeparty_model->add($data);
                
                if ($id) {
					 handle_opposite_profile_image_upload($id);
                    set_alert('success', _l('added_successfully', _l('opposite_party')));
                    redirect(admin_url('opposite_parties'));
                }
            } else {
                if (!has_permission('opposite_parties', '', 'edit')) {
                    access_denied('opposite_parties');
                }
                $success = $this->oppositeparty_model->update($this->input->post(), $id);
                if ($success == true) {
					 handle_opposite_profile_image_upload($id);
                    set_alert('success', _l('updated_successfully', _l('opposite_party')));
                }
                redirect(admin_url('opposite_parties'));
            }
        }

        if (!$this->input->get('group')) {
            $group = 'profile';
        } else {
            $group = $this->input->get('group');
        }
        // View group
        $data['group']  = $group;

        if ($id == '') {
            $title = _l('add_new', _l('opposite_party'));
        } else {
            $client = $this->oppositeparty_model->get($id);
            $client->userid = $client->id;
            if (!$client) {
                blank_page('Opposite Party Not Found');
            }

            if ($group == 'profile') {
                
            } elseif ($group == 'attachments') {
                $data['attachments']   = get_all_recovery_attachments($id,'oppositeparty');
            }elseif ($group == 'notes') {
                $data['user_notes'] = $this->misc_model->get_notes($id, 'oppositeparty');
            }elseif ($group == 'contacts') {
                $data['party_contacts'] = $this->oppositeparty_model->get_contacts($id);
				$data['nationality']=get_nationality();
				$data['contact_type']=get_party_contacttype();
            } elseif ($group == 'projects') {
                $this->load->model('projects_model');
                $data['project_statuses'] = $this->projects_model->get_project_statuses();
            } elseif ($group == 'overview') {
				 $data['party_contacts'] = $this->oppositeparty_model->get_contacts($id);
				  $data['user_notes'] = $this->misc_model->get_notes($id, 'oppositeparty');
            }elseif ($group == 'kycattachments') {
                $this->load->model('casediary_model');
              $data['document_types']    = $this->casediary_model->get_document_types_bycategory('8');
			 $data['attachments']   = get_all_recovery_attachments($id,'oppositeparty',true);
            }  
            $data['client']        = $client;
            $title                 = $client->name;
            
        }
        if ($this->input->get('customer_id')) {
            $data['customer_id'] = $this->input->get('customer_id');
        }
        $data['staff'] = $this->staff_model->get('', ['active' => 1]);
        // Get all active staff members (used to add reminder)
        $data['members'] = $data['staff'];
        $data['clients'] = $this->clients_model->get();
		//$data['nationality']=$
        $this->load->model('partytype_model');  
                //$data['party_type']=$this->partytype_model->get();
                $data['party_type']=$this->db->get('tblserviceprovider')->result_array();
        // print_r($data['party_type']);
        $data['title'] = $title;

        $this->load->view('admin/oppositeparties/oppositeparty', $data);
    }

     /* Delete opposite_party */
    public function delete($id)
    {
        if (!has_permission('opposite_parties', '', 'delete')) {
            access_denied('opposite_parties');
        }
        if (!$id) {
            redirect(admin_url('opposite_parties'));
        }
        $response = $this->oppositeparty_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('delete_transactions_warning',_l('opposite_party')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('opposite_party')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('opposite_party')));
        }
        redirect(admin_url('opposite_parties'));
    }

    public function upload_attachment($id)
    {
        handle_oppositeparty_attachments_upload($id);

    }

    public function delete_attachment($customer_id, $id)
    {
        if (has_permission('opposite_parties', '', 'delete') ) {
            $this->oppositeparty_model->delete_attachment($id);
        }
        redirect($_SERVER['HTTP_REFERER']);
    } 

     public function pagination()
    {
      $q = $date = '';
      if($this->input->post()){
        $q= $this->input->post('q');
        /*if($this->input->post('date') != ''){
            $date = $this->input->post('date');
        }*/
      }
      

      $this->load->library("pagination");
      $config = array();
      $config["base_url"] = "#";
      $config["total_rows"] =  $this->oppositeparty_model->fetch_details_numrows($q);
      $config["per_page"] = 12;
      $config["uri_segment"] = 4;
      $config["use_page_numbers"] = TRUE;
      $config["full_tag_open"] = '<ul class="pagination opposite-page">';
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
       'client_data'   => $this->oppositeparty_model->fetch_details($q,$config["per_page"], $start),
       'total_clients'=> '<span class="badge badge-success" style="padding: 10px;
    font-size: 15px;"><b>'.$config["total_rows"].' '._l('opposite_parties').'</b></span>',
      );
      echo json_encode($output);
    }
	 /* Change client status / active / inactive */
    public function change_opponent_status($id, $status)
    {
        if ($this->input->is_ajax_request()) {
            $this->oppositeparty_model->change_opponent_status($id, $status);
        }
    }
	 public function defendars($client_id)
    {
        $this->app->get_table_data('my_opposite_contacts', array(
            'client_id' => $client_id,
        ));
    }

	 public function defendar($party_id, $contact_id = '')
    {
        if (!has_permission('opposite_parties', '', 'view')) {
            if (!is_recovery_admin($project_id)) {
                echo _l('access_denied');
                die;
            }
        }
          $data['opposite_id'] = $party_id;
        $data['contactid']   = $contact_id;
        if ($this->input->post()) {
            $data = $this->input->post();
			
            unset($data['contactid']);
            if ($contact_id == '') {
            if (!has_permission('opposite_parties', '', 'create')) {
                    if (!is_recovery_admin($project_id)) {
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;
                    }
                }
                $id      = $this->oppositeparty_model->add_defendar($data, $party_id);
                $message = '';
                $success = false;
                if ($id) {
					 $success = true;
                    $message = _l('added_successfully', _l('contacts_defendar'));
                }
               
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    ));
                die;
            } else {
                if (!has_permission('opposite_parties', '', 'edit')) {
                    
                        header('HTTP/1.0 400 Bad error');
                        echo json_encode(array(
                            'success' => false,
                            'message' => _l('access_denied'),
                        ));
                        die;

                }
               // $original_contact = $this->projects_model->get_installment($contact_id);
                $success          = $this->oppositeparty_model->update_defendar($data, $contact_id);
                $message          = '';
                $proposal_warning = false;
                $original_email   = '';
                $updated          = false;
                
                if ($success == true) {
                    $updated = true;
                    $message = _l('updated_successfully', _l('contacts_defendar'));
                }
                
             //   $totalpaid = $this->projects_model->get_installment_totalpaid($project_id);
                echo json_encode(array(
                    'success' => $success,
                    'proposal_warning' => $proposal_warning,
                    'message' => $message,
                 //   'original_email' => $original_email,
                    'has_primary_contact'=>true,
                 //   'totalpaid'=>$totalpaid,
                ));
                die;
            }
			  
        }
        if ($contact_id == '') {
            $title = _l('add_new', _l('defendar_lowercase'));
        } else {
            $data['contact'] = $this->oppositeparty_model->get_defendar($contact_id);

            if (!$data['contact']) {
                header('HTTP/1.0 400 Bad error');
                echo json_encode(array(
                    'success' => false,
                    'message' => 'Contact Not Found',
                ));
                die;
            }
            $title = _l('edit', _l('defender_lowercase'));
        }
		//$data['party_contacts'] = $this->oppositeparty_model->get_contacts($id);
				$data['nationality']=get_nationality();
		$data['contact_type']=get_party_contacttype();
       // $data['customer_permissions'] = get_contact_permissions();
        $data['title']                = $title;
        $this->load->view('admin/oppositeparties/modals/defendar', $data);
    }
	 public function delete_defendar($customer_id, $id)
    {
        if (!has_permission('opposite_parties', '', 'delete')) {
            if (!is_admin($customer_id)) {
                access_denied('opposite_parties');
            }
        }

        $this->oppositeparty_model->delete_defendar($id);
        redirect(admin_url('opposite_parties/opposite_party/' . $customer_id . '?group=contacts'));
    }

    public function add_opposite_party_name(){
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $data = $this->input->post();
                $id = $this->oppositeparty_model->add($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('opposite_party'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
                ]);
            }
        }
    }
public function add_partykyc($id=''){
		 if ($this->input->post()) {
			 $data=$this->input->post();
			 $id   = $data['rel_id'];
               // unset($data['contractid']);
			 
             $message         = '';
            $success=handle_oppositeparty_kycattachments_upload($id);
				 if ($success == true) {
                $message = $id ? _l('added_successfully', _l('kyc')) : '';
					  $updated          = true;
				 }
                if ($success) {
					$message= _l('contractkyc_latest_uploaded');
					
				}
			 
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
		}
	}
    public function mark_as_active($id)
    {
        $this->db->where('id', $id);
        $this->db->update(db_prefix() . 'oppositeparty', [
            'active' => 1,
        ]);
        redirect(admin_url('oppositeparties/oppositeparty/' . $id));
    }
    
       public function add_quick_opposite_party(){
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $data = $this->input->post();
                $id = $this->oppositeparty_model->add($data);
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('opposite_party'));
                }
                echo json_encode([
                    'success' => $success,
                    'message' => $message,
                    'id'      => $id,
                    'name'    => $this->input->post('name'),
					'link'	  =>'opposite_parties/opposite_party/'.$id,
					
                ]);
            }
        }
    }
}
    