<?php

?>
<style type="text/css">
  .panel-info>.panel-heading {
    color: #31708f;
    background-color: #d9edf7;
    border-color: #bce8f1;
    font-weight: bold;
    text-transform: uppercase;
}
</style>

<h4 class="customer-profile-group-heading"><?php echo _l('debtor_add_edit_profile'); ?></h4>
<div class="row">
   <?php echo form_open($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs" role="tablist">
         <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
            <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
               <?php echo _l( 'details'); ?>
            </a>
         </li>
      </ul>
      <div class="tab-content">         
        <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
          <!--------------- First Section Start---------------------------->   
            <div class="panel ">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-4 border-right">
                  
                  <?php $value=( isset($client) ? $client->name : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true,'autocomplete'=>'off')); ?>
                  <?php echo render_input('name', 'opposite_company',$value,'text',$attrs); ?>
                  </div>
                  

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->firstname : ''); ?>
                    <?php echo render_input( 'firstname', 'firstname',$value); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->lastname : ''); ?>
                    <?php echo render_input( 'lastname', 'lastname',$value,'text'); ?>
                  </div>
                  <div class="col-md-4">
                  <div class="form-group select-placeholder f_client_id">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('contract_client_string'); ?></label>
                     <select id="clientid" name="client_id" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php $selected = (isset($client) ? $client->client_id : '');
                        if($selected == ''){
                         $selected = (isset($customer_id) ? $customer_id: '');
                      }
                      if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                     } ?>
                      </select>
                  </div>
                 </div>
                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->email : ''); ?>
                    <?php echo render_input( 'email', 'email',$value); ?>
                  </div>

                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->mobile : ''); ?>
                    <?php echo render_input( 'mobile', 'mobile',$value); ?>
                  </div>
                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->city : ''); ?>
                    <?php echo render_input( 'city', 'city',$value); ?>
                  </div>
                  
                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->address : ''); ?>
                      <?php echo render_textarea( 'address', 'address',$value); ?>
                  </div>
                  
                </div>
              </div>
            </div>
            <!--------------- First Section End ------------------------------>
        
      </div>
     
    
   </div>
   <?php echo form_close(); ?>
</div>

