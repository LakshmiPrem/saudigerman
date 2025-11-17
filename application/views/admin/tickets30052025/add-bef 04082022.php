<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'new_ticket_form')); ?>
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-body">
						<div class="row">
							<div class="col-md-6">
								<?php if(!isset($project_id) && !isset($contact)){ ?>
									<a href="#" id="ticket_no_contact"><span class="label label-default">
										<i class="fa fa-envelope"></i> <?php echo _l('ticket_create_no_contact'); ?>
									</span>
								</a>
								<a href="#" class="hide" id="ticket_to_contact"><span class="label label-default">
									<i class="fa fa-user-o"></i> <?php echo _l('ticket_create_to_contact'); ?>
								</span>
							</a>
							<div class="mbot15"></div>
						<?php } ?>
						<?php if(get_option('services') == 1){ ?>
								<div class="row"> <div class="col-md-12">
									<?php if(is_admin() || get_option('staff_members_create_inline_ticket_services') == '1'){
										echo render_select_with_input_group('service',$services,array('serviceid','name'),'ticket_settings_service','8','<a href="#" onclick="new_service();return false;"><i class="fa fa-plus"></i></a>');
									} else {
										echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service');
									}
									?>
									</div></div>	
								
							<?php } ?>
						<?php echo render_input('subject','ticket_settings_subject','','text',array('required'=>'true')); ?>
						<div class="form-group select-placeholder" id="ticket_contact_w">
							<label for="contactid"><?php echo _l('related_branch'); ?></label>
							<select name="contactid" required id="contactid" class="ajax-search" data-width="100%" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
								<?php if(isset($contact)) { ?>
									<option value="<?php echo $contact['id']; ?>" selected><?php echo $contact['firstname'] . ' ' .$contact['lastname']; ?></option>
								<?php } ?>
								<option value=""></option>
							</select>
							<?php echo form_hidden('userid'); ?>
						</div>
						<div class="row">
							<div class="col-md-6">
								<?php echo render_input('name','ticket_settings_to','','text',array('disabled'=>true)); ?>
							</div>
							<div class="col-md-6">
								<?php echo render_input('email','ticket_settings_email','','email',array('disabled'=>true)); ?>
							</div>
						</div>
						<div class="row">
						<div class="col-md-6">
						<div class="form-group">
						<?php echo render_input('opposteparty','customer_name','','text',array('required'=>'true')); ?>
							</div>	
						</div>
						<div class="col-md-6">
								<?php echo render_input('customer_code','ticket_settings_code','','text'); ?>
							</div>
							<div class="col-md-12">
								<?php echo render_input('cc','CCS'); ?>
							</div>
							
							
						</div>
					</div>
					<div class="col-md-6">
						<div class="form-group">
							<label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
							<input type="text" class="tagsinput" id="tags" name="tags" data-role="tagsinput">
						</div>
						<div class="row">
							<div class="col-md-6">

						<div class="form-group select-placeholder">
							<label for="assigned" class="control-label">
								<?php echo _l('ticket_settings_assign_to'); ?>
							</label>
							<select name="assigned" id="assigned" class="form-control selectpicker" data-live-search="true" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" data-width="100%">
								<option value=""><?php echo _l('ticket_settings_none_assigned'); ?></option>
								<?php foreach($staff as $member){ ?>
									<option value="<?php echo $member['staffid']; ?>" <?php if($member['staffid'] == get_staff_user_id()){echo 'selected';} ?>>
										<?php echo $member['firstname'] . ' ' . $member['lastname'] ; ?>
									</option>
								<?php } ?>
							</select>
						</div>
						</div>
						<div class="col-md-6">
								<?php echo render_input('file_amount','amt_case','','text'); ?>
						</div>
						</div>
						<div class="row">
							<div class="col-md-<?php if(get_option('services') == 1){ echo 6; }else{echo 12;} ?>">
								<?php $priorities['callback_translate'] = 'ticket_priority_translate';
								echo render_select('priority', $priorities, array('priorityid','name'), 'ticket_settings_priority', hooks()->apply_filters('new_ticket_priority_selected', 2), array('required'=>'true')); ?>
							</div>
							<div class="col-md-6">
								<?php echo render_select('department',$departments,array('departmentid','name'),'ticket_settings_departments',(count($departments) == 1) ? $departments[0]['departmentid'] : '',array('required'=>'true')); ?>
							</div>
							<?php if(get_option('services') == 1){ ?>
							<!--	<div class="col-md-6">
									<?php if(is_admin() || get_option('staff_members_create_inline_ticket_services') == '1'){
										echo render_select_with_input_group('service',$services,array('serviceid','name'),'ticket_settings_service','8','<a href="#" onclick="new_service();return false;"><i class="fa fa-plus"></i></a>');
									} else {
										echo render_select('service',$services,array('serviceid','name'),'ticket_settings_service');
									}
									?>
									
								</div>-->
							<?php } ?>
						</div>

						<div class="form-group projects-wrapper hide">
							<label for="project_id"><?php echo _l('project'); ?></label>
							<div id="project_ajax_search_wrapper">
								<select name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>"<?php if(isset($project_id)){ ?> data-auto-project="true" data-project-userid="<?php echo $userid; ?>"<?php } ?>>
									<?php if(isset($project_id)){ ?>
										<option value="<?php echo $project_id; ?>" selected><?php echo '#'.$project_id. ' - ' . get_project_name_by_id($project_id); ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
					<div class="col-md-12">
								
								 <?php echo render_textarea('gen_reason','reason','',array('rows'=>2),array(),'',''); ?>	
                      
                          <?php echo render_textarea('oth_comments','oth_comments','',array('rows'=>2),array(),'',''); ?>	
						</div>
                          <div class="col-md-12 hide" id="creditapp1">
                          <?php echo render_input('credit_saleperson','credit_saleperson','','text'); ?>
							</div>	
					</div>
					
					<div class="col-md-12">
						<?php echo render_custom_fields('tickets'); ?>
					</div>
				</div>
			</div>
		</div>
		<!--  Additional data for Case Close Application ------>
		<div class="row hide" id="closeapp">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<h4><?php echo _l('caseclose_info'); ?></h4>
					</div>
					<div class="panel-body">
					                          	<div class="row">
						<div class="col-md-4">
						<div class="form-group">
						<?php echo render_input('closecase_type','closecase_type','','text'); ?>
							</div>	
						</div>
						<div class="col-md-4">
								<?php echo render_input('ledgerclaim_amount','ledgerclaim_amount','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('closecase_amount','closecase_amount','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('total_expense','total_expense','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('amount_received','amount_received1','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('excess_amount','excess_amount','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('writeoff_amount','writeoff_amount','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('legalrequest_no','legalrequest_no','','text'); ?>
							</div>
							
						</div>
						                          <div class="col-md-12">
                             <fieldset style="padding: 20px;border:#F9F0F1 solid">
 						 <legend> <?=_l('civilcase_fileddet')?></legend>                
                        
           				 
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_close">  
                   <thead>
                   	<th width="25%"><?=_l('guaranteamount')?></th>
                   	<th width="75%"><?=_l('remarks')?></th>
                   	
                   
                   </thead>
                    <tbody>
                    
                                        
                       <?php 
														 $i=0;
					 ?>
                      <tr id="clrow<?=$i?>" class="dynamic-added-close"> 
                     
                     
                      <td><input type="text" name="civilcase_fileddet[amount][]" placeholder="Enter Amount" class="form-control" value=""  /></td>
                      <td><textarea  name="civilcase_fileddet[remarks][]" rows="2" class="form-control"></textarea></td>
                     
                      
                        <td><button type="button" name="addcasedet" id="addcasedet" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                    
                 
                    </tbody>
                     
                </table>  
							 </div>
          </fieldset>
           

												</div>
			
					</div>
				</div>
			</div>
		</div>
				<!--  Additional data for Civil case Application ------>
		<div class="row hide" id="civilapp">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<h4><?php echo _l('civil_attachment'); ?></h4>
					</div>
					<div class="panel-body">
						                          <div class="col-md-12">
                                            
                        
           				 
                     <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_civilattach">  
                   <thead>
                    <th class="not_sortable">Sr. No.</th>
                                    
                   	<th><?=_l('document_name')?></th>
                   	
                   	<th><?=_l('ticket_add_attachments')?></th>
                   
                   </thead>
                    <tbody>
                    
                                        
                       <?php 
														 $i=0;
						 foreach($document_types1 as $doc_type){
							 $i++;
					 ?>
                      <tr id="drow<?=$i?>" class="dynamic-added-civil"> 
                       <td><?=$i;?></td>                    
                      <td><input type="text" name="cvdocument_name[]" placeholder="Document Name" readonly class="form-control" value="<?=$doc_type['name']?>"  /><input type="hidden" name="cvdocument_type[]" value="<?=$doc_type['id']?>"  /></td>
                       <td><div class="form-group"><input type="file"  extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="cvattachments<?=$i?>" accept="<?php echo get_ticket_form_accepted_mimes(); ?>"></div></td>
                      
                         
                       
                    </tr>
                    <?php
						 }
						?>
                 
                    </tbody>
                     
                </table>  
							 </div>
           

												</div>
			
					</div>
				</div>
			</div>
		</div>
		<!--  Additional data for Credit Application ------>
			<div class="row hide" id="creditapprevision">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<h4><?php echo _l('credit_revision summary'); ?></h4>
					</div>
					<div class="panel-body">
					                          	<div class="row">
						<div class="col-md-4">
						<div class="form-group">
						<?php echo render_input('business_type','business_type','','text'); ?>
							</div>	
						</div>
						<div class="col-md-4">
								<?php echo render_input('excredit_days','excredit_days','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('excredit_amount','excredit_amount','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('procredit_days','procredit_days','','text'); ?>
							</div>
							<div class="col-md-4">
								<?php echo render_input('procredit_amount','procredit_amount','','text'); ?>
							</div>
							 <div class="col-md-4">
                       <div class="form-group">
          
            <label for="cars"><?=_l('bank_statment1')?></label>
  	
               <select name="bank_stmt" id="bank_stmt" class="form-control selectpicker" >
                <option value="yes">Available</option>
               <option value="no">Not Available</option>
              
               </select>


            </div>
                      </div>
                      <div class="col-md-4">
							              <div class="form-group">
          
            <label for="cars"><?=_l('payment_terms')?></label>
  	
               <select name="payment_terms" id="payment_terms" class="form-control selectpicker" >
                <option value="yes">Due Date</option>
               <option value="no" >Every Month PDC</option>
              
               </select>


            </div>
                      </div>
							<div class="col-md-4">
								<?php echo render_input('securechq_amount','securechq_amount','','text'); ?>
							</div>
							 <div class="col-md-4">
							              <div class="form-group">
          
            <label for="cars"><?=_l('balance_confirm')?></label>
  	
               <select name="balance_confirm" id="balance_confirm" class="form-control selectpicker" >
                <option value="yes">Yes</option>
               <option value="no" >No</option>
              
               </select>


            </div>
                      </div>
                      <div class="col-md-12">
								
								 <?php echo render_textarea('year_return_cheque','year_return_cheque','',array('rows'=>2),array(),'',''); ?>
					</div>
						
							 <div class="col-md-4">
							              <div class="form-group">
          
            <label for="cars"><?=_l('police_civilcase')?></label>
  	
               <select name="police_civilcase" id="police_civilcase" class="form-control selectpicker" >
                <option value="yes">Yes</option>
               <option value="no" >No</option>
              
               </select>


            </div>
                      </div>
							<div class="col-md-4">
							              <div class="form-group">
          
            <label for="cars"><?=_l('year_payment_default')?></label>
  	
               <select name="year_payment_default" id="year_payment_default" class="form-control selectpicker" >
                <option value="yes">Yes</option>
               <option value="no" >No</option>
              
               </select>


            </div>
                      </div>
                      
							<div class="col-md-4">
								<?php echo render_input('dealing_customer','dealing_customer','','text'); ?>
							</div>
							
						</div>

			
					</div>
				</div>
			</div>
		</div>
		<!--  Credit attachment   -->
		<div class="row hide" id="creditapp">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<h4><?php echo _l('credit_attachment'); ?></h4>
					</div>
					<div class="panel-body">
                    <div class="col-md-12">
                    <div class="table-responsive">  
                <table class="table table-bordered" id="dynamic_field_credit">  
                   <thead>
                   	<th><?=_l('document_type')?></th>
                   	<th><?=_l('document_number')?></th>
                   	<th><?=_l('document_name')?></th>
                   	<th><?=_l('nationality')?></th>
                   	<th><?=_l('expiry_date')?></th>
                   	<th><?=_l('ticket_add_attachments')?></th>
                   
                   </thead>
                    <tbody>
                    
                                        
                       <?php 
														 $i=0;
					 ?>
                      <tr id="drow<?=$i?>" class="dynamic-added-credit"> 
                      <td> <select class="form-control" name="document_type[]">
                     <option></option>
                     <?php foreach($document_types as $doc_type){ ?>
                        <option value="<?=$doc_type['id']?>" ><?=$doc_type['name']?></option>
                     <?php } ?>
                  </select></td>
                     
                      <td><input type="text" name="document_number[]" placeholder="Document Number" class="form-control"  /></td>
                      <td><input type="text" name="document_name[]" placeholder="Document Name" class="form-control"  /></td>
                      <td> <select class="form-control" name="nationality[]">
                     <option></option>
                     <?php foreach($nationality as $nat_type){ ?>
                        <option value="<?=$nat_type['country_id']?>"><?=$nat_type['short_name']?></option>
                     <?php } ?>
                  </select></td>
                      <td><input type="date" name="expiry_date[]" placeholder="Expiry Date" class="form-control" value=""  /></td> 
                       <td><input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="crattachments[]"  accept="<?php echo get_ticket_form_accepted_mimes(); ?>"></td>
                      
                        <td><button type="button" name="addoverdue" id="addoverdue" class="btn btn-success"><i class="fa fa-plus"></i></button></td>  
                       
                    </tr>
                    
                 
                    </tbody>
                     
                </table>  
							 </div>
           

												</div>
			
					</div>
				</div>
			</div>
		</div>
		
		<!-- Document Type  -->
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="panel-heading">
						<?php echo _l('ticket_add_body'); ?>
					</div>
					<div class="panel-body">
						<div class="btn-bottom-toolbar text-right">
							<button type="submit" data-form="#new_ticket_form" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>" class="btn btn-info"><?php echo _l('open_ticket'); ?></button>
						</div>
						<!--<div class="row">
							<div class="col-md-12 mbot20 before-ticket-message">
								<div class="row">
									<div class="col-md-6">
										<select id="insert_predefined_reply" data-width="100%" data-live-search="true" class="selectpicker" data-title="<?php echo _l('ticket_single_insert_predefined_reply'); ?>">
											<?php foreach($predefined_replies as $predefined_reply){ ?>
												<option value="<?php echo $predefined_reply['id']; ?>"><?php echo $predefined_reply['name']; ?></option>
											<?php } ?>
										</select>
									</div>
									<?php if(get_option('use_knowledge_base') == 1){ ?>
										<div class="visible-xs">
											<div class="mtop15"></div>
										</div>
										<div class="col-md-6">
											<?php $groups = get_all_knowledge_base_articles_grouped(); ?>
											<select id="insert_knowledge_base_link" data-width="100%" class="selectpicker" data-live-search="true" onchange="insert_ticket_knowledgebase_link(this);" data-title="<?php echo _l('ticket_single_insert_knowledge_base_link'); ?>">
												<option value=""></option>
												<?php foreach($groups as $group){ ?>
													<?php if(count($group['articles']) > 0){ ?>
														<optgroup label="<?php echo $group['name']; ?>">
															<?php foreach($group['articles'] as $article) { ?>
																<option value="<?php echo $article['articleid']; ?>">
																	<?php echo $article['subject']; ?>
																</option>
															<?php } ?>
														</optgroup>
													<?php } ?>
												<?php } ?>
											</select>
										</div>
									<?php } ?>
								</div>


							</div>
						</div>-->
						<div class="clearfix"></div>
						<?php echo render_textarea('message','','',array(),array(),'','tinymce'); ?>
					</div>
					<div class="panel-footer attachments_area">
						<div class="row attachments">
							<div class="attachment">
							
								<div class="col-md-4 col-md-offset-2 mbot15">
								      <div class="form-group">
                     <label class=""><?php echo _l('document_type'); ?></label>
                  <select class="form-control" name="document_type[]">
                     <option></option>
                     <?php foreach($document_types as $doc_type){ ?>
                        <option value="<?=$doc_type['id']?>" ><?=$doc_type['name']?></option>
                     <?php } ?>
                  </select>
									</div></div>
									
                  
										<div class="col-md-4 mbot15">
								
									<div class="form-group">
										<label for="attachment" class="control-label"><?php echo _l('ticket_add_attachments'); ?></label>
										<div class="input-group">
											<input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="attachments[0]"  accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
											<span class="input-group-btn">
												<button class="btn btn-success add_more_attachments p8-half" data-max="<?php echo get_option('maximum_allowed_ticket_attachments'); ?>" type="button"><i class="fa fa-plus"></i></button>
											</span>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php echo form_close(); ?>
</div>
</div>
<?php $this->load->view('admin/tickets/services/service'); ?>
<?php init_tail(); ?>
<?php hooks()->do_action('new_ticket_admin_page_loaded'); ?>
<script>
	$(function(){

		init_ajax_search('contact','#contactid.ajax-search',{
			tickets_contacts:true,
			contact_userid:function(){
					// when ticket is directly linked to project only search project client id contacts
					var uid = $('select[data-auto-project="true"]').attr('data-project-userid');
					if(uid){
						return uid;
					} else {
						return '';
					}
				}
			});

		validate_new_ticket_form();

		<?php if(isset($project_id) || isset($contact)){ ?>
			$('body.ticket select[name="contactid"]').change();
		<?php } ?>

		<?php if(isset($project_id)){ ?>
			$('body').on('selected.cleared.ajax.bootstrap.select','select[data-auto-project="true"]',function(e){
				$('input[name="userid"]').val('');
				$(this).parents('.projects-wrapper').addClass('hide');
				$(this).prop('disabled',false);
				$(this).removeAttr('data-auto-project');
				$('body.ticket select[name="contactid"]').change();
			});
		<?php } ?>
		$( "#service" ).change(function() {
  
			 var cttype = $('#service').val();
		//alert(cttype);
        if(cttype=='1' || cttype=='10')   
        {
						
			// $('select[name*="document_type"]').prop('required',true);
			$('#creditapp').addClass('hide');
			$('#creditapp1').addClass('hide');
			$('#closeapp').addClass('hide');
			$('#civilapp').removeClass('hide');
			$('#creditapprevision').addClass('hide');
			//$('.attachments input[name*="attachments"]').prop('required',true);
			$('input[name="customer_code"]').prop('required',true);
			var rowCount = $("#dynamic_field_civilattach tr").length;
			
			for (var i =1; i < rowCount; i++) {
				var fname='cvattachments'+i;
			
				$('input[name="'+fname+'"]').prop('required',true);
			//$('input[name="cvattachments2"]').prop('required',true);
		
			}
		}
			 else if(cttype=='2' || cttype=='11')
			{
				$('#creditapp').addClass('hide');
				$('#creditapp1').addClass('hide');
				$('#closeapp').addClass('hide');
				$('#civilapp').addClass('hide');
				$('#creditapprevision').addClass('hide');
				$('input[name="customer_code"]').prop('required',true);
			}
			else if(cttype=='5' || cttype=='3')
			{
				
				$('input[name="customer_code"]').prop('required',true);
				$('#creditapp').removeClass('hide');
				$('#creditapp1').addClass('hide');
				$('#creditapprevision').removeClass('hide');
				$('#closeapp').addClass('hide');
				$('#civilapp').addClass('hide');
				$('select[name*="document_type"]').prop('required',false);
			// $('#document_type').prop('required',false);
				$('.attachments input[name*="attachments"]').prop('required',false);
			}
			else if(cttype=='6' || cttype=='9')
			{
				
				$('input[name="customer_code"]').prop('required',false);
				$('input[name="credit_saleperson"]').prop('required',true);
				$('#creditapp').removeClass('hide');
				$('#creditapp1').removeClass('hide');
				$('#creditapprevision').addClass('hide');
				$('#closeapp').addClass('hide');
				$('#civilapp').addClass('hide');
				$('select[name*="document_type"]').prop('required',false);
			// $('#document_type').prop('required',false);
				$('.attachments input[name*="attachments"]').prop('required',false);
			}
			 else if(cttype=='7')
			{
				$('#creditapp').addClass('hide');
				$('#creditapp1').addClass('hide');
				$('#closeapp').removeClass('hide');
				$('#civilapp').addClass('hide');
				$('#creditapprevision').addClass('hide');
				$('input[name="customer_code"]').prop('required',true);
			}
		else
			{
				$('#creditapp').addClass('hide');
				$('#creditapp1').addClass('hide');
				$('#closeapp').addClass('hide');
				$('#civilapp').addClass('hide');
				$('select[name*="document_type"]').prop('required',false);
				$('input[name="customer_code"]').prop('required',false);
			// $('#document_type').prop('required',false);
				$('.attachments input[name*="attachments"]').prop('required',false);
			}
});
		var cd=1;var cc=1;
		$('#addoverdue').click(function(){  
		 
           cd++;  
           $('#dynamic_field_credit').append('<tr id="drow'+cd+'"class="dynamic-added-credit"> <td> <select class="form-control" name="document_type[]"><option></option> <?php foreach($document_types as $doc_type){ ?><option value="<?=$doc_type['id']?>" ><?=$doc_type['name']?></option> <?php } ?></select></td><td><input type="text" name="document_number[]" placeholder="Document Number" class="form-control"  /></td><td><input type="text" name="document_name[]" placeholder="Document Name" class="form-control"  /></td><td> <select class="form-control" name="nationality[]"><option></option><?php foreach($nationality as $nat_type){ ?><option value="<?=$nat_type['country_id']?>"><?=$nat_type['short_name']?></option><?php } ?> </select></td><td><input type="date" name="expiry_date[]" placeholder="Expiry Date" class="form-control" value=""/></td><td><input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="crattachments[]"  accept="<?php echo get_ticket_form_accepted_mimes(); ?>"></td><td><button type="button" name="remove" id="'+cd+'" class="btn btn-danger btn_credit_remove">X</button></td></tr>');  
			
      });
  
      $(document).on('click', '.btn_credit_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#drow'+button_id+'').remove();  
      }); 
		$('#addcasedet').click(function(){  
		 
           cc++;  
           $('#dynamic_field_close').append('<tr id="clrow'+cc+'"class="dynamic-added-close"> <td width="25%"><input type="text" name="civilcase_fileddet[amount][]" placeholder="Enter Amount" class="form-control" value=""  /></td><td width="75%"><textarea  name="civilcase_fileddet[remarks][]" rows="2" class="form-control"></textarea></td> <td><button type="button" name="remove" id="'+cc+'" class="btn btn-danger btn_close_remove">X</button></td></tr>');  
			
      });
  
      $(document).on('click', '.btn_close_remove', function(){  
           var button_id = $(this).attr("id");  
		 
           $('#clrow'+button_id+'').remove();  
      }); 
		
		
	});

</script>
</body>
</html>
