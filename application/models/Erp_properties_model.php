<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Erp_properties_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
    * Add new contract type
    * @param mixed $data All $_POST data
    */
    public function add($data)
    {
        
        $data['dateadded'] = date('Y-m-d H:i:s');
        if(total_rows('tbloppositeparty',['erp_vendor_no'=> $data['member_no']]) > 0){
        	$data['member_id'] = $this->db->get_where('tbloppositeparty',['erp_vendor_no' => $data['member_no']])->row()->id;
	        // update sales 30 date if payment status is greater than 30 

            if(floatval($data['payment_percentage']) >= 30 ){
                $data['sale_30_date']       =  date('Y-m-d');
            }
            
            // update transfer 80 date if payment status is greater than 80
            if(floatval($data['payment_percentage']) >= 80 ){
                $data['transfer_80_date']   =  date('Y-m-d');
            }

            $this->db->insert('tblerp_properties', $data);
	        $insert_id = $this->db->insert_id();
	        if ($insert_id) {
	            log_activity('Client Property Added [' . $data['unit_name'] . ']');
                // check Main Project exists or not
                if(total_rows('tblprojects',['sale_projectcode' => $data['project_code'],'case_type'=>'projects'] ) > 0 ){
                    // Check sub project exists or not
                    if(total_rows('tblprojects',['sub_plot_no' => $data['unit_name'],'sale_projectcode' => $data['project_code'],'case_type'=>'submatter']) <= 0 ){
                        //send notification to legal team to create sub project
                        $this->send_notification_for_create_sub_project_to_leagal_team($data['project_code'],$data['unit_name']);
                    }
                }else{
                    //send notification to legal team to create main project
                    $this->send_notification_for_create_main_sales_project_to_leagal_team($data['project_code']);

                }

                

	            return $insert_id;
	        }	
        }
    	

        return false;
    }

    public function send_notification_for_create_main_sales_project_to_leagal_team($project_code){
        // select Legal department Staff ids
        $members = $this->db->get_where('tblstaff_departments',['departmentid'=>1])->result_array();
        $notifiedUsers = [];
        foreach ($members as $member) {
            $notified = add_notification([
                'fromuserid'      =>  get_staff_user_id(),
                'description'     => 'notification_to_create_main_sales_project',
                'link'            => 'projects/project/?case_type=projects&project_code='.$project_code,
                'touserid'        => $member['staffid'],
                'additional_data' => serialize([
                    $project_code,
                ]),
            ]);
            if ($notified) {
                array_push($notifiedUsers, $member['staffid']);
            }
        
            //send_mail_template('project_status_changed_to_member',$member['email'], $member['staff_id'], $id);
        }
        pusher_trigger_notification($notifiedUsers);
    }

    public function send_notification_for_create_sub_project_to_leagal_team($project_code,$unit_name){
        // select Legal department Staff ids
        $members = $this->db->get_where('tblstaff_departments',['departmentid'=>1])->result_array();
        $main_project_id      = $this->db->get_where('tblprojects',['sale_projectcode'=>$project_code,'case_type'=>'projects'])->id;
        $clientid             = $this->db->get_where('tblprojects',['sale_projectcode'=>$project_code,'case_type'=>'projects'])->clientid;
        $notifiedUsers = [];
        foreach ($members as $member) {
            $notified = add_notification([
                'fromuserid'      =>  get_staff_user_id(),
                'description'     => 'notification_to_create_sales_sub_project',
                'link'            => 'projects/project/?case_type=submatter&customer_id='.$clientid.'&related_matter='.$main_project_id.'&project_code='.$project_code.'&sub_unit_name='.$unit_name,
                'touserid'        => $member['staffid'],
                'additional_data' => serialize([
                    $project_code,$unit_name
                ]),
            ]);
            if ($notified) {
                array_push($notifiedUsers, $member['staffid']);
            }
        
            //send_mail_template('project_status_changed_to_member',$member['email'], $member['staff_id'], $id);
        }
        pusher_trigger_notification($notifiedUsers);
    }

    /**
     * Edit contract type
     * @param mixed $data All $_POST data
     * @param mixed $id Contract type id
     */
    public function update($data, $id)
    {
        $this->db->where('unit_name', $id);
        $this->db->update('tblerp_properties', $data);
        if ($this->db->affected_rows() > 0) {

            if(total_rows('tblprojects',['sale_projectcode' => $data['project_code'],'case_type'=>'projects'] ) > 0 ){
                    // Check sub project exists or not
                if(total_rows('tblprojects',['sub_unit_name' => $id,'sale_projectcode' => $data['project_code'],'case_type']) <= 0 ){
                    //send notification to legal team to create sub project
                    $this->send_notification_for_create_sub_project_to_leagal_team($data['project_code'],$id);
                }
            }else{
                //send notification to legal team to create main project
                $this->send_notification_for_create_main_sales_project_to_leagal_team($data['project_code']);

            }

            $this->send_payment_status_notification($id);
            $this->update_sales_amounts_in_projects($id);
            log_activity('Client Property Updated [' . $id . ']');
            return true;
        }

        return false;
    }

    public function send_payment_status_notification($unit_name){
        
        $propery_details = $this->db->get_where('tblerp_properties',['unit_name' => $unit_name])->row();
        $this->load->model('projects_model');
        // if payment status is equal or greater than 30 , send sales contract notification 
        if( $propery_details->payment_percentage >= 30  && $propery_details->sale_30_notified == 0){
             // select project id
            if(total_rows('tblprojects',['sub_unit_name' => $unit_name]) > 0 ){
                $project_id = $this->db->get_where('tblprojects',['sub_unit_name' => $unit_name])->row()->id; 
                $this->projects_model->_notify_project_members_payment_status($project_id,$unit_name,30);
                $this->db->where('unit_name',$unit_name);
                $this->db->update('tblerp_properties',['sale_30_notified'=>1,'sale_30_date'=>date('Y-m-d')]);
            }
        }

        if($propery_details->payment_percentage >= 80 &&  $propery_details->transfer_80_notified == 0){
            if(total_rows('tblprojects',['sub_unit_name' => $unit_name]) > 0 ){
                $project_id = $this->db->get_where('tblprojects',['sub_unit_name' => $unit_name])->row()->id; 
                $this->projects_model->_notify_project_members_payment_status($project_id,$unit_name,80);
                $this->db->where('unit_name',$unit_name);
                $this->db->update('tblerp_properties',['transfer_80_notified'=>1,'transfer_80_date'=>date('Y-m-d')]);
            }
        } 
    }

    public function update_sales_amounts_in_projects($unit_name){
        $propery_details = $this->db->get_where('tblerp_properties',['unit_name' => $unit_name])->row();
        if(total_rows('tblprojects',['sub_unit_name' => $unit_name]) > 0 ){
            $project_id = $this->db->get_where('tblprojects',['sub_unit_name' => $unit_name])->row()->id; 

            $this->db->where('id',$project_id);
            $this->db->update('tblprojects',[
                                                'purchase_price'     => $propery_details->sale_price,
                                                'sub_deposit_amount' => $propery_details->deposit,
                                                'sub_balance_amount' => $propery_details->balance,
                                                'sub_title_no'       => $propery_details->property_title_no,
                                            ]);
        }
    }

    /**
     * @param  integer ID (optional)
     * @return mixed
     * Get contract type object based on passed id if not passed id return array of all types
     */
    public function get($id = '',$where=[])
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblerp_properties')->row();
        }
         if ((is_array($where) && count($where) > 0) || (is_string($where) && $where != '')) {
            $this->db->where($where);
        }
        $this->db->order_by('id', 'DESC');
        $types = $this->db->get('tblerp_properties')->result_array();
        return $types;
    }

    /**
     * @param  integer ID
     * @return mixed
     * Delete contract type from database, if used return array with key referenced
     */
    public function delete($id)
    {
        /*if (is_reference_in_table('court_type_id', db_prefix() . 'projects', $id)) {
            return array(
                'referenced' => true,
            );
        }*/
        $this->db->where('id', $id);
        $this->db->delete('tblerp_properties');
        if ($this->db->affected_rows() > 0) {
            log_activity('Client Property Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

   
}
