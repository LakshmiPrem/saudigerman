<div class="modal fade" id="type" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('legal_risks/type'), array('id'=>'legalrisk-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('legalrisk_type_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_risk_type'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_legal_risk_type1"></div>
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
      _validate_form($('#legalrisk-type-form'),{name:'required'},manage_legalrisk_types);
      $('#type').on('hidden.bs.modal', function(event) {
        $('#additional_legal_risk_type1').html('');
        $('#type input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_legalrisk_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('contract') && typeof(response.id) != 'undefined') {
                var ctype = $('#legalrisk_type');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-legalrisk-types')){
            $('.table-legalrisk-types').DataTable().ajax.reload();
        }
        $('#type').modal('hide');
    });
    return false;
}
function new_type1(){ 
    $('#type').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_type1(invoker,id){ 
    var name = $(invoker).data('name');
    $('#additional_legal_risk_type1').append(hidden_input('id',id));
    $('#type input[name="name"]').val(name);
    $('#type').modal('show');
    $('.add-title').addClass('hide');
}
</script>
