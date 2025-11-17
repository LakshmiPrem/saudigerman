<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<div class="row">
      <div class="col-md-7 border-right project-overview-left">
       
     <div class="panel_s panel-info">
                     <div class="panel-body">
      <div class="row">
       <div class="col-md-12">
         <p class="project-info text-uppercase bold text-dark font-medium" style="color: #FF1493">
            <?php echo _l('overview'); ?>
         </p>
      </div>
      <?php if(count($project->shared_vault_entries) > 0){ ?>
      <?php $this->load->view('admin/clients/vault_confirm_password'); ?>
      <div class="col-md-12">
         <p class="bold">
           <a href="#" onclick="slideToggle('#project_vault_entries'); return false;">
             <i class="fa fa-cloud"></i> <?php echo _l('project_shared_vault_entry_login_details'); ?>
          </a>
       </p>
       <div id="project_vault_entries" class="hide">
         <?php foreach($project->shared_vault_entries as $vault_entry){ ?>
         <div class="row" id="<?php echo 'vaultEntry-'.$vault_entry['id']; ?>">
            <div class="col-md-6">
               <p class="mtop5">
                  <b><?php echo _l('server_address'); ?>: </b><?php echo $vault_entry['server_address']; ?>
               </p>
               <p>
                  <b><?php echo _l('port'); ?>: </b><?php echo !empty($vault_entry['port']) ? $vault_entry['port'] : _l('no_port_provided'); ?>
               </p>
               <p>
                  <b><?php echo _l('vault_username'); ?>: </b><?php echo $vault_entry['username']; ?>
               </p>
               <p class="no-margin">
                  <b><?php echo _l('vault_password'); ?>: </b><span class="vault-password-fake">
                     <?php echo str_repeat('&bull;',10);?>  </span><span class="vault-password-encrypted"></span> <a href="#" class="vault-view-password mleft10" data-toggle="tooltip" data-title="<?php echo _l('view_password'); ?>" onclick="vault_re_enter_password(<?php echo $vault_entry['id']; ?>,this); return false;"><i class="fa fa-lock" aria-hidden="true"></i></a>
                  </p>
               </div>
               <div class="col-md-6">
                  <?php if(!empty($vault_entry['description'])){ ?>
                  
                  <p  style="max-height: 150px;; white-space: nowrap;overflow-y: scroll;">
                     <b><?php echo _l('vault_description'); ?>: </b><br /><?php echo $vault_entry['description']; ?>
                  </p>
                  <?php } ?>
               </div>
            </div>
            <hr class="hr-10" />
            <?php } ?>
         </div>
         <hr class="hr-panel-heading project-area-separation" />
      </div>
      <?php } ?>
      <div class="col-md-6">
         <table class="table no-margin project-overview-table">
            <tbody>
                 <?php if(!empty($project->related_matter)){?>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('related_matter'); ?></td>
                  <td>
                      <a style="color:#1446E5;font-weight: bold" href="<?php echo admin_url(); ?>projects/view/<?php echo $project->related_matter; ?>">
                        <?php echo get_parent_project_namebyid($project->related_matter); ?>
					  </a>
                  </td>
				</tr>
             <?php } ?>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('project_customer'); ?></td>
                  <td>
					   <?php $clposition=(!empty($project->client_position))?' - <span style="color:black;">'. get_position_name_by_id($project->client_position).'</span>':''; ?>
                      <a style="color:#1446E5;font-weight: bold" href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>">
                        <?php echo $project->client_data->company.$clposition; ?>
                      </a>
                  </td>
              </tr>
              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('opposite_party'); ?></td>
                  <td>
					  <?php $opposition=(!empty($project->oppositeparty_position))?' - <span style="color:black;">'. get_position_name_by_id($project->oppositeparty_position).'</span>':''; ?>
                      <a style="color:#075722;font-weight: bold" href="<?php echo admin_url(); ?>opposite_parties/opposite_party/<?php echo $project->opposite_party; ?>">
                        <?php echo get_opposite_party_name($project->opposite_party). $opposition; ?>
                      </a>
                  </td>
              </tr>
              <tr class="project-overview-file-no hide">
                  <td class="bold"><?php echo _l('opposite_defendar'); ?></td>
                  <td>  <?php $contacts= get_opposite_contact_name($project->opposite_party,'defendant');
					  foreach($contacts as $contact){
						  echo $contact['contact_name'].' <br>';
					  }
					  ?></td>
               </tr>
               <tr class="project-overview-file-no hide">
                  <td class="bold"><?php echo _l('company_status'); ?></td>
                  <td style="text-transform: uppercase"><?php echo get_opposite_party_cmpstatus($project->opposite_party); ?></td>
               </tr>
              
               <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('country'); ?></td>
                  <td><?php echo get_countryproject_name($project->countryid); ?></td>
               </tr>
               
                <tr class="project-overview-file-no hide">
                  <td class="bold"><?php echo _l('ledger_code'); ?></td>
                  <td><?php echo $project->ledger_code; ?></td>
               </tr>
            <!--   <?php if(has_permission('projects','','edit')){ ?>
               <tr class="project-overview-billing">
                  <td class="bold"><?php echo _l('project_billing_type'); ?></td>
                  <td>
                     <?php
                     if($project->billing_type == 1){
                       $type_name = 'project_billing_type_fixed_cost';
                    } else if($project->billing_type == 2){
                       $type_name = 'project_billing_type_project_hours';
                    } else {
                       $type_name = 'project_billing_type_project_task_hours';
                    }
                    echo _l($type_name);
                    ?>
                 </td>
                 <?php if($project->billing_type == 1 || $project->billing_type == 2){
                  echo '<tr class="project-overview-amount">';
                  if($project->billing_type == 1){
                    echo '<td class="bold">'._l('project_total_cost').'</td>';
                    echo '<td>'.app_format_money($project->project_cost, $currency).'</td>';
                 } else {
                    echo '<td class="bold">'._l('project_rate_per_hour').'</td>';
                    echo '<td>'.app_format_money($project->project_rate_per_hour, $currency).'</td>';
                 }
                 echo '<tr>';
              }
           }
           ?>-->
           <tr class="project-overview-status">
            <td class="bold"><?php echo _l('project_status'); ?></td>
            <td style="color:<?php echo $project_status['color']; ?>"><?php echo $project_status['name']; ?></td>
         </tr>
        
         <?php if($project->estimated_hours && $project->estimated_hours != '0'){ ?>
         <tr class="project-overview-estimated-hours">
            <td class="bold<?php if(hours_to_seconds_format($project->estimated_hours) < (int)$project_total_logged_time){echo ' text-warning';} ?>"><?php echo _l('estimated_hours'); ?></td>
            <td><?php echo str_replace('.', ':', $project->estimated_hours); ?></td>
         </tr>
         <?php } ?>
         <?php if(has_permission('projects','','create')){ ?>
        <!-- <tr class="project-overview-total-logged-hours">
            <td class="bold"><?php echo _l('project_overview_total_logged_hours'); ?></td>
            <td><?php echo seconds_to_time_format($project_total_logged_time); ?></td>
         </tr>-->
         <?php } ?>
	
         <tr class="project-overview-case-type">
            <td class="bold"><?php echo _l('case_type'); ?></td>
            <td><?php echo _l($project->case_type); ?></td>
         </tr>
		<?php if(!empty($project->ip_status)){?>
          <tr class="project-overview-case-type">
            <td class="bold"><?php echo _l('ip_status'); ?></td>
            <td><?php echo ucwords($project->ip_status); ?></td>
         </tr>
		<?php } ?>
		<?php if(!empty($project->pf_agreement_no)){?>
          <tr class="project-overview-case-type">
            <td class="bold"><?php echo _l('pf_agreement_no'); ?></td>
            <td><?php echo ($project->pf_agreement_no); ?></td>
         </tr>
         <tr class="project-overview-case-type">
            <td class="bold"><?php echo _l('pf_agreement_amount'); ?></td>
            <td><?php echo _l($project->pf_agreement_amount); ?></td>
         </tr>
		<?php } ?>
		<?php if(!empty($project->expenses_prefix) && $project->expenses_prefix!=0.00){?>
           <tr class="project-overview-case-type">
            <td class="bold"><?php echo _l('expenses_prefix'); ?></td>
            <td><?php echo ($project->expenses_prefix); ?></td>
         </tr>
		<?php } ?>
         <?php if($project->template_id > 0 ){  ?>
         <tr class="project-overview-case-template">
            <td class="bold"><?php echo _l('casetemplate'); ?></td>
            <td><a href="<?php echo admin_url('casetemplates/view/'.$project->template_id); ?>" ><?php echo get_matter_template_name_by_id($project->template_id); ?></a></td>
         </tr>
      <?php } ?>

         <?php $custom_fields = get_custom_fields('projects');
         if(count($custom_fields) > 0){ ?>
         <?php foreach($custom_fields as $field){ ?>
         <?php $value = get_custom_field_value($project->id,$field['id'],'projects');
         if($value == ''){continue;} ?>
         <tr>
            <td class="bold"><?php echo ucfirst($field['name']); ?></td>
            <td><?php echo $value; ?></td>
         </tr>
         <?php } ?>
         <?php } ?>
      </tbody>
   </table>
