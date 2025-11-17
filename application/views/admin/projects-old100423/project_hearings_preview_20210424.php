<style type="text/css">
  .hearing.nav-tabs>li>a{
    font-size: 12.5px;
  }
</style>
<ul class="nav nav-tabs hearing" role="tablist" id="myTab">
  <?php foreach ($hearing_types as $key => $court) {  ?>
      <li role="presentation" <?php if($hearing_type_tab == $court['id']) {  ?> class="active" <?php } ?>>
        <a href="#tab_content<?=$court['id']?>" aria-controls="tab_content" role="tab" data-toggle="tab" onclick="courtClick('<?php echo $court['name']; ?>','<?=$court['id']?>')">
          <?php echo $court['name']; ?>
        </a>
      </li>
  <?php } ?>       
</ul>
<div class="tab-content">
  <button id="btn_add_hearing"  type="button" data-toggle="collapse" data-target="#demo" class="btn btn-info mbot25">ADD <?php echo _l($hearing_type_tab);?> </button>
  <div id="demo" class="collapse">
    <?php $this->load->view('admin/projects/project_hearing');?>
  </div>
 
    <?php foreach ($hearing_types as $key => $court) { 

     $court_no = $court['id'].'_no';

     ?>
    <div role="tabpanel" class="tab-pane  <?php if($hearing_type_tab ==  $court['id']) { ?> active <?php } ?>" id="tab_content<?=$court['id']?>">
      
<div class="row">
  <div class="col-md-12" id="small-table<?=$court['id']?>">

      <div class="task-table">
   
      <table class="table dt-table scroll-responsive table-hearings<?=$court['id']?>" data-order-col="1" data-order-type="asc">
        <thead>
    <tr> 
               
      <th><?php echo _l('hearing_date'); ?></th>
      <th><?php echo _l('hearing_list_subject'); ?></th>
      <th><?php echo _l('client'); ?></th>
      <th><?php echo _l('casediary_casenumber'); ?></th>
      <th><?php echo _l($court_no); ?></th>
      <th><?php echo _l('casediary_oppositeparty'); ?></th>
      <th><?php echo _l('court_decision'); ?></th>
      <th></th>

    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($hearings as $row_hearing) {
        if($row_hearing->hearing_type == $court['id']){
        ?>
        <tr>
        <td><?=_d($row_hearing->hearing_date)?></td>
        <td><a data-toggle="collapse"  onclick="show_edit_form('#div_hearngForm<?=$row_hearing->id?>')" > <?=$row_hearing->subject?></a></td>
        <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?=$project->client_data->company?></a></td>
        <td><a onclick="init_hearing(<?=$row_hearing->id?>,'#hearings<?=$court['id']?>','table-hearings<?=$court['id']?>','#small-table<?=$court['id']?>'); return false;" ><?=$row_hearing->case_number?></a></td>
        <td><?=$row_hearing->court_no?></td>
        <td><?=$row_hearing->opposite_party_name?></td>
        <td><?=$row_hearing->proceedings?></td>
        <td><a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('casediary/delete_hearing/'.$row_hearing->project_id.'/'.$row_hearing->id) ?>"><i class="fa fa-remove"></i></a></td>
      </tr>
      <?php }} ?>  
  </tbody>
 </table>
</div>
</div>
           
              <div class="col-md-8 small-table-right-col">
                  <div id="hearings<?=$court['id']?>" class="hide">
                    
                  </div>
              </div>

</div> 
             


      </div> <!---tab --->
    <?php } ?>
     
</div>



<?php $this->load->view('admin/casediary/hearing_reference'); ?>
<?php $this->load->view('admin/casediary/court_degree'); ?>
<?php $this->load->view('admin/casediary/hallnumber'); ?>
<?php $this->load->view('admin/casediary/court_region'); ?>
