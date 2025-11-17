             <div class="row ">
                   <div class="col-md-12">
                   <?php echo form_open_multipart('admin/tickets/editcreditapp/'.$creditdoc->id,array('id'=>'creditdoc-form','autocomplete'=>'off')); ?>
                    <div class="col-md-12">
                         <!-- // For email exist check -->
                        <?php echo form_hidden('id',$creditdoc->id); ?>
                          <?php echo form_hidden('ticketid',$creditdoc->ticketid); ?>
                        <label for="assigned"> <?php echo _l('document_type'); ?></label>
                        <select class="form-control" name="document_type">
                     <option></option>
                     <?php foreach($document_types as $doc_type){ ?>
                        <option value="<?=$doc_type['id']?>" <?php if($doc_type['id']==$creditdoc->document_type) echo 'selected';?> ><?=$doc_type['name']?></option>
                     <?php } ?>
                  </select>
                        <?php $value=( isset($creditdoc) ? $creditdoc->document_number : ''); ?>
                        <?php echo render_input( 'document_number', 'document_number',$value); ?>
                         <?php $value=( isset($creditdoc) ? $creditdoc->document_name : ''); ?>
                        <?php echo render_input( 'document_name', 'document_name',$value); ?>
                         <label for="assigned"> <?php echo _l('nationality'); ?></label>
                        <select class="form-control" name="nationality">
                     <option></option>
                     <?php foreach($nationality as $nat_type){ ?>
                        <option value="<?=$nat_type['country_id']?>" <?php if($nat_type['country_id']==$creditdoc->nationality) echo 'selected';?> ><?=$nat_type['short_name']?></option>
                     <?php } ?>
                  </select>
                       
                        <label for="assigned"> <?php echo _l('expiry_date'); ?></label>
                        <?php $value=( isset($creditdoc) ? $creditdoc->expiry_date : ''); ?>
                       <input type="date" name="expiry_date" placeholder="Expiry Date" class="form-control" value="<?=$value?>"  />
                       
                        <label for="assigned"> <?php echo _l('ticket_add_attachments'); ?></label>
                        <input type="file" extension="<?php echo str_replace(['.', ' '], '', get_option('ticket_attachments_file_extensions')); ?>" filesize="<?php echo file_upload_max_size(); ?>" class="form-control" name="crattachments"  accept="<?php echo get_ticket_form_accepted_mimes(); ?>">
                      
            </div>
       <div class="row">
         <div class="clearfix"></div>
                    <br>
         <div class="col-md-12 text-center  mtop30">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#contact-form"><?php echo _l('submit'); ?></button>
    </div>
    </div>
     <?php echo form_close(); ?>
				 </div>
      </div>
    

