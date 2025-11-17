<div class="task-table">
   
<div class="row">
  <div class="col-md-12">
   <div class="panel_s panel-info">
                     <div class="panel-body">
  <?php echo form_open(admin_url('casediary/courtorder/'),array('id'=>'courtorder-form')); ?>
            <?php echo form_hidden('project_id',$project->id); ?>

      <div class="row">
         <h3><?php echo _l('project_court_attach'); ?></h3>
          
            <div class="col-md-3">
        
            <?php echo render_select('documentid',$document_types,array('id','name'),'corder_type',''); ?>
		  </div>
          <div class="col-md-3">
        
          <?php echo render_input('corder_name','corder_name',''); ?>
          </div>
           <div class="col-md-3">
        <?php $value=_d(date('Y-m-d')); ?>
          <?php echo render_date_input('order_date','corder_date',$value); ?>
          </div>
              <div class="col-md-3">
        <?php $value=_d(date('Y-m-d')); ?>
          <?php echo render_date_input('end_date','cend_date',''); ?>
          </div>
           <div class="col-md-3">
        
          <?php echo render_input('corder_amount','corder_amount',''); ?>
          </div>
          <div class="col-md-6">
          <?php //$value = (isset($scopes) ? $scopes->scope_description : ''); ?>
          <?php echo render_textarea('remark','remarks','',array('rows'=>2,'required'=>'true'),array(),'',''); ?>
          </div>
          <div class="col-md-3 mtop40">
             <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#courtorder_form"><?php echo _l('add_courtorder'); ?></button>
          </div>
      </div>
  <?php echo form_close(); ?>
  </div>
</div>
	</div></div>
	<div class="row">
 <div class="col-md-12">
  <div class="panel_s panel-info">
                     <div class="panel-body">
  <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
      <th><?php echo _l('corder_type'); ?></th> 
      <th><?php echo _l('corder_name'); ?></th> 
      <th><?php echo _l('corder_date'); ?></th> 
       <th><?php echo _l('cend_date'); ?></th>      
      <th><?php echo _l('project_courts'); ?></th>
        <th><?php echo _l('status'); ?></th> 
      
      <th></th>

    </tr>
  </thead>
  <tbody>
    <?php //if(isset($scope)){
      foreach ($court_order as $row_) { ?>
        <tr>
       
        <td><?=get_document_type_name($row_['documentid']);?></td>
         <td><?=$row_['corder_name']?></td>
         <td><?=_d($row_['order_date'])?></td>
          <td><?=_d($row_['end_date'])?></td>
         <td>
                  <div data-note-description="<?php echo $row_['id']; ?>">
                    <?php echo $row_['remark']; ?>
                </div>
             
          </td>
          <td> <?php
          // Toggle active/inactive customer
    $toggleActive='';
    $toggleActive .= '<div  class="onoffswitch" data-toggle="tooltip" data-title="' . _l('is_active') . '">
        <input type="checkbox" onchange="setTimeout(function(){ reload_tbl()},100);" data-switch-url="' . admin_url().'casediary/verify_courtorder" name="onoffswitch" class="onoffswitch-checkbox" id="' .$row_['id'] . '" data-id="' . $row_['id'] . '" ' . ($row_['active'] == '1' ? 'checked' : '') . '>
        <label class="onoffswitch-label" for="' .$row_['id'] . '"></label>
    </div>';

    //For exporting
    $toggleActive .= '<span class="hide">' . ($row_['active'] == '1' ? _l('yes') : _l('no')) . '</span>';
    //$row[] = $aRow['datecreated'];


    
    echo $toggleActive;?></td>
        <td>
        <!--  <a href="#" class="btn btn-default btn-icon" onclick="toggle_edit_note(<?php echo $row_['id']; ?>);return false;"><i class="fa fa-pencil-square-o"></i></a>-->
          <a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('casediary/delete_courtorder/'.$row_['project_id'].'/'.$row_['id']) ?>"><i class="fa fa-remove"></i></a></td>
      </tr>
      <?php }//} ?>  
  </tbody>
 </table>
	  </div></div>
</div>
  </div>
   <div class="row">
  
   <div class="col-md-12">
     <div class="panel_s panel-info">
                     <div class="panel-body">
	    <h3><?php echo _l('discussion_attachments'); ?></h3>
  <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
      <th><?php echo _l('corder_type'); ?></th> 
      <th><?php echo _l('documents'); ?></th> 
      
    

    </tr>
  </thead>
  <tbody>
    <?php //if(isset($scope)){
      foreach ($court_order as $row_) { ?>
        <tr>
       
        <td><?=get_document_type_name($row_['documentid']);?></td>
        
        <td><?php
									   $relatedfiles=get_all_court_attachments($row_['project_id'],$row_['documentid']);
										if($relatedfiles>0){
									   foreach($relatedfiles as $file){
			  $path = 'uploads/projects/' . $row_['project_id'] . '/'. $file['file_name'];
									   echo '<a target="_blank" href="'.base_url($path).'" download ><i class="fa fa-download"></i><br>'.$file['file_name'].'</a><br>';}}
			else{
				echo 'No Attachments';
			}
			?>
       </td>
      </tr>
      <?php }?>  
  </tbody>
 </table>
   </div></div>
	   </div>
	   </div>
    
