<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="ClosureModal">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title"><?php echo $title; ?></h4>
            </div>
            <?php
            if (!isset($template)) {
                echo form_open_multipart('admin/templates/closure', array('id' => 'closure-form'));
            } else {
                echo form_open_multipart('admin/templates/closure/' . $id, array('id' => 'closure-form'));
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
                        echo render_input('name', 'clause_name', $name);
                         $juri = isset($template) ? $template->ai_jurisdiction : '';
                        echo render_input('ai_jurisdiction', 'jurisdiction', $juri);
                       ?>
					 <?php
                         $content1 = isset($template) ? $template->ai_constraints : '';
                        echo render_textarea('ai_constraints', 'constraints', $content1);
                        ?>
                    </div>
    
			
                     <div class="col-sm-12">
						     <div class="checkbox checkbox-primary">
								   <?php $value1 = isset($template) ? $template->general_ai : 0; ?>
      <input type="checkbox" name="general_ai" id="general_ai" <?php if($value1==1) echo 'checked';?>>
      <label for="general_ai"><?php echo _l('is_generalorai'); ?></label>
    </div>

                        <?php
                         $content = isset($template) ? $template->content : '';
                        echo render_textarea('content1', 'closure_content', $content);
                        ?>
                         
                             
               
                       
                    </div>
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
$(document).ready(function(){
	
    $('#closure-form #general_ai').change(function(){ 
        if($(this).is(':checked')) {
            // Show textarea
           // $('#ai_clause_box').show();
 var mtypeSelected = $('#closure-form input[name="name"]').val();
			var jurisdiction=$('#closure-form input[name="ai_jurisdiction"]').val();
			var constraints=$('#closure-form textarea[name="ai_constraints"]').val();
            // Call backend to generate clause
            $.ajax({
                url: '<?php echo admin_url("templates/generate_ai_clause"); ?>',
                type: 'POST',
                dataType: 'json',
                data: { type: mtypeSelected,jurisdiction:jurisdiction,constraints:constraints}, // you can pass clause type dynamically
                success: function(res){
                    if(res.status === 'success'){
						var editor = tinyMCE.activeEditor;
					editor.setContent(res.clause.html);
						
                       // $('#ai_clause').val(res.clause);
                    } else {
                        $('#ai_clause').val("Error: Could not generate clause");
                    }
                }
            });
        } else {
            // Hide textarea and clear
           // $('#ai_clause_box').hide();
            $('#ai_clause').val('');
        }
    });
});
</script>
