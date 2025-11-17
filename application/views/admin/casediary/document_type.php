<div class="modal fade" id="hearing_document_type" tabindex="-1" role="dialog" style="z-index:99999;">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newDocumentType'), array('id'=>'hearing_document_type-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_document_type"></div>
                        <?php echo render_input('name', 'contract_type_name'); ?>
                    </div>
                    <div class="col-md-12">
        
            <?php echo render_select('category',$category,array('id','name'),'category'); ?>
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
      _validate_form($('#hearing_document_type-type-form'),{name:'required'},manage_hearing_document_type);
      $('#hearing_document_type').on('hidden.bs.modal', function(event) {
        $('#additional_document_type').html('');
        $('#hearing_document_type input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_hearing_document_type(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('#document_type_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-document_type')){
            $('.table-document_type').DataTable().ajax.reload();
        }
        $('#hearing_document_type').modal('hide');
    });
    return false;
}
function new_document_type(){
    $('#hearing_document_type').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_document_type(invoker,id){
    var name = $(invoker).data('name');
    $('#additional_document_type').append(hidden_input('id',id));
    $('#hearing_document_type input[name="name"]').val(name);
    $('#hearing_document_type').modal('show');
    $('.add-title').addClass('hide');
}
</script>
