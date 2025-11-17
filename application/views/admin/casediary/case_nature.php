<div class="modal fade" id="model_case_nature" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newCaseNature'), array('id'=>'case_nature-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('new'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_case_nature"></div>
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
      _validate_form($('#case_nature-type-form'),{name:'required'},manage_case_nature_types);
      $('#model_case_nature').on('hidden.bs.modal', function(event) {
        $('#additional_case_nature').html('');
        $('#case_nature input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_case_nature_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){ 
            alert_float('success',response.message);
            if(($('body').hasClass('project') || $('body').hasClass('page-proposals-admin')) && typeof(response.id) != 'undefined') {
                var ctype = $('#case_nature');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-case_nature')){
            $('.table-case_nature').DataTable().ajax.reload();
        }
        $('#model_case_nature').modal('hide');
    });
    return false;
}
function new_case_nature(){
    $('#model_case_nature').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_case_nature(invoker,id){
    var name = $(invoker).data('name');
    $('#additional_case_nature').append(hidden_input('id',id));
    $('#model_case_nature input[name="name"]').val(name);
    $('#model_case_nature').modal('show');
    $('.add-title').addClass('hide');
}
</script>
