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



