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
         <?php
         $customer_custom_fields = false;
         if(total_rows('tblcustomfields',array('fieldto'=>'customers','active'=>1)) > 0 ){
              $customer_custom_fields = true;
          ?>
          <li role="presentation" class="<?php if($this->input->get('tab') == 'custom_fields'){echo 'active';}; ?>">
            <a href="#custom_fields" aria-controls="custom_fields" role="tab" data-toggle="tab">
               <?php //echo do_action('customer_profile_tab_custom_fields_text',_l( 'custom_fields')); ?>
            </a>
         </li>
         <?php } ?>
          
          <?php if(isset($client)){ ?>
            <li role="presentation">
            <a href="#debtproducts" aria-controls="debtproducts" role="tab" data-toggle="tab">
               <?php echo _l( 'debtproducts'); ?>
            </a>
         </li>
          
       <?php } ?>
         <?php //do_action('after_customer_billing_and_shipping_tab',isset($client) ? $client : false); ?>
         <?php if(isset($client)){ ?>
         <li role="presentation<?php if($this->input->get('tab') && $this->input->get('tab') == 'contacts'){echo ' active';}; ?>">
            <a href="#contacts" aria-controls="contacts" role="tab" data-toggle="tab">
                <?php 
                  echo _l( 'installments');
              
                ?>
            </a>
         </li>
        <?php if(is_admin()){?>
         <li role="presentation">
            <a href="#customer_admins" aria-controls="customer_admins" role="tab" data-toggle="tab">
               <?php echo _l('assigned_users'); ?>
            </a>
         </li>
       <?php } ?>
        
         <?php //do_action('after_customer_admins_tab',$client); ?>
         <?php } ?>
      </ul>
      <div class="tab-content">
         <?php //do_action('after_custom_profile_tab_content',isset($client) ? $client : false); ?>
         <?php if($customer_custom_fields) { ?>
         <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'custom_fields'){echo 'active';}; ?>" id="custom_fields">
               <?php $rel_id=(isset($client) ? $client->userid : false); ?>
               <?php echo render_custom_fields( 'customers',$rel_id); ?>
         </div>
         <?php } ?>
         <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab')){echo ' active';}; ?>" id="contact_info">

            <!--------------- First Section Start---------------------------->   
            <div class="panel ">
              <div class="panel-body">
                <div class="row">
                  <div class="col-md-4 border-right">
                  
                  <?php $value=( isset($client) ? $client->debtor_title : ''); ?>
                  <?php $attrs = (isset($client) ? array() : array('autofocus'=>true,'autocomplete'=>'off')); ?>
                  <?php echo render_input('debtor_title', 'debtor_title',$value,'text',$attrs); ?>
                  </div>
                  <div class="col-md-4 border-right">
                    <?php 
                     $selected =( isset($client) ? $client->client_id : '');
                     if($selected == ''){
                     $selected = (isset($customer_id) ? $customer_id: '');
                      }
                     echo render_select( 'client_id',$clients,array( 'userid',array( 'company')), 'client',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                    ?>
                  </div>
                  <?php $attr= [];
                  if(!is_approver(get_staff_user_id())){ 
                    $attr['disabled'] = 'disabled';}
                   ?>
                  <div class="col-md-4 border-right">
                    <?php $status_arr = get_status_(); 
                    $selected = (isset($client) ? $client->status : 'submitted');
                    echo render_select('status',$status_arr,array('id','name'),'status',$selected,$attr);?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $next_file_number = get_option('next_corporate_file_no');?>
                    <?php $value=( isset($client) ? $client->file_no : $next_file_number); ?>
                    <?php echo render_input( 'file_no', 'file_no',$value); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? new_number_format($client->outstanding_amount) : ''); ?>
                    <?php $attrs = (isset($client) ? array() : array('autofocus'=>true)); ?>
                    <?php echo render_input( 'outstanding_amount', 'outstanding_amount',$value,'text',$attrs); ?>
                  </div>
                  
                 

                  <div class="col-md-2 ">
                    <?php $value=( isset($client) ? $client->age_of_the_debt : ''); ?>
                    <?php echo render_input( 'age_of_the_debt', 'age_of_the_debt',$value); ?>
                  </div>
                  <div class="col-md-1 border-right">
                    <label class="mtop30">Months</label>
                  </div>

  

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->last_update_from_debtor : ''); ?>
                      <?php echo render_textarea( 'last_update_from_debtor', 'last_update_from_debtor',$value); ?>
                  </div>

                 

                  
                </div>
              </div>
            </div>
            <!--------------- First Section End ------------------------------>

            <!--------------- Personal Info Section   Start---------------------------->   
            <div class="panel panel-info">
              <div class="panel-heading personal_info"  ><?=_l('personal_info')?></div> 
              <div class="panel-body">
                <div class="row">

                  <div class="col-md-4 border-right">
                    <div class="input-group">
                      <label><small class="req text-danger">* </small><?php echo _l('mobile_no');?></label>
                      <?php $value=( isset($client) ? $client->mobile_no : '+971'); ?>
                      <input type="text" class="form-control" name="mobile_no"  id="mobile_no" value="<?=$value?>" style="margin-bottom: 10px;" pattern="[0-9 _+]*">
                      <div class="input-group-btn">
                        <a class="btn btn-success" href="https://wa.me/<?=$value?>" target="_blank" >
                          <i class="fa fa-whatsapp"></i>
                        </a>
                      </div>
                    </div>
                  </div>

                  <div class="col-md-4 border-right">
                    <div class="input-group">
                      <label><?php echo _l('whatsapp_no');?></label>
                      <?php $value=( isset($client) ? $client->mobile_other : '+971'); ?>
                      <input type="text" class="form-control" name="mobile_other"  id="mobile_other" value="<?=$value?>" style="margin-bottom: 10px;" pattern="[0-9 _+]*">
                      <div class="input-group-btn">
                        <a class="btn btn-success" href="https://wa.me/<?=$value?>" target="_blank" >
                          <i class="fa fa-whatsapp"></i>
                        </a>
                      </div>
                    </div>
                  </div>



                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->email_id : ''); ?>
                    <?php echo render_input( 'email_id', 'client_email_id',$value,'email'); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $countries= get_all_countries();
                    $customer_default_country = get_option('customer_default_country');
                    $selected =( isset($client) ? $client->country : $customer_default_country);
                    echo render_select( 'country',$countries,array( 'country_id',array( 'short_name')), 'clients_country',$selected,array('data-none-selected-text'=>_l('dropdown_non_selected_tex')));
                     ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php  $emirates_arr = get_emirates();
                    $selected = (isset($client)) ? $client->emirate :'';
                    echo render_select('emirate',$emirates_arr,array('id',array( 'name')),'emirates',$selected); ?>  
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->city : ''); ?>
                    <?php echo render_input( 'city', 'city',$value); ?>
                  </div>

                  

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->address : ''); ?>
                    <?php echo render_textarea( 'address', 'office_address',$value); ?>
                  </div>
                  
                  <div class="col-md-4 border-right">
                      <?php $value=( isset($client) ? $client->debtor_current_location : ''); ?>
                      <?php echo render_textarea( 'debtor_current_location', 'current_location',$value); ?>
                  </div>

                </div>
              </div>
            </div>
            <!--------------- Personal Info Section End ------------------------->

             <!--------------- Contact Person Info Section  Start----------------->   
            <div class="panel panel-info">
              <div class="panel-heading personal_info"  ><?=_l('contact_person_info')?></div> 
              <div class="panel-body">
                <div class="row">
                
                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->contact_person_name : ''); ?>
                    <?php echo render_input( 'contact_person_name', 'contact_person_name',$value); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->contact_person_email : ''); ?>
                    <?php echo render_input( 'contact_person_email', 'contact_person_email',$value,'email'); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->contact_person_number : '+971'); ?>
                    <?php echo render_input( 'contact_person_number', 'contact_person_number',$value); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->contact_person_whatsapp : '+971'); ?>
                    <?php echo render_input( 'contact_person_whatsapp', 'contact_person_whatsapp',$value); ?>
                  </div>

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->contact_person_additional : ''); ?>
                    <?php echo render_textarea( 'contact_person_additional', 'contact_person_additional',$value); ?>
                  </div>

                </div>
              </div>
            </div>

             <!--------------- Contact Person Info Section  End----------------->   

            <!--------------- Other Info Section   Start---------------------------->   
            <div class="panel panel-info">
              <div class="panel-heading personal_info"  ><?=_l('other')?></div> 
              <div class="panel-body">
                <div class="row">
                 

                
                 

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->trade_license_no : ''); ?>
                    <?php echo render_input( 'trade_license_no', 'trade_license_no',$value); ?>
                  </div>
                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->trade_license_authority : ''); ?>
                    <?php echo render_input( 'trade_license_authority', 'trade_license_authority',$value); ?>
                  </div>

                 

                 

                  

                  

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->incorporation_year : ''); ?>
                    <?php echo render_input( 'incorporation_year', 'incorporation_year',$value); ?>
                  </div>

                  

                  <div class="col-md-4 border-right hide">
                    <?php if(!isset($client)){ ?>
                      <i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('customer_currency_change_notice'); ?>"></i>
                      <?php }
                      $s_attrs = array('data-none-selected-text'=>_l('system_default_string'));
                      $selected = '';
                      if(isset($client) && client_have_transactions($client->userid)){
                         $s_attrs['disabled'] = true;
                      }
                      foreach($currencies as $currency){
                         if(isset($client)){
                           if($currency['id'] == $client->default_currency){
                             $selected = $currency['id'];
                          }
                       }
                    }
                             // Do not remove the currency field from the customer profile!
                    echo render_select('default_currency',$currencies,array('id','name','symbol'),'invoice_add_edit_currency',$selected,$s_attrs); ?>
                  </div>

                  

                  <div class="col-md-4 border-right">
                    <?php $value=( isset($client) ? $client->remarks : ''); ?>
                    <?php echo render_textarea( 'remarks', 'remarks',$value); ?>
                  </div>
                 

                  <div class="col-md-4 hide">
                     <?php if(get_option('disable_language') == 0){ ?>
            <div class="form-group select-placeholder">
               <label for="default_language" class="control-label"><?php echo _l('localization_default_language'); ?>
               </label>
               <select name="default_language" id="default_language" class="form-control selectpicker" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                  <option value=""><?php echo _l('system_default_string'); ?></option>
                  <?php foreach(list_folders(APPPATH .'language') as $language){
                     $selected = '';
                     if(isset($client)){
                        if($client->default_language == $language){
                           $selected = 'selected';
                        }
                     }
                     ?>
                     <option value="<?php echo $language; ?>" <?php echo $selected; ?>><?php echo ucfirst($language); ?></option>
                     <?php } ?>
                  </select>
               </div>
               <?php } ?>
                  </div>


                </div>
              </div>
            </div>
           
                 
            

                
      </div>
      <?php if(isset($client)){ ?>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') && $this->input->get('tab') == 'contacts'){echo ' active';}; ?>" id="contacts">
             <div class="row">
              <div class="col-md-4">
                 <?php $value=( isset($client) ? $client->number_of_installments : ''); ?>
                        <?php echo render_input('number_of_installments', 'number_of_installments',$value,'number'); ?>
              </div>
              <div class="col-md-4">
                
                    <?php $value=( isset($client) ? _d($client->installment_start_date) : _d(date('Y-m-d'))); ?>
               <?php echo render_date_input( 'installment_start_date', 'installment_start_date',$value); ?>
              </div>
              <div class="col-md-4">
                <button type="button" style="margin-top: 31px;" id="btn_installment" class="btn btn-info">Save</button>
              </div>
            </div> 
            <hr> 

            <div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>" >
               <a href="#" onclick="installment(<?php echo $client->userid; ?>); return false;" class="btn btn-info new-contact mbot25"><?php echo _l('new_installment'); ?></a>

               <a href="<?php echo admin_url('corporate_recoveries/generate_settlement_document_word/'.$client->userid) ?>"  class="btn btn-default btn-with-tooltip mbot25" data-toggle="tooltip" title="<?php echo _l('settlement_document'); ?>" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i> Generate Settlement Document</a>

<!--  <a target="_blank" href="<?php echo admin_url('corporate_recoveries/settlement_form/'.$client->userid) ?>" class="btn btn-default btn-with-tooltip mbot25" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i>
                <?php echo _l('settlement_form'); ?>                 
               </a> -->
 <?php $path        = base_url('uploads/corporate_recovery/').$client->userid.'/settlement_doc/'; ?>
 <?php $file_path   = get_upload_path_by_type('corporate_recovery').$client->userid.'/settlement_doc/Settlement_Document.docx';

if(file_exists($file_path)){ ?>

               <a href="<?php echo $path.'Settlement_Document.docx'; ?>"  class="btn btn-danger btn-with-tooltip mbot25" data-toggle="tooltip" download title="<?php echo _l('settlement_document'); ?>" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i> Download Settlement Document</a>
  <?php } ?>
            </div>

            <div style="float: right;">
                   <label class="radio-inline">
      <input type="radio" name="settlement_type" onclick="update_settlement_type('one_time',<?php echo $client->userid ?>);" <?php if($client->settlement_type == 'one_time') { ?> checked <?php  } ?> value="one_time" > One Time Settlement
    </label>
    <label class="radio-inline">
      <input type="radio" <?php if($client->settlement_type == 'installment') { ?> checked <?php  } ?> name="settlement_type" value="installment" onclick="update_settlement_type('installment',<?php echo $client->userid ?>);" >Installment
    </label>
                 </div>


            <?php //} ?>
            
             <div class="row mbot15">
          
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
                <h3 class="bold"><span id="def_credit_limit"><?php echo app_format_money($client->outstanding_amount,$customer_currency->name); ?></span></h3>
                <span class="text-dark"><?php echo _l('outstanding_amount'); ?></span>
            </div>

            <?php 
             $totalpaid = 0;
              $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblrecoveries_installments` WHERE recovery_id = ? AND installment_status = ? AND recovery_type = ?',array($client->userid,'paid','corporate'))->row();
             if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
             }


            ?>
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
                <h3 class="bold"><span id="def_total_paid"><?php echo app_format_money($totalpaid,$customer_currency->name); ?></span></h3>
                <span class="text-dark"><?php echo _l('total_paid'); ?></span>
            </div>
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
                <h3 class="bold"><span id="def_balance"><?php echo app_format_money(replace_comas($client->outstanding_amount) - $totalpaid,$customer_currency->name); ?></span></h3>
                <span class="text-dark"><?php echo _l('balance'); ?></span>
            </div>
          </div>

            <?php
             $table_data = array(
              _l('installment_date'),
              _l('installment_amount'),
				  _l('paid_amount'),
				  _l('balance'),
              _l('installment_status'),
              _l('is_verified'),
              _l('verified_by'),
              _l('verified_date'),
              _l('remarks')
            );
            array_push($table_data,_l('options'));
            echo render_datatable($table_data,'installments'); ?>


         </div>
        
         <div role="tabpanel" class="tab-pane" id="customer_admins">
            <?php if (has_permission('corporate_recovery', '', 'create') || has_permission('corporate_recovery', '', 'edit')) { ?>
             <a href="#" data-toggle="modal" data-target="#customer_admins_assign" class="btn btn-info mbot30"><?php echo _l('assign_user'); ?></a> 
            <?php } ?>
            <table class="table dt-table" data-order-col="1" data-order-type="asc">
               <thead>
                  <tr>
                     <th><?php echo _l('staff_member'); ?></th>
                     <th><?php echo _l('customer_admin_date_assigned'); ?></th>
                     <?php if(has_permission('corporate_recovery','','create') || has_permission('corporate_recovery','','edit')){ ?>
                     <!-- <th ><?php echo _l('options'); ?></th> -->
                     <?php } ?>
                  </tr>
               </thead>
                <tbody>
                  <?php foreach($customer_admins as $c_admin){ ?>
                  <tr>
                     <td><a href="<?php echo admin_url('profile/'.$c_admin['staff_id']); ?>">
                        <?php echo staff_profile_image($c_admin['staff_id'], array(
                           'staff-profile-image-small',
                           'mright5'
                           ));
                           echo get_staff_full_name($c_admin['staff_id']); ?></a>
                     </td>
                     <td data-order="<?php echo $c_admin['date_assigned']; ?>"><?php echo _dt($c_admin['date_assigned']); ?></td>
                     <?php if(has_permission('corporate_recovery','','create') || has_permission('corporate_recovery','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('corporate_recoveries/delete_customer_admin/'.$client->userid.'/'.$c_admin['staff_id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
         </div>
         <?php } ?>

          <?php ###################### Products  Start################ ?>
         <div role="tabpanel" class="tab-pane" id="debtproducts">
            <div class="col-md-12">
            <a href="#" data-toggle="modal" data-target="#product_modal" class="btn btn-success  mbot15" ><?php echo _l('new_product'); ?></a>
            <div class="clearfix"></div>
            <table class="table dt-table table-debt-products" data-order-col="5" data-order-type="desc">
               <thead>
                  <tr>
                     <th><?php echo _l('product_name'); ?></th>
                     <th><?php echo _l('nature'); ?></th>
                     <th><?php echo _l('invoice_amount'); ?></th>
                     <th><?php echo _l('outstanding_amount'); ?></th>
                     <th><?php echo _l('due_date'); ?></th>
                     <th><?php echo _l('remarks'); ?></th>
                     <?php if(has_permission('corporate_recovery','','create') || has_permission('corporate_recovery','','edit')){ ?>
                     <th><?php echo _l('options'); ?></th>
                     <?php } ?>
                  </tr>
               </thead>
               <tbody>
                
                <?php foreach($debt_products as $debt_product){ ?>
                  <tr>
                     <td><?php echo $debt_product['product_name'] ?></td>
                     <td data-order="<?php echo $debt_product['nature']; ?>"><?php echo $debt_product['nature']; ?></td>
                     <td data-order="<?php echo $debt_product['principal_amount']; ?>"><?php echo app_format_money($debt_product['principal_amount'],$customer_currency->name); ?></td>
                     <td data-order="<?php echo $debt_product['outstanding_amount']; ?>"><?php echo app_format_money($debt_product['outstanding_amount'],$customer_currency->name); ?></td>
                     <td data-order="<?php echo $debt_product['due_date']; ?>"><?php echo _d($debt_product['due_date']); ?></td>
                     <td><?php echo nl2br($debt_product['remarks']); ?></td>
                     <?php if(has_permission('corporate_recovery','','create') || has_permission('corporate_recovery','','edit')){ ?>
                     <td>
                        <a href="<?php echo admin_url('corporate_recoveries/delete_product/'.$client->userid.'/'.$debt_product['id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                     <?php } ?>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
          </div>
         </div>

        <?php ###################### Products End ################# ?>     

      <div role="tabpanel" class="tab-pane" id="litigation">
        <div class="row">
          <div class="col-md-12">
            <div class="row">
              
            </div>
          </div>
        </div>
      </div>

      <div role="tabpanel" class="tab-pane" id="partners">
            <div class="row">
               <div class="col-md-12">
                   <a onclick="partner();return false;" class="btn btn-info mbot30"><?php echo _l('add_partner'); ?></a>


             <?php ###################### ?>
 
         <!--         <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
        <?php echo form_open(admin_url('corporate_recoveries/download_sample')); ?>
        <?php //echo form_hidden('download_sample','true'); ?>
         <a href="<?php echo admin_url('corporate_recoveries/download_sample');?>"  class="btn btn-success">Download Sample</a>
        <?php echo form_close(); ?>
        <?php $max_input = ini_get('max_input_vars');
        if(($max_input>0 && isset($total_rows_post) && $total_rows_post >= $max_input)){ ?>
        <div class="alert alert-warning">
          Your hosting provider has PHP setting <b>max_input_vars</b> at <?php echo $max_input;?>.<br/>
          Ask your hosting provider to increase the <b>max_input_vars</b> setting to <?php echo $total_rows_post;?> or higher or import less rows.
        </div>
        <?php } ?>
        
              <div class="row">
                <div class="col-md-4 mtop15">
                  <?php echo form_open_multipart(admin_url('corporate_recoveries/import_partner'),array('id'=>'import_form')) ;?>
                  <?php echo form_hidden('clients_import','true'); ?>
                  <?php echo form_hidden('recovery_id',$client->userid); ?>
                  <?php echo render_input('file_csv','choose_csv_file','','file'); ?>

                 
                  <div class="form-group">
                    <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                    
                  </div>
                  <?php echo form_close(); ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>    
                 -->
<?php ######################  ?>      

            <table class="table dt-table">
               <thead>
                  <tr>
                     <th><?php echo _l('name'); ?></th>
                     <th><?php echo 'Is MD ?'; ?></th>
                     <th><?php echo 'Is Partner ?'; ?></th>
                     <th><?php echo 'Is Guarantor ?'; ?></th>
                     <th><?php echo _l('passport_no'); ?></th>
                     <th><?php echo _l('email'); ?></th>
                     <!-- <th><?php echo _l('emirates_id'); ?></th>
                     <th><?php echo _l('adhar_card'); ?></th> -->
                     <th><?php echo _l('telephone'); ?></th>
                     <th><?php echo _l('address_uae'); ?></th>
                     <th><?php echo _l('address_ind'); ?></th>
                     <th><?php echo _l('options'); ?></th>
                  </tr>
               </thead>
               <tbody>
                  <?php foreach($partners as $partner){ ?>
                  <tr>
                     <td><a onclick="partner(<?php echo $partner['id'];?>); return false;"><?=$partner['name']?></a></td>
                     <td><?=ucfirst($partner['is_md'])?></td>
                     <td><?=ucfirst($partner['is_partner'])?></td>
                     <td><?=ucfirst($partner['is_guarantor'])?></td>
                     <td><?=$partner['passport_no']?></td>
                     <td><?php echo $partner['pt_email'] ; ?></td>
                    <!--  <td><?php echo $partner['emirates_id'] ; ?></td>
                     <td><?php echo $partner['adhar_card'] ; ?></td> -->
                     <td><?php $a =  array_filter(json_decode($partner['telephone'],true)) ; 
                            echo str_replace(',', '<br />', implode(",", $a));
                      ?></td>
                     <td><?php echo $partner['uae_address'] ; ?></td>
                     <td><?php echo $partner['india_address'] ; ?></td>

                     <td>
                        <a href="<?php echo admin_url('corporate_recoveries/delete_partner/'.$client->userid.'/'.$partner['id']); ?>" class="btn btn-danger _delete btn-icon"><i class="fa fa-remove"></i></a>
                     </td>
                  </tr>
                  <?php } ?>
               </tbody>
            </table>
            </div>
         </div>


      </div>
   </div>
   <?php echo form_close(); ?>
</div>
<div id="contact_data"></div>
<?php if(isset($client)){ ?>
<?php if (has_permission('corporate_recovery', '', 'create') || has_permission('corporate_recovery', '', 'edit')) { ?>
<div class="modal fade" id="customer_admins_assign" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('corporate_recoveries/assign_admins/'.$client->userid)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('assign_user'); ?></h4>
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

<div class="modal fade" id="product_modal" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('corporate_recoveries/add_product/'.$client->userid)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('new_product'); ?></h4>
         </div>
         <div class="modal-body">
            <?php echo render_input('product_name','product_name'); ?>
            <?php echo render_input('nature','nature');?>
            <?php echo render_input('principal_amount','invoice_amount');?>
            <?php echo render_input('outstanding_amount','outstanding_amount');?>
            <?php echo render_date_input('due_date','due_date');?>
            <?php echo render_textarea( 'remarks', 'remarks'); ?>
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
<?php $this->load->view('admin/clients/client_group'); ?>


<div id="_partner"></div>
