<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo form_open((isset($court_instance) ? admin_url('projects/court_instance/'.$court_instance->id) : admin_url('projects/court_instance')),array('id'=>'court-instance-form')); ?>

<input type="hidden" name="project_id" class="project_id" >

<div class="modal-header">

   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>

   <h4 class="modal-title"><?php echo _l('court_instance'); ?></h4>

</div>

<div class="modal-body">

  <div class="row"> <!-- row2 -->

            <?php ############  Instance Type ########### ?>

            <div class="col-md-4" <?php if(isset($court_instance)){?> style="pointer-events: none;"<?php }?>>  

              <?php $selected = (isset($court_instance) ? $court_instance->instance_id : ''); ?>

              <?php  echo render_select_with_input_group('instance_id',$proejct_instances,array('id','instance_name'),'court_instance',$selected,'<a href="#" onclick="new_court_instance();return false;"><i class="fa fa-plus"></i></a>');?>

            </div>

				<?php ############## stage_requestno ################### ?>

            <div class="col-md-4 border-right ">
              <?php $value = (isset($court_instance) ? $court_instance->stage_requestno : ''); ?>
              <?php echo render_input('stage_requestno','e_request_no',$value); ?>
            </div>
	  		<?php ############## stage_applicationdt ################### ?>

            <div class="col-md-4 border-right ">
              <?php $value = (isset($court_instance) ? _d($court_instance->stage_applicationdt): ''); ?>
              <?php echo render_date_input('stage_applicationdt','application_date',$value); ?>
            </div>

            <?php ############  Case Number ########### ?>

            <div class="col-md-4">

              <?php $value = (isset($court_instance) ? $court_instance->case_number : ''); ?><?php echo render_input('case_number','casediary_casenumber',$value,'text'); ?>

            </div>   
			 <?php ############## stage_registrationdt ################### ?>

            <div class="col-md-4 border-right ">
              <?php $value = (isset($court_instance) ? _d($court_instance->stage_registrationdt): ''); ?>
              <?php echo render_date_input('stage_registrationdt','stage_registrationdt',$value); ?>
            </div>


            <?php ############## Case Nature ################### ?>



            <div class="col-md-4 border-right ">

              <?php $selected = (isset($court_instance) ? $court_instance->instance_casenature : ''); ?>

              <?php  echo render_select_with_input_group('instance_casenature',$case_natures,array('id','name'),'case_nature',$selected,'<a href="#" onclick="new_case_nature();return false;"><i class="fa fa-plus"></i></a>');?>

            </div>

            <?php $position_arr = get_client_positions(); ?>

            <?php #########  Client Position    ###############?>

            <div class="col-md-4 border-right">

            <?php $selected = (isset($court_instance) ? $court_instance->client_position : 'plaintiff'); ?>

            <?php echo render_select('client_position',$position_arr,array('id','name'),'client_position',$selected);?>

          </div>



          <?php #########  Opposite Party Position    ###############?>

          <div class="col-md-4 border-right">

            <?php $selected = (isset($court_instance) ? $court_instance->opposite_party_position : 'defendant'); ?>

            <?php echo render_select('opposite_party_position',$position_arr,array('id','name'),'opposite_party_position',$selected);?>

          </div>

          <div class="col-md-4 border-right">

          <?php ##################### Court  ##############################

            $selected = (isset($court_instance) ? $court_instance->court_id : ''); 

            if(is_admin() ){

             echo render_select_with_input_group('court_id',$courts,array('id','name'),'hearing_court',$selected,'<a href="#" onclick="new_Courts();return false;"><i class="fa fa-plus"></i></a>');

           } else {

            echo render_select('court_id',$courts,array('id','name'),'hearing_court',$selected);

            }?>

          </div>

          <?php ############  Court Fee ########### ?>
          <div class="col-md-4 border-right">
            <?php $value = (isset($court_instance) ? $court_instance->stage_courtfee : ''); ?>
            <?php echo render_input('stage_courtfee','stage_courtfee',$value,'number'); ?>
          </div>
          <div class="col-md-4 border-right ">
              <?php $value = (isset($court_instance) ? $court_instance->stage_courtfeedt: ''); ?>
              <?php echo render_date_input('stage_courtfeedt','date_court_fees_paid',$value); ?>
            </div> 

          

          <?php ##########  Lawyer Attending ########## ?>

                  <div class="col-md-4 border-right">

            <?php 

					   $selected = []; 

              if(isset($court_instance->lawyer_id) &&($court_instance->lawyer_id !='null')){  
				  if(!empty($court_instance->lawyer_id) ){
				  $lawyers=json_decode($court_instance->lawyer_id);

				

                foreach ($lawyers as $value) {

                  $selected[] = $value;

                }
				}

              }

					

            echo render_select('lawyer_id[]',$lawyer_staffs,array('staffid',array('firstname','lastname')),'lawyer_attending',$selected,array('multiple'=>true));?>

          </div>

            



           <?php ############## Opposite Party Lawyer ################### ?>



            <div class="col-md-4 border-right ">

              <?php $value = (isset($court_instance) ? $court_instance->opposite_lawyer : ''); ?>

              <?php echo render_input('opposite_lawyer','opposite_lawyer',$value,'text'); ?>

            </div>

              <?php ############## Legal Coordinator ################### ?>



            <div class="col-md-4 border-right ">

              <?php $value = (isset($court_instance) ? $court_instance->legal_cordinator : ''); ?>

              <?php echo render_select('legal_cordinator',$lawyer_staffs,array('staffid',array('firstname','lastname')),'legal_coordinator',$value);

				//echo render_input('legal_cordinator','legal_coordinator',$value,'text'); ?>

           							

            </div>

              <?php ############  Claim Amount ########### ?>

          <div class="col-md-4 border-right">

            <?php $value = (isset($court_instance) ? $court_instance->claiming_amount : ''); ?>

            <?php echo render_input('claiming_amount','claiming_amount',$value,'number'); ?>

          </div>

             <div class="col-md-4 border-right">

            <?php $value = (isset($court_instance) ? $court_instance->execution_amount : ''); ?>

            <?php echo render_input('execution_amount','judgement_amount',$value,'number'); ?>

          </div>

              <div class="col-md-4 border-right">

            <?php $value = (isset($court_instance) ? $court_instance->account_amount : ''); ?>

            <?php echo render_input('account_amount','account_amount',$value,'number'); ?>

          </div>

              <div class="col-md-4 border-right">

                          <?php $value=(isset($court_instance) ? $court_instance->execution_percent: ''); ?>

                         <?php echo render_input('execution_percent', 'interest_rate',$value,'number'); ?>

                     </div>

               <div class="col-md-4 border-right">

                         <?php $value = (isset($court_instance) ? _d($court_instance->execution_duedate) : ''); ?>

                          <?php echo render_date_input('execution_duedate', 'execution_duedate',$value); ?>

              </div>

              <?php ############## Case Status ################### ?>

		<!--	<div class="col-md-4">	

  				  <?php $position_arr = get_case_statuses(); ?>

       			<?php  $selected = (isset($court_instance) ? $court_instance->case_status : 'not_started'); 

						echo render_select('case_status',$position_arr,array('id','name'),'case_status',$selected);?>

       		</div>-->



          <div class="col-md-12"><hr></div>

    

          <?php  ######### Details of Claim ########### ?>

          <div class="col-md-6 border-right">

            <?php $value = (isset($court_instance) ? $court_instance->details_of_claim : ''); ?>

            <?php echo render_textarea('details_of_claim','details_of_claim',$value,array(),array(),'','tinymce'); ?>

          </div>

          <?php ##########  Case Details ############### ?>

          <div class="col-md-6 ">

           <?php $value = (isset($court_instance) ? $court_instance->case_details : ''); ?>

            <?php echo render_textarea('case_details','casediary_case_details',$value,array(),array(),'','tinymce'); ?>

         <div class="input-group-btn ">

                          <a class="btn btn-default mtop25"  onclick="changeLanguageByButtonClick()">Translate</a>

                        </div>

                         <div class="col-md-12">

                     <input class="hide" value="en" id="language"/>

                      <p class="translate" id="p1" style="visibility: hidden;" ></p>

                      <div id="google_translate_element" style="display:none"></div>

                       <?php $value = (isset($court_instance) ? $court_instance->case_details_en : ''); ?>

                  <?php  echo render_textarea( 'case_details_en', 'case_details_en',$value);?>

                      

                   

                    </div>

          </div>      

        </div><!-- end row2 -->    

         <div class="modal-footer">

                <button type="submit" id="btn_hearing_form" class="btn btn-info pull-right" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#hearing_form"><?php echo _l('submit'); ?></button>

                <button type="button" class="btn btn-default pull-right mright5" data-dismiss="modal"><?php echo _l('close'); ?></button>

                

              

        </div>

 

