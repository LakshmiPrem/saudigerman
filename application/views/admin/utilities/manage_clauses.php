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
         <button type="button" class="btn btn-info" onclick="add_clause('contracts', 1);"><?php echo _l('add_clause'); ?></button>
         <hr>
      </div>
						
						<div class="col-md-3 hide">
							
							<?php echo render_select('related_user', $staffs, array('staffid','firstname'), 'als_staff',get_staff_user_id()); ?>
						</div>
						
                       
						</div>

<!------------------  Filter End -------------------------------------->

						<?php
						$table_data = array(
							_l('subject'),
							_l('actions'),
							);
						$custom_fields = get_custom_fields('all_clauses',array('show_on_table'=>1));
						foreach($custom_fields as $field){
							array_push($table_data,$field['name']);
						}
						render_datatable($table_data,'all_clauses');
						?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div id="modal-wrapper1"></div>
<?php init_tail(); ?>
<script>
	$(function(){

		var ProjectsServerParams = {};
	 var tAPI=initDataTable('.table-all_clauses',window.location.href,undefined,undefined,ProjectsServerParams,[0, 'desc']);


		/* $('input[name="related_to"]').on('change',function(){
            tAPI.ajax.reload();
        });*/
	$('input[name="start_date"]').on('change',function(){
            tAPI.ajax.reload();
        });
    $('input[name="end_date"]').on('change',function(){
            tAPI.ajax.reload();
        });

		$.each(ProjectsServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
            	//alert(ProjectsServerParams['related_user']);
                 tAPI.ajax.reload();
            });
        });

	});
	
</script>
</body>
</html>


	