<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="project-service-modal" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open(admin_url('projects/mattertype'),array('id'=>'project-service-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit_mattertype'); ?></span>
                    <span class="add-title"><?php echo _l('new_mattertype'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                        <div id="additional_matter_type"></div>
                        <?php $value= [

                                ['id' => 'litigation', 'name' => 'Litigation'],

                                ['id' => 'nonlitigation', 'name' => 'Nonlitigation']

                                ];?>
                         <?php 
                          if(get_staff_user_id()==1)
						$attr=array();
						else
						$attr= array('readonly'=>'true'); ?>
                         
                          <?php echo render_input('id','mattertypes','','text',$attr); ?>
                          
                          <?php echo render_select('type',$value,array('id','name'),'services_st_name'); ?>


      </select>
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
        appValidateForm($('#project-service-form'),{name:'required'},manage_ticket_services);
        $('#project-service-modal').on('hidden.bs.modal', function(event) {
            $('#additional_matter_type').html('');
            $('#project-service-modal input[name="id"]').val('');
			$('#project-service-modal input[name="type"]').val('');
            $('.add-title').removeClass('hide');
            $('.edit-title').removeClass('hide');
        });
    });
    function manage_ticket_services(form) {
        var data = $(form).serialize();
        var url = form.action;
        var ticketArea = $('body').hasClass('ticket');
        if(ticketArea) {
            data+='&ticket_area=true';
        }
        $.post(url, data).done(function(response) {
            if(ticketArea) {
               response = JSON.parse(response);
               if(response.success == true && typeof(response.id) != 'undefined'){
                var group = $('select#service');
                group.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                group.selectpicker('val',response.id);
                group.selectpicker('refresh');
            }
            $('#project-service-modal').modal('hide');
        } else {
            window.location.reload();
        }
    });
        return false;
    }
    function new_service(){
        $('#project-service-modal').modal('show');
        $('.edit-title').addClass('hide');
    }
    function edit_service(invoker,type_id){
        var id = $(invoker).data('id');
		 var type = $(invoker).data('type');
        $('#additional_matter_type').append(hidden_input('type_id',type_id));
        $('#project-service-modal input[name="id"]').val(id);
		$('#project-service-modal select[name="type"]').selectpicker('val',type);
        $('#project-service-modal select[name="type"]').selectpicker('refresh');
        $('#project-service-modal').modal('show');
        $('.add-title').addClass('hide');
    }
</script>
