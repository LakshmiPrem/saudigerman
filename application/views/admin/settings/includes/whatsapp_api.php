<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo render_input('settings[whatsapp_instance_key]','settings_whatsapp_instance_key',get_option('whatsapp_instance_key')); ?>
<?php echo render_input('settings[whatsapp_api_key]','settings_whatsapp_api_key',get_option('whatsapp_api_key')); ?>

<hr />
		<h4><?php echo _l('settings_send_test_whatsapp_heading'); ?></h4>
		<p class="text-muted"><?php echo _l('settings_send_test_whatsapp_subheading'); ?></p>
		<div class="form-group">
	<?php 
 echo render_textarea('settings[whatsapp_instance_msg]','sample_whatsapp_msg',get_option('whatsapp_instance_msg')); ?>
		
			
			
		</div>
		<div class="form-group">
			<div class="input-group">
				<input type="tel" class="form-control" name="test_whatsapp" data-ays-ignore="true" placeholder="<?php echo _l('settings_send_test_whatsapp_string'); ?>">
				<div class="input-group-btn">
					<button type="button" class="btn btn-default test_whatsapp p7" id="test_whatsapp">Test Whatsapp</button>
				</div>
			</div>
		</div>
