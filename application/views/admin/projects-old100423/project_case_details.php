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
elseif($project->case_type == 'policecase'){ ?>
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
  					<?php $value = (isset($project) ? $project->pc_filedby : ''); ?>
       				<?php echo render_input('pc_name','pc_name',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project) ? $project->pc_filedby : ''); ?>
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
 <div class="panel-heading">
      <h4 class="panel-title">
        <button type="button" class="btn btn-info "  onclick="init_court_instance();" >
           <?=ucwords(_l('create_instance'))?>
        </button>
      </h4>
  </div> 
  <?php foreach ($court_instances as  $court_instance) {?>
    <div class="panel panel-default">
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

         <!-- <div class="col-md-4 border-right">
            <?php $value = (isset($project) ? $project->reason : ''); ?>
            <?php echo render_textarea('reason','reason',$value,array(),array(),'',''); ?>
          </div>-->

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



