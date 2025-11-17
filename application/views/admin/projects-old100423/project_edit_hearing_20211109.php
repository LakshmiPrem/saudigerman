<?php foreach ($hearings as $hearing) { ?>
<div class="edit_hearing"  id="div_hearngForm<?=$hearing->id?>">

   <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tab_content<?=$hearing->id?>" aria-controls="tab_content" role="tab" data-toggle="tab">
              <?php echo _l('hearings').' '._l('details'); ?>
            </a>
          </li>
          <li role="presentation" class="hide">
            <a href="#tab_attachments<?=$hearing->id?>" aria-controls="tab_attachments" role="tab" data-toggle="tab">
              <?php echo _l('contract_attachments'); ?>
            </a>
          </li>
           <li ><a target="_blank" href="<?php echo admin_url('projects/hearing_notice/'.$hearing->id) ?>" class="btn btn-info btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('hearing_notice'); ?>" data-placement="bottom"> <i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;Download Hearing Notice  </a></li>
           <li > <a  class="btn btn-default" data-target="#hearing_send_to_customer" onclick="setHearingId(<?=$hearing->id?>)"  data-toggle="modal" title="<?php echo _l('send_hearing_notice'); ?>"> <i class="fa fa-envelope"></i>&nbsp;&nbsp;<?php echo _l('send_hearing_notice'); ?></a></li>
    </ul>

    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active" id="tab_content<?=$hearing->id?>">

<?php echo form_open(admin_url('projects/hearing/'.$hearing->id),array('id'=>'hearing-edit-form')); ?>

                <div class="row">
                    <div class="col-md-12">
                       
                        
                        <?php echo form_hidden('id',$hearing->id); ?>
                        <?php echo form_hidden('project_id',$project->id); ?>
                        <div class="col-md-4 border-right hide">
                          <?php 
                              $selected = (isset($hearing) ? $hearing->hearing_type : ''); 

                          echo render_select('hearing_type',$hearing_types,array('id','name'),'hearing_type',$selected); ?>
                        </div>
        <div class="col-md-4 border-right">

<?php ####################### Subject ###################################################

    $value = (isset($hearing) ? $hearing->subject : ''); ?>
          <?php echo render_input('subject','hearing_subject',$value,'text'); ?>

        </div>
        <div class="col-md-4 border-right">
  <?php ####################### Date ###################################################
          
            $value = (isset($hearing) ? _d($hearing->hearing_date) : _d(date('Y-m-d'))); ?>
            <?php echo render_datetime_input('hearing_date','hearing_date',$value); ?>
           </div>
         
      <div class="col-md-4 border-right">
  <?php ##################  Postponed Until ###########################################
          
            $value = (isset($hearing->postponed_until) ? _d($hearing->postponed_until) : ''); ?>
            <?php if(isset($hearing)){?>
            <?php echo render_datetime_input('postponed_until','hearing_postponed_until',$value); ?>
          <?php }else{?>
           <?php echo render_datetime_input('postponed_until','hearing_postponed_until',$value,array("disabled"=>"disabled")); ?><?php } ?>
          
      </div>
    
 <div class="col-md-4 border-right">

        <?php ####################### Court Fee ###############################################

        $value = (isset($hearing) ? $hearing->court_fee : ''); ?>
          <?php echo render_input('court_fee','court_fee',$value,'text'); ?>

        </div>

        

 <div class="col-md-4 border-right">

<?php #######################appeal_no ###############################################

        $value = (isset($hearing) ? $hearing->court_no : ''); 
         $label = $hearing_type_tab.'_no'; ?>
          <?php echo render_input('court_no',$label,$value,'text'); ?>

        </div>
      <div class="col-md-4 border-right">
<?php ######################### Hearing Reference #####################################
          $selected = (isset($hearing) ? $hearing->hearing_reference : '');
          if(is_admin() ){
           echo render_select_with_input_group('hearing_reference',$arr_hearinig_references,array('id','name'),'hearing_reference',$selected,'<a href="#" onclick="new_hearingReference();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('hearing_reference',$arr_hearinig_references,array('id','name'),'hearing_reference',$selected);
          }?>
        </div>
    <div class="col-md-4 border-right">
<?php ######################### Court Region ########################################
          $selected = (isset($hearing) ? $hearing->court_region : '');
          if(is_admin() ){
           echo render_select_with_input_group('court_region',$arr_court_regions,array('id','name'),'hearing_court_region',$selected,'<a href="#" onclick="new_court_regions();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('court_region',$arr_court_regions,array('id','name'),'hearing_court_region',$selected);
          }?>
        </div>

 <?php ######## Hall Number ################## ?>
          <div class="col-md-4 border-right">
            <?php $selected = (isset($hearing) ? $hearing->hall_number : '');
            if(is_admin() ){
            echo render_select_with_input_group('hall_number',$hallnumber_types,array('id','name'),'casediary_hallnumber',$selected,'<a href="#" onclick="new_hallnumber();return false;"><i class="fa fa-plus"></i></a>');
            } else {
            echo render_select('hall_number',$hallnumber_types,array('id','name'),'casediary_hallnumber',$selected);
            }?>
          </div>
          

      <?php ##########  Lawyer Attending ########## ?>
          <div class="col-md-4 border-right">
            <?php $selected = (isset($hearing) ? $hearing->lawyer_id : '');
            if($selected == ''){
            $selected = (isset($lawyer_id) ? $lawyer_id : '');
            }
            echo render_select('lawyer_id',$staff,array('staffid',array('firstname','lastname')),'lawyer_attending',$selected);?>
          </div>
          
    <div class="col-md-12"><hr></div>
    <div class="col-md-6 border-right">
 <?php
 ###############################  Comments ##############################################

        $value = (isset($hearing) ? $hearing->comments : ''); ?>
        <?php echo render_textarea('comments','hearing_comments',$value,array('rows'=>5),array(),'','tinymce'); ?>
      </div>

    <div class="col-md-6 border-right">
 <?php
 #######################  {Proceedings} #######################################

        $value = (isset($hearing) ? $hearing->proceedings : ''); ?>
        <?php echo render_textarea('proceedings','proceedings',$value,array('rows'=>5),array(),'','tinymce'); ?>
      </div>


                    </div>
                <hr>    
        


                </div>
            <div class="modal-footer">

                <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#discussion_form"><?php echo _l('update'); ?></button>
               <!--  <button type="button" class="btn btn-default" data-target="#hearing_send_to_customer" onclick="setHearingId(<?=$hearing->id?>)"  data-toggle="modal"><?php echo _l('send_hearing_notice'); ?></button> -->
              
            </div>
        
        <?php echo form_close(); ?>
      </div>
    <div role="tabpanel" class="tab-pane" id="tab_attachments<?=$hearing->id?>">
      <?php ############# Attachment Start ########################## ?>
      <div class="col-md-12">
         <div class="form-group">
        <?php echo form_open_multipart(admin_url('casediary/upload_hearing_attachments/'.$hearing->id.'/'.$project->id),array('class'=>'dropzone','id'=>'hearing-files-upload'));  ?>
            <input type="file" name="file" multiple />
        <?php echo form_close(); ?>

        
      </div>
      </div>
      <div class="col-md-4">
        <div class="form-group">
          <div id="contract_attachments" class="mtop30">
            <?php 
            $data = '<div class="row">';
            foreach($hearing->attachments as $attachment) {
              $href_url = site_url('uploads/projects/'.$attachment['id'].'/');
              if(!empty($attachment['external'])){
                $href_url = $attachment['external_link'];
              }
              $data .= '<div class="display-block contract-attachment-wrapper">';
              $data .= '<div class="col-md-10">';
              $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
              $data .= '<a href="'.$href_url.$attachment['file_name'].'" target="_blank">'.$attachment['file_name'].'</a>';
              $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
              $data .= '</div>';
              $data .= '<div class="col-md-2 text-right">';
              if($attachment['staffid'] == get_staff_user_id() || is_admin()){
               $data .= '<a href="#" class="text-danger" onclick="delete_hearing_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
             }
             $data .= '</div>';
             $data .= '<div class="clearfix"></div><hr/>';
             $data .= '</div>';
           }
           $data .= '</div>';
           echo $data;
           ?>
         </div>
       </div>
      </div> 
    </div>
    <?php ############# Attachment End  ########################### ?>  
  </div>
</div>
<?php } ?>


