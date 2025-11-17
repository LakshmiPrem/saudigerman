<div class="modal fade" id="model_ipsubcategories" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('casediary/newIpsubcategory'), array('id'=>'ipsubcategories-type-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('ipsubcategory_edit'); ?></span>
                    <span class="add-title"><?php echo _l('new_ipsubcategory'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional"></div>
                        <?php echo render_input('subcategory_name', 'contract_type_name'); ?>
                    </div>
                      <div class="col-md-12" id="divipcategory1">
        			<?php $types=get_ip_types();?>
                    <?php echo render_select('category_id',$types,array('id','name'),'ip_category'); ?>
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
      _validate_form($('#ipsubcategories-type-form'),{name:'required'},manage_ipsubcategories_types);
      $('#model_ipsubcategories').on('hidden.bs.modal', function(event) {
        $('#additional').html('');
        $('#ipsubcategories input[name="subcategory_name"]').val('');
		  $('#category_id').selectpicker('val','');
        $('.add-title').removeClass('hide');
        $('.edit-title').removeClass('hide');
    });
  });
  function manage_ipsubcategories_types(form) {
    var data = $(form).serialize();
    var url = form.action;
    $.post(url, data).done(function(response) {
        response = JSON.parse(response);
        if(response.success == true){ 
            alert_float('success',response.message);
            if(($('body').hasClass('project') || $('body').hasClass('page-proposals-admin')) && typeof(response.id) != 'undefined') {
               
				 var ctype = $('#ip_subcategory');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
            }
        }
        if($.fn.DataTable.isDataTable('.table-ipsubcategories')){
            $('.table-ipsubcategories').DataTable().ajax.reload();
        }
        $('#model_ipsubcategories').modal('hide');
    });
    return false;
}
function new_Ipsubcategories(){
    $('#model_ipsubcategories').modal('show');
	  if($('body').hasClass('project')){
        var catid= $('#project_form select[name="ip_category"]').val();
    //   $('#additional').append(hidden_input('client_id',clientid));
      $('#category_id').selectpicker('val',catid);  
	  $('#model_ipsubcategories').find('#divipcategory1').hide();
     }
    $('.edit-title').addClass('hide');
}
function edit_ipsubcategory(invoker,id){
    var name = $(invoker).data('name');
	var category = $(invoker).data('type');
    $('#additional').append(hidden_input('id',id));
    $('#model_ipsubcategories input[name="subcategory_name"]').val(name);
	 $('#category_id').selectpicker('val',category);
    $('#model_ipsubcategories').modal('show');
    $('.add-title').addClass('hide');
}
</script>
