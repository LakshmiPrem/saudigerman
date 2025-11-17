<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="quick_opposite_party" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('opposite_parties/add_quick_opposite_party'), array('id'=>'quick_opposite_party-form')); ?>
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
                        <div id="additional_qoppoparty_div"></div>
                        <?php echo render_input('name', 'opposite_company'); ?>
                         <?php echo render_input( 'firstname', 'firstname'); ?>
                          <?php echo render_input( 'lastname', 'lastname'); ?>
                             <div class="form-group select-placeholder">
                                    <label for="clientid" class="control-label"><?php echo _l('project_customer'); ?></label>
                                    <select id="clientid" name="client_id" data-live-search="true" data-width="100%" class="ajax-search"  data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                     <?php $selected = '';
                                     if($selected == ''){
                                         $selected = (isset($customer_id) ? $customer_id: '');
                                     }
                                     if($selected != ''){
                                        $rel_data = get_relation_data('customer',$selected);
                                        $rel_val = get_relation_values($rel_data,'customer');
                                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                                    } ?>
                                    </select>
                                </div>
                           <?php echo render_input( 'email', 'email'); ?>
                           <?php echo render_input( 'mobile', 'mobile'); ?>
                            <?php echo render_textarea( 'address', 'address'); ?>
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
      appValidateForm($('#quick_opposite_party-form'),{name:'required'},manage_oppose_types);
      $('#quick_opposite_party').on('hidden.bs.modal', function(event) {
        $('#additional_qoppoparty_div').html('');
        $('#quick_opposite_party input[name="name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
	
  });
  function manage_oppose_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){
            alert_float('success',response.message);
            if($('body').hasClass('project') && typeof(response.id) != 'undefined') {
                var ctype = $('select[name="opposite_party[]"]');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
			 window.location.href = admin_url + response.link;
        }
        
        $('#quick_opposite_party').modal('hide');
    });
    return false;
}
function new_quick_opposite_party(){
    $('#quick_opposite_party').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_quick_opposite_party(invoker,id){
    var name = $(invoker).data('name');
    $('#additional_qoppoparty_div').append(hidden_input('id',id));
    $('#quick_opposite_party input[name="name"]').val(name);
    $('#quick_opposite_party').modal('show');
    $('.add-title').addClass('hide');
}
</script>
