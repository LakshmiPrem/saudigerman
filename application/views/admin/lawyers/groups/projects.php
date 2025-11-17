<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('projects'); ?></h4>
<?php if(isset($client)){ ?>
<?php if(has_permission('projects','','create')){ ?>

<div class="dropdown">
  <button class="btn btn-info dropdown-toggle  mbot25<?php if($client->active == 0){echo ' disabled';} ?>" type="button" data-toggle="dropdown">New
  <span class="caret"></span></button>
  <ul class="dropdown-menu ">
   <?php $case_types = get_case_client_types();
      foreach($case_types as $case_type){
   ?>
    <li><a href="<?php echo admin_url('projects/project?case_type='.$case_type['id'].'&lawyer_id='.$client->lawyerid); ?>"><?=_l($case_type['id'])?></a></li>
 <?php } ?>
  </ul>
</div>

<?php } ?>
<div class="row">
   <?php
      $_where = '';
      if(!has_permission('projects','','view')){
        $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
      }
      ?>
   <?php foreach($project_statuses as $status){ ?>
  <!--  <div class="col-md-5ths total-column">
      <div class="panel_s">
         <div class="panel-body">
            <h3 class="text-muted _total">
               <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id']. ' AND clientid='.$client->lawyerid ?>
               <?php echo total_rows(db_prefix().'projects',$where); ?>
            </h3>
            <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
         </div>
      </div>
   </div> -->
   <?php } ?>
</div>
<div class="_filters _hidden_inputs hidden staff_projects_filter">
          <?php echo form_hidden('staff_id',$client->staffid); ?>
        </div>
        <?php render_datatable(array(
          _l('project_name'),
          _l('project_start_date'),
          _l('project_deadline'),
          _l('project_status'),
          ),'staff-projects',[], [
              'data-last-order-identifier' => 'my-projects',
              'data-default-order'  => get_table_last_order('my-projects'),
          ]); ?>
        </div>
<?php
   //$this->load->view('admin/projects/table_html', array('class'=>'projects-single-client'));

}
?>

