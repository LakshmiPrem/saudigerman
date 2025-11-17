<?php defined('BASEPATH') or exit('No direct script access allowed'); ?> 
 <?php
if ( isset( $client ) ) {
	?>
	<h4 class="customer-profile-group-heading">
		<?php echo _l('overview'); ?>
		
	</h4>
	<div class="row">
		<div class="col-md-7 border-right project-overview-left">
			<div class="panel_s panel-info">
				<div class="panel-body">
					<div class="row">
						<div class="col-md-12">
							<table class="table no-margin project-overview-table">
								<tbody>

									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('opposite_company'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->name; ?>
												
											</a>
										</td>
									</tr>
									<?php if(!empty($client->type)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('type_of_other_party'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo ($client->type==1)? _l('individual'):_l('company'); ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									
									<?php if(!empty($client->party_type)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('oppo_party_type'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo get_position_name_by_id($client->party_type); ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									<?php if(!empty($client->company_registration_number)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('company_registration_number'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->company_registration_number; ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									<?php if(!empty($client->company_registration_date)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('company_registration_date'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo _d($client->company_registration_date); ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									
									<?php if(!empty($client->tradelicence)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('trade_licence'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->tradelicence; ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									<?php if(!empty($client->trade_commence_date)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('trade_commence_date'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo _d($client->trade_commence_date); ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									<?php if(!empty($client->trade_expiry)){?>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('trade_expiry_dt'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo _d($client->trade_expiry); ?>

											</a>
										</td>
									</tr>
									<?php } ?>
									
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
											<?php echo _l('contactno'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->mobile; ?>

											</a>
										</td>
									</tr>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('email'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->email; ?>

											</a>
										</td>
									</tr>
									<tr class="project-overview-customer">
										<td class="bold">
											<?php echo _l('address'); ?>
										</td>
										<td>
											<a style="color:#1446E5;font-weight: bold" href="#">
												<?php echo $client->address; ?>

											</a>
										</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
				</div>
			</div>

		</div>
		<div class="col-md-5 project-overview-right">
			<div class="row">
				<div class="col-md-12">
					<div class="panel_s panel-info">
						<div class="panel-body">
							<div class="team-members project-overview-team-members">
								<div class="tc-content project-overview-description">

									<div class="col-md-12">
										<p class="bold font-size-14 project-info" style="color: #FF1493">
											<?php echo _l('employee'); ?>
										</p>
										<hr class="hr-panel-heading project-area-separation"/>
										<?php if(count($party_contacts) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_clientcontact') . '</p>';
   }
	 foreach($party_contacts as $plawyer){
		   echo '<a href="#"><b>'.ucwords($plawyer['contact_name']).' -  '.ucwords($plawyer['designation']).'</b></a><br>';
	   }?>
										<div class="clearfix"></div>
										<hr class="hr-panel-heading project-area-separation"/>
									</div>
		
								</div>
								


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
		</div>

		<div class="clearfix"></div>
	</div>

	<?php
}
?>