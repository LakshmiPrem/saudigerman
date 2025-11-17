<div class="modal fade" id="model_courts" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newCourt'), array('id'=>'courts-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('court_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_court'); ?></span>
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
      _validate_form($('#courts-type-form'),{name:'required'},manage_courts_types);
      $('#model_courts').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#courts input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_courts_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){ 
            alert_float('success',response.message);
            if(($('body').hasClass('project') || $('body').hasClass('page-proposals-admin')) && typeof(response.id) != 'undefined') {
                var ctype = $('#court_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-courts')){
            $('.table-courts').DataTable().ajax.reload();
        }
        $('#model_courts').modal('hide');
    });
    return false;
}
function new_Courts(){
    $('#model_courts').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_court(invoker,id){
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id',id));
    $('#model_courts input[name="name"]').val(name);
    $('#model_courts').modal('show');
    $('.add-title').addClass('hide');
}
</script>
