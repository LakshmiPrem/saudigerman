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

       		<?php $legal_arr = get_legal_statuses(); ?>	

  					<?php $value = (isset($project) ? $project->cheque_status : ''); ?>

       				<?php echo render_select('cheque_status',$legal_arr,array('id','name'),'cheque_status',$value); ?>

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





<?php }

elseif($project->case_type == 'police_case'){ ?>

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

  					<?php $value = (isset($project) ? $project->pc_filedby : ''); ?>

       				<?php echo render_input('pc_filedby','pc_filedby',$value,'text'); ?>

       		</div>

       		<div class="col-md-4">	

  					<?php $value = (isset($project) ? $project->pc_name : ''); ?>

       				<?php echo render_input('pc_name','pc_name',$value,'text'); ?>

       		</div>

       		<div class="col-md-4">	

  					<?php $value = (isset($project) ? $project->pc_city : ''); ?>

       				<?php echo render_input('pc_city','pc_city',$value,'text'); ?>

       		</div>



       		<div class="col-md-4">	

  					<?php $value = (isset($project) ? $project->pc_checksno : ''); ?>

       				<?php echo render_input('pc_checksno','pc_checksno',$value,'number'); ?>

       		</div>

       		<div class="col-md-4">	

  					<?php $value = (isset($project) ? $project->pc_bank : ''); ?>

       				<?php echo render_input('pc_bank','pc_bank',$value,'text'); ?>

       		</div>



       	<!--	<div class="col-md-4">	

  					<?php $value = (isset($project) ? _d($project->cheque_due_date) : ''); ?>

       				<?php echo render_date_input('cheque_due_date','due_date',$value); ?>

       		</div>-->



       		<div class="col-md-4">	

  					<?php $value = (isset($project) ? $project->pc_caseamount : ''); ?>

       				<?php echo render_input('pc_caseamount','pc_caseamount',$value,'text'); ?>

       		</div>



          <!--<div class="col-md-4">  

            <?php $position_arr = get_approval_statuses(); ?>

            <?php  $selected = (isset($project) ? $project->approval_status : ' ');

            echo render_select('approval_status',$position_arr,array('id','name'),'approval_status',$selected);?>

          </div>-->



          <div class="col-md-4">  

            <?php $value = (isset($project) ? _d($project->pc_regstrn_date) : _d(date('Y-m-d'))); ?>

              <?php echo render_date_input('pc_regstrn_date','pc_regstrn_date',$value); ?>

          </div>



          <div class="col-md-4">  

            <?php $value = (isset($project) ? $project->pc_complaint_no : ''); ?>

              <?php echo render_input('pc_complaint_no','pc_complaint_no',$value,'text'); ?>

          </div>



           <div class="col-md-4">  

            <?php $value = (isset($project) ? $project->pc_criminal_caseno : ''); ?>

              <?php echo render_input('pc_criminal_caseno','pc_criminal_caseno',$value,'text'); ?>

          </div>









       	  <div class="col-md-4">  

            <?php $yes_no_arr = [['id'=>'no','name'=>'No'],['id'=>'yes','name'=>'Yes']]  ?>

            <?php  $selected = (isset($project) ? $project->pc_converted_civil : 'no');?>

            <?php echo render_select('pc_converted_civil',$yes_no_arr,array('id','name'),'pc_converted_civil',$selected);?>



          </div>

           <div class="col-md-4">  

            <?php $value = (isset($project) ? $project->pc_civil_caseno : ''); ?>

              <?php echo render_input('pc_civil_caseno','pc_civil_caseno',$value,'text'); ?>

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

<div class="panel-group" id="accordion">

  <div class="panel panel-info">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#abscound_case">

           <?=ucwords(_l('abscound_case'))?>

        </a>

      </h4>

      <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('abscound_case') ?>" style="margin-left: 120px; margin-top: -17px;">

      

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/abscound_writeoff_case/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="abscounded" data-id="abscounded"  <?php echo($project->abscounded == '1' ? 'checked' : '') ?>>

        <label class="onoffswitch-label" for="abscounded"></label>

      </div>

    </div>

    <div id="abscound_case" class="panel-collapse collapse ">

      <div class="panel-body">

         <?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>



          <div class="col-md-12 ">

            <?php $value = (isset($project) ? $project->abscounded_remarks : ''); ?>

            <?php echo render_textarea('abscounded_remarks','remarks',$value,array(),array(),'',''); ?>

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

 <div class="panel-heading hide">

      <h4 class="panel-title">

        <button type="button" class="btn btn-info "  onclick="init_court_instance();" >

           <?=ucwords(_l('create_instance'))?>

        </button>

      </h4>

  </div> 

  <?php foreach ($court_instances as  $court_instance) {?>

    <div class="panel panel-default hide">

    <div class="panel-heading">



      <h4 class="panel-title" >

         <a href="javascript:void(0);" onclick="init_court_instance(<?=$court_instance['id']?>);" > <i class="fa fa-circle-o" aria-hidden="true"></i> 

           <?=$court_instance['instance_name']?> <br><small>    <?=$court_instance['case_number']?> /  <?=$court_instance['courtname']?>  / <?=$court_instance['case_nature_name']?> / <?=$court_instance['claiming_amount']?>  </small>

         </a> 

      </h4>

    </div>

  </div>

  <?php } ?>



  <div class="panel panel-info">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#abscound_case">

           <?=ucwords(_l('abscound_case'))?>

        </a>

      </h4>

      <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('abscound_case') ?>" style="margin-left: 130px; margin-top: -17px;">

      

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/abscound_writeoff_case/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="abscounded" data-id="abscounded"  <?php echo($project->abscounded == '1' ? 'checked' : '') ?>>

        <label class="onoffswitch-label" for="abscounded"></label>

      </div>

    </div>

    <div id="abscound_case" class="panel-collapse collapse ">

      <div class="panel-body">

         <?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>



          <div class="col-md-12 ">

            <?php $value = (isset($project) ? $project->abscounded_remarks : ''); ?>

            <?php echo render_textarea('abscounded_remarks','remarks',$value,array(),array(),'',''); ?>

          </div>

           <hr>

        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>

        <?php echo form_close(); ?>

      </div>

    </div>

  </div>





  <div class="panel panel-warning">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#writeoff_case">

           <?=ucwords(_l('writeoff_case'))?>

        </a>

       

      </h4>

      <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('writeoff_case') ?>" style="margin-left: 120px; margin-top: -17px;">

        <?php  $value=($project->writeoff == '1'? 'checked' : '') ?>

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/abscound_writeoff_case/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="writeoff" data-id="writeoff" <?=$value?>>

        <label class="onoffswitch-label" for="writeoff"></label>

      </div>

    </div>

    <div id="writeoff_case" class="panel-collapse collapse ">

      <div class="panel-body">

         <?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>



         

         <?php $write_sts_arr = [

                                  ['id'=>'partially','name'=>'Partially'],

                                  ['id'=>'completely','name'=>'Completely']

                                ]; ?>

            <?php #########  Client Position    ###############?>

          <div class="col-md-4 border-right">

            

            <?php  $selected = (isset($project) ? $project->writeoff_status : '');

            echo render_select('writeoff_status',$write_sts_arr,array('id','name'),'writeoff_status',$selected);?>

          </div>



          <div class="col-md-4 border-right ">

           <?php $value = (isset($project) ? $project->writeoff_amount : ''); ?>

            <?php echo render_input('writeoff_amount','writeoff_amount',$value,'number'); ?>

          </div>



          <div class="col-md-4 border-right">

            <?php $value = (isset($project) ? $project->writeoff_financial_year : ''); ?>

            <?php echo render_input('writeoff_financial_year','writeoff_financial_year',$value); ?>

          </div>



          <div class="col-md-12 border-right">

            <?php $value = (isset($project) ? $project->writeoff_remarks : ''); ?>

            <?php echo render_textarea('writeoff_remarks','remarks',$value,array(),array(),'',''); ?>

          </div>

           <hr>

        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>

        <?php echo form_close(); ?>

      </div>

    </div>

  </div>

  <?php

			if($project->case_type == 'labour_case'){  ?>

	  <div class="panel panel-success">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-parent="#accordion" href="#clearance_cert">

           <?=ucwords(_l('clearance_cert'))?>

        </a>

      </h4>

      <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('clearance_cert') ?>" style="margin-left: 320px; margin-top: -17px;">

       <?php  $value=($project->clearance_cert == '1'? 'checked' : '') ?>

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/abscound_writeoff_case/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="clearance_cert" data-id="clearance_cert" <?=$value?>>

        <label class="onoffswitch-label" for="clearance_cert"></label>

      </div>

    </div>

    



  </div>

  <?php

		}

	?>

	

	  <div class="panel panel-success">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#settlement_case">

           <?=ucwords(_l('settlement_case'))?>

        </a>

       

      </h4>

      <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('settlement_case') ?>" style="margin-left: 120px; margin-top: -17px;">

        <?php  $value=($project->settlement == '1'? 'checked' : '') ?>

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/abscound_writeoff_case/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="settlement" data-id="settlement" <?=$value?>>

        <label class="onoffswitch-label" for="settlement"></label>

      </div>

    </div>

    <div id="settlement_case" class="panel-collapse collapse ">

      <div class="panel-body">

         <?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>



         

         <?php $write_sts_arr = [

                                  ['id'=>'yes','name'=>'Completed'],

                                  ['id'=>'no','name'=>'Not Completed']

                                ]; ?>

            <?php #########  Client Position    ###############?>

          <div class="col-md-4 border-right">

            

            <?php  $selected = (isset($project) ? $project->settlement_status: '');

            echo render_select('settlement_status',$write_sts_arr,array('id','name'),'settlement_status',$selected);?>

          </div>



         



          <div class="col-md-8 border-right">

            <?php $value = (isset($project) ? $project->settlement_remark : ''); ?>

            <?php echo render_textarea('settlement_remark','remarks',$value,array(),array(),'',''); ?>

          </div>

           <hr>

        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>

        <?php echo form_close(); ?>

      </div>

    </div>

  </div>

  

  	  <div class="panel panel-primary">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#transferred_case">

           <?=ucwords(_l('transferred_case'))?>

        </a>

       

      </h4>

      <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('transferred_case') ?>" style="margin-left: 220px; margin-top: -17px;">

        <?php  $value=($project->transferred == '1'? 'checked' : '') ?>

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/abscound_writeoff_case/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="transferred" data-id="transferred" <?=$value?>>

        <label class="onoffswitch-label" for="transferred"></label>

      </div>

    </div>

    <div id="transferred_case" class="panel-collapse collapse ">

      <div class="panel-body">

         <?php echo form_open(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>



          <div class="col-md-4 border-right">

                                     <?php ############## Country ################### ?>

                    <?php $selected = (isset($project) ? $project->trans_country : '234'); ?>

                        <?php  echo render_select('trans_country',$countries,array('country_id','short_name'),'country',$selected,array());?>

                        </div>

         



          <div class="col-md-8 border-right">

            <?php $value = (isset($project) ? $project->trans_remarks : ''); ?>

            <?php echo render_textarea('trans_remarks','remarks',$value,array(),array(),'',''); ?>

          </div>

           <hr>

        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>

        <?php echo form_close(); ?>

      </div>

    </div>

  </div>

  

	

    <div class="panel panel-danger">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#close_case">

           <?=ucwords(_l('close_case'))?>

        </a>

      </h4>

    </div>

    <div id="close_case" class="panel-collapse collapse ">

      <div class="panel-body">

         <?php echo form_open_multipart(admin_url('projects/save_project_table_details/'.$project->id),array('id'=>'case-form')); ?>



         

         <?php $final_sts_arr = get_case_final_statuses(); ?>

            <?php #########  Client Position    ###############?>

            <div class="col-md-4 border-right">

            

            <?php  $selected = (isset($project) ? $project->final_status : '');

            echo render_select('final_status',$final_sts_arr,array('id','name'),'final_status',$selected);?>

          </div>

		<!--

          <div class="col-md-4 border-right ">

           <?php $value = (isset($project) ? _d($project->closed_date) : _d(date('Y-m-d'))); ?>

            <?php echo render_date_input('closed_date','closed_date',$value); ?>

          </div>

		       <?php ############## verify staff ################### ?>

		 

            <div class="col-md-4 border-right ">

              <?php  $selected = (isset($project) ? $project->close_verify : '');?>

              <?php echo render_select('close_verify',$lawyer_staffs,array('staffid',array('firstname','lastname')),'close_verify',$selected);

				//echo render_input('legal_cordinator','legal_coordinator',$value,'text'); ?>

           							

            </div>

              <div class="col-md-4 border-right ">

           <?php $value = (isset($project) ? _d($project->close_verifydt) : _d(date('Y-m-d'))); ?>

            <?php echo render_date_input('close_verifydt','close_verifydt',$value); ?>

          </div>

         <div class="col-md-4 border-right">

            <?php $value = (isset($project) ? $project->reason : ''); ?>

            <?php echo render_textarea('reason','reason',$value,array(),array(),'',''); ?>

          </div>-->

			      <div class="col-md-4 border-right">

                            <div class="form-group select-placeholder">

                                <label for="status"><?php echo _l('matter_status'); ?></label>

                                <div class="clearfix"></div>

                                <select name="status" id="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" <?php if ($project->status==4) echo 'disabled';?>>

                                    <?php foreach($statuses as $status){ ?>

                                        <option value="<?php echo $status['id']; ?>" <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>><?php echo $status['name']; ?></option>

                                    <?php } ?>

                                </select>

                            </div>

                        </div>

          

                                          <div class="col-md-4 border-right">

                

         <div class="form-group">

                                <label for="close_document" class="profile-image"><?php echo _l('close_document'); ?></label>

                                <input type="file" name="close_attachment" class="form-control" id="close_attachment" required>

                             </div>

                        <?php if((isset($project) && $project->close_attachment != NULL) ){ ?>

                             <?php 

							   $extension = pathinfo($project->close_attachment, PATHINFO_EXTENSION);

							   if($extension!='pdf'){?>

                            <div class="img">

                                <?php $path = get_upload_path_by_type('project').'/'.$project->id.'/'; ?>

                                <img class="img-responsive" src="<?php echo base_url('uploads/projects/').$project->id.'/'.$project->close_attachment; ?>">

                            </div>



                        <?php }else{ ?> 

               <div class="img">

               <a target="_blank" href=<?php echo base_url('uploads/projects/').$project->id.'/'.$project->close_attachment; ?> download ><i class="fa fa-download"></i></a>

							   </div>  

               <?php }}

			if($project->closed_date!=''){

			

			$value1 = (isset($project) ? _d($project->closed_date) : '');

											  echo '<b> Closed Staff: '.get_staff_full_name($project->closed_staff).'</b><br><b> Closed Date: '.$value1.'</b>';}?>

                </div>

               <div class="col-md-12 border-right">

            <?php $value = (isset($project) ? $project->closed_remarks : ''); ?>

            <?php echo render_textarea('closed_remarks','remarks',$value,array('rows'=>2),array(),'',''); ?>

          </div>

        

           <hr>

           <?php if ($project->status!=4){?>

        <button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>

        <?php } ?>

        <?php echo form_close(); ?>

      </div>

    </div>

  </div>

    <div class="panel" style="background-color:#F3A8AA">

    <div class="panel-heading">

      <h4 class="panel-title">

        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#close_case_verify">

           <?=ucwords(_l('close_case_verify'))?>

        </a>

      </h4>

       <div  class="onoffswitch"  data-toggle="tooltip" data-title="<?php echo _l('close_case_verify') ?>" style="margin-left: 200px; margin-top: -17px;">

        <?php  $value=($project->closeverify_status == '1'? 'checked' : '') ?>

        <input type="checkbox"  data-switch-url="<?php echo  admin_url('projects/close_caseverify/'.$project->id) ?>" name="onoffswitch" 

        class="onoffswitch-checkbox" id="closeverify_status" data-id="closeverify_status" <?=$value?>>

        <label class="onoffswitch-label" for="closeverify_status"></label>

      </div>

    </div>

 

    <div id="close_case_verify" class="panel-collapse collapse ">

      <div class="panel-body">

               

            <?php ############## verify staff ################### ?>

		

            <div class="col-md-4 border-right ">

              <?php  $selected = (isset($project) ? get_staff_full_name($project->close_verify ): '');?>

              <?php echo render_input('close_verify','close_verify',$selected,'text',array('readonly' => 'readonly')); ?>

           							

            </div>

              <div class="col-md-4 border-right ">

           <?php $value = (isset($project) ? _d($project->close_verifydt) : ''); ?>

            <?php echo render_date_input('close_verifydt','close_verifydt',$value,array('readonly' => 'readonly')); ?>

          </div>

        

			 

           <hr>

       

      </div>

    </div>

  </div>





</div>





<?php } ?>



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







