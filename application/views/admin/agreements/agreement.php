<?php init_head(); ?>
<div id="wrapper">
   <div class="content accounting-template">
      <div class="row">
         <?php
            if(isset($proposal)){
             echo form_hidden('isedit',$proposal->id);
            }
            $rel_type = '';
            $rel_id = '';
            if(isset($proposal) || ($this->input->get('rel_id') && $this->input->get('rel_type'))){
             if($this->input->get('rel_id')){
               $rel_id = $this->input->get('rel_id');
               $rel_type = $this->input->get('rel_type');
             } else {
               $rel_id = $proposal->rel_id;
               $rel_type = $proposal->rel_type;
             }
            }
            ?>
         <?php echo form_open($this->uri->uri_string(),array('id'=>'agreement-form','class'=>'agreement-form')); ?>
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
                     <?php if(isset($proposal)){ ?>
                     <!-- <div class="col-md-12">
                        <?php echo format_proposal_status($proposal->status); ?>
                     </div> -->
                     <!-- <div class="clearfix"></div>
                     <hr /> -->
                     <?php } ?>
                     <div class="col-md-6 border-right">

                        <?php
                         //echo render_select('proposal_id',$proposals,array('id','subject',),'proposal',$selected);
                         ?>


                        <?php $value = (isset($proposal) ? $proposal->subject : ''); ?>
                        <?php $attrs = (isset($proposal) ? array() : array('autofocus'=>true)); ?>
                        <?php echo render_input('subject','proposal_subject',$value,'text',$attrs); ?>
                        <div class="form-group select-placeholder">
                           <label for="rel_type" class="control-label"><?php echo _l('proposal_related'); ?></label>
                           <select name="rel_type" id="rel_type" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <option value=""></option>
                              <option value="lead" <?php if((isset($proposal) && $proposal->rel_type == 'lead') || $this->input->get('rel_type')){if($rel_type == 'lead'){echo 'selected';}} ?>><?php echo _l('proposal_for_lead'); ?></option>
                              <option value="customer" <?php if((isset($proposal) &&  $proposal->rel_type == 'customer') || $this->input->get('rel_type')){if($rel_type == 'customer'){echo 'selected';}} ?>><?php echo _l('proposal_for_customer'); ?></option>
                           </select>
                        </div>
                        <div class="form-group select-placeholder<?php if($rel_id == ''){echo ' hide';} ?> " id="rel_id_wrapper">
                           <label for="rel_id"><span class="rel_id_label"></span></label>
                           <div id="rel_id_select">
                              <select name="rel_id" id="rel_id" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                              <?php if($rel_id != '' && $rel_type != ''){
                                 $rel_data = get_relation_data($rel_type,$rel_id);
                                 $rel_val = get_relation_values($rel_data,$rel_type);
                                    echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                 } ?>
                              </select>
                           </div>
                        </div>
                        <div class="row">
                          <div class="col-md-6">
                              <?php $value = (isset($proposal) ? _d($proposal->date) : _d(date('Y-m-d'))) ?>
                              <?php echo render_date_input('date','proposal_date',$value); ?>
                          </div>
                          <div class="col-md-6 hide">
                            <?php
                        $value = '';
                        if(isset($proposal)){
                          $value = _d($proposal->open_till);
                        } else {
                          if(get_option('proposal_due_after') != 0){
                              $value = _d(date('Y-m-d',strtotime('+'.get_option('proposal_due_after').' DAY',strtotime(date('Y-m-d')))));
                          }
                        }
                        echo render_date_input('open_till','proposal_open_till',$value); ?>
                          </div>
                        <!-- </div> -->
                        <?php
                           $selected = '';
                           $s_attrs = array('data-show-subtext'=>true);
                           foreach($currencies as $currency){
                            if($currency['isdefault'] == 1){
                              $s_attrs['data-base'] = $currency['id'];
                            }
                            if(isset($proposal)){
                              if($currency['id'] == $proposal->currency){
                                $selected = $currency['id'];
                              }
                              if($proposal->rel_type == 'customer'){
                                $s_attrs['disabled'] = true;
                              }
                            } else {
                              if($rel_type == 'customer'){
                                $customer_currency = $this->clients_model->get_customer_default_currency($rel_id);
                                if($customer_currency != 0){
                                  $selected = $customer_currency;
                                } else {
                                  if($currency['isdefault'] == 1){
                                    $selected = $currency['id'];
                                  }
                                }
                                $s_attrs['disabled'] = true;
                              } else {
                               if($currency['isdefault'] == 1){
                                $selected = $currency['id'];
                              }
                            }
                           }
                           }
                           ?>
                          
                             
                            
                          
                          <!-- <div class="row"> -->
                        <div class="col-md-6">
                                 <?php
                        echo render_select('currency',$currencies,array('id','name','symbol'),'proposal_currency',$selected, hooks()->apply_filters('proposal_currency_disabled',$s_attrs));
                           ?>
                              </div>
                             
