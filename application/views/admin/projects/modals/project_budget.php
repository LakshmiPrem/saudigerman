<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Miles Stones -->
<div class="modal fade" id="projectbudget" tabindex="-1" role="dialog">
    <div class="modal-dialog">
         <?php echo  form_open(admin_url('projects/project_budget'),array('id'=>'project-budget-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                   <!-- <span class="edit-title"><?php echo _l('edit_subfile'); ?></span>-->
                    <span class="add-title"><?php echo _l('add_budget'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
  
                              			
       		<div class="col-md-3 hide">	
       		    <?php echo form_hidden('project_id',$project->id); ?>
                          
                           
                            <div id="additional_budget1"></div>
  					<?php //echo render_select('budget_taskid',$templatetasks,array('id','name'),'tasks','50');?>
  					  
       		</div>
			<div class="col-md-3 hide"><?php echo render_input('budget_preregister','exp_preregister','0','number'); ?></div>
               <div class="col-md-3 hide"><?php echo render_input('budget_consent','exp_consent','0','number'); ?> </div>
				<div class="col-md-3 hide"><?php echo render_input('budget_valuation','exp_valuation','0','number'); ?> </div>
			<div class="col-md-3 hide"><?php echo render_input('budget_registration','exp_registration','0','number'); ?> </div>
			<div class="col-md-3 hide"><?php echo render_input('budget_disbursement','exp_disbursement','0','number'); ?> </div>
       		<div class="col-md-6"><?php echo render_input('budget_amount','expense_add_edit_amount','','number'); ?> </div>
					<div class="col-md-6">	
       		 <?php $value = _d(date('Y-m-d')); ?>
  				   <?php echo render_date_input('budget_date','budget_add_edit_date',$value); ?> 
       		</div>
			<div id="revapprove">
       		 <div class="col-md-6">
                      <?php
                          
                echo render_select('budget_reviewer_id',$staff,array('staffid',array('firstname','lastname')),'reviewer_name',' ');
               ?>
                </div>
                     <div class="col-md-6">
                      <?php
              //  $selected = (isset($expense) ? $expense->approvalid : '');
               
                echo render_select('budget_approvalid',$staff,array('staffid',array('firstname','lastname')),'approval_name',' ');
               ?>
					</div>
					</div>
       		
       	     		
         
       		<div class="col-md-12">	
  				 <?php echo render_textarea('budget_description', 'project_discussion_description','',array('rows'=>'4')); ?>
       		</div>
       		
       	
    
   
                </div>
            

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#project-budget-form"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mile stones end -->

<script>
 appValidateForm($('#project-budget-form'), {
         // budget_taskid: 'required',
         budget_description: 'required',
         budget_approvalid:'required',
         budget_reviewer_id:'required',
         budget_date:'required'
       
      }, projectbudgetSubmitHandler);
function projectbudgetSubmitHandler(form) {

      $.post(form.action, $(form).serialize()).done(function(response) {

          response = JSON.parse(response);

		  if (response.id) {
  		if (response.success == true) {
           	 alert_float('success', response.message);
				
        }

               $('#project-budget-form').find('button[type="submit"]').button('reset');  
		$('#projectbudget').modal('hide');

              window.location.assign(response.url);

          }

      });

      return false;

  }
/* project budget */

function new_projectbudget() {
	
      $('#projectbudget').modal('show');
	$('#projectbudget .edit-title').addClass('hide');
  }
function edit_projectbudget(invoker,id){
	 
    var preregister = $(invoker).data('preregister');
    var consent = $(invoker).data('consent');
    var valuation = $(invoker).data('valuation');
    var register = $(invoker).data('registration');
    var disbursement = $(invoker).data('disbursement');
    var amount = $(invoker).data('amount');
    var bdate = $(invoker).data('bdate');
    var bdesc = $(invoker).data('bdesc');
    var review = $(invoker).data('review');
    var approve = $(invoker).data('approve');
	
//	var category = $(invoker).data('category');
	var type = $(invoker).data('type');
    $('#additional_budget1').append(hidden_input('id',id));
    $('#projectbudget input[name="budget_preregister"]').val(preregister);
    $('#projectbudget input[name="budget_consent"]').val(consent);
    $('#projectbudget input[name="budget_valuation"]').val(valuation);
    $('#projectbudget input[name="budget_registration"]').val(register);
    $('#projectbudget input[name="budget_disbursement"]').val(disbursement);
    $('#projectbudget input[name="budget_amount"]').val(amount);
    $('#projectbudget input[name="budget_date"]').val(bdate);
    $('#projectbudget select[name="budget_reviewer_id"]').selectpicker('val',review);
    $('#projectbudget select[name="budget_approvalid"]').selectpicker('val',approve);
    $('#projectbudget textarea[name="budget_description"]').val(bdesc);
//	$('#category').selectpicker('val',category);
	 $('#projectbudget').find('#revapprove').hide();
   $('#projectbudget').modal('show');
	$('#projectbudget .add-title').addClass('hide');
   
}
</script>
