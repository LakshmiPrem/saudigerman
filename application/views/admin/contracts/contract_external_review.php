<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">

    <div class="col-md-12">
      <div class="panel_s">
         <div class="panel-body">

       
<div style="display: flex; height: 100vh; width: 100%; overflow: hidden;">

  <!-- LEFT HALF: PDF Preview -->
  <div style="flex: 1; border-right: 1px solid #ddd; display: flex; flex-direction: column;">
    <div style="background: #f8f9fa; padding: 10px; border-bottom: 1px solid #ddd;">
      <h4 style="margin: 0;">PDF Preview</h4>
    </div>
    <iframe 
        src="<?= admin_url('contracts/view_uploadpdf/' . $contract->id); ?>#toolbar=0&navpanes=0&scrollbar=0" 
        style="flex: 1; width: 100%; border: none;">
    </iframe>
  </div>

  <!-- RIGHT HALF: Contract Details -->
  



    <div class="col-md-4">
    <div class="card" style="height:800px; overflow-y:auto;">
        <div class="card-header bg-light">
            <h4 class="mb-0 text-success">Approvals</h4>
        </div>

        <div class="card-body">
            <!-- Approvals Table -->
            <div id="approval_table_div" style="min-height:400px;">
                <div class="text-center text-muted">Loading approvals...</div>
            </div>

            <?php
            // --- Check if the logged-in user is an approver for this contract ---
            $this->db->where('rel_id', $contract->id);
            $this->db->where('staffid', get_staff_user_id());
            $this->db->where('approval_type', 'read_by');
            $approver_exists = $this->db->get('tblapprovals')->row();

            if ($approver_exists && $approver_exists->approval_status != 7) { ?>
                <div class="text-start" style="margin-top:10px;"> 
                    <a href="<?= admin_url('contracts/review_pdf/' . $contract->id . '/' . $contract->type); ?>"

                       class="btn btn-success">
                        <i class="fa fa-file-pdf-o"></i> Review PDF
                    </a>
                </div>
            <?php } ?>
        </div>
    </div>
</div>
</div>
</div>
</div>
  </div>
    </div>
      </div>
<?php init_tail(); ?>
<script>
$(document).ready(function() {
    var type = 'overview';
    var rel_id = <?php echo (int)$contract->id; ?>;   
     var rel_name = "<?php echo ($contract->type == 'contracts' ? 'contract' : 'po'); ?>";
    
				$('#approval_table_div').html('');
				$.ajax({
					url: "<?php echo admin_url('approval/table')?>/" + rel_name + "/" + rel_id,
					data: {
                        
                        type: type,
                    },
					success: function(response)
					{
						$('#approval_table_div').html(response);
						
					}
				});
});
</script>
