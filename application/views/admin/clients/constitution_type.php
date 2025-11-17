<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="contype" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('clients/constitutiontype'), array('id'=>'constitution-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('constitution_type_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_constitution_type'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'constitution_type_name'); ?>
                        <?php echo render_input('shortname', 'services_st_name'); ?>
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
      appValidateForm($('#constitution-type-form'),{name:'required'},manage_constitution_types);
      $('#contype').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#contype input[name="name"]').val('');
	    $('#contype input[name="shortname"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_constitution_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('client') && typeof(response.id) != 'undefined') {
                var ctype = $('#document_type');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-constitution-types')){
            $('.table-constitution-types').DataTable().ajax.reload();
        }
        $('#contype').modal('hide');
    });
    return false;
}
function new_contype(){
	 $('.edit-title').addClass('hide');
    $('#contype').modal('show');
   
}
function edit_contype(invoker,id){
	  $('.add-title').addClass('hide');
    var name = $(invoker).data('name');
	  var sname = $(invoker).data('short');
    $('#additional').append(hidden_input('id',id));
    $('#contype input[name="name"]').val(name);
	$('#contype input[name="shortname"]').val(sname);
    $('#contype').modal('show');
  
}
</script>
