<div class="modal fade" id="partytypes" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casetemplates/new_document_checklist'), array('id'=>'partytypes-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('party_type_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_partytype'); ?></span>
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
      _validate_form($('#partytypes-type-form'),{name:'required'},manage_party_types);
      $('#partytypes').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#partytypes input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_party_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            window.location.reload();
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('#document_checklists');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-contract-types')){
            $('.table-contract-types').DataTable().ajax.reload();
        }
        $('#partytypes').modal('hide');
    });
    return false;
}
function new_document_checklist(){
    $('#partytypes').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_type(invoker,id){
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id',id));
    $('#partytypes input[name="name"]').val(name);
    $('#partytypes').modal('show');
    $('.add-title').addClass('hide');
}
</script>
