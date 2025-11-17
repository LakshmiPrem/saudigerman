<?php if($project->case_type == 'chequebounce'){ ?>
<div class="content">
  <div class="row">
    <div class="panel_s">
      <div class="panel-body">
<?php echo form_open(admin_url('projects/save_case_details/'.$project->id),array('id'=>'case-form')); ?>
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

<ul class="nav nav-tabs" role="tablist">
  <li role="presentation" class="active">
    <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
      <?php echo _l('project_case_details'); ?>
    </a>
  </li>
  <!-- <li role="presentation">
    <a href="#tab_acts" aria-controls="tab_acts" role="tab" data-toggle="tab">
      <?php echo _l('acts'); ?>
    </a>
  </li> -->
 </ul>
 <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="tab_content">
    	<div class="content">
    		<div class="row">
        		<div class="panel_s">
            		<div class="panel-body">
<?php echo form_open(admin_url('projects/save_case_details/'.$project->id),array('id'=>'case-form')); ?>
     			<div class="row"> <!-- row2 -->
     				<?php ############  Case Number ########### ?>
	  				<div class="col-md-4">	
	  					<?php $value = (isset($project) ? $project->case_number : ''); ?>
         				<?php echo render_input('case_number','casediary_casenumber',$value,'text'); ?>
         			</div>	

     				<?php ############## File No ################### ?>

          	<div class="col-md-4 border-right ">
					    <?php  $value = (isset($project) ? $project->file_no : ''); ?>
					    <?php echo render_input('file_no','casediary_file_no',$value,'text'); ?>
				    </div> 

				    <?php ############## File No ################### ?>

          			<div class="col-md-4 border-right ">
					    <?php  $value = (isset($project) ? $project->old_file_no : ''); ?>
					    <?php echo render_input('old_file_no','old_file_no',$value,'text'); ?>
				    </div>
				    <?php $position_arr = get_client_positions(); ?>
				    <?php #########  Client Position    ###############?>
    				<div class="col-md-4 border-right">
						
						<?php  $selected = (isset($project) ? $project->client_position : 'defendant');
						echo render_select('client_position',$position_arr,array('id','name'),'client_position',$selected);?>
					</div>

					 <?php #########  Opposite Party Position    ###############?>
    				<div class="col-md-4 border-right">
						
						<?php  $selected = (isset($project) ? $project->opposite_party_position : 'defendant');
						echo render_select('opposite_party_position',$position_arr,array('id','name'),'opposite_party_position',$selected);?>
					</div>

					 <?php ############## Clients Makani No ################### ?>

          			<div class="col-md-4 border-right ">
					    <?php  $value = (isset($project) ? $project->clients_makani : ''); ?>
					    <?php echo render_input('clients_makani','clients_makani',$value,'text'); ?>
				    </div>

				     <?php ############## Opposite Party Makani No ################### ?>

          			<div class="col-md-4 border-right ">
					    <?php  $value = (isset($project) ? $project->opposite_makani : ''); ?>
					    <?php echo render_input('opposite_makani','opposite_makani',$value,'text'); ?>
				    </div>

				    <div class="col-md-4 border-right">

					<?php ##################### Court  ##############################
					  $selected = (isset($project) ? $project->court_id : '');
					  if(is_admin() ){
					   echo render_select_with_input_group('court_id',$courts,array('id','name'),'hearing_court',$selected,'<a href="#" onclick="new_Courts();return false;"><i class="fa fa-plus"></i></a>');
					 } else {
					  echo render_select('court_id',$courts,array('id','name'),'hearing_court',$selected);
					  }?>
      				</div>

					
         			<?php ############  Claim Amount ########### ?>
         			<div class="col-md-4 border-right">
						<?php $value =(isset($project) ? $project->claiming_amount : ''); ?>
						<?php echo render_input('claiming_amount','claiming_amount',$value,'text'); ?>
					</div>
					
					<?php ##########   Referred By ############## ?>
					<div class="col-md-4 ">
						<?php  $value = (isset($project) ? $project->referred_by : ''); ?>
						<?php echo render_input('referred_by','referred_by',$value,'text'); ?>
					</div>
					<?php ##########  Lawyer Attending ########## ?>
					<div class="col-md-4 border-right">
						<?php 
						/*$selected = [];
                        if(isset($project->assigned_lawyers)){
                          foreach ($project->assigned_lawyers as $value) {
                            $selected[] = $value['assigneeid'];
                          }
                        }*/
             $selected = (isset($project) ? $project->lawyer_id : ''); 
						echo render_select('lawyer_id',$staff,array('staffid',array('firstname','lastname')),'lawyer_attending',$selected);?>
					</div>
					
					<?php ########## Opposite Party ##############  ?>
					<div class="col-md-4">

						<?php $selected = (isset($project) ? $project->opposite_party : '');
						
						if(is_admin() ){
						echo render_select_with_input_group('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty',$selected,'<a href="#" onclick="new_opposite_party();return false;"><i class="fa fa-plus"></i></a>');
						} else {
						echo render_select('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty',$selected);
						}?>
					</div>

					 <?php ############## Opposite Party Lawyer ################### ?>

          			<div class="col-md-4 border-right ">
					    <?php  $value = (isset($project) ? $project->opposite_lawyer : ''); ?>
					    <?php echo render_input('opposite_lawyer','opposite_lawyer',$value,'text'); ?>
				    </div>


					<div class="col-md-12"><hr></div>
    
					<?php  ######### Details of Claim ########### ?>
					<div class="col-md-6 border-right">
						<?php $value = (isset($project) ? $project->details_of_claim : ''); ?>
						<?php echo render_textarea('details_of_claim','details_of_claim',$value,array(),array(),'','tinymce'); ?>
					</div>
					<?php ##########  Case Details ############### ?>
					<div class="col-md-6 ">
						<?php $value = (isset($project) ? $project->case_details : ''); ?>
						<?php echo render_textarea('case_details','casediary_case_details',$value,array(),array(),'','tinymce'); ?>
					</div>			
				</div><!-- end row2 -->	   
				<hr>
				<button type="submit" class="btn btn-info"><?php echo _l('project_save_case'); ?></button>
			
<?php echo form_close(); ?>
			</div>
		</div>
	</div>
</div>

</div>

</div>
<?php $this->load->view('admin/casediary/court'); ?>
<?php //$this->load->view('admin/casediary/court_type'); ?>
<?php $this->load->view('admin/casediary/oppositeparty'); ?>
<?php $this->load->view('admin/casediary/partytype'); ?>

<?php } ?>