</div>
<div class="col-md-6  project-percent-col mtop2">
   
   <p class="bold text-center hide"><?php echo _l('project_progress_text'); ?></p>
   <div class="project-progress relative mtop15 text-center hide" data-value="<?php echo $percent_circle; ?>" data-size="150" data-thickness="22" data-reverse="true">
      <strong class="project-percent"></strong>
   </div>
   <?php hooks()->do_action('admin_area_after_project_progress') ?>
    <div class="col-md-12 border-right project-overview-left">
    <table class="table no-margin project-overview-table">
            <tbody>
              <tr class="project-overview-id">
                  <td class="bold"><?php echo _l('project'); ?> <?php echo _l('the_number_sign'); ?></td>
                  <td>
                      <?php echo $project->file_no; ?>
                  </td>
              </tr>
          <!--   <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('casediary_file_no'); ?></td>
                  <td><?php echo $project->file_no; ?></td>
               </tr>-->
				<?php if(!empty($project->rack_no)){?>
               <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('casediary_rack_no'); ?></td>
                  <td><?php echo $project->rack_no; ?></td>
               </tr>
				<?php } ?>
				<?php if(!empty($project->ticketid)){?>
                <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('legal_request'); ?></td>
                  <td><?php echo get_project_requestno($project->ticketid); ?></td>
               </tr>
				<?php } ?>
                <tr class="project-overview-date-created">
            <td class="bold"><?php echo _l('project_datecreated'); ?></td>
            <td><?php echo _d($project->project_created); ?></td>
         </tr>
         <tr class="project-overview-start-date">
            <td class="bold"><?php echo _l('project_start_date'); ?></td>
            <td><?php echo _d($project->start_date); ?></td>
         </tr>
         <?php if($project->deadline){ ?>
         <tr class="project-overview-deadline">
            <td class="bold"><?php echo _l('project_deadline'); ?></td>
            <td><?php echo _d($project->deadline); ?></td>
         </tr>
         <?php } ?>
         <?php if($project->date_finished){ ?>
         <tr class="project-overview-date-finished">
            <td class="bold"><?php echo _l('project_completed_date'); ?></td>
            <td class="text-success"><?php echo _dt($project->date_finished); ?></td>
         </tr>
         <?php } ?>
		 <?php if (!empty($project->ip_category)) {?>
                  <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('ip_category'); ?></td>
                  <td>
                      <?php echo ucwords(get_matteripcategory($project->ip_category)); ?>
                  </td>
              </tr>
                  <?php if($project->ip_category==6){?>
                   <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('ip_subcategory'); ?></td>
                  <td>
                      <?php echo ucwords($project->ip_artwork); ?>
                  </td>
              </tr>
                  <?php } } ?>
                  <?php if (!empty($project->ip_subcategory)) {?>
                   <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('ip_subcategory'); ?></td>
                  <td>
                      <?php echo get_matteripsubcategory($project->ip_subcategory); ?>
                  </td>
              </tr>
               <?php } ?>
                <?php 
				if($project->ip_logo!=''){
				  $path = get_upload_path_by_type('project') . $project->id . '/'. $project->ip_logo;?>
				 <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('artwork_filename'); ?></td>
                  <td><?php echo $project->ip_logo; ?>
                   <?php if(file_exists($path)){?>
            <a href="<?php echo site_url('download/downloadlogofile/'.$project->id.'/'.$project->ip_logo); ?>" class="btn btn-info btn-icon"><i class="fa fa-download"></i></a>
             <!-- <a target="_blank" href=<?php echo base_url('uploads/projects/').$project->id.'/'.$project->ip_logo; ?> download ><i class="fa fa-download"></i></a>-->
							   </div>  
              </td>
               </tr>	
				<?php }} ?>
              
                <?php if (!empty($project->ip_class)) {?>
                <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('class'); ?></td>
                  <td><?php echo $project->ip_class; ?></td>
               </tr>
	<?php } ?>
	  <?php if (!empty($project->ip_fileno)) {?>
               <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('file_no'); ?>: <?php echo $project->ip_filingno; ?></td>
                  <td class="bold"><?php echo _l('filing_date'); ?>: <?php echo _d($project->ip_filingdt); ?></td>
               </tr>
	<?php } ?>
	  <?php if (!empty($project->ip_regno)) {?>
                <tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('registration_no'); ?>:  <?php echo $project->ip_regno; ?></td>
                  <td class="bold"><?php echo _l('ip_registration_date'); ?>:  <?php echo _d($project->ip_registrationdt); ?></td>
               </tr>
	<?php } ?>
		</tbody>
	</table>
	</div>
