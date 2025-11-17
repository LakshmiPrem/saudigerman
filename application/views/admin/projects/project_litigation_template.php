        <?php $case_type = ( isset( $case_type ) ? $case_type : 'other_projects' );

	$case1 = ( isset( $project ) ? $project->case_type : $case_type );
	?>
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
<!-----------------------------added fields---------------------------------------------------------------------->
                        <?php $position_arr = get_client_positions(); ?>
            <?php #########  Client Position    ###############?>
            <div class="col-md-6  border-right">
				<?php
             $selected = (isset($project) ? $project->client_position  : '');
							if($selected==''){
							  $selected = (isset($client_posi) ? $client_posi : '1');}
				
            echo render_select('client_position',$position_arr,array('id','name'),'client_position',$selected);?>
          </div>

          <?php #########  Opposite Party Position    ###############?>
            <div class="col-md-6 border-right">
            
            <?php  $selected = (isset($project) ? $project->oppositeparty_position : '');
								if($selected==''){
							  $selected = (isset($oppo_posi) ? $oppo_posi : '2');  }
            echo render_select('oppositeparty_position',$position_arr,array('id','name'),'opposite_party_position',$selected);?>
          </div>
		  <div class="col-md-6 border-right">
                        
                         <?php

                         $selected = [];
				
                       if(isset($project->addition_client)){  
						   	 $addclients=json_decode($project->additional_client);
                             foreach ($addclients as $value) {

                                array_push($selected,$value);

                            }

                        }
								

                        echo render_select('additional_client[]',$other_clients,array('userid','company'),'additional_clients',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
								 
                        ?>
 
                    </div>
			  <div class="col-md-6 border-right">
                        
                         <?php

                         $selected1 = [];
				
                       if(isset($project->additional_party)){  
						   	 $addpartys=json_decode($project->additional_party);
                             foreach ($addpartys as $value1) {

                                array_push($selected1,$value1);

                            }

                        }
								

                        echo render_select('additional_party[]',$oppositeparty_names,array('id','name'),'additional_partys',$selected1,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
								 
                        ?>
 
                    </div>
          <div class="col-md-6 border-right <?php if(isset($project)&& !empty($project->project_stage)) echo 'hide';?>">
            
            <?php  $selected = (isset($project) ? $project->project_stage : '');
            echo render_select('project_stage',$project_stages,array('id','name'),'project_stage',$selected);?>
           </div>

          <div class="col-md-6 border-right <?php if(isset($project)) echo 'hide';?>">
		<?php ####################### Subject ###################################################

    $value = (isset($project) ? $project->current_application_no : ''); ?>
          <?php echo render_input('current_application_no','application_number',$value); ?>

        </div>
 	<div class="col-md-6 border-right <?php if(isset($project)) echo 'hide';?>">
 	 <?php ####################### Date ###################################################
          
            $value = (isset($project) ? _d($project->current_application_date) : _d(date('Y-m-d'))); ?>
            <?php echo render_date_input('current_application_date','application_date',$value); ?>
           </div>
<!-----------------------------added fields---------------------------------------------------------------------->
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
                             <div class="col-md-6">
                             <?php ############## Rack No ################### ?>
                    <?php 
                         $value = (isset($project) ? $project->rack_no : ''); 
                            echo render_input('rack_no','casediary_rack_no',$value,'text'); 
                      

                    ?>
                        </div>
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
                          <div class="col-md-6">
                             <?php ############## Country ################### ?>
                    <?php $selected = (isset($project) ? $project->countryid : '234'); ?>
                        <?php  echo render_select('countryid',$countries,array('country_id','short_name'),'country',$selected,array());?>
                        </div>
                    </div>
    <div class="row">
                    <div class="col-md-6">
                        <?php $value = (isset($project) ? $project->pf_agreement_no : ''); ?>
                        <?php echo render_input('pf_agreement_no','pf_agreement_no',$value); ?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = (isset($project) ? _d($project->pf_agreement_dt) : ''); ?>
                        <?php echo render_date_input('pf_agreement_dt','pf_agreement_dt',$value); ?>
                    </div>
                </div>
   <div class="row">
                   <div class="col-md-6">
                        <?php $value = (isset($project) ? $project->pf_agreement_amount : ''); ?>
                        <?php echo render_input('pf_agreement_amount','pf_agreement_amount',$value); ?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = (isset($project) ? $project->expenses_prefix : ''); ?>
                        <?php echo render_input('expenses_prefix','expenses_prefix',$value); ?>
                    </div>
                  
                    <!--<div class="col-md-6">
                        <?php $value = (isset($project) ? _d($project->deadline) : ''); ?>
                        <?php echo render_date_input('deadline','project_deadline',$value); ?>
                    </div>-->
                </div>