<?php init_head(); ?>
<div id="wrapper" class="customer_profile">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <?php if(isset($client) && $client->active == 0){ ?>
            <div class="alert alert-warning">
               <?php echo _l('customer_inactive_message'); ?>
               <br />
               <a href="<?php echo admin_url('opposite_parties/mark_as_active/'.$client->id); ?>"><?php echo _l('mark_as_active'); ?></a>
            </div>
            <?php } ?>
           
            <?php if(isset($client) && (!has_permission('opposite_parties','','view'))){?>
            <div class="alert alert-info">
               <?php echo _l('customer_admin_login_as_client_message',get_staff_full_name(get_staff_user_id())); ?>
            </div>
            <?php } ?>
         </div>
         <?php if($group == 'profile'){ ?>
         <div class="btn-bottom-toolbar btn-toolbar-container-out text-right">
            <button class="btn btn-info only-save customer-form-submiter">
               <?php echo _l( 'submit'); ?>
            </button>
            
         </div>
         <?php } ?>
         <?php if(isset($client)){ ?>
         <div class="col-md-3">
            <div class="panel_s">
               <div class="panel-body customer-profile-tabs">
                  <h4 class="customer-heading-profile bold">
                       <?php if(has_permission('opposite_parties','','delete') || is_admin()){ ?>
                  <div class="btn-group pull-left mright10">
                     <a href="#" class="dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="caret"></span>
                     </a>
                     <ul class="dropdown-menu dropdown-menu-left">
                        
                        <?php if(has_permission('opposite_parties','','delete')){ ?>
                        <li>
                           <a href="<?php echo admin_url('opposite_parties/delete/'.$client->userid); ?>" class="text-danger delete-text _delete"><i class="fa fa-remove"></i> <?php echo _l('delete'); ?>
                           </a>
                        </li>
                        <?php } ?>
                     </ul>
                  </div>
                  <?php } ?>
                  #<?php echo $client->userid . ' ' . $title; ?></h4>
                  <?php $this->load->view('admin/oppositeparties/tabs'); ?>

               </div>
            </div>
         </div>
         <?php } ?>
         <div class="col-md-<?php if(isset($client)){echo 9;} else {echo 12;} ?>">
            <div class="panel_s">
               <div class="panel-body">
                  <?php if(isset($client)){ ?>
                  <?php echo form_hidden( 'isedit'); ?>
                  <?php echo form_hidden( 'userid',$client->userid); ?>
                  <div class="clearfix"></div>
                  <?php } ?>
                  <div>
                     <div class="tab-content">
                        <?php $this->load->view('admin/oppositeparties/groups/'.$group); ?>
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>
      <?php if($group == 'profile'){ ?>
      <div class="btn-bottom-pusher"></div>
      <?php } ?>
   </div>
</div>
<?php init_tail(); ?>
<?php if(isset($client)){ ?>
<script>
   $(function(){
      init_rel_tasks_table(<?php echo $client->userid; ?>,'oppositeparty');
   });
</script>
<?php } ?>
<?php if(!empty($google_api_key) && !empty($client->latitude) && !empty($client->longitude)){ ?>
<script>
   var latitude = '<?php echo $client->latitude; ?>';
   var longitude = '<?php echo $client->longitude; ?>';
   var mapMarkerTitle = '<?php echo $client->debtor_title; ?>';
</script>
<!-- <?php echo app_script('assets/js','map.js'); ?>
<script async defer src="https://maps.googleapis.com/maps/api/js?key=<?php echo $google_api_key; ?>&callback=initMap"></script> -->
<?php } ?>
<?php $this->load->view('admin/oppositeparties/oppositeparty_js'); ?>
 <?php $this->load->view('admin/oppositeparties/modals/kyc_attachment'); ?>
</body>
</html>
 <script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
 