<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-4 left-column">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin"><?php echo $title ?>
              <?php if(isset($contract) && has_permission('documents','','delete')){ ?>
              <small><a href="<?php echo admin_url('documents/delete/'.$contract->id); ?>" class="pull-right mleft5 text-danger _delete"><?php echo _l('delete'); ?></a></small>
              <?php } ?>
              <?php if(isset($contract) && has_permission('documents','','create')){ ?>
              <small><a href="<?php echo admin_url('documents/copy/'.$contract->id); ?>" class="pull-right hide"><?php echo _l('document_copy'); ?></a></small>
              <?php } ?>
            </h4>
            <hr class="hr-panel-heading" />
            <?php echo form_open($this->uri->uri_string(),array('id'=>'contract-form')); ?>
            <div class="form-group">
              <div class="checkbox checkbox-primary no-mtop checkbox-inline" style="display: none;">
                <input type="checkbox" id="trash" name="trash" data-toggle="tooltip" title="<?php echo _l('contract_trash_tooltip'); ?>" <?php if(isset($contract)){if($contract->trash == 1){echo 'checked';}}; ?>>
                <label for="trash"><?php echo _l('contract_trash'); ?></label>
              </div>
              <div class="checkbox checkbox-primary checkbox-inline hide">
                <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client" <?php if(isset($contract)){if($contract->not_visible_to_client == 1){echo 'checked';}}; ?>>
                <label for="not_visible_to_client"><?php echo _l('contract_not_visible_to_client'); ?></label>
              </div>
            </div>
            <div class="form-group select-placeholder">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('contract_client_string'); ?></label>
                     <select id="clientid" name="client" data-live-search="true" data-width="100%" class="ajax-search select" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php $selected = (isset($contract) ? $contract->client : '1');
                        if($selected == ''){
                         $selected = (isset($customer_id) ? $customer_id: '1');
                      }
                      if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                     } ?>
                  </select>
               </div>

               
               <!-- <div class="form-group">
                <?php $date=date('m-Y');?>
                	<label for="category"><?php echo _l('category'); ?></label>
									<select id="document_category" class="selectpicker" name="document_category" data-width="100%" data-show-subtext="true">
										
										<option value="SA" <?php if (isset($contract) && $contract->document_category == 'SA') { echo 'selected'; } ?>>Sale Agreement</option>
                    <option value="MT" <?php if (isset($contract) && $contract->document_category == 'MT') { echo 'selected'; } ?>>Mother Title</option>
                    <option value="SIC/SUB" <?php if (isset($contract) && $contract->document_category == 'SIC/SUB') { echo 'selected'; } ?>>Sub-Title</option>
                    <option value="T" <?php if (isset($contract) && $contract->document_category == 'T') { echo 'selected'; } ?>>Client's Title</option>
                    <option value="OL" <?php if (isset($contract) && $contract->document_category == 'OL') { echo 'selected'; } ?>>Offer Letters</option>
                    <option value="court" <?php if (isset($contract) && $contract->document_category == 'court') { echo 'selected'; } ?>>Court Matter</option>
                    <option value="Min" <?php if (isset($contract) && $contract->document_category == 'Min') { echo 'selected'; } ?>>Minutes</option>
                    <option value="Cont" <?php if (isset($contract) && $contract->document_category == 'Cont') { echo 'selected'; } ?>>Contracts</option>
                    <option value="PLY" <?php if (isset($contract) && $contract->document_category == 'PLY') { echo 'selected'; } ?>>Policies</option>

									</select>
								</div> -->
                <!-- <input class="hide" type="text" name="selectedcategory" id="selectedcategory" value=""> -->
                <!-- <div class="form-group" id="property_name_div">
                <?php $selected = (isset($contract) ? $contract->property_name : '');?>
                <?php echo render_select('property_name',$erpproperties,array('id','unit_name','project_code'),'property_name',$selected); ?>
                
								</div> -->
                <div class="form-group select-placeholder projects-wrapper<?php if((isset($contract) && !customer_has_projects($contract->client))){ echo ' hide';} ?>" id="matter">
                  <label for="project_id"><?php echo _l('project'); ?></label>
                   <div id="project_ajax_search_wrapper">
                    <select name="project_id" id="project_id" class="projects ajax-search ays-ignore" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                       <?php
                       if(isset($contract) && $contract->project_id != 0){
                        echo '<option value="'.$contract->project_id.'" selected>'.get_project_name_by_id($contract->project_id).'</option>';
                     }
                     ?>
                  </select>
               </div>
               <!-- <?php $selected = (isset($contract) ? $contract->project_id : '');?>
                <?php echo render_select('project_id',$projectnames,array('id','name'),'project',$selected); ?> -->
            </div>
            
               
                   <!--<div class="form-group" <?php if(isset($contract)){?> style="pointer-events: none;"<?php }?>>           
                        <?php ############## File No ################### ?>
                    <?php 
                        $next_file_number = get_option('next_safefile_number');
                        $prefix = get_option('safe_no_prefix');
                        if(isset($contract)) {
							 
                            echo render_input('safe_uniqueno','safe_uniqueno',$contract->safe_uniqueno,'text',array('readonly'=>'readonly')); 
                        }
                        else{
                             $_file_number = str_pad($next_file_number, get_option('number_padding_prefixes'), '0', STR_PAD_LEFT); ?>
                                <?php  //$value = (isset($project) ? $project->file_no : ''); ?>
                                <?php echo render_input('safe_uniqueno','safe_file_no',$prefix.$_file_number,'text');
                        }

                    ?>
			  </div>  !-->

        <div class="form-group" <?php if(isset($contract)){?> style="pointer-events: none;"<?php }?>>           
        <?php ############## File No ################### ?>
                    <?php 
                        
                        if(isset($contract)) {
							 
                            echo render_input('safe_uniqueno','safe_file_no',$contract->safe_uniqueno,'text',array('readonly'=>'readonly')); 
                        }
                        else{ ?>
                                
                                <?php echo render_input('safe_uniqueno','safe_file_no','','text');
                        }

                    ?>
			  </div>  
        <!-- <?php $value = (isset($contract) ? $contract->file_number : ''); ?>
          <?php echo render_input('file_number','file_number',$value,'text'); ?>  -->
                <?php $value = (isset($contract) ? $contract->subject : ''); ?>
          <?php echo render_input('subject','document_subject',$value,'text',array('data-toggle'=>'tooltip','title'=>'contract_subject_tooltip')); ?>         
              
 		<?php 
 $selected = (isset($document_other_party) ? $document_other_party : ''); 



            echo render_select('doc_other_party[]',$oppositeparty_names,array('id','name'),'name_party',$selected,array('multiple'=>true));
          
           ?>
       
          <!--div class="form-group">
            <label for="contract_value"><?php echo _l('contract_value'); ?></label>
            <div class="input-group" data-toggle="tooltip" title="<?php echo _l('contract_value_tooltip'); ?>">
              <input type="number" class="form-control" name="contract_value" value="<?php if(isset($contract)){echo $contract->contract_value; }?>">
              <div class="input-group-addon">
                <?php echo $base_currency->symbol; ?>
              </div>
            </div>
          </div-->
          <?php
          $selected = (isset($contract) ? $contract->contract_type : '');
          if(is_admin() || get_option('staff_members_create_inline_contract_types') == '1'){
           echo render_select_with_input_group('contract_type',$types,array('id','name'),'document_type',$selected,'<a href="#" onclick="new_type();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('contract_type',$types,array('id','name'),'document_type',$selected);
        }
        ?>
        
        <div class="row">
          <div class="col-md-6">
            <?php $value = (isset($contract) ? _d($contract->datestart) : _d(date('Y-m-d'))); ?>
            <?php echo render_date_input('datestart','document_start_date',$value); ?>
          </div>
          <div class="col-md-6">
            <?php $value = (isset($contract) ? _d($contract->dateend) : ''); ?>
            <?php echo render_date_input('dateend','document_end_date',$value); ?>
          </div>
        </div>
        <?php $value = (isset($contract) ? $contract->description : ''); ?>
        <?php echo render_textarea('description','safe_description',$value,array('rows'=>10)); ?>
        <?php $rel_id = (isset($contract) ? $contract->id : false); ?>
        <?php echo render_custom_fields('documents',$rel_id); ?>
        <div class="btn-bottom-toolbar text-right">
          <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
        </div>
        <?php echo form_close(); ?>
      </div>
    </div>
  </div>
  <?php if(isset($contract)) { ?>
  <div class="col-md-8 right-column">
    <div class="panel_s">
      <div class="panel-body">
        <h4 class="no-margin"><?php echo _l('document_edit_overview'); ?></h4>
        <hr class="hr-panel-heading" />
        <?php if($contract->trash > 0){
          echo '<div class="ribbon default"><span>'._l('contract_trash').'</span></div>';
        } ?>
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
              <?php echo _l('additional_details'); ?>
            </a>
          </li>
          <li role="presentation">
            <a href="#tab_attachments" aria-controls="tab_attachments" role="tab" data-toggle="tab">
              <?php echo _l('contract_attachments'); ?>
            </a>
          </li>
			<li role="presentation">
            <a href="#tab_issues" aria-controls="tab_issues" role="tab" data-toggle="tab">
              <?php echo _l('request_return'); ?>
            </a>
          </li>
          <li role="presentation" class="hide">
            <a href="#tab_renewals" aria-controls="tab_renewals" role="tab" data-toggle="tab">
              <?php echo _l('no_document_renewals_history_heading'); ?>
            </a>
          </li>
          <?php #########################################################################
          ?>
            <li role="presentation">
               <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $contract->id ;?> + '/' + 'document', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
               <?php echo _l('estimate_reminders'); ?>
               <?php
                  $total_reminders = total_rows('tblreminders',
                   array(
                    'isnotified'=>0,
                    'staff'=>get_staff_user_id(),
                    'rel_type'=>'document',
                    'rel_id'=>$contract->id
                  )
                  );
                  if($total_reminders > 0){
                   echo '<span class="badge">'.$total_reminders.'</span>';
                  }
                  ?>
               </a>
            </li>
          <?php ##############################################################################?>
          <li role="presentation">
            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab" onclick="init_rel_tasks_table(<?php echo $contract->id; ?>,'document'); return false;">
              <?php echo _l('tasks'); ?>
            </a>
          </li>
          <li role="presentation">
            <a href="#" onclick="contract_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="toggle_view">
              <i class="fa fa-expand"></i></a>
            </li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab_content">
              <div class="row">
                <div class="col-md-12 text-right _buttons" style="display: none;">
                  <a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?print=true'); ?>" target="_blank" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom"><i class="fa fa-print"></i></a>
                  <a href="<?php echo admin_url('contracts/pdf/'.$contract->id); ?>" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                  <a href="#" class="btn btn-default mright5" data-target="#contract_send_to_client_modal" data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('contract_send_to_email'); ?>" data-placement="bottom"><i class="fa fa-envelope"></i></span></a>
                </div>
                <div class="col-md-12" style="display: none;">
                  <?php if(isset($contract_merge_fields)){ ?>
                  <hr class="hr-panel-heading" />
                  <p class="bold mtop10 text-right"><a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                  <div class=" avilable_merge_fields mtop15 hide">
                    <ul class="list-group">
                      <?php
                      foreach($contract_merge_fields as $field){
                       foreach($field as $f){
                        if(strpos($f['key'],'statement_') === FALSE && strpos($f['key'],'password') === FALSE && strpos($f['key'],'email_signature') === FALSE){
                          echo '<li class="list-group-item"><b>'.$f['name'].'</b>  <a href="#" class="pull-right" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></li>';
                        }
                      }
                    } ?>
                  </ul>
                </div>
                <?php } ?>
              </div>
            </div>
            <hr class="hr-panel-heading" />
            <div class="editable tc-content" id="con" style="border:1px solid #f1f1f1;min-height:70px;">
              <?php
              if(empty($contract->content)){
               //echo do_action('new_contract_default_content','<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_link') . '</span>');
             } else {
               echo $contract->content;
             }
             ?>
           </div>
           <!--button id="p" class="p">Save Contract</button-->
         </div>
         <div role="tabpanel" class="tab-pane" id="tab_attachments">
          <?php echo form_open(admin_url('documents/add_contract_attachment/'.$contract->id),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>
          <?php echo form_close(); ?>
          <div class="text-right mtop15">
            <div id="dropbox-chooser"></div>
          </div>
          <div id="contract_attachments" class="mtop30">
            <?php
            $data = '<div class="row">';
            foreach($contract->attachments as $attachment) {
              $href_url = site_url('download/file/document/'.$attachment['id']);
              if(!empty($attachment['external'])){
                $href_url = $attachment['external_link'];
              }
              $data .= '<div class="display-block contract-attachment-wrapper">';
              $data .= '<div class="col-md-10">';
              $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
			  $data .= '<a href="'.$href_url.'"'.(!empty($attachment['external']) ? ' target="_blank"' : '').'>'.ucwords($attachment['subject']).' - '.$attachment["file_name"].'</a>';
            //  $data .= '<a href="'.$href_url.'">'.$attachment['file_name'].'</a>';
              $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
              $data .= '</div>';
              $data .= '<div class="col-md-2 text-right">';
              if($attachment['staffid'] == get_staff_user_id() || is_admin()){
               $data .= '<a href="#" class="text-danger" onclick="delete_contract_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
             }
             $data .= '</div>';
             $data .= '<div class="clearfix"></div><hr/>';
             $data .= '</div>';
           }
           $data .= '</div>';
           echo $data;
           ?>
         </div>
       </div>
	   <div role="tabpanel" class="tab-pane" id="tab_issues">
		          <div class="panel-body">


              <!------------------  Filter Start -------------------------------------->

						<div class="row">
						<div class="col-md-3">
							<?php echo render_date_input('requested_date_from','Requested Date From');?>
						
						</div>
						<div class="col-md-3">
							<?php echo render_date_input('requested_date_to','Requested Date To');?>
						
						</div>
						<div class="col-md-3">
							<?php echo render_date_input('return_date_from','Return Date From');?>
						</div>

                        <div class="col-md-3">
							<?php echo render_date_input('return_date_to','Return Date To');?>
						</div>
						</div>

<!------------------  Filter End -------------------------------------->
                            <?php
                            $table_data = array(
                               '#',
                               _l('document_in_name'),
                               [
         						'name'     => _l('document_list_client'),
								'th_attrs' => ['class' => 'not_visible'],
								],
                              // _l('matter'),
                               _l('safe_uniqueno'),
							   _l('requested_by'),
                               _l('document_in_requested_date'),
                              _l('approval_by'),
                               _l('received_by'),
								_l('document_in_received_date'),
                               _l('document_document_view'),
                               );
                            $custom_fields = get_custom_fields('documents_in',array('show_on_table'=>1));
                            foreach($custom_fields as $field){
                               array_push($table_data,$field['name']);
                           }
                           $table_data = hooks()->apply_filters('documents_table_columns',$table_data);
                           array_push($table_data,_l('options'));
                           render_datatable($table_data,'documents_in'); ?>
                       </div>
		</div>
       <div role="tabpanel" class="tab-pane hide" id="tab_renewals">

         <?php if(has_permission('documents', '', 'create') || has_permission('documents', '', 'edit')){ ?>
         <div class="_buttons">
          <a href="#" class="btn btn-default" data-toggle="modal" data-target="#renew_contract_modal">
            <i class="fa fa-refresh"></i> <?php echo _l('contract_renew_heading'); ?>
          </a>
        </div>
        <hr />
        <?php } ?>
        <div class="clearfix"></div>

        <?php
        if(count($contract_renewal_history) == 0){
         echo _l('no_contract_renewals_found');
       }
       foreach($contract_renewal_history as $renewal){ ?>
       <div class="display-block">
        <div class="media-body">
          <div class="display-block">
            <b>
              <?php
              echo _l('contract_renewed_by',$renewal['renewed_by']);
              ?>
            </b>
            <?php if($renewal['renewed_by_staff_id'] == get_staff_user_id() || is_admin()){ ?>
            <a href="<?php echo admin_url('documents/delete_renewal/'.$renewal['id'] . '/'.$renewal['contractid']); ?>" class="pull-right _delete text-danger"><i class="fa fa-remove"></i></a>
            <br />
            <?php } ?>
            <small class="text-muted"><?php echo _dt($renewal['date_renewed']); ?></small>
            <hr class="hr-10" />
            <span class="text-success bold" data-toggle="tooltip" title="<?php echo _l('contract_renewal_old_start_date',_d($renewal['old_start_date'])); ?>">
              <?php echo _l('contract_renewal_new_start_date',_d($renewal['new_start_date'])); ?>
            </span>
            <br />
            <?php if(is_date($renewal['new_end_date'])){
              $tooltip = '';
              if(is_date($renewal['old_end_date'])){
               $tooltip = _l('contract_renewal_old_end_date',_d($renewal['old_end_date']));
             }
             ?>
             <span class="text-success bold" data-toggle="tooltip" title="<?php echo $tooltip; ?>">
              <?php echo _l('contract_renewal_new_end_date',_d($renewal['new_end_date'])); ?>
            </span>
            <br/>
            <?php } ?>
            <?php if($renewal['new_value'] > 0){
              $contract_renewal_value_tooltip = '';
              if($renewal['old_value'] > 0){
               $contract_renewal_value_tooltip = ' data-toggle="tooltip" data-title="'._l('contract_renewal_old_value',_format_number($renewal['old_value'])).'"';
             } ?>
             <span class="text-success bold"<?php echo $contract_renewal_value_tooltip; ?>>
              <?php echo _l('contract_renewal_new_value',_format_number($renewal['new_value'])); ?>
            </span>
            <br />
            <?php } ?>
          </div>
        </div>
        <hr />
      </div>
      <?php } ?>
    </div>
<?php ############################################################################################?>

     <div role="tabpanel" class="tab-pane" id="tab_reminders">
               <a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target=".reminder-modal-document-<?php echo $contract->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('document_set_reminder_title'); ?></a>
               <hr />
                <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
              
               <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$contract->id,'name'=>'document','members'=>$members,'reminder_title'=>_l('document_set_reminder_title'))); ?>
    </div>
<?php ############################################################################################?>


    <div role="tabpanel" class="tab-pane" id="tab_tasks">
      <?php init_relation_tasks_table(array('data-new-rel-id'=>$contract->id,'data-new-rel-type'=>'document')); ?>
    </div>
  </div>
</div>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
<?php init_tail(); ?>
<?php if(isset($contract)){ ?>
<!-- init table tasks -->
<script>
  var contract_id = '<?php echo $contract->id; ?>';
</script>
<?php $this->load->view('admin/documents/send_to_client'); ?>
<?php $this->load->view('admin/documents/renew_document'); ?>
<?php } ?>
<?php $this->load->view('admin/documents/document_type'); ?>
<script>
   init_ajax_case_search_by_customer_id();
    // Ajax project search but only for specific customer
function init_ajax_case_search_by_customer_id(selector) { 
    selector = typeof(selector) == 'undefined' ? '#case_id.ajax-search' : selector;
    init_ajax_search('casediary', selector, {
        customer_id: function() {
            return $('select[name="client"]').val();
        }
    });
} 

/* $('select[name="client"]').on('change',function(){
       customer_init();
     });

  function customer_init(){
        var customer_id = $('select[name="client"]').val();
        var caseAjax = $('select[name="case_id"]');
        
        var clonedCaseAjaxSearchSelect = caseAjax.html('').clone();
        
        var caseWrapper = $('.case-wrapper');

        caseAjax.selectpicker('destroy').remove();

        caseAjax    = clonedCaseAjaxSearchSelect;

        $('#case_ajax_search_wrapper').append(clonedCaseAjaxSearchSelect);

        init_ajax_case_search_by_customer_id();
        
        if(!customer_id){
         //  set_base_currency();
           caseWrapper.addClass('hide');
         }
       $.get(admin_url + 'receipts/get_customer_change_data/'+customer_id,function(response){
         
         if(customer_id && response.customer_has_cases){
           caseWrapper.removeClass('hide');
         } else {
           caseWrapper.addClass('hide');
         }


        
       },'json');
     }*/
//   $("#p").click(function(){
  
//   var data = {};
//          data.contract_id = contract_id;
//          data.content = $("#con").val();
//          $.post(admin_url + 'documents/save_contract_data', data).done(function(response) {
//           response = JSON.parse(response);
//           if (response.success == true) {
//            alert_float('success', response.message);
//          }
//        }).fail(function(error){
//         var response = JSON.parse(error.responseText);
//         alert_float('danger', response.message);
//       });
// });

  Dropzone.autoDiscover = false;
  $(function(){
	  init_ajax_project_search_by_customer_id();
    if($('#contract-attachments-form').length > 0){
        new Dropzone("#contract-attachments-form", $.extend({},_dropzone_defaults(),{
           success:function(file){
            if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
             var location = window.location.href;
             window.location.href= location.split('?')[0]+'?tab=tab_attachments';
           }
         }
       }));
    }

    // In case user expect the submit btn to save the contract content
    $('#contract-form').on('submit',function(){
      $('#inline-editor-save-btn').click();
      return true;
    });

    if(typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0 ){
      document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
        success: function(files) {
         $.post(admin_url+'documents/add_external_attachment',{files:files,contract_id:contract_id,external:'dropbox'}).done(function(){
          var location = window.location.href;
          window.location.href= location.split('?')[0]+'?tab=tab_attachments';
        });
       },
       linkType: "preview",
       extensions: app_allowed_files.split(','),
     }));
    }

    _validate_form($('#contract-form'),{client:'required',datestart:'required',subject:'required'});
    _validate_form($('#renew-contract-form'),{new_start_date:'required'});

  /*  var _templates = [];
    $.each(contract_templates, function(i, template) {
      _templates.push({
        url: admin_url + 'documents/get_template?name=' + template,
        title: template
      });
    });*/
    // alert();
    var editor_settings = {
      selector: 'div.editable',
      //inline: true,
      menu: {
        //file: {title: 'File', items: 'newdocument'},
        edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
        insert: {title: 'Insert', items: 'link media | template hr'},
        view: {title: 'View', items: 'visualaid'},
        format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
        table: {title: 'Table', items: 'inserttable tableprops deletetable | cell row column'}
        //tools: {title: 'Tools', items: 'spellchecker code'}
      },
      branding:false,
      theme: 'modern',
      skin: 'perfex',
      relative_urls: false,
      remove_script_host: false,
      inline_styles : true,
      verify_html : false,
      cleanup : false,
      valid_elements : '+*[*]',
      valid_children : "+body[style], +style[type]",
      apply_source_formatting : false,
      file_browser_callback: elFinderBrowser,
      table_class_list: [{
       title: 'Flat',
       value: 'table'
     }, {
       title: 'Table Bordered',
       value: 'table table-bordered'
     }],
     table_default_styles: {
       width: '100%'
     },
     removed_menuitems: 'newdocument',
     fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
     plugins: [
     'advlist pagebreak autolink autoresize lists link image charmap hr anchor',
     'searchreplace wordcount visualblocks visualchars code',
     'media nonbreaking save table contextmenu directionality',
     'paste textcolor colorpicker'
     ],
     autoresize_bottom_margin: 50,
     pagebreak_separator: '<p pagebreak="true"></p>',
     toolbar1: 'save_button fontselect fontsizeselect insertfile | styleselect',
     //toolbar2:'bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
     toolbar2: 'media image | forecolor backcolor link ',
  
     setup: function(editor) {

      editor.on('blur', function() {
        $.Shortcuts.start();
      });

      editor.on('focus', function() {
       $.Shortcuts.stop();
     });

      editor.addButton('save_button', {
        text: appLang.contract_save,
        icon: false,
        id: 'inline-editor-save-btn',
        onclick: function() {
         var data = {};
         data.contract_id = contract_id;
         data.content = editor.getContent();
         $.post(admin_url + 'documents/save_contract_data', data).done(function(response) {
          response = JSON.parse(response);
          if (response.success == true) {
           alert_float('success', response.message);
         }
       }).fail(function(error){
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
      });
     }
   });
    },
  }
  /*if (_templates.length > 0) {
    editor_settings.templates = _templates;
    editor_settings.plugins[3] = 'template ' + editor_settings.plugins[3];
  }*/

  tinymce.init(editor_settings);
