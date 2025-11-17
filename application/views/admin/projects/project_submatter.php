<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<h4 class="customer-profile-group-heading"><?php echo _l('related_matter'); ?></h4>
<?php if(isset($project)){ ?>
<?php if(has_permission('projects','','create')){ 
 $scase_type ='submatter';
$posturl=admin_url('projects/project?case_type='.$project->case_type.'&customer_id='.$project->clientid.'&related_matter='.$project->id);
?>
 <a href="<?php echo $posturl ?>" target="_new" class="btn btn-info mbot25"><?php echo _l('new_submatter')._l($project->case_type); ?></a>
<?php } ?>
<div class="row">
   <?php
      $_where = '';
      if(!has_permission('projects','','view')){
        $_where = 'id IN (SELECT project_id FROM '.db_prefix().'project_members WHERE staff_id='.get_staff_user_id().')';
      }
      ?>
   <?php foreach($project_statuses as $status){ ?>
   <div class="col-md-5ths total-column">
      <div class="panel_s">
         <div class="panel-body">
            <h3 class="text-muted _total">
               <?php $where = ($_where == '' ? '' : $_where.' AND ').'status = '.$status['id'].' AND related_matter='.$project->id; ?>
               <?php echo total_rows(db_prefix().'projects',$where); ?>
            </h3>
            <span style="color:<?php echo $status['color']; ?>"><?php echo $status['name']; ?></span>
         </div>
      </div>
   </div>
   <?php } ?>
</div>
<?php
   $this->load->view('admin/projects/table_html_submatter', array('class'=>'projects-submatter'));
}
?>

