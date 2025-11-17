<div class="modal fade" id="model_expensepayment" tabindex="-1" role="dialog" style="z-index:99999999;">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('expenses/add_expensepayment'), array('id'=>'expense-payment-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                         <span class="add-title"> <?php echo _l('new_expense_payment'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                   
                    <div class="col-md-12">
                                           
                        <?php echo render_input('exp_paid_amount','installment_amount'); ?>
                       <?php echo render_date_input('exp_paid_date','exp_paid_date',_d(date('Y-m-d'))); ?>
                        <?php echo render_textarea('exp_description','remarks'); ?>
                      
                        <div id="additional"></div>
                      
                         <?php echo form_hidden('expense_id'); ?>
                         <?php echo form_hidden('project_id'); ?>
                        
                 
                    </div>
					 <div class="col-md-6">
                                <label for="installment_receipt" class="profile-image"><small class="req text-danger">* </small><?php echo _l('upload_document'); ?></label>
                                <input type="file" name="pop_attachment" class="form-control" id="pop_attachment" required>
                                
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
appValidateForm($('#expense-payment-form'), {
        content:'required'
		 
      }, manage_expense_payments);
  function manage_expense_payments(form) { 
    var formURL = $(form).attr("action");//alert(formURL);
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
            alert_float('success',response.message);
    	 
        }
       window.location.reload();
        $('#model_expensepayment').modal('hide');
    });
    return false;
}
function new_expense_payment(expenseid,projectid){
	// $('#additional').append(hidden_input('id',executeid));
	$('#model_expensepayment').find('input[name="expense_id"]').val(expenseid);
	$('#model_expensepayment').find('input[name="project_id"]').val(projectid);
	// $('#additional').append(hidden_input('up_hearing_id',hearingid));
	 $('#model_expensepayment').modal('show');
    $('.edit-title').addClass('hide');
}

</script>
