<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<?php $case_type = ( isset( $case_type ) ? $case_type : 'other_projects' );

	$case1 = ( isset( $project ) ? $project->case_type : $case_type );
	?>
<?php $work_type = ( isset( $work_type ) ? $work_type : 'nonlitigation' );

	
	?>
<div id="wrapper">
    <div class="content">
        <div class="row">
           <?php echo form_open_multipart($this->uri->uri_string(),array('class'=>'project-form','id'=>'project_form')); ?>

            <div class="col-md-8 col-md-offset-2">
                <h4 class="tw-mt-0 tw-font-semibold tw-text-lg tw-text-neutral-700">
                    <?php echo $title; ?>
                </h4>
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="horizontal-scrollable-tabs panel-full-width-tabs">
                            <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
                            <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
                            <div class="horizontal-tabs">
                                <ul class="nav nav-tabs nav-tabs-horizontal" role="tablist">
                                    <li role="presentation" class="active">
                                        <a href="#tab_project" aria-controls="tab_project" role="tab" data-toggle="tab">
                                            <?php echo _l('project'); ?>
                                        </a>
                                    </li>
                                    <li role="presentation">
                                        <a href="#tab_settings" aria-controls="tab_settings" role="tab"
                                            data-toggle="tab">
                                            <?php echo _l('project_settings'); ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="tab-content tw-mt-3">
                            <div role="tabpanel" class="tab-pane active" id="tab_project">
  						<?php
                        $disable_type_edit = '';
                        if(isset($project)){
                            if($project->billing_type != 1){
                                if(total_rows(db_prefix().'tasks',array('rel_id'=>$project->id,'rel_type'=>'project','billable'=>1,'billed'=>1)) > 0){
                                    $disable_type_edit = 'disabled';
                                }
                            }
                        }
                        ?>
                        <?php $value = (isset($project) ? $project->name : ''); ?>
                        <?php echo render_input('name','project_name',$value); ?>
                       
                      
                <?php 
					if($work_type=='litigation')       

						$this->load->view('admin/projects/project_litigation_template'); 
					 else 
						$this->load->view('admin/projects/project_nonlitigation_template'); 
				?>
                                  
                    
                    <div class="form-group">
                        <div class="checkbox checkbox-success">
                            <input type="checkbox" <?php if((isset($project) && $project->progress_from_tasks == 1) || !isset($project)){echo 'checked';} ?> name="progress_from_tasks" id="progress_from_tasks">
                            <label for="progress_from_tasks"><?php echo _l('calculate_progress_through_tasks'); ?></label>
                        </div>
                    </div>
                    <?php
                    if(isset($project) && $project->progress_from_tasks == 1){
                        $value = $this->projects_model->calc_progress_by_tasks($project->id);
                    } else if(isset($project) && $project->progress_from_tasks == 0){
                        $value = $project->progress;
                    } else {
                        $value = 0;
                    }
                    ?>
                    <label for=""><?php echo _l('project_progress'); ?> <span class="label_progress"><?php echo $value; ?>%</span></label>
                    <?php echo form_hidden('progress',$value); ?>
                    <div class="project_progress_slider project_progress_slider_horizontal mbot15"></div>
                    <div class="row">
                       <div class="col-md-6 hide" >
                            <div class="form-group select-placeholder">
                                <label for="billing_type"><?php echo _l('project_billing_type'); ?></label>
                                <div class="clearfix"></div>
                                <select name="billing_type" class="selectpicker" id="billing_type" data-width="100%" <?php echo $disable_type_edit ; ?> data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <option value=""></option>
                                    <option value="1" <?php if(isset($project) && $project->billing_type == 1 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 1){echo 'selected'; } ?>><?php echo _l('project_billing_type_fixed_cost'); ?></option>
                                    <option value="2" <?php if(isset($project) && $project->billing_type == 2 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 2){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_hours'); ?></option>
                                    <option value="3" data-subtext="<?php echo _l('project_billing_type_project_task_hours_hourly_rate'); ?>" <?php if(isset($project) && $project->billing_type == 3 || !isset($project) && $auto_select_billing_type && $auto_select_billing_type->billing_type == 3){echo 'selected'; } ?>><?php echo _l('project_billing_type_project_task_hours'); ?></option>
                                    <option value="4" <?php if(isset($project) && $project->billing_type == 4 ){echo 'selected'; } ?>><?=_l('retainer')?></option>
                                    <option value="5" <?php if(isset($project) && $project->billing_type == 5 ){echo 'selected'; } ?>><?=_l('probono')?></option>
                                    <option value="6" <?php if(isset($project) && $project->billing_type == 6 ){echo 'selected'; } ?>><?=_l('success_fee')?></option>
                                    <option value="7" <?php if(isset($project) && $project->billing_type == 7 ){echo 'selected'; } ?>><?=_l('no_fee_arrangement_yet')?></option>
                                </select>
                                <?php if($disable_type_edit != ''){
                                    echo '<p class="text-danger">'._l('cant_change_billing_type_billed_tasks_found').'</p>';
                                }
                                ?>
                            </div>
                        </div>
					  <div class="col-md-6">
                        <?php $value = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('start_date','project_start_date',$value); ?>
                    </div>

                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="status"><?php echo _l('project_status'); ?></label>
                                <div class="clearfix"></div>
                                <select name="status" id="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach($statuses as $status){ 
									if($status['id']!=4){?>
                                       
                                        <option value="<?php echo $status['id']; ?>" <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>><?php echo $status['name']; ?></option>
                                    <?php } }?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($project) && project_has_recurring_tasks($project->id)) { ?>
                        <div class="alert alert-warning recurring-tasks-notice hide"></div>
                    <?php } ?>
                    <?php if(is_email_template_active('project-finished-to-customer')){ ?>
                        <div class="form-group project_marked_as_finished hide">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="project_marked_as_finished_email_to_contacts" id="project_marked_as_finished_email_to_contacts">
                                <label for="project_marked_as_finished_email_to_contacts"><?php echo _l('project_marked_as_finished_to_contacts'); ?></label>
                            </div>
                        </div>
                    <?php } ?>
                    <?php if(isset($project)){ ?>
                        <div class="form-group mark_all_tasks_as_completed hide">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="mark_all_tasks_as_completed" id="mark_all_tasks_as_completed">
                                <label for="mark_all_tasks_as_completed"><?php echo _l('project_mark_all_tasks_as_completed'); ?></label>
                            </div>
                        </div>
                        <div class="notify_project_members_status_change hide">
                            <div class="checkbox checkbox-primary">
                                <input type="checkbox" name="notify_project_members_status_change" id="notify_project_members_status_change">
                                <label for="notify_project_members_status_change"><?php echo _l('notify_project_members_status_change'); ?></label>
                            </div>
                            <hr />
                        </div>
                    <?php } ?>
                    <?php
                    $input_field_hide_class_total_cost = '';
                    if(!isset($project)){
                        if($auto_select_billing_type && $auto_select_billing_type->billing_type != 1 || !$auto_select_billing_type){
                            $input_field_hide_class_total_cost = 'hide';
                        }
                    } else if(isset($project) && $project->billing_type != 1){
                        $input_field_hide_class_total_cost = 'hide';
                    }
                    ?>
                     <div class="row">
                   <!--     <div class="col-md-6">
                    <div id="project_cost" class="<?php echo $input_field_hide_class_total_cost; ?>">
                        <?php $value = (isset($project) ? $project->project_cost : ''); ?>
                        <?php echo render_input('project_cost','project_total_cost',$value,'number'); ?>
                    </div>
                    <?php
                    $input_field_hide_class_rate_per_hour = '';
                    if(!isset($project)){
                        if($auto_select_billing_type && $auto_select_billing_type->billing_type != 2 || !$auto_select_billing_type){
                            $input_field_hide_class_rate_per_hour = 'hide';
                        }
                    } else if(isset($project) && $project->billing_type != 2){
                        $input_field_hide_class_rate_per_hour = 'hide';
                    }
                    ?>
                    
                   
                        <div id="project_rate_per_hour" class="<?php echo $input_field_hide_class_rate_per_hour; ?>">
                        <?php $value = (isset($project) ? $project->project_rate_per_hour : ''); ?>
                        <?php
                        $input_disable = array();
                        if($disable_type_edit != ''){
                            $input_disable['disabled'] = true;
                        }
                        ?>
                        <?php echo render_input('project_rate_per_hour','project_rate_per_hour',$value,'number',$input_disable); ?>
							</div></div>
                       
                        <div class="col-md-6">
                            <?php echo render_input('estimated_hours','estimated_hours',isset($project) ? $project->estimated_hours : '','number'); ?>
                        </div>-->
                         <div class="col-md-6">
                          <?php  $lawyer = (isset($clawyer_id) ? $lawyer_id : '');
							 $value = (isset($project) ? $project->lawyer_id : $lawyer); ?>
              <?php echo render_select('lawyer_id',$lawyer_staffs,array('staffid',array('firstname','lastname')),'law_firm',$value);
							 ?>
						</div>
                        <div class="col-md-6">
                         <?php
                         $selected = array();
                         if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                        } else {
                            array_push($selected,get_staff_user_id());
							 if(isset($lawyer_id)){
							 array_push($selected,$lawyer_id);	 
							 }
                        }
                        echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                        ?>
                    </div>
                </div>
              
             
                <?php if(isset($project) && $project->date_finished != null && $project->status == 4) { ?>
                    <?php echo render_datetime_input('date_finished','project_completed_date',_dt($project->date_finished)); ?>
                <?php } ?>
                <div class="form-group">
                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                    <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($project) ? prep_tags_input(get_tags_in($project->id,'project')) : ''); ?>" data-role="tagsinput">
                </div>
                <?php $rel_id_custom_field = (isset($project) ? $project->id : false); ?>
                <?php echo render_custom_fields('projects',$rel_id_custom_field); ?>
                <p class="bold"><?php echo _l('project_description'); ?></p>
                <?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
                <?php echo render_textarea('description','',$contents,array(),array(),'','tinymce'); ?>

                <?php if (isset($estimate)) {?>
                <hr class="hr-panel-heading" />
                <h5 class="font-medium"><?php echo _l('estimate_items_convert_to_tasks') ?></h5>
                <input type="hidden" name="estimate_id" value="<?php echo $estimate->id ?>">
                <div class="row">
                    <?php foreach($estimate->items as $item) { ?>
                    <div class="col-md-8 border-right">
                        <div class="checkbox mbot15">
                            <input type="checkbox" name="items[]" value="<?php echo $item['id'] ?>" checked id="item-<?php echo $item['id'] ?>">
                            <label for="item-<?php echo $item['id'] ?>">
                                <h5 class="no-mbot no-mtop text-uppercase"><?php echo $item['description'] ?></h5>
                                <span class="text-muted"><?php echo $item['long_description'] ?></span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div data-toggle="tooltip" title="<?php echo _l('task_single_assignees_select_title'); ?>">
                            <?php echo render_select('items_assignee[]',$staff,array('staffid',array('firstname','lastname')),'', get_staff_user_id(),array('data-actions-box'=>true),array(),'','clean-select',false); ?>
                        </div>
                    </div>
                    <?php } ?>
                </div>
                <?php } ?>
                <hr class="hr-panel-heading" />

                <?php if(is_email_template_active('assigned-to-project')){ ?>
                    <div class="checkbox checkbox-primary">
                     <input type="checkbox" name="send_created_email" id="send_created_email">
                     <label for="send_created_email"><?php echo _l('project_send_created_email'); ?></label>
                 </div>
             <?php } ?>
                            </div>
                            <div role="tabpanel" class="tab-pane" id="tab_settings">
                                <div id="project-settings-area">
                          
           <div class="form-group select-placeholder">
                <label for="contact_notification" class="control-label">
                    <span class="text-danger">*</span>
                    <?php echo _l('projects_send_contact_notification'); ?>
                </label>
                <select name="contact_notification" id="contact_notification" class="form-control selectpicker"
                        data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" required>
                    <?php
                    $options = [
                        ['id'=> 1 , 'name' => _l('project_send_all_contacts_with_notifications_enabled')],
                        ['id'=> 2 , 'name' => _l('project_send_specific_contacts_with_notification')],
                        ['id'=> 0 , 'name' => _l('project_do_not_send_contacts_notifications')]
                    ];
                    foreach ($options as $option) { ?>
                        <option value="<?php echo $option['id']; ?>" <?php if ((isset($project) && $project->contact_notification == $option['id'])) {
                            echo ' selected';
                        } ?>><?php echo $option['name']; ?></option>
                    <?php } ?>
                </select>
            </div>
            <!-- hide class -->
            <div class="form-group select-placeholder <?php echo (isset($project) && $project->contact_notification == 2) ? '' : 'hide' ?>" id="notify_contacts_wrapper">
                <label for="notify_contacts" class="control-label"><span class="text-danger">*</span> <?php echo _l('project_contacts_to_notify') ?></label>
                <select name="notify_contacts[]" data-id="notify_contacts" id="notify_contacts" class="ajax-search" data-width="100%" data-live-search="true"
                data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>" multiple>
                    <?php
                    $notify_contact_ids = isset($project) ? unserialize($project->notify_contacts) : [];
                    foreach ($notify_contact_ids as $contact_id) {
                        $rel_data = get_relation_data('contact',$contact_id);
                        $rel_val = get_relation_values($rel_data,'contact');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                    }
                    ?>
                </select>
            </div>
           <?php foreach($settings as $setting){

            $checked = ' checked';
            if(isset($project)){
                if($project->settings->{$setting} == 0){
                    $checked = '';
                }
            } else {
                foreach($last_project_settings as $last_setting) {
                    if($setting == $last_setting['name']){
                        // hide_tasks_on_main_tasks_table is not applied on most used settings to prevent confusions
                        if($last_setting['value'] == 0 || $last_setting['name'] == 'hide_tasks_on_main_tasks_table'){
                            $checked = '';
                        }
                    }
                }
                if(count($last_project_settings) == 0 && $setting == 'hide_tasks_on_main_tasks_table') {
                    $checked = '';
                }
            } ?>
            <?php if($setting != 'available_features'){ ?>
                <div class="checkbox hide">
                    <input type="checkbox" name="settings[<?php echo $setting; ?>]" <?php echo $checked; ?> id="<?php echo $setting; ?>">
                    <label for="<?php echo $setting; ?>">
                        <?php if($setting == 'hide_tasks_on_main_tasks_table'){ ?>
                            <?php echo _l('hide_tasks_on_main_tasks_table'); ?>
                        <?php } else{ ?>
                            <?php echo _l('project_allow_client_to',_l('project_setting_'.$setting)); ?>
                        <?php } ?>
                    </label>
                </div>
            <?php } else { ?>
                <div class="form-group mtop15 select-placeholder project-available-features">
                    <label for="available_features"><?php echo _l('visible_tabs'); ?></label>
                    <select name="settings[<?php echo $setting; ?>][]" id="<?php echo $setting; ?>" multiple="true" class="selectpicker" id="available_features" data-width="100%" data-actions-box="true" data-hide-disabled="true">
                        <?php foreach(get_project_tabs_admin() as $tab) {
                            $selected = '';
                            if(isset($tab['collapse'])){ ?>
                                <optgroup label="<?php echo $tab['name']; ?>">
                                    <?php foreach($tab['children'] as $tab_dropdown) {
                                        $selected = '';
                                        if(isset($project) && (
                                            (isset($project->settings->available_features[$tab_dropdown['slug']])
                                                && $project->settings->available_features[$tab_dropdown['slug']] == 1)
                                            || !isset($project->settings->available_features[$tab_dropdown['slug']]))) {
                                            $selected = ' selected';
                                    } else if(!isset($project) && count($last_project_settings) > 0) {
                                        foreach($last_project_settings as $last_project_setting) {
                                            if($last_project_setting['name'] == $setting) {
                                                if(isset($last_project_setting['value'][$tab_dropdown['slug']])
                                                    && $last_project_setting['value'][$tab_dropdown['slug']] == 1) {
                                                    $selected = ' selected';
                                            }
                                        }
                                    }
                                } else if(!isset($project)) {
                                    $selected = ' selected';
                                }
                                ?>
                                <option value="<?php echo $tab_dropdown['slug']; ?>"<?php echo $selected; ?><?php if(isset($tab_dropdown['linked_to_customer_option']) && is_array($tab_dropdown['linked_to_customer_option']) && count($tab_dropdown['linked_to_customer_option']) > 0){ ?> data-linked-customer-option="<?php echo implode(',',$tab_dropdown['linked_to_customer_option']); ?>"<?php } ?>><?php echo $tab_dropdown['name']; ?></option>
                            <?php } ?>
                        </optgroup>
                    <?php } else {
                        if(isset($project) && (
                            (isset($project->settings->available_features[$tab['slug']])
                             && $project->settings->available_features[$tab['slug']] == 1)
                            || !isset($project->settings->available_features[$tab['slug']]))) {
                            $selected = ' selected';
                    } else if(!isset($project) && count($last_project_settings) > 0) {
                        foreach($last_project_settings as $last_project_setting) {
                            if($last_project_setting['name'] == $setting) {
                                if(isset($last_project_setting['value'][$tab['slug']])
                                    && $last_project_setting['value'][$tab['slug']] == 1) {
                                    $selected = ' selected';
                            }
                        }
                    }
                } else if(!isset($project)) {
                    $selected = ' selected';
                }
                ?>
                <option value="<?php echo $tab['slug']; ?>"<?php if($tab['slug'] =='project_overview'){echo ' disabled selected';} ?>
                <?php echo $selected; ?>
                <?php if(isset($tab['linked_to_customer_option']) && is_array($tab['linked_to_customer_option']) && count($tab['linked_to_customer_option']) > 0){ ?> data-linked-customer-option="<?php echo implode(',',$tab['linked_to_customer_option']); ?>"<?php } ?>>
                <?php echo $tab['name']; ?>
            </option>
        <?php } ?>
    <?php } ?>
</select>
</div>
<?php } ?>
<hr class="no-margin hide" />
<?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer text-right">
                        <button type="submit" data-form="#project_form" class="btn btn-primary" autocomplete="off"
                            data-loading-text="<?php echo _l('wait_text'); ?>">
                            <?php echo _l('submit'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/casediary/ipcategory'); ?>
<?php $this->load->view('admin/casediary/ipsubcategory'); ?>
<script>
    <?php if(isset($project)){ ?>
        var original_project_status = '<?php echo $project->status; ?>';
	
    <?php } ?>

        $(function(){

		 if ($('#parent_sub').val() == 'submatter') { $('.main_div').removeClass('hide'); }else{ $('.main_div').addClass('hide'); }
            $contacts_select = $('#notify_contacts'),
            $contacts_wrapper = $('#notify_contacts_wrapper'),
            $clientSelect = $('#clientid'),
            $contact_notification_select = $('#contact_notification');

            init_ajax_search('contacts', $contacts_select, {
                rel_id: $contacts_select.val(),
                type: 'contacts',
                extra: {
                    client_id: function () {return $clientSelect.val();}
                }
            });

            if ($clientSelect.val() == '') {
                $contacts_select.prop('disabled', true);

                $contacts_select.selectpicker('refresh');
            } else {
                $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
            }

            $clientSelect.on('changed.bs.select', function () {
                if ($clientSelect.selectpicker('val') == '') {
                    $contacts_select.prop('disabled', true);
                } else {
                    $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
                    $contacts_select.prop('disabled', false);
                }
                deselect_ajax_search($contacts_select[0]);
                $contacts_select.find('option').remove();
                $contacts_select.selectpicker('refresh');
            });

            $contact_notification_select.on('changed.bs.select', function () {
                if ($contact_notification_select.selectpicker('val') == 2) {
                    $contacts_select.siblings().find('input[type="search"]').val(' ').trigger('keyup');
                    $contacts_wrapper.removeClass('hide');
                } else {
                    $contacts_wrapper.addClass('hide');
                    deselect_ajax_search($contacts_select[0]);
                }
            });

        $('select[name="billing_type"]').on('change',function(){
            var type = $(this).val();
            if(type == 1){
                $('#project_cost').removeClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            } else if(type == 2){
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').removeClass('hide');
            } else {
                $('#project_cost').addClass('hide');
                $('#project_rate_per_hour').addClass('hide');
            }
        });
		$('select[name="clientid"]').change(function(){
			get_matters_of_client_ajax();
    
		});
        appValidateForm($('#project_form'), {
            name: 'required',
            clientid: 'required',
            start_date: 'required',
          //  billing_type: 'required',
         //   file_no : 'required',
			//opposite_party:'required',
		//	ledger_code:'required',
			
			 project_stage: {
               required: {
                   depends: function(element) {
                       return ($('select[name="case_type"]').val() != 'intellectual_property' && $('select[name="case_type"]').val() != 'other_projects' && $('select[name="case_type"]').val() != 'legal_consultancy') ? true : false
                   }
               }
           },
            'notify_contacts[]': {
                required: {
                    depends: function() {
                        return !$contacts_wrapper.hasClass('hide');
                    }
                }
            },
        });

        $('select[name="status"]').on('change',function(){
            var status = $(this).val();
            var mark_all_tasks_completed = $('.mark_all_tasks_as_completed');
            var notify_project_members_status_change = $('.notify_project_members_status_change');
            mark_all_tasks_completed.removeClass('hide');
            if(typeof(original_project_status) != 'undefined'){
                if(original_project_status != status){

                    mark_all_tasks_completed.removeClass('hide');
                    notify_project_members_status_change.removeClass('hide');

                    if(status == 4 || status == 5 || status == 3) {
                        $('.recurring-tasks-notice').removeClass('hide');
                        var notice = "<?php echo _l('project_changing_status_recurring_tasks_notice'); ?>";
                        notice = notice.replace('{0}', $(this).find('option[value="'+status+'"]').text().trim());
                        $('.recurring-tasks-notice').html(notice);
                        $('.recurring-tasks-notice').append('<input type="hidden" name="cancel_recurring_tasks" value="true">');
                        mark_all_tasks_completed.find('input').prop('checked',true);
                    } else {
                        $('.recurring-tasks-notice').html('').addClass('hide');
                        mark_all_tasks_completed.find('input').prop('checked',false);
                    }
                } else {
                    mark_all_tasks_completed.addClass('hide');
                    mark_all_tasks_completed.find('input').prop('checked',false);
                    notify_project_members_status_change.addClass('hide');
                    $('.recurring-tasks-notice').html('').addClass('hide');
                }
            }

            if(status == 4){
                $('.project_marked_as_finished').removeClass('hide');
            } else {
                $('.project_marked_as_finished').addClass('hide');
                $('.project_marked_as_finished').prop('checked',false);
            }
        });

        $('#project_form').on('submit',function(){
            $('select[name="billing_type"]').prop('disabled',false);
            $('#available_features,#available_features option').prop('disabled',false);
            $('input[name="project_rate_per_hour"]').prop('disabled',false);
        });

        var progress_input = $('input[name="progress"]');
        var progress_from_tasks = $('#progress_from_tasks');
        var progress = progress_input.val();

        $('.project_progress_slider').slider({
            min:0,
            max:100,
            value:progress,
            disabled:progress_from_tasks.prop('checked'),
            slide: function( event, ui ) {
                progress_input.val( ui.value );
                $('.label_progress').html(ui.value+'%');
            }
        });

        progress_from_tasks.on('change',function(){
            var _checked = $(this).prop('checked');
            $('.project_progress_slider').slider({disabled:_checked});
        });

        $('#project-settings-area input').on('change',function(){
            if($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == false){
                $('#create_tasks').prop('checked',false).prop('disabled',true);
                $('#edit_tasks').prop('checked',false).prop('disabled',true);
                $('#view_task_comments').prop('checked',false).prop('disabled',true);
                $('#comment_on_tasks').prop('checked',false).prop('disabled',true);
                $('#view_task_attachments').prop('checked',false).prop('disabled',true);
                $('#view_task_checklist_items').prop('checked',false).prop('disabled',true);
                $('#upload_on_tasks').prop('checked',false).prop('disabled',true);
                $('#view_task_total_logged_time').prop('checked',false).prop('disabled',true);
            } else if($(this).attr('id') == 'view_tasks' && $(this).prop('checked') == true){
                $('#create_tasks').prop('disabled',false);
                $('#edit_tasks').prop('disabled',false);
                $('#view_task_comments').prop('disabled',false);
                $('#comment_on_tasks').prop('disabled',false);
                $('#view_task_attachments').prop('disabled',false);
                $('#view_task_checklist_items').prop('disabled',false);
                $('#upload_on_tasks').prop('disabled',false);
                $('#view_task_total_logged_time').prop('disabled',false);
            }
        });

            // Auto adjust customer permissions based on selected project visible tabs
            // Eq Project creator disable TASKS tab, then this function will auto turn off customer project option Allow customer to view tasks

            $('#available_features').on('change',function(){
                $("#available_features option").each(function(){
                 if($(this).data('linked-customer-option') && !$(this).is(':selected')) {
                    var opts = $(this).data('linked-customer-option').split(',');
                    for(var i = 0; i<opts.length;i++) {
                        var project_option = $('#'+opts[i]);
                        project_option.prop('checked',false);
                        if(opts[i] == 'view_tasks') {
                            project_option.trigger('change');
                        }
                    }
                }
            });
            });
            $("#view_tasks").trigger('change');
            <?php if(!isset($project)) { ?>
                $('#available_features').trigger('change');
            <?php } ?>
        });
    </script>

<!-----my js------------------->
<script type="text/javascript">

    <?php if(isset($project)) {?> 
        apply_in_available_features('<?php echo $project->case_type ?>'); 
        $('#available_features option[value="project_case_details"]').text('<?=_l($project->case_type)?>');
    <?php }else{ ?>
        $('#available_features option[value="project_case_details"]').text('<?=_l($case_type)?>');

        apply_in_available_features('<?=$case_type?>');
        get_template_by_case_type('<?=$case_type?>');
        get_template_details();
		get_matters_of_client_ajax();
    <?php } ?>
    $('#case_type').change(function(){
        var type = $(this).val();
        apply_in_available_features(type);
        get_template_by_case_type(type)
    }); 

    function apply_in_available_features(type){

        var CaseDetailsTab  = $('#available_features option[value="project_case_details"]');
        var HearingsTab  = $('#available_features option[value="hearings"]');
		var ExpenseTab  = $('#available_features option[value="project_expenses"]');
		var EmailTab  = $('#available_features option[value="communication_center"]');
		var LegalTab  = $('#available_features option[value="project_tickets"]');
		var MeetingsTab  = $('#available_features option[value="project_discussions"]');
		var SettlementTab  = $('#available_features option[value="project_settlement"]');
		var TimesheetTab  = $('#available_features option[value="project_timesheets"]');
		var MilestoneTab  = $('#available_features option[value="project_milestones"]');
		var ScopeTab  = $('#available_features option[value="scope"]');
		var GanttTab  = $('#available_features option[value="project_gantt"]');
		var ContractTab  = $('#available_features option[value="project_contracts"]');
		var JudgeTab= $('#available_features option[value="project_judgment"]');
		var CourtorderTab= $('#available_features option[value="court_order"]');
		var SettleTab= $('#available_features option[value="project_settlement"]');
        var FinalTab= $('#available_features option[value="project_subfiles"]');

        CaseDetailsTab.removeClass('hide');
        HearingsTab.removeClass('hide');
		 ExpenseTab.removeClass('hide');
		EmailTab.removeClass('hide');
		LegalTab.removeClass('hide');
		MeetingsTab.removeClass('hide');
		SettlementTab.removeClass('hide');
		TimesheetTab.removeClass('hide');
		MilestoneTab.removeClass('hide');
		ScopeTab.removeClass('hide');
		GanttTab.removeClass('hide');
		ContractTab.removeClass('hide');
        JudgeTab.removeClass('hide');
        CourtorderTab.removeClass('hide');
        SettlementTab.removeClass('hide');
        FinalTab.removeClass('hide');
       if(type == 'court_case'){ 
            CaseDetailsTab.prop('selected',true);
            HearingsTab.prop('selected',true); 
			ExpenseTab.prop('selected',true);
			EmailTab.prop('selected',true);
			LegalTab.prop('selected',true);
			MeetingsTab.prop('selected',true);
            JudgeTab.prop('selected',true);
            CourtorderTab.prop('selected',true);
            SettlementTab.prop('selected',true);
             FinalTab.removeClass('hide');
        }else if(type == 'labour_case' | type == 'personal_law'){ 
            CaseDetailsTab.prop('selected',true);
            HearingsTab.prop('selected',true); 
			ExpenseTab.prop('selected',true);
			EmailTab.prop('selected',true);
			LegalTab.prop('selected',true);
			MeetingsTab.prop('selected',true);
            JudgeTab.prop('selected',true);
            SettlementTab.prop('selected',true);
            FinalTab.prop('selected',false);
            FinalTab.addClass('hide');
        }else if(type == 'intellectual_property'){ 
            CaseDetailsTab.prop('selected',false);
           CaseDetailsTab.addClass('hide');
			HearingsTab.prop('selected',false);
            HearingsTab.addClass('hide'); 
			ExpenseTab.prop('selected',true);
			EmailTab.prop('selected',true);
			LegalTab.prop('selected',true);
			MeetingsTab.prop('selected',true);
            JudgeTab.prop('selected',false);
            JudgeTab.addClass('hide'); 
			CourtorderTab.prop('selected',false);
            CourtorderTab.addClass('hide');
			SettlementTab.prop('selected',false);
            SettlementTab.addClass('hide');
        }else if(type == 'chequebounce'){ 
            CaseDetailsTab.prop('selected',true);
            HearingsTab.prop('selected',false);
            HearingsTab.addClass('hide'); 
			ExpenseTab.prop('selected',false);
            ExpenseTab.addClass('hide');  
			EmailTab.prop('selected',false);
            EmailTab.addClass('hide');
			LegalTab.prop('selected',false);
            LegalTab.addClass('hide');
			MeetingsTab.prop('selected',false);
            MeetingsTab.addClass('hide');
			SettlementTab.prop('selected',false);
            SettlementTab.addClass('hide');
			TimesheetTab.prop('selected',false);
            TimesheetTab.addClass('hide');
			MilestoneTab.prop('selected',false);
            MilestoneTab.addClass('hide');
			ScopeTab.prop('selected',false);
            ScopeTab.addClass('hide');
			GanttTab.prop('selected',false);
            GanttTab.addClass('hide');
			ContractTab.prop('selected',false);
            ContractTab.addClass('hide');
            JudgeTab.prop('selected',false);
            FinalTab.prop('selected',false);
            FinalTab.addClass('hide');
        }else if(type == 'legal_consultancy' || type == 'policecase' || type == 'other_projects' ){ 
            CaseDetailsTab.prop('selected',true);
			ExpenseTab.prop('selected',true);
			EmailTab.prop('selected',true);
			LegalTab.prop('selected',true);
			MeetingsTab.prop('selected',true);
            HearingsTab.prop('selected',false);
            HearingsTab.addClass('hide');  
            JudgeTab.prop('selected',false);
            JudgeTab.addClass('hide');
			CourtorderTab.prop('selected',false);
            CourtorderTab.addClass('hide');
			SettlementTab.prop('selected',false);
            SettlementTab.addClass('hide');
             FinalTab.prop('selected',false);
            FinalTab.addClass('hide');
        }else{
            CaseDetailsTab.prop('selected',false);
            HearingsTab.prop('selected',false);
            CaseDetailsTab.addClass('hide'); 
            HearingsTab.addClass('hide'); 
            JudgeTab.addClass('hide');
			CourtorderTab.prop('selected',false);
            CourtorderTab.addClass('hide');
			SettlementTab.prop('selected',false);
            SettlementTab.addClass('hide');
             FinalTab.prop('selected',false);
            FinalTab.addClass('hide');
        }
       $('#available_features').selectpicker('refresh');
    }

    function get_template_by_case_type(case_type) { 
            $.get(admin_url + 'casediary/get_matter_templates_by_case_type/'+case_type,function(response){
                if(response){ 
                    var ctype = $('select[name="template_id"]');
                    var options = [];
                    options.push("<option value=''></option>");
                    $.each( response, function( key, value ) {
                      var option = "<option value="+value.id+">" + value.name + "</option>";
                      options.push(option);
                    });
                    ctype.html(options);
                    ctype.selectpicker('refresh');
                     get_template_details();
                } else {
                    alert_float('danger',response.message);
                }
            },'json');
    }


    $('#template_id').on('change',function(){
        get_template_details();
    });

    function get_template_details(id) {
        var id =$('#template_id').val();
        if(id >0){
            $.get(admin_url + 'casetemplates/get_template_details/'+id,function(response){
                if(response.success == true){ 
                    var temp = response.casetemplate;
                   $(tinymce.get('description').getBody()).html(temp.description);
                } else {
                    alert_float('danger',response.message);
                }
            },'json');
        }
    }
	 $('#parent_sub').on('change',function(){
       
    if ($(this).val() == 'submatter') { $('.main_div').removeClass('hide'); }else{ $('.main_div').addClass('hide'); } 
  });
    function get_matters_of_client_ajax() { 
        var clientSelected = $('select[name="clientid"]').val();
        if(clientSelected > 0){
            $.get(admin_url + 'projects/get_matters_of_client/'+clientSelected,function(response){
                var ctype = $('select[name="related_matter"]');
                $('select[name="related_matter"] option').remove();
                if(response ){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
                    <?php if(isset($project)){ 
                            //$opp_ids = array_column($project->assigned_opposite_parties,'opposite_party_id');
                            $loe_id = $project->related_matter;
                            //foreach ($opp_ids as $value) { ?>
                              ctype.selectpicker('val',<?php echo $loe_id ?>);
                            <?php //}
                    } ?>
					   <?php if(isset($related_matter)){ 
                            //$opp_ids = array_column($project->assigned_opposite_parties,'opposite_party_id');
                            $loe_id = $related_matter;
                            //foreach ($opp_ids as $value) { ?>
                              ctype.selectpicker('val',<?php echo $loe_id ?>);
                            <?php //}
                    } ?>i   
                    ctype.selectpicker('refresh');                  
                } else {
                    alert_float('danger','Error');
                }
            },'json');
        }
    }
/*	 $(document).ready(function () {  
        $("#name").keyup(function () {  
            $(this).val($(this).val().toUpperCase());  
        });  
    }); */
	
   $('select[name="ip_category"]').change(function(){
	 if($('#ip_category').val()==6){
		
			   $('#ipothertext').removeClass('hide');
		 	$('#ipsubcat').addClass('hide');
			}else{
				 $('#ipothertext').addClass('hide');
				 $('#ipsubcat').removeClass('hide');
				 // load_ip_subcategory();
				load_ip_subcategory_ajax($(this).val());
			 }
   
   });
   function load_ip_subcategory(){
      var category = $('select[name="ip_category"]').val(); //alert(category);
      $('select[name="ip_subcategory"]').selectpicker('val','');
      $('select[name="ip_subcategory"]').selectpicker('refresh');
      $('select[name="ip_subcategory"] > option').each(function() {
            var subcategory_code  =$(this).data('code');
             $(this).hide();
            if(subcategory_code != category){
               $(this).hide();
            }else{
               $(this).show();
				
            }
         $('select[name="ip_subcategory"]').selectpicker('refresh');

      });
     
   }
	
    function load_ip_subcategory_ajax(categoryid='',subid='') {
        var cateid = typeof (categoryid) != 'undefined' ? categoryid : $('#ip_category').val() ;
		 $('select[name="ip_subcategory"]').selectpicker('val','');
      $('select[name="ip_subcategory"]').selectpicker('refresh');
        var subid = typeof (subid) != 'undefined' ? subid : $('#ip_subcategory').val() ;
        requestGetJSON('casediary/get_ipsub_by_category_id_ajax/' + cateid ).done(function(response) {
				
            var dtype = $('#ip_subcategory');
            $("#ip_subcategory option").remove();
            dtype.append('<option value=""></option>').selectpicker();
            if(response){ 
                //var obj = jQuery.parseJSON(response);
                $.each(response, function(key,value) {
                  console.log();
                  dtype.append('<option value="'+value.id+'">'+value.subcategory_name+'</option>').selectpicker();
                });
                dtype.selectpicker('val',subid);   
                dtype.selectpicker('refresh');
            }
           
        });
    }
</script>
    
<!----------------------------->
</body>
</html>
