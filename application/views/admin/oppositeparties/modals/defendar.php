<!-- Modal Contact -->
<div class="modal fade" id="contact" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <?php echo form_open('admin/opposite_parties/defendar/'.$opposite_id.'/'.$contactid,array('id'=>'contact-form1','autocomplete'=>'off')); ?>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel"><?php echo $title; ?><br /><small id="" style="color: white;"><?php echo get_party_name($opposite_id,true); ?></small></h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6">
                        
                        <!-- // For email exist check -->
                        <?php echo form_hidden('contactid',$contactid); ?>
                         <?php $selected=( isset($contact) ? $contact->party_type : ''); ?>
                               
           			<?php echo render_select('party_type',$contact_type,array('id','name'),'party_type',$selected);?>
                       </div>
                         <div class="col-md-6">  
                        <?php $value=( isset($contact) ? $contact->contact_name : ''); ?>
                       
                        <?php echo render_input( 'contact_name', 'contact_name',$value); ?>
                         </div>
                         <div class="col-md-6">
                         <?php $selected=( isset($contact) ? $contact->nationality : ''); ?>
                          
                              
           			<?php echo render_select('nationality',$nationality,array('id','nation'),'nationality',$selected);?>
                      </div>
                         <div class="col-md-6">
                       <?php $value=( isset($contact) ? $contact->designation : ''); ?>
                        <?php echo render_input( 'designation', 'designation',$value); ?>
                        </div>
                         <div class="col-md-6">
                        <?php $value=( isset($contact) ? $contact->contactno : ''); ?>
                        <?php echo render_input( 'contactno', 'contactno',$value); ?>
					</div>
                        <div class="col-md-6">
                        <?php $value=( isset($contact) ? $contact->emailid : ''); ?>
                        <?php echo render_input('emailid', 'emailid',$value); ?>
					</div>
                         <div class="col-md-6">
                        <?php $value=( isset($contact) ? $contact->idnumber : ''); ?>
                        
                        <?php echo render_input( 'idnumber', 'idnumber',$value); ?>
						</div>
                         <div class="col-md-6">
                        <?php $value=( isset($contact) ? $contact->id_expiry  : ''); ?>
                        <?php echo render_date_input('id_expiry', 'id_expiry',$value); ?>
                        </div>
                         <div class="col-md-6">
                         <?php $value=( isset($contact) ? $contact->passportno : ''); ?>
                         <?php echo render_input('passportno', 'passportno',$value); ?>
                         </div>
                         <div class="col-md-6">
                        <?php $value=( isset($contact) ? $contact->passport_expiry  : ''); ?>
                        <?php echo render_date_input('passport_expiry', 'passport_expiry',$value); ?>
						</div>
						 <div class="col-md-12">
						 <?php $value=( isset($contact) ? $contact->home_contact : ''); ?>
                        <?php echo render_input( 'home_contact', 'home_contact',$value); ?>
           				<?php $value=( isset($contact) ? $contact->home_address : ''); ?>
                    <?php echo render_textarea( 'home_address','home_address',$value); ?>

             
            </div>
        </div>
    </div>
    <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
        <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" autocomplete="off" data-form="#contact-form"><?php echo _l('submit'); ?></button>
    </div>
    <?php echo form_close(); ?>
</div>
</div>
</div>