<?php #################### client contact ########################################?>                      
                          <div class="col-md-6">
                              <?php
                                 $i = 0;
                                 $selected = '';
                                 foreach($staff as $member){
                                  if(isset($proposal)){
                                    if($proposal->assigned == $member['staffid']) {
                                      $selected = $member['staffid'];
                                    }
                                  }
                                  $i++;
                                 }
                                 echo render_select('assigned',$staff,array('staffid',array('firstname','lastname')),'staff_signed_by',$selected);
                                 ?>
                           </div>

                          <div class="col-md-6">
                            <?php 
                              $value = (isset($proposal) ? $proposal->valid_for : ''); ?>
                              <?php echo render_input('valid_for','valid_for',$value); ?>
                          </div>

                          <div class="col-md-6 hide">
                            <?php 
                              $value = (isset($proposal) ? $proposal->total : ''); ?>
                              <?php echo render_input('total','agreement_total',$value); ?>
                          </div>

                          <div class="col-md-6">
                            <?php 
                              $value = (isset($proposal) ? $proposal->registration_fee : ''); ?>
                              <?php echo render_input('registration_fee','registration_fee',$value); ?>
                          </div>

                        <div class="col-md-6">
                          <?php $value=( isset($proposal) ? $proposal->file_no_agreement : ''); ?>
                          <?php echo render_input('file_no_agreement','file_number',$value,'text');?>
                        </div>

                          <div class="col-md-12">
                          <?php
                          $professional_fee_amounts = (isset($proposal) ? json_decode($proposal->professional_fee_amounts) : '');  
                              $professional_fee = (isset($proposal) ? json_decode($proposal->professional_fee) : ''); ?>
                            <table class="table table-hover table-bordered">
                              <caption><strong>Professional Fee</strong></caption>
                              <thead>
                                <tr>
                                  <th width="60%">Debt Amount in AED </th>
                                  <th width="40%">Percentage (%)</th>
                                </tr>
                              </thead>
                              <tbody>
                                <tr>
                                  <td>
    <?php $prof_fee_amount0 = (isset($professional_fee_amounts[0]) ? $professional_fee_amounts[0] : '50000'); ?>

                                <div class=" form-inline">Below &nbsp;&nbsp; <input type="text" class="form-control " name="professional_fee_amounts[]" placeholder="" value="<?=$prof_fee_amount0?>"></div></td>
  <?php $prof_fee0 = (isset($professional_fee[0]) ? $professional_fee[0] : ''); ?>
                                  <td><input type="text" class="form-control" name="professional_fee[] " placeholder="%" value="<?=$prof_fee0?>"></td>
                                </tr>
                                <tr>
                                  <td>
   <?php $prof_fee_amount1 = (isset($professional_fee_amounts[1]) ? $professional_fee_amounts[1] : ''); ?>
                                    <div class="form-inline">Between &nbsp;&nbsp;<input type="text" class="form-control" name="professional_fee_amounts[] " placeholder="" value="<?=$prof_fee_amount1?>"></div></td>
