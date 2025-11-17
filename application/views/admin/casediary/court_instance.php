<div class="modal fade" id="model_courts_instance" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newCourtInstance'), array('id'=>'courts-instance-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('courtinstance_edit'); ?></span>
                    <span class="add-title"><?php echo _l('create_instance'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional1"></div>
                        <?php echo render_input('name', 'contract_type_name'); ?>
                    </div>
                     <div class="col-md-12">
        
            <?php echo render_input('other_name', 'additional_name'); ?>
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
      _validate_form($('#courts-instance-form'),{name:'required'},manage_courts_instances);
      $('#model_courts_instance').on('hidden.bs.modal', function(event) {
        $('#additional1').html('');
        $('#courts_instance input[name="name"]').val('');
		 $('#courts_instance input[name="other_name"]').val('');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_courts_instances(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){ 
            alert_float('success',response.message);
            if(($('body').hasClass('project') || $('body').hasClass('page-proposals-admin')) && typeof(response.id) != 'undefined') {
                var ctype = $('#details_type');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
				var ctype = $('#instance_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
				
            }
        }else{
            alert_float('danger','Court Instabnce Already Exists.');
        }
        if($.fn.DataTable.isDataTable('.table-courtinstances')){
            $('.table-courtinstances').DataTable().ajax.reload();
        }
        $('#model_courts_instance').modal('hide');
    });
    return false;
}
function new_court_instance(){
    $('#model_courts_instance').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_court_instance(invoker,id){

    var name = $(invoker).data('name');
	 var sname = $(invoker).data('type');
    $('#additional1').append(hidden_input('id',id));
    $('#model_courts_instance input[name="name"]').val(name);
	 $('#model_courts_instance input[name="other_name"]').val(sname);
    $('#model_courts_instance').modal('show');
    $('.add-title').addClass('hide');
}
</script>
