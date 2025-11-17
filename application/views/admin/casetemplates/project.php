<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <?php echo form_open($this->uri->uri_string(),array('id'=>'project_form')); ?>
            <div class="col-md-7">
                <div class="panel_s">
                    <div class="panel-body">
                        <h4 class="no-margin">
                            <?php echo $title; ?>
                        </h4>
                        <hr class="hr-panel-heading" />
                        <?php
                        $disable_type_edit = '';
                        if(isset($project)){
                            if($project->billing_type != 1){
                                if(total_rows('tbltasks',array('rel_id'=>$project->id,'rel_type'=>'casetemplates','billable'=>1,'billed'=>1)) > 0){
                                    $disable_type_edit = 'disabled';
                                }
                            }
                        }
                        ?>
                        <?php $selected = (isset($project) ? $project->case_type : $case_type); ?>
                        <!-- <div class="form-group">
                            <label class="control-label"><?php echo _l('related_to'); ?></label>
                            <select class="form-control selectpicker" id="template_rel_to" name="template_rel_to" >
                                <option value="project" <?php if($selected == 'project' ) echo 'selected'; ?>  ><?=_l('projects')?></option>
                                <option value="leads" <?php if($selected == 'leads' ) echo 'selected'; ?>  ><?=_l('leads')?></option> -->
                               <!--  <option value="customer" <?php if($selected == 'customer' ) echo 'selected'; ?>  ><?=_l('customer')?></option> -->
                            <!-- </select> 
                        </div> -->

                        <?php $case_types = get_case_client_types();
                        $case_type = (isset($project) ? $project->case_type : 'court_case');
                        $selected = (isset($project) ? $project->case_type : $case_type);
         
                        //echo render_select('case_type',$case_types,array('id','id'),'case_type',$selected,array());?>
                        <div class="form-group">
                            <label class="control-label"><?php echo _l('case_type'); ?></label>
                            <select class="form-control selectpicker" id="case_type" name="case_type" >
                                <?php foreach($case_types as $case_type){ ?>
                                    <option value="<?=$case_type['id']?>" <?php if($selected == $case_type['id'] ) echo 'selected'; ?>  ><?=_l($case_type['id'])?></option>
                                <?php } ?>
                            </select> 
                        </div>
                       
                        <?php //echo form_hidden('case_type',$selected);?>

                        <?php $value = (isset($project) ? $project->name : ''); ?>
                        <?php echo render_input('name','case_title',$value); ?>

                         
                    <?php
                    if(isset($project) && $project->progress_from_tasks == 1){
                        $value = $this->projects_model->calc_progress_by_tasks($project->id);
                    } else if(isset($project) && $project->progress_from_tasks == 0){
                        $value = $project->progress;
                    } else {
                        $value = 0;
                    }
                    ?>
                    <!-- <label for=""><?php echo _l('project_progress'); ?> <span class="label_progress"><?php echo $value; ?>%</span></label>
                    <?php echo form_hidden('progress',$value); ?>
                    <div class="project_progress_slider project_progress_slider_horizontal mbot15"></div> -->
                    <div class="row hide">
                       
                        <div class="col-md-6">
                            <div class="form-group select-placeholder">
                                <label for="status"><?php echo _l('project_status'); ?></label>
                                <div class="clearfix"></div>
                                <select name="status" id="status" class="selectpicker" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                                    <?php foreach($statuses as $status){ ?>
                                    <option value="<?php echo $status['id']; ?>" <?php if(!isset($project) && $status['id'] == 2 || (isset($project) && $project->status == $status['id'])){echo 'selected';} ?>><?php echo $status['name']; ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                   
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
                    <div id="project_cost" class="<?php echo $input_field_hide_class_total_cost; ?> hide" >
                        <?php $value = (isset($project) ? $project->project_cost : ''); ?>
                        <?php echo render_input('project_cost','matter_total_cost',$value,'number'); ?>
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
                    </div>
                    <div class="row ">
                        <div class="col-md-6" style="display: none;">
                            <?php echo render_input('estimated_hours','estimated_hours',isset($project) ? $project->estimated_hours : '','number'); ?>
                        </div>
                        <div class="col-md-12 hide">
                           <?php
                           $selected = array();
                           if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                        } else {
                            array_push($selected,get_staff_user_id());
                        }
                        echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'project_members',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                        ?>
                    </div>
                </div>
                <div class="row hide">
                    <div class="col-md-6">
                        <?php $value = (isset($project) ? _d($project->start_date) : _d(date('Y-m-d'))); ?>
                        <?php echo render_date_input('start_date','project_start_date',$value); ?>
                    </div>
                    <div class="col-md-6">
                        <?php $value = (isset($project) ? _d($project->deadline) : ''); ?>
                        <?php echo render_date_input('deadline','project_deadline',$value); ?>
                    </div>
                </div>
                <?php if(isset($project) && $project->date_finished != null && $project->status == 4) { ?>
                    <?php echo render_datetime_input('date_finished','project_completed_date',_dt($project->date_finished)); ?>
                <?php } ?>
                <div class="form-group hide">
                    <label for="tags" class="control-label"><i class="fa fa-tag" aria-hidden="true"></i> <?php echo _l('tags'); ?></label>
                    <input type="text" class="tagsinput" id="tags" name="tags" value="<?php echo (isset($project) ? prep_tags_input(get_tags_in($project->id,'project')) : ''); ?>" data-role="tagsinput">
                </div>
                <?php $rel_id_custom_field = (isset($project) ? $project->id : false); ?>
                <?php echo render_custom_fields('projects',$rel_id_custom_field); ?>
                <p class="bold"><?php echo _l('project_description'); ?></p>
                <?php $contents = ''; if(isset($project)){$contents = $project->description;} ?>
                <?php echo render_textarea('description','',$contents,array(),array(),'','tinymce'); ?>
                <?php if(total_rows('tblemailtemplates',array('slug'=>'assigned-to-project','active'=>0)) == 0){ ?>
                <div class="checkbox checkbox-primary hide">
                   <input type="checkbox" name="send_created_email" id="send_created_email">
                   <label for="send_created_email"><?php echo _l('case_send_created_email'); ?></label>
               </div>
               <?php } ?>
               <div class="btn-bottom-toolbar text-right">
                   <button type="submit" data-form="#project_form" class="btn btn-info" autocomplete="off" data-loading-text="<?php echo _l('wait_text'); ?>"><?php echo _l('submit'); ?></button>
               </div>
           </div>
       </div>
   </div>
   

 