<?php $prof_fee1 = (isset($professional_fee[1]) ? $professional_fee[1] : ''); ?>

                                  <td><input type="text" class="form-control" name="professional_fee[]" placeholder="%" value="<?=$prof_fee1?>"></td>
                                </tr>
                                <tr>
                                  <td>
 <?php $prof_fee_amount2 = (isset($professional_fee_amounts[2]) ? $professional_fee_amounts[2] : ''); ?>
                                <div class="form-inline">Between &nbsp;&nbsp;<input type="text" class="form-control" name="professional_fee_amounts[] " placeholder="" value="<?=$prof_fee_amount2?>"></div></td>
<?php $prof_fee2 = (isset($professional_fee[2]) ? $professional_fee[2] : ''); ?>

                                  <td><input type="text" class="form-control" name="professional_fee[]" placeholder="%" value="<?=$prof_fee0?>"></td>
                                </tr>
                                <tr>
                                  <td>
 <?php $prof_fee_amount3 = (isset($professional_fee_amounts[3]) ? $professional_fee_amounts[3] : ''); ?>
                                <div class="form-inline">Above &nbsp;&nbsp;<input type="text" class="form-control" name="professional_fee_amounts[] " placeholder="" value="<?=$prof_fee_amount3?>"> </div></td>
<?php $prof_fee3 = (isset($professional_fee[3]) ? $professional_fee[3] : ''); ?>
                                  <td><input type="text" class="form-control" name="professional_fee[]" placeholder="%" value="<?=$prof_fee3?>"></td>
                                </tr>
                              </tbody>
                            </table>
                          </div>
                         
<?php
#################################################################################################?>
                      </div>


                        <?php $fc_rel_id = (isset($proposal) ? $proposal->id : false); ?>
                         
                        
                     </div>
                     <div class="col-md-6">
                        <div class="row">
                           <div class="col-md-6 hide">
                              <div class="form-group select-placeholder">
                                 <label for="status" class="control-label"><?php echo _l('proposal_status'); ?></label>
                                 <?php
                                    $disabled = '';
                                    if(isset($proposal)){
                                     if($proposal->estimate_id != NULL || $proposal->invoice_id != NULL){
                                       $disabled = 'disabled';
                                     }
                                    }
                                    ?>
                                 <select name="status" class="selectpicker" data-width="100%" <?php echo $disabled; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach($statuses as $status){ ?>
                                    <option value="<?php echo $status; ?>" <?php if((isset($proposal) && $proposal->status == $status) || (!isset($proposal) && $status == 0)){echo 'selected';} ?>><?php echo format_proposal_status($status,'',false); ?></option>
                                    <?php } ?>
                                 </select>
                              </div>
                           </div>
                           <div class="col-md-12">
                            <?php $value = (isset($proposal->client_contact_name) ? $proposal->client_contact_name : '') ?>  
                          <?php echo render_input('client_contact_name','agreement_signed_by',$value); ?>

                              <?php /*
                                 $i = 0;
                                 $selected = '';
                                 foreach($staff as $member){
                                  if(isset($proposal)){
                                    if($proposal->assigned == $member['staffid']) {
                                      $selected = $member['staffid'];
                                    }
                                  }
                                  $i++;
                                 }
                                 echo render_select('assigned',$staff,array('staffid',array('firstname','lastname')),'proposal_assigned',$selected);*/
                                 ?>
                           </div>
                        </div>
                        <?php $value = (isset($proposal) ? $proposal->proposal_to : ''); ?>
                        <?php echo render_input('proposal_to','proposal_to',$value); ?>
                        <?php $value = (isset($proposal) ? $proposal->address : ''); ?>
                        <?php echo render_textarea('address','proposal_address',$value); ?>
                        <div class="row">
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->city : ''); ?>
                              <?php echo render_input('city','billing_city',$value); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->state : ''); ?>
                              <?php echo render_input('state','billing_state',$value); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $countries = get_all_countries(); ?>
                              <?php $selected = (isset($proposal) ? $proposal->country : ''); ?>
                              <?php echo render_select('country',$countries,array('country_id',array('short_name'),'iso2'),'billing_country',$selected); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->zip : ''); ?>
                              <?php echo render_input('zip','billing_zip',$value); ?>
                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->client_contact_mail : ''); ?>
                              <?php echo render_input('client_contact_mail','proposal_client_contact_mail',$value); ?>
                              <input type="hidden" name="email" id="email" value="<?=$value?>">

                           </div>
                           <div class="col-md-6">
                              <?php $value = (isset($proposal) ? $proposal->client_contact_phone : ''); ?>
                              <?php echo render_input('client_contact_phone','proposal_client_contact_phone',$value); ?>
                              <input type="hidden" name="phone" id="phone" value="<?=$value?>">
                           </div>

                           <div class="form-group no-mbot">
                           <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                           <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($proposal) ? prep_tags_input(get_tags_in($proposal->id,'agreement')) : ''); ?>" data-role="tagsinput">
                        </div>
                          
                        </div>

                           
                     </div>


