<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
	<div class="content">
		<div class="row">
			<div class="col-md-12">

				<div class="panel_s">
					<div class="panel-body">
						
						
						
						
						
<!------------------  Filter Start -------------------------------------->

						<div class="row">
						
						<div class="col-md-3">
							<?php echo render_date_input('start_date','uploaded_date_from');?>
						
						</div>
						<div class="col-md-3">
							<?php echo render_date_input('end_date','uploaded_date_to');?>
						</div>

                        <div class="col-md-3">
							<?php echo render_date_input('expiry_start_date','expiry_date_from');?>
						
						</div>
						<div class="col-md-3">
							<?php echo render_date_input('expiry_end_date','expiry_date_to');?>
						</div>
                       
						</div>

<!------------------  Filter End -------------------------------------->

						<?php
						$table_data = array(
							_l('file'),
							_l("project_name"),
                            _l('date_uploaded'),
                            _l('subject'),
                            _l('document_type'),
                            _l('issue_date'),
                            _l('expiry_date'),
							);
						$custom_fields = get_custom_fields('repository_project_files',array('show_on_table'=>1));
						foreach($custom_fields as $field){
							array_push($table_data,$field['name']);
						}
						render_datatable($table_data,'repository_project_files');
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<?php init_tail(); ?>
<script>
	$(function(){

		var ProjectsServerParams = {};
		ProjectsServerParams['expiry_start_date'] = '[name="expiry_start_date"]';
        ProjectsServerParams['expiry_end_date'] = '[name="expiry_end_date"]';
		ProjectsServerParams['start_date'] = '[name="start_date"]';
     	ProjectsServerParams['end_date'] = '[name="end_date"]';

		 //var tAPI=initDataTable('.table-projects', admin_url+'projects/table', undefined, undefined, ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array(0,'desc'))); ?>);


		 var tAPI=initDataTable('.table-repository_project_files',window.location.href,undefined,undefined,ProjectsServerParams);

		 $('input[name="expiry_start_date"]').on('change',function(){
            tAPI.ajax.reload();
        });
        $('input[name="expiry_end_date"]').on('change',function(){
            tAPI.ajax.reload();
        });
	$('input[name="start_date"]').on('change',function(){
            tAPI.ajax.reload();
        });
    $('input[name="end_date"]').on('change',function(){
            tAPI.ajax.reload();
        });

		$.each(ProjectsServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                 tAPI.ajax.reload();
            });
        });

	});

	


	
</script>
</body>
</html>


	