<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="TemplateModal">
    <div class="modal-dialog modal-xxl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $title; ?></h4>
            </div>
            <?php
            if (!isset($template)) {
                echo form_open_multipart('admin/templates/template', array('id' => 'template-form'));
            } else {
                echo form_open_multipart('admin/templates/template/' . $id, array('id' => 'template-form'));
            }
            ?>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-12">
                      
                        <?php
                        echo form_hidden('rel_type', $rel_type);

                        // so when modal is submitted, it returns to the proposal/contract that was being edited.
                        echo form_hidden('rel_id', $rel_id);
                        $name = isset($template) ? $template->name : '';
                        echo render_input('name', 'template_name', $name);
                        
                         $selected = isset($template) ? $template->agreement_type : '';
                        ?>
                    </div>
                       <div class="col-md-6">
                       <?php
                        echo render_select('agreement_type',$types,array('id','name'),'contract_type',$selected);
                           ?>
                    </div>
					 <div class="col-md-6">
                                <label for="temp_filename" class="profile-image"><?php echo _l('upload_tempdocument'); ?></label>
                                <input type="file" name="file" class="form-control" id="file" >
                         <?php if((isset($template) && $template->temp_filename != NULL) ){

						$extension = pathinfo($template->temp_filename, PATHINFO_EXTENSION);

							   if($extension=='pdf'){?>
	 					 <div class="col-md-12">

                         <iframe src="<?php echo base_url('uploads/template/').$id.'/'.$template->installment_receipt; ?>"></iframe>

               <a target="_blank" href=<?php echo base_url('uploads/template/').$id.'/'.$template->temp_filename; ?> download ><i class="fa fa-download"></i></a>

							   </div>  
						 <?php } elseif($extension=='png' || $extension=='jpeg'){?>
                            <div class="img">

                                <?php $path = get_upload_path_by_type('template').'/'.$id.'/'; ?>

                                <img class="img-responsive" src="<?php echo base_url('uploads/templates/').$id.'/'.$template->temp_filename; ?>">

                            </div>



                        <?php }else{ ?>   

                         <div class="col-md-12">


               <a target="_blank" href=<?php echo base_url('uploads/templates/').$id.'/'.$template->temp_filename; ?> download ><i class="fa fa-download"></i></a>

							   </div>  

               <?php }}?>              
              </div>
					<div class="col-md-12 mbot25">
						  <?php if(isset($contract_merge_fields)){ ?>
                              <hr class="hr-panel-heading" />
                              <p class="bold mtop10 text-right"> <a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                              <div class="avilable_merge_fields col-md-12 mtop15 hide">
                                 <div class="col-md-12" style="border: 1px solid blue;">
                                    <?php
                                    foreach($contract_merge_fields as $field){
										
                                      foreach($field as $f){
										  echo '<div class="col-md-2" style="border: 1px solid #D1D4DD;height: 30px;">';
                                         echo '<b>'.$f['name'].'</b></div><div class="col-md-2" style="border: 1px solid #D1D4DD;height: 30px;"><span>  <a href="#" class="" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></span>';
										  echo '</div>';
                                      }
										
                                   }
                                   ?>
                                </div>
                             </div>
                          <?php } ?>
					</div>
                     <div class="col-sm-12">
                        <?php
                         $content = isset($template) ? $template->content : '';
                        echo render_textarea('content', 'template_content', $content);
                        ?>
                         
                             
               
                       
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_btn" data-dismiss="modal"><?php echo _l('close'); ?></button>
                    <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
                </div>
                <?php echo form_close(); ?>
            </div>
        </div>
    </div>
</div>
<script>
function insert_merge_field(field) {
  var key = $(field).text();
  tinymce.activeEditor.execCommand('mceInsertContent', false, key);
}
</script>