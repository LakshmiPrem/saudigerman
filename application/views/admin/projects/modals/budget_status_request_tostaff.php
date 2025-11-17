<div class="modal fade" id="model_budgetstatus" tabindex="-1" role="dialog" style="z-index:99999999;">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/add_budgetrequest'), array('id'=>'budget-status-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                         <span class="add-title"> <?php echo _l('budget_status_remark'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_budget"></div>
                         <?php echo form_hidden('project_id',$project->id); ?>
                        <?php echo render_textarea('budget_status_remark','remarks'); ?>
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
appValidateForm($('#budget-status-form'), {
        budget_status_remark:'required'
		 
      }, manage_budstatus_types);
  function manage_budstatus_types(form) { 
    var data = $(form).serialize();
									  
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
		
        if(response.success == true){ 
            alert_float('success',response.message);
    	 
        }
       window.location.reload();
        $('#model_budgetstatus').modal('hide');
    });
    return false;
}
function budget_statusrequest_to_staff(id,status){
	$('#additional_budget').append(hidden_input('id',id));
	 $('#additional_budget').append(hidden_input('budget_status',status));
	 $('#model_budgetstatus').modal('show');
    $('.edit-title').addClass('hide');
}

</script>
