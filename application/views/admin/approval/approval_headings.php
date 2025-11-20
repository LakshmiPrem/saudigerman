<div class="modal fade" id="hearing_document_type" tabindex="-1" role="dialog" style="z-index:99999;">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('approval/newApprovalType'), array('id'=>'hearing_document_type-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
        
            <?php echo render_select('designation_id',$designations,array('id','name'),'approval_heading'); ?>
          </div>
                    <div class="col-md-12">
                        <div id="additional_approval"></div>
                        <?php echo render_input('name', 'contract_type_name'); ?>
                    </div>
                    
              <div class="col-md-12">
        
            <?php echo render_select('rel_type',$reltypes,array('id','name'),'related_to'); ?>
		  </div>
              <!-- <div class="col-md-12">
												<label>Related To</label>
												<select class="form-control select2" name="rel_type" id="rel_type" required>
													<option value="expense">Expense</option>
													<option value="legal_request">Legal Request</option>
												</select>
											</div>-->
               <div class="col-md-12">
                       
                        <?php echo render_input('head_order', 'approval_order'); ?>
                    </div>
                     <div class="col-md-12">
                    <?php $threshold_limits=get_threshold_limits();?>
            <?php echo render_select('threshold_limit',$threshold_limits,array('id','name'),'threshold_limit'); ?>
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
  window.addEventListener('load',function(){
      _validate_form($('#hearing_document_type-type-form'),{designation_id:'required',name:'required'},manage_hearing_document_type);
      $('#hearing_document_type').on('hidden.bs.modal', function(event) {
        $('#additional_approval').html('');
        $('#hearing_document_type input[name="name"]').val('');
		  $('#hearing_document_type input[name="head_order"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
     // On change of the select
    $('#designation_id').on('change', function() {
        var selectedText = $("#designation_id option:selected").text();
        
        // Display the selected name
         $('#hearing_document_type input[name="name"]').val(selectedText);
       // $('#designation_name_display').text(selectedText);
    });
  });
  function manage_hearing_document_type(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('#document_type_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-approval_heading')){
            $('.table-approval_heading').DataTable().ajax.reload();
        }
        $('#hearing_document_type').modal('hide');
    });
    return false;
}
function new_approval_heading(){
    $('#hearing_document_type').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_approval_heading(invoker,id){
    var name = $(invoker).data('name');
	 var designation = $(invoker).data('desig');
	 var order = $(invoker).data('order');
     var limit = $(invoker).data('limit');
	 var rtype = $(invoker).data('type');
    $('#additional_approval').append(hidden_input('id',id));
    $('#hearing_document_type input[name="name"]').val(name);
//	$('#hearing_document_type input[name="rel_id"]').val(category);
	// $("#rel_id").selectpicker('val',category);
	 $("#rel_type").selectpicker('val',rtype);
	 $("#designation_id").selectpicker('val',designation);
      $("#threshold_limit").selectpicker('val',limit);
	$('#hearing_document_type input[name="head_order"]').val(order);
    $('#hearing_document_type').modal('show');
    $('.add-title').addClass('hide');
	
}
</script>
