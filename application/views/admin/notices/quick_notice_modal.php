<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="quick_notice" tabindex="-1" role="dialog">
    <div class="modal-dialog">
        <?php echo form_open_multipart(admin_url('notices/notice'), array('id'=>'quick_notice-form')); ?>
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">
                    <span class="edit-title"><?php echo _l('edit'); ?></span>
                    <span class="add-title"><?php echo _l('add_new').' '._l('notice'); ?></span>
                </h4>
            </div>
            <div class="modal-body">
                <div class="row">
                
      <div class="col-md-12">
                  <div class="form-group">
                     <!--<div class="checkbox checkbox-primary no-mtop checkbox-inline">
                        <input type="checkbox" id="trash" name="trash">
                        <label for="trash"><i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="<?php echo _l('notice_trash_tooltip'); ?>" ></i> <?php echo _l('notice_trash'); ?></label>
                     </div>-->
                     <div class="checkbox checkbox-primary checkbox-inline hide">
                        <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client" >
                        <label for="not_visible_to_client"><?php echo _l('notice_not_visible_to_client'); ?></label>
                     </div>
					  <div class="checkbox checkbox-primary checkbox-inline">
                        <input type="checkbox" name="is_nonstandard" id="is_nonstandard">
                        <label for="is_nonstandard"><?php echo _l('is_nonstandard'); ?></label>
                     </div>
                  </div>
		     <?php if(get_option('enable_legal_request')==1) { ?>
                    <?php $requests=$this->db->get('tbltickets')->result_array(); ?>
                        <?php  echo render_select('ticketid',$requests,array('ticketid',array('ticketid','subject')),'legal_request','',array(),array(),'','',true,'-');?>
		  <?php } ?>
                       
                 <div class="form-group select-placeholder f_client_id">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('project_customer'); ?></label>
                     <select id="clientid" name="client" data-live-search="true" data-width="100%" class="ajax-search select" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        
                        
                  </select>
               </div>
             <?php //$value = (isset($notice) ? $notice->subject : ''); ?>
            <i class="fa fa-question-circle pull-left" data-toggle="tooltip" title="<?php echo _l('notice_subject_tooltip'); ?>"></i>
            <?php echo render_input('subject','notice_subject',''); ?>

               <div class="form-group hide" id="divproject">
				<?php
				$projects=$this->projects_model->get_clients_of_case();
                  echo render_select('quick_projectid',$projects,array('id','name'),'project');
				?>
            </div>

            <?php ########## Opposite Party ##############  ?>
         <!-- <div class="col-md-6"> -->
           <?php 
            $oppositeparty_names=$this->db->get('tbloppositeparty')->result_array();
				    echo render_select('other_party',$oppositeparty_names,array('id','name'),'name_party');
           // echo render_input('other_party','other_party',$value);
           // }?>
         <!-- </div> -->
		
         
            <div class="form-group">
               <label for="notice_value"><?php echo _l('notice_value'); ?></label>
               <div class="input-group" data-toggle="tooltip" title="<?php echo _l('notice_value_tooltip'); ?>">
                  <input type="number" class="form-control" id= "notice_value" name="notice_value" value="">
                  <div class="input-group-addon">
                     <?php $base_currency=$this->db->get_where('tblcurrencies',array('isdefault'=>1))->row();
                     echo $base_currency->symbol; ?>
                  </div>
               </div>
            </div>
            <?php
           $types=$this->db->get('tblnotices_types')->result_array();
            if(is_admin() || get_option('staff_members_create_inline_notice_types') == '1'){
              echo render_select_with_input_group('qnotice_type',$types,array('id','name'),'notice_type','','<a href="#" onclick="new_type();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('qnotice_type',$types,array('id','name'),'notice_type');
         }
         ?>
         
            <div class="hide" id="div_template" <?php if(isset($notice)){?> style="pointer-events: none;"<?php }?>>
             <?php 
                  $this->load->model('notices_model');
                 $templates=$this->notices_model->get_templates_of_notice();          
                $selected = (isset($contact) ? $project->notice_template_id : '');?>
                  <?php  echo render_select('notice_template_id',$templates,array('id','name'),'notice_template',$selected); ?>
          </div> 
    
        
         <div class="row">
         
		   <div class="col-md-6">
               
               <?php echo render_date_input('datestart','notice_start_date'); ?>
            </div>
            <div class="col-md-6">
               
               <?php echo render_date_input('dateend','notice_end_date'); ?>
            </div>
			 <div class="col-md-12 hide">
			 <?php 
                     $payment_terms=get_payment_terms();  
                        echo render_select('payment_terms',$payment_terms,array('id','name'),'payment_terms','',array());?>
			 </div>
			 <div id="notice_install" class="hide">
            <div class="col-md-6">
               
               <?php echo render_input('no_of_installment','no_of_installment'); ?>
            </div>
            <div class="col-md-6">
               
               <?php echo render_date_input('default_effective_date','default_effective_date'); ?>
            </div>
           <div class="col-md-12">
               
               <?php echo render_input('installment_amount','installment_amount','','number'); ?>
            </div>
			</div>
			 <div class="col-md-6">
			   
                      <?php $statuses=$this->db->get(db_prefix() . 'notices_status')->result_array();
                      echo render_select('status',$statuses,array('id','name'),'status','',array(),array(),'','',false); ?>
			 </div>
			  <div class="col-md-6">
               
               <?php echo render_date_input('final_expiry_date','final_expiry_date'); ?>
            </div>
              <div class="col-md-12 hide">
                       
                     <?php
                        $staff=$this->db->get_where('tblstaff',array('active'=>1))->result_array();
                        echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'notice_assignees','',array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                        ?>
                    
                     </div>
                           
                             <div class="col-md-12 hide">
                  <div class="checkbox checkbox-primary billable hide">
               <input type="checkbox" id="is_autorenewal" name="is_autorenewal">
               <label for="is_autorenewal"><?php echo _l('is_autorenewal'); ?></label>
            </div>
				   </div>
         </div>
         
         <?php echo render_textarea('description','notice_description','',array('rows'=>10)); ?>
         
         <?php echo render_custom_fields('notices',false); ?>
         <div class="btn-bottom-toolbar text-right">
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
      
</div>

                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
                <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
            </div>
        </div><!-- /.modal-content -->
        <?php echo form_close(); ?>
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
 <?php $this->load->view('admin/notices/notice_type'); ?>
<script>
	
   window.addEventListener('load',function(){

    // init_ajax_project_search_by_customer_id();
	   //  get_templates_of_notice_ajax();
   }); 
	$('select[name="qnotice_type"]').change(function(){
     //  alert($('select[name="qnotice_type"]').val()); 
	    get_templates_of_notice_ajax();
    
    });
    function get_clients_of_case_ajax() { 
        var clientSelected = $('select[name="client"]').val();
        if(clientSelected > 0){
            $.get(admin_url + 'projects/get_clients_of_case/'+clientSelected,function(response){
				
                var ctype = $('select[name="quick_projectid"]');
                $('select[name="quick_projectid"] option').remove();
                if(response ){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
                   
                    ctype.selectpicker('refresh'); 
					if(response[0].id!='')
						$('#divproject').removeClass('hide');
					else
						$('#divproject').addClass('hide');
                } else {
                    alert_float('danger','Error');
					
                }
            },'json');
        }
    }
	 $('select[name="client"]').change(function(){
      get_clients_of_case_ajax();
    	//get_clients_of_otherparty_ajax();
    });
	 $('#quick_projectid').on('change', function() {
							
				var department =  $('select[name="quick_projectid"]').val();//alert(department);
				var url=admin_url+'projects/getProjectInfo';
				// AJAX request
			$.ajax({
				url:url,
				method: 'post',
				data: {projectid: department},
				dataType: 'json',
				success: function(response){
					 //$('#other_party').val(response.opposte_party);
					// $('#type_stamp').val(response.stamp_type);
					// $('#subject').val(response.subject);
					 $('#notice_value').val(response.claiming_amount);
					$('#other_party').selectpicker('val',response.opposite_party);
					$('#other_party').selectpicker('refresh');
					/* var ctype = $('#client');
				ctype.find('option:first').after('<option value="'+response.userid+'">'+response.company+'</option>');
                ctype.selectpicker('val',response.userid);
                ctype.selectpicker('refresh');*/
												
				}
			});
		});
    function get_clients_of_otherparty_ajax() {
        var clientSelected = $('select[name="client"]').val();//alert(clientSelected);

        if(clientSelected > 0){
            $.get(admin_url + 'casediary/get_clients_of_oppositeparty/'+clientSelected,function(response){
                var ctype = $('select[name="other_party"]');
                $('select[name="other_party"] option').remove();
                if(response ){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
					  
                    ctype.selectpicker('refresh');                  
                } else {
                    alert_float('danger','Error');
                }
            },'json');
        }
    }
    function get_templates_of_notice_ajax() { 
        var clientSelected = $('select[name="qnotice_type"]').val();//alert(clientSelected);
        if(clientSelected !=''){
			$('#div_template').removeClass('hide');
            $.get(admin_url + 'notices/get_templates_of_notice/'+clientSelected,function(response){
                var ctype = $('select[name="notice_template_id"]');
                $('select[name="notice_template_id"] option').remove();
                if(response ){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
                    <?php if(isset($notice)){ 
                            //$opp_ids = array_column($project->assigned_opposite_parties,'opposite_party_id');
                            $toe_id = $notice->notice_template_id;
                            //foreach ($opp_ids as $value) { ?>
                              ctype.selectpicker('val',<?php echo $toe_id ?>);
                            <?php //}
                    } ?>    
                    ctype.selectpicker('refresh');                  
                } else {
                    alert_float('danger','Error');
                }
            },'json');
        }else{
			$('#div_template').addClass('hide');
		}
    }
function new_quick_notice(clientid='',company='',opposite_party=''){
	if(clientid!=''){
	 var ctype = $('select[name="client"]');
                ctype.find('option:first').after('<option value="'+clientid+'">'+company+'</option>');
                ctype.selectpicker('val',clientid);
                ctype.selectpicker('refresh');	
		$('#divclientnotice').addClass('hide');
		
	}
   if(opposite_party!=''){ 
      $('select[name="other_party"]').selectpicker('val',opposite_party);
   }
    $('#quick_notice').modal('show');
    $('.edit-title').addClass('hide');
}
function edit_quick_notice(invoker,id){
    var name = $(invoker).data('name');
  //  $('#additional').append(hidden_input('id',id));
    $('#quick_notice input[name="name"]').val(name);
    $('#quick_notice').modal('show');
    $('.add-title').addClass('hide');
}

</script>
