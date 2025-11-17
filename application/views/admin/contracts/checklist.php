<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="checklist" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('contracts/checklist'), array('id'=>'checklist-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('checklist_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_checklist'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_checklist"></div>
                        <?php echo render_input('name', 'name'); ?>
                    </div>
                    <div class="col-md-12">
                        <?php echo render_input('key_provision', 'key_provision'); ?>
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
      appValidateForm($('#checklist-form'),{name:'required'},manage_checklist);
      $('#type').on('hidden.bs.modal', function(event) {
        $('#additional_contract_type').html('');
        $('#type input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_checklist(form) { 
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) { 
        response = JSON.parse(response);
        if(response.success == true){ 
            alert_float('success',response.message);
            window.location.href = '<?php echo admin_url("contracts/risk_value_checklist"); ?>';
            //if($('body').hasClass('risk_value_checklist') && typeof(response.id) != 'undefined') {
                // var ctype = $('#contract_type');
                // ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                // ctype.selectpicker('val',response.id);
                // ctype.selectpicker('refresh');
            //}
        }
        if($.fn.DataTable.isDataTable('.table-risk-checklist')){ 
            $('.table-risk-checklist').DataTable().ajax.reload();
        }
        $('#checklist').modal('hide');
    });
    return false;
}
function new_checklist(){
    $('#checklist').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_checklist(invoker,id){
    var name = $(invoker).data('name');
    var key_provision = $(invoker).data('key');
    $('#additional_checklist').append(hidden_input('id',id));
    $('#checklist input[name="name"]').val(name);
    $('#checklist input[name="key_provision"]').val(key_provision);
    $('#checklist').modal('show');
    $('.add-title').addClass('hide');
    $('.edit-title').addClass('show');
}
</script>
