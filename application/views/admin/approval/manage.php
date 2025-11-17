<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                <div class="row _buttons">
                     <div class="col-md-12">
                      <a class="btn btn-info new-contact mbot25" href="#" onclick="load_approval_modal('<?php echo admin_url('approval/approvals?rel_name=general&rel_id=28'); ?>');return false;"><?php echo _l('new_approval'); ?></a>
                       
                       
                     </div>
              
                  </div>
                  <hr class="hr-panel-heading hr-10" />
                 <div class="row">
			<div class="col-md-12" id="div_approvals_list"></div>
				   </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>

<?php $this->load->view('admin/approval/approval_js'); ?>

<?php init_tail(); ?>
<script type="text/javascript">
			init_approval_table('general',28);
</script>
</body>
</html>

