<script type="text/javascript">
	var table;
			
	function init_approval_table(rel_name,rel_id){
		//var rel_name = "<?php //echo ($contract->type == 'contracts' ? 'contract' : 'po'); ?>";
				
				$('#div_approvals_list').html('');
				$.ajax({
					url: "<?php echo admin_url('approval/table')?>/"+rel_name+'/'+rel_id,
					success: function(response)
					{
						$('#div_approvals_list').html(response);
						
					}
				});
			}
	function load_approval_modal(url)
	{ 
			url = typeof (url) != 'undefined' ? url : admin_url + 'approval/approvals';
  			requestGet(url).done(function (response) {
        	$('#_approval').html(response);
        	$("body").find('#approval_modal').modal({
            show: true,
            backdrop: 'static'
        	});

     

    	}).fail(function (error) {
        	alert_float('danger', error.responseText);
    	})
	
	}
	function update_approval_remarks(invoker,id){
				var remarks = $(invoker).val();
				var data = {remarks : remarks,id:id };
				var url = "<?php echo admin_url('approval/save_approval_remarks')?>";
				$.post(url, data).done(function (response) {
					response = JSON.parse(response);
					if (response.success !== '') {
						alert_float('success', 'Approval Remarks Changed successfully.');
					} else {
					   alert_float('danger', 'Error');   
					}
					$('#approval_modal').modal('hide');
					init_approval_table(response.rel_name,response.rel_id);

				}).fail(function (data) {
					//alert_float('danger','Error');
					return false;
				});
				
			}
	function update_approval_status(invoker,id){
		
				var status_id = $(invoker).val();
				var data = {status_id : status_id,id:id };
		
				var url = "<?php echo admin_url('approval/save_approval_status')?>";
				$.post(url, data).done(function (response) {
					response = JSON.parse(response);
					if (response.success !== '') {
						alert_float('success', 'Approval Status Changed successfully.');
					} else {
					   alert_float('danger', 'Error');   
					}
					$('#approval_modal').modal('hide');
					init_approval_table(response.rel_name,response.rel_id);
					
				}).fail(function (data) {
					//alert_float('danger','Error');
					return false;
				});
				
			}
	
</script>