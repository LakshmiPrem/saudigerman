<div class="modal fade" id="model_contract_review" tabindex="-1" role="dialog" style="z-index:99999999;">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('contracts/add_contractpostaction'), array('id'=>'contract-actionreviewform')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                         <span class="add-title"> <?php echo _l('contract_postaction'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_amend"></div>
                         <?php echo form_hidden('contract_id'); ?>
						<?php 
                     $categories=get_contract_actions();  
                        echo render_select('category_id',$categories,array('id','category_name'),'action_category','',array());?>
						  <?php echo render_textarea('description','project_description','',array('rows'=>5)); ?>
              
						 <?php echo render_date_input('due_date','Date'); ?>
						<?php 
                     $action_status=get_postaction_statuses();  
                        echo render_select('status',$action_status,array('id','name'),'status','Pending',array());?>
                        
                    </div>
                    <div class="form-group">
                                <label for="installment_receipt" class="profile-image"><?php echo _l('latest_postaction'); ?></label>
                                <input type="file" name="post_attachment" class="form-control" id="post_attachment">
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
appValidateForm($('#contract-actionreviewform'), {
        due_date:'required',
        category_id:'required',
	description:'required'
		 
      }, manage_actionreview_types);
  function manage_actionreview_types(form) { 
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
		 $('#model_contract_review').modal('hide');
          $('#contract-actionreviewform').find('button[type="submit"]').button('reset');
             window.location.reload();// window.location.assign(response.url);
         
      });
      return false;
  }
function upload_contractreview(id){
	
	 $('#additional').append(hidden_input('id',id));
	 $('#model_contract_review input[name="contract_id"]').val(id);
	 $('#model_contract_review').modal('show');
    $('.edit-title').addClass('hide');
}

</script>