</div>  

<?php echo form_close(); ?>





<script type="text/javascript">

  var positions = <?php echo json_encode(get_client_positions()) ;?>;



   $('#client_position').change(function(){

      var clientpositn = $('#client_position').val();

      var ctype =  $('#opposite_party_position');

      if(clientpositn == positions[0].id){

         ctype.selectpicker('val',positions[1].id);

         ctype.selectpicker('refresh');

      }else{

         ctype.selectpicker('val',positions[0].id);

         ctype.selectpicker('refresh');

      }



   });

   $('#opposite_party_position').change(function(){

      var clientpositn = $('#opposite_party_position').val();

      var ctype =  $('#client_position');

      if(clientpositn == positions[0].id){

         ctype.selectpicker('val',positions[1].id);

         ctype.selectpicker('refresh');

      }else{

         ctype.selectpicker('val',positions[0].id);

         ctype.selectpicker('refresh');

      }

   });

	$( "#instance_id" ).change(function() {



		 var stid = $('#instance_id').val();

		if(stid=='19'){

			$('#case_number').val('LAP');

		}

});

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



      var src =tinyMCE.get('case_details').getContent();// document.getElementById("executor").value;   

      document.getElementById("p1").innerHTML = src;

		//alert(src);

      //document.getElementById("txt2").value=document.getElementById("p1").innerHTML;

       break;

    }

  }

  setTimeout(function(){

  $('#case_details_en').val( $('#p1').text());

//document.getElementById("executor_translated").value=document.getElementById("p1").innerHTML;

  },1000);

} 

</script>

<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>