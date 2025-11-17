<!-- (Ajax Modal)-->
<div class="modal" id="approval_modal">
	<div class="modal-dialog  modal-lg" role="document">
		<?php echo form_open(admin_url('approval/approvals'),array('id'=>'approval-form','autocomplete'=>'off')); ?>
		<input type="hidden" name="rel_type" value="<?php echo $rel_name; ?>">
		<input type="hidden" name="rel_id" value="<?php echo $rel_id; ?>">
		<div class="modal-content modal-content-demo">
			<div class="modal-header">
				<h6 class="modal-title"><?=_l('new_approval')?></h6><button aria-label="Close" class="close" data-dismiss="modal" type="button"><span aria-hidden="true">&times;</span></button>
			</div>
			<div class="modal-body">
				<div class="row">
				<div class="col-md-12">
					<div class="panel panel-info">
						<div class="panel-body">
							<div class="form-group col-md-7">
							 <label for="<?php echo _l('reference_no'); ?>"><small class="req text-danger">* </small><?php echo _l('reference_no'); ?></label>
                     <?php
								$value='';
                      $next_ref_number = get_option('next_reference_no');
                        $prefix = get_option('reference_prefix');
					   $_file_number = str_pad($next_ref_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); 
						
							$value=get_request_client($rel_id,$rel_name);//$prefix.$_file_number;
							$refno=get_approval_reference($rel_id,$rel_name);
              $cur_refno=!empty($refno)?$refno:$value; 			
								?>
								
								<input class="form-control" name="approval_name" placeholder="Enter Approval Name" required type="text" value="<?=$cur_refno?>" <?php if($cur_refno!='') echo 'readonly '?>>
							</div>
              <div class="col-md-5">
               <?php $appdue_date=get_approval_duedate($rel_id,$rel_name);?>
               <?php $value = !empty($appdue_date)?_d($appdue_date):_d(date('Y-m-d')); ?>
               <?php echo render_date_input('approvaldue_date','approval_due_date',$value); ?>
            </div>
							 <table  class="table table-striped table-responsive table-bordered _approval_stages_table cases_" id="case_app" border="1" style="margin-top:0px;" width="100%">
							
								<thead>
									<tr>
										<th scope="col"><?php echo _l('approval_heading'); ?></th>
										<th scope="col"><?php echo _l('approval_by'); ?></th>
										<th></th>
									</tr>
								</thead>
								<tbody>
									 <?php 
									$i=0;
									$approval_stages_selected=[];
									  $approval_stages_selected = get_approvals($rel_id,$rel_name);
						if(sizeof($approval_stages_selected)>0){ 
                          foreach ($approval_stages_selected as  $bs) { 
                         ?> 
                         <?php if($bs['approval_status']==3){$st1=array('disabled'=>'disabled'); } else {$st1=array();}?>
						<tr id="bsrow<?=$i?>">
                            <input type="hidden" name="approval_row_id[]" value="<?php echo $bs['id']; ?>">
							 <td><?php echo render_select('approval_heading_id[]',$approval_headings ,array('id','name'),'',$bs['approval_heading_id'],$st1);?></td>
                           <td><?php echo render_select('approval_assigned[]',$staffs ,array('staffid',array('firstname','lastname')),'',$bs['staffid'],$st1,[],'','',false);?></td>
                            <td><?php echo render_input('approval_remarks[]','',$bs['approval_remarks']);?></td>
                             <?php if($bs['approval_status']==1 || $bs['approval_status']==2)  {?>                                             
                            <td width="10%" contenteditable="false">
                            <button type="button" class="btn btn-danger btn-bs-delete DeleteBoxRow btn-sm"><i class="fa fa-remove"></i></button>
                            </td>
							 <?php } ?>
                        </tr>
                        <?php } ?>
                    <?php }else{ 
                         ?>
                        <tr id="bsrow<?=$i?>">
                            <td><?php echo render_select('approval_heading_id[]',$approval_headings ,array('id','name'),'','',[],[],'','',false);?></td>
                           <td><?php echo render_select('approval_assigned[]',$staffs ,array('staffid',array('firstname','lastname')),'','',[],[],'','',false);?></td>
                            <td><?php echo render_input('approval_remarks[]','','');?></td>
                           
                            <td  contenteditable="false">
                                <button type="button" class="btn btn-danger btn-bs-delete DeleteBoxRow btn-sm"><i class="fa fa-remove"></i></button>
                            </td>
                        </tr>
                  <?php } ?>
								</tbody>
								 <tfoot>
                        <tr>
                            <td colspan="7" align="center">
                                <button type="button" class="btn btn-info add-new mtop15"><i class="fa fa-plus"></i> Add New</button>
                            </td>
                        </tr>
                    </tfoot>
							</table>
						</div>
					</div>
				</div>
				</div>  
        	
  			</div>
			<div class="modal-footer">
				<button class="btn ripple btn-primary" id="approval_save_btn" type="submit">Save</button>
				<button class="btn ripple btn-secondary" data-dismiss="modal" type="button">Close</button>
			</div>
			<?php echo form_close(); ?>
		</div>
	</div>
