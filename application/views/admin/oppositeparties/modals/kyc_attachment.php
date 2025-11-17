<div class="modal fade" id="model_partykycupload" tabindex="-1" role="dialog" style="z-index:99999999;">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('opposite_parties/add_partykyc'), array('id'=>'partykyc-upload-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                         <span class="add-title"> <?php echo _l('upload_kyc'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4">
  					 <?php echo render_input('subject', 'project_discussion_subject','' , 'text'); ?>
						</div>
                       <div class="col-md-4">
      			                         
       			  <?php
		    echo render_select('document_type',$document_types,array('id','name'),'document_type');?>
       		</div>
                <div class="col-md-4">
                      
                        <div class="form-group">
                                <label for="installment_receipt" class="profile-image"><?php echo _l('latest_partykyc'); ?></label>
                                <input type="file" name="file" class="form-control" id="file">
                             </div>
						</div>
                    <div class="col-md-12">
                           <div id="additional"></div>
                         <?php echo form_hidden('contract_id'); ?>
                          <?php echo form_hidden('rel_id'); ?>      
                                 
                     
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

<script>
appValidateForm($('#partykyc-upload-form'), {
       file:'required',
	document_type:'required' 
      }, manage_upload_types);
  function manage_upload_types(form) { 
	  	  var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
	
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
            alert_float('success', response.message);
        }
		 $('#model_partykycupload').modal('hide');
          $('#partykyc-upload-form').find('button[type="submit"]').button('reset');
             window.location.reload();// window.location.assign(response.url);
         
      });
      return false;
  }
function upload_partykycfile(id,contract_id=''){
	//alert(id);
	 $('#additional').append(hidden_input('rel_id',id));
	 $('#model_partykycupload input[name="rel_id"]').val(id);
	 $('#model_partykycupload input[name="contract_id"]').val(contract_id);
	 $('#model_partykycupload').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_court(invoker,id){
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id',id));
    $('#model_partykycupload input[name="name"]').val(name);
    $('#model_partykycupload').modal('show');
    $('.add-title').addClass('hide');
}
</script>
