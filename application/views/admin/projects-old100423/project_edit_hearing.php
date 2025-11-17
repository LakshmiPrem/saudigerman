<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
 <?php echo form_open((isset($hearing) ? admin_url('projects/hearing/'.$hearing->id) : admin_url('projects/hearing')),array('id'=>'hearing-form')); ?>
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4 class="modal-title">
      <?php if(isset($hearing)){
         $name= '';
         if(!empty($hearing->subject)){
           $name = $hearing->subject;
         } 
         echo '#'._l($hearing->hearing_type) . ' - ' .  $name;
         }else{
           echo _l('add_new',_l('hearings'));
         } 
         ?>
   </h4>
</div>
<div class="modal-body">
  <div class="row">
    <div class="col-md-12">
      <ul class="nav nav-tabs" role="tablist">
        <li role="presentation" class="active">
          <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
            <?php echo _l('hearings').' '._l('details'); ?>
          </a>
        </li>
        <?php if(isset($hearing)){ ?>
        <li role="presentation" class="hide">
          <a href="#tab_attachments<?=$hearing->id?>" aria-controls="tab_attachments" role="tab" data-toggle="tab">
            <?php echo _l('contract_attachments'); ?>
          </a>
        </li>
        <li ><a target="_blank" href="<?php echo admin_url('projects/hearing_notice/'.$hearing->id) ?>" class="btn btn-info btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('hearing_notice'); ?>" data-placement="bottom"> <i class="fa fa-file-pdf-o" style="color:red;"></i>&nbsp;&nbsp;<?php echo _l('download').' '._l('hearing_notice') ?></a></li>
        <li > <a  class="btn btn-default" data-target="#hearing_send_to_customer" onclick="setHearingId(<?=$hearing->id?>)"  data-toggle="modal" title="<?php echo _l('send_hearing_notice'); ?>"> <i class="fa fa-envelope" style="color:green;"></i>&nbsp;&nbsp;<?php echo _l('send_hearing_notice'); ?></a></li>

        <li style="float:right;">
          <button type="submit" id="btn_hearing_form mtop5" class="btn btn-info pull-right" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#hearing_form"><?php echo _l('submit'); ?></button>
        </li>
      <?php } ?>

      </ul> 

      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="tab_content">
         

                <div class="row">
                    <div class="col-md-12"> 
   <div class="col-md-3 border-right" id="div_hearing_project">
            <div class="form-group">
              <?php  $selected = (isset($hearing) ? $hearing->project_id : '');
               echo render_select('project_id',$projects,array('id',array('file_no','name')),'projects',$selected);?>
            </div>
         </div>    

         <div class="col-md-3 border-right">
            <div class="form-group">
              <?php  
                    $selected = (isset($hearing) ? $hearing->h_instance_id : ' '); 
               echo render_select('h_instance_id',$hearing_types,array('id','name'),'hearing_type',$selected);?>
            </div>
         </div>    
                                              
        <div class="col-md-3 border-right">

   <?php ####################### Subject ###################################################

    $value = (isset($hearing) ? $hearing->subject : ''); ?>
          <?php echo render_input('subject','hearing_subject',$value,'text'); ?>

        </div>

        <div class="col-md-3 border-right">

