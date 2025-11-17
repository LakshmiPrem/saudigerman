<div class="modal fade" id="model_contract_amendment" tabindex="-1" role="dialog" style="z-index:99999999;">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('contracts/add_contractamendpdf'), array('id'=>'contract-_amendmentform')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                         <span class="add-title"> <?php echo _l('upload_contract'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_amend"></div>
                         <?php echo form_hidden('contract_id',$contract->id); ?>
						  <?php echo render_textarea('amendment_text','amendment_text','',array('rows'=>5)); ?>
              
						 <?php echo render_date_input('effective_date','contract_effectivedate'); ?>
						<?php 
                     $amend_status=get_amend_statuses();  
                        echo render_select('amend_status',$amend_status,array('id','name'),'status','Draft',array());?>
                        <div class="form-group">
                                <label for="installment_receipt" class="profile-image"><?php echo _l('latest_amendment'); ?></label>
                                <input type="file" name="amend_attachment" class="form-control" id="amend_attachment">
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

<script>
appValidateForm($('#contract-_amendmentform'), {
        amend_attachment:'required',
        effective_date:'required',
	amendment_text:'required'
		 
      }, manage__amendment_types);
  function manage__amendment_types(form) { 
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
		 $('#model_contract_amendment').modal('hide');
          $('#contract-_amendmentform').find('button[type="submit"]').button('reset');
             window.location.reload();// window.location.assign(response.url);
         
      });
      return false;
  }
function upload_contractamendment(id){
	
	 $('#additional').append(hidden_input('id',id));
	 $('#model_contract_amendment').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_court(invoker,id){
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id',id));
    $('#model_contract_amendment input[name="name"]').val(name);
    $('#model_contract_amendment').modal('show');
    $('.add-title').addClass('hide');
}
</script>
