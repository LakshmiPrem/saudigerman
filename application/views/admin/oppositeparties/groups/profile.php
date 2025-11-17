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

<h4 class="customer-profile-group-heading"><?php echo _l('debtor_add_edit_profile1'); ?></h4>
<div class="row">
   <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'client-form','autocomplete'=>'off')); ?>
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
                <div class="col-md-4">
                <?php  $selected = (isset($client) ? $client->type : '');?>
                <label><?php echo _l('type_of_other_party'); ?></label>
                               <div class="select-placeholder">
                                <select name="type" id="type" class="selectpicker" data-width="100%">
                                 <option value="1" <?php if($selected == 1)  echo 'selected'; ?> ><?php echo _l('individual'); ?></option>   
                                 <option value="2" <?php if($selected == 2)  echo 'selected'; ?> ><?php echo 'company'; ?></option>
                               </select>
                               </div>
                </div>

                <div class="col-md-4">
                <?php  $selected = (isset($client) ? $client->party_type : '');?>
                <?php 
                echo render_select('party_type',$party_type,array('id','provider_name'),'oppo_party_type',$selected);
                ?>
                </div>
                  <div class="col-md-4 border-right">
                  
                  <?php $value=( isset($client) ? $client->name : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true,'autocomplete'=>'off')); ?>
                  <?php echo render_input('name', 'opposite_company',$value,'text',$attrs); ?>
                  </div>
                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->email : ''); ?>
                    <?php echo render_input( 'email', 'email',$value); ?>
                  </div>
                  
					
                  
                <!--  <div class="col-md-4">
                  <div class="form-group select-placeholder f_client_id">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('contract_client_string1'); ?></label>
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
                    <?php $value=( isset($client) ? $client->nationality : ''); ?>
                    <?php echo render_input( 'nationality', 'nationality',$value); ?>
                     
                  </div>-->
                  
                  <div class="col-md-4 ">
                   <?php $cities=get_emirates();?>
                    <?php $value=( isset($client) ? $client->city : ''); ?>
                   
                     <?php //echo render_select('city',$cities,array('id','name'),'city',$value);
                     echo render_input( 'city', 'city',$value); ?>
                  </div>
                  
                 

                  
                  



          
                <div class="hide" id="company">
                <!-- <div class="col-md-4">
                    <?php $value=( isset($client) ? $client->company_registration_number: ''); ?>
                    <?php echo render_input( 'company_registration_number', 'company_registration_number',$value); ?>
                  </div>
                  <div class="col-md-4">
                
                <?php $value=( isset($client) ? _d($client->company_registration_date) : _d(date('Y-m-d'))); ?>
              <?php echo render_date_input( 'company_registration_date', 'company_registration_date',$value); ?>
            </div> -->
            <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->tradelicence : ''); ?>
                    <?php echo render_input( 'tradelicence', 'trade_licence',$value); ?>
                  </div>
                    <div class="col-md-4">
                
                    <?php $value=( isset($client) ? _d($client->trade_commence_date) : _d(date('Y-m-d'))); ?>
              		<?php echo render_date_input( 'trade_commence_date', 'trade_commence_date',$value); ?>
            		</div>
                    <div class="col-md-4">
                
                    <?php $value=( isset($client) ? _d($client->trade_expiry) : _d(date('Y-m-d'))); ?>
              		<?php echo render_date_input( 'trade_expiry', 'trade_expiry_dt',$value); ?>
            		</div>
                  
                   
                 
                   <div class="col-md-4">  
            <?php $yes_no_arr = [['id'=>'yes','name'=>'Active'],['id'=>'no','name'=>'Closed']]  ?>
            <?php  $selected = (isset($client) ? $client->company_status : 'yes');?>
            <?php echo render_select('company_status',$yes_no_arr,array('id','name'),'company_status',$selected);?>

          </div>
            </div>
               
            <div  id="individual">
            <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->firstname: ''); ?>
                    <?php echo render_input( 'firstname', 'firstname',$value); ?>
                  </div>
                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->lastname: ''); ?>
                    <?php echo render_input( 'lastname', 'lastname',$value); ?>
                  </div>
                  <div class="col-md-4">
                <?php  $selected = (isset($client) ? $client->gender : '');?>
                <label><?php echo _l('gender'); ?></label>
                               <div class="select-placeholder">
                                <select name="gender" id="gender" class="selectpicker" data-width="100%">
                                 <option value="1" <?php if($selected == 1)  echo 'selected'; ?> ><?php echo _l('male'); ?></option>   
                                 <option value="2" <?php if($selected == 2)  echo 'selected'; ?> ><?php echo _l('female'); ?></option>
                               </select>
                               </div>
                </div>
                </div>



                  
                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->address : ''); ?>
                      <?php echo render_textarea( 'address', 'address',$value,array( 'rows'=>2)); ?>
                      
                  </div>

                  

                           <div class="col-md-6">
                
         <div class="form-group">
                                <label for="installment_receipt" class="profile-image"><?php echo _l('image'); ?></label>
                                <input type="file" name="profile_image" class="form-control" id="profile_image">
                             </div>
                        <?php if((isset($client) && $client->profile_image != NULL) ){ ?>
                             <?php echo '<b>Updated Date:</b> '._d($client->profile_date); 
							   $extension = pathinfo($client->profile_image, PATHINFO_EXTENSION);
							   if($extension!='pdf'){?>
                            <div class="img">
                                <?php $path = get_upload_path_by_type('oppositeparty').'/'.$client->id.'/'; ?>
                                <img class="img-responsive" src="<?php echo base_url('uploads/oppositeparty/').$client->id.'/'.$client->profile_image; ?>">
                            </div>

                        <?php }else{ ?> 
               <div class="img">
               <a target="_blank" href=<?php echo base_url('uploads/oppositeparty/').$client->id.'/'.$client->profile_image; ?> download ><i class="fa fa-download"></i></a>
							   </div>  
               <?php }}?>
                
                </div>
                <!-- hudden fields -->
                <div class="hide">

                <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->company_nature : ''); ?>
                    <?php echo render_input( 'company_nature', 'company_nature',$value); ?>
                  </div>
                 
                  <div class="col-md-4 border-right">  
            <?php $owner_arr = [['id'=>'uae','name'=>'Inside UAE'],['id'=>'absconded','name'=>'Absconded']]  ?>
            <?php  $selected = (isset($client) ? $client->owner_status : 'uae');?>
            <?php echo render_select('owner_status',$owner_arr,array('id','name'),'owner_status',$selected);?>

          </div>
          

                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->mobile : ''); ?>
                    <?php echo render_input( 'mobile', 'contact1',$value); ?>
                  </div>
                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->contact2: ''); ?>
                    <?php echo render_input( 'contact2', 'contact2',$value); ?>
                  </div>
                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->makani_number : ''); ?>
                    <?php echo render_input( 'makani_number', 'makani_no',$value); ?>
                  </div>
					<div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->jurisdiction: ''); ?>
                    <?php echo render_input( 'jurisdiction', 'jurisdiction',$value); ?>
                  </div>
                  <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->credit_limit_days: ''); ?>
                    <?php echo render_input( 'credit_limit_days', 'credit_limit_days',$value); ?>
                     
                  </div>
                  
                   <div class="col-md-4 ">
                    <?php $value=( isset($client) ? $client->credit_amount: ''); ?>
                    <?php echo render_input( 'credit_amount', 'credit_amount',$value); ?>
                     
                  </div>
                  <div class="col-md-6 border-right">
                    <?php $value=( isset($client) ? $client->current_maplocation : ''); ?>
                      <?php echo render_textarea( 'current_maplocation', 'current_maplocation',$value,array( 'rows'=>2)); ?>
                      <?php if ($value!='' ) echo '<b>Updated Date:</b> '._d($client->locationmap_dt); ?>
                  </div>

                </div>
                <!-- end hidden fields -->

                </div>
              </div>
            </div>
            <!--------------- First Section End ------------------------------>
        
      </div>
     
    
   </div>
   <?php echo form_close(); ?>
</div>

