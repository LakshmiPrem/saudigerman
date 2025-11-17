<div class="panel_s">
   <div class="panel-body">
      <div class="horizontal-scrollable-tabs preview-tabs-top">
         <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
         <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
         <div class="horizontal-tabs">
            <ul class="nav nav-tabs nav-tabs-horizontal mbot15" role="tablist">
               <li role="presentation" class="active">
                  <a href="#tab_proposal" aria-controls="tab_proposal" role="tab" data-toggle="tab">
                  <?php echo _l('proposal'); ?>
                  </a>
               </li>
            </ul>
        </div>
      </div>



      <hr class="hr-panel-heading" />
      <div class="row">
         <div class="col-md-12">
            <div class="tab-content">
               <div role="tabpanel" class="tab-pane active" id="tab_proposal">
                  <div class="row mtop10">


                  		<div class="panel_s">
   <div class="panel-body">

<div class="row">
<div class="col-md-12">

<?php echo form_open(admin_url('casediary/hearing/'.$hearing->id),array('id'=>'hearing-edit-form')); ?>
<?php  echo form_hidden('id',$hearing->id); ?>
<div class="col-md-4 border-right hide">
<?php 
$selected = (isset($hearing) ? $hearing->hearing_type : ''); 

echo render_select('hearing_type',$hearing_types,array('id','name'),'hearing_type',$selected); ?>
</div>
        <?php echo form_close(); ?>

</div>
</div>

</div>
</div>


                  </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</div>









<!-- <div class="panel_s">
   <div class="panel-body">

<div class="row">
<div class="col-md-12">

<?php echo form_open(admin_url('casediary/hearing/'.$hearing->id),array('id'=>'hearing-edit-form')); ?>
<?php  echo form_hidden('id',$hearing->id); ?>
<div class="col-md-4 border-right hide">
<?php 
$selected = (isset($hearing) ? $hearing->hearing_type : ''); 

echo render_select('hearing_type',$hearing_types,array('id','name'),'hearing_type',$selected); ?>
</div>
        <?php echo form_close(); ?>

</div>
</div>

</div>
</div> -->