<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo is_empty_customer_company($client->userid) ? _l('customer_attachments1') : _l('customer_attachments1'); ?></h4>

<?php if((has_permission('customers','','create') || is_customer_admin($client->userid)) && $client->registration_confirmed == '1'){
     ?>
<div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>" >
   <a href="#" onclick="constitution(<?php echo $client->userid; ?>); return false;" class="btn btn-info new-contact mbot25"><?php echo _l('add_vakalath'); ?></a>
</div>
<?php } ?>
<?php
   
 $table_data =  array(_l('matter_id'),_l('document_type'),_l('subject'),_l('issue_date'),_l('expiry_date'),_l('options'));
  
   echo render_datatable($table_data,'client_constitution'); ?>
   <?php $this->load->view('admin/clients/modals/send_subfile_modal'); ?>
<?php } ?>
<div id="constitution_data"></div>
<div id="consent_data"></div>

