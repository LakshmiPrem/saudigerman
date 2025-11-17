<div class="modal fade" id="h_refer" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newhearing_reference'), array('id'=>'hrefer-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('hearing_reference_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_hearing_reference'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_hearing_reference"></div>
                        <?php echo render_input('name', 'contract_type_name'); ?>
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
      _validate_form($('#hrefer-type-form'),{name:'required'},manage_hearing_reference);
      $('#type').on('hidden.bs.modal', function(event) {
        $('#additional_hearing_reference').html('');
        $('#type input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_hearing_reference(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){ 
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('#hearing_reference');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-hearingReference')){
            $('.table-hearingReference').DataTable().ajax.reload();
        }
        $('#h_refer').modal('hide');
    });
    return false;
}
function new_hearingReference(){
    $('#h_refer').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_hearingReference(invoker,id){
    var name = $(invoker).data('name');
    $('#additional_hearing_reference').append(hidden_input('id',id));
    $('#h_refer input[name="name"]').val(name);
    $('#h_refer').modal('show');
    $('.add-title').addClass('hide');
}
</script>
