<div class="task-table">
   <!-- <button id="btn_add_hearing"  type="button" data-toggle="collapse" data-target="#demo" class="btn btn-info mbot25"><?php echo _l('add_scope'); ?></button> -->
    
<!-- <div id="demo" class="collapse">-->  
<div class="row">
  <div class="col-md-12">
  <?php echo form_open(admin_url('casetemplates/add_to_template/'),array('id'=>'checklist-form')); ?>
      <?php echo form_hidden('casetemplate_id',$project->id); ?>
      <div class="row">
          <div class="col-md-8">
            <?php 
             $selected=[];
                $document_checklists2 = explode(',',$project->document_checklists);
                foreach ($document_checklists2 as $value) {
                  $selected[] = $value;
                }
                echo render_select_with_input_group('document_checklists[]',$all_document_checklists,array('id','name'),'document_checklists',$selected,'<a href="#" onclick="new_document_checklist();return false;"><i class="fa fa-plus"></i></a>',array('multiple'=>true));
            ?>
          </div>
          <div class="col-md-4 mtop30">
             <button type="submit" class="btn btn-info" data-loading-text="<?php echo _l('wait_text'); ?>" data-autocomplete="off" data-form="#discussion_form"><?php echo _l('add'); ?></button>
          </div>
      </div>
  <?php echo form_close(); ?>
  </div>
</div>
<?php if(isset($hearings)){ ?>
 
  <?php //$this->load->view('admin/casediary/project_edit_hearing');?>
<?php } ?>
  <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="desc">
  <thead>
    <tr> 
               
      <th><?php echo _l('document_checklists'); ?></th>
      <th></th>

    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($document_checklists2 as $row_) { ?>
        <tr>
         <td width="50%">
                <div data-note-description="<?php echo $row_; ?>">
                    <?php echo get_document_master_name($row_); ?>
                </div>
              
          </td>
        <td>
          </td>
      </tr>
      <?php } ?>  
  </tbody>
 </table>
</div>
    
<?php $this->load->view('admin/casetemplates/document_checklist'); ?>
