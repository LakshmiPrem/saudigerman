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
						<div class="col-md-4">
							
							<?php echo render_select('related_to', $related_to, array('id','type'), 'related_to'); ?>
						</div>
						<div class="col-md-4">
							<?php echo render_date_input('start_date','uploaded_date_from');?>
						
						</div>
						<div class="col-md-4">
							<?php echo render_date_input('end_date','uploaded_date_to');?>
						</div>
                       
						</div>

<!------------------  Filter End -------------------------------------->

						<?php
						$table_data = array(
							_l('file'),
							_l("uploaded_by"),
                            _l('date_uploaded'),
							_l('related_to'),
                            _l('subject'),
							);
						$custom_fields = get_custom_fields('repository_files',array('show_on_table'=>1));
						foreach($custom_fields as $field){
							array_push($table_data,$field['name']);
						}
						render_datatable($table_data,'repository_files');
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
		ProjectsServerParams['related_to'] = '[name="related_to"]';
		ProjectsServerParams['start_date'] = '[name="start_date"]';
     	ProjectsServerParams['end_date'] = '[name="end_date"]';

		 //var tAPI=initDataTable('.table-projects', admin_url+'projects/table', undefined, undefined, ProjectsServerParams, <?php echo hooks()->apply_filters('projects_table_default_order', json_encode(array(0,'desc'))); ?>);


		 var tAPI=initDataTable('.table-repository_files',window.location.href,undefined,undefined,ProjectsServerParams);

		 $('input[name="related_to"]').on('change',function(){
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


	