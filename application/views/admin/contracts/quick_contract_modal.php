<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="quick_contract" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('contracts/add_quick_contract'), array('id'=>'quick_contract-form')); ?>
        <div class="modal-content">
          <input type="hidden" name="type" value="contracts">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new').' '._l('contract'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                
      <div class="col-md-12">
                  <div class="form-group">
                     <!--<div class="checkbox checkbox-primary no-mtop checkbox-inline">
                        <input type="checkbox" id="trash" name="trash">
                        <label for="trash"><i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="<?php echo _l('contract_trash_tooltip'); ?>" ></i> <?php echo _l('contract_trash'); ?></label>
                     </div>-->
                      <div class="checkbox checkbox-primary no-mtop checkbox-inline" 
     style="display:flex; align-items:center; gap:40px; flex-wrap:wrap; margin-top:10px;">

 

  <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="is_payable" name="is_payable"
      <?php echo ' checked'; ?>
      onclick="handleSingleSelect(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="is_payable" style=" color:#333; cursor:pointer;">
      <?php echo _l('is_payable'); ?>
    </label>
  </div>

  <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="is_receivable" name="is_receivable"
     
      onclick="handleSingleSelect(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="is_receivable" style=" color:#333; cursor:pointer;">
      <?php echo _l('is_receivable'); ?>
    </label>
  </div>
 <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="trash" name="trash"
     
      onclick="handleSingleSelect(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="trash" style=" color:#333; cursor:pointer;">
      <?php echo _l('contract_trash'); ?>
    </label>
  </div>
  <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="is_non_std" name="is_non_std" 
     
      onclick="toggleUploadAndTemplate(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="is_non_std" style=" color:#333; cursor:pointer;">
      <?php echo _l('is_non_std'); ?>
    </label>
  </div>