</div>
</div>
		 </div>
			   </div>
			   	<?php $tags = get_tags_in($project->id,'project'); ?>
<?php if(count($tags) > 0){ ?> 
   <div class="row">
	   <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">

<div class="clearfix"></div>
<div class="tags-read-only-custom project-overview-tags">
   <hr class="hr-panel-heading project-area-separation hr-10" />
   <?php echo '<p class="font-size-14"><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
   <input type="text" class="tagsinput read-only" id="tags" name="tags" value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
</div>
<div class="clearfix"></div>

			</div></div></div></div>
			<?php } ?>
			 <div class="row">
			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="tc-content project-overview-description">
   <p class="text-uppercase bold text-dark font-medium" style="color: green"><?php echo _l('project_description'); ?></p>
   <hr class="hr-panel-heading project-area-separation" />
  
   <?php if(empty($project->description)){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_project') . '</p>';
   }
   echo '<b>'.check_for_links($project->description).'</b>';?>
</div>
			</div></div></div></div>
			        <?php if(has_permission('projects','','view')) { 
if($work_type=='litigation'){ ?>
        <div class="panel_s panel-info">
                     <div class="panel-body">
        
	<div class="row">
		   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
           
         <p class="text-uppercase text-info bold"><?php echo _l('project_overview_claim'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->claiming_amount, $currency); ?></p>
                        
         </div>
      </div>
   </div>
   		   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
            <p class="text-uppercase text-muted bold"><?php echo _l('project_overview_expenses'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount')), $currency); ?></p>
           
         </div>
      </div>
   </div>
   		   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
              <p class="text-uppercase text-success bold"><?php echo _l('total_amount'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->claiming_amount+sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount')), $currency); ?></p>
           
         </div>
      </div>
   </div>
   		   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
             <p class="text-uppercase text-danger bold"><?php echo _l('Execution'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->execution_amount, $currency); ?></p>
           
         </div>
      </div>
   </div>
    <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
              <p class="text-uppercase text-muted bold"><?php echo _l('judgement'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->judgement_amount, $currency); ?></p>
           
         </div>
      </div>
   </div>
   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
              <p class="text-uppercase text-muted bold"><?php echo _l('settlement'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->outstanding_amount, $currency); ?></p>
           
         </div>
      </div>
   </div>
   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
              <p class="text-uppercase text-info"><?php echo _l('project_overview_settle_paid'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status'=>'paid'),'field'=>'amount_received'))+sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'partially_paid'),'field'=>'amount_received')), $currency); ?></p>
           
         </div>
      </div>
   </div>
   <div class="col-md-3 total-column">
      <div class="panel_s">
         <div class="panel-body">
              <p class="text-uppercase text-success bold"><?php echo _l('project_overview_settle_balance'); ?></p>
         <p class="bold font-medium"><?php
			 $total=$project->claiming_amount+sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount'));
			$paid1=sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'paid'),'field'=>'amount_received'));
		 $paid2=sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'partially_paid'),'field'=>'amount_received'));
												  $bal=$total-($paid1+$paid2);
												  echo app_format_money($bal, $currency); ?></p>
           
         </div>
      </div>
   </div>
	</div>
    <!-- Claim Amount and Amount Received -->

     
</div>
</div>
  <?php } } ?>
