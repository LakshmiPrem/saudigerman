<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="customer_shareholder_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button group="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">
                    <span class="edit-title"><?php echo _l('customer_shareholder_edit_heading'); ?></span>
                    <span class="add-title"><?php echo _l('customer_shareholder_add_heading'); ?></span>
                </h4>
            </div>
            <?php echo form_open('admin/clients/shareholder',array('id'=>'customer-shareholder-modal')); ?>
            <div class="modal-body">
                <div class="row">
                   <div class="col-md-12">
                   	 <?php
						 $s_types = [['id'=>'person','name'=>'Internal Individual'],['id'=>'external','name'=>'External Party (Person and Company)'],['id'=>'internal','name'=>'Internal Entity / Business Unitl']];
						echo render_select('stake_type',$s_types,array('id','name'),'category'); ?>
                   </div>
                    <div class="col-md-12 hide" id="internal">
                   	<?php 
						// $clients_=get_client_list();
				          echo render_select('internal_party',$clients_,array('userid','company'),'internal_party');
                       ?> 
                   </div>
                    <div class="col-md-12 hide" id="external">
                   	<?php 
						// $comp_person=get_otherparty_list();
				          echo render_select('external_party',$comp_person,array('id','name'),'external_party');
                       ?>  
                   </div>
                    <div class="col-md-12">
                       
                        <?php echo render_input( 'firstname', 'client_firstname',''); ?>
                        <?php echo render_input( 'lastname', 'client_lastname',''); ?>
                        <?php echo render_input( 'email', 'client_email','', 'email'); ?>
                        <?php echo form_hidden('id'); ?>
                         <?php echo render_input( 'phonenumber', 'client_phonenumber','','text',array('autocomplete'=>'off')); ?>
                        
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button group="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button group="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
    window.addEventListener('load',function(){
       appValidateForm($('#customer-shareholder-modal'), {
        firstname: 'required',stake_type:'required'
    }, manage_customer_shareholders);

       $('#customer_shareholder_modal').on('show.bs.modal', function(e) {
        var invoker = $(e.relatedTarget);
        var group_id = $(invoker).data('id');
		  
        $('#customer_shareholder_modal .add-title').removeClass('hide');
        $('#customer_shareholder_modal .edit-title').addClass('hide');
        $('#customer_shareholder_modal input[name="id"]').val('');
        $('#customer_shareholder_modal input[name="firstname"]').val('');
		$('#customer_shareholder_modal input[name="lastname"]').val('');
		 $('#stake_type').selectpicker('val','');
		 $('#customer_shareholder_modal input[name="phonenumber"]').val('');
		    $('#customer_shareholder_modal input[name="email"]').val('');
        // is from the edit button
        if (typeof(group_id) !== 'undefined') {
            $('#customer_shareholder_modal input[name="id"]').val(group_id);
            $('#customer_shareholder_modal .add-title').addClass('hide');
            $('#customer_shareholder_modal .edit-title').removeClass('hide');
            $('#customer_shareholder_modal input[name="firstname"]').val($(invoker).parents('tr').find('td').eq(1).text());
			$('#customer_shareholder_modal input[name="lastname"]').val($(invoker).parents('tr').find('td').eq(2).text());
			  $('#stake_type').selectpicker('val',$(invoker).parents('tr').find('td').eq(3).text());
			$('#customer_shareholder_modal input[name="email"]').val($(invoker).parents('tr').find('td').eq(5).text());
			$('#customer_shareholder_modal input[name="phonenumber"]').val($(invoker).parents('tr').find('td').eq(4).text());
        }
    });
		$("#stake_type" ).change(function() {
  
			 var cttype = $('#stake_type').val();
			if(cttype=='internal'){
				$('#internal').removeClass('hide');
				$('#external').addClass('hide');
				$('#customer_shareholder_modal input[name="firstname"]').val('');
			 $('#customer_shareholder_modal input[name="lastname"]').val('');
             $('#customer_shareholder_modal input[name="mobile"]').val('');
			}else if(cttype=='external'){
				$('#internal').addClass('hide');
				$('#external').removeClass('hide');
				 $('#customer_shareholder_modal input[name="firstname"]').val('');
			 $('#customer_shareholder_modal input[name="lastname"]').val('');
             $('#customer_shareholder_modal input[name="mobile"]').val('');
			}else{
				$('#internal').addClass('hide');
				$('#external').addClass('hide');
		     $('#customer_shareholder_modal input[name="firstname"]').val('');
			 $('#customer_shareholder_modal input[name="lastname"]').val('');
             $('#customer_shareholder_modal input[name="mobile"]').val('');
			}
		
		
	});
   $('#external_party').on('change',function(){
        get_external_details();
    });
   $('#internal_party').on('change',function(){
        get_internal_details();
    });

   });
    function manage_customer_shareholders(form) {
        var data = $(form).serialize();
        var url = form.action;
        $.post(url, data).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
				if($.fn.DataTable.isDataTable('.table-customer-shareholders')){
                    $('.table-customer-shareholders').DataTable().ajax.reload();
                }
                    if(($('body').hasClass('customer-profile') || $('body').hasClass('project')) && typeof(response.id) != 'undefined') {
                   var ctype = $('#shareholder_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
				var ctype = $('#stakeholder_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
				var ctype = $('#litclient_id');
                ctype.find('option:first').after('<option value="'+response.id+'">'+response.name+'</option>');
                ctype.selectpicker('val',response.id);
                ctype.selectpicker('refresh');
                 var groups = $('select[name="bus_stakeholder[]"]');
                    groups.prepend('<option value="'+response.id+'">'+response.name+'</option>');
					groups.selectpicker('val',response.id);
                    groups.selectpicker('refresh');
                }
                alert_float('success', response.message);
            }
            $('#customer_shareholder_modal').modal('hide');
        });
        return false;
    }
 
    function get_external_details(id) {
        var id =$('#external_party').val();
        if(id >0){
            $.get(admin_url + 'opposite_parties/get_external_details/'+id,function(response){
                if(response.success == true){ 
                   var temp = response.exparties;
					  $('#customer_shareholder_modal input[name="firstname"]').val(temp.name);
			$('#customer_shareholder_modal input[name="lastname"]').val(temp.name);
               $('#customer_shareholder_modal input[name="mobile"]').val(temp.mobile);
		
                } else {
                    alert_float('danger',response.message);
                }
            },'json');
        }
    }
 	function get_internal_details(id) {
        var id =$('#internal_party').val();
        if(id >0){
            $.get(admin_url + 'clients/get_internal_details/'+id,function(response){
                if(response.success == true){ 
                   var temp = response.inparties;
					  $('#customer_shareholder_modal input[name="firstname"]').val(temp.company);
			$('#customer_shareholder_modal input[name="lastname"]').val(temp.company);
               $('#customer_shareholder_modal input[name="mobile"]').val(temp.phonenumber);
		
                } else {
                    alert_float('danger',response.message);
                }
            },'json');
        }
    }
</script>
