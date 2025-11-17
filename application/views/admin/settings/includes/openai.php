<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>

<?php render_yes_no_option('enable_openai','enable_openai'); ?>
<hr />
<?php echo render_input('settings[openai_apikey]','openai_apikey',get_option('openai_apikey')); ?>
<hr />
