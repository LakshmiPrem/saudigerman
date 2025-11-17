<?php //print_r($projects_); ?>
<div class="row" style="margin-left:0px; margin-right: 0px;"> 
   <div class="panel_s">
      <div class="panel-body">
         <ul class="nav nav-pills">
             <li class="active" style="background-color: #a5dee673;"><a data-toggle="pill" href="#home"><?php echo _l('customers') ?></a></li>
             <li style="background-color: #a5dee673;"><a data-toggle="pill" href="#menu1"><?php echo _l('projects') ?></a></li>
             <!-- <li><a data-toggle="pill" href="#menu2">Menu 2</a></li>
             <li><a data-toggle="pill" href="#menu3">Menu 3</a></li> -->
         </ul>

         <div class="tab-content">
<!------- Clients ------------------------------------------------------------------>

           <div id="home" class="tab-pane fade in active">
            <hr>
             <div class="row">
               <div class="col-md-12">
                  <div class="row">
                <div class="col-sm-4">
                   <div class="form-group has-search">
                <input type="text" id="search_2"  class="form-control" placeholder="Search <?php echo _l('customers') ?>">
               </div>
                <div class="no_result2 hide"><h5>No result found..</h5></div>
                </div>  
                </div>
               
         
               <?php //print_r($clients_); 
               foreach ($clients_ as $client_) { 

                  $primary_contact_id = get_primary_contact_user_id($client_['userid']);
                  $number_cases = total_rows('tblprojects',array('clientid'=>$client_['userid']));
                  ?>
               <a href="<?php echo admin_url('clients/client/'.$client_['userid']) ?>"><div class="col-sm-3 searchCard2"  data-string="<?php echo $client_['company'];  ?> <?php echo $client_['phonenumber']; ?> <?php echo _l($client_['client_no']); ?> <?php echo $client_['city']; ?> " >
                  <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(255, 0, 0, 0.80), 0 2px 10px 0 rgba(0, 0, 0, 0.62); padding: 10px; margin: 8px; height: 168px;"  >
                     <div class="card-body">
                       <h5 class="card-title" ><strong><?php echo $client_['company']; ?></strong></h5>
                       <p class="card-text" style="margin:  0 0 4px;"><b>City :</b><?php echo $client_['city']; ?> </p>
                       <p class="card-text" style="margin:  0 0 4px;"><b>Phone No:</b><?php echo $client_['phonenumber']; ?> </p>
                       <p class="card-text" style="margin:  0 0 4px;"><b>Clinet No :</b><?php echo $client_['client_no']; ?> |  <span class="label label inline-block" style="color: black;border: 1px solid #1B8BF9;"><?php echo date('d F Y',strtotime($client_['datecreated'])); ?></span> </p>
                       
                         <?php if(isset($primary_contact_id)){ ?>
                      <img style="float: right;position: absolute; right:30px; " src="<?php echo contact_profile_image_url($primary_contact_id,'thumb'); ?>" id="contact-img" class="staff-profile-image-small">
               <?php } ?>

                        <p class="card-text" style="margin:  0 0 4px;"> <a href="#" title="Number Of Cases" class="btn btn-info" style="border-radius: 12px;">Cases : <b><?=$number_cases?></b></a></p>
                                   
                     </div>
                   </div>
               </div>  </a>

               <?php } ?>

               <div class="col-md-12" > 
                 <ul id="pagin" class="pagination">
                    
                 </ul>
               </div> 

               </div><!-- col-12 -->

               </div><!-- row --> 



           </div>
<!------- Case ------------------------------------------------------------------>
           <div id="menu1" class="tab-pane fade">
             <hr>

             <div class="row ">
               <div class="col-md-12">
                   <div class="row">
                <div class="col-sm-4">
               <div class="form-group has-search">
                <input type="text" id="search_"  class="form-control" placeholder="Search <?php echo _l('project') ?>">
               </div>
                <div class="no_result hide"><h5>No result found..</h5></div>
             </div>
          </div>
               <?php $i=0; foreach ($projects_ as $project_) { 

                  $primary_contact_id = get_primary_contact_user_id($project_['clientid']);
                  ?>
                  <?php if($i<=3) {$color = 'red';}
                   elseif($i <=7) { $color = '#0bec0b';}
                   elseif ($i <= 11){$color = '#1c19d5c7';} 
                       // code...
                   ?> 
                <a href="<?php echo admin_url('projects/view/'.$project_['id']) ?>" ><div class="col-sm-3 searchCard"  data-string="<?php echo $project_['name'];  ?> <?php echo $project_['company']; ?> <?php echo _l($project_['case_type']); ?> <?php echo $project_['case_number']; ?> " >
                    
                  <div class="card"  style="box-shadow: 0 2px 5px 0 rgba(0, 0, 0, 0.56), 0 2px 10px 0 rgba(0, 0, 0, 0.52); padding: 10px; margin: 1px; height: 180px; max-height: 180px;"  >
                    
                     <div class="card-body">
                       <h5 class="card-title" ><strong><?php echo $project_['name']; ?></strong></h5>
                       <p class="card-text" style="margin:  0 0 4px;"><b>Case No :</b><?php echo $project_['case_number']; ?> </p>
                       <p class="card-text" style="margin:  0 0 4px;"><b>Client Name :</b><?php echo $project_['company']; ?> </p>
                       <p class="card-text" style="margin:  0 0 4px;"><b>Start Date :</b><?php echo _d($project_['start_date']); ?>  </p>
                       <p class="card-text" style="margin:  0 0 4px;">
                        <span class="label label inline-block" style="color: red;border: 1px solid red;"><?php echo _l($project_['case_type']); ?></span> | <?php $status = get_project_status_by_id($project_['status']);?> <span class="label label inline-block project-status-"<?php $project_['status'] ?> style="color:<?=$status['color']?>;border:1px solid <?=$status['color']?>"> <?=$status['name']?></span></p>
                        <!-- <?php if(isset($primary_contact_id)){ ?>
                      <img style="float: right;position: absolute; " src="<?php echo contact_profile_image_url($primary_contact_id,'thumb'); ?>" id="contact-img" class="staff-profile-image-small">
               <?php } ?> -->
                                  
                     </div>
                      <!-- <div style=" margin: auto; height: 2px;width:100%;background-color: <?=$color?>;"></div> -->
                   </div>
                   
                    <div style=" margin: auto; height: 2px;width:96%;background-color: <?=$color?>;"></div> 

                     
               </div>  </a>

               <?php $i++; } ?>

               <div class="col-md-12" > 
                 <ul id="pagin" class="pagination">
                    
                 </ul>
               </div> 

               </div><!-- col-12 -->

               </div><!-- row --> 

           </div>
<!------- Leads ------------------------------------------------------------------>

           <div id="menu2" class="tab-pane fade">
             <h3>Menu 2</h3>
             <p>Some content in menu 2.</p>
           </div>
         </div>
      </div>
   </div>
</div>