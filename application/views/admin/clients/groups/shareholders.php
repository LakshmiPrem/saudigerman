<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo is_empty_customer_company($client->userid) ? _l('shareholders') : _l('shareholders'); ?></h4>
<?php if($this->session->flashdata('gdpr_delete_warning')){ ?>
    <div class="alert alert-warning">
     [GDPR] The contact you removed has associated proposals using the email address of the contact and other personal information. You may want to re-check all proposals related to this customer and remove any personal data from proposals linked to this contact.
   </div>
<?php } ?>
<?php if((has_permission('customers','','create') || is_customer_admin($client->userid)) && $client->registration_confirmed == '1'){
     ?>
<div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>" >
   <a href="#" onclick="shareholder(<?php echo $client->userid; ?>); return false;" class="btn btn-info new-contact mbot25"><?php echo _l('new_shareholder'); ?></a>
</div>
<?php } ?>
<?php
   $table_data = array(_l('shareholder_name'));

  $table_data = array_merge($table_data, array(_l('shareholder_percentage')));
     echo render_datatable($table_data,'clientshareholders'); ?>
<?php } ?>
<div id="shareholder_data"></div>
<div id="consent_data"></div>
