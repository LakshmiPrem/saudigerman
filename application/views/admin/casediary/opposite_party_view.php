<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">
				<div class="panel_s">
					<div class="col-md-12 no-padding">
						<div class="panel_s">
							<?php echo form_open($this->uri->uri_string()); ?>
							<div class="panel-body">
								<h4 class="no-margin"><?php echo _l('opposite_party'); ?> </h4>
								<hr class="hr-panel-heading" />
								<div class="col-md-4">
									<?php echo render_input('name','opposite_company',$opposite_party->name); ?>
								</div>

								<div class="col-md-4">
									<?php echo render_input('firstname','firstname',$opposite_party->firstname); ?>
								</div>

								<div class="col-md-4">
									<?php echo render_input('lastname','lastname',$opposite_party->lastname); ?>
								</div>

								<div class="col-md-4">
									<div class="form-group select-placeholder f_client_id">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('contract_client_string'); ?></label>
                     <select id="clientid" name="client_id" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php $selected = (isset($opposite_party) ? $opposite_party->client_id : '');
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
								</div>

								<div class="col-md-4">
									<?php echo render_input('email','email',$opposite_party->email); ?>
								</div>

								<div class="col-md-4">
									<?php echo render_input('mobile','mobile',$opposite_party->mobile); ?>
								</div>

								<div class="col-md-4">
									<?php echo render_input('city','city',$opposite_party->city); ?>
								</div>

								<div class="col-md-4">
									<?php echo render_textarea('address','address',$opposite_party->address,array('rows'=>7)); ?>
								</div>


								
					
								
								
								
								
								
								
								<div class="btn-bottom-toolbar text-right">
									<button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
								</div>
							</div>
							<?php echo form_close(); ?>
						</div>
					</div>
				</div>
			</div>
			</div>
				<div class="col-md-12">
					<div class="btn-bottom-pusher"></div>
				</div>
			</div>
			<?php init_tail(); ?>
			<script>
				$(function(){
					appValidateForm($('form'),{ amount:'required', date:'required' });
				});
			</script>
		</body>
		</html>