<?php if(isset($contract)){ ?>
        var contract_id = '<?php echo $contract->id; ?>';

        var DocumentsServerParams = {};

        DocumentsServerParams['requested_date_from'] = '[name="requested_date_from"]';
		    DocumentsServerParams['requested_date_to'] = '[name="requested_date_to"]';
        DocumentsServerParams['return_date_from'] = '[name="return_date_from"]';
        DocumentsServerParams['return_date_to'] = '[name="return_date_to"]';

        $.each($('._hidden_inputs._filters input'),function(){
            DocumentsServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
        });

        var headers_documents = $('.table-documents_in').find('th');
        var not_sortable_documents = (headers_documents.length - 1);

        var tAPI=initDataTable('.table-documents_in', admin_url+'documents/table_in/'+contract_id, [not_sortable_documents], [not_sortable_documents], DocumentsServerParams,<?php echo hooks()->do_action('documents_table_default_order',json_encode(array(5,'ASC'))); ?>);
       
        $('input[name="requested_date_from"]').on('change',function(){
            tAPI.ajax.reload();
        });
	    $('input[name="requested_date_to"]').on('change',function(){
            tAPI.ajax.reload();
        });
        $('input[name="return_date_from"]').on('change',function(){
            tAPI.ajax.reload();
        });
        $('input[name="return_date_to"]').on('change',function(){
            tAPI.ajax.reload();
        });

		$.each(ProjectsServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                 tAPI.ajax.reload();
            });
        });
        <?php }?>
});

