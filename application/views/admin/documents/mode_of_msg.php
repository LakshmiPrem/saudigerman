<div class="modal fade" id="type1" tabindex="-1" role="dialog">
<div class="modal-dialog">
        <?php echo form_open(admin_url('documents/mode_of_msg_manage'), array('id'=>'mode_of_msg-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('mode_of_msg_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_mode_of_msg'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'name'); ?>
                        <!-- <?php echo render_color_picker('statuscolor',_l('ticket_status_add_edit_color')); ?> -->
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
      appValidateForm($('#mode_of_msg-form'),{name:'required'},manage_mode_of_msg);
      $('#type1').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#type1 input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_mode_of_msg(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            // if($('body').hasClass('type1') && typeof(response.id) != 'undefined') {
                var ctype = $('#mode_of_msg');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            // }
        }
        if($.fn.DataTable.isDataTable('.table-mode_of_msg')){
            $('.table-mode_of_msg').DataTable().ajax.reload();
        }
        $('#type1').modal('hide');
    });
    return false;
}
function new_type1(){
    $('#type1').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_mode_of_msg(invoker,id){
    var name = $(invoker).data('name');
    var color = $(invoker).data('color');
    $('#additional').append(hidden_input('id',id));
    $('#type1 input[name="name"]').val(name);
    $('#type1 .colorpicker-input').colorpicker('setValue',color);
    $('#type1').modal('show');
    $('.add-title').addClass('hide');
}
</script>
