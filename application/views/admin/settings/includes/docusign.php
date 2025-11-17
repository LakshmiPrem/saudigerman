<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php echo render_input('settings[docusign_api_accountid]','settings_api_accountid',get_option('docusign_api_accountid')); ?>
<?php echo render_input('settings[docusign_api_interkey]','settings_docusign_integrationkey',get_option('docusign_api_interkey')); ?>
<?php echo render_input('settings[docusign_api_secretkey]','settings_docusign_clientSecret',get_option('docusign_api_secretkey')); ?>
<?php echo render_input('settings[docusign_base_path]','settings_docusign_base_path',get_option('docusign_base_path')); ?>

<hr />
<?php render_yes_no_option('enable_docusign_incontract','enable_docusign_in_contract'); ?>	
<hr/>
<!-- Drafttable Comparison API -->
<?php echo render_input('settings[draftable_authToken]','settings_draftable_authToken',get_option('draftable_authToken')); ?>	
<?php echo render_input('settings[draftable_accountid]','settings_draftable_accountid',get_option('draftable_accountid')); ?>	


