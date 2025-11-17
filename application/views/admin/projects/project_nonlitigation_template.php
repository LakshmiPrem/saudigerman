       <?php $case_type = ( isset( $case_type ) ? $case_type : 'other_projects' );

	$case1 = ( isset( $project ) ? $project->case_type : $case_type );
	?> 
  <div class="row <?php if($case1!='intellectual_property')echo 'hide'?>">
                  	          <div class="col-md-6">
                   	         <?php 
          			$ip_types = get_ip_types();
					 $selected = (isset($project) ? $project->ip_category : ''); ?>
        			 <?php echo render_select_with_input_group('ip_category',$ip_types,array('id','name'),'ip_category',$selected,'<a href="#" onclick="new_Ipcategories();return false;"><i class="fa fa-plus"></i></a>'); ?>
        			
						</div>
                   	          <div class="col-md-6" id="ipsubcat">
                   	         <?php 
          			$ip_subtypes = get_ipsub_types();
					 $selected = (isset($project) ? $project->ip_subcategory : ''); ?>
        			 <?php 
							  echo render_select_with_input_group('ip_subcategory',$ip_subtypes,array('id','subcategory_name','category_name','category_id'),'ip_subcategory',$selected,'<a href="#" onclick="new_Ipsubcategories();return false;"><i class="fa fa-plus"></i></a>'); ?>
						</div>
             <div class="col-md-6 hide" id="ipothertext">

                                 <?php $value = (isset($project) ? $project->ip_artwork : ''); ?>

                              <?php echo render_input('ip_artwork','ip_othertext',$value); ?>

                             </div>
</div>
                <?php if(!isset($project)){

                            $case_types = get_case_client_types();
                            $case_type = (isset($case_type) ? $case_type : 'other_projects');
                            $selected = (isset($project) ? $project->case_type : $case_type);?>
                            <div class="hide">
                            <?php echo render_select('case_type',$case_types,array('id','name'),'case_type',$selected);?>
                            </div>
								
                        <?php $selected = (isset($project) ? $project->template_id : ''); ?>
                        <?php  echo render_select('template_id',$casetemplates,array('id','name'),'matter_templates',$selected,array());?>
                         <?php } ?>
	
