<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo is_empty_customer_company($client->userid) ? _l('meetings') : _l('meetings'); ?></h4>

<?php if((has_permission('customers','','create') || is_customer_admin($client->userid)) && $client->registration_confirmed == '1'){
	
     ?>
<div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>" >
   <a href="#" onclick="constitution(<?php echo $client->userid; ?>,'',this); return false;" data-type="meeting" class="btn btn-info new-contact mbot25"><?php echo _l('add_meeting'); ?></a>
</div>
<?php } ?>
<?php
   
 $table_data =  array(_l('subject'),_l('document_type'),_l('issue_date'),_l('expiry_date'));
  
 echo render_datatable($table_data,'client_meeting'); ?>
<?php } ?>
<div id="constitution_data"></div>
<div id="consent_data"></div>

