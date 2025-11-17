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
   <a href="./"> <i class="fa fa-home"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
   <a href="#"> <i class="fa fa-users"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
    <a href="#"><i class="fa fa-briefcase"></i></a>
  </div>
  <div class="item col-xs-12 dispad">
    <a href="#"><i class="fa fa-bank"></i></a>
  </div>
  
</section>

<?php //print_r($clients); ?>
<section class="std">
   <div class="container-fluid">
     <div class="boxx">
       <label>List of Clients</label>
        <table id="t01" style="width: 100%;">
          <thead>
            <tr>
            <th>id</th>
            <th style="text-align: center;">Name</th> 
<!--             <th>mobile</th>
 -->            <th></th>
          </tr>
          </thead>
          
       <tbody>
     <?php
      foreach($clients as $client) {
     ?>     
          <tr>
            <td><?=$client['client_no']?></td>
            <td><?=$client['company']?></td>
<!--             <td><?=$client['phonenumber']?></td>
 -->            <td><a href="tel:<?=$client['phonenumber']?>" class="callto"><span class="fa fa-phone-square"></span></a></td>
          </tr>
        <?php 
      }
        ?>
          </tbody>   
        </table>
     </div>
   </div>
</section>


<?php  $this->load->view('boss_app/includes/scripts');?>


</body>
</html>




<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.19/css/jquery.dataTables.min.css">

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/rowreorder/1.2.5/css/rowReorder.dataTables.min.css">

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.dataTables.min.css
">


<script type="text/javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/1.10.19/js/jquery.dataTables.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/rowreorder/1.2.5/js/dataTables.rowReorder.min.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>

<script type="text/javascript">
    $(document).ready(function() {
    $('#t01').DataTable( {
        rowReorder: {
            selector: 'td:nth-child(2)'
        },
        "lengthChange": false,
        "info" :'',
        "scrollY": "300px",
        "scrollCollapse": true,
        responsive: true

    } );

   


} );


</script>