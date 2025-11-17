<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php
		$ps = get_option('client_productkey');
		if(!empty($ps)){
			if(false == $this->encryption->decrypt($ps)){
				$ps = $ps;
			} else {
				$ps = $this->encryption->decrypt($ps);
			}
		}
$expiry_date=!empty(get_option('client_expiry'))?_d(get_option('client_expiry')):_d(date('Y-m-d'));
$last_validate_date=!empty(get_option('client_validatedt'))?_d(get_option('client_validatedt')):_d(date('Y-m-d'));
  ?>
<?php echo render_textarea('settings[client_productkey]','client_productkey',$ps); ?>
<?php echo render_input('settings[client_edition]','client_edition',get_option('client_edition')); ?>
<?php echo render_date_input('settings[client_expiry]','client_expiry',$expiry_date); ?>
<?php echo render_date_input('settings[client_validatedt]','client_validatedt',$last_validate_date); ?>
<hr />
		
<hr />
/*function generateSubscriptionKey($length = 50) {
    // Define the characters to use in the key
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ=*#*';
    $charactersLength = strlen($characters);
    $randomKey = '';

    // Generate the key
    for ($i = 0; $i < $length; $i++) {
        $randomKey .= $characters[rand(0, $charactersLength - 1)];
    }
$this->load->library('encryption');

$subscriptionKey = $randomKey; // Or retrieve securely
$encryptedKey = $this->encryption->encrypt($subscriptionKey);
    return $subscriptionKey;
}*/
		
	


