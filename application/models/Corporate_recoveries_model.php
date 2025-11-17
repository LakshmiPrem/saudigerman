<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Corporate_recoveries_model extends App_Model
{
    private $contact_columns;

    public function __construct()
    {
        parent::__construct();
        //$this->contact_columns = do_action('contact_columns', array('firstname', 'lastname', 'email', 'phonenumber', 'title', 'password', 'send_set_password_email', 'donotsendwelcomeemail', 'permissions', 'direction', 'invoice_emails', 'estimate_emails', 'credit_note_emails', 'contract_emails', 'task_emails', 'project_emails', 'is_primary'));
        $this->load->model(array('client_vault_entries_model', 'client_groups_model', 'statement_model'));
    }

    /**
     * Get client object based on passed clientid if not passed clientid return array of all clients
     * @param  mixed $id    client id
     * @param  array  $where
     * @return mixed
     */
    public function get($id = '', $where = array())
    {
        $this->db->select('*,CONCAT(file_no," - ",debtor_title) as name');

        $this->db->join('tblcountries', 'tblcountries.country_id = tblcorporate_recoveries.country', 'left');
        $this->db->where($where);

        if (is_numeric($id)) {
            $this->db->where('tblcorporate_recoveries.id', $id);
            $client = $this->db->get('tblcorporate_recoveries')->row();
            return $client;
        }

        $this->db->order_by('id', 'asc');

        $defaulters = $this->db->get('tblcorporate_recoveries')->result_array();
        $res = array();
        foreach ($defaulters as $defaulter) {
            
            $defaulter['totalpaid'] = $this->get_installment_totalpaid($defaulter['id']);

            $res[] = $defaulter;


        }
        return $res;
    }

    public function get_notes($recovery_id = ''){
        $this->db->select('tblcorporate_recoveries.company,tblcorporate_recoveries.cif_id,tblnotes.*');
        $this->db->join('tblnotes','tblnotes.rel_id = tblcorporate_recoveries.id');
        $this->db->where('tblnotes.rel_type','corporate');
        if(is_numeric($recovery_id)){
            $this->db->where('tblcorporate_recoveries.id',$recovery_id);
        }
        return $this->db->get('tblcorporate_recoveries')->result_array();
    }

    public function get_agents(){
        $this->db->distinct();
        $this->db->select('staff_id as id,CONCAT(tblstaff.firstname," ",tblstaff.lastname) as agent_name');
        $this->db->join('tblrecoveryadmins','tblrecoveryadmins.staff_id = tblstaff.staffid');
        //$this->db->where('staffid !=',1);
        return $this->db->get('tblstaff')->result_array();

        //return $this->db->query('SELECT DISTINCT(staff_id) FROM tblcustomeradmins')->result_array();
    }


    

    public function get_installment_totalpaid($defaulterID){
        $totalpaid = 0;
         $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblrecoveries_installments` WHERE recovery_type = ? AND  recovery_id = ? AND installment_status = ?',array('corporate',$defaulterID,'paid'))->row();
         if($totalpaid_qry->totalpaid > 0){
            $totalpaid = $totalpaid_qry->totalpaid;
         }
        return $totalpaid;
    }

    /**
     * Get customers contacts
     * @param  mixed $customer_id
     * @param  array  $where       perform where in query
     * @return array
     */
    public function get_partners($customer_id = '', $where = array())
    {
        $this->db->where($where);
        $this->db->where('recovery_type','corporate');
        if ($customer_id != '') {
            $this->db->where('recovery_id', $customer_id);
        }
        $this->db->order_by('id', 'DESC');

        return $this->db->get('tblcorporate_recoveries_partners')->result_array();
    }


    

    

    /**
     * Get single contacts
     * @param  mixed $id contact id
     * @return object
     */
    public function get_contact($id)
    {
        $this->db->where('id', $id);

        return $this->db->get('tblcontacts')->row();
    }

    /**
     * @param array $_POST data
     * @param client_request is this request from the customer area
     * @return integer Insert ID
     * Add new client to database
     */
    public function add($data, $client_or_lead_convert_request = false)
    {
        

        //print_r($data);

        $data = $this->check_zero_columns($data);
        if($data['datecreated'] == null || $data['datecreated'] == ''){
            unset($data['datecreated']);
        }
        $data['datecreated'] = isset($data['datecreated']) ? to_sql_date($data['datecreated'],true) :  date('Y-m-d H:i:s');

        if (is_staff_logged_in()) {
            $data['addedfrom'] = get_staff_user_id();
        }


        //$hook_data                = do_action('before_client_added', array('data'=>$data));
        //$data = $hook_data['data'];
        //$tmp = json_encode($data['managing_directors']);
        //unset($data['managing_directors']);
        //$data['managing_directors'] = $tmp;
        
        unset($data['liability']);
        unset($data['nature']);
        unset($data['due']);
        unset($data['overdue']);
        unset($data['principal_amount']);
        unset($data['DataTables_Table_0_length']);
        unset($data['DataTables_Table_3_length']);
        unset($data['DataTables_Table_1_length']);


        
        unset($data['clients_import']);
        unset($data['recovery_id']);
        unset($data['file_csv']);
        $this->db->insert('tblcorporate_recoveries', $data);

        $userid = $this->db->insert_id();
        if ($userid) {
           
            $this->db->where('name', 'next_corporate_file_no');
            $this->db->set('value', 'value+1', false);
            $this->db->update('tbloptions');

            //do_action('after_client_added', $userid);
            $log = $data['company'];

            if ($log == '' && isset($contact_id)) {
                $log = get_contact_full_name($contact_id);
            }

            $isStaff = null;
            if (!is_client_logged_in() && is_staff_logged_in()) {
                $log .= ' From Staff: ' . get_staff_user_id();
                $isStaff = get_staff_user_id();
            }

            log_activity('New Corporate Recovery Created [' . $log . ']', $isStaff);
        }

        return $userid;
    }

    /**
     * @param  array $_POST data
     * @param  integer ID
     * @return boolean
     * Update client informations
     */
    public function update($data, $id, $client_request = false)
    {
        
        
        $affectedRows = 0;
        
        $data = $this->check_zero_columns($data);


        //$hook_data                = do_action('before_client_added', array('data'=>$data));
        //$data = $hook_data['data'];
        //$tmp = json_encode($data['managing_directors']);
        //unset($data['managing_directors']);
        //$data['managing_directors'] = $tmp;
        
        unset($data['liability']);
        unset($data['nature']);
        unset($data['due']);
        unset($data['overdue']);
        unset($data['principal_amount']);
        unset($data['DataTables_Table_0_length']);
        unset($data['DataTables_Table_2_length']);
        unset($data['DataTables_Table_3_length']);
        unset($data['DataTables_Table_1_length']);


        unset($data['clients_import']);
        unset($data['recovery_id']);
        unset($data['file_csv']);
        $this->db->where('id', $id);
        $this->db->update('tblcorporate_recoveries', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;

        }
        /*$assign['customer_admins']   = array();
        if($data['assigned_to'] != ''){
           $assign['customer_admins'][] = $data['assigned_to'];
           $this->assign_admins($assign, $id);
        }*/
        


        if ($affectedRows > 0) {

            /*if($data['installment_start_date'] != '' && $data['number_of_installments'] > 0){
               $start_date = $data['installment_start_date'];
                for ($i=1; $i <= $data['number_of_installments']; $i++) { 
                     
                     $installment['defaulter_id']  = $id;
                     $installment['installment_date']  =  $start_date;
                     $installment['installment_amount']  = 0;
                     $installment['installment_status']  = 'not_paid';

                     $start_date = date('Y-m-d',strtotime("+$i months",strtotime($start_date)));
                     $this->add_installment($installment,$id);

                }  

            }*/
            //do_action('after_client_updated', $id);
            log_activity('Corporate Recovery Info Updated [' . $data['debtor_title'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Update contact data
     * @param  array  $data           $_POST data
     * @param  mixed  $id             contact id
     * @param  boolean $client_request is request from customers area
     * @return mixed
     */
    public function update_contact($data, $id, $client_request = false)
    {
        $affectedRows = 0;
        $contact = $this->get_contact($id);
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $this->load->helper('phpass');
            $hasher                       = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']             = $hasher->HashPassword($data['password']);
            $data['last_password_change'] = date('Y-m-d H:i:s');
        }

        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;
        $set_password_email_sent = false;

        $permissions = isset($data['permissions']) ? $data['permissions'] : array();
        $data['is_primary'] = isset($data['is_primary']) ? 1 : 0;

        // Contact cant change if is primary or not
        if ($client_request == true) {
            unset($data['is_primary']);
            if (isset($data['email'])) {
                unset($data['email']);
            }
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if ($client_request == false) {
            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 :0;
            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 :0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 :0;
            $data['contract_emails'] = isset($data['contract_emails']) ? 1 :0;
            $data['task_emails'] = isset($data['task_emails']) ? 1 :0;
            $data['project_emails'] = isset($data['project_emails']) ? 1 :0;
        }

        $hook_data = do_action('before_update_contact', array('data'=>$data, 'id'=>$id));
        $data = $hook_data['data'];

        $this->db->where('id', $id);
        $this->db->update('tblcontacts', $data);

        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            if (isset($data['is_primary']) && $data['is_primary'] == 1) {
                $this->db->where('userid', $contact->userid);
                $this->db->where('id !=', $id);
                $this->db->update('tblcontacts', array(
                    'is_primary' => 0,
                ));
            }
        }

        if ($client_request == false) {
            $customer_permissions = $this->roles_model->get_contact_permissions($id);
            if (sizeof($customer_permissions) > 0) {
                foreach ($customer_permissions as $customer_permission) {
                    if (!in_array($customer_permission['permission_id'], $permissions)) {
                        $this->db->where('userid', $id);
                        $this->db->where('permission_id', $customer_permission['permission_id']);
                        $this->db->delete('tblcontactpermissions');
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
                foreach ($permissions as $permission) {
                    $this->db->where('userid', $id);
                    $this->db->where('permission_id', $permission);
                    $_exists = $this->db->get('tblcontactpermissions')->row();
                    if (!$_exists) {
                        $this->db->insert('tblcontactpermissions', array(
                            'userid' => $id,
                            'permission_id' => $permission,
                        ));
                        if ($this->db->affected_rows() > 0) {
                            $affectedRows++;
                        }
                    }
                }
            } else {
                foreach ($permissions as $permission) {
                    $this->db->insert('tblcontactpermissions', array(
                        'userid' => $id,
                        'permission_id' => $permission,
                    ));
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if ($send_set_password_email) {
                $set_password_email_sent = $this->authentication_model->set_password_email($data['email'], 0);
            }
        }
        if ($affectedRows > 0 && !$set_password_email_sent) {
            logActivity('Contact Updated [' . $data['firstname'] . ' ' . $data['lastname'] . ']');

            return true;
        } elseif ($affectedRows > 0 && $set_password_email_sent) {
            return array(
                'set_password_email_sent_and_profile_updated' => true,
            );
        } elseif ($affectedRows == 0 && $set_password_email_sent) {
            return array(
                'set_password_email_sent' => true,
            );
        }

        return false;
    }

    /**
     * Add new contact
     * @param array  $data               $_POST data
     * @param mixed  $customer_id        customer id
     * @param boolean $not_manual_request is manual from admin area customer profile or register, convert to lead
     */
    public function add_contact($data, $customer_id, $not_manual_request = false)
    {
        $send_set_password_email = isset($data['send_set_password_email']) ? true : false;

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['permissions'])) {
            $permissions = $data['permissions'];
            unset($data['permissions']);
        }

        $send_welcome_email = true;
        if (isset($data['donotsendwelcomeemail'])) {
            $send_welcome_email = false;
        } elseif (strpos($_SERVER['HTTP_REFERER'], 'register') !== false) {
            $send_welcome_email = true;
            // If client register set this auto contact as primary
            $data['is_primary'] = 1;
        }

        if (isset($data['is_primary'])) {
            $data['is_primary'] = 1;
            $this->db->where('userid', $customer_id);
            $this->db->update('tblcontacts', array(
                'is_primary' => 0,
            ));
        } else {
            $data['is_primary'] = 0;
        }

        $password_before_hash  = '';
        $data['userid'] = $customer_id;
        if (isset($data['password'])) {
            $password_before_hash = $data['password'];
            $this->load->helper('phpass');
            $hasher              = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
            $data['password']    = $hasher->HashPassword($data['password']);
        }

        $data['datecreated'] = date('Y-m-d H:i:s');

        if (!$not_manual_request) {
            $data['invoice_emails'] = isset($data['invoice_emails']) ? 1 :0;
            $data['estimate_emails'] = isset($data['estimate_emails']) ? 1 :0;
            $data['credit_note_emails'] = isset($data['credit_note_emails']) ? 1 :0;
            $data['contract_emails'] = isset($data['contract_emails']) ? 1 :0;
            $data['task_emails'] = isset($data['task_emails']) ? 1 :0;
            $data['project_emails'] = isset($data['project_emails']) ? 1 :0;
        }

        $hook_data = array(
            'data' => $data,
            'not_manual_request' => $not_manual_request,
        );

        $hook_data = do_action('before_create_contact', $hook_data);
        $data  = $hook_data['data'];

        $data['email'] = trim($data['email']);

        $this->db->insert('tblcontacts', $data);
        $contact_id = $this->db->insert_id();

        if ($contact_id) {
            if (isset($custom_fields)) {
                handle_custom_fields_post($contact_id, $custom_fields);
            }
            // request from admin area
            if (!isset($permissions) && $not_manual_request == false) {
                $permissions = array();
            } elseif ($not_manual_request == true) {
                $permissions         = array();
                $_permissions        = get_contact_permissions();
                $default_permissions = @unserialize(get_option('default_contact_permissions'));
                if (is_array($default_permissions)) {
                    foreach ($_permissions as $permission) {
                        if (in_array($permission['id'], $default_permissions)) {
                            array_push($permissions, $permission['id']);
                        }
                    }
                }
            }

            if ($not_manual_request == true) {
                // update all email notifications to 0
                $this->db->where('id', $contact_id);
                $this->db->update('tblcontacts', array(
                    'invoice_emails'=>0,
                    'estimate_emails'=>0,
                    'credit_note_emails'=>0,
                    'contract_emails'=>0,
                    'task_emails'=>0,
                    'project_emails'=>0,
                ));
            }
            foreach ($permissions as $permission) {

                $this->db->insert('tblcontactpermissions', array(
                    'userid' => $contact_id,
                    'permission_id' => $permission,
                ));

                // Auto set email notifications based on permissions
                if ($not_manual_request == true) {
                    if ($permission == 6) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('project_emails'=>1, 'task_emails'=>1));
                    } elseif ($permission == 3) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('contract_emails'=>1));
                    } elseif ($permission == 2) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('estimate_emails'=>1));
                    } elseif ($permission == 1) {
                        $this->db->where('id', $contact_id);
                        $this->db->update('tblcontacts', array('invoice_emails'=>1, 'credit_note_emails'=>1));
                    }
                }
            }

            $lastAnnouncement = $this->db->query("SELECT announcementid FROM tblannouncements WHERE showtousers = 1 AND announcementid = (SELECT MAX(announcementid) FROM tblannouncements)")->row();
            if ($lastAnnouncement) {
                // Get all announcements and set it to read.
                $this->db->select('announcementid')
                ->from('tblannouncements')
                ->where('showtousers', 1)
                ->where('announcementid !=', $lastAnnouncement->announcementid);

                $announcements = $this->db->get()->result_array();
                foreach ($announcements as $announcement) {
                    $this->db->insert('tbldismissedannouncements', array(
                        'announcementid' => $announcement['announcementid'],
                        'staff' => 0,
                        'userid' => $contact_id,
                    ));
                }
            }
            if ($send_welcome_email == true) {
                $this->load->model('emails_model');
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($data['userid'], $contact_id, $password_before_hash));
                $this->emails_model->send_email_template('new-client-created', $data['email'], $merge_fields);
            }

            if ($send_set_password_email) {
                $this->authentication_model->set_password_email($data['email'], 0);
            }

            logActivity('Contact Created [' . $data['firstname'] . ' ' . $data['lastname'] . ']');
            do_action('contact_created', $contact_id);

            return $contact_id;
        }

        return false;
    }

    /**
     * Used to update company details from customers area
     * @param  array $data $_POST data
     * @param  mixed $id
     * @return boolean
     */
    public function update_company_details($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }
        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }
        if (isset($data['billing_country']) && $data['billing_country'] == '') {
            $data['billing_country'] = 0;
        }
        if (isset($data['shipping_country']) && $data['shipping_country'] == '') {
            $data['shipping_country'] = 0;
        }

        // From v.1.9.4 these fields are textareas
        $data['address'] = trim($data['address']);
        $data['address'] = nl2br($data['address']);
        if (isset($data['billing_street'])) {
            $data['billing_street'] = trim($data['billing_street']);
            $data['billing_street'] = nl2br($data['billing_street']);
        }
        if (isset($data['shipping_street'])) {
            $data['shipping_street'] = trim($data['shipping_street']);
            $data['shipping_street'] = nl2br($data['shipping_street']);
        }

        $this->db->where('userid', $id);
        $this->db->update('tblclients', $data);
        if($this->db->affected_rows() > 0){
            $affectedRows++;
        }
        if ($affectedRows > 0) {
            do_action('customer_updated_company_info', $id);
            logActivity('Customer Info Updated From Clients Area [' . $data['company'] . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer staff members that are added as customer admins
     * @param  mixed $id customer id
     * @return array
     */
    public function get_admins($id)
    {
        $this->db->where('customer_id', $id);
        $this->db->where('recovery_type','corporate'); 
        return $this->db->get('tblrecoveryadmins')->result_array();
    }

    /**
     * Get unique staff id's of customer admins
     * @return array
     */
    public function get_customers_admin_unique_ids()
    {
        return $this->db->query('SELECT DISTINCT(staff_id) FROM tblrecoveryadmins')->result_array();
    }

    /**
     * Assign staff members as admin to customers
     * @param  array $data $_POST data
     * @param  mixed $id   customer id
     * @return boolean
     */
    public function assign_admins($data, $id)
    {
        $affectedRows = 0;

        if (count($data) == 0) {
            $this->db->where('customer_id', $id);
            $this->db->where('recovery_type','corporate'); 
            $this->db->delete('tblrecoveryadmins');
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $current_admins     = $this->get_admins($id);
            $current_admins_ids = array();
            foreach ($current_admins as $c_admin) {
                array_push($current_admins_ids, $c_admin['staff_id']);
            }
            foreach ($current_admins_ids as $c_admin_id) {
                if (!in_array($c_admin_id, $data['customer_admins'])) {
                    $this->db->where('staff_id', $c_admin_id);
                    $this->db->where('customer_id', $id);
                    $this->db->where('recovery_type','corporate'); 
                    $this->db->delete('tblrecoveryadmins');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            foreach ($data['customer_admins'] as $n_admin_id) {
                if (total_rows('tblrecoveryadmins', array(
                    'customer_id' => $id,
                    'staff_id' => $n_admin_id,
                    'recovery_type'=>'corporate'
                )) == 0) {
                    $this->db->insert('tblrecoveryadmins', array(
                        'customer_id' => $id,
                        'staff_id' => $n_admin_id,
                        'date_assigned' => date('Y-m-d H:i:s'),
                        'recovery_type'=>'corporate',
                        'assigned_by' => get_staff_user_id()
                    ));
   

                    log_activity(' Corporate Recovery  [ '.get_recovers_name($id). '- ID :'.  $id . '] assigned to '.get_staff_full_name($n_admin_id));
                    $url = 'corporate_recoveries/corporate_recovery/'.$id;
                    $debtor_name = get_recovers_name($id);
                    if (get_staff_user_id() != $n_admin_id) {
                        
                        $notified = add_notification(array(
                            'description' => 'assigned_corporate_debtor',
                            'touserid' => $n_admin_id,
                            'link' => $url,
                            'additional_data' => serialize(array(
                                $debtor_name,
                            )),        
                        ));

                    }
                    // for assigner
                    $msg = 'You assigned a corporate recovery ['.get_recovers_name($id).'] to '; 
                    $notified = add_notification(array(

                            'description' => $msg.get_staff_full_name($n_admin_id),
                            'touserid' => get_staff_user_id() ,
                            'link' => $url,
                            'additional_data' => serialize(array(
                                $debtor_name,
                            )),        
                    ));

                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @return boolean
     * Delete client, also deleting rows from, dismissed client announcements, ticket replies, tickets, autologin, user notes
     */
    public function delete($id)
    {
        $affectedRows = 0;

        /*if (is_reference_in_table('clientid', 'tblinvoices', $id)
            || is_reference_in_table('clientid', 'tblestimates', $id)
            || is_reference_in_table('clientid', 'tblcreditnotes', $id)) {
            return array(
                'referenced' => true,
            );
        }*/

        do_action('before_client_deleted', $id);

        $this->db->where('id', $id);
        $this->db->delete('tblcorporate_recoveries');
        if ($this->db->affected_rows() > 0) {

            $affectedRows++;

            // Delete all tickets start here
            /*$this->db->where('userid', $id);
            $tickets = $this->db->get('tbltickets')->result_array();
            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }*/

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'corporate');
            $this->db->delete('tblnotes');

            $this->db->where('customer_id', $id);
            $this->db->where('recovery_type', 'corporate');
            $this->db->delete('tblrecoveryadmins');

            $this->db->where('recovery_id', $id);
            $this->db->where('recovery_type', 'corporate');
            $this->db->delete('tblreassigns');
            
            
            // Get customer related tasks
            $this->db->where('rel_type', 'recovery');
            $this->db->where('rel_id', $id);
            $tasks = $this->db->get('tblstafftasks')->result_array();

            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id']);
            }
            $this->db->where('rel_type', 'recovery');
            $this->db->where('rel_id', $id);
            $this->db->delete('tblreminders');


            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'recovery');
            $attachments = $this->db->get('tblfiles')->result_array();
            foreach ($attachments as $attachment) {
                $this->delete_attachment($attachment['id']);
            }
        }
        if ($affectedRows > 0) {
            do_action('after_client_deleted', $id);
            logActivity('Corporate Recovery Deleted [' . $id . ']');

            return true;
        }

        return false;
    }

    /**
     * Delete customer contact
     * @param  mixed $id contact id
     * @return boolean
     */
    public function delete_contact($id)
    {
        $this->db->select('userid');
        $this->db->where('id', $id);
        $result      = $this->db->get('tblcontacts')->row();
        $customer_id = $result->userid;
        do_action('before_delete_contact', $id);
        $this->db->where('id', $id);
        $this->db->delete('tblcontacts');
        if ($this->db->affected_rows() > 0) {
            if (is_dir(get_upload_path_by_type('contact_profile_images') . $id)) {
                delete_dir(get_upload_path_by_type('contact_profile_images') . $id);
            }

            $this->db->where('contact_id', $id);
            $this->db->delete('tblcustomerfiles_shares');

            $this->db->where('userid', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbldismissedannouncements');

            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'contacts');
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('userid', $id);
            $this->db->delete('tblcontactpermissions');

            // Delete autologin if found
            $this->db->where('user_id', $id);
            $this->db->where('staff', 0);
            $this->db->delete('tbluserautologin');

            $this->db->select('ticketid');
            $this->db->where('contactid', $id);
            $this->db->where('userid', $customer_id);
            $tickets = $this->db->get('tbltickets')->result_array();

            $this->load->model('tickets_model');
            foreach ($tickets as $ticket) {
                $this->tickets_model->delete($ticket['ticketid']);
            }

            $this->db->where('contactid', $id);
            $this->db->where('userid', $customer_id);
            $this->db->delete('tblticketreplies');

            return true;
        }

        return false;
    }

    /**
     * Get customer default currency
     * @param  mixed $id customer id
     * @return mixed
     */
    public function get_customer_default_currency($id)
    {
        $this->db->select('default_currency');
        $this->db->where('userid', $id);
        $result = $this->db->get('tblclients')->row();
        if ($result) {
            return $result->default_currency;
        }

        return false;
    }

    /**
     *  Get customer billing details
     * @param   mixed $id   customer id
     * @return  array
     */
    public function get_customer_billing_and_shipping_details($id)
    {
        $this->db->select('billing_street,billing_city,billing_state,billing_zip,billing_country,shipping_street,shipping_city,shipping_state,shipping_zip,shipping_country');
        $this->db->from('tblclients');
        $this->db->where('userid', $id);

        $result = $this->db->get()->result_array();
        if (count($result) > 0) {
            $result[0]['billing_street'] = clear_textarea_breaks($result[0]['billing_street']);
            $result[0]['shipping_street'] = clear_textarea_breaks($result[0]['shipping_street']);
        }

        return $result;
    }

    /**
     * Get customer files uploaded in the customer profile
     * @param  mixed $id    customer id
     * @param  array  $where perform where
     * @return array
     */
    public function get_customer_files($id, $where = array())
    {
        $this->db->where($where);
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'customer');
        $this->db->order_by('dateadded', 'desc');

        return $this->db->get('tblfiles')->result_array();
    }

    /**
     * Delete customer attachment uploaded from the customer profile
     * @param  mixed $id attachment id
     * @return boolean
     */
    public function delete_attachment($id)
    {
        $this->db->where('id', $id);
        $attachment = $this->db->get('tblfiles')->row();
        $deleted    = false;
        if ($attachment) {
            if (empty($attachment->external)) {
                $relPath = get_upload_path_by_type('corporate') . $attachment->rel_id . '/';
                $fullPath =$relPath.$attachment->file_name;
                unlink($fullPath);
                $fname = pathinfo($fullPath, PATHINFO_FILENAME);
                $fext = pathinfo($fullPath, PATHINFO_EXTENSION);
                $thumbPath = $relPath.$fname.'_thumb.'.$fext;
                if (file_exists($thumbPath)) {
                    unlink($thumbPath);
                }
            }

            $this->db->where('id', $id);
            $this->db->delete('tblfiles');
            if ($this->db->affected_rows() > 0) {
                $deleted = true;
                /*$this->db->where('file_id', $id);
                $this->db->delete('tblcustomerfiles_shares');*/
                logActivity('Defaulter Attachment Deleted [ID: ' . $attachment->rel_id . ']');
            }

            if (is_dir(get_upload_path_by_type('corporate') . $attachment->rel_id)) {
                // Check if no attachments left, so we can delete the folder also
                $other_attachments = list_files(get_upload_path_by_type('corporate') . $attachment->rel_id);
                if (count($other_attachments) == 0) {
                    delete_dir(get_upload_path_by_type('corporate') . $attachment->rel_id);
                }
            }
        }

        return $deleted;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update contact status Active/Inactive
     */
    public function change_contact_status($id, $status)
    {
        $hook_data['id']     = $id;
        $hook_data['status'] = $status;
        $hook_data           = do_action('change_contact_status', $hook_data);
        $status              = $hook_data['status'];
        $id                  = $hook_data['id'];
        $this->db->where('id', $id);
        $this->db->update('tblcontacts', array(
            'active' => $status,
        ));
        if ($this->db->affected_rows() > 0) {
            logActivity('Contact Status Changed [ContactID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_client_status($id, $status)
    {
        $this->db->where('userid', $id);
        $this->db->update('tblclients', array(
            'active' => $status,
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Customer Status Changed [CustomerID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    /**
     * @param  mixed $_POST data
     * @return mixed
     * Change contact password, used from client area
     */
    public function change_contact_password($data)
    {
        $hook_data['data'] = $data;
        $hook_data         = do_action('before_contact_change_password', $hook_data);
        $data              = $hook_data['data'];

        // Get current password
        $this->db->where('id', get_contact_user_id());
        $client = $this->db->get('tblcontacts')->row();
        $this->load->helper('phpass');
        $hasher = new PasswordHash(PHPASS_HASH_STRENGTH, PHPASS_HASH_PORTABLE);
        if (!$hasher->CheckPassword($data['oldpassword'], $client->password)) {
            return array(
                'old_password_not_match' => true,
            );
        }
        $update_data['password']             = $hasher->HashPassword($data['newpasswordr']);
        $update_data['last_password_change'] = date('Y-m-d H:i:s');
        $this->db->where('id', get_contact_user_id());
        $this->db->update('tblcontacts', $update_data);
        if ($this->db->affected_rows() > 0) {
            logActivity('Contact Password Changed [ContactID: ' . get_contact_user_id() . ']');

            return true;
        }

        return false;
    }

    /**
     * Get customer groups where customer belongs
     * @param  mixed $id customer id
     * @return array
     */
    public function get_customer_groups($id)
    {
        return $this->client_groups_model->get_customer_groups($id);
    }

    /**
     * Get all customer groups
     * @param  string $id
     * @return mixed
     */
    public function get_groups($id = '')
    {
        return $this->client_groups_model->get_groups($id);
    }

    /**
     * Delete customer groups
     * @param  mixed $id group id
     * @return boolean
     */
    public function delete_group($id)
    {
        return $this->client_groups_model->delete($id);
    }

    /**
     * Add new customer groups
     * @param array $data $_POST data
     */
    public function add_group($data)
    {
        return $this->client_groups_model->add($data);
    }

    /**
     * Edit customer group
     * @param  array $data $_POST data
     * @return boolean
     */
    public function edit_group($data)
    {
        return $this->client_groups_model->edit($data);
    }

    /**
    * Create new vault entry
    * @param  array $data        $_POST data
    * @param  mixed $customer_id customer id
    * @return boolean
    */
    public function vault_entry_create($data, $customer_id)
    {
        return $this->client_vault_entries_model->create($data, $customer_id);
    }

    /**
     * Update vault entry
     * @param  mixed $id   vault entry id
     * @param  array $data $_POST data
     * @return boolean
     */
    public function vault_entry_update($id, $data)
    {
        return $this->client_vault_entries_model->update($id, $data);
    }

    /**
     * Delete vault entry
     * @param  mixed $id entry id
     * @return boolean
     */
    public function vault_entry_delete($id)
    {
        return $this->client_vault_entries_model->delete($id);
    }

    /**
     * Get customer vault entries
     * @param  mixed $customer_id
     * @param  array  $where       additional wher
     * @return array
     */
    public function get_vault_entries($customer_id, $where = array())
    {
        return $this->client_vault_entries_model->get_by_customer_id($customer_id, $where);
    }

    /**
     * Get single vault entry
     * @param  mixed $id vault entry id
     * @return object
     */
    public function get_vault_entry($id)
    {
        return $this->client_vault_entries_model->get($id);
    }

    /**
    * Get customer statement formatted
    * @param  mixed $customer_id customer id
    * @param  string $from        date from
    * @param  string $to          date to
    * @return array
    */
    public function get_statement($customer_id, $from, $to)
    {
        return $this->statement_model->get_statement($customer_id, $from, $to);
    }

    /**
    * Send customer statement to email
    * @param  mixed $customer_id customer id
    * @param  array $send_to     array of contact emails to send
    * @param  string $from        date from
    * @param  string $to          date to
    * @param  string $cc          email CC
    * @return boolean
    */
    public function send_statement_to_email($customer_id, $send_to, $from, $to, $cc = '')
    {
        return $this->statement_model->send_statement_to_email($customer_id, $send_to, $from, $to, $cc);
    }

    private function check_zero_columns($data)
    {
        if (!isset($data['show_primary_contact'])) {
            $data['show_primary_contact'] = 0;
        }

        if (isset($data['default_currency']) && $data['default_currency'] == '' || !isset($data['default_currency'])) {
            $data['default_currency'] = 0;
        }

        if (isset($data['country']) && $data['country'] == '' || !isset($data['country'])) {
            $data['country'] = 0;
        }

        /*if (isset($data['billing_country']) && $data['billing_country'] == '' || !isset($data['billing_country'])) {
            $data['billing_country'] = 0;
        }*/

        /*if (isset($data['shipping_country']) && $data['shipping_country'] == '' || !isset($data['shipping_country'])) {
            $data['shipping_country'] = 0;
        }
*/
        return $data;
    }

     /**
     * Get leads sources
     * @param  mixed $id Optional - Source ID
     * @return mixed object if id passed else array
     */
    public function get_source($id = false)
    {
        if (is_numeric($id)) {
            $this->db->where('id', $id);

            return $this->db->get('tblleadssources')->row();
        }

        return $this->db->get('tblleadssources')->result_array();
    }


    function add_installment($data ,$defaulter_id){
        $data['installment_date'] = date('Y-m-d',strtotime($data['installment_date']));
        $data['datecreated'] = date('Y-m-d H:i:s');

        $data['addedby'] = get_staff_user_id();
        $data['recovery_id'] = $defaulter_id;
        $data['recovery_type'] = 'corporate';
        unset($data['customer_id']);
        $this->db->insert('tblrecoveries_installments',$data);
        $a = $this->db->error();
        return $insert_id = $this->db->insert_id();
    }


    /**
     * @param  integer ID
     * @param  integer Status ID
     * @return boolean
     * Update client status Active/Inactive
     */
    public function change_defaulter_status($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblcorporate_recoveries', array(
            'active' => $status,
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Corporate Recovery Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

     /**
     * Delete customer contact
     * @param  mixed $id contact id
     * @return boolean
     */
    public function delete_installment($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete('tblrecoveries_installments');
        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }

    public function get_installment($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tblrecoveries_installments')->row();
    }

    public function update_installment($data,$id){

        $this->db->where('id',$id);
        $this->db->update('tblrecoveries_installments',$data);

        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }

    function add_partner($data){

        $tmp = $data['telephone'];
        unset($data['telephone']);
        $data['telephone'] = json_encode($tmp);
        if(isset($data['is_md'])){
            $data['is_md'] ='yes';
        }else{
            $data['is_md'] ='no';    
        }

        if(isset($data['is_partner'])){
            $data['is_partner'] ='y';
        }else{
            $data['is_partner'] ='n';    
        }
        if(isset($data['is_guarantor'])){
            $data['is_guarantor'] ='y';
        }else{
            $data['is_guarantor'] ='n';    
        }
        $data['recovery_type'] = 'corporate';
        //for excel import
        //$data['india_address'] = $data['house_name']."<br>".$data['p_o']."<br>".$data['district']."<br>".$data['state']." - ".$data['pin'];
        $this->db->insert('tblcorporate_recoveries_partners',$data);
        return $this->db->insert_id();
    }

    function update_partner($data,$id){

        $tmp = $data['telephone'];
        unset($data['telephone']);
        $data['telephone'] = json_encode($tmp);
        if(isset($data['is_md'])){
            $data['is_md'] ='yes';
        }else{
            $data['is_md'] ='no';    
        }
         if(isset($data['is_partner'])){
            $data['is_partner'] ='y';
        }else{
            $data['is_partner'] ='n';    
        }
        if(isset($data['is_guarantor'])){
            $data['is_guarantor'] ='y';
        }else{
            $data['is_guarantor'] ='n';    
        }
        //for excel import
        //$data['india_address'] = $data['house_name']."<br>".$data['p_o']."<br>".$data['district']."<br>".$data['state']." - ".$data['pin'];
        $this->db->where('id',$id);
        $this->db->update('tblcorporate_recoveries_partners',$data);
        return true;
    }

    

    public function delete_partner($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete('tblcorporate_recoveries_partners');
        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }

    public function get_partner($id,$where=array()){
        $this->db->where($where);
        $this->db->where('id', $id);
        return $this->db->get('tblcorporate_recoveries_partners')->row();


    }

     public function save_demand_notice($data ,$defaulter_id){
        $num_ro = $this->db->get_where('tbldebt_demandnotice',array('defaulter_id'=>$defaulter_id,'rel_name'=>'corporate'));
        $data['demand_notice_date'] = to_sql_date($data['demand_notice_date']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = get_staff_user_id();
        $data['defaulter_id'] = $defaulter_id;
        $data['rel_name'] = 'corporate';
        if($num_ro->num_rows() > 0){

            $this->db->where('defaulter_id',$defaulter_id);
            $this->db->update('tbldebt_demandnotice',$data);
            return true;
        }else{
            $this->db->insert('tbldebt_demandnotice',$data);
            return $insert_id = $this->db->insert_id();
        }
        return true;


        
    }
    public function get_demand_notice($id)
    {
        $this->db->where('defaulter_id', $id);
        $this->db->where('rel_name','corporate');
        return $this->db->get('tbldebt_demandnotice')->row();
    }

    public function mass_assign_admins($data, $id)
    {
        $affectedRows = 0;

        if (count($data) > 0) {
            
            $current_admins     = $this->get_admins($id);
            $current_admins_ids = array();
            foreach ($current_admins as $c_admin) {
                array_push($current_admins_ids, $c_admin['staff_id']);
            }
            
            foreach ($data['customer_admins'] as $n_admin_id) {
                if (total_rows('tblrecoveryadmins', array(
                    'customer_id' => $id,
                    'staff_id' => $n_admin_id,
                    'recovery_type'=>'corporate'
                )) == 0) { 
                    $this->db->insert('tblrecoveryadmins', array(
                        'customer_id' => $id,
                        'staff_id' => $n_admin_id,
                        'date_assigned' => date('Y-m-d H:i:s'),
                        'recovery_type' =>'corporate',
                        'assigned_by' => get_staff_user_id()
                    ));

                    logActivity(' Corporate Recovery  [ '.get_recovers_name($id). '- ID :'.  $id . '] assigned to '.get_staff_full_name($n_admin_id));

                    if (get_staff_user_id() != $n_admin_id) {
                        $url = 'corporate_recoveries/corporate_recovery/'.$id;
                        $debtor_name = get_recovers_name($id);
                        $notified = add_notification(array(
                            'description' => 'assigned_corporate_debtor',
                            'touserid' => $n_admin_id,
                            'link' => $url,
                            'additional_data' => serialize(array(
                                $debtor_name,
                            )),        
                        ));
                    }
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function verify_installment($id, $status)
    {
        $this->db->where('id', $id);
        $this->db->update('tblrecoveries_installments', array(
            'is_verified' => $status,
            'verified_date'=>date('Y-m-d H:i:s'),
            'verified_by'=>get_staff_user_id()
        ));

        if ($this->db->affected_rows() > 0) {
            logActivity('Installment Verified  [ID: ' . $id . ' : ' . $status . ']');

            return true;
        }

        return false;
    }

    public function get_all_debt_products($id)
    {
        $this->db->where('defaulter_id', $id);
         $this->db->where('rel_name','corporate');
        return $this->db->get('tbldebt_products')->result_array();
    }

    public function add_product($data ,$defaulter_id){
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['due_date'] = to_sql_date($data['due_date']);
        $data['addedfrom'] = get_staff_user_id();
        $data['defaulter_id'] = $defaulter_id;
        $data['rel_name'] = 'corporate';
        $this->db->insert('tbldebt_products',$data);
        return $insert_id = $this->db->insert_id();
    }

    public function delete_product($id)
    {
        
        $this->db->where('id', $id);
        $this->db->delete('tbldebt_products');
        if ($this->db->affected_rows() > 0) {

            return true;
        }

        return false;
    }


    public function search_debtor($term)
    {
        $this->db->select('tblcorporate_recoveries.company,id,mobile_no,email_id,new_mobile,mobile_other,country,emirate,address,address_1');
        $this->db->like('company', $term);
        $this->db->from('tblcorporate_recoveries');
        $data = $this->db->get()->result_array();
        $item = array();
        foreach ($data as $row) {
            $item[] = array("value"=>$row['company'],
                            "id"=>$row['id'],
                            "mobile_no"=>$row['mobile_no'],
                            "email_id"=>$row['email_id'],
                            "new_mobile"=>$row['new_mobile'],
                            "mobile_other"=>$row['mobile_other'],
                            "country"=>$row['country'],
                            "emirate"=>$row['emirate'],
                            "address"=>$row['address'],
                            "address_1"=>$row['address_1']);
        }
        return $item;
    }

    public function get_assigned_users($id){
        $this->db->where('recovery_type','corporate');
        $this->db->where('recovery_id', $id);
        return $this->db->get('tblreassigns')->result_array();
    }
    
}