<?php #######################appeal_no ###############################################

        $value = (isset($hearing) ? $hearing->court_no : ''); 
          ?>
          <?php echo render_input('court_no','casediary_casenumber',$value,'text'); ?>

        </div>

          <div class="col-md-3 border-right">
  <?php ####################### Date ###################################################
          
            $value = (isset($hearing) ? _d($hearing->hearing_date) : _d(date('Y-m-d'))); ?>
            <?php echo render_datetime_input('hearing_date','hearing_date',$value); ?>
           </div>
         
      <div class="col-md-3 border-right">
  <?php ##################  Postponed Until ###########################################
          
            $value = (isset($hearing->postponed_until) ? _d($hearing->postponed_until) : ''); ?>
            <?php $attr=array();
            if(isset($hearing)){
              if($value == ' ')
              $attr = array("disabled"=>"disabled");
              ?>
            <?php echo render_datetime_input('postponed_until','hearing_postponed_until',$value,$attr); ?>
          <?php }else{?>
           <?php echo render_datetime_input('postponed_until','hearing_postponed_until',$value,array("disabled"=>"disabled")); ?><?php } ?>
          
      </div>
    
        <div class="col-md-3 border-right">
        <?php ####################### Court Fee################################

        $value = (isset($hearing) ? $hearing->court_fee : ''); ?>
          <?php echo render_input('court_fee','court_fee',$value,'text'); ?>

        </div>
       <!--  <div class="col-md-3 border-right">
          <?php ##################### Court  ##############################
            $selected = (isset($hearing) ? $hearing->hearing_court : '');
             if(is_admin() ){
             echo render_select_with_input_group('hearing_court',$courts,array('id','name'),'hearing_court',$selected,'<a href="#" onclick="new_Courts();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('hearing_court',$courts,array('id','name'),'hearing_court',$selected);
            }?>
          </div> -->
      <div class="col-md-3 border-right">
<?php ######################### Hearing Reference #####################################
          $selected = (isset($hearing) ? $hearing->hearing_reference : '');
          if(is_admin() ){
           echo render_select_with_input_group('hearing_reference',$arr_hearinig_references,array('id','name'),'hearing_reference',$selected,'<a href="#" onclick="new_hearingReference();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('hearing_reference',$arr_hearinig_references,array('id','name'),'hearing_reference',$selected);
          }?>
        </div>
    <div class="col-md-3 border-right">
<?php ######################### Court Region ########################################
          $selected = (isset($hearing) ? $hearing->court_region : '');
          if(is_admin() ){
           echo render_select_with_input_group('court_region',$arr_court_regions,array('id','name'),'hearing_court_region',$selected,'<a href="#" onclick="new_court_regions();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('court_region',$arr_court_regions,array('id','name'),'hearing_court_region',$selected);
          }?>
        </div>

 <?php ######## Hall Number ################## ?>
          <div class="col-md-3 border-right">
            <?php $selected = (isset($hearing) ? $hearing->hall_number : '');
            if(is_admin() ){
            echo render_select_with_input_group('hall_number',$hallnumber_types,array('id','name'),'casediary_hallnumber',$selected,'<a href="#" onclick="new_hallnumber();return false;"><i class="fa fa-plus"></i></a>');
            } else {
            echo render_select('hall_number',$hallnumber_types,array('id','name'),'casediary_hallnumber',$selected);
            }?>
          </div>
          

      <?php ##########  Lawyer Attending ########## ?>
          <div class="col-md-3 border-right">
            <?php $selected = (isset($hearing) ? $hearing->lawyer_id : '');
            if($selected == ''){
            $selected = (isset($lawyer_id) ? $lawyer_id : '');
            }
            echo render_select('lawyer_id',$lawyer_staffs,array('staffid',array('firstname','lastname')),'lawyer_attending',$selected);?>
          </div>
          
    <div class="col-md-12"></div>
    <div class="col-md-6 border-right">
      <p class="bold"><?php echo _l('hearing_comments'); ?></p>
 <?php
 ###############################  Comments ##############################################

        $value = (isset($hearing) ? $hearing->comments : ''); ?>
       <?php echo render_textarea('comments','',$value,array(),array(),'','tinymce'); ?>

        
      </div>

    <div class="col-md-6 border-right">
          <p class="bold"><?php echo _l('proceedings'); ?></p>

 <?php
 #######################  {Proceedings} #######################################

        $value = (isset($hearing) ? $hearing->proceedings : ''); ?>
        <?php echo render_textarea('proceedings','',$value,array(),array(),'','tinymce'); ?>
              <div class="input-group-btn ">
                          <a class="btn btn-default mtop25"  onclick="changeLanguageByButtonClick()">Translate</a>
                        </div>
                           </div> 
                         <div class="col-md-12">
                     <input class="hide" value="en" id="language"/>
                      <p class="translate" id="p1" style="visibility: hidden;" ></p>
                      <div id="google_translate_element" style="display:none"></div>
                     
                  <?php  echo render_textarea( 'proceeding_en', 'proceedings_en',$value);?>
                      
                   
                    </div>
       

                    </div>
                <hr>    
      
                </div>
            <div class="modal-footer">
                <button type="submit" id="btn_hearing_form" class="btn btn-info pull-right" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#hearing_form"><?php echo _l('submit'); ?></button>
                <button type="button" class="btn btn-default pull-right mright5" data-dismiss="modal"><?php echo _l('close'); ?></button>
                
              
            </div>
        
       
        </div>
      </div>

    </div>
  </div>
</div>  
 <?php echo form_close(); ?>
<script type="text/javascript">
 <?php if(!isset($hearing)){ ?>
 $(function() {
  get_casedetails_table_data_ajax();
 });
 <?php } ?> 
  $('#hearing-form select[name="h_instance_id"]').change(function(){
    get_casedetails_table_data_ajax();
  });

  function get_casedetails_table_data_ajax() {
    var h_type = $('#hearing-form select[name="h_instance_id"]').val();
    var selectedprojectid = $('#hearing-form select[name="project_id"]').val();
    if(selectedprojectid > 0 && h_type != ''){
      requestGetJSON('casediary/get_casedetails_table_data_ajax/' + selectedprojectid + '/' + h_type).done(function(response) {
        if(response){
          var $hearingForm = $('#hearing-form');
          $hearingForm.find('#h_client_position').selectpicker('val',response.client_position);
          $hearingForm.find('#court_no').val(response.case_number);
          $hearingForm.find('#h_oppositeparty_position').selectpicker('val',response.opposite_party_position);
          $hearingForm.find('#h_oppositeparty_position').selectpicker('val',response.opposite_party_position);
          $hearingForm.find('#hearing_court').selectpicker('val',response.court_id);
          $hearingForm.find('#lawyer_id').selectpicker('val',response.lawyer_id);
          $hearingForm.find('#h_casenature_id').selectpicker('val',response.instance_casenature);
        }
       
      });
    }
    
  }
</script>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: "ar"}, 'google_translate_element');
}

function changeLanguageByButtonClick() {

  ///var language = document.getElementById("language").value;
  var selectField = document.querySelector("#google_translate_element select");
  for(var i=0; i < selectField.children.length; i++){
    var option = selectField.children[i];
    // find desired langauge and change the former language of the hidden selection-field 
    if(option.value=='en'){
       selectField.selectedIndex = i;
       // trigger change event afterwards to make google-lib translate this side
       selectField.dispatchEvent(new Event('change'));
		// Get the HTML contents of the currently active editor
tinyMCE.activeEditor.getContent();

// Get the raw contents of the currently active editor
tinyMCE.activeEditor.getContent({format : 'raw'});

// Get content of a specific editor:

      var src =tinyMCE.get('proceedings').getContent();// document.getElementById("executor").value;   
      document.getElementById("p1").innerHTML = src;
		//alert(src);
      //document.getElementById("txt2").value=document.getElementById("p1").innerHTML;
       break;
    }
  }
  setTimeout(function(){
  $('#proceeding_en').val( $('#p1').text());
//document.getElementById("executor_translated").value=document.getElementById("p1").innerHTML;
  },1000);
} 
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
 