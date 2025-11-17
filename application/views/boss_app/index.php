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
            <h2 class="pro-tittle">Beveron Smart Office</h2>
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
   <a href="<?php echo base_url('boss_app/clients'); ?>"> <i class="fa fa-users"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
    <a href="#"><i class="fa fa-briefcase"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
    <a href="#"><i class="fa fa-bank"></i></a>
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
          <center class="ico-box-left">
            <i class="fa fa-briefcase"></i>
            <p>Contracts</p>
          </center>
        </div>
        <div class="co-xs-12 dispad">
          <center class="left-bottom ico-box-left">
            <i class="fa fa-bank"></i>
            <p>Legal Requests</p>
          </center>
        </div>
      </div>
      <div class="col-xs-6 dispad">
        <div class="co-xs-12 dispad">
          <a href="<?php echo base_url('boss_app/clients'); ?>"><center class="right-bottom ico-box-right">
            <i class="fa fa-users"></i>
            <p>Approvals</p>
          </center></a>
        </div>
      </div>
    </div>
  </div>
</section>

<?php $this->load->view('boss_app/includes/scripts');?>



</body>
</html>
