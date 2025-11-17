<?php if($project->case_type == 'chequebounce'){ ?>
<div class="content">
  <div class="row">
    <div class="panel_s">
      <div class="panel-body">
<?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>
     		<div class="row"> <!-- row2 -->
   				<?php ############  Case Number ########### ?>
  				<div class="col-md-4 border-right ">
					    <?php  $value = (isset($project) ? $project->file_no : ''); ?>
					    <?php echo render_input('file_no','casediary_file_no',$value,'text'); ?>
				  </div> 

       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? _d($project->cheque_date) : ''); ?>
       				<?php echo render_date_input('cheque_date','cheque_date',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? $project->cheque_no : ''); ?>
       				<?php echo render_input('cheque_no','cheque_no',$value,'text'); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? _d($project->cheque_issue_date) : ''); ?>
       				<?php echo render_date_input('cheque_issue_date','cheque_issue_date',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? _d($project->cheque_due_date) : ''); ?>
       				<?php echo render_date_input('cheque_due_date','due_date',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? $project->cheque_amount : ''); ?>
       				<?php echo render_input('cheque_amount','cheque_amount',$value,'text'); ?>
       		</div>

       		<div class="col-md-4">	
  				  <?php $position_arr = get_approval_statuses(); ?>
       			<?php  $selected = (isset($project) ? $project->approval_status : ' ');
						echo render_select('approval_status',$position_arr,array('id','name'),'approval_status',$selected);?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? $project->cheque_status : ''); ?>
       				<?php echo render_input('cheque_status','cheque_status',$value,'text'); ?>
       		</div>

       		<div class="col-md-8 border-right">
						<?php $value = (isset($project) ? $project->remarks : ''); ?>
						<?php echo render_textarea('remarks','remarks',$value,array(),array(),'',''); ?>
					</div>


        </div>
        <hr>
				<button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
			
<?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>


