<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo render_input('settings[ms_tenantid]','settings_ms_tenantid',get_option('ms_tenantid')); ?>
<?php echo render_input('settings[ms_clientid]','settings_ms_clientid',get_option('ms_clientid')); ?>
<?php echo render_input('settings[ms_clientSecret]','settings_ms_clientSecret',get_option('ms_clientSecret')); ?>
<hr />
	
		<i class="fa fa-question-circle pull-left" data-toggle="tooltip" data-title="<?php echo _l('ms_username_help'); ?>"></i>
		<?php echo render_input('settings[ms_username]','settings_ms_username',get_option('ms_username')); ?>
		<?php
		$ps = get_option('ms_password');
		if(!empty($ps)){
			if(false == $this->encryption->decrypt($ps)){
				$ps = $ps;
			} else {
				$ps = $this->encryption->decrypt($ps);
			}
		}
		echo render_input('settings[ms_password]','settings_ms_password',$ps,'password',array('autocomplete'=>'off')); ?>
<hr />
<?php render_yes_no_option('enable_sharepoint','enable_sharepoint'); ?>	
		
	