</div>
                     <div class="checkbox checkbox-primary checkbox-inline hide">
                        <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client" >
                        <label for="not_visible_to_client"><?php echo _l('contract_not_visible_to_client'); ?></label>
                     </div>
                  </div>
		  
                       
                 <div class="form-group select-placeholder f_client_id">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('project_customer'); ?></label>
                     <select id="clientid" name="client" data-live-search="true" data-width="100%" class="ajax-search select" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        
                        
                  </select>
               </div>
             <?php //$value = (isset($contract) ? $contract->subject : ''); ?>
           
            <?php echo render_input('subject','contract_subject',''); ?>

            <div class="form-group hide" id="divproject">
				<?php
				$projects=$this->projects_model->get_clients_of_case();
                  echo render_select('projectid',$projects,array('id','name'),'project');
				?>
            </div>

            <?php ########## Opposite Party ##############  ?>
         <!-- <div class="col-md-6"> -->
           <?php 
            $oppositeparty_names=$this->db->get('tbloppositeparty')->result_array();
				    echo render_select('other_party',$oppositeparty_names,array('id','name'),'name_party');
           // echo render_input('other_party','other_party',$value);
           // }?>
         <!-- </div> -->
		
         
            <div class="form-group">
               <label for="contract_value"><?php echo _l('contract_value'); ?></label>
               <div class="input-group" data-toggle="tooltip" title="<?php echo _l('contract_value_tooltip'); ?>">
                  <input type="number" class="form-control" id= "contract_value" name="contract_value" value="">
                  <div class="input-group-addon">
                     <?php $base_currency=$this->db->get_where('tblcurrencies',array('isdefault'=>1))->row();
                     echo $base_currency->symbol; ?>
                  </div>
               </div>
            </div>
            <?php
           $types=$this->db->get('tblcontracts_types')->result_array();
            if(is_admin() || get_option('staff_members_create_inline_contract_types') == '1'){
              echo render_select_with_input_group('contract_type',$types,array('id','name'),'contract_type','','<a href="#" onclick="new_type();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('contract_type',$types,array('id','name'),'contract_type');
         }
         ?>
         
            <div class="hide" id="div_template1">
             <?php 
                  $this->load->model('contracts_model');
                 $templates=$this->contracts_model->get_templates_of_contract(); ?>
                  <?php  echo render_select('contract_template_id',$templates,array('id','name'),'contract_template'); ?>
          </div> 
    
        
         <div class="row">
         
		   <div class="col-md-6">
               <?php $value =  _d(date('Y-m-d')); ?> 
               <?php echo render_date_input('datestart','contract_start_date',$value); ?>
            </div>
            <div class="col-md-6">
               
               <?php echo render_date_input('dateend','contract_end_date'); ?>
            </div>
            
            
            <!--------------additional--------------------------------->    
			 <div class="col-md-6">
			 <?php 
                     $payment_terms=get_payment_terms();  
                        echo render_select('payment_terms',$payment_terms,array('id','name'),'payment_terms','',array());?>
			 </div>

       <div class="col-md-6">
			 <?php 
                     $category=get_contract_category();  
                        echo render_select('contract_category',$category,array('id','name'),'contract_category','',array());?>
			 </div>

       <div class="col-md-6">
			 <?php 
                     $sub_category=get_contract_subcategory();  
                        echo render_select('contract_subcategory',$sub_category,array('id','name'),'contract_subcategory','',array());?>
			 </div>

       <div class="col-md-6">
			 <?php 
                     $staffs=$this->db->get_where('tblstaff',array('active'=>1))->result_array(); 
                        echo render_select('purchaser',$staffs,array('staffid',array('firstname','lastname')),'purchaser','',array());?>
			 </div>

       <div class="col-md-6">
			 <?php 
                     $this->load->model('departments_model');
                      $departments=$this->departments_model->get();
                        echo render_select('contract_department',$departments,array('departmentid','name'),'contract_department','',array());?>
			 </div>



        
            <div class="form-group col-md-6">
               <label for="installment_receipt" class="profile-image"><?php echo _l('upload_contract'); ?></label>
               <input type="file" name="agree_attachment" class="form-control" id="agree_attachment">
            </div>
         

   <!--------------additional---------------------------------> 
			
			 <div id="contract_install" class="hide">
            <div class="col-md-6">
               
               <?php echo render_input('no_of_installment','no_of_installment'); ?>
            </div>
            <div class="col-md-6">
               
               <?php echo render_date_input('default_effective_date','default_effective_date'); ?>
            </div>
           <div class="col-md-12">
               
               <?php echo render_input('installment_amount','installment_amount','','number'); ?>
            </div>
			</div>
			 <div class="col-md-6 hide">
			   
                      <?php $statuses=$this->db->get(db_prefix() . 'contracts_status')->result_array();
                      echo render_select('status',$statuses,array('id','name'),'status',1,array(),array(),'','',false); ?>
			 </div>
			  <div class="col-md-6 hide">
               
               <?php echo render_date_input('final_expiry_date','final_expiry_date'); ?>
            </div>
              <div class="col-md-12 hide">
                       
                     <?php
                        $staff=$this->db->get_where('tblstaff',array('active'=>1))->result_array();
                        $selected=get_staff_user_id();
                        echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'contract_assignees',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                        ?>
                    
                     </div>
                           
                             <div class="col-md-12 hide">
                  <div class="checkbox checkbox-primary billable">
               <input type="checkbox" id="is_autorenewal" name="is_autorenewal">
               <label for="is_autorenewal"><?php echo _l('is_autorenewal'); ?></label>
            </div>
				   </div>
         </div>
         
         <?php echo render_textarea('description','contract_description','',array('rows'=>10)); ?>
         
         <?php echo render_custom_fields('contracts',false); ?>
         <div class="btn-bottom-toolbar text-right">
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      
</div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php $this->load->view('admin/approval/approval_js'); ?>
<script>
	 window.addEventListener('load',function(){
      appValidateForm($('#quick_contract-form'),{client:'required',subject:'required',datestart:'required',dateend:'required',contract_type:'required'},manage_opposecontract_types);
      $('#quick_contract').on('hidden.bs.modal', function(event) {
        $('#additional_qcontract_div').html('');
        $('#quick_contract input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
		get_clients_of_case_ajax();
	     get_templates_of_contract_ajax();
		 
    });
	
  });
    function get_clients_of_case_ajax() { 
        var clientSelected = $('select[name="client"]').val();
        if(clientSelected > 0){
            $.get(admin_url + 'projects/get_clients_of_case/'+clientSelected,function(response){
				
                var ctype = $('select[name="projectid"]');
                $('select[name="projectid"] option').remove();
                if(response ){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
                   
                    ctype.selectpicker('refresh'); 
					if(response[0].id!='')
						$('#divproject').removeClass('hide');
					else
						$('#divproject').addClass('hide');
                } else {
                    alert_float('danger','Error');
					
                }
            },'json');
        }
    }
	 $('select[name="client"]').change(function(){
      get_clients_of_case_ajax();
    
    });
     $('#ticketid').on('change', function() {
							
				var department = $(this).val();
				var url=admin_url+'tickets/getTicketInfo';
				// AJAX request
			$.ajax({
				url:url,
				method: 'post',
				data: {ticketid: department},
				dataType: 'json',
				success: function(response){
					// $('#other_party').val(response.opposteparty);
					 $('#type_stamp').val(response.stamp_type);
					 $('#subject').val(response.subject);
					 $('#contract_value').val(response.file_amount);
					$('#other_party').selectpicker('val',response.opposteparty);
					
					// var ctype = $('#quick_contract-form select[name="client"]');
			//	ctype.find('option:first').after('<option value="'+response.userid+'">'+response.company+'</option>');
					// Add the new option
var ctype = $('#quick_contract-form select[name="client"]');

// Clear old selection if needed
ctype.find('option[value="' + response.userid + '"]').remove();

// Add option and mark it selected
ctype.append('<option value="' + response.userid + '" selected>' + response.company + '</option>');

// Tell selectpicker
ctype.selectpicker('refresh');
ctype.selectpicker('val', response.userid);

// Update the visible label (Perfex sometimes needs double refresh)
ctype.trigger('change');
				// Force label update if still blank
/*$('.bootstrap-select[data-id="clientid"] .filter-option-inner-inner')
    .text(response.company);*/								
				}
			});
		});
  function manage_opposecontract_types(form) {
    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
     // Find the submit/save button
    var $submitBtn = $(form).find('button[type="submit"], .btn-info, .btn-primary');

    // Disable button and show spinner
    $submitBtn.prop('disabled', true);
    var originalBtnText = $submitBtn.html();
    $submitBtn.html('<i class="fa fa-spinner fa-spin"></i> Saving...');
	
    $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response){
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('select[name="opposite_party[]"]');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
			//new_quick_person_casebill(response.id,response.clientid);
			// window.location.href = admin_url + response.link;
        }
        
        $('#quick_contract').modal('hide');
        
        setTimeout(function() {
            var rel_name = 'contract';
            var rel_id = response.id;
            load_approval_modal(admin_url + 'approval/approvals?rel_name=' + rel_name + '&rel_id=' + rel_id);
        }, 1000);
        
    });
    return false;
}

	$('#quick_contract-form select[name="contract_type"]').change(function(){
        
     const isNonStdChecked = document.querySelector('#is_non_std').checked;

     if (isNonStdChecked) {
      get_templates_of_contract_ajax();
     }
    
    });
    function get_templates_of_contract_ajax() { 
        var clientSelected = $('#quick_contract-form select[name="contract_type"]').val();
        if(clientSelected !=''){
			
            $.get(admin_url + 'contracts/get_templates_of_contract/'+clientSelected,function(response){
				if(response.length>0)
				$('#div_template1').removeClass('hide');
				else 
				$('#div_template1').addClass('hide');	
                var ctype = $('#quick_contract-form select[name="contract_template_id"]');
				
                  $('#quick_contract-form select[name="contract_template_id"] option').remove();
                if(response){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
                    <?php if(isset($contract)){ 
                            //$opp_ids = array_column($project->assigned_opposite_parties,'opposite_party_id');
                            $toe_id = $contract->contract_template_id;
                            //foreach ($opp_ids as $value) { ?>
                              ctype.selectpicker('val',<?php echo $toe_id ?>);
                            <?php //}
                    } ?>    
                    ctype.selectpicker('refresh'); 
					
                } else {
                    alert_float('danger','Error');
					
                }
            },'json');
        }else{
			$('#div_template1').addClass('hide');
		}
    }
