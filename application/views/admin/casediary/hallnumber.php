<div class="modal fade" id="hallnumbers" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newhallnumber'), array('id'=>'hallnumbers-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('hallnumber_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_hallnumber'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_hallnumber"></div>
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
      _validate_form($('#hallnumbers-type-form'),{name:'required'},manage_hallnumber_types);
      $('#hallnumbers').on('hidden.bs.modal', function(event) {
        $('#additional_hallnumber').html('');
        $('#hallnumbers input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_hallnumber_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('#hall_number');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-contract-types')){
            $('.table-contract-types').DataTable().ajax.reload();
        }
        $('#hallnumbers').modal('hide');
    });
    return false;
}
function new_hallnumber(){
    $('#hallnumbers').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_hallnumber(invoker,id){
    var name = $(invoker).data('name');
    $('#additional_hallnumber').append(hidden_input('id',id));
    $('#hallnumbers input[name="name"]').val(name);
    $('#hallnumbers').modal('show');
    $('.add-title').addClass('hide');
}
</script>
