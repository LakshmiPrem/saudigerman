<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php echo  form_open_multipart((isset($hearing_judgement) ? admin_url('projects/hearing_judgement/'.$hearing_judgement->id) : admin_url('projects/hearing_judgement')),array('id'=>'hearing-judgement-form')); ?>
<input type="hidden" name="project_id" class="project_id" >
<div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4 class="modal-title"><?php echo _l('set_hearing_judgement'); ?></h4>
</div>
<div class="modal-body">
  <div class="row"> <!-- row2 -->

    <!-- <div class="col-md-12">
       <div class="total_rate"></div><hr>
    </div> -->
     <?php ############  Instance Type ########### ?>

            <div class="col-md-6 border-right" id="div_stagejudge_project">  

              <?php $selected = (isset($hearing_judgement) ? $hearing_judgement->stage_id :''); ?>

              <?php  echo render_select('stage_id',$proejct_instances,array('id','instance_name'),'court_instance',$selected);?>

            </div>
<div class="col-md-6 border-right hide">
      <?php $status_arr = get_judge_rule_status(); ?>   
      <?php $selected = (isset($hearing_judgement) ? $hearing_judgement->judgement_ruling : 'ruling'); ?>
      <?php echo render_select('judgement_ruling',$status_arr,array('id','name'),'project_judgement',$selected);?>
    </div>
  	<div class="col-md-6 border-right">
      <?php $selected = (isset($hearing_judgement) ? $hearing_judgement->judgement_ruling_status : ''); ?>
      <?php echo render_select('judgement_ruling_status',$judgement_statuses,array('id','name'),'judgement_status',$selected);?>
    </div>
    	<div class="col-md-6 border-right">
      <?php $selected = (isset($hearing_judgement) ? $hearing_judgement->decree_order_status : ''); ?>
      <?php echo render_select('decree_order_status',$order_statuses,array('id','name'),'decree_order_status',$selected);?>
    </div>
    <?php ####################### Date ################  ?>
    <div class="col-md-6 border-right">
      <?php $value = (isset($hearing_judgement) ? _d($hearing_judgement->judgement_date) : _d(date('Y-m-d'))); ?>
      <?php echo render_date_input('judgement_date','judgement_date',$value); ?>
    </div>
    <?php ####################### Court Fee ##########   ?>
    <div class="col-md-6 border-right">
      <?php $value = (isset($hearing_judgement) ? $hearing_judgement->award : ''); ?>
      <?php echo render_input('award','judgement_award',$value); ?>
    </div>
      <div class="col-md-12 border-right">
  	<div class="form-group">
    <label for="judge_attachment" class="profile-image"><?php echo _l('attach_judgement_doc'); ?></label>
     <input type="file" name="judge_attachment" class="form-control" id="file">
    </div>
	  </div>  
      <div class="col-md-12 border-right">
      <?php $value = (isset($hearing_judgement) ? $hearing_judgement->directions : ''); ?>
      <?php echo render_textarea('directions','judgement_directions',$value); ?>
    </div>
     <div class="col-md-12 border-right">
      <?php $value = (isset($hearing_judgement) ? $hearing_judgement->summary : ''); ?>
      <?php echo render_textarea('summary','summary',$value); ?>
      
    </div>
  </div><!-- end row2 -->    
  <div class="modal-footer">
    <button type="submit" id="btn_hearing_judgement" class="btn btn-info pull-right" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#hearing_form"><?php echo _l('submit'); ?></button>
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
</script>