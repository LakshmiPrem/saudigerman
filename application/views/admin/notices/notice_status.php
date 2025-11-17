<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="notice_status" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('notices/status'), array('id'=>'notice-status-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('notice_status_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_notice_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_notice_status"></div>
                        <?php echo render_input('name', 'notice_status_name'); ?>
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
      appValidateForm($('#notice-status-form'),{name:'required'},manage_notice_status);
      $('#notice_status').on('hidden.bs.modal', function(event) {
        $('#additional_notice_status').html('');
        $('#notice_status input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_notice_status(form) { 
    var data = $(form).serialize();
    var url = form.action; 
    $.post(url, data).done(function(response) { 
        response = JSON.parse(response); 
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('notice') && typeof(response.id) != 'undefined') {
                var ctype = $('#notice_status');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-notice-status')){
            $('.table-notice-status').DataTable().ajax.reload();
        }
        $('#notice_status').modal('hide');
    });
    return false;
}
function new_status(){
    $('#notice_status').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_status(invoker,id){
    var name = $(invoker).data('name');
    var color = $(invoker).data('color');
    $('#additional_notice_status').append(hidden_input('id',id));
    $('#notice_status input[name="name"]').val(name);
    $('#notice_status .colorpicker-input').colorpicker('setValue',color);
    $('#notice_status').modal('show');
    $('.add-title').addClass('hide');
}
</script>
