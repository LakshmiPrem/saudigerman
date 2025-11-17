<?php $spath =   base_url('assets/boss/'); ?>

<?php $this->load->view('boss_app/includes/header');?>

<body>
  
<section class="navsection">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-12 full-layer">
          <div class="col-xs-1 menu-bar dispad  clearfix">
            <img src="<?php echo $spath .'images/icons/menu-white.png';?>" class="img-responsive menu-bar">
          </div>
          <div class="col-xs-10 dispad">
            <h2 class="pro-tittle"><?php echo get_option('companyname') ?></h2>
          </div>
        </div>
    </div>
  </div>
</section>
<section class="slide-menu">
  <div class="item col-xs-12 dispad back-btn">
    <i class="fa fa-close"></i>
  </div>
  <div class="item col-xs-12 dispad">
   <a href="<?php echo admin_url('contracts'); ?>"> <i class="fas fa-file-contract"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
    <a href="<?php echo admin_url('contracts'); ?>"><i class="fas fa-ticket-alt"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
    <a href="<?php echo admin_url('clients'); ?>"><i class="fa fa-users"></i></a>
  </div>
  
</section>
<section class="std">
    <!--<div class="container-fluid">
      <div class="form-group">
            <div class="all-slc col-xs-12 dispad">
              <div class="col-xs-7 dispad"><input placeholder="search" type="text" class=" home-search form-control"></div>
               <div class="drp col-xs-3 slt">
                <select class="form-box dispad" id="">
                    <option>Case</option>
                    <option>Clients</option>
                    
                </select>
              </div>
              <i class="col-xs-2 search-ico fa fa-search"></i>
            </div>
          </div>
    </div> -->
</section>
<section class="quick">
  <div class="container-fluid">
    <div class="row">
      <div class="col-xs-6 dispad">
        <div class="co-xs-12 dispad">
          <a href="<?php echo admin_url('contracts'); ?>">
          <center class="ico-box-left">
            <!-- <i class="fas fa-file-contract"></i> -->
            <h1><span class="badge badge-primary"><?php echo $conracts_count_active; ?></span></h1>
            <p>Contracts Active</p>
          </center></a>
        </div>
        <div class="co-xs-12 dispad">
          <a href="<?php echo admin_url('contracts'); ?>"><center class="left-bottom ico-box-left" >
            <!-- <i class="fas fa-file-contract" style="color:white;"></i> -->
            <h1><span class="badge badge-warning"><?php echo $contracts_count_recently_created?></span></h1>
            <p>Contracts Recently Added</p>
          </center></a>
        </div>
         
   <?php
		foreach($statuses as $status){
     $_where = 'status='.$status['ticketstatusid'];
  
  ?>
 <div class="co-xs-12 dispad">
      <a href="<?php echo admin_url('tickets'); ?>"><center class="left-bottom ico-box-left" >
            <!-- <i class="fas fa-ticket-alt" style="color:white;"></i> -->
            <h1><span class="badge badge-success"><?php echo total_rows(db_prefix().'tickets',$_where); ?></span></h1>
            <p> Legal Request <?php echo ticket_status_translate($status['ticketstatusid']); ?> </p>
          </center></a>
  </div>
  <?php } ?>
          
         <div class="co-xs-12 dispad">
          <a href="<?php echo admin_url('contracts'); ?>"><center class="left-bottom ico-box-left" >
            <!-- <i class="fas fa-ticket-alt" style="color:white;"></i> -->
            <h1><span class="badge badge-success">4</span></h1>
            <p>Legal Requests In Progress </p>
          </center></a>
        </div>
      </div>
      <div class="col-xs-6 dispad">
        <div class="co-xs-12 dispad">
          <a href="<?php echo admin_url('contracts'); ?>">
            <center class="right-bottom ico-box-right">
           <!--  <i class="fas fa-file-signature"></i> -->
           <h1><span class="badge badge-danger"><?php echo $contract_approval_awaits?></span></h1>
            <p> <a href="<?php echo admin_url('dashboard/index');?>?confirmation=contract">Contracts Awaiting Approvals</a></p>
          </center></a>
        </div>
         <div class="co-xs-12 dispad">
          <a href="<?php echo admin_url('tickets'); ?>">
            <center class="right-bottom ico-box-right" style="background-color: #b2e192;">
            <!-- <i class="fa fa-users"></i> -->
            <h1><span class="badge badge-danger"><?php echo $legal_approval_awaits?></span></h1>
            <p> <a href="<?php echo admin_url('dashboard/index');?>?confirmation=legal">Legal Requests Awaiting Approvals</a></p>
          </center></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $this->load->view('boss_app/includes/scripts');?>

</body>
</html>
