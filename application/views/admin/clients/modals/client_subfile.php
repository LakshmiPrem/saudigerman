<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Modal Contact -->
<div class="modal fade" id="constitution" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open(admin_url('clients/form_constitution/'.$customer_id.'/'.$contactid),array('id'=>'constitution-form','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?><br /><small class="color-white" id=""><?php echo get_company_name($customer_id,true); ?></small></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-12">
                 
                      
                       <div class="col-md-12 border-right"> 
                         <?php echo form_hidden('contactid',$contactid); ?> 
                        <?php $value=( isset($contact) ? $contact->subject : ''); ?>     
    <?php echo render_input('subject', 'project_discussion_subject',$value , 'text'); ?>
    </div>
     <?php $value =  _d(date('Y-m-d')); ?>

    <div class="col-md-6" <?php if(isset($contact)){?> style="pointer-events: none;"<?php }?>>
        <?php $std11=(isset($contact)? array('readonly'=>'readonly'):array());?>
       <?php $value=( isset($contact) ? _d($contact->issue_date) : _d(date('Y-m-d'))); ?>
       <?php echo render_date_input('issue_date','issue_date',$value,$std11); ?> 
    </div>
   
     <div class="col-md-6">
        <?php $value=( isset($contact) ? _d($contact->expiry_date) : ''); ?>
       <?php echo render_date_input('expiry_date','expiry_date',$value); ?> 
    </div>
      <div class="col-md-12" <?php if(isset($contact)){?> style="pointer-events: none;"<?php }?>>
       <?php $value=( isset($contact) ? $contact->document_type : ''); ?>
         <?php $std11=(isset($contact)? array('readonly'=>'readonly'):array());?>
        <?php
		    echo render_select_with_input_group('document_type',$document_types,array('id','name'),'document_type',$value,'<a href="#" onclick="new_contype(); return false;"><i class="fa fa-plus"></i></a>',$std11);?>
   
    </div>
    <div class="col-md-12 border-right">
     <?php $value=( isset($contact) ? $contact->description : ''); ?>
    <?php echo render_textarea('description', 'project_discussion_description',$value,array('rows'=>'1')); ?>
     </div>
     <div class="col-md-12 border-right">
     
     
         <div class="form-group">
                                <label for="installment_receipt" class="profile-image"><small class="req text-danger">* </small><?php echo _l('upload_document'); ?></label>
                                 <?php $value=( isset($contact) ? $contact->file_name : ''); ?>
                                <input type="file" name="file_name" class="form-control" id="file_name" <?php if($value==''){ echo 'required'; } ?>>
                             </div>
                        <?php if((isset($contact) && $contact->file_name != NULL) ){ ?>
                             <?php 
							   $extension = pathinfo($contact->file_name, PATHINFO_EXTENSION);
							   if($extension=='png'||$extension=='jpg' ||$extension=='jpeg'){?>
                            <div class="img">
                                <?php $path = get_upload_path_by_type('client_file_images').'/'.$contact->userid.'/'; ?>
                               
                                 <img src="<?php echo base_url('uploads/client_file_images/' . $contact->userid . '/' . $contact->file_name); ?>" class="img img-responsive">
                            </div>

                        <?php }else{ ?> 
               <div class="img">
               <a target="_blank" href=<?php echo base_url('uploads/client_file_images/').$contact->userid.'/'.$contact->file_name; ?> download ><i class="fa fa-download"></i></a>
							   </div>  
               <?php }}?>
                
                </div>
                                    
                     

            </div>
        </div>
       </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#constitution-form"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
</div>