<?php echo form_close(); ?>
</div>
<div class="btn-bottom-pusher"></div>
</div>
</div>
<?php init_tail(); ?>
<script>
    <?php if(isset($project)){ ?>
        var original_project_status = '<?php echo $project->status; ?>';
        <?php } ?>
        $(function(){

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

            _validate_form($('form'),{name:'required'});

            $('select[name="status"]').on('change',function(){
                var status = $(this).val();
                var mark_all_tasks_completed = $('.mark_all_tasks_as_completed');
                var notify_project_members_status_change = $('.notify_project_members_status_change');
                mark_all_tasks_completed.removeClass('hide');
                if(typeof(original_project_status) != 'undefined'){
                    if(original_project_status != status){
                        mark_all_tasks_completed.removeClass('hide');
                        mark_all_tasks_completed.find('input').prop('checked',true);
                        notify_project_members_status_change.removeClass('hide');
                    } else {
                        mark_all_tasks_completed.addClass('hide');
                        mark_all_tasks_completed.find('input').prop('checked',false);
                        notify_project_members_status_change.addClass('hide');
                    }
                }
                if(status == 4){
                    $('.project_marked_as_finished').removeClass('hide');
                } else {
                    $('.project_marked_as_finished').addClass('hide');
                    $('.project_marked_as_finished').prop('checked',false);
                }
            });

            $('form').on('submit',function(){
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
</body>
</html>
<script type="text/javascript">
    $('#template_id').on('change',function(){
        var id =$('#template_id').val();
        if(id >0){
            var  newp = $('#name').val();
            var  newc = $('#clientid').val();
            $('#copy_project').modal('show');
            
            if (typeof(id) != 'undefined') {
                $('#copy_form').attr('action', $('#copy_form').data('copy-url') + id);
            }
            $('#name').val();$('#clientid').val();
            $('#copy_project_name').val(newp);
            $('#clientid_copy_project').val(newc);
        }
    });

   /* $('#case_type').change(function(){ 
        var case_type = $('#case_type').val();
        $('#court_case_settings').hide();
        $('#legal_consultancy_settings').hide();
        $('#police_complaints_settings').hide();
        $('#legal_drafting_settings').hide();
        $('#intellectual_property_settings').hide();
        $('#arbitrations_settings').hide();
        $('#'+case_type+'_settings').show();
    });
*/
</script>