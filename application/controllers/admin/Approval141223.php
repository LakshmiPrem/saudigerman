<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Approval extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('approval_model');
		$this->load->model('tickets_model'); 
		$this->load->model('contracts_model'); 
    /*    if (!is_admin()) {
            access_denied('approval');
        }*/
    }
	/* List all approvals */
    public function index()
    {
		
		$data['category']             = $this->tickets_model->get_service();
		$data['reltypes']= get_approval_service();
        $data['title'] = _l('approvals');
		
        $this->load->view('admin/approval/manage', $data);
		
    }

    /* List all approval headings */
    public function approvalheading()
    {
        if ($this->input->is_ajax_request()) {
            $this->app->get_table_data('my_approval_headings');
        }
		
		$data['category']             = $this->tickets_model->get_service();
		$data['reltypes']= get_approval_service();
        $data['title'] = _l('approval_heading');
		
        $this->load->view('admin/approval/manage_approval_headings', $data);
		
    }
	  public function newApprovalType($id = '')
    {
        if (!is_admin() && !is_client_admin() ) {
            access_denied('approval');
        }
        if ($this->input->post()) {
            if (!$this->input->post('id')) {
                $id = $this->approval_model->add($this->input->post());
                if ($id) {
                    $success = true;
                    $message = _l('added_successfully', _l('approval_heading'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                    'id'=>$id,
                    'name'=>$this->input->post('name'),
                ));
            } else {
                $data = $this->input->post();
                $id   = $data['id'];
                unset($data['id']);
                $success = $this->approval_model->update($data, $id);
                $message = '';
                if ($success) {
                    $message = _l('updated_successfully', _l('approval_heading'));
                }
                echo json_encode(array(
                    'success' => $success,
                    'message' => $message,
                ));
            }
        }
    } 

       /* Delete announcement from database */
    public function delete_approval_heading($id)
    {
        if (!$id) {
            redirect(admin_url('approval'));
        }
        
        $response = $this->approval_model->delete($id);

        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('approval_heading')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('approval_heading')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('approval_heading')));
        }
        redirect(admin_url('approval'));
    }

    /* Add or edit tax / ajax */
    public function approvals1()
    {
		
        if ($this->input->post()) {
			$data=$this->input->post();
			 $last_approval_id = $this->db->select("*")->limit(1)->order_by('id','DESC')->get('tblapprovals')->row(); 
        if($last_approval_id){
          $insert['approval_key'] = $last_approval_id->id + 1;
        }else{
          $insert['approval_key'] =  1;        
        }
            $insert['approval_name'] = $data['approval_name'];
        $insert['rel_type']      =  $data['rel_type'];
        $insert['rel_id']      =  $data['rel_id'];
        $insert['approval_status'] = 2;
        $insert['addedfrom'] = get_staff_user_id();
        $insert['dateadded'] = date('Y-m-d H:i:s');
        $headings = $data['approval_heading_id'];
        $approval_assigned = $data['approval_assigned'];
        $inserted = false;
	    $notifiedUsers=[];
        foreach($headings as $key=>$heading){
          $insert['approval_heading_id'] = $heading;
          $insert['staffid']  = $approval_assigned[$key];
		 $result=$this->db->insert('tblapprovals',$insert);
          if($result>0){
			  $subject='';
			  if($data['rel_type']=='contract'){
				  $link1= 'contracts/contract/' . $data['rel_id'] . '?tab=approvals';
				  $contract=$this->contracts_model->get($data['rel_id']);
				  $subject=$contract->subject;
				 
			  }
			  else if($data['rel_type']=='ticket'){
				 $link1='tickets/ticket/' . $data['rel_id'].'?confirmation=approval';
				  $ticket = $this->tickets_model->get_ticket_by_id($data['rel_id']);
				  $subject=$ticket->subject;
			  }else if($data['rel_type']=='expense'){
				  $link1='projects/view/' . $data['rel_id'].'?tab=project_expenses';
				   $project = $this->projects_model->get($data['rel_id']);
				  $subject=$project->name;
			  }
			
			   $notified = add_notification([
                    'description'     => 'legal_request_approval',
                    'touserid'        => $approval_assigned[$key],
                    'fromcompany'     => 1,
                    'fromuserid'      => get_staff_user_id(),
                    'link'            => $link1,
                    'additional_data' => serialize([
                        $subject,
                    ]),
                ]);
                if ($notified) {
                    array_push($notifiedUsers, $approval_assigned[$key]);
                } 
			  $inserted = true;
			  pusher_trigger_notification($notifiedUsers);
          }else{
            $inserted = false;
          }

        }
		
        if($inserted == 1){
			//echo($inserted);
          echo json_encode(array(
            'success' => true,
            'message' => 'Successfully Added',
          ));
        }
        

       return false;
        
        
      }

        
		$this->load->model('staff_model'); 
		
		 $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
		$data['rel_name']=$this->input->get('rel_name');
		$data['rel_id']=$this->input->get('rel_id');
        $data['staffs']                = $this->staff_model->get('', $whereStaff);
		 $data['approval_headings'] = $this->approval_model->get('',['rel_type'=>$data['rel_name']]);
		 $data['statuses']          = $this->tickets_model->get_ticket_status();
		 $this->load->view('admin/approval/modals/approval_modal', $data);
		
    }

    public function approvals()
    {
		
        if ($this->input->post()) {
			$data=$this->input->post();
			$edit_approval=isset($data['approval_row_id'])?$data['approval_row_id']:[];
			$approvecount=0;$count=0;
			if(sizeof($edit_approval)>0){
				
				$approvecount=count($data['approval_row_id']);
				 $last_approval_key = $this->db->distinct()->select("approval_key")->limit(1)->order_by('id','DESC')->get_where('tblapprovals',array('rel_id'=>$data['rel_id'],'rel_type'=>$data['rel_type']))->row()->approval_key; 
				$prvapprovals=get_approvals($data['rel_id'],$data['rel_type'],'',$last_approval_key);
				foreach($prvapprovals as $prapp){
					if (!in_array($prapp['id'], $edit_approval)){
						 $this->db->where('id', $prapp['id']);
						$this->db->delete('tblapprovals');
					}
				}
				 $insert['approval_key'] = $last_approval_key;
			}else{
			 $last_approval_id = $this->db->select("*")->limit(1)->order_by('id','DESC')->get('tblapprovals')->row(); 
        if($last_approval_id){
          $insert['approval_key'] = $last_approval_id->id + 1;
        }else{
          $insert['approval_key'] =  1;        
        }
			}
         $insert['approval_name'] = $data['approval_name'];
        $insert['rel_type']      =  $data['rel_type'];
        $insert['rel_id']      =  $data['rel_id'];
        $insert['approval_status'] = 2;
        $insert['addedfrom'] = get_staff_user_id();
        $insert['dateadded'] = date('Y-m-d H:i:s');
        $headings = $data['approval_heading_id'];
        $approval_assigned = $data['approval_assigned'];
		$approval_remarks=$data['approval_remarks'];
        $inserted = false;
	    $notifiedUsers=[];
        foreach($headings as $key=>$heading){
          $insert['approval_heading_id'] = $heading;
          $insert['staffid']  = $approval_assigned[$key];
		 $insert['approval_remarks']  = $approval_remarks[$key];
		//print_r($insert);
		if($approvecount>$count){
		 $this->db->where('id', $edit_approval[$key]);
        $result=$this->db->update('tblapprovals', $insert);
			$count++;
		}
			else{
		 $result=$this->db->insert('tblapprovals',$insert);
			}
          if($result>0 || $result==true){
			  $subject='';
			  $description='';
			  if($data['rel_type']=='contract'){
				  $link1= 'contracts/contract/' . $data['rel_id'] . '?tab=approvals';
				  $contract=$this->contracts_model->get($data['rel_id']);
				  $subject=$contract->subject;
				  $description='contract_approval';
				 
			  }
			  else if($data['rel_type']=='ticket'){
				 $link1='tickets/ticket/' . $data['rel_id'].'?confirmation=approval';
				  $ticket = $this->tickets_model->get_ticket_by_id($data['rel_id']);
				  $subject=$ticket->subject;
				  $description='legal_request_approval';
			  }else if($data['rel_type']=='expense'){
				  $link1='projects/view/' . $data['rel_id'].'?tab=project_expenses';
				   $project = $this->projects_model->get($data['rel_id']);
				  $subject=$project->name;
				  $description='expense_approval';
			  }
			
			   $notified = add_notification([
                    'description'     => $description,
                    'touserid'        => $approval_assigned[$key],
                    'fromcompany'     => 1,
                    'fromuserid'      => get_staff_user_id(),
                    'link'            => $link1,
                    'additional_data' => serialize([
                        $subject,
                    ]),
                ]);
                if ($notified) {
                    array_push($notifiedUsers, $approval_assigned[$key]);
                } 
			  $inserted = true;
			  pusher_trigger_notification($notifiedUsers);
          }else{
            $inserted = false;
          }

        }
		
        if($inserted == 1){
			//echo($inserted);
          echo json_encode(array(
            'success' => true,
            'message' => 'Successfully Added',
          ));
        }
        

       return false;
        
        
      }

        
		$this->load->model('staff_model'); 
		
		 $whereStaff                 = [];
        if (get_option('access_tickets_to_none_staff_members') == 0) {
            $whereStaff['is_not_staff'] = 0;
        }
		$data['rel_name']=$this->input->get('rel_name');
		$data['rel_id']=$this->input->get('rel_id');
        $data['staffs']                = $this->staff_model->get('', $whereStaff);
		 $data['approval_headings'] = $this->approval_model->get('',['rel_type'=>$data['rel_name']]);
		 $data['statuses']          = $this->tickets_model->get_ticket_status();
		 $this->load->view('admin/approval/modals/approval_modal', $data);
		
    }
	
    public function table($rel_name,$rel_id)
    {
     
      $approval_statuses =  $this->tickets_model->get_ticket_status();
		$this->db->select("*")->limit(1)->order_by('id','DESC')->get('tblapprovals')->row();
      $approvals = $this->approval_model->getapprovalsbykey($rel_name,$rel_id);
      $tbody = '';
        if(sizeof($approvals) > 0 ) {
            $tbody .= '
                       <ul class="nav nav-tabs" id="nav-tab" role="tablist">';
                        foreach ($approvals as $key=> $approvalk) {
                          $active = ($key == 0 ) ? 'active' : '';
						  $tbody .='<li class="nav-item '.$active.' role="presentation">';
                          $tbody .='<a class="nav-link '.$active.'" id="nav-'.$approvalk['approval_key'].'-tab" data-toggle="tab" href="#nav-'.$approvalk['approval_key'].'" role="tab" aria-controls="nav-'.$approvalk['approval_key'].'" aria-selected="true">'.$approvalk['approval_name'].'</a></li>'; 
                        }
 
   
  
            $tbody .= '</ul>
           
            <div class="tab-content" id="nav-tabContent">';
            foreach ($approvals as $key0=> $approvalk2) {    
              $active1 = ($key0 == 0 ) ? 'in active' : '';
              $approval_keys = $this->approval_model->getapprovaldata('',['approval_key'=>$approvalk2['approval_key']]);
              $tbody .= '<div class="tab-pane fade  '.$active1.' " id="nav-'.$approvalk2['approval_key'].'" role="tabpanel" aria-labelledby="nav-'.$approvalk2['approval_key'].'">
                          <div class="card custom-card">
                            <div class="card-header">'.$approvalk2['approval_name'].'</div>
                              <div class="card-body">
                                <div class="table-responsive">
                                  <table id="table-approvals" class="table table-bordered border-t0 key-buttons text-nowrap w-100" >
                                    <thead>
                                      <tr>
                                        <th>'._l('approve_by').'</th>
                                        <th>'._l('approval_status').'</th>
                                        <th>'._l('remarks').'</th>
                                      </tr>
                                    </thead>
                                    <tbody>';
                                    foreach ($approval_keys as $key1 => $approval) {
                                             
                                      $disabled = '';
                                      if(get_staff_user_id() != $approval['staffid'] || $approval['approval_status']==3){
                                        $disabled = 'disabled';
                                      }
                                      $approved_date_span ='';
                                      if($approval['dateapproved'] != ''){
                                        $approved_date_span = '<span class="badge badge-dark">Updated Date :'.$approval['dateapproved'].'</span>';
                                      }
                                      $tbody  .= '<tr>';
                                      $tbody .= '<td>'.get_staff_full_name($approval['staffid']).'<br><b>'.get_approval_heading_name_by_id($approval['approval_heading_id']).'</b></td>';
                                      $status_ = '<select onchange="update_approval_status(this,'.$approval['id'].');return false;" class="form-control" name="status_id" '.$disabled.' >';
                                      foreach($approval_statuses  as $status){
                                        $selected = ($status['ticketstatusid'] == $approval['approval_status']) ? 'selected' : '';
                                        $status_ .= '<option value="'.$status['ticketstatusid'].'" '.$selected.'>'.$status['name'].'</option>';
                                      } 
                                      $status_ .='</select>'.$approved_date_span;
                                      $tbody .= '<td>'.$status_.'</td>' ;
                                      $tbody .= '<td><textarea class="form-control" '.$disabled.' onchange="update_approval_remarks(this,'.$approval['id'].');return false;"  name="approval_remarks" rows="2" placeholder="Approval remarks..">'.$approval['approval_remarks'].'</textarea></td>';
                                       $tbody .= '</tr>
                                           ';
                                    }
                $tbody .= ' </tbody>
                              </table>
                            </div>
                          </div></div>
                          </div>';
      }
      }else{
        $tbody .= '<tr><td colspan="3" align="center">No Approvals</td></tr>';
      }

      $tbody .='</div>'; 
      $response = $tbody;
        echo $response;
    }
	 public function save_approval_remarks()
    {
      $approval_id =$this->input->post('id');
      $update['approval_remarks'] =$this->input->post('remarks');
      $success = $this->approval_model->updateapproval($update, $approval_id);
      $approval_row = $this->db->get_where('tblapprovals',array('id'=>$approval_id))->row();

      echo json_encode(array(
        'success' => true,
        'message' => 'Successfully Added',
        'rel_name'=>$approval_row->rel_type,
        'rel_id'  =>$approval_row->rel_id
      ));
    }

    public function save_approval_status()
    {
      $approval_id =$this->input->post('id');
      $update['approval_status'] =$this->input->post('status_id');
      $update['dateapproved'] = date('Y-m-d H:i:s');
	$success = $this->approval_model->updateapproval($update, $approval_id);
      $approval_row = $this->db->get_where('tblapprovals',array('id'=>$approval_id))->row();//$a_model->find($approval_id);
      echo json_encode(array(
        'success' => true,
        'message' => 'Successfully Added',
        'rel_name'=>$approval_row->rel_type,
        'rel_id'  => $approval_row->rel_id
      ));
    }

    /* Delete tax from database */
    public function delete($id)
    {
        if (!$id) {
            redirect(admin_url('approval'));
        }
        $response = $this->taxes_model->delete($id);
        if (is_array($response) && isset($response['referenced'])) {
            set_alert('warning', _l('is_referenced', _l('tax_lowercase')));
        } elseif ($response == true) {
            set_alert('success', _l('deleted', _l('tax')));
        } else {
            set_alert('warning', _l('problem_deleting', _l('tax_lowercase')));
        }
        redirect(admin_url('taxes'));
    }

    public function tax_name_exists()
    {
        if ($this->input->post()) {
            $tax_id = $this->input->post('taxid');
            if ($tax_id != '') {
                $this->db->where('id', $tax_id);
                $_current_tax = $this->db->get(db_prefix().'taxes')->row();
                if ($_current_tax->name == $this->input->post('name')) {
                    echo json_encode(true);
                    die();
                }
            }
            $this->db->where('name', $this->input->post('name'));
            $total_rows = $this->db->count_all_results(db_prefix().'taxes');
            if ($total_rows > 0) {
                echo json_encode(false);
            } else {
                echo json_encode(true);
            }
            die();
        }
    }
	public function testcron()
	{
		$servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "bevera9w_legal_counsel_dbnew";

		// Create connection
		$conn = new mysqli($servername, $username, $password, $dbname);
		// Check connection
		if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
		$dt=date('Y-m-d');
		$sql = "INSERT INTO testcron (remainder, description)
		VALUES (CURDATE(), 'Test Cron')";

		if ($conn->query($sql) === TRUE) {
  	echo "New record created successfully";
		} else {
  	echo "Error: " . $sql . "<br>" . $conn->error;
		}

		$conn->close();
		
	}
}
