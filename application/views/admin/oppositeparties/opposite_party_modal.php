<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="opposite_party" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('opposite_parties/add_opposite_party_name'), array('id'=>'opposite_party-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new').' '._l('opposite_party'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('name', 'opposite_company'); ?>
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
      appValidateForm($('#opposite_party-form'),{name:'required'},manage_contract_types);
      $('#opposite_party').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#opposite_party input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_contract_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('ticket') && typeof(response.id) != 'undefined') {
                var ctype = $('#opposteparty');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-contract-types')){
            $('.table-contract-types').DataTable().ajax.reload();
        }
        $('#opposite_party').modal('hide');
    });
    return false;
}
function new_opposite_party(){
    $('#opposite_party').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_opposite_party(invoker,id){
    var name = $(invoker).data('name');
    $('#additional').append(hidden_input('id',id));
    $('#opposite_party input[name="name"]').val(name);
    $('#opposite_party').modal('show');
    $('.add-title').addClass('hide');
}
</script>