<?php 
		   if($work_type=='litigation'){
	 $num_rows = total_rows('tblhearings',array('project_id'=>$project->id,'h_instance_id'=>$case_overview->instance_id));
	
	if($num_rows>0){
	//$lasthearing=getlatesthearingbystage($case_overview->instance_id,$project->id);												
	?>
<div class="row mtop25">
<div class="col-md-12">
  <div class="panel-heading stageover bold"><?php echo _l('hearings'); ?></div>

       
          <?php  $this->load->view('admin/projects/project_hearing_overview'); ?>
       
   
    </div>
</div>
<?php } } ?>

         <div class="row hide">
         <?php if(count($court_instances) != 0){?>
      <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
         <p class="text-uppercase bold text-dark font-medium">
           &emsp; <a class="" data-toggle="collapse" href="#lcourtinstance" role="button" aria-expanded="false" aria-controls="collapseExample" style="color: green"><?php echo _l('court_instance_details'); ?></a>
         </p>
         <div class="collapse in" id="lcourtinstance">
             <div class="clearfix"></div>
            
    <div class="col-md-12"  style="height:340px;overflow-y:scroll;">
     
  <table class="table table-bordered" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
      <th><?php echo _l('court_instance'); ?></th> 
       <th><?php echo _l('case_no'); ?></th>
        <th><?php echo _l('case_nature'); ?></th>
        <th><?php echo _l('hearing_court'); ?></th>
         <th><?php echo _l('claiming_amount'); ?></th> 
         <th><?php echo _l('judgement_amount'); ?></th>    
              
    </tr>
  </thead>
  <tbody>
    <?php //if(isset($scope)){
      foreach ($court_instances as $row_) { ?>
        <tr>
       
        <td><?=$row_['instance_name']?></td>
         <td><?=$row_['case_number']?></td>
          <td><?=$row_['case_nature_name']?></td>
           <td><?=$row_['courtname']?></td>
            <td><?=$row_['claiming_amount']?></td>
              <td><?php if($row_['instance_id']!=5)echo $row_['execution_amount']?></td>
        
              
       
      </tr>
      <?php }//} ?>  
  </tbody>
 </table>
						 </div></div></div>
         </div>
      </div> 
   <?php } ?>
   </div>
	