<?php }else{ ?>	

<div class="panel-group" id="accordion">

  <div class="panel-heading">
      <h4 class="panel-title">
        <a class="btn btn-info accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#create_instance" >
           <?=ucwords(_l('create_instance'))?>
        </a>
      </h4>
  </div> 
  <div id="create_instance" class="panel-collapse collapse in">
    <div class="panel-body">
      <?php echo form_open(admin_url('projects/save_court_instance/'.$project->id),array('id'=>'case-form')); ?>
          <div class="row"> <!-- row2 -->
            <?php ############  Instance Type ########### ?>
            <div class="col-md-4">  
                <?php  echo render_select_with_input_group('details_type',$proejct_instances,array('instance_slug','instance_name'),'court_instance','','<a href="#" onclick="new_court_instance();return false;"><i class="fa fa-plus"></i></a>');?>
            </div>  
            <?php ############  Case Number ########### ?>
            <div class="col-md-4">  
                <?php echo render_input('case_number','casediary_casenumber','','text'); ?>
            </div>  
            <?php ############## Case Nature ################### ?>

            <div class="col-md-4 border-right ">
              <?php  echo render_select_with_input_group('casenature',$case_natures,array('id','name'),'case_nature','','<a href="#" onclick="new_case_nature();return false;"><i class="fa fa-plus"></i></a>');?>
            </div>
            <?php $position_arr = get_client_positions(); ?>
            <?php #########  Client Position    ###############?>
            <div class="col-md-4 border-right">
            <?php echo render_select('client_position',$position_arr,array('id','name'),'client_position');?>
          </div>

           <?php #########  Opposite Party Position    ###############?>
            <div class="col-md-4 border-right">
            <?php echo render_select('opposite_party_position',$position_arr,array('id','name'),'opposite_party_position');?>
          </div>


          <div class="col-md-4 border-right">
          <?php ##################### Court  ##############################
            $selected =  '';
            if(is_admin() ){
             echo render_select_with_input_group('court_id',$courts,array('id','name'),'hearing_court',$selected,'<a href="#" onclick="new_Courts();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('court_id',$courts,array('id','name'),'hearing_court',$selected);
            }?>
          </div>
          <?php ############  Claim Amount ########### ?>
          <div class="col-md-4 border-right">
            <?php  $value =''; ?>
            <?php echo render_input('instance_claiming_amount','claiming_amount',$value,'number'); ?>
          </div>
          
          <?php ##########  Lawyer Attending ########## ?>
          <div class="col-md-4 border-right">
            <?php 
            echo render_select('lawyer_id[]',$lawyer_staffs,array('staffid',array('firstname','lastname')),'lawyer_attending','',array('multiple'=>true));?>
          </div>

           <?php ############## Opposite Party Lawyer ################### ?>

            <div class="col-md-4 border-right ">
              <?php  $value = ''; ?>
              <?php echo render_input('opposite_lawyer','opposite_lawyer',$value,'text'); ?>
            </div>

           <div class="col-md-4 ">
            <?php  $value =  ''; ?>
            <?php echo render_input('business_responsible','referred_by',$value,'text'); ?>
          </div>


          <div class="col-md-12"><hr></div>
    
          <?php  ######### Details of Claim ########### ?>
          <div class="col-md-5 border-right">
            <?php $value = ''; ?>
            <?php echo render_textarea('details_of_claim','details_of_claim',$value,array(),array(),'','tinymce'); ?>
          </div>
          <?php ##########  Case Details ############### ?>
          <div class="col-md-7 ">
            <?php $value =  ''; ?>
            <?php echo render_textarea('case_details','casediary_case_details',$value,array(),array(),'','tinymce'); ?>
            <div class="input-group-btn ">
                          <a class="btn btn-default mtop25"  onclick="changeLanguageByButtonClick()">Translate</a>
                        </div>
                        <div class="col-md-12">
                     <input class="hide" value="en" id="language"/>
                      <p class="translate" id="p1" style="visibility: hidden;" ></p>
                      <div id="google_translate_element" style="display:none"></div>
                       <?php $value = (isset($first_instance_row) ? $first_instance_row->case_details_en : ''); ?>
                  <?php  echo render_textarea( 'case_details_en', 'case_details_en',$value);?>
                      
                   
                    </div>
          </div>      
        </div><!-- end row2 -->    
        <hr>
        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
 
<?php echo form_close(); ?>
        </div>
      </div>
 </div>
 <?php  foreach ($court_instances as $project_instance) {  ?>
   <div class="panel panel-default" style="margin-bottom: 7px;">
   
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#<?=$project_instance['instance_slug'].$project_instance['id']?>">
           <?=ucfirst($project_instance['instance_name'])?>
        </a>
      </h4>
    </div> 
    <div id="<?=$project_instance['instance_slug'].$project_instance['id']?>" class="panel-collapse collapse ">
      <div class="panel-body">
        <?php echo form_open(admin_url('projects/save_court_instance/'.$project->id),array('id'=>'case-form')); 
          if(isset($project_instance['id'])){ 
            echo form_hidden('id',$project_instance['id']);
          }
        ?>
          <div class="row"> <!-- row2 -->

              <?php ############  Instance Type ########### ?>
            <div class="col-md-4">  
              <?php $selected = (isset($project_instance) ? $project_instance['details_type'] : ''); ?>
              <?php echo render_select('details_type',$proejct_instances,array('instance_slug','instance_name'),'court_instance',$selected);?>
            </div>  

            <?php ############  Case Number ########### ?>
             <div class="col-md-4">  
              <?php $value = (isset($project_instance) ? $project_instance['case_number'] : ''); ?>
                <?php echo render_input('case_number','casediary_casenumber',$value,'text'); ?>
              </div>  


            <?php ############## Case Nature ################### ?>

              <div class="col-md-4 border-right ">
              <?php  $selected = (isset($project_instance) ? $project_instance['instance_casenature'] : ''); ?>
              <?php  echo render_select_with_input_group('casenature',$case_natures,array('id','name'),'case_nature',$selected,'<a href="#" onclick="new_case_nature();return false;"><i class="fa fa-plus"></i></a>');?>
            </div>
            <?php $position_arr = get_client_positions(); ?>
            <?php #########  Client Position    ###############?>
            <div class="col-md-4 border-right">
            
           <?php  $selected = (isset($project_instance) ? $project_instance['client_position'] : ''); 
            echo render_select('client_position',$position_arr,array('id','name'),'client_position',$selected);?>
          </div>

           <?php #########  Opposite Party Position    ###############?>
            <div class="col-md-4 border-right">
            
             <?php  $selected = (isset($project_instance) ? $project_instance['opposite_party_position'] : ''); 
            echo render_select('opposite_party_position',$position_arr,array('id','name'),'opposite_party_position',$selected);?>
          </div>


          <div class="col-md-4 border-right">

          <?php ##################### Court  ##############################?>
            <?php  $selected = (isset($project_instance) ? $project_instance['court_id'] : ''); 
            if(is_admin() ){
             echo render_select_with_input_group('court_id',$courts,array('id','name'),'hearing_court',$selected,'<a href="#" onclick="new_Courts();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('court_id',$courts,array('id','name'),'hearing_court',$selected);
            }?>
              </div>

          
              <?php ############  Claim Amount ########### ?>
          <div class="col-md-4 border-right">
              <?php $value = (isset($project_instance) ? $project_instance['instance_claiming_amount'] : ''); ?>
            <?php echo render_input('instance_claiming_amount','claiming_amount',$value,'text'); ?>
          </div>
          
          <?php ##########  Lawyer Attending ########## ?>
          <div class="col-md-4 border-right">
            <?php 
            $selected = []; 
              if(isset($project_instance['lawyers_assigned'])){  
                foreach ($project_instance['lawyers_assigned'] as $value) {
                  $selected[] = $value['assigneeid'];
                }
              }
            echo render_select('lawyer_id[]',$lawyer_staffs,array('staffid',array('firstname','lastname')),'lawyer_attending',$selected,array('multiple'=>true));?>
          </div>

           <?php ############## Opposite Party Lawyer ################### ?>

            <div class="col-md-4 border-right ">
              <?php $value = (isset($project_instance) ? $project_instance['opposite_lawyer'] : ''); ?>
              <?php echo render_input('opposite_lawyer','opposite_lawyer',$value,'text'); ?>
            </div>

           <div class="col-md-4 ">
						<?php $value = (isset($project_instance) ? $project_instance['business_responsible'] : ''); ?>
						<?php echo render_input('business_responsible','referred_by',$value,'text'); ?>
					</div>


          <div class="col-md-12"><hr></div>
    
          <?php  ######### Details of Claim ########### ?>
          <div class="col-md-5 border-right">
            <?php $value = (isset($project_instance) ? $project_instance['details_of_claim'] : ''); ?>
            <?php echo render_textarea('details_of_claim','details_of_claim',$value,array(),array(),'','tinymce'); ?>
          </div>
          <?php ##########  Case Details ############### ?>
          <div class="col-md-7 ">
            <?php $value = (isset($project_instance) ? $project_instance['case_details'] : ''); ?>
            <?php echo render_textarea('case_details','casediary_case_details',$value,array(),array(),'','tinymce'); ?>
          </div>      
        </div><!-- end row2 -->    
        <hr>
        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>

        <?php 
     $num_rows = total_rows('tblhearings',array('project_id'=>$project->id,'hearing_type'=>'first_instance'));
    if($num_rows > 0){ 
      
    ?>
       <a class="btn btn-default mleft20 mtop3" data-toggle="collapse" href="#<?=$project_instance['details_type']?>" role="button" aria-expanded="false" aria-controls="collapseExample" onclick="hearingTable('<?=$project_instance['details_type']?>',<?=$project->id?>)">
            <?php echo $project_instance['instance_name'].' '._l('hearings'); ?>
          </a>
          <hr>
    <?php } ?>      
<?php echo form_close(); ?>
        
<?php if($num_rows > 0){ ?>
        <div class="collapse" id="<?=$project_instance['details_type']?>">
          <div class="card card-body">
            <table class="table dt-table scroll-responsive table-<?=$project_instance['details_type']?>-hearings" data-order-col="1" data-order-type="asc">
              <thead>
                <tr> 
                  <th><?php echo _l('hearing_date'); ?></th>
                  <th><?php echo _l('hearing_list_subject'); ?></th>
                  <th><?php echo _l('client'); ?></th>
                  <th><?php echo _l('court_fee'); ?></th>
                  <th><?php echo _l('casediary_casenumber'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                  <th><?php echo _l('court_decision'); ?></th>
                </tr>
              </thead>
              <tbody>

              </tbody>
            </table>
         
          </div>
        </div>

 <?php } ?>
      </div>


    </div>
</div>
  <?php } ?>
  



  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#close_case">
           <?=ucwords(_l('close_case'))?>
        </a>
      </h4>
    </div>
    <div id="close_case" class="panel-collapse collapse ">
      <div class="panel-body">
         <?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>

         
         <?php $final_sts_arr = get_case_final_statuses(); ?>
            <?php #########  Client Position    ###############?>
            <div class="col-md-4 border-right">
            
            <?php  $selected = (isset($project) ? $project->final_status : '');
            echo render_select('final_status',$final_sts_arr,array('id','name'),'final_status',$selected);?>
          </div>

          <div class="col-md-4 border-right ">
           <?php $value = (isset($project) ? _d($project->closed_date) : _d(date('Y-m-d'))); ?>
            <?php echo render_datetime_input('closed_date','closed_date',$value); ?>
          </div>

          <div class="col-md-4 border-right">
            <?php $value = (isset($project) ? $project->reason : ''); ?>
            <?php echo render_textarea('reason','reason',$value,array(),array(),'',''); ?>
          </div>

          <div class="col-md-4 border-right">
            <?php $value = (isset($project) ? $project->closed_remarks : ''); ?>
            <?php echo render_textarea('closed_remarks','remarks',$value,array(),array(),'',''); ?>
          </div>
           <hr>
        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>


</div>

<?php $this->load->view('admin/casediary/court'); ?>
<?php $this->load->view('admin/casediary/case_nature'); ?>
<?php $this->load->view('admin/casediary/court_instance'); ?>

<style type="text/css">
   .panel-heading .accordion-toggle:after {
    /* symbol for "opening" panels */
    font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
    content: "\2212";    /* adjust as needed, taken from bootstrap.css */
    float: right;        /* adjust as needed */
    color: black;         /* adjust as needed */
}
.panel-heading .accordion-toggle.collapsed:after {
    /* symbol for "collapsed" panels */
    content: "\2b";    /* adjust as needed, taken from bootstrap.css */
}
</style>

<script type="text/javascript">
  function hearingTable(tableName,projectID) {
	  alert(tableName);
     var fnServerParams = {  };
     var tableName_ = '.table-'+tableName+'-hearings';
     if ($.fn.DataTable.isDataTable(tableName_)) {
       $(tableName_).DataTable().destroy();
     }
     _table_api = initDataTable(tableName_, admin_url + 'projects/hearings_tables/'+projectID+'/'+tableName, false, false, fnServerParams, [
       [1, 'ASC'],
       [1, 'ASC']
       ]);
  }

 $(document.body).addClass('notranslate');
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
		alert(src);
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

<?php } ?>