function new_quick_contract(clientid='',company=''){
    $('#quick_contract-form')[0].reset();

  // Also clear selectpicker values and refresh them
  $('#quick_contract-form').find('select').each(function() {
    $(this).val('').selectpicker('refresh');
  });


	if(clientid!=''){
	 var ctype = $('#quick_contract-form select[name="client"]');
   // Clear old selection if needed
ctype.find('option[value="' + clientid + '"]').remove();

// Add option and mark it selected
ctype.append('<option value="' + clientid + '" selected>' + company + '</option>');

// Tell selectpicker
ctype.selectpicker('refresh');
 ctype.selectpicker('val',clientid);

// Update the visible label (Perfex sometimes needs double refresh)
ctype.trigger('change');
               	
		$('#divclientcontract').addClass('hide');
		
	}
    $('#quick_contract').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_quick_contract(invoker,id){
    var name = $(invoker).data('name');
  //  $('#additional').append(hidden_input('id',id));
    $('#quick_contract input[name="name"]').val(name);
    $('#quick_contract').modal('show');
    $('.add-title').addClass('hide');
}

</script>
<script>
function handleSingleSelect(checkbox) {
  const checkboxes = document.querySelectorAll('#trash, #is_payable, #is_receivable');
  checkboxes.forEach(cb => {
    if (cb !== checkbox) cb.checked = false;
  });
  
  toggleCategoryFields();
}

function toggleCategoryFields() {
  var isPayableChecked = $('#is_payable').is(':checked');
  var isReceivableChecked = $('#is_receivable').is(':checked');

  var contractCategory = $('#contract_category').closest('.form-group');
  var contractSubcategory = $('#contract_subcategory').closest('.form-group');

  if (isPayableChecked) {
    contractCategory.removeClass('hide');   // Show category
    contractSubcategory.removeClass('hide'); // Show subcategory
  } else {
    contractCategory.addClass('hide');      // Hide category
    contractSubcategory.addClass('hide');   // Hide subcategory
  }
}

// Function to toggle Upload and Template visibility
function toggleUploadAndTemplate() {
  const isNonStdChecked = document.querySelector('#is_non_std').checked;
  const ispayableChecked = document.querySelector('#is_payable').checked;
  const isreceivableChecked = document.querySelector('#is_receivable').checked;

  const uploadDiv = document.querySelector('#agree_attachment').closest('.form-group');
  const templateDiv = document.querySelector('#div_template1');

   

  if (isNonStdChecked) {
    uploadDiv.classList.add('hide');      // Hide upload
    templateDiv.classList.remove('hide'); // Show template
    
  } else {
    uploadDiv.classList.remove('hide');   // Show upload
    templateDiv.classList.add('hide');    // Hide template
    
  }

}

// Run on page load
window.addEventListener('load', function() {
  toggleUploadAndTemplate();
  toggleCategoryFields();
});



$('select[name="contract_category"]').change(function(){ 
	 load_contract_subcategory_ajax($(this).val());
   
});


function load_contract_subcategory_ajax(categoryid='',subid='') {
        var cateid = typeof (categoryid) != 'undefined' ? categoryid : $('#contract_category').val() ;
		 $('select[name="contract_subcategory"]').selectpicker('val','');
      $('select[name="contract_subcategory"]').selectpicker('refresh');
        var subid = typeof (subid) != 'undefined' ? subid : $('#contract_subcategory').val() ;
        requestGetJSON('contracts/get_contractsub_by_category_id_ajax/' + cateid ).done(function(response) {
				
            var dtype = $('#contract_subcategory');
            $("#contract_subcategory option").remove();
            dtype.append('<option value=""></option>').selectpicker();
            if(response){ 
                //var obj = jQuery.parseJSON(response);
                $.each(response, function(key,value) { 
                  console.log();
                  dtype.append('<option value="'+value.id+'">'+value.name+'</option>').selectpicker();
                });
                dtype.selectpicker('val',subid);   
                dtype.selectpicker('refresh');
            }
           
        });
    }

</script>

<script>
$(document).ready(function() {
    var $fileInput = $('#agree_attachment');
    var $saveBtn = $('button[type="submit"]'); // adjust selector if different

    $fileInput.on('change', function() {
        if (this.files.length > 0) {
            // Simulate file being processed or uploaded
            $saveBtn.prop('disabled', true);
            $saveBtn.html('<i class="fa fa-spinner fa-spin"></i> Uploading...');

            // Create a dummy delay to mimic file processing
            // (browser doesn't upload until form submit)
            setTimeout(function() {
                $saveBtn.prop('disabled', false);
                $saveBtn.html('Save');
            }, 2000); // show progress for 2 seconds
        } else {
            $saveBtn.prop('disabled', false);
            $saveBtn.html('Save');
        }
    });
});
</script>
