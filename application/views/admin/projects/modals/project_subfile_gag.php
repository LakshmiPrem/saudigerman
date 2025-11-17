<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Miles Stones -->
<div class="modal fade" id="projectsub" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl">
         <?php echo  form_open_multipart(admin_url('projects/project_subfile'),array('id'=>'project-subfile-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                   <!-- <span class="edit-title"><?php echo _l('edit_subfile'); ?></span>-->
                    <span class="add-title"><?php echo _l('add_vakalath'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
  
                              			
       		<div class="col-md-4">	
       		    <?php echo form_hidden('project_id',$project->id); ?>
                            <?php echo form_hidden('client_id',$project->clientid); ?>
                             <?php echo form_hidden('rel_type',$project->case_type);?>
                            <div id="additional_vakalath"></div>
  					 <?php echo render_input('subject', 'project_discussion_subject','' , 'text'); ?>
  					  
       		</div>
       		<div class="col-md-4">
      			                         
       			  <?php
		    echo render_select_with_input_group('document_type',$document_types,array('id','name'),'document_type','','<a href="#"  onclick="new_document_type(); return false;"><i class="fa fa-plus"></i></a>');?>
       		</div>
            <div class="col-md-4">	
       		 <?php $value = _d(date('Y-m-d')); ?>
  				   <?php echo render_date_input('filing_date','filing_date',$value); ?> 
       		</div>	
       			
       		<div class="col-md-4">	
       		 <?php $value = _d(date('Y-m-d')); ?>
  				   <?php echo render_date_input('issue_date','issue_date',$value); ?> 
       		</div>
       		<div class="col-md-4">	
  				<?php echo render_date_input('expiry_date','contract_end_date',''); ?>
       		</div>
     		
         
       		<div class="col-md-8">	
  				 <?php echo render_textarea('description', 'project_discussion_description','',array('rows'=>'1')); ?>
       		</div>
       		
       		 <div class="col-md-6">
                                <label for="installment_receipt" class="profile-image"><?php echo _l('upload_document'); ?></label>
                                <input type="file" name="pop_attachment" class="form-control" id="pop_attachment" >
                                
              </div>
    
   
                </div>
            

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#project-subfile-form"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<!-- Mile stones end -->