<div class="row">
						 <div class="col-md-6">
                  <?php $yes_no_arr = [['id'=>'main','name'=>'Main'],['id'=>'submatter','name'=>'Submatter']]  ?>

            	<?php 
	$type1 = (isset($related_matter) ? 'submatter' : 'main');
	$selected = ( isset($project) ? $project->parent_sub : $type1); ?>

           		 <?php echo render_select('parent_sub',$yes_no_arr,array('id','name'),'parent_sub',$selected);?>
				
                
				<div class="main_div hide">
                    <?php  $related_matter = (isset($related_matter) ? $related_matter : '');

                            $selected = (isset($project) ? $project->related_matter : $related_matter);
					?>
           		 <?php echo render_select('related_matter',$related_matters,array('id','name'),'related_matter',$selected);?>
                 </div>
							 </div>
	<div class="form-group select-placeholder col-md-6">
                            <label for="clientid" class="control-label"><?php echo _l('project_customer'); ?></label>
                            <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                             <?php $selected = (isset($project) ? $project->clientid : '');
                             if($selected == ''){
                                 $selected = (isset($customer_id) ? $customer_id: '');
                             }
                             if($selected != ''){
                                $rel_data = get_relation_data('customer',$selected);
                                $rel_val = get_relation_values($rel_data,'customer');
                                echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                            } ?>
                        </select>
                    </div>
                        <div class="col-md-6">
                             <?php ########## Opposite Party ##############  ?>
                          

                            <?php $selected = (isset($project) ? $project->opposite_party : '');
                            
                            echo render_select('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty',$selected);
                            ?>
                          
                        </div>
	  					 <div class="col-md-6">
                             <?php ############## Country ################### ?>
                    <?php $selected = (isset($project) ? $project->countryid : '234'); ?>
                        <?php  echo render_select('countryid',$countries,array('country_id','short_name'),'country',$selected,array());?>
                        </div>
                      <!--  <div class="col-md-6">
                             <?php ############## File No ################### ?>
                    <?php 
                        $next_file_number = get_option('next_file_number');
                        $prefix = get_option('file_no_prefix');
                        if(isset($project->file_no)) {

                            echo render_input('file_no','casediary_file_no',$project->file_no,'text'); 
                        }
                        else{
                             $_file_number = str_pad($next_file_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>
                                <?php  //$value = (isset($project) ? $project->file_no : ''); ?>
                                <?php echo render_input('file_no','casediary_file_no',$prefix.$_file_number,'text');
                        }

                    ?>
                        </div>-->
              
                       <div class="col-md-6 <?php if(get_option('enable_legalrequest_in_case')==0) echo 'hide'; ?>">
                             <?php ############## Ledger Code ################### ?>
                   <?php $value = (isset($project) ? $project->ledger_code : ''); ?>
                        <?php echo render_input('ledger_code','ledger_code',$value); ?>
                        </div>
                        <div class="col-md-6 <?php if(get_option('enable_legalrequest_in_case')==0) echo 'hide'; ?>">
                             <?php ############## Request No Code ################### ?>
                    <?php $selected = (isset($project) ? $project->ticketid : ''); ?>
                        <?php  echo render_select('ticketid',$civils,array('ticketid','subject'),'legal_request',$selected,array());?>
                        </div>
                    
                    </div>
	   
				  <div class="row <?php if($case1!='intellectual_property')echo 'hide'?>">
                  	                            	        
                    	       
                       			 <div class="col-md-6">

                                 <?php $value = (isset($project) ? $project->ip_class : ''); ?>

                      			  <?php echo render_input('ip_class','class',$value); ?>

                       			 </div>
                       			 <div class="col-md-6">

                                 <?php $value = (isset($project) ? $project->ip_filingno : ''); ?>

                      			  <?php echo render_input('ip_filingno','file_no',$value); ?>

                       			 </div>
                       			  <div class="col-md-6">	
  					<?php $value = (isset($project) ? _d($project->ip_filingdt) : _d(date('Y-m-d'))); ?>
             	  <?php echo render_date_input('ip_filingdt','filing_date',$value); ?>
       				</div>
                   <div class="col-md-6">

                                 <?php $value = (isset($project) ? $project->ip_regno : ''); ?>

                      			  <?php echo render_input('ip_regno','registration_no',$value); ?>

                       			 </div>
                       			   <div class="col-md-6">	
  					<?php $value = (isset($project) ? _d($project->ip_registrationdt) :''); ?>
             	  <?php echo render_date_input('ip_registrationdt','ip_registration_date',$value); ?>
       				</div>
					<div class="col-md-6">
					 <?php $ip_statuses = get_ip_statuses();

          $selected = (isset($project) ? $project->ip_status : 'applied'); ?>

         <?php echo render_select('ip_status',$ip_statuses,array('id','name'),'ip_status',$selected); ?>
					  </div>
                    </div>
						
						      <div class="row <?php if($case1!='intellectual_property') echo 'hide'?> mbot20">
                   <div class="col-md-12">
                    <?php $value=( isset($project) ? $project->ip_description : ''); ?>
                    <?php echo render_textarea('ip_description','ip_description',$value); ?>
					    <?php if((isset($project) && $project->ip_logo == NULL) || !isset($project)){ ?>
                     <div class="form-group">
                       <label for="installment_receipt" class="profile-image"><?php echo _l('attach_artwork'); ?></label>
						 <input type="file" name="ip_logo" class="form-control" id="ip_logo">
                        </div>
                     <?php } ?>
                     
                                <?php if(isset($project) && $project->ip_logo!=''){
				  $path = get_upload_path_by_type('project') . $project->id . '/'. $project->ip_logo;
	 $path1 = base_url('uploads/projects/'.$project->id.'/'.$project->ip_logo); ?>
				
                       <div class="form-group">
                        <div class="row">
                           <div class="col-md-6">
							   <div class="col-md-6 text-right">
								 <?php   if(is_image($path1)){?>
								     <img src="<?=$path1?>" style="max-width: 100%;" >
								   <?php } ?>
							   </div>
                            
                              <div class="col-md-6 text-right">
                              <a title="Delete Trade Mark Logo" href="<?php echo admin_url('projects/remove_trade_mark_logo/'.$project->id); ?>"><i class="fa fa-remove fa-2x" style="color: red;"></i></a>
                           </div>
                           </div>
                           <div class="col-md-6 text-left">
                          <?php  	if(file_exists($path)){?>
           		 <a href="<?php echo site_url('download/downloadlogofile/'.$project->id.'/'.$project->ip_logo); ?>" class="btn btn-info btn-icon pull-right mbot10"><?php echo $project->ip_logo.' '; ?><i class="fa fa-download"></i></a>
                  <?php } ?>
                           </div>
                        </div>
                     </div>
					   <?php } ?>
						</div> 
                   
                    </div> 

