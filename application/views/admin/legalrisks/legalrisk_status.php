<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('legal_risks/riskstatus'), array('id'=>'legalrisk-status-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('legalrisk_status_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_risk_status'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_legal_risk_status"></div>
                        <?php echo render_input('statusname', 'risk_status'); ?>
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
      _validate_form($('#legalrisk-status-form'),{name:'required'},manage_legalrisk_statuses);
      $('#type').on('hidden.bs.modal', function(event) {
        $('#additional_legal_risk_status').html('');
        $('#type input[name="statusname"]').val('');
		  $('#type .colorpicker-input').colorpicker('setValue','');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_legalrisk_statuses(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('contract') && typeof(response.id) != 'undefined') {
                var ctype = $('#legalrisk_status');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-legalrisk-statuses')){
            $('.table-legalrisk-statuses').DataTable().ajax.reload();
        }
        $('#type').modal('hide');
    });
    return false;
}
function new_type_status(){
    $('#type').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_type_status(invoker,id){
    var name = $(invoker).data('name');
	var color = $(invoker).data('color');
    $('#additional_legal_risk_status').append(hidden_input('id',id));
    $('#type input[name="statusname"]').val(name);
	$('#type .colorpicker-input').colorpicker('setValue',color);
    $('#type').modal('show');
    $('.add-title').addClass('hide');
}
</script>
