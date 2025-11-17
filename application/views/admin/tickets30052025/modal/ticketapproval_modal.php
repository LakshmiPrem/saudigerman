<!-- (Ajax Modal)-->
<div class="modal" id="approval_modal">
	<div class="modal-dialog  modal-lg" role="document">
		<?php echo form_open(admin_url('tickets/update_single_ticket_approvals'),array('id'=>'approval-form','autocomplete'=>'off')); ?>
		<input type="hidden" name="rel_type" value="<?php echo $rel_name; ?>">
		<input type="hidden" name="rel_id" value="<?php echo $rel_id; ?>">
		<div class="modal-content modal-content-demo">
			<div class="modal-header">
				<h6 class="modal-title">New Approval</h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
				<div class="col-md-12">
					<div class="panel panel-info">
						<div class="panel-body">
							<div class="form-group">
							 <label for="<?php echo _l('approval_name'); ?>"><small class="req text-danger">* </small><?php echo _l('approval_name'); ?></label>
                     <?php
								$value='';
                      $next_ref_number = get_option('next_reference_no');
                        $prefix = get_option('reference_prefix');
					   $_file_number = str_pad($next_ref_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); 
						//if($rel_name=='expense'){
							$value=get_request_client($rel_id,$rel_name);//$prefix.$_file_number;
						//}
								
								?>
					
								
								<input class="form-control" name="approval_name" placeholder="Enter Approval Name" required type="text" value="<?=$value?>" <?php if($value!='') echo 'readonly '?>>
							</div>
							<table  class="table table-striped  table-bordered" border="1" style="margin-top:0px;">
								<thead>
									<tr>
										<th scope="col">Approval Heading</th>
										<th scope="col">Approve By</th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									<?php foreach ($approval_headings as $main_approval_heading) { 
									?>
									<tr>
										<td>
											<select class="form-control select2" name="approval_heading_id[]" >
												<?php foreach($approval_headings  as $approval_heading){ ?>
													<option value="<?=$approval_heading['id']?>" <?php if($approval_heading['id'] == $main_approval_heading['id']) echo 'selected'; ?>><?=$approval_heading['name']?></option>
												<?php } ?>
											</select>
										</td>
										<td><select class="form-control select2" name="approval_assigned[]" >
												<?php foreach($staffs  as $staff){ ?>
													<option value="<?=$staff['staffid']?>"><?=$staff['firstname'].' '.$staff['lastname']?></option>
												<?php } ?>
											</select>
										</td>
										<td  contenteditable="false">
										<button type="button" class="btn btn-danger btn-bs-delete">Delete</button>
										</td>
									</tr>
									<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
				</div>  
        	
  			</div>
			<div class="modal-footer">
				<button class="btn ripple btn-primary" id="save_changes_approval_ticket" type="submit">Save</button>
				<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
		

<script>
$(function() {   
	$('.btn-bs-delete').click(function () { 
      $(this).parents('tr').remove();
   });

   $('#approval-form').submit(function(e) { 
	  
        e.preventDefault();
        $('#save_changes_approval_ticket').prop('disabled', true);
            var data = $(this).serialize();
			var url =  $(this).attr("action");//"<?php echo admin_url('approval/approvals')?>";
	  
            $.post(url, data).done(function(response) { 
				response = JSON.parse(response);
				// alert(response);
				if(response.success == true){
					alert_float('success', response.message);
					$('#approval_modal').modal('hide');
					window.location.reload();
				}
			/*	if($.fn.DataTable.isDataTable('.table-approval-headings')){
					$('.table-approval-headings').DataTable().ajax.reload();
				}*/
				
			});       
       
    	});

  
});

	
  </script>
			