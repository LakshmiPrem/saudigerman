<div class="row">
   <div class="col-md-6 border-right project-overview-left">
      <div class="row">
       <div class="col-md-12">
         <p class="project-info bold font-size-14">
            <?php echo _l('overview'); ?>
         </p>
      </div>
      <div class="col-md-12">
         <table class="table no-margin project-overview-table">
            <tbody>

              <tr class="project-overview-customer">
                  <td class="bold"><?php echo _l('name'); ?></td>
                  <td><?php echo $project->name; ?>
                  </td>
               </tr>
            
              <tr class="project-overview-date-created">
                <td class="bold"><?php echo _l('project_datecreated'); ?></td>
                <td><?php echo _d($project->project_created); ?></td>
              </tr>
      </tbody>
   </table>
</div>

</div>
<?php $tags = get_tags_in($project->id,'casetemplates'); ?>
<?php if(count($tags) > 0){ ?>
<div class="clearfix"></div>
<div class="tags-read-only-custom project-overview-tags hide">
   <hr class="hr-panel-heading project-area-separation hr-10" />
   <?php echo '<p class="font-size-14"><b><i class="fa fa-tag" aria-hidden="true"></i> ' . _l('tags') . ':</b></p>'; ?>
   <input type="text" class="tagsinput read-only" id="tags" name="tags" value="<?php echo prep_tags_input($tags); ?>" data-role="tagsinput">
</div>
<div class="clearfix"></div>
<?php } ?>
<div class="tc-content project-overview-description">
   <hr class="hr-panel-heading project-area-separation" />
   <p class="bold font-size-14 project-info"><?php echo _l('project_description'); ?></p>
   <?php if(empty($project->description)){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_project') . '</p>';
   }
   echo check_for_links($project->description); ?>
</div>
<div class="team-members project-overview-team-members hide">
   <hr class="hr-panel-heading project-area-separation" />
   <?php if(has_permission('casediary','','edit') || has_permission('casediary','','create')){ ?>
   <div class="inline-block pull-right mright10 project-member-settings" data-toggle="tooltip" data-title="<?php echo _l('add_edit_members'); ?>">
      <a href="#" data-toggle="modal" class="pull-right" data-target="#add-edit-members"><i class="fa fa-cog"></i></a>
   </div>
   <?php } ?>
   
</div>
</div>
<div class="col-md-6 project-overview-right">
   <div class="row">
      <div class="col-md-<?php echo ($project->deadline ? 6 : 12); ?> project-progress-bars">
         <?php $tasks_not_completed_progress = round($tasks_not_completed_progress,2); ?>
         <?php $project_time_left_percent = round($project_time_left_percent,2); ?>
         <div class="row">
           <div class="project-overview-open-tasks">
            <div class="col-md-9">
               <p class="text-uppercase bold text-dark font-medium">
                  <?php echo $tasks_not_completed; ?> / <?php echo $total_tasks; ?> <?php echo _l('project_open_tasks'); ?>
               </p>
               <p class="text-muted bold"><?php echo $tasks_not_completed_progress; ?>%</p>
            </div>
            <div class="col-md-3 text-right">
               <i class="fa fa-check-circle<?php if($tasks_not_completed_progress >= 100){echo ' text-success';} ?>" aria-hidden="true"></i>
            </div>
            <div class="col-md-12 mtop5">
               <div class="progress no-margin progress-bar-mini">
                  <div class="progress-bar light-green-bg no-percent-text not-dynamic" role="progressbar" aria-valuenow="<?php echo $tasks_not_completed_progress; ?>" aria-valuemin="0" aria-valuemax="100" style="width: 0%" data-percent="<?php echo $tasks_not_completed_progress; ?>">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>

</div>
<hr class="hr-panel-heading" />




</div>
</div>


<style type="text/css">
  .red{
    background-color: red !important;
  }
</style>