<?php defined('BASEPATH') or exit('No direct script access allowed'); ?> 
 <?php
if ( isset( $client ) ) {
	?>
	<h4 class="customer-profile-group-heading">
		<?php echo _l('overview'); ?>
		
	   <a href="<?php echo admin_url('clients/export_client_data/'.$client->userid); ?>" target="_blank" class="btn btn-info pull-right text-center  mleft5"><?php echo _l('export_company'); ?></a>
		  
		
	</h4>
	<div class="row">
		<div class="col-md-8 border-right project-overview-left">
			<div class="panel_s panel-info">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table no-margin project-overview-table">
								<tbody>

									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_company'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->company; ?>
												
											</a>
										</td>
									</tr>
									<?php if(!empty($client->parent_company)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('parent_company'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo get_company_name($client->parent_company); ?>
												
											</a>
										</td>
									</tr>
								<?php } ?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_address'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->address; ?>

											</a>
										</td>
									</tr>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('clients_country'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo get_countryproject_name($client->country); ?>

											</a>
										</td>
									</tr>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_city'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->city; ?>

											</a>
										</td>
									</tr>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_state'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->state; ?>

											</a>
										</td>
									</tr>
									
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_phonenumber'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->phonenumber; ?>

											</a>
										</td>
									</tr>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_website'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->website; ?>

											</a>
										</td>
									</tr>
									<?php if(count($client_owners)>0){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_owner'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php 
																	  foreach($client_owners as $owner){
																	  echo $owner['firstname'].' '.$owner['lastname'].'<br>';
																	  } ?>

											</a>
										</td>
								</tr>
								<?php } ?>
										<?php if(count($client_managers)>0){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_manager'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php 
																	  foreach($client_managers as $owner1){
																	  echo $owner1['firstname'].' '.$owner1['lastname'].'<br>';
																	  } ?>

											</a>
										</td>
								</tr>
								<?php } ?>
										<?php if(count($client_directors)>0){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_director'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php 
																	  foreach($client_directors as $owner11){
																	  echo $owner11['firstname'].' '.$owner11['lastname'].'<br>';
																	  } ?>

											</a>
										</td>
								</tr>
								<?php } ?>
										<?php if(count($client_secretarys)>0){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_secretary'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php 
																	  foreach($client_secretarys as $owner12){
																	  echo $owner12['firstname'].' '.$owner12['lastname'].'<br>';
																	  } ?>

											</a>
										</td>
								</tr>
								<?php } ?>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="panel_s panel-info">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table no-margin project-overview-table">
								<tbody>

							
									<?php if(!empty($client->incorporation_date)){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('client_incorporation_date'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo _d($client->clientopen_date); ?>

											</a>
										</td>
									</tr>
								<?php } ?>
									
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('customer_group'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->customerGroups; ?>

											</a>
										</td>
									</tr>
									
									
									 <?php if(!empty($client->service_agent)){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('service_agent'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->service_agent; ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									 <?php if(!empty($client->other_identiyno)){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('other_identityno'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->other_identiyno; ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									 <?php if(!empty($client->tax_regno)){?>
										<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('tax_regno'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->tax_regno; ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									<?php if(!empty($client->default_currency)){?>
				<tr class="project-overview-file-no">
                  <td class="bold"><?php echo _l('invoice_add_edit_currency'); ?></td>
                  <td><?php echo get_currency_namebyid($client->default_currency); ?></td>
               </tr>
				<?php } ?>
									
									
									

								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>
			<hr class="hr-panel-heading"/>
		</div>
		<div class="col-md-4 project-overview-right">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s panel-info">
						<div class="panel-body">
							<div class="team-members project-overview-team-members">
								<div class="tc-content project-overview-description">

									<div class="col-md-12">
										<p class="bold font-size-14 project-info" style="color: #FF1493">
											<?php echo _l('stakeholder'); ?>
										</p>
										<hr class="hr-panel-heading project-area-separation"/>
										<?php if(count($contacts) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_clientcontact') . '</p>';
   }
	 foreach($contacts as $plawyer){
		   echo '<a href="#"><b>'.ucwords($plawyer['title']).' - '. ucwords($plawyer['firstname']).' '.ucwords($plawyer['lastname']).'</b></a><br>';
	   }?>
										<div class="clearfix"></div>
										<hr class="hr-panel-heading project-area-separation"/>
									</div>
									<div class="col-md-12">
										<div class="clearfix"></div>
										<p class="bold font-size-14 project-info" style="color: #FF1493">
											<?php echo _l('shareholders'); ?>
										</p>
										<hr class="hr-panel-heading project-area-separation"/>
										<?php if(count($shareholders) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_clientshareholder') . '</p>';
   }
	  
	 foreach($shareholders as $plegal){
		   echo '<a href="#"><b>'.get_clientshareholdername($plegal['shareholder_id']).' - '.$plegal['share_percentage'].' % </b></a><br>';
	   }?>
									</div>
								</div>
								<div class="clearfix"></div>
								<hr class="hr-panel-heading project-area-separation"/>


							</div>
						</div>
					</div>
				</div>
			</div>

			<!----- Latest Update Section---------------->
			<div class="panel_s panel-info">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<p class="text-uppercase bold text-dark font-medium">
								<a class="" data-toggle="collapse" href="#lcupdate" role="button" aria-expanded="false" aria-controls="collapseExample">
									<?php echo _l('latest_update'); ?>
								</a>
							</p>
							<div class="collapse in" id="lcupdate">
								<?php if(count($user_notes) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_clientupdate') . '</p>';
   }
	  
	 foreach($user_notes as $note){
		   echo '<a href="#"><b>'.$note['description'].' - '._d($note['dateadded']).'</b></a><br>';
	   }?>
							</div>
						</div>
					</div>
				</div>
			</div>
			  <!----- BranchesView Section---------------->   
   <?php if(sizeof($branches)>0){?>
    <div class="panel_s panel-info">
                     <div class="panel-body">
   <div class="row">
      <div class="col-md-12">
         <p class="text-uppercase bold text-dark font-medium">
            <a class="" data-toggle="collapse" href="#lcupdate1" role="button" aria-expanded="false" aria-controls="collapseExample"><?php echo _l('branches'); ?></a>
         </p><hr/>
         <div class="collapse in" id="lcupdate1">
            <div class="col-md-12"  style="font-size:16px;">
   
            <?php foreach($branches as $branch){?>
            	<a style="color:#1446E5;font-weight: bold;" target="_blank" href="<?php echo admin_url(); ?>clients/client/<?php echo $branch['userid']; ?>">
			<?php echo  $branch['company'].'<hr/>';
			 ?>
			</a>
        
	       
			<?php  } ?>
		 </div>
				
         </div>
      </div> 
   </div>
   </div> 
   </div>
   <?php } ?>
	  <!----- Subfile Tree View Section---------------->   
  
		</div>

		
	</div>

	<?php
}
?>
