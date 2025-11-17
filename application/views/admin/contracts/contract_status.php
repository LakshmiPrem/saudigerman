<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="contract_status" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('contracts/status'), array('id'=>'contract-status-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('contract_status_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_contract_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_contract_status"></div>
                        <?php echo render_input('name', 'contract_status_name'); ?>
                        <?php echo render_color_picker('statuscolor',_l('ticket_status_add_edit_color')); ?>
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
      appValidateForm($('#contract-status-form'),{name:'required'},manage_contract_status);
      $('#contract_status').on('hidden.bs.modal', function(event) {
        $('#additional_contract_status').html('');
        $('#contract_status input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_contract_status(form) { 
    var data = $(form).serialize();
    var url = form.action; 
    $.post(url, data).done(function(response) { 
        response = JSON.parse(response); 
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('contract') && typeof(response.id) != 'undefined') {
                var ctype = $('#contract_status');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-contract-status')){
            $('.table-contract-status').DataTable().ajax.reload();
        }
        $('#contract_status').modal('hide');
    });
    return false;
}
function new_status(){
    $('#contract_status').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_status(invoker,id){
    var name = $(invoker).data('name');
    var color = $(invoker).data('color');
    $('#additional_contract_status').append(hidden_input('id',id));
    $('#contract_status input[name="name"]').val(name);
    $('#contract_status .colorpicker-input').colorpicker('setValue',color);
    $('#contract_status').modal('show');
    $('.add-title').addClass('hide');
}
</script>