function delete_contract_attachment(wrapper,id){
     if (confirm_delete()) {
        $.get(admin_url + 'documents/delete_contract_attachment/'+id,function(response){
           if(response.success == true){
            $(wrapper).parents('.contract-attachment-wrapper').remove();
          } else {
            alert_float('danger',response.message);
          }
        },'json');
     }
    return false;
}

function insert_merge_field(field){
   var key = $(field).text();
   tinymce.activeEditor.execCommand('mceInsertContent', false, key);
}

function contract_full_view(){
  $('.left-column').toggleClass('hide');
  $('.right-column').toggleClass('col-md-7');
  $('.right-column').toggleClass('col-md-12');
}
</script>
<script>

  // $('#document_category').on('change', function (e) {
  //       var selectedcategory=$("#document_category option:selected").val();
    
  //       $('#selectedcategory').val(selectedcategory); 
  //       if(selectedcategory=="court"){
  //         $('#safe_uniqueno').val("");
  //         $('#matter').show();
  //         $('#property_name_div').hide();
  //         //$("#project_id").css("display", "none");
  //       }else if(selectedcategory=="PLY" || selectedcategory=="Min"){
  //         $('#property_name_div').hide();
  //         $('#matter').hide();
  //         var Year = (new Date).getFullYear();
  //         var currentYear=Year.toString().slice(-2)
  //         var currentMonth = (new Date).getMonth() + 1;

  //         var category=$("#selectedcategory").val()
          
  //         var valueSelected = category+'.'+''+currentMonth+'/'+currentYear;
  //         $('#safe_uniqueno').val(valueSelected); 
  //       }else{
  //         $('#safe_uniqueno').val(selectedcategory);
  //         $('#matter').hide();
  //         $('#property_name_div').show();
  //       }
       
  //   });
  //   $('#project_id').on('change', function (e) {
  //       var selectedproject=$("#project_id option:selected").text();
  //       var selectedcategory=$("#document_category option:selected").val();
  //       $('#selectedcategory').val(selectedproject); 
         
        
  //       if(selectedcategory=="court"){
  //         $('#safe_uniqueno').val(selectedproject);
  //       }
       
  //   });

  //   $('#property_name').on('change', function (e) {
      
  //     var selectedtext=$("#property_name option:selected").text();
  //     var selectedsubtext = $("#property_name option:selected").attr("data-subtext");
  //     var Year = (new Date).getFullYear();
  //     var currentYear=Year.toString().slice(-2)
  //     var currentMonth = (new Date).getMonth() + 1;

  //     var category=$("#selectedcategory").val()
      
  //     var valueSelected = category+'.'+''+selectedtext+'.'+''+selectedsubtext+'.'+''+currentMonth+'/'+currentYear;
  //     // var valueSelected = category+selectedtext+selectedsubtext+currentMonth+'/'+currentYear;
  //     if(category=="court"){
  //         $('#safe_uniqueno').val("");
  //       }else{
  //         $('#safe_uniqueno').val(valueSelected); 
  //       }
      
  // });
</script>
</body>
</html>
