<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="<?php echo $locale; ?>" class="notranslate" translate="no">
<head>
    <?php $isRTL = (is_rtl() ? 'true' : 'false'); ?>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="user-scalable=no, width=device-width, initial-scale=1, maximum-scale=1" />

    <title><?php echo isset($title) ? $title : get_option('companyname'); ?></title>

    <?php echo app_compile_css(); ?>
    <?php render_admin_js_variables(); ?>

    <script>
        var totalUnreadNotifications = <?php echo $current_user->total_unread_notifications; ?>,
        proposalsTemplates = <?php echo json_encode(get_proposal_templates()); ?>,
        contractsTemplates = <?php echo json_encode(get_contract_templates()); ?>,
        billingAndShippingFields = ['billing_street','billing_city','billing_state','billing_zip','billing_country','shipping_street','shipping_city','shipping_state','shipping_zip','shipping_country'],
        isRTL = '<?php echo $isRTL; ?>',
        taskid,taskTrackingStatsData,taskAttachmentDropzone,taskCommentAttachmentDropzone,newsFeedDropzone,expensePreviewDropzone,taskTrackingChart,cfh_popover_templates = {},_table_api;
    </script>
    <?php app_admin_head(); ?>
</head>
<body <?php echo admin_body_class(isset($bodyclass) ? $bodyclass : ''); ?><?php if($isRTL === 'true'){ echo 'dir="rtl"';}; ?>>
<?php hooks()->do_action('after_body_start'); ?>
   
 <div id="wrapper">
   <div class="content" style="padding: 0px 15px;">
        <div class="row">
          <div class="col-md-12 ui-sortable" data-container="top-12" style="/* height: 250px; *//* background: white; */ padding: 0;">
            <?php //$this->load->view('admin/app_home/daily_quotes'); ?>
            </div>
           <?php ##########3?>
        </div>
     </div>
     <section>
       <div class="container pedr">
         <div class="col-xs-6" style="
    padding: 0;
">
    <div style="background: white;padding: 12px 15px;border-radius: 8px;margin: 4px;">
     <div>
      <i class="fa fa-tachometer menu-icon" style="font-size: 25px;padding-bottom: 32px;color: #339936;"></i></div>
     <div>
      <a href="<?php echo admin_url('dashboard'); ?>">  <b style="font-size: 14px; font-weight: 600;">Dashboard</b></a>
     </div>
    </div>
</div>

 <div class="col-xs-6" style="
    padding: 0;
">
    <div style="
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    margin: 4px;
">
        <div><i class="fa fa-shopping-cart menu-icon" style="
    font-size: 25px;
    padding-bottom: 32px;
    color: #1b8bf9;
"></i></div>
<div>
    <a href="<?php echo admin_url('invoices'); ?>" > <b style="
    font-size: 14px;
    font-weight: 600;
">Invoices</b>
    </div>
    </div>
</div>


 <div class="col-xs-6" style="
    padding: 0;
">
    <div style="
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    margin: 4px;
">
        <div><i class="fa fa-user menu-icon" style="
    font-size: 25px;
    padding-bottom: 32px;
    color: #efc037;
"></i></div>
<div>
    <a href="<?php echo admin_url('clients'); ?>"> <b style="
    font-size: 14px;
    font-weight: 600;
">Clients</b></a>
    </div>
    </div>
</div>

 <div class="col-xs-6" style="
    padding: 0;
">
    <div style="
    background: white;
    padding: 12px 15px;
    border-radius: 8px;
    margin: 4px;
">
        <div><i class="fa fa-users menu-icon" style="
    font-size: 25px;
    padding-bottom: 32px;
    color: #ff6f00;
"></i></div>
<div>
    <a href="<?php echo admin_url('suppliers'); ?>"> <b style="
    font-size: 14px;
    font-weight: 600;
">Suppliers</b></a>
    </div>
    </div>
</div>

 
       </div>
     </section>
</div>
    
  </body>
  <?php $this->load->view('admin/includes/scripts'); ?>
   <style type="text/css">
     #wrapper{
      background: #edf1f5 !important;
     }
     .ind-nv a{
        padding: 0px 6px;
     }
     .pedr{
      padding: 0 10px !important;
     }
   </style>
  
    
  </body>
</html>
