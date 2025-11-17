<div class="content">
  <div class="row">
    <div class="panel_s">
      <div class="panel-body">
      <div class="col-md-12">
	<div class="col-md-9"><p style="font-size: 20px;font-weight: 400"><?php echo _l('project_project_details'); ?></p></div><div class="col-md-3"><?php if(isset($project)){ ?>
<a href="#" data-toggle="modal" data-target=".reminder-modal-project-<?php echo $project->id; ?>" class="btn btn-success mbot25"><i class="fa fa-bell-o"></i> <?php echo _l('set_reminder'); ?></a></div></div>

<div class="clearfix"></div>

<?php //render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders');
$this->load->view('admin/includes/modals/reminder',array('id'=>$project->id,'name'=>'project','members'=>$members,'reminder_title'=>_l('set_reminder')));
} ?>
<hr />
      <?php echo form_open((isset($project_instances) ? admin_url('projects/project_instance/'.$project_instances->id) : admin_url('projects/project_instance')),array('id'=>'project-instance-form')); ?>

     		<div class="row"> <!-- row2 -->
   			
       		<div class="col-md-4">	
       		  <?php  $value = (isset($project_instances) ? $project_instances->project_id: $project->id); ?>
					    <input type="hidden" name="project_id" id="project_id" value="<?=$value?>">
  					<?php $value = (isset($project_instances) ? $project_instances->agreement_date : ''); ?>
       				<?php echo render_input('agreement_date','agreement_date',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->tenure_work: ''); ?>
       				<?php echo render_input('tenure_work','tenure_work',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->nature_work: ''); ?>
       				<?php echo render_input('nature_work','nature_work',$value,'text'); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->dlp_startdate : ''); ?>
       				<?php echo render_input('dlp_startdate','dlp_startdate',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->dlp_enddate : ''); ?>
       				<?php echo render_input('dlp_enddate','dlp_enddate',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->bank_gamount : ''); ?>
       				<?php echo render_input('bank_gamount','bank_gamount',$value,'text'); ?>
       		</div>
       		<div class="col-md-3">	
  					<?php $value = (isset($project_instances) ? $project_instances->bank_name : ''); ?>
       				<?php echo render_input('bank_name','bank_name',$value,'text'); ?>
       		</div>
				<div class="col-md-6 border-right">
						<?php $value = (isset($project_instances) ? $project_instances->bank_gdetails : ''); ?>
						<?php echo render_textarea('bank_gdetails','bank_gdetails',$value,array("rows"=>2),array(),'',''); ?>
					</div>
      		<?php $gamt_sts_arr = [
                                  ['id'=>'yes','name'=>'YES'],
                                  ['id'=>'no','name'=>'NO']
                                ]; ?>
          
          <div class="col-md-3 border-right">
            				
       			<?php  $selected = (isset($project_instances) ? $project_instances->bgamt_receive : 'no');
						echo render_select('bgamt_receive',$gamt_sts_arr,array('id','name'),'bgamt_receive',$selected,array('onchange'=>'valueChanged()'));?>
       		</div>
 			<div class="col-md-12 <?php if ($selected=='no')echo 'hide'?>" id="bgamt_receive1">
       		<div class="col-md-8 border-right">
						<?php $value = (isset($project_instances) ? $project_instances->bgamt_rec_remark : ''); ?>
						<?php echo render_textarea('bgamt_rec_remark','bgamt_rec_remark',$value,array("rows"=>2),array(),'',''); ?>
					</div>
					<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->bgamt_rec_date : ''); ?>
       				<?php echo render_input('bgamt_rec_date','bgamt_rec_date',$value); ?>
				</div></div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->retention_amount : ''); ?>
       				<?php echo render_input('retention_amount','retention_amount',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->retention_percent : ''); ?>
       				<?php echo render_input('retention_percent','retention_percent',$value,'text'); ?>
       		</div>
       			<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->retention_startdate : ''); ?>
       				<?php echo render_input('retention_startdate','retention_startdate',$value); ?>
       		</div>

       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->retention_enddate : ''); ?>
       				<?php echo render_input('retention_enddate','retention_enddate',$value); ?>
       		</div>
       		<?php $ret_sts_arr = [
                                  ['id'=>'yes','name'=>'YES'],
                                  ['id'=>'no','name'=>'NO']
                                ]; ?>
          
          <div class="col-md-4 border-right">
            				
       			<?php  $selected = (isset($project_instances) ? $project_instances->retamt_receive : 'no');
						echo render_select('retamt_receive',$ret_sts_arr,array('id','name'),'retamt_receive',$selected,array('onchange'=>'valueChanged1()'));?>
       		</div>
		<div class="col-md-12 <?php if ($selected=='no')echo 'hide'?>" id="retamt_receive1">
       		<div class="col-md-8 border-right">
						<?php $value = (isset($project_instances) ? $project_instances->retamt_rec_remark : ''); ?>
						<?php echo render_textarea('retamt_rec_remark','retamt_rec_remark',$value,array("rows"=>2),array(),'',''); ?>
					</div>
					<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->retamt_rec_date : ''); ?>
       				<?php echo render_input('retamt_rec_date','retamt_rec_date',$value); ?>
       		</div></div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->project_amount : ''); ?>
       				<?php echo render_input('project_amount','project_amount',$value,'text'); ?>
       		</div>
				
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->variations : ''); ?>
       				<?php echo render_input('variations','variations',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->total_workdone : ''); ?>
       				<?php echo render_input('total_workdone','total_workdone',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->deductions : ''); ?>
       				<?php echo render_input('deductions','deductions',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->final_amount : ''); ?>
       				<?php echo render_input('final_amount','final_amount',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->approved_final_amount : ''); ?>
       				<?php echo render_input('approved_final_amount','approved_final_amount',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->pamount_received : ''); ?>
       				<?php echo render_input('pamount_received','pamount_received',$value,'text'); ?>
       		</div>
       		<div class="col-md-4">	
  					<?php $value = (isset($project_instances) ? $project_instances->pamount_balance : ''); ?>
       				<?php echo render_input('pamount_balance','pamount_balance',$value,'text'); ?>
       		</div>


        </div>
        	<hr>
				<button type="submit" class="btn btn-info"><?php echo _l('save'); ?></button>
			
<?php echo form_close(); ?>
      </div>
    </div>
  </div>
</div>




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
	
    function valueChanged()
    {
		
		selectElement = document.querySelector('#bgamt_receive');
        output = selectElement.value;
        if(output=='yes')   
          //  $("#reanswer").show();
		 $('#bgamt_receive1').removeClass('hide');
        else
			 $('#bgamt_receive1').addClass('hide');
            //$("#reanswer").hide();
    }
	function valueChanged1()
    {
		alert('ss');
		selectElement = document.querySelector('#retamt_receive');
        output = selectElement.value;
        if(output=='yes')   
          //  $("#reanswer").show();
		 $('#retamt_receive1').removeClass('hide');
        else
			 $('#retamt_receive1').addClass('hide');
            //$("#reanswer").hide();
    }
</script>




