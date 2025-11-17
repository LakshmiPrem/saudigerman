<?php $this->load->view('authentication/includes/my_head.php'); ?>  

  <body  class="">

    <section class="main">

      <div class="col-sm-6 hidden-xs">

        <div>

          

        </div>

      </div>

      <div class="col-sm-6  col-xs-12 log">

        <h2 class="mpadh2"><?php echo _l('admin_auth_login_heading'); ?></h2>

        <center class="mpad">

           <!--img class="img-responsive logo-b" src='<?php echo base_url('assets/login/images/boss-logo.PNG'); ?>'-->

           <div class="company-logo">

            <?php get_company_logo(); ?>


           </div>
           <br>

            <div class="col-sm-12">

                <?php $this->load->view('authentication/includes/alerts'); ?>

                <?php echo form_open($this->uri->uri_string()); ?>

                <?php echo validation_errors('<div class="alert alert-danger text-center">', '</div>'); ?>

      <?php hooks()->do_action('after_admin_login_form_start'); ?>

                <div class="form-group">

                    <input type="email" id="email" name="email" class="form-control" autofocus placeholder="<?php echo _l('admin_auth_login_email'); ?>">

                </div>

                <div class="form-group">

                    <input type="password" id="password" name="password" class="form-control" placeholder="<?php echo _l('admin_auth_login_password'); ?>">

                </div>

         <div class="checkbox">

          <label for="remember">

           <input type="checkbox" id="remember" name="remember"> <?php echo _l('admin_auth_login_remember_me'); ?>

         </label>

       </div>

       <div class="form-group">

        <button type="submit" class="btn btn-info btn-block"><?php echo _l('admin_auth_login_button'); ?></button>

      </div>

      <div class="form-group">

        <a href="<?php echo site_url('authentication/forgot_password'); ?>"><?php echo _l('admin_auth_login_fp'); ?></a>

      </div>

      <?php if(get_option('recaptcha_secret_key') != '' && get_option('recaptcha_site_key') != ''){ ?>

      <div class="g-recaptcha" data-sitekey="<?php echo get_option('recaptcha_site_key'); ?>"></div>

      <?php } ?>

       <?php hooks()->do_action('before_admin_login_form_close'); ?>

      <?php echo form_close(); ?>

            </div>

        </div>

      </center>

    </section>

  </body>

</html>

