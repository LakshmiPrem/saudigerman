<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<style>
        /* Center the download link on the screen */


        .download-container {
         margin-top: 2%;
            text-align: center;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .download-icon {
            font-size: 48px;
            color: #007bff; /* Bootstrap primary color */
            margin-bottom: 10px;
        }

        .download-link {
            font-size: 18px;
            color: #007bff;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        .download-link:hover {
            text-decoration: underline;
        }
    </style>
    <!-- Include Font Awesome for the download icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <div class="mtop15 preview-top-wrapper" style="background-color: #ADD8E6; padding: 20px;">
   <div class="row">
      <div class="col-md-3">
         <div class="mbot30">
            <div class="contract-html-logo">
               <?php echo get_dark_company_logo(); ?>
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
    <div class="download-container pt-5" >
        <!-- File download icon -->
            <!-- ===== BOX 1: Email + Password + OTP ===== -->
    <div style="border: 2px solid #007bff; border-radius: 8px; background: #f8fbff; padding: 25px; width: 50%; margin: 0 auto 20px auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

        <div class="download-icon">
            <i class="fa-solid fa-download"></i>
        </div>
<?php $path =site_url('files/pdf/');  ?>
       
      <div class="email_div" style="text-align:center;">
        <?php $value=isset($file_data->party_uploademail)?$file_data->party_uploademail:"" ?>
  <label for="email" class="control-label !tw-m-0"><?= _l('email'); ?></label>
  <input type="text" id="email" name="email" class="form-control" required autocomplete="off" placeholder="Please provide email id for otp verification" style=" margin:0 auto;" value="<?php echo $value?>">
  <small id="email_error" style="color:red; display:none;">Please enter a valid email address.</small>
</div>
<br>
            <div class="password_div <?php if($file_data->party_uploademail==null){ echo "hide";}  ?> " id="verify_button" style="text-align: center;">
    <label for="password" class="control-label !tw-m-0"><?= _l('enter_opt'); ?></label>
    <input type="password" id="password" name="password" class="form-control" autocomplete="off" placeholder="Please check your mail id for OTP" required style=" margin: 0 auto;">
<input type="hidden" id="correctPassword" value="<?php echo $password; ?>">

</div><br>

<!-- Hidden download link -->
<div class="upload_div" style="display: none;">
    <a href="<?php echo $path . $file_data->id; ?>" onclick="updateFiles('<?php echo $file_data->id; ?>')" class="download-link" download>
        Download <?php //echo htmlspecialchars($file['original_contract_filename']); ?>
    </a>
</div>


<!-- Submit buttons in one line -->
<div style="display: flex; gap: 10px; justify-content: center; align-items: center; margin-top: 10px;">
  <div class="button_check <?php if($file_data->party_uploademail==null){ echo "hide";} ?>" id="verify_button_">
    <a href="#" class="btn btn-success" onclick="checkPassword()"> <?= _l('check'); ?> </a>
  </div>

  <?php if ($file_data->party_uploademail != null) { ?>
  <div class="button_check otp_div" id="otp_button" style="display: none;">
    <a href="#" class="btn btn-success" onclick="get_otp('<?php echo $file_data->id; ?>')">
      <?php echo _l('resend otp'); ?>
    </a>
  </div>

  <script>
    // Show the button after 10 seconds
    setTimeout(() => {
      document.getElementById('otp_button').style.display = 'block';
    }, 10000);
  </script>
<?php } else { ?>
  <div class="button_check otp_div" >
    <a href="#" class="btn btn-success" onclick="get_otp('<?php echo $file_data->id; ?>')">
      <?php echo _l('get otp'); ?>
    </a>
  </div>
<?php } ?>

</div>
</div>
   <!-- ===== BOX 1: Email + Password + OTP ===== -->
    <div style="border: 2px solid #007bff; border-radius: 8px; background: #f8fbff; padding: 25px; width: 50%; margin: 0 auto 20px auto; box-shadow: 0 2px 6px rgba(0,0,0,0.1);">

<div class="download_link " <?php if ($file_data->party_uploademail == null) { ?> style="display: none;" <?php }?>>

 <?php
         echo form_open_multipart(
             site_url('files/upload/' .  $file_data->id.'/'. $file_data->otherparty_hash),
             ['id' => 'contract-form']
         );
         ?>
         <div id="contact-profile-image"
               class="form-group">
               <label for="contract" class="profile-image"><?php echo _l('signed_contract_upload'); ?></label>
               <input type="file" style=" margin:0 auto;" name="agree_attachment" class="form-control" id="contract">
            </div>
            <div >
          <button style="width:50%; margin:0 auto;" type="submit"
          class="btn btn-info"
          autocomplete="off" >
          <?php echo _l('upload'); ?>
      </button>
   </div>
   <?php echo form_close(); ?>
   </div>
   </div>

     
    </div>
<!-- <div class="mtop15 preview-top-wrapper">
   <div class="row">
      <div class="col-md-3">
         <div class="mbot30">
            <div class="contract-html-logo">
              
            </div>
         </div>
      </div>
      <div class="clearfix"></div>
   </div>
   
   
</div> -->
      </div>

      <script>
    // Predefined password from PHP
    // const correctPassword = "<?php echo $password; ?>";
    const otherparty_hash = "<?php echo $file_data->otherparty_hash; ?>"

    function checkPassword() {
        // Get the entered password from the input field
        const correctPassword = document.getElementById('correctPassword').value;
        const enteredPassword = document.getElementById('password').value;

        // Compare the entered password with the correct password
        if (enteredPassword === correctPassword) {
            // If the password matches, show the download link
            document.querySelector('.download_link').style.display = 'block';
             document.querySelector('.upload_div').style.display = 'block';
            
            document.querySelector('.button_check').style.display = 'none';
            document.querySelector('.password_div').style.display = 'none';
             document.querySelector('.email_div').style.display = 'none';
             document.querySelector('.otp_div').style.display = 'none';
             
            
        } else if(enteredPassword ==="") {
            // If the password is incorrect, show an alert
            alert('Password not provided. Please try again.');
        }else{
            alert('Incorrect password. Please try again.');
        }
    }

    function get_otp(id) {
    var email = document.getElementById('email').value.trim();
    var errorMsg = document.getElementById('email_error');
    
    // Simple email regex validation
    var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailPattern.test(email)) {
        errorMsg.style.display = 'block';
        return false; // Stop execution if invalid email
    } else {
        errorMsg.style.display = 'none';
    }

    // Proceed with AJAX only if email is valid
    $.ajax({
        url: site_url + 'files/get_otp/' + id,
        type: 'GET',
        dataType: 'json',
        data: { party_uploademail: email,
            otherparty_hash:otherparty_hash
         },
        success: function(data) {
            // alert();
           document.getElementById("correctPassword").value = data.otp;
            var verifyButton = document.getElementById("verify_button");
            var verifyButton_ = document.getElementById("verify_button_");
    //   alert();
      // Check if it exists, then remove the "hide" class
      if (verifyButton) {
          verifyButton.classList.remove("hide");
      }
      if (verifyButton_) {
          verifyButton_.classList.remove("hide");
      }
            console.log('Files updated successfully:', data);
        },
        error: function(xhr, status, error) {
            console.error('Error updating files:', error);
        }
    });
}

// Example usage:
// updateFiles(123); // Replace 123 with the actual ID
</script>
<?php
   // get_template_part('identity_confirmation_form', array('formData' => form_hidden('action', 'sign_contract')));
   ?>

