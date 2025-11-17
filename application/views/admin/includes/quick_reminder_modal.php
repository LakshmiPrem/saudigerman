<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="quick_reminder" tabindex="-1" role="dialog">
	<div class="modal-dialog">
		<?php echo form_open(admin_url('misc/add_quick_reminder'), array('id'=>'quick_reminder-form')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new').' '._l('reminder'); ?></span>
                </h4>
			
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						
						<?php echo render_datetime_input('date','set_reminder_date','',array('data-date-min-date'=>_d(date('Y-m-d')))); ?>
										               <div class="row">
                  <div class="col-md-6">
                     <div class="form-group">
                        <label for="rel_type" class="control-label"><?php echo _l('task_related_to'); ?></label>
                        <select name="rel_type" class="selectpicker" id="rem_rel_type" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""></option>
                           <option value="project"><?php echo _l('project'); ?></option>
                           <option value="invoice"><?php echo _l('invoice'); ?> </option>
                           <option value="customer"><?php echo _l('client'); ?></option>
                           <option value="estimate"><?php echo _l('estimate'); ?>
                           </option>
                           <option value="contract">
                              <?php echo _l('contract'); ?>
                           </option>
                           <option value="ticket" >
                              <?php echo _l('ticket'); ?>
                           </option>
                           <option value="expense">
                              <?php echo _l('expense'); ?>
                           </option>
                           <option value="lead">
                              <?php echo _l('lead'); ?>
                           </option>
                           <option value="proposal">
                              <?php echo _l('proposal'); ?>
                           </option>
                         </select>
                     </div>
                  </div>
                  <div class="col-md-6">
                     <div class="form-group hide" id="rem_rel_id_wrapper">
                        <label for="rel_id" class="control-label"><span class="rem_rel_id_label"></span></label>
                        <div id="rem_rel_id_select">
                           <select name="rel_id" id="rem_rel_id" class="ajax-sesarch" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php if(isset($rel_id)&&$rel_id != '' && $rel_type != ''){
                              $rel_data = get_relation_data($rel_type,$rel_id);
                              $rel_val   = get_relation_values($rel_data,$rel_type);
                              echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                              } ?>
                           </select>
                        </div>
                     </div>
                  </div>
               </div>
						<?php // echo render_input('company', 'milestone_name'); ?>
						<?php $members=get_staffinfo();?>
						<?php echo render_select('staff',$members,array('staffid',array('firstname','lastname')),'reminder_set_to',get_staff_user_id(),array('data-current-staff'=>get_staff_user_id())); ?>
						<?php echo render_textarea('description','reminder_description'); ?>
						<?php if(is_email_template_active('reminder-email-staff')) { ?>
						<div class="form-group">
							<div class="checkbox checkbox-primary">
								<input type="checkbox" name="notify_by_email" id="notify_by_email">
								<label for="notify_by_email">
									<?php echo _l('reminder_notify_me_by_email'); ?>
								</label>
							</div>
						</div>
						<?php } ?>

					</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					<?php echo _l('close'); ?>
				</button>
				<button type="submit" class="btn btn-info">
					<?php echo _l('submit'); ?>
				</button>
			</div>
		</div>
		<!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div>
	<!-- /.modal-dialog -->
</div> <!-- /.modal -->

<script>
	

	window.addEventListener( 'load', function () {
		var _rem_rel_id = $('#rem_rel_id'),
   _rem_rel_type = $('#rem_rel_type'),
   _rem_rel_id_wrapper = $('#rem_rel_id_wrapper'),
   data = {};
		appValidateForm( $( '#quick_reminder-form' ), {
			date: 'required',
			rel_type: 'required',
			rel_id: 'required',
			staff: 'required'
		}, manage_contract_reminders );
		$( '#quick_reminder' ).on( 'hidden.bs.modal', function ( event ) {
			$( '#additional' ).html( '' );
			//$( '#quick_reminder input[name="date"]' ).val( '' );
			$( '.add-title' ).removeClass( 'hide' );
			$( '.edit-title' ).removeClass( 'hide' );
		} );
		 $( "body" ).off( "change", "#rem_rel_id" );
	init_datepicker();
		 init_selectpicker();
		
   _rem_rel_type.on('change', function() {

     var clonedSelect = _rem_rel_id.html('').clone();
     _rem_rel_id.selectpicker('destroy').remove();
     _rem_rel_id = clonedSelect;
     $('#rem_rel_id_select').append(clonedSelect);
     $('.rem_rel_id_label').html(_rem_rel_type.find('option:selected').text());

     reminder_rel_select();
     if($(this).val() != ''){
      _rem_rel_id_wrapper.removeClass('hide');
    } else {
      _rem_rel_id_wrapper.addClass('hide');
    }
  //  init_remproject_details(_rem_rel_type.val());
   });
		
 function reminder_rel_select(){
	
      var serverData = {};
      serverData.rel_id = $('#rem_rel_id').val();
      data.type = $('#rem_rel_type').val();
      init_ajax_search($('#rem_rel_type').val(),$('#rem_rel_id'),serverData);
     }
     function init_remproject_details(type,tasks_visible_to_customer){
      var wrap = $('.non-project-details');
      var wrap_task_hours = $('.task-hours');
      if(type == 'project'){
        if(wrap_task_hours.hasClass('project-task-hours') == true){
          wrap_task_hours.removeClass('hide');
        } else {
          wrap_task_hours.addClass('hide');
        }
        wrap.addClass('hide');
        $('.project-details').removeClass('hide');
      } else {
        wrap_task_hours.removeClass('hide');
        wrap.removeClass('hide');
        $('.project-details').addClass('hide');
      
      }

    }
		
		 });
	function manage_contract_reminders( form ) {
		var data = $( form ).serialize();
		var url = form.action;
		$.post( url, data ).done( function ( response ) {
			response = JSON.parse( response );
			if ( response.success == true ) {
				alert_float( 'success', response.message );
			
				//window.location.href = admin_url + 'clients/client/' + response.id;
			}

			$( '#quick_reminder' ).modal( 'hide' );
		} );
		return false;
	}

	function new_quick_reminder() {
		$( '#quick_reminder' ).modal( 'show' );
		$( '.edit-title' ).addClass( 'hide' );
	}

	function edit_quick_reminder( invoker, id ) {
		var name = $( invoker ).data( 'name' );
		$( '#additional' ).append( hidden_input( 'id', id ) );
		$( '#quick_reminder input[name="name"]' ).val( name );
		$( '#quick_reminder' ).modal( 'show' );
		$( '.add-title' ).addClass( 'hide' );
	}
	


</script>