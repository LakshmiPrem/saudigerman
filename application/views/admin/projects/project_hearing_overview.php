     <div class="panel-body">
       <?php if($project->project_stage==$case_overview->instance_id && $case_overview->case_number!='' && $case_overview->stage_status==0){ ?>
       <div class="col-md-12">
   	         <a href="#" id="btn_add_hearing" class="btn btn-info pull-right mbot25" onclick="init_hearing('',<?=$case_overview->instance_id?>); return false;"><?php echo _l('add_new').' '._l($case_overview->details_type).' '._l('hearing'); ?> </a>
	 </div>  
		 <?php } ?>
    
      
  
     <div class="col-md-12">
	 	
   <?php render_datatable(array(
                        //_l('id'),
                        _l('hearing_date'),
                        _l('hearing_list_subject'),
                        //_l('court_fee'),
                        _l('casediary_casenumber'),
                        _l('assigned_lawyer'),
                        _l('court_decision'),
					//	_l('comments'),
					//	_l('action'),
                        ),'stage-hearings'); ?>
              
          
 	 </div> 
	  </div>  




