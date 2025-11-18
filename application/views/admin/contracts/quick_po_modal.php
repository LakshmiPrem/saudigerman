<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="quick_po" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('contracts/add_quick_contract'), array('id'=>'quick_po-form')); ?>
        <div class="modal-content">
          <input type="hidden" name="type" value="po">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new').' '._l('po'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                
      <div class="col-md-12">
         
                     
		        <div class="form-group col-md-6">
               <label for="installment_receipt" class="profile-image"><?php echo _l('upload_po'); ?></label>
               <input type="file" name="agree_attachment" class="form-control" id="agree_attachment1">
            </div>
            <div class="col-md-6 form-group">
           <label for="subject" ><?php echo _l('subject'); ?></label>
               <input type="text" name="subject" class="form-control" id="subject1">
         </div>

              <div class="col-md-6">
               <div class="form-group select-placeholder">
                   

                    <?php if($type=='contracts'){
                        $this->load->model('clients_model');
                      $clients=$this->clients_model->get('',['tblclients.active'=>1]);

                    }else{
                        $this->load->model('clients_model');
                        $clients=$this->clients_model->get('', [
                            'tblclients.active' => 1,
                            'tblclients.ctype'  => $type
                        ]);

                    }
        
                    echo render_select('client',$clients,             
                            array('userid', 'company'), 'client');
                    ?>
                </div>

               </div>       
            
             

            <?php ########## Opposite Party ##############  ?>
         <div class="col-md-6">
           <?php 
            $oppositeparty_names=$this->db->get('tbloppositeparty')->result_array();
				    echo render_select('other_party',$oppositeparty_names,array('id','name'),'name_party');
           // echo render_input('other_party','other_party',$value);
           // }?>
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
</div>
<?php $this->load->view('admin/approval/approval_js'); ?>
<script>
	 window.addEventListener('load',function(){
      appValidateForm($('#quick_po-form'),{client:'required'},manage_opposepo_types);
      $('#quick_po').on('hidden.bs.modal', function(event) {
        $('#additional_qcontract_div').html('');
        $('#quick_po input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
		get_clients_of_case_ajax();
	     //get_templates_of_contract_ajax();
		 
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
	
    
  function manage_opposepo_types(form) {
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
        
        $('#quick_po').modal('hide');
        
        setTimeout(function() {
            var rel_name = 'po';
            var rel_id = response.id;
            load_approval_modal(admin_url + 'approval/approvals?rel_name=' + rel_name + '&rel_id=' + rel_id);
        }, 1000);
        
    });
    return false;
}

   
function new_quick_po(clientid='',company=''){
    $('#quick_po-form')[0].reset();

  // Also clear selectpicker values and refresh them
  $('#quick_po-form').find('select').each(function() {
    $(this).val('').selectpicker('refresh');
  });


	if(clientid!=''){
	 var ctype = $('select[name="client"]');
                ctype.find('option:first').after('<option value="'+clientid+'">'+company+'</option>');
                ctype.selectpicker('val',clientid);
                ctype.selectpicker('refresh');	
		$('#divclientcontract').addClass('hide');
		
	}
    $('#quick_po').modal('show');
    $('.edit-title').addClass('hide');
}


</script>


<script>
$(document).ready(function() {
    var $fileInput = $('#agree_attachment1');
    var $saveBtn = $('button[type="submit"]'); // adjust selector if different

    $fileInput.on('change', function() {
        if (this.files.length > 0) {


          var fileName = this.files[0].name;
          var cleanName = fileName.replace(/\.[^/.]+$/, "");

            // Put the file name in the subject field
            $('#subject1').val(cleanName);

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