<div class="col-md-12">
  <div class="container-lg">
    <div class="table-responsive">
        <div class="table-wrapper">
            <div class="table-title">
                <div class="row">
                    <div class="col-md-8"><h3><b>Debtors Details</b></h3></div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-info add-new mtop15"><i class="fa fa-plus"></i> Add New</button>
                    </div>
                </div>
            </div>
            <table class="table table-bordered debtor " width="100%">
                <thead>
                    <tr>
                        <th><?php  echo _l('debtor_name');?></th>
                        <th><?php  echo _l('outstanding_amount');?></th>
                        <th><?php  echo _l('age_of_the_debt');?></th>
                        <th><?php  echo _l('debtor_address');?></th>
                        <th><?php  echo _l('debtor_contact_details');?></th>
                        <th><?php  echo _l('email');?></th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
        <?php $debtor_name = (isset($proposal->debtor_name) ? json_decode($proposal->debtor_name) : '');
        $outstanding_amount = (isset($proposal->outstanding_amount) ? json_decode($proposal->outstanding_amount) : '');
        $age_of_debt = (isset($proposal->age_of_debt) ? json_decode($proposal->age_of_debt) : '');
        $debtor_address = (isset($proposal->debtor_address) ? json_decode($proposal->debtor_address) : '');
        $debtor_contact_details = (isset($proposal->debtor_contact_details) ? json_decode($proposal->debtor_contact_details) : '');
        $email_id = (isset($proposal->email_id) ? json_decode($proposal->email_id) : '');
        if(isset($proposal)){
        $j=0;

        foreach ($debtor_name as $value) {
          ?>   
                   <tr>
                     <td>
                        <?php echo render_input('debtor_name[]','',$debtor_name[$j]); ?>
                      </td>
                      <td>
                        <?php echo render_input('outstanding_amount[]','',$outstanding_amount[$j]); ?>
                      </td>
                      <td>
                        <?php echo render_input('age_of_debt[]','',$age_of_debt[$j]); ?>
                      </td>
                      <td>
                        <?php echo render_input('debtor_address[]','',$debtor_address[$j]); ?>
                      </td>
                      <td>
                        <?php echo render_input('debtor_contact_details[]','',$debtor_contact_details[$j]); ?>
                      </td>
                      <td>
              
                        <?php echo render_input('email_id[]','',$email_id[$j]); ?>
                      </td>
                      <td>
                        <!-- <a class="btn add" title="Add" data-toggle="tooltip"><i class="fa fa-plus"></i></a> -->
                            <!-- <a class="btn" title="Edit" data-toggle="tooltip"><i class="fa fa-pencil"></i></a> -->
                            <a class="btn btn-danger delete" title="Delete" data-toggle="tooltip"><i class="fa fa-trash-o"></i></a>
                      </td>
                   </tr>
            <?php $j++; } }else{?>
                    <tr>
                      <td>
                        <?php echo render_input('debtor_name[]',''); ?>
                      </td>
                      <td>
                        <?php echo render_input('outstanding_amount[]',''); ?>
                      </td>
                      <td>
                        <?php echo render_input('age_of_debt[]',''); ?>
                      </td>
                      <td>
                        <?php echo render_input('debtor_address[]',''); ?>
                      </td>
                      <td>
                        <?php echo render_input('debtor_contact_details[]'); ?>
                      </td>
                      <td>
                        <?php echo render_input('email_id[]',''); ?>
                      </td>
                      <td>
                        <!-- <a class="btn add" title="Add" data-toggle="tooltip"><i class="fa fa-plus"></i></a> -->
                            <!-- <a class="btn" title="Edit" data-toggle="tooltip"><i class="fa fa-pencil"></i></a> -->
                            <a class="btn btn-danger delete" title="Delete" data-toggle="tooltip"><i class="fa fa-trash-o"></i></a>
                      </td>
                   </tr>
                  <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
</div>     
</div>


                  </div>
                  <div class="btn-bottom-toolbar bottom-transaction text-right">
                  <p class="no-mbot pull-left mtop5 btn-toolbar-notice"><?php //echo _l('include_proposal_items_merge_field_help','<b>{proposal_items}</b>'); ?></p>
                    <button type="button" class="btn btn-info mleft10 proposal-form-submit save-and-send transaction-submit hide">
                        <?php echo _l('save_and_send'); ?>
                    </button>
                    <button class="btn btn-info mleft5 proposal-form-submit transaction-submit" type="submit">
                      <?php echo _l('submit'); ?>
                    </button>
               </div>
               </div>
            </div>
         </div>
         <div class="col-md-12">
            <div class="panel_s">
               <?php //$this->load->view('admin/estimates/_add_edit_items'); ?>
            </div>
         </div>
         <?php echo form_close(); ?>
         <?php //$this->load->view('admin/invoice_items/item'); ?>
      </div>

      <div class="btn-bottom-pusher"></div>
   </div>
</div>
<?php init_tail();?>
<script>
   var _rel_id = $('#rel_id'),
   _rel_type = $('#rel_type'),
   _rel_id_wrapper = $('#rel_id_wrapper'),
   data = {};

   $(function(){
    init_currency_symbol();
    // Maybe items ajax search
    //init_ajax_search('items','#item_select.ajax-search',undefined,admin_url+'items/search');
    validate_proposal_form();
    $('body').on('change','#rel_id', function() {
     if($(this).val() != ''){
      $.get(admin_url + 'proposals/get_relation_data_values/' + $(this).val() + '/' + _rel_type.val(), function(response) {
        
        $('input[name="client_contact_name"]').val(response.signed_by);
        $('input[name="proposal_to"]').val(response.to);
        $('textarea[name="address"]').val(response.address);
        $('input[name="email"]').val(response.email);
        $('input[name="phone"]').val(response.phone);
        $('input[name="city"]').val(response.city);
        $('input[name="state"]').val(response.state);
        $('input[name="zip"]').val(response.zip);
        $('input[name="client_contact_mail"]').val(response.email);
        $('input[name="client_contact_phone"]').val(response.phone);
        $('select[name="country"]').selectpicker('val',response.country);
        var currency_selector = $('#currency');
        if(_rel_type.val() == 'customer'){
          if(typeof(currency_selector.attr('multi-currency')) == 'undefined'){
            currency_selector.attr('disabled',true);
          }

         } else {
           currency_selector.attr('disabled',false);
        }
        var proposal_to_wrapper = $('[app-field-wrapper="proposal_to"]');
        if(response.is_using_company == false && !empty(response.company)) {
          proposal_to_wrapper.find('#use_company_name').remove();
          proposal_to_wrapper.find('#use_company_help').remove();
          proposal_to_wrapper.append('<div id="use_company_help" class="hide">'+response.company+'</div>');
          proposal_to_wrapper.find('label')
          .prepend("<a href=\"#\" id=\"use_company_name\" data-toggle=\"tooltip\" data-title=\"<?php echo _l('use_company_name_instead'); ?>\" onclick='document.getElementById(\"proposal_to\").value = document.getElementById(\"use_company_help\").innerHTML.trim(); this.remove();'><i class=\"fa fa-building-o\"></i></a> ");
        } else {
          proposal_to_wrapper.find('label #use_company_name').remove();
          proposal_to_wrapper.find('label #use_company_help').remove();
        }
       /* Check if customer default currency is passed */
       if(response.currency){
         currency_selector.selectpicker('val',response.currency);
       } else {
        /* Revert back to base currency */
        currency_selector.selectpicker('val',currency_selector.data('base'));
      }
      currency_selector.selectpicker('refresh');
      currency_selector.change();
    }, 'json');
    }
   });
    $('.rel_id_label').html(_rel_type.find('option:selected').text());
    _rel_type.on('change', function() {
      var clonedSelect = _rel_id.html('').clone();
      _rel_id.selectpicker('destroy').remove();
      _rel_id = clonedSelect;
      $('#rel_id_select').append(clonedSelect);
      proposal_rel_id_select();
      if($(this).val() != ''){
        _rel_id_wrapper.removeClass('hide');
      } else {
        _rel_id_wrapper.addClass('hide');
      }
      $('.rel_id_label').html(_rel_type.find('option:selected').text());
    });
    proposal_rel_id_select();
    <?php if(!isset($proposal) && $rel_id != ''){ ?>
      _rel_id.change();
      <?php } ?>
    });
   function proposal_rel_id_select(){
      var serverData = {};
      serverData.rel_id = _rel_id.val();
      data.type = _rel_type.val();
      <?php if(isset($proposal)){ ?>
        serverData.connection_type = 'proposal';
        serverData.connection_id = '<?php echo $proposal->id; ?>';
      <?php } ?>
      init_ajax_search(_rel_type.val(),_rel_id,serverData);
   }
   function validate_proposal_form(){
      _validate_form($('#agreement-form'), {
        subject : 'required',
        proposal_to : 'required',
        rel_type: 'required',
        rel_id : 'required',
        date : 'required',
        file_no_agreement:'required',
        /*email: {
         email:true,
         required:true
       },*/
       currency : 'required',
     });
   }

    $("#assigned").on("change",  function (event){
      var id = $(this).val();
      $.ajax({
          url: admin_url + 'proposals/get_sales_managr_ph_email/' + id,
          type: "GET",
          dataType: "JSON",
          success: function(response){ 
            $('input[name="sales_manager_email"]').val(response.email);
            $('input[name="sales_manager_phone"]').val(response.phone);
          }
        });
    });
</script>
</body>
</html>

<script>
$(document).ready(function(){
  $('[data-toggle="tooltip"]').tooltip();
  var actions = $(".debtor td:last-child").html();
  // Append table with add row form on add new button click
    $(".add-new").click(function(){
    //$(this).attr("disabled", "disabled");
    var index = $(".debtor tbody tr:last-child").index();
        var row = '<tr>' +
            '<td><input type="text" class="form-control" name="debtor_name[]" id="debtor_name"></td>' +
            '<td><input type="text" class="form-control" name="outstanding_amount[]" id="outstanding_amount"></td>' +
            '<td><input type="text" class="form-control" name="age_of_debt" id="age_of_debt"></td>' +
            '<td><input type="text" class="form-control" name="debtor_address[]" id="debtor_address"></td>' +
            '<td><input type="text" class="form-control" name="debtor_contact_details[]" id="debtor_contact_details"></td>' +
            '<td><input type="text" class="form-control" name="email_id[]" id="email_id"></td>' +
      '<td>' + actions + '</td>' +
        '</tr>';
      $(".debtor").append(row);   
    $(".debtor tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();
    });
  // Add row on add button click
  $(document).on("click", ".add", function(){
    var empty = false;
    var input = $(this).parents("tr").find('input[type="text"]');
        input.each(function(){
      if(!$(this).val()){
        $(this).addClass("error");
        empty = true;
      } else{
                $(this).removeClass("error");
            }
    });
    $(this).parents("tr").find(".error").first().focus();
    if(!empty){
      input.each(function(){
        $(this).parent("td").html($(this).val());
      });     
      $(this).parents("tr").find(".add, .edit").toggle();
      $(".add-new").removeAttr("disabled");
    }   
    });
  // Edit row on edit button click
  $(document).on("click", ".edit", function(){    
        $(this).parents("tr").find("td:not(:last-child)").each(function(){
      $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '">');
    });   
    $(this).parents("tr").find(".add, .edit").toggle();
    $(".add-new").attr("disabled", "disabled");
    });
  // Delete row on delete button click
  $(document).on("click", ".delete", function(){
        $(this).parents("tr").remove();
    $(".add-new").removeAttr("disabled");
    });
});
</script>