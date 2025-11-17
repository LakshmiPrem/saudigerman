<style type="text/css">
  .hearing.nav-tabs>li>a{
    font-size: 12.5px;
  }
</style>
<a href="#" id="btn_add_hearing" class="btn btn-info mbot25" onclick="init_hearing('',<?php echo $project->id; ?>); return false;">Add </a>

<ul class="nav nav-tabs hearing" role="tablist" id="myTab">
  <li role="presentation" class="active" >
    <a href="#tab_content-all" aria-controls="tab_content" role="tab" data-toggle="tab" onclick="courtClick('all','all')">
      All
    </a>
  </li>
  <?php foreach ($hearing_types as  $court) {   ?>
      <li role="presentation" <?php if($hearing_type_tab == $court['instance_slug']) {  ?> class="active" <?php } ?>>
        <a href="#tab_content<?=$court['instance_slug']?>" aria-controls="tab_content" role="tab" data-toggle="tab" onclick="courtClick('<?php echo $court['instance_slug']; ?>','<?=$court['instance_slug']?>')">
          <?php echo $court['instance_name']; ?>
        </a>
      </li>
  <?php } ?>       
</ul>
<div class="tab-content">
  <!---------- List All Hearings -------------->
  
  <div role="tabpanel" class="tab-pane  active" id="tab_content<?=$court['id']?>">
    <div class="task-table">
      <table class="table dt-table scroll-responsive  table-all-hearings " data-order-col="1" data-order-type="asc">
        <thead>
          <tr> 
            <th><?php echo _l('hearing_date'); ?></th>
            <th><?php echo _l('hearing_list_subject'); ?></th>
            <th><?php echo _l('client'); ?></th>
            <th><?php echo _l('court_fee'); ?></th>
            <th><?php echo _l('casediary_casenumber'); ?></th>
            <th><?php echo _l('casediary_oppositeparty'); ?></th>
            <th><?php echo _l('court_decision'); ?></th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>
  <div id="demo" class="collapse">
    <?php //$this->load->view('admin/projects/project_hearing');?>
  </div>
  <?php if(isset($hearings)){ ?>
    <?php //$this->load->view('admin/projects/project_edit_hearing');?>
  <?php } ?>
    <?php foreach ($hearing_types as $key => $court) { 

     $court_no = $court['id'].'_no';

     ?>
    <div role="tabpanel" class="tab-pane  <?php if($hearing_type_tab ==  $court['id']) { ?> active <?php } ?>" id="tab_content<?=$court['id']?>">
      <div class="task-table">
   
      <table class="table dt-table scroll-responsive table-<?=$court['id']?>-hearings" data-order-col="1" data-order-type="asc">
        <thead>
    <tr> 
      <th><?php echo _l('hearing_date'); ?></th>
      <th><?php echo _l('hearing_list_subject'); ?></th>
      <th><?php echo _l('client'); ?></th>
      <th><?php echo _l('court_fee'); ?></th>
      <th><?php echo _l('casediary_casenumber'); ?></th>
      <th><?php echo _l('casediary_oppositeparty'); ?></th>
      <th><?php echo _l('court_decision'); ?></th>
    </tr>
  </thead>
  <tbody>
   
  </tbody>
 </table>
</div>
      </div>
    <?php } ?>
     
</div>
<?php $this->load->view('admin/casediary/hearing_reference');?>
<?php $this->load->view('admin/casediary/court_degree');?>
<?php $this->load->view('admin/casediary/hallnumber');?>
<?php $this->load->view('admin/casediary/court_region');?>
<?php $this->load->view('admin/projects/send_hearing_notice_email_template'); ?>
<?php //$this->load->view('admin/casediary/court'); ?>
<?php //$this->load->view('admin/casediary/case_nature'); ?>