</div>
<div class="col-md-5 project-overview-right">
  			 <div class="row">
				 <?php if($work_type=='litigation'){?>
				  <div class="col-md-12">
        <div class="panel_s panel-info">
				 <div class="panel-heading stageover bold">
				
			
		     <?php echo _l('lateststage_information');?>
	 
	<?php  if($case_overview->instance_id==$project->project_stage && $case_overview->stage_status==0){ ?>
	   <a href="javascript:void(0);" onclick="init_court_instance(<?=$case_overview->id?>);" class="btn btn-info  pull-right" > <i class="fa fa-edit" aria-hidden="true"></i> <?php echo _l('edit_stage'); ?><br> </a>
			 <?php } ?>
		  
				  
		</div> 
  <div class="panel-body no-radius">
	<table class="table no-margin project-overview-table">
                <tbody>
					<tr>
   					 <td class="bold" style="border-width:4px;color:white;font-size: 14px;" bgcolor= "#0d3c61"><?php echo _l('project_stage'); ?></td>
   					 <td class="bold" style="border-width:4px;color:white;font-size: 14px;" bgcolor= "#0d3c61"><?php echo _l(get_court_instance_name_by_id($project->project_stage)); ?></td>
 					 </tr>
                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('e_request_no'); ?></td>
                        <td class="bold"><?php echo $project->current_application_no;?></td>
						 </tr>
                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('application_date'); ?></td>
                        <td class="bold"><?php echo _d($case_overview->stage_applicationdt); ?></td>
			        </tr>

                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('casediary_casenumber'); ?></td>
                        <td class="bold"><?php echo $case_overview->case_number?></td>
						 </tr>
                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('date_of_registration'); ?></td>
                        <td class="bold"><?php echo _d($case_overview->stage_registrationdt); ?></td>
			        </tr>

                  
					<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('case_nature'); ?></td>
                        <td class="bold"><?= get_nature_of_case_by_id($case_overview->instance_casenature)?></td>
			        </tr>
				<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('hearing_court'); ?></td>
                        <td class="bold"><?=get_court_name($case_overview->court_id)?></td>
						</tr>
				
		 <?php if($case_overview->stage_status==1){?>
					<?php 
												
		  $hearingdata=get_judgedhearing_details($project->id,$case_overview->instance_id,$case_overview->stage_status);
				
				if($hearingdata!=''){?>
		<tr class="project-overview-id">
			<td class="bold" colspan="2"><?php echo _l('judgement_info'); ?></td></tr>
		<tr class="project-overview-id">
     <td class="bold"> <p class="card-text" style="margin:  0 0 4px;"><a href=""><span class="btn btn-success" style="border-radius: 12px;"><?php
							echo $hearingdata->judgement_ruling_status.' - ';?><?php
												   echo _d($hearingdata->judgement_date);?></span></a></p></td>
      <td class="bold" >
			
				  <p class="card-text" style="margin:  0 0 4px;">
					  <?php 
					if($hearingdata->judge_attachment!=''){
					echo '<a class="btn btn-info mleft5" title="'._l('judgement').'" href="' . site_url('uploads/projects/' . $hearingdata->project_id . '/' . $hearingdata->judge_attachment) . '" download><i class="fa fa-download" aria-hidden="true"></i>' . '</a>';
					}?>
					 
					  <span class="btn btn-warning" style="border-radius: 12px;margin-left: 2px;"><?php echo " "
      . (strtotime(date('y-m-d')) - strtotime($hearingdata->judgement_date))/60/60/24 ." Days Left ";?></span>
				</p>
			
	</td>
		</tr>
						<?php } ?>
		<tr class="project-overview-id">
			<td class="bold" colspan="2">
    	<?php $row=get_judgedhearing_details($project->id,$case_overview->instance_id,$case_overview->stage_status);?>
	 <div class="col-md-4">
		 <div class="panel_s">
         <div class="panel-body">
         <p class="text-uppercase text-center"><?php echo _l('judge_date'); ?></p>
         <p class="bold font-medium text-center"><?php echo _d($row->judgement_date); ?></p>
		  <p class="text-uppercase text-center"><?php echo _l('judgement_award'); ?></p>
         <p class="bold font-medium text-center"><?php echo (!empty($row->award))?ucwords($row->award):''; ?></p>
			 </div>
		 </div>
      </div>
			<div class="col-md-8">
				 <div class="panel_s" style="height: 175px;overflow-y: scroll">
         <div class="panel-body">
			 <p class="text-uppercase bold"><?php echo _l('judgement_directions'); ?></p>
		 <p class=" font-medium"><?php echo $row->directions ?></p>	
         <p class="text-uppercase bold"><?php echo _l('summary'); ?></p>
		<p class=" font-medium"><?php echo ucwords($row->judgement_ruling_status).' - '.$row->summary; ?></p>
      </div>
					 </div>
		 </div>

		
				</td>
			        </tr>
<?php } else{?>
		<tr class="project-overview-id">
     <td class="bold"><?php echo _l('next_hearing_date'); ?></td>
      <td class="bold" >
			<?php 
				$hearingdata=getlatesthearingbystage($case_overview->instance_id,$project->id);
				if($hearingdata!=''){?>
				  <p class="card-text" style="margin:  0 0 4px;"><a href="" onclick="init_hearing(<?=$hearingdata->id?>);return false;"><span class="btn btn-success" style="border-radius: 12px;"><?php
							echo $hearingdata->subject.' - ';?><?php
							echo _d($hearingdata->hearing_date);?>
				  </span></a>
				</p>
				<?php }else{?>
					<a href="#" onclick="init_hearing();return false;" title="'._l('not_booked').'" class="btn btn-warning mright15" style="border-radius: 12px;"> <b><?=_l('not_booked')?></b></a>
				<?php } ?>
	</td>
		</tr>
		<tr class="project-overview-id">
					 <td class="bold"><?php echo _l('next_hearing_for'); ?></td>
                        <td class="bold"><?php   
						 $action_statuses=get_judge_rule_status();
						 echo render_select('sale_status_top',$action_statuses,array('id','name'),'','',array(),array(),'no-mbot','',false); ?>
						<div class="pull-right hide mtop10" id="divjudge"><?php echo get_hearing_latest_nextdate($project->id,$case_overview->instance_id) ?></div>
						<div class="pull-right mtop10" id="divtask"><?php if(has_permission('projects','','create') && $project->project_stage==$case_overview->instance_id){ ?>
							  <a href="#" onclick="init_hearing_judgement('undefined',this);return false;" data-type="ruling" class="btn btn-info mbot25"><?php echo _l('set_hearing_ruling'); ?></a>
                        
                        <?php } ?></div>
						</td>
			        </tr>
					<?php } ?>


					<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('court_fees'); ?></td>
                        <td class="bold"><?php echo $case_overview->stage_courtfee ?></td>
						</tr>
					<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('date_court_fees_paid'); ?></td>
                        <td class="bold"><?php echo _d($case_overview->stage_courtfeedt); ?></td>
			        </tr>
					 </tbody>
            </table>
	</div>
	</div>
	</div>
	 <?php } ?>
   			 <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="team-members project-overview-team-members">
   <hr class="hr-panel-heading project-area-separation" />
   <?php if(has_permission('projects','','edit')){ ?>
   <div class="inline-block pull-right mright10 project-member-settings" data-toggle="tooltip" data-title="<?php echo _l('add_edit_members'); ?>">
      <a href="#" data-toggle="modal" class="pull-right" data-target="#add-edit-members"><i class="fa fa-cog"></i></a>
   </div>
   <?php } ?>
   <p class="bold font-size-14 project-info" style="color: #FF1493">
      <?php echo _l('project_members'); ?>
   </p>
   <div class="clearfix"></div>
   <?php
   if(count($members) == 0){
      echo '<p class="text-muted mtop10 no-mbot">'._l('no_project_members').'</p>';
   }
   foreach($members as $member){ ?>
   <div class="media">
      <div class="media-left">
         <a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>">
            <?php echo staff_profile_image($member['staff_id'],array('staff-profile-image-small','media-object')); ?>
         </a>
      </div>
      <div class="media-body">
         <?php if(has_permission('projects','','edit')){ ?>
         <a href="<?php echo admin_url('projects/remove_team_member/'.$project->id.'/'.$member['staff_id']); ?>" class="pull-right text-danger _delete"><i class="fa fa fa-times"></i></a>
         <?php } ?>
         <h5 class="media-heading mtop5"><a href="<?php echo admin_url('profile/'.$member["staff_id"]); ?>"><?php echo get_staff_full_name($member['staff_id']); ?></a>
            <?php if(has_permission('projects','','create') || $member['staff_id'] == get_staff_user_id()){ ?>
            <br /><small class="text-muted"><?php echo _l('total_logged_hours_by_staff') .': '.seconds_to_time_format($member['total_logged_time']); ?></small>
            <?php } ?>
         </h5>
      </div>
   </div>
   <?php } ?>
	<?php 
		   if($work_type=='litigation'){?>
   <div class="tc-content project-overview-description">
   <hr class="hr-panel-heading project-area-separation" />
   <div class="col-md-6">
   <p class="bold font-size-14 project-info" style="color: #FF1493"><?php echo _l('lawyer_attending'); ?></p>
   <?php if(count($asslawyers) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_projectlawyer') . '</p>';
   }
	 foreach($asslawyers as $plawyer){
		   echo '<a href="#"><b>'.get_staff_full_name($plawyer['assigneeid']).'</b></a><br>';
	   }?>
	   <div class="clearfix"></div>
	   </div>
	   <div class="col-md-6">
	 
   <p class="bold font-size-14 project-info" style="color: #FF1493"><?php echo _l('legal_coordinator'); ?></p>
   <?php if(count($legals) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_projectlegal') . '</p>';
   }
	  
	 foreach($legals as $plegal){
		   echo '<a href="#"><b>'.get_staff_full_name($plegal['legal_ids']).'</b></a><br>';
	   }?>
	   </div>
</div>
<?php } ?>
   <div class="clearfix"></div>
       <hr class="hr-panel-heading project-area-separation" />


