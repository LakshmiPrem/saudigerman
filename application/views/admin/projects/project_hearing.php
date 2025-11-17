<!-- Miles Stones -->
<?php echo form_open(admin_url('projects/hearing/'),array('id'=>'hearing-form')); ?>

      <div class="row">
        <div class="col-md-12">              
        <?php echo form_hidden('project_id',$project->id); ?>
        <?php echo form_hidden('hearing_type','first_instance'); ?>
         
        <div class="col-md-4 border-right">
  <?php ####################### Subject #####################################

          $value = (isset($hearing) ? $hearing->subject : ''); ?>
          <?php echo render_input('subject','hearing_subject',$value,'text'); ?>
        </div>
        <div class="col-md-4 border-right">
  <?php ####################### Date ###################################
          
            $value = (isset($hearing) ? _d($hearing->hearing_date) : _d(date('Y-m-d H:i:s'))); ?>
            <?php echo render_datetime_input('hearing_date','hearing_date',$value); ?>
           </div>
         
      <div class="col-md-4 border-right">
  <?php ##################  Postponed Until #################################
          
            $value = (isset($hearing->postponed_until) ? _d($hearing->postponed_until) : ''); ?>
            <?php if(isset($hearing)){?>
            <?php echo render_datetime_input('postponed_until','hearing_postponed_until',$value); ?>
          <?php }else{?>
           <?php echo render_datetime_input('postponed_until','hearing_postponed_until',$value,array("disabled"=>"disabled")); ?><?php } ?>
          
      </div>

      <div class="col-md-4 border-right">
<?php ####################### Court Fee ##################################
        $value = (isset($hearing) ? $hearing->court_fee : ''); ?>
          <?php echo render_input('court_fee','court_fee',$value,'text'); ?>

      </div>
      <div class="col-md-4 border-right">
<?php #######################appeal_no ###############################
        $value = (isset($hearing) ? $hearing->court_no : ''); 
        $label = 's';//$hearing_type_tab.'_no'; ?>
          <?php echo render_input('court_no',$label,$value,'text'); ?>

      </div>

      <div class="col-md-4 border-right">
<?php ############### Hearing Reference ############################
          $selected = (isset($hearing) ? $hearing->hearing_reference : '');
          if(is_admin() ){
           echo render_select_with_input_group('hearing_reference',$arr_hearinig_references,array('id','name'),'hearing_reference',$selected,'<a href="#" onclick="new_hearingReference();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('hearing_reference',$arr_hearinig_references,array('id','name'),'hearing_reference',$selected);
          }?>
      </div>
      <div class="col-md-4 border-right">
<?php ####################### Court Region ###############################
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
 <?php ####################  Comments ##############################

        $value = (isset($hearing) ? $hearing->comments : ''); ?>
        <?php echo render_textarea('comments','hearing_comments',$value,array(),array(),'','tinymce'); ?>
    </div>

    <div class="col-md-6 border-right">
        <?php ########################  Proceedings ##########################

        $value = (isset($hearing) ? $hearing->proceedings : ''); ?>
        <?php echo render_textarea('proceedings','proceedings',$value,array(),array(),'','tinymce'); ?>
    </div>
 
  </div>
</div>
<div class="modal-footer">
    <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#discussion_form"><?php echo _l('submit'); ?></button>
</div>
        
<?php echo form_close(); ?>


