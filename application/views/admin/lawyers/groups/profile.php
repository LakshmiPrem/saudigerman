<h4 class="customer-profile-group-heading"><?php echo _l('client_add_edit_profile'); ?></h4>
<div class="row">
   <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off')); ?>
   <div class="additional"></div>
   <div class="col-md-12">
      <ul class="nav nav-tabs profile-tabs row customer-profile-tabs" role="tablist">
         <li role="presentation" class="<?php if(!$this->input->get('tab')){echo 'active';}; ?>">
            <a href="#contact_info" aria-controls="contact_info" role="tab" data-toggle="tab">
               <?php echo _l( 'lawyer_profile_details'); ?>
            </a>
         </li>
         
      </ul>
      <div class="tab-content">
         <?php //hooks()->apply_filters('after_custom_profile_tab_content',isset($client) ? $client : false); ?>
         
         <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">
            <div class="row">
               
               <div class="col-md-6">
                
                  <?php 
                  $value=( isset($client) ? $client->firstname : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                  <?php echo render_input( 'firstname', 'firstname',$value,'text',$attrs); ?>
                   <?php 
                  $value=( isset($client) ? $client->lastname : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                  <?php echo render_input( 'lastname', 'lastname',$value,'text',$attrs); ?>
                 
                 <?php $value=( isset($client) ? $client->phonenumber : ''); ?>
                 <?php echo render_input( 'phonenumber', 'client_phonenumber',$value); ?>
                 <?php $value=( isset($client) ? $client->email : ''); ?>
                 <?php echo render_input( 'email', 'client_email_id',$value); ?>
                 <?php if((isset($client) && empty($client->website)) || !isset($client)){
                   $value=( isset($client) ? $client->website : '');
                   echo render_input( 'website', 'client_website',$value);
                } else { ?>
                <div class="form-group">
                  <label for="website"><?php echo _l('client_website'); ?></label>
                  <div class="input-group">
                     <input type="text" name="website" id="website" value="<?php echo $client->website; ?>" class="form-control">
                     <div class="input-group-addon">
                        <span><a href="<?php echo maybe_add_http($client->website); ?>" target="_blank" tabindex="-1"><i class="fa fa-globe"></i></a></span>
                     </div>
                  </div>
               </div>
               <?php } ?>

                <?php $value=( isset($client) ? $client->address : ''); ?>
               <?php echo render_textarea( 'address', 'client_address',$value); ?>
               
             

            </div>
            <div class="col-md-6">
              <?php $selected = (isset($client) ? $client->category_id : '');
               echo render_select_with_input_group('category_id',$categories,array('id','name'),'category',$selected,'<a href="#" onclick="new_category();return false;"><i class="fa fa-plus"></i></a>');?>
               <?php $value=( isset($client) ? $client->city : ''); ?>
               <?php echo render_input( 'city', 'client_city',$value); ?>
               <div class='hide'>
               <?php $emirates_arr = get_emirates();
               $selected=( isset($client) ? $client->emirate : ''); ?>
               <?php  echo render_select('emirate',$emirates_arr,array('id',array( 'name')),'lead_state',$selected); ?>
               </div>
               <?php $value=( isset($client) ? $client->po_box : ''); ?>
               <?php echo render_input( 'po_box', 'client_postal_code',$value); ?>
               <?php $countries= get_all_countries();
               $customer_default_country = get_option('customer_default_country');
               $selected =( isset($client) ? $client->country : $customer_default_country);
               echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'clients_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
               ?>


               <?php if(isset($client)){ ?>
                         <?php echo staff_profile_image($client->staffid,array('img','img-responsive','staff-profile-image-thumb'),'thumb'); ?>
                        <?php if(!empty($client->profile_image)){ ?>
                       
                        <a href="<?php echo admin_url('staff/remove_staff_profile_image/'.$client->staffid); ?>"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                        <hr />
                        <?php } ?>
                        <div id="contact-profile-image" class="form-group<?php if(isset($client) && !empty($client->profile_image)){echo ' hide';} ?>">
                            <label for="profile_image" class="profile-image"><?php echo _l('client_profile_image'); ?></label>
                            <input type="file" name="profile_image" class="form-control" id="profile_image">
                        </div>
                       <!-- <hr> 
                          <div class="checkbox checkbox-primary">
                              <?php
                                 $is_lawyer = '';
                                 if(isset($client)) {
                                  if($client->is_lawyer == 1){
                                   $is_lawyer = 'checked';
                                 }
                                 }
                                 ?>
                              <input type="checkbox" name="is_lawyer" id="is_lawyer" <?php echo $is_lawyer; ?> value="1">
                              <label for="send_welcome_email"><?php echo _l('is_lawyer'); ?></label>
                           </div>
               -->


            </div>
         </div>
      </div>
     
      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<div id="contact_data"></div>
<?php if(isset($client)){ ?>
<?php if (has_permission('lawyers', '', 'create') || has_permission('lawyers', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('clients/assign_admins/'.$client->userid)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('assign_admin'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach($customer_admins as $c_admin){
               array_push($selected,$c_admin['staff_id']);
            }
            echo render_select('customer_admins[]',$staff,array('staffid',array('firstname','lastname')),'',$selected,array('multiple'=>true),array(),'','',false); ?>
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      </div>
      <!-- /.modal-content -->
      <?php echo form_close(); ?>
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php } ?>
<?php } ?>
<?php 
$this->load->view('admin/lawyers/modals/lawyer_category'); ?>

