<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="quick_chatgpt" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-xxl">
		<?php echo form_open(admin_url('misc/add_quick_chatgpt'), array('id'=>'quick_chatgpt-form')); ?>
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				<h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new').' '._l('chatprompt'); ?></span>
                </h4>
			
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-12">
						
						
						
						<?php $contracts=get_contractinfo();?>
						<?php echo render_select('contract_id',$contracts,array('id','subject'),'contract'); ?>
					 <?php echo render_textarea('prompt_text','prompt_text','',array(),array(),''); ?>
					
					</div>
						<div id="gptresult" class="col-md-12 hide">
							
				 <?php echo render_textarea('prompt_textresult','template_content','',array('rows'=>20),array(),'','tinymce'); ?>
				</div>
				</div>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default" data-dismiss="modal">
					<?php echo _l('close'); ?>
				</button>
				<button type="submit" class="btn btn-info">
					<?php echo _l('generate'); ?>
				</button>
			</div>
		</div>
		<!-- /.modal-content -->
		<?php echo form_close(); ?>
	</div>
	<!-- /.modal-dialog -->
</div> <!-- /.modal -->

<script>
	


</script>