</div>
		

<script>
$(document).ready(function(){
	 init_selectpicker();
     // Init datepickers
    init_datepicker();
  $('[data-toggle="tooltip"]').tooltip();
  var selectbox = '<?php echo render_select('approval_heading_id[]',$approval_headings,array('id','name'),'','',[],[],'','',false);?>';
 var selectbox1 = '<?php echo render_select('approval_assigned[]',$staffs,array('staffid',array('firstname','lastname')),'','',[],[],'','',false);?>';
  var actions           = '<button type="button" class="btn btn-danger btn-bs-delete DeleteBoxRow"><i class="fa fa-remove"></i></button>';//$("._billing_stages_table tbody td:last-child").html();
  var billing_stage_div = $("._approval_stages_table tbody td:first-child").html();
  // Append table with add row form on add new button click
    $(".add-new").click(function(){
        //$('div.new-categories').append($newdiv);
        //$(this).attr("disabled", "disabled");
        var index = $("._approval_stages_table tbody tr:last-child").index();
        var row   = '<tr id="bsrow'+index+1+'">' +
                        '<td>'+selectbox+'</td>' +
						'<td>'+selectbox1+'</td>' +
                       	'<td><input type="text" class="form-control" name="approval_remarks[]" id="approval_remarks[]"></td>' + 
                      	'<td>' + actions + '</td>' +    
                     '</tr>';
        $("._approval_stages_table tbody").append(row);   
        $("._approval_stages_table tbody tr").eq(index + 1).find(".add, .edit").toggle();
        $('[data-toggle="tooltip"]').tooltip();
        init_selectpicker();
     
        
    });
  // Add row on add button click
  $(document).on("click", ".add", function(){
    var empty = false;
    var input = $(this).parents("tr").find('input[type="text"]');
        input.each(function(){
      if(!$(this).val()){
        $(this).addClass("error");
        empty = true;
      } else{
                $(this).removeClass("error");
            }
    });
    $(this).parents("tr").find(".error").first().focus();
    if(!empty){
      input.each(function(){
        $(this).parent("td").html($(this).val());
      });     
      $(this).parents("tr").find(".add, .edit").toggle();
      $(".add-new").removeAttr("disabled");
    }   
  });
  // Edit row on edit button click
  $(document).on("click", ".edit", function(){    
    $(this).parents("tr").find("td:not(:last-child)").each(function(){
    $(this).html('<input type="text" class="form-control" value="' + $(this).text() + '">');
    });   
    $(this).parents("tr").find(".add, .edit").toggle();
    $(".add-new").attr("disabled", "disabled");
    });
  // Delete row on delete button click
  $(document).on("click", ".DeleteBoxRow", function(){
        $(this).parents("tr").remove();
        $(".add-new").removeAttr("disabled");
    });

    $('#approval_save_btn').click(function() {
        $(this).prop('disabled', true); // Disable the button
        $(this).text('Saving...'); // Optionally, change the button text to indicate progress
        $('#approval-form').submit();
    });
});
$(function() {   
	$('.btn-bs-delete').click(function () { 
      $(this).parents('tr').remove();
   });

   $('#approval-form').submit(function(e) { 
    $('#approval_heading_id').removeAttr('disabled');
    $('#approval_assigned').removeAttr('disabled');
	  //$(this).find(':input').prop('disabled', false);
        e.preventDefault();
       
            var data = $(this).serialize();
			var url =  $(this).attr("action");//"<?php echo admin_url('approval/approvals')?>";
            $.post(url, data).done(function(response) { 
				response = JSON.parse(response);
				// alert(response);
				if(response.success == true){
					alert_float('success', response.message);
					$('#approval_modal').modal('hide');
					//window.location.reload();
				//	alert(response.link);
          window.location.href = admin_url+response.link;
          //window.location.reload()
				}
			/*	if($.fn.DataTable.isDataTable('.table-approval-headings')){
					$('.table-approval-headings').DataTable().ajax.reload();
				}*/
				
			});       
       
    	});

  
});

	
  </script>
			