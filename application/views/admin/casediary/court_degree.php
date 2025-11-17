<div class="modal fade" id="model_court_degree" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newCourtDegree'), array('id'=>'model_court_degree-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('court_degree_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_court_degree'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
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
      _validate_form($('#model_court_degree-type-form'),{name:'required'},manage_court_degree);
      $('#model_court_degree').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#model_court_degree input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_court_degree(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('#court_degree');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-contract-types')){
            $('.table-contract-types').DataTable().ajax.reload();
        }
        $('#model_court_degree').modal('hide');
    });
    return false;
}
function new_court_degree(){
    $('#model_court_degree').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_type(invoker,id){
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id',id));
    $('#model_court_degree input[name="name"]').val(name);
    $('#model_court_degree').modal('show');
    $('.add-title').addClass('hide');
}
</script>
