<div class="modal fade" id="lawyer_category_model" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('lawyers/lawyer_category'), array('id'=>'lawyer-category-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_category'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <div class="col-md-8">
                            <?php echo render_input('name', 'contract_type_name');
                         ?>
                        </div>

                        <div class="col-md-4">
                            <?php echo render_input('rate', 'rate');
                         ?>
                        </div>
                        
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
      _validate_form($('#lawyer-category-form'),{name:'required',rate:'required'},manage_lawyer_category);
      $('#lawyer_category_model').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#lawyer_category_model input[name="name"]').val('');
        $('#lawyer_category_model input[name="rate"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_lawyer_category(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) { 
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('page-lawyers-admin') && typeof(response.id) != 'undefined') {
                var ctype = $('#category_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-lawyer-category')){
            $('.table-lawyer-category').DataTable().ajax.reload();
        }
        $('#lawyer_category_model').modal('hide');
    });
    return false;
}
function new_category(){
    $('#lawyer_category_model').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_category(invoker,id){
    var name = $(invoker).data('name');
    var inv =  name.split("##");
    $('#additional').append(hidden_input('id',id));
    $('#lawyer_category_model input[name="name"]').val(inv[0]);
    $('#lawyer_category_model input[name="rate"]').val(inv[1]);
    $('#lawyer_category_model').modal('show');
    $('.add-title').addClass('hide');
}
</script>
