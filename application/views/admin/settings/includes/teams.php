<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo render_input('settings[teams_api_tenantid]','teams_api_tenantid',get_option('teams_api_tenantid')); ?>
<?php echo render_input('settings[teams_api_clientid]','teams_api_clientid',get_option('teams_api_clientid')); ?>
<?php echo render_input('settings[teams_api_clientsecret]','teams_api_clientsecret',get_option('teams_api_clientsecret')); ?>
<?php echo render_input('settings[teams_api_username]','teams_api_username',get_option('teams_api_username')); ?>
<?php echo render_input('settings[teams_api_password]','teams_api_password',get_option('teams_api_password')); ?>

<hr />
<?php render_yes_no_option('enable_teams_integration','enable_teams_integration'); ?>	
		