</div>
			</div></div></div></div>

   <!----- Latest Update Section---------------->   
    <div class="panel_s panel-info">
                     <div class="panel-body">
   <div class="row">
      <div class="col-md-12">
         <p class="text-uppercase bold text-dark font-medium">
            <a class="" data-toggle="collapse" href="#lcupdate" role="button" aria-expanded="false" aria-controls="collapseExample"><?php echo _l('latest_case_details_upadte'); ?></a>
         </p>
         <div class="collapse in" id="lcupdate">
            <?php echo get_case_latest_update($project->id); ?>
         </div>
      </div> 
   </div>
   </div> 
   </div>
   <div class="panel_s panel-info hide">
                     <div class="panel-body">
   <div class="row">
      <div class="col-md-12">
         <p class="text-uppercase bold text-dark font-medium">
            <a  data-toggle="collapse" href="#lhupdate" role="button" aria-expanded="false" aria-controls="collapseExample"><?php echo _l('latest_hearings_upadte'); ?></a>
         </p>
         <div class="collapse in" id="lhupdate">
            <?php echo get_hearing_latest_update($project->id); ?>
         </div>
      </div> 
   </div>
		</div>
	</div>
   
<!------------------------------------------->
  <!-- <div class="panel_s panel-info">
                     <div class="panel-body">
   <div class="row">
      <div class="col-md-<?php echo ($project->deadline ? 6 : 12); ?> project-progress-bars">
         <div class="row">
           <div class="project-overview-open-tasks">
            <div class="col-md-9">
               <p class="text-uppercase bold text-dark font-medium">
                  <span dir="ltr"><?php echo $tasks_not_completed; ?> / <?php echo $total_tasks; ?></span>
                  <?php //echo _l('project_open_tasks'); ?>
               </p>
               <p class="text-muted bold"><?php echo $tasks_not_completed_progress; ?>%</p>
            </div>
            <div class="col-md-3 text-right">
               <i class="fa fa-check-circle<?php if($tasks_not_completed_progress >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
            </div>
            <div class="col-md-12 mtop5">
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $tasks_not_completed_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
                  </div>
               </div>
            </div>
      </div>
   </div>
	   </div>
						 </div></div></div>-->
						  <div class="panel_s panel-info">
                     <div class="panel-body">
   <div class="row">
      <div class="col-md-<?php echo ($project->deadline ? 6 : 12); ?> project-progress-bars">
         <div class="row">
           <div class="project-overview-open-tasks">
            <div class="col-md-9">
               <p class="text-uppercase bold text-dark font-medium">
                  <span dir="ltr"><?php echo $tasks_not_completed; ?> / <?php echo $total_tasks; ?></span>
                  <?php echo _l('project_open_tasks'); ?>
               </p>
               <p class="text-muted bold"><?php echo $tasks_not_completed_progress; ?>%</p>
            </div>
            <div class="col-md-3 text-right">
               <i class="fa fa-check-circle<?php if($tasks_not_completed_progress >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
            </div>
            <div class="col-md-12 mtop5">
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar progress-bar-success no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $tasks_not_completed_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php if($project->deadline){ ?>
   <div class="col-md-6 project-progress-bars project-overview-days-left">
      <div class="row">
         <div class="col-md-9">
            <p class="text-uppercase bold text-dark font-medium">
               <span dir="ltr"><?php echo $project_days_left; ?> / <?php echo $project_total_days; ?></span>
               <?php echo _l('project_days_left'); ?>
            </p>
            <p class="text-muted bold"><?php echo $project_time_left_percent; ?>%</p>
         </div>
         <div class="col-md-3 text-right">
            <i class="fa fa-calendar-check-o<?php if($project_time_left_percent >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
         </div>
         <div class="col-md-12 mtop5">
            <div class="progress no-margin progress-bar-mini">
               <div class="progress-bar<?php if($project_time_left_percent == 0){echo ' progress-bar-warning ';} else { echo ' progress-bar-success ';} ?>no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $project_time_left_percent; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $project_time_left_percent; ?>">
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
</div>
							  </div></div>


<?php if(has_permission('projects','','create')) { ?>

<div class="row">
   <?php if($project->billing_type == 3 || $project->billing_type == 2){ ?>
   <div class="col-md-12 project-overview-logged-hours-finance">
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->total_logged_time_by_billing_type($project->id);
         ?>
         <p class="text-uppercase text-muted"><?php echo _l('project_overview_logged_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_billable_time($project->id);
         ?>
         <p class="text-uppercase text-info"><?php echo _l('project_overview_billable_hours'); ?> <span class="bold"><?php echo $data['logged_time'] ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_billed_time($project->id);
         ?>
         <p class="text-uppercase text-success"><?php echo _l('project_overview_billed_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="col-md-3">
         <?php
         $data = $this->projects_model->data_unbilled_time($project->id);
         ?>
         <p class="text-uppercase text-danger"><?php echo _l('project_overview_unbilled_hours'); ?> <span class="bold"><?php echo $data['logged_time']; ?></span></p>
         <p class="bold font-medium"><?php echo app_format_money($data['total_money'], $currency); ?></p>
      </div>
      <div class="clearfix"></div>
    
   </div>
   <?php } ?>
</div>

<div class="row">
 <!--  <div class="col-md-12 project-overview-expenses-finance">
     
      <div class="col-md-3">
         <p class="text-uppercase text-info"><?php echo _l('project_overview_claim'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->claiming_amount, $currency); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-muted"><?php echo _l('project_overview_expenses'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money(sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount')), $currency); ?></p>
      </div>
     
      <div class="col-md-3">
         <p class="text-uppercase text-success"><?php echo _l('total_amount'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->claiming_amount+sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount')), $currency); ?></p>
      </div>
      <div class="col-md-3">
         <p class="text-uppercase text-danger"><?php echo _l('Execution'); ?></p>
         <p class="bold font-medium"><?php echo app_format_money($project->execution_amount, $currency); ?></p>
      </div
   </div>
    <div class="clearfix"></div>
      <hr class="hr-panel-heading" />
</div>-->

	</div>
<?php } ?>
<?php if(count($court_order)>0){?>
<div class="project-overview-timesheets-chart">
   <div class="panel_s panel-info">
                     <div class="panel-body">
  
         <p class="text-uppercase bold text-dark font-medium">
           &emsp; <a class="" data-toggle="collapse" href="#lcourtupdate" role="button" aria-expanded="false" aria-controls="collapseExample" style="color: green"><?php echo _l('project_court_attach_grant'); ?></a>
         </p>
         <div class="collapse in" id="lcourtupdate">
             <div class="clearfix"></div>
            
    <div class="col-md-12">
     <div class="panel_s panel-info">
                     <div class="panel-body">
  <table class="table table-bordered" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
      <th><?php echo _l('corder_type'); ?></th> 
       <th><?php echo _l('corder_date'); ?></th> 
         <th><?php echo _l('corder_amount'); ?></th> 
          <th><?php echo _l('status'); ?></th>       
              
    </tr>
  </thead>
  <tbody>
    <?php //if(isset($scope)){
      foreach ($court_order as $row_) { ?>
        <tr>
       
        <td><?=get_document_type_name($row_['documentid']);?></td>
         <td><?=_d($row_['order_date'])?></td>
			<td><?=app_format_money($row_['corder_amount'], $currency)?></td>
      <td><img  style="width:40px;height:40px;" src=<?=base_url('assets/images/checked.jpg')?> /></td>
       
       
      </tr>
      <?php }//} ?>  
  </tbody>
 </table>
						 </div></div></div>
         </div>
		</div></div>
	   

   </div>
  <?php } ?>
   <!-- <div class="dropdown">
     <h3><?php echo _l('project_court_attach'); ?></h3>
     <a href="#" class="dropdown-toggle" type="button" id="dropdownMenuProjectLoggedTime" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
         <?php if(!$this->input->get('overview_chart')){
            echo _l('this_week');
         } else {
            echo _l($this->input->get('overview_chart'));
         }
         ?>
         <span class="caret"></span>
      </a>
      <ul class="dropdown-menu" aria-labelledby="dropdownMenuProjectLoggedTime">
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=this_week'); ?>"><?php echo _l('this_week'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=last_week'); ?>"><?php echo _l('last_week'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=this_month'); ?>"><?php echo _l('this_month'); ?></a></li>
         <li><a href="<?php echo admin_url('projects/view/'.$project->id.'?group=project_overview&overview_chart=last_month'); ?>"><?php echo _l('last_month'); ?></a></li>
      </ul>
   </div>-->

  <!-- <canvas id="timesheetsChart" style="max-height:300px;" width="300" height="300"></canvas>-->
	
	         <?php 
	
				if($project->ip_logo!='' && $project->case_type=='intellectual_property'){?>
	<div class="panel-heading stageover bold"><?php echo _l('logo_preview'); ?></div>
      <div class="panel_s">
                    <div class="panel-body">
					
					  <div class="mtop15 col-md-6">
				 <?php $path = get_upload_path_by_type('project') . $project->id . '/'. $project->ip_logo;
				   $logopath = base_url('uploads/projects/'.$project->id.'/'.$project->ip_logo); ?>
				
                  <img src="<?=$logopath?>" style="max-width: 100%;" ><br><?php echo $project->ip_logo; ?>
                   <?php if(file_exists($path)){?>
            <a href="<?php echo site_url('download/downloadlogofile/'.$project->id.'/'.$project->ip_logo); ?>" class="btn btn-info btn-icon"><i class="fa fa-download"></i></a>
						  <?php } ?>
            
							   </div> 
						<?php } ?>
						<?php  $total_attach = total_rows(db_prefix().'project_files',
                          array(
                           'project_id'=>$project->id,'final_doc'=>1
                         )
                        );
												if($total_attach>0){
													$finalogo=$this->db->order_by('id', 'desc')->limit(1)->select('file_name')->from('tblproject_files')->where('project_id', $project->id)->where('final_doc', 1)->get()->row()->file_name;
													?>
												
							  <div class="mtop15 col-md-6">
							 <p class="bold mtop25"><?php echo _l('finallogo_preview'); ?></p>	  
				 <?php $path = get_upload_path_by_type('project') . $project->id . '/'. $finalogo;
				   $logopath1 = base_url('uploads/projects/'.$project->id.'/'.$finalogo); 
								  if(is_image($path)){?>
				
                  <img src="<?=$logopath1?>" style="max-width: 100%;" >
								  <?php } ?>
								  <br><?php echo $finalogo; ?>
                   <?php if(file_exists($path)){?>
            <a href="<?php echo site_url('download/downloadlogofile/'.$project->id.'/'.$finalogo); ?>" class="btn btn-info btn-icon"><i class="fa fa-download"></i></a>
            
							   </div>
						
		  </div></div>
              	
				<?php }} ?>
</div>
    
     
</div>
<div class="row mtop25">
	
<?php 	//$stages=$this->db->select('tblcase_details.instance_id as instanceid,tblcase_details.details_type as detailtype')->from('tblcase_details')->where('project_id', $project->id)->get()->result_array(); 
  
	$i=1;
 if(count($stages)>0){
		?>
	<div class="panel-heading stageover bold"><?php echo _l('case_history'); ?></div>
      <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
									<?php 
	 						foreach($stages as $stage){
								if($project->project_stage !=$stage['instance_id']){
								$key=$stage['detailtype'];
								?>
                                    <li role="presentation" class="<?php if($key == $group1){echo 'active ';} ?>">
                                        <a href="#tab_<?php echo $key; ?>" aria-controls="tab_<?php echo $key; ?>" role="tab" data-toggle="tab" style="color: white;background-color:#0d3c61;font-size: 12px;text-transform: uppercase;">
                                            <?php echo _l($key); ?>
                                        </a>
                                    </li>
									<?php }} ?>
                                   
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content tw-mt-3">
							<?php 
	 						foreach($stages as $stage){
								if($project->project_stage !=$stage['instance_id']){
								$key=$stage['detailtype'];
								?>
                            <div role="tabpanel" class="tab-pane <?php if($key == $group1){echo 'active ';} ?>" id="tab_<?php echo $key; ?>">
							<div class="col-md-6">
	

  <div class="panel-body no-radius">
	<table class="table no-margin project-overview-table">
                <tbody>
					
                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('e_request_no'); ?></td>
                        <td class="bold"><?php echo $stage['stage_requestno'];?></td>
						 </tr>
                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('application_date'); ?></td>
                        <td class="bold"><?php echo _d($stage['stage_applicationdt']); ?></td>
			        </tr>

                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('casediary_casenumber'); ?></td>
                        <td class="bold"><?php echo $stage['case_number']?></td>
						 </tr>
                    <tr class="project-overview-id">
                        <td class="bold"><?php echo _l('date_of_registration'); ?></td>
                        <td class="bold"><?php echo _d($stage['stage_registrationdt']); ?></td>
			        </tr>

                 
					<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('case_nature'); ?></td>
                        <td class="bold"><?=$stage['case_nature_name']?></td>
			        </tr>
				<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('hearing_court'); ?></td>
                        <td class="bold"><?=$stage['courtname']?></td>
						</tr>
			
					<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('court_fees'); ?></td>
                        <td class="bold"><?php echo $case_overview->stage_courtfee ?></td>
						</tr>
					<tr class="project-overview-id">
                        <td class="bold"><?php echo _l('date_court_fees_paid'); ?></td>
                        <td class="bold"><?php echo _d($case_overview->stage_courtfeedt); ?></td>
			        </tr>
					 </tbody>
            </table>
	</div>
	
	</div>	
								 
                                  
    <div class="col-md-6">
		<div class="row">
			
		 <?php if($stage['stage_status']==1){?>
			
    	<?php $row=get_judgedhearing_details($project->id,$stage['instance_id'],$stage['stage_status']);?>

		<tr class="project-overview-id">
			<td class="bold" colspan="2">
		 <div class="col-md-4">
		 <div class="panel_s">
         <div class="panel-body">
         <p class="text-uppercase text-center"><?php echo _l('judge_date'); ?></p>
         <p class="bold font-medium text-center"><?php echo _d($row->judgement_date); ?></p>
		  <p class="text-uppercase text-center"><?php echo _l('judgement_award'); ?></p>
         <p class="bold font-medium text-center"><?php echo (!empty($row->award))?ucwords($row->award):''; ?></p>
			  <?php 
					if($row->judge_attachment!=''){
					echo '<a class="btn btn-info mleft5" title="'._l('judgement').'" href="' . site_url('uploads/projects/' . $row->project_id . '/' . $row->judge_attachment) . '" download><i class="fa fa-download" aria-hidden="true"></i>' . '</a>';
					}?>
			 </div>
		 </div>
      </div>
			<div class="col-md-8">
				 <div class="panel_s" style="height: 175px;overflow-y: scroll">
         <div class="panel-body">
			 <p class="text-uppercase bold"><?php echo _l('judgement_directions'); ?></p>
		 <p class=" font-medium"><?php echo $row->directions ?></p>	
         <p class="text-uppercase bold"><?php echo _l('summary'); ?></p>
		<p class=" font-medium"><?php echo ucwords($row->judgement_ruling_status).' - '.$row->summary; ?></p>
      </div>
					 </div>
		 </div>
			</td></tr>
<?php } ?>

</div>
          <div class="stagehearing-table">
  <?php  $hearings   = $this->hearing_model->get_hearing_bystage('',['project_id'=>$stage['project_id'],'h_instance_id'=>$stage['instance_id']]);
							
								if(count($hearings)>0){
								?>
      <table class="table dt-table scroll-responsive table-<?=$stage['id']?>-hearings" data-order-col="1" data-order-type="asc">
        <thead>
    <tr> 
      <th><?php echo _l('hearing_date'); ?></th>
     
      <th><?php echo _l('hearing_list_subject'); ?></th>
      <th><?php echo _l('court_fee'); ?></th>
      <th><?php echo _l('casediary_casenumber'); ?></th>
      <th><?php echo _l('assigned_lawyer'); ?></th>
      <th><?php echo _l('court_decision'); ?></th>
	 <th><?php echo _l('comments'); ?></th>
	
    </tr>
  </thead>
  <tbody>
	  <?php foreach($hearings as $hearing){?>
	  <tr>
		<td><?=$hearing['hearing_date'];?></td>	
	  <td><?=$hearing['subject'];?></td>
	  <td><?=$hearing['court_fee'];?></td>	
	  <td><?=$hearing['court_no'];?></td>	
	  <td><?=$hearing['lawyer_id'];?></td>	
	  <td><?=$hearing['proceedings'];?></td>
	  
	  <td><?=get_casedetails_complete_update($hearing['project_id'],$hearing['id']);?></td>	
	</tr>
	  <?php }?>
 </tbody>
 </table>
			  <?php } ?>
</div>
	
     </div>

							</div>
							<?php }} ?>
						</div>
		  </div>
	</div>
	<?php } ?>	
		  </div>
<?php if(get_project_files_attached1($project->id)){?>
<div class="row">
     <div class="panel_s panel-info">
                     <div class="panel-body">
     
		  
   <?php echo get_project_files_attached($project->id); ?> 
   
						 </div> </div></div>
						 <?php } ?>
</div>
<div class="modal fade" id="add-edit-members" tabindex="-1" role="dialog">
   <div class="modal-dialog">
      <?php echo form_open(admin_url('projects/add_edit_members/'.$project->id)); ?>
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('project_members'); ?></h4>
         </div>
         <div class="modal-body">
            <?php
            $selected = array();
            foreach($members as $member){
              array_push($selected,$member['staff_id']);
           }
           echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
           ?>
        </div>
        <div class="modal-footer">
         <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
         <button type="submit" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
      </div>
   </div>
   <!-- /.modal-content -->
   <?php echo form_close(); ?>
</div>
<!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php if(isset($project_overview_chart)){ ?>
<script>
   var project_overview_chart = <?php echo json_encode($project_overview_chart); ?>;
</script>
<?php } ?>
