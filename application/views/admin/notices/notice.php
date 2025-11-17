<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         
<?php if(isset($notice)) { ?>
   <div class="col-md-12">
      <div class="panel_s">
         <div class="panel-body">
            <h4 class="no-margin"><?php echo $notice->subject; ?><?php 
            if(!empty($notice->notice_refno)){
               echo ' - '.$notice->notice_refno;
            }
          ?>
          
               <?php if($notice->other_party!=0){?>
                        <a href="<?php echo admin_url('opposite_parties/opposite_party/'.$notice->other_party); ?>" class="pull-right" >
                  <?php echo get_opposite_party_name($notice->other_party);?>
               </a>
               <?php } ?></h4><br> 

               <?php if($notice->project_id!=0){?>
                        <a href="<?php echo admin_url('projects/view/'.$notice->project_id); ?>" class="btn btn-info pull-right" >
                  <?php echo _l('goback_project'); ?>
               </a>
               <?php } ?></h4><br>

			 <span class="pull-right" style="font-size:14px;font-weight: bold;padding-right:85px;"> <?php echo 'Prepared By : '. get_staff_full_name($notice->addedfrom); ?></span>
            <a href="<?php echo site_url('notice/'.$notice->id.'/'.$notice->hash); ?>" target="_blank">
               <?php echo _l('view_notice'); ?>
            </a>
          
                <?php if($notice->ticketid!=0 && get_option('enable_legal_request')==1 ){?>
              <a href="<?php echo admin_url('tickets/ticket/'.$notice->ticketid); ?>" target="_blank" class="pull-right" >
               <?php echo _l('view_ticket'); ?>
            </a>
            <?php } ?>
            <hr class="hr-panel-heading" />
            <?php if($notice->trash > 0){
               echo '<div class="ribbon default"><span>'._l('notice_trash').'</span></div>';
            } ?>
            <div class="horizontal-scrollable-tabs preview-tabs-top">
               <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
               <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
               <div class="horizontal-tabs">
                  <ul class="nav nav-tabs tabs-in-body-no-margin notice-tab nav-tabs-horizontal mbot15" role="tablist">
                     <li role="presentation" class="<?php if(!$this->input->get('tab') || $this->input->get('tab') == 'tab_content'){echo 'active';} ?>">
                        <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
                           <?php echo _l('notice_notice'); ?>
                        </a>
                     </li>
					 <li role="presentation" class="<?php if($this->input->get('tab') == 'tab_notice'){echo 'active';} ?> <?php if(empty($notice->notice_template_id)) echo 'hide';?>">
                        <a href="#tab_notice" aria-controls="tab_notice" role="tab" data-toggle="tab">
                           <?php echo _l('notice_content'); ?>
                        </a>
                     </li> 
                     <li role="presentation" class="<?php if($this->input->get('tab') == 'tab_version'){echo 'active';} ?>">
                        <a href="#tab_version" aria-controls="tab_version" role="tab" data-toggle="tab">
                           <?php echo _l('notice_version'); ?>
                        </a>
                     </li> 

                     <li role="presentation" class="<?php if($this->input->get('tab') == 'attachments'){echo 'active';} ?>">
                        <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
                           <?php echo _l('notice_attachments'); ?>
                           <?php if($totalAttachments = count($notice->attachments)) { ?>
                             <span class="badge attachments-indicator"><?php echo $totalAttachments; ?></span>
                          <?php } ?>
                       </a>
                    </li>
                    <li role="presentation">
                     <a href="#tab_comments" aria-controls="tab_comments" class="<?php if($this->input->get('tab') == 'comments'){echo 'active';} ?>" role="tab" data-toggle="tab" onclick="get_notice_comments(); return false;">
                        <?php echo _l('notice_comments'); ?>
                        <?php
                        $totalComments = total_rows(db_prefix().'notice_comments','notice_id='.$notice->id)
                        ?>
                        <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
                     </a>
                  </li>
                  <li role="presentation" class="<?php if($this->input->get('tab') == 'approvals'){echo 'active';} ?> <?php if($notice->is_nonstandard == 0){echo 'hide';} ?>">
                     <a href="#approvals" aria-controls="approvals" role="tab" data-toggle="tab">
                        <?php echo _l('approvals'); ?>
                       </a>
                  </li>
                  <li class="hide" role="presentation" class="<?php if($this->input->get('tab') == 'renewals'){echo 'active';} ?>">
                     <a href="#renewals" aria-controls="renewals" role="tab" data-toggle="tab">
                        <?php echo _l('no_notice_renewals_history_heading'); ?>
                        <?php if($totalRenewals = count($notice_renewal_history)) { ?>
                           <span class="badge"><?php echo $totalRenewals; ?></span>
                        <?php } ?>
                     </a>
                  </li> 
				 <li role="presentation" class="<?php if($this->input->get('tab') == 'trackings'){echo 'active';} ?>">
                     <a href="#trackings" aria-controls="trackings" role="tab" data-toggle="tab">
                        <?php echo _l('no_notice_tracking_history_heading'); ?>
                       
                     </a>
                  </li>
                   
                  <li role="presentation" class="tab-separator">
                     <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab" onclick="init_rel_tasks_table(<?php echo $notice->id; ?>,'notice'); return false;">
                        <?php echo _l('tasks'); ?>
                     </a>
                  </li>
                        <li role="presentation"  class="<?php if($this->input->get('tab') == 'reminder'){echo 'active';} ?>">
                              <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $notice->id ;?> + '/' + 'notice', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
                                 <?php echo _l('set_reminder'); ?>
                                 <?php
                                 $total_reminders = total_rows(db_prefix().'reminders',
                                   array(
                                     'isnotified'=>0,
                                     'staff'=>get_staff_user_id(),
                                     'rel_type'=>'notice',
                                     'rel_id'=>$notice->id
                                  )
                                );
                                 if($total_reminders > 0){
                                   echo '<span class="badge">'.$total_reminders.'</span>';
                                }
                                ?>
                             </a>
                          </li>
                  <li role="presentation" class="tab-separator">
                     <a href="#tab_notes" onclick="get_sales_notes(<?php echo $notice->id; ?>,'notices'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
                        <?php echo _l('updates'); ?>
                        <span class="notes-total">
                           <?php if($totalNotes > 0){ ?>
                              <span class="badge"><?php echo $totalNotes; ?></span>
                           <?php } ?>
                        </span>
                     </a>
                  </li>
                 <!-- <li role="presentation" class="tab-separator">-->
                  <a href="#tab_templates" onclick="get_templates('notices', <?php echo $notice->id ?>); return false" aria-controls="tab_templates" role="tab" data-toggle="tab" class="hide">
                        <?php echo _l('templates'); ?>
                     </a>
                  </li>
                  <!--------------activity_log-------------------------------->
                  <li role="presentation" class="tab-separator">
                  <a href="#tab_activitylog" aria-controls="tab_activitylog" role="tab" data-toggle="tab">
                        <?php echo _l('activitylog'); ?>
                     </a>
                  </li>
                  <!--------------activity_log-------------------------------->
                  <li role="presentation" data-toggle="tooltip" title="<?php echo _l('emails_tracking'); ?>" class="tab-separator">
                     <a href="#tab_emails_tracking" aria-controls="tab_emails_tracking" role="tab" data-toggle="tab">
                        <?php if(!is_mobile()){ ?>
                           <i class="fa fa-envelope-open-o" aria-hidden="true"></i>
                        <?php } else { ?>
                           <?php echo _l('emails_tracking'); ?>
                        <?php } ?>
                     </a>
                  </li>
                  <li role="presentation" class="tab-separator toggle_view hide">
                     <a href="#" onclick="notice_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>">
                        <i class="fa fa-expand"></i></a>
                     </li>
                  </ul>
               </div>
            </div>
            <div class="tab-content">
            <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab') || $this->input->get('tab') == 'tab_content'){echo ' active';} ?>" id="tab_content">
                  <div class="row">
                                    <?php if($notice->signed == 1){ ?>
                                       <div class="col-md-12">
                                          <div class="alert alert-success">
                                             <?php echo _l('document_signed_info',array(
                                                '<b>'.$notice->acceptance_firstname . ' ' . $notice->acceptance_lastname . '</b> (<a href="mailto:'.$notice->acceptance_email.'">'.$notice->acceptance_email.'</a>)',
                                                '<b>'. _dt($notice->acceptance_date).'</b>',
                                                '<b>'.$notice->acceptance_ip.'</b>')
                                             ); ?>
                                          </div>
                                       </div>
                                       <?php } else if($notice->marked_as_signed == 1) { ?>
                                          <div class="col-md-12">
                                             <div class="alert alert-info">
                                                <?php echo _l('notice_marked_as_signed_info'); ?>
                                             </div>
                                          </div>
                                       <?php } ?>
					   <?php if($notice->party_signed == 1){ ?>
                                       <div class="col-md-12">
                                          <div class="alert alert-info">
                                             <?php echo _l('partydocument_signed_info',array(
                                                '<b>'.$notice->partyacc_firstname . ' ' . $notice->partyacc_lastname . '</b> (<a href="mailto:'.$notice->partyacc_email.'">'.$notice->partyacc_email.'</a>)',
                                                '<b>'. _dt($notice->partyacc_date).'</b>',
                                                '<b>'.$notice->partyacc_ip.'</b>')
                                             ); ?>
                                          </div>
                                       </div>
                                       <?php }?>
                                    <div class="col-md-12 text-right _buttons">
                                             <div class="btn-group">
                                                      <?php if(isset($notice) && $notice->notice_filename == ''){ ?>
                                                         <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_notice'); ?>" class="btn btn-info" onclick="upload_noticefile(<?php echo $notice->id; ?>); return false;">
                                                         <i class="fa fa-upload"></i>
                                                         <?php echo _l('upload_notice'); ?>
                                                         </a>
                                                         
                                                         <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf-o"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                                                         <ul class="dropdown-menu dropdown-menu-right">
                                                         <li class="hidden-xs"><a href="<?php echo admin_url('notices/pdf/'.$notice->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                                                         <li class="hidden-xs"><a href="<?php echo admin_url('notices/pdf/'.$notice->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                                         <li><a href="<?php echo admin_url('notices/pdf/'.$notice->id); ?>"><?php echo _l('download'); ?></a></li>
                                                         <li>
                                                         <a href="<?php echo admin_url('notices/pdf/'.$notice->id.'?print=true'); ?>" target="_blank">
                                                         <?php echo _l('print'); ?>
                                                         </a>
                                                         </li>
                                                         </ul>
                                                      <?php }
                                                      else{
                                                         $totalversions = total_rows(db_prefix().'notice_versions','noticeid='.$notice->id);
                                                         if($totalversions>0){
                                                            $latest_version=get_current_notice_versioninfo($notice->id);
                                                         
                                                            $path1 = site_url('download/downloadagreementversion/'. $latest_version->noticeid.'/'.$latest_version->id);
                                                   
                                                            $file_path   = get_upload_path_by_type('notice').$latest_version->noticeid.'/'.$latest_version->version_internal_file_path;
                                                            if(file_exists($file_path)){ 
                                                            
                                                               $dispaly = '<a href="'. $path1 .'"  class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" download title="'._l('latest_agreement').'" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i></a>';
                                                                  
                                                               echo $dispaly;
                                                            }
                                                         }
                                                         else{
                                                            ?>
												  <?php if($notice->marked_as_signed ==0 && $notice->signed == 0 && $notice->party_signed == 0){ ?>
                                                            <a href="#" onclick="delete_notice_document(<?php echo $notice->id; ?>); return false;" class="btn btn-danger mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_notice')?></a>
												 <?php } ?>
                                                            <?php 
                                                            $file_path   = get_upload_path_by_type('notice').$notice->id.'/';
                                                               //$lpath        = base_url('uploads/notices/').$notice->id.'/';
                                                            $path = site_url('download/downloadagreement/'. $notice->id); 															   
                                                            if(file_exists($file_path.$notice->notice_filename)){ ?>
                                                               <a download href="<?php echo $path ; ?>"  class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" download title="<?php echo _l('latest_agreement'); ?>" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i></a>
                                                            <?php }
                                                            $path = site_url('download/downloadagreement/'. $notice->id); ?>
                                                               <!-- <a download href="<?php echo $path;?>"  class="btn btn-default maleft10"><i class="fa fa-download"></i> <?php echo _l('notice'); ?></a>
                                                               <a href="#" onclick="delete_notice_document(<?php echo $notice->id; ?>); return false;" class="btn btn-default mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_notice')?></a>  -->       
                                                            <?php 
                                                         }
                                                      }?>
					<?php if(isset($notice) && $notice->notice_template_id != ''){ ?>
                                                        
                                                         
                                                         <a href="#" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf-o"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>
                                                         <ul class="dropdown-menu dropdown-menu-right">
                                                         <li class="hidden-xs"><a href="<?php echo admin_url('notices/pdf/'.$notice->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>
                                                         <li class="hidden-xs"><a href="<?php echo admin_url('notices/pdf/'.$notice->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>
                                                         <li><a href="<?php echo admin_url('notices/pdf/'.$notice->id); ?>"><?php echo _l('download'); ?></a></li>
                                                         <li>
                                                         <a href="<?php echo admin_url('notices/pdf/'.$notice->id.'?print=true'); ?>" target="_blank">
                                                         <?php echo _l('print'); ?>
                                                         </a>
                                                         </li>
                                                         </ul>
                                                      <?php }?>
                                    <!------------------signed notice--------------------------------------------------------------->
                                    <?php if(isset($notice) && ($notice->marked_as_signed == 1 || ( $notice->signed == 1 && $notice->party_signed == 1))){ ?>
                                                      <?php if(isset($notice) && $notice->signed_notice_filename == ''){ ?>
                                                         <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_signednotice'); ?>" class="btn btn-info" onclick="upload_signed_noticefile(<?php echo $notice->id; ?>); return false;">
                                                         <i class="fa fa-upload"></i>
                                                         <?php echo _l('upload_signednotice'); ?>
                                                         </a>
                                                      <?php }else{
                                                         $file_path   = get_upload_path_by_type('notice').$notice->id.'/';
                                                         $path1 = site_url('download/downloadsigned_agreement/'.$notice->id); 
                                                         if(file_exists($file_path.$notice->signed_notice_filename)){?>
                                                            <a href="#" onclick="delete_signed_notice_document(<?php echo $notice->id; ?>);" class="btn btn-danger mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_signed_notice')?></a>
                                                            <a download href="<?php echo $path1; ?>" class="btn btn-success btn-with-tooltip mright5" data-toggle="tooltip" download title="<?php echo _l('signed_notice'); ?>" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i></a>
                                                         <?php } 
                                                      } ?>
                                    <?php } ?>
                                    <!-------------------signed notice-------------------------------------------------------------->
                                          
                                       
                                             </div>
                                             <a href="#" class="btn btn-default" data-target="#notice_send_to_client_modal" data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('notice_send_to_email'); ?>" data-placement="bottom">
                                                <i class="fa fa-envelope"></i></span>
                                             </a>
                                             <!-- <a href="#" class="btn btn-warning" data-target="#notice_send_for_approval" data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('notice_send_for_approval'); ?>" data-placement="bottom">
                                                <i class="fa fa-envelope" style="color:white;"></i></span>
                                             </a> -->
                                                   <a target="_blank" href="<?php echo admin_url('notices/legalnotice_approval/'.$notice->id); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Download notice Approval"> <i class="fa fa-file-pdf-o"></i> notice Approval </a>
                                             <div class="btn-group">
                                                <button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                   <?php echo _l('more'); ?> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
                                                   <li>
                                                      <a href="<?php echo site_url('notice/'.$notice->id.'/'.$notice->hash); ?>" target="_blank">
                                                         <?php echo _l('view_notice'); ?>
                                                      </a>
                                                   </li>
                                                   <?php
                                                   if($notice->sended == 0 && $notice->signed == 0 && staff_can('edit', 'notices')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/mark_as_send_sms/'.$notice->id); ?>">
                                                         <?php echo _l('mark_as_send_sms'); ?>
                                                      </a>
                                                   </li>
                                                   <?php } ?>
                                                   <?php
                                                   if($notice->sended == 0 && staff_can('edit', 'notices')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/mark_as_send/'.$notice->id); ?>">
                                                         <?php echo _l('mark_as_send_email'); ?>
                                                      </a>
                                                   </li>
                                             
                                                <?php } ?>
                                          
                                                   <?php
                                                   if($notice->signed == 0 && $notice->marked_as_signed == 0 && staff_can('edit', 'notices')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/mark_as_signed/'.$notice->id); ?>">
                                                         <?php echo _l('mark_as_signed'); ?>
                                                      </a>
                                                   </li>
                                                <?php } else if($notice->signed == 0 && $notice->marked_as_signed == 1 && staff_can('edit', 'notices')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/unmark_as_signed/'.$notice->id); ?>">
                                                         <?php echo _l('unmark_as_signed'); ?>
                                                      </a>
                                                   </li>
                                                <?php } ?>
                                                <?php hooks()->do_action('after_notice_view_as_client_link', $notice); ?>
                                                <?php if(has_permission('notices','','create')){ ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/copy/'.$notice->id); ?>">
                                                         <?php echo _l('notice_copy'); ?>
                                                      </a>
                                                   </li>
                                                <?php } ?>
                                                <?php if($notice->signed == 1 && has_permission('notices','','delete')){ ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/clear_signature/'.$notice->id); ?>" class="_delete">
                                                         <?php echo _l('clear_signature'); ?>
                                                      </a>
                                                   </li>
                                                <?php } ?>
                                                <?php if(has_permission('notices','','delete')){ ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('notices/delete/'.$notice->id); ?>" class="_delete">
                                                         <?php echo _l('delete'); ?></a>
                                                      </li>
                                                   <?php } ?>
                                                </ul>
                                             </div>
                                    </div>

                                    
                 </div>
                 <hr class="hr-panel-heading" />
                 <?php if(!staff_can('edit','notices')) { ?>
                  <div class="alert alert-warning notice-edit-permissions">
                     <?php echo _l('notice_content_permission_edit_warning'); ?>
                  </div>
               <?php } ?>

              <?php //} ?>
			
         <div class="panel_s">
         <div class="panel-body">
                  <?php echo form_open($this->uri->uri_string(),array('id'=>'notice-form')); ?>
                  <div class="form-group">
                     <div class="checkbox checkbox-primary no-mtop checkbox-inline hide">
                        <input type="checkbox" id="trash" name="trash"<?php if(isset($notice)){if($notice->trash == 1){echo ' checked';}}; ?>>
                        <label for="trash"><i class="fa fa-question-circle" data-toggle="tooltip" data-placement="left" title="<?php echo _l('notice_trash_tooltip'); ?>" ></i> <?php echo _l('notice_trash'); ?></label>
                     </div>
                     <div class="checkbox checkbox-primary checkbox-inline hide">
                        <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client" <?php if(isset($notice)){if($notice->not_visible_to_client == 1){echo 'checked';}}; ?>>
                        <label for="not_visible_to_client"><?php echo _l('notice_not_visible_to_client'); ?></label>
                     </div>
					     <div class="checkbox checkbox-primary checkbox-inline">
                        <input type="checkbox" name="is_nonstandard" id="not_visible_to_client" <?php if(isset($notice)){if($notice->is_nonstandard == 1){echo 'checked';}}; ?> style="pointer-events: none;">
                        <label for="is_nonstandard"><?php echo _l('is_nonstandard'); ?></label>
                     </div>
                  </div> 
			   
                  
                  <?php if(get_option('enable_legal_request')==1) { ?>
                    <?php $selected = (isset($notice) ? $notice->ticketid : ''); ?>
                    <?php  echo render_select('ticketid',$requests,array('ticketid',array('ticketid','subject')),'legal_request',$selected,array(),array(),'','',true,'-');?>
                  <?php } ?>
                  <div class="col-md-4"  <?php if(isset($notice)){?> style="pointer-events: none;"<?php }?>>
                 <div class="form-group select-placeholder f_client_id">
                     <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('notice_client_string'); ?></label>
                     <select id="clientid" name="client" data-live-search="true" data-width="100%" class="ajax-search select" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php $selected = (isset($notice) ? $notice->client : '');
                        if($selected == ''){
                         $selected = (isset($customer_id) ? $customer_id: '16');
                      }
                      if($selected != ''){
                        $rel_data = get_relation_data('customer',$selected);
                        $rel_val = get_relation_values($rel_data,'customer');
                        echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
                     } ?>
                  </select>
               </div>
            </div>
            <div class="col-md-4">
             <?php $value = (isset($notice) ? $notice->subject : ''); ?>
            <i class="fa fa-question-circle pull-left" data-toggle="tooltip" title="<?php echo _l('notice_subject_tooltip'); ?>"></i>
            <?php echo render_input('subject','notice_subject',$value); ?>
            </div>
            <div class="col-md-4">
               <div class="form-group select-placeholder projects-wrapper<?php if((isset($notice) && !customer_has_projects($notice->client))){ echo ' hide';} ?>">
                  <label for="project_id"><?php echo _l('project'); ?></label>
                  <div id="project_ajax_search_wrapper">
                    <select name="project_id" id="project_id" class="projects ajax-search ays-ignore" data-live-search="true" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                       <?php
                       if(isset($notice) && $notice->project_id != 0){
                        echo '<option value="'.$notice->project_id.'" selected>'.get_project_name_by_id($notice->project_id).'</option>';
                     }
                     ?>
                  </select>
               </div>
            </div>
            </div>
            <?php ########## Opposite Party ##############  ?>
         <!-- <div class="col-md-6"> -->
         <div class="col-md-4"  <?php if(isset($notice)){?> style="pointer-events: none;"<?php }?>>
           <?php $selected = (isset($notice) ? $notice->other_party: '');
                        if($selected == ''){
                         $selected = (isset($party_id) ? $party_id: '');
                      }
            /*if(is_admin() ){
            echo render_select_with_input_group('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty',$selected,'<a href="#" onclick="new_opposite_party();return false;"><i class="fa fa-plus"></i></a>');
            } else {*/
				    echo render_select('other_party',$oppositeparty_names,array('id','name'),'opposite_party',$selected);
           // echo render_input('other_party','other_party',$value);
           // }?>
         </div> 
		
         <div class="col-md-4">
            <div class="form-group">
               <label for="notice_value"><?php echo _l('notice_value'); ?></label>
               <div class="input-group" data-toggle="tooltip" title="<?php echo _l('notice_value_tooltip'); ?>">
                  <input type="number" class="form-control" id= "notice_value" name="notice_value" value="<?php if(isset($notice)){echo $notice->notice_value; }?>">
                  <div class="input-group-addon">
                     <?php echo $base_currency->symbol; ?>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4"  <?php if(isset($notice)){?> style="pointer-events: none;"<?php }?>>
            <?php
            $selected = (isset($notice) ? $notice->notice_type : '');
            if(is_admin() || get_option('staff_members_create_inline_notice_types') == '1'){
              echo render_select_with_input_group('notice_type',$types,array('id','name'),'notice_type',$selected,'<a href="#" onclick="new_type();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('notice_type',$types,array('id','name'),'notice_type',$selected);
         }
         ?>
			 
         <?php $value = (isset($notice) ? $notice->type_stamp : '');
            
           // echo render_input('type_stamp','term',$value);
         ?>
        </div>
			
         
           <div class="col-md-4" id="div_template1" <?php if(isset($notice)){?> style="pointer-events: none;"<?php }?>>
             <?php 
                           
                $selected = (isset($notice) ? $notice->notice_template_id : '');?>
                  <?php  echo render_select('notice_template_id',$templates,array('id','name'),'notice_template',$selected); ?>
          </div>
		   <div class="col-md-4">
               <?php $value = (isset($notice) ? _d($notice->datestart) : _d(date('Y-m-d'))); ?>
               <?php echo render_date_input('datestart','notice_start_date',$value); ?>
            </div>
			   <div class="col-md-4">
               <?php $value = (isset($notice) ? _d($notice->final_expiry_date) : ''); ?>
               <?php echo render_date_input('final_expiry_date','final_expiry_date',$value); ?>
            </div>
            <div class="col-md-4">
               <?php $value = (isset($notice) ? _d($notice->dateend) : ''); ?>
               <?php echo render_date_input('dateend','notice_end_date',$value); ?>
            </div>
			 <div class="col-md-4 hide">
			 <?php $selected = (isset($notice) ? $notice->payment_terms : '');
                     $payment_terms=get_payment_terms();  
                        echo render_select('payment_terms',$payment_terms,array('id','name'),'payment_terms',$selected,array());?>
			 </div>
			 <div id="notice_install" class="hide">
            <div class="col-md-4">
               <?php $value = (isset($notice) ? $notice->no_of_installment : ''); ?>
               <?php echo render_input('no_of_installment','no_of_installment',$value); ?>
            </div>
            <div class="col-md-4">
               <?php $value = (isset($notice) ? _d($notice->default_effective_date) : ''); ?>
               <?php echo render_date_input('default_effective_date','default_effective_date',$value); ?>
            </div>
           <div class="col-md-4">
               <?php $value = (isset($notice) ? $notice->installment_amount : ''); ?>
               <?php echo render_input('installment_amount','installment_amount',$value,'number'); ?>
            </div>
			</div>
			 <div class="col-md-4">
			   <?php $selected = (isset($notice) ? $notice->status : '2'); ?>
                      <?php echo render_select('status',$statuses,array('id','name'),'status',$selected,array(),array(),'','',false); ?>
			 </div>
			
              <div class="col-md-4 hide">
                       
                     <?php
                         $selected = array();
                         if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                        } else {
                            array_push($selected,get_staff_user_id());
                        }
                        echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'notice_assignees',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                        ?>
                    
                     </div>
                           
                             <div class="col-md-4 mtop35 hide">
                  <div class="checkbox checkbox-primary billable">
               <input type="checkbox" id="is_autorenewal" name="is_autorenewal" <?php if(isset($notice)){if($notice->is_autorenewal == 1){echo 'checked';}}; ?>>
               <label for="is_autorenewal"><?php echo _l('is_autorenewal'); ?></label>
            </div>
				   </div>
          <div class="col-md-8">
         <?php $value = (isset($notice) ? $notice->description : ''); ?>
         <?php echo render_textarea('description','notice_description',$value,array('rows'=>3)); ?>
					  </div>
         <?php $rel_id = (isset($notice) ? $notice->id : false); ?>
         <?php echo render_custom_fields('notices',$rel_id); ?>
         <div class="btn-bottom-toolbar text-right">
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
      </div>
      </div>
	<!-----------tab notice------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'tab_notice'){echo ' active';} ?> <?php if(empty($notice->notice_template_id)) echo 'hide';?>" id="tab_notice">
         <div class="row mtop20">
	
         <?php  if(isset($notice)&& !empty($notice->notice_template_id)){ ?>
			 	<div class="col-md-12 mbot25">
						  <?php if(isset($notice_merge_fields)){ ?>
                              <hr class="hr-panel-heading" />
                              <p class="bold mtop10 text-right"> <a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                              <div class="avilable_merge_fields col-md-12 mtop15 hide">
                                 <div class="col-md-12" style="border: 1px solid blue;">
                                    <?php
                                    foreach($notice_merge_fields as $field){
										
                                      foreach($field as $f){
										  echo '<div class="col-md-2" style="border: 1px solid #D1D4DD;height: 30px;">';
                                         echo '<b>'.$f['name'].'</b></div><div class="col-md-2" style="border: 1px solid #D1D4DD;height: 30px;"><span>  <a href="#" class="" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></span>';
										  echo '</div>';
                                      }
										
                                   }
                                   ?>
                                </div>
                             </div>
                          <?php } ?>
					</div>
                                       <div class="col-md-12 hide">
                                          <?php if(isset($notice_merge_fields)){ ?>
                                             <hr class="hr-panel-heading" />
                                             <p class="bold mtop10 text-right"> <a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                                             <div class=" avilable_merge_fields mtop15 hide">
                                                <ul class="list-group">
                                                   <?php
                                                   foreach($notice_merge_fields as $field){
                                                   foreach($field as $f){
                                                      echo '<li class="list-group-item"><b>'.$f['name'].'</b>  <a href="#" class="pull-right" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></li>';
                                                   }
                                                }
                                                ?>
                                             </ul>
                                          </div>
                                       <?php } ?>
                                       </div>
			         <div class="col-md-12 hide">
                        <?php if(isset($notice_closure_fields)){ ?>
                           <hr class="hr-panel-heading" />
                           <p class="bold mtop10 text-right"><a href="#" onclick="slideToggle('.avilable_closure_fields'); return false;"><?php echo _l('available_closure'); ?></a></p>
                           <div class=" avilable_closure_fields mtop15 hide">
                              <ul class="list-group">
                                 <?php
                                 foreach($notice_closure_fields as $f1){?>
                                   
                                      <li class="list-group-item"><b><?=$f1['name']?></b>  <a href="#" class="pull-right" onclick="insert_template(this,'notices',<?php echo $f1['id']; ?>);return false;" ><?=$f1['name']?></a></li>
									 
                                   
                               <?php }
                                ?>
                             </ul>
                          </div>
                       <?php } ?>
						
                    </div>
                                 <?php } ?> 
     
         </div>
		      <hr class="hr-panel-heading" />
                 <?php if(!staff_can('edit','notices')) { ?>
                  <div class="alert alert-warning notice-edit-permissions">
                     <?php echo _l('notice_content_permission_edit_warning'); ?>
                  </div>
               <?php } ?>
               <div class="tc-content<?php if(staff_can('edit','notices')){echo ' editable';} ?>"
                  style="border:1px solid #d2d2d2;min-height:70px; border-radius:4px;">
                  <?php
                  if(empty($notice->content) && staff_can('edit','notices')){
                    echo hooks()->apply_filters('new_notice_default_content', '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>');
                 } else {
                    echo $notice->content;
                 }
                 ?>
      </div>
		  	 <div class="row mtop25 mbot20">
              <?php if(!empty($notice->signature)) { ?>
              
                  <div class="col-md-6  text-left">
                     <div class="bold">
						 <h4><?php echo _l('first_party')?></h4>
                        <p class="no-mbot"><?php echo _l('notice_signed_by') . ": {$notice->acceptance_firstname} {$notice->acceptance_lastname}"?></p>
                        <p class="no-mbot"><?php echo _l('notice_signed_date') . ': ' . _dt($notice->acceptance_date) ?></p>
                        <p class="no-mbot"><?php echo _l('notice_signed_ip') . ": {$notice->acceptance_ip}"?></p>
                     </div>
                     <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                     <?php if($notice->signed == 1 && has_permission('notices','','delete')){ ?>
                        <a href="<?php echo admin_url('notices/clear_signature/'.$notice->id); ?>" data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>" class="_delete text-danger">
                           <i class="fa fa-remove"></i>
                        </a>
                     <?php } ?>
                     </p>
                     <div class="pull-left">
                        <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_upload_path_by_type('notice').$notice->id.'/'.$notice->signature)); ?>" class="img-responsive" alt="">
                     </div>
               </div>
           
         <?php } ?>
		     
             
				    <?php if(!empty($notice->party_signature)) { ?>
                  <div class="col-md-6 text-right">
                     <div class="bold">
						  <h4><?php echo _l('second_party')?></h4>
                        <p class="no-mbot"><?php echo _l('notice_signed_by') . ": {$notice->partyacc_firstname} {$notice->partyacc_lastname}"?></p>
                        <p class="no-mbot"><?php echo _l('notice_signed_date') . ': ' . _dt($notice->partyacc_date) ?></p>
                        <p class="no-mbot"><?php echo _l('notice_signed_ip') . ": {$notice->partyacc_ip}"?></p>
                     </div>
                     <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                     <?php if($notice->party_signed == 1 && has_permission('notices','','delete')){ ?>
                        <a href="<?php echo admin_url('notices/clear_signature/'.$notice->id); ?>" data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>" class="_delete text-danger">
                           <i class="fa fa-remove"></i>
                        </a>
                     <?php } ?>
                     </p>
                     <div class="pull-right">
                        <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_upload_path_by_type('notice').$notice->id.'/'.$notice->party_signature)); ?>" class="img-responsive" alt="">
                     </div>
               </div>
          
         <?php } ?>
				     </div>
				</div>
<!-----------tab content------------------------------------------------------------>
<!-----------tab version------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'tab_version'){echo ' active';} ?>" id="tab_version">
         <div class="row mtop20">

         <?php if(isset($notice)&& $notice->notice_filename == ''){ ?>
                                       <div class="col-md-12">
                                          <?php if(isset($notice_merge_fields)){ ?>
                                             <hr class="hr-panel-heading" />
                                             <p class="bold mtop10 text-right hide"> <a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                                             <div class=" avilable_merge_fields mtop15 hide">
                                                <ul class="list-group">
                                                   <?php
                                                   foreach($notice_merge_fields as $field){
                                                   foreach($field as $f){
                                                      echo '<li class="list-group-item"><b>'.$f['name'].'</b>  <a href="#" class="pull-right" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></li>';
                                                   }
                                                }
                                                ?>
                                             </ul>
                                          </div>
                                       <?php } ?>
                                       </div>
                                 <?php }else{ ?> 
                                       <hr class="hr-panel-heading" />
                                       <p class="bold mtop10 text-right"> 
                                          <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_notice'); ?>" class="btn btn-info" onclick="upload_noticeversionfile(<?php echo $notice->id; ?>); return false;">
                                       <i class="fa fa-upload"></i>
                                       <?php echo _l('upload_noticeversion'); ?>
                                    </a>
                                          <?php
                                    if(get_option('enable_sharepoint')==1){?>
                                          <?php if($totalversions==0){?>
                                          <a href="<?php echo $notice->sharepoint_link; ?>" target="_blank" class="btn btn-warning btn-sm mleft20" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit_base') ?> </a>
                                          <?php } ?>
                                          <a href="#" class="btn btn-success btn-sm mright10" onclick="save_as_notice_new_version(<?php $notice->id ?>); return false;"><!-- <i class=" fa fa-info-circle pull-left fa-lg" data-toggle="tooltip" data-title="<?php echo _l('load_latest_content_from_sharepoint_info'); ?>"></i> --> <?php echo _l('load_latest_content_from_sharepoint') ?> <i class="fa fa-arrow-circle-down"></i></a>
                                          <?php } ?>
                                       </p>
                              <?php } ?>

         <?php
                  // select all notice versions
                  $notice_versions = get_all_notice_versions($notice->id); ?>
                    <table 
	class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('version')?></th>
			<th width="20%"><?php echo _l('file_name')?></th>
			<th><?php echo _l('version_added_date')?></th>
			<th><?php echo _l('version_added')?></th>
			<th><?php echo _l('action')?></th>
			<th><?php echo _l('mark_as_final')?></th>
      <th><?php echo _l('active')?></th>
		</tr>
	</thead>



   <?php foreach ($notice_versions as $notice_version){?>
      <tr>
      		<td><?php echo $notice_version['version']?></td>
			<td width="20%"><?php echo $notice_version['version_internal_file_path'];?>
			<?php if(get_option('enable_sharepoint')==1){?>
			 <!-- <a href="<?php echo $notice_version['version_sharpoint_link'] ?>"  target="_blank" class="btn btn-warning btn-sm mleft10" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit') ?> </a>-->
			 <?php } ?>
			 </td>
			
			<td><?php echo _d($notice_version['dateadded'])?></td>
			<td><?php echo get_staff_full_name($notice_version['addedby'])?></td>
			<td><?php
		 $path1 = site_url('download/downloadagreementversion/'. $notice_version['noticeid'].'/'.$notice_version['id']);
			 
    $file_path   = get_upload_path_by_type('notice').$notice_version['noticeid'].'/'.$notice_version['version_internal_file_path'];
    if(file_exists($file_path)){ 
	
		$dispaly = '<a href="'. $path1 .'"  class="btn btn-sm btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('download').'" data-placement="bottom"><i class="fa fa-download" aria-hidden="true"></i></a>';
		
		echo $dispaly;
	}else{
        echo  '-';
    }
	 ?>
	
			</td>
			<td>
				                    <?php if($notice_version['active']== 1){?>
                              <?php if($notice->final_doc != $notice_version['id']){  ?>
                              <a class="btn btn-success" title=" <?php echo _l('mark_as_final') ?>" href="<?php echo admin_url('notices/mark_as_final_doc/'.$notice->id.'/'.$notice_version['id']) ?>"><i class="fa fa-star-o"></i></a>
                              <?php }} ?>
                              <?php echo icon_btn('notices/delete_version/' . $notice_version['id'].'/'.$notice_version['noticeid'], 'remove', 'btn-danger _delete hide'); ?>
			</td>
      <td>

		   <?php
            $checked = '';
            if($notice_version['active'] == 1){
              $checked = 'checked';
            }
            ?>
		  <div class="onoffswitch">
              <input type="checkbox" data-switch-url="<?php echo admin_url(); ?>notices/change_version_status" id="<?php echo $notice_version['id']; ?>" data-id="<?php echo $notice_version['id']; ?>" class="onoffswitch-checkbox" value="<?php echo $notice_version['id']; ?>" <?php echo $checked; ?>>
              <label class="onoffswitch-label" for="<?php echo $notice_version['id']; ?>"></label>
            </div>
      </td>
		</tr>
   <?php } ?>
   </table>
         </div>
      </div>
<!-----------tab version------------------------------------------------------------>
        <div role="tabpanel" class="tab-pane" id="tab_reminders">
                  <a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target=".reminder-modal-notice-<?php echo $notice->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('set_reminder'); ?></a>
                  <hr />
                  <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
               </div>
      <div role="tabpanel" class="tab-pane" id="tab_notes">
         <?php echo form_open(admin_url('notices/add_note/'.$notice->id),array('id'=>'sales-notes','class'=>'notice-notes-form')); ?>
         <?php echo render_textarea('description'); ?>
         <div class="text-right">
            <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('notice_add_note'); ?></button>
         </div>
         <?php echo form_close(); ?>
         <hr />
         <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
         </div>
      </div>
      <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'comments'){echo ' active';} ?>" id="tab_comments">
         <div class="row notice-comments mtop15">
            <div class="col-md-12">
               <div id="notice-comments"></div>
               <div class="clearfix"></div><?php if(isset($notice)){ if($notice->marked_as_signed == 1){ $readonly='readonly';} else{ $readonly='';} } ?>
               <textarea name="content" id="comment" rows="4" class="form-control mtop15 notice-comment" <?php echo $readonly;?>></textarea>
               <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_notice_comment();"><?php echo _l('proposal_add_comment'); ?></button>
            </div>
         </div>
      </div>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'attachments'){echo ' active';} ?>" id="attachments">
         <?php echo form_open(admin_url('notices/add_notice_attachment/'.$notice->id),array('id'=>'notice-attachments-form','class'=>'dropzone')); ?>
         <?php echo form_close(); ?>
         <div class="text-right mtop15">
            <button class="gpicker" data-on-pick="noticeGoogleDriveSave">
               <i class="fa fa-google" aria-hidden="true"></i>
               <?php echo _l('choose_from_google_drive'); ?>
            </button>
            <div id="dropbox-chooser"></div>
            <div class="clearfix"></div>
         </div>
         <!-- <img src="https://drive.google.com/uc?id=14mZI6xBjf-KjZzVuQe8-rjtv_wXEbDTw" /> -->

         <div id="notice_attachments" class="mtop30">
            <?php
            $data = '<div class="row">';
            foreach($notice->attachments as $attachment) {
             $href_url = site_url('download/file/notice/'.$attachment['attachment_key']);
             if(!empty($attachment['external'])){
              $href_url = $attachment['external_link'];
           }
           $data .= '<div class="display-block notice-attachment-wrapper">';
           $data .= '<div class="col-md-10">';
           $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
           $data .= '<a href="'.$href_url.'"'.(!empty($attachment['external']) ? ' target="_blank"' : '').'>'.$attachment['file_name'].'</a>';
           $data .= '<p class="text-muted">'.$attachment["filetype"].'</p>';
           $data .= '</div>';
           $data .= '<div class="col-md-2 text-right">';
           if($attachment['staffid'] == get_staff_user_id() || is_admin()){
            $data .= '<a href="#" class="text-danger" onclick="delete_notice_attachment(this,'.$attachment['id'].'); return false;"><i class="fa fa fa-times"></i></a>';
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
<!-----------tab tracking------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'trackings'){echo ' active';} ?>" id="trackings">
         <div class="row mtop20">
         <div class="col-md-12">
         <?php
                  // select all notice trackings ?>
                
                    <table 
	class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('date')?></th>
			<th width="20%"><?php echo _l('time')?></th>
			<th><?php echo _l('status_place')?></th>
			<th><?php echo _l('version_added')?></th>
			<th><?php echo _l('status')?></th>
			
		</tr>
	</thead>



   <?php foreach ($notice_trackings as $tracking){?>
      <tr>
      		<td><?php echo $tracking['status_date']?></td>
      		<td><?php echo $tracking['status_time']?></td>
      		<td><?php echo $tracking['status_place']?></td>
      		<td><?php echo $tracking['tracking_status']?></td>

	
		</tr>
   <?php } ?>
   </table>
         </div>
         </div>
      </div>
<!-----------tab version------------------------------------------------------------>
<div role="tabpanel" class="hide tab-pane<?php if($this->input->get('tab') == 'renewals'){echo ' active';} ?>" id="renewals">
   <?php if(has_permission('notices', '', 'create') || has_permission('notices', '', 'edit')){ ?>
      <div class="_buttons">
         <a href="#" class="btn btn-default" data-toggle="modal" data-target="#renew_notice_modal">
            <i class="fa fa-refresh"></i> <?php echo _l('notice_renew_heading'); ?>
         </a>
      </div>
      <hr />
   <?php } ?>
   <div class="clearfix"></div>
   <?php
   if(count($notice_renewal_history) == 0){
     echo _l('no_notice_renewals_found');
  }
  foreach($notice_renewal_history as $renewal){ ?>
   <div class="display-block">
      <div class="media-body">
         <div class="display-block">
            <b>
               <?php
               echo _l('notice_renewed_by',$renewal['renewed_by']);
               ?>
            </b>
            <?php if($renewal['renewed_by_staff_id'] == get_staff_user_id() || is_admin()){ ?>
               <a href="<?php echo admin_url('notices/delete_renewal/'.$renewal['id'] . '/'.$renewal['noticeid']); ?>" class="pull-right _delete text-danger"><i class="fa fa-remove"></i></a>
               <br />
            <?php } ?>
            <small class="text-muted"><?php echo _dt($renewal['date_renewed']); ?></small>
            <hr class="hr-10" />
              <?php if($renewal['renewed_by_staff_id'] == get_staff_user_id() || is_admin()){ ?>
                <a  href="<?php echo site_url('download/downloadnoticefile/').$renewal['noticeid'].'/'.$renewal['id']; ?>" class="pull-right text-info" title="Download Renewal" ><i class="fa fa-download"></i></a>
               
               <br />
            <?php } ?>
            <span class="text-success bold" data-toggle="tooltip" title="<?php echo _l('notice_renewal_old_start_date',_d($renewal['old_start_date'])); ?>">
               <?php echo _l('notice_renewal_new_start_date',_d($renewal['new_start_date'])); ?>
            </span>
            <br />
            <?php if(is_date($renewal['new_end_date'])){
               $tooltip = '';
               if(is_date($renewal['old_end_date'])){
                 $tooltip = _l('notice_renewal_old_end_date',_d($renewal['old_end_date']));
              }
              ?>
              <span class="text-success bold" data-toggle="tooltip" title="<?php echo $tooltip; ?>">
               <?php echo _l('notice_renewal_new_end_date',_d($renewal['new_end_date'])); ?>
            </span>
            <br/>
         <?php } ?>
         <?php if($renewal['new_value'] > 0){
            $notice_renewal_value_tooltip = '';
            if($renewal['old_value'] > 0){
              $notice_renewal_value_tooltip = ' data-toggle="tooltip" data-title="'._l('notice_renewal_old_value', app_format_money($renewal['old_value'], $base_currency)).'"';
           } ?>
           <span class="text-success bold"<?php echo $notice_renewal_value_tooltip; ?>>
            <?php echo _l('notice_renewal_new_value', app_format_money($renewal['new_value'], $base_currency)); ?>
         </span>
         <br />
      <?php } ?>
   </div>
</div>
<hr />
</div>
<?php } ?>
</div>
<div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'approvals'){echo ' active';} ?>" id="approvals">
   <?php if(has_permission('notices', '', 'create') || has_permission('notices', '', 'edit') ){ ?>
       
      <div class="_buttons">
        <?php 
					$service='notice';?>
         <?php if(!get_notice_count($notice->id,$service)){ ?>
         <a class="btn btn-info" href="#" onclick="load_approval_modal('<?php echo admin_url('approval/approvals?rel_name='.$service.'&rel_id='.$notice->id); ?>');return false;"><?=_l('new_approval')?></a>
         <?php }else{?>
			 <a class="btn btn-info" href="#" onclick="load_approval_modal('<?php echo admin_url('approval/approvals?rel_name='.$service.'&rel_id='.$notice->id); ?>');return false;"><?=_l('edit_approval')?></a>			
				<?php	} ?>
      </div>
      <hr />
   <?php } ?>
   <div class="clearfix"></div>
    <div id="div_approvals_list"></div>

</div>
<div role="tabpanel" class="tab-pane" id="tab_emails_tracking">
   <?php
   $this->load->view('admin/includes/emails_tracking',array(
    'tracked_emails'=>
    get_tracked_emails($notice->id, 'notice'))
);
?>
</div>
<div role="tabpanel" class="tab-pane" id="tab_tasks">
   <?php init_relation_tasks_table(array('data-new-rel-id'=>$notice->id,'data-new-rel-type'=>'notice')); ?>
</div>
<div role="tabpanel" class="tab-pane hide" id="tab_templates">
   <div class="row notice-templates">
      <div class="col-md-12">
         <button type="button" class="btn btn-info" onclick="add_template('notices', <?php echo $notice->id ?>);"><?php echo _l('add_template'); ?></button>
         <hr>
      </div>
      <div class="col-md-12">
         <div id="notice-templates" class="notice-templates-wrapper"></div>
      </div>
   </div>
</div>
<!----------------------activitylog------------------------------------->
<div role="tabpanel" class="tab-pane" id="tab_activitylog">
               <div class="panel_s no-shadow">
                  <div class="activity-feed">
                     <?php foreach($activity_log as $log){ ?>
                     <div class="feed-item">
                        <div class="date">
                           <span class="text-has-action" data-toggle="tooltip" data-title="<?php echo _dt($log['date']); ?>">
                           <?php echo time_ago($log['date']); ?>
                           </span>
                        </div>
                        <div class="text">
                           <?php if($log['staffid'] != 0){ ?>
                           <a href="<?php echo admin_url('profile/'.$log["staffid"]); ?>">
                           <?php echo staff_profile_image($log['staffid'],array('staff-profile-xs-image pull-left mright5'));
                              ?>
                           </a>
                           <?php
                              }
                              $additional_data = '';
                              if(!empty($log['additional_data'])){
                               $additional_data = unserialize($log['additional_data']);
                               echo ($log['staffid'] == 0) ? _l($log['description'],$additional_data) : $log['full_name'] .' - '._l($log['description'],$additional_data);
                              } else {
                                  echo $log['full_name'] . ' - ';
                                 if($log['custom_activity'] == 0){
                                    echo _l($log['description']);
                                 } else {
                                    echo _l($log['description'],'',false);
                                 }
                              }
                              ?>
                        </div>
                     </div>
                     <?php } ?>
                  </div>
                 
                  <div class="clearfix"></div>
               </div>
            </div>
         <!------activitylog----------------------------------->
</div>
</div>
</div>
</div>
<?php } ?>
</div>
</div>
</div>
<div id="modal-wrapper"></div>
<?php init_tail(); ?>
<?php if(isset($notice)){ ?>
   <!-- init table tasks -->
   <script>
      var notice_id = '<?php echo $notice->id; ?>';
  </script>
   
   <?php $this->load->view('admin/notices/send_to_client'); ?>
   <?php //$this->load->view('admin/notices/send_for_approval'); ?>
   <?php $this->load->view('admin/notices/renew_notice'); ?>
   <?php $this->load->view('admin/notices/notice_type'); ?>
   <?php $this->load->view('admin/notices/external_notice_upload'); ?>
   <?php $this->load->view('admin/notices/external_notice_version_upload'); ?>
   <?php $this->load->view('admin/notices/external_signed_notice_upload'); ?>
<?php $this->load->view('admin/approval/approval_js'); ?>
<script type="text/javascript">
	init_approval_table( '<?php echo $service; ?>', '<?php echo $notice->id; ?>');
</script>
<!-- The reminders modal -->
<?php $this->load->view('admin/includes/modals/reminder',array(
   'id'=>$notice->id,
   'name'=>'notice',
   'members'=>$staff,
   'reminder_title'=>_l('set_reminder'))
); ?>
<?php } ?>


<script>
   Dropzone.autoDiscover = false;
   $(function () {
	  // get_templates_of_notice_ajax();
     init_ajax_project_search_by_customer_id();
     if ($('#notice-attachments-form').length > 0) {
        new Dropzone("#notice-attachments-form",appCreateDropzoneOptions({
           success: function (file) {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                 var location = window.location.href;
                 window.location.href = location.split('?')[0] + '?tab=attachments';
              }
           }
        }));
     }

    // In case user expect the submit btn to save the notice content
    $('#notice-form').on('submit', function () {
     $('#inline-editor-save-btn').click();
     return true;
  });

    if (typeof (Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
     document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
        success: function (files) {
           $.post(admin_url + 'notices/add_external_attachment', {
              files: files,
              notice_id: notice_id,
              external: 'dropbox'
           }).done(function () {
              var location = window.location.href;
              window.location.href = location.split('?')[0] + '?tab=attachments';
           });
        },
        linkType: "preview",
        extensions: app.options.allowed_files.split(','),
     }));
  }

  appValidateForm($('#notice-form'), {
     client: 'required',
     datestart: 'required',
     subject: 'required'
  });

  appValidateForm($('#renew-notice-form'), {
     new_start_date: 'required'
  });

  var _templates = [];
  $.each(noticesTemplates, function (i, template) {
     _templates.push({
        url: admin_url + 'notices/get_template?name=' + template,
        title: template
     });
  });

  var editor_settings = {
     selector: 'div.editable',
     inline: true,
     theme: 'inlite',
     relative_urls: false,
     remove_script_host: false,
     inline_styles: true,
     verify_html: false,
     cleanup: false,
     apply_source_formatting: false,
     valid_elements: '+*[*]',
     valid_children: "+body[style], +style[type]",
     file_browser_callback: elFinderBrowser,
     table_default_styles: {
        width: '100%'
     },
     fontsize_formats: '8pt 10pt 12pt 14pt 18pt 24pt 36pt',
     pagebreak_separator: '<p pagebreak="true"></p>',
     plugins: [
     'advlist pagebreak autolink autoresize lists link image charmap hr',
     'searchreplace visualblocks visualchars code',
     'media nonbreaking table contextmenu',
     'paste textcolor colorpicker'
     ],
     autoresize_bottom_margin: 50,
     insert_toolbar: 'image media quicktable | bullist numlist | h2 h3 | hr',
     selection_toolbar: 'save_button bold italic underline superscript | forecolor backcolor link | alignleft aligncenter alignright alignjustify | fontselect fontsizeselect h2 h3',
     contextmenu: "image media inserttable | cell row column deletetable | paste pastetext searchreplace | visualblocks pagebreak charmap | code",
     setup: function (editor) {

        editor.addCommand('mceSave', function () {
           save_notice_content(true);
        });

        editor.addShortcut('Meta+S', '', 'mceSave');

        editor.on('MouseLeave blur', function () {
           if (tinymce.activeEditor.isDirty()) {
              save_notice_content();
           }
        });

        editor.on('MouseDown ContextMenu', function () {
           if (!is_mobile() && !$('.left-column').hasClass('hide')) {
              notice_full_view();
           }
        });

        editor.on('blur', function () {
           $.Shortcuts.start();
        });

        editor.on('focus', function () {
           $.Shortcuts.stop();
        });

     }
  }

  if (_templates.length > 0) {
     editor_settings.templates = _templates;
     editor_settings.plugins[3] = 'template ' + editor_settings.plugins[3];
     editor_settings.contextmenu = editor_settings.contextmenu.replace('inserttable', 'inserttable template');
  }

  if(is_mobile()) {

     editor_settings.theme = 'modern';
     editor_settings.mobile    = {};
     editor_settings.mobile.theme = 'mobile';
     editor_settings.mobile.toolbar = _tinymce_mobile_toolbar();

     editor_settings.inline = false;
     window.addEventListener("beforeunload", function (event) {
      if (tinymce.activeEditor.isDirty()) {
         save_notice_content();
      }
   });
  }

  tinymce.init(editor_settings);
  var tab1='<?php echo $tab; ?>';
	   if(tab1=='comments')
        get_notice_comments();
	   
var cttype = $('#payment_terms').val();
		
		noticeinstallment_action(cttype);
});
 $('#ticketid').on('change', function() {
							
				var department = $(this).val();
				var url=admin_url+'tickets/getTicketInfo';
				// AJAX request
			$.ajax({
				url:url,
				method: 'post',
				data: {ticketid: department},
				dataType: 'json',
				success: function(response){
					// $('#other_party').val(response.opposteparty);
					 $('#type_stamp').val(response.stamp_type);
					 $('#subject').val(response.subject);
					 $('#notice_value').val(response.file_amount);
					$('#other_party').selectpicker('val',response.opposteparty);
					
					 var ctype = $('#client');
				ctype.find('option:first').after('<option value="'+response.userid+'">'+response.company+'</option>');
                ctype.selectpicker('val',response.userid);
                ctype.selectpicker('refresh');
												
				}
			});
		});
function save_notice_content(manual) {
  var editor = tinyMCE.activeEditor;
  var data = {};
  data.notice_id = notice_id;
  data.content = editor.getContent();
  $.post(admin_url + 'notices/save_notice_data', data).done(function (response) {
     response = JSON.parse(response);
     if (typeof (manual) != 'undefined') {
          // Show some message to the user if saved via CTRL + S
          alert_float('success', response.message);
       }
       // Invokes to set dirty to false
       editor.save();
    }).fail(function (error) {
     var response = JSON.parse(error.responseText);
     alert_float('danger', response.message);
  });
 }

 function delete_notice_attachment(wrapper, id) {
  if (confirm_delete()) {
     $.get(admin_url + 'notices/delete_notice_attachment/' + id, function (response) {
        if (response.success == true) {
           $(wrapper).parents('.notice-attachment-wrapper').remove();

           var totalAttachmentsIndicator = $('.attachments-indicator');
           var totalAttachments = totalAttachmentsIndicator.text().trim();
           if(totalAttachments == 1) {
            totalAttachmentsIndicator.remove();
         } else {
            totalAttachmentsIndicator.text(totalAttachments-1);
         }
      } else {
        alert_float('danger', response.message);
     }
  }, 'json');
  }
  return false;
}

function insert_merge_field(field) {
  var key = $(field).text();
  tinymce.activeEditor.execCommand('mceInsertContent', false, key);
}

function notice_full_view() {
  $('.left-column').toggleClass('hide');
  $('.right-column').toggleClass('col-md-7');
  $('.right-column').toggleClass('col-md-12');
  $(window).trigger('resize');
}

function add_notice_comment() {
  var comment = $('#comment').val();
  if (comment == '') {
     return;
  }
  var data = {};
  data.content = comment;
  data.notice_id = notice_id;
  $('body').append('<div class="dt-loader"></div>');
  $.post(admin_url + 'notices/add_comment', data).done(function (response) {
     response = JSON.parse(response);
     $('body').find('.dt-loader').remove();
     if (response.success == true) {
        $('#comment').val('');
        get_notice_comments();
     }
  });
}

function get_notice_comments() {
  if (typeof (notice_id) == 'undefined') {
     return;
  }
  requestGet('notices/get_comments/' + notice_id).done(function (response) {
     $('#notice-comments').html(response);
     var totalComments = $('[data-commentid]').length;
     var commentsIndicator = $('.comments-indicator');
     if(totalComments == 0) {
      commentsIndicator.addClass('hide');
   } else {
      commentsIndicator.removeClass('hide');
      commentsIndicator.text(totalComments);
   }
});
}

function remove_notice_comment(commentid) {
  if (confirm_delete()) {
     requestGetJSON('notices/remove_comment/' + commentid).done(function (response) {
        if (response.success == true) {

         var totalComments = $('[data-commentid]').length;

         $('[data-commentid="' + commentid + '"]').remove();

         var commentsIndicator = $('.comments-indicator');
         if(totalComments-1 == 0) {
            commentsIndicator.addClass('hide');
         } else {
            commentsIndicator.removeClass('hide');
            commentsIndicator.text(totalComments-1);
         }
      }
   });
  }
}

function edit_notice_comment(id) {
  var content = $('body').find('[data-notice-comment-edit-textarea="' + id + '"] textarea').val();
  if (content != '') {
     $.post(admin_url + 'notices/edit_comment/' + id, {
        content: content
     }).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
           alert_float('success', response.message);
           $('body').find('[data-notice-comment="' + id + '"]').html(nl2br(content));
        }
     });
     toggle_notice_comment_edit(id);
  }
}

function toggle_notice_comment_edit(id) {
  $('body').find('[data-notice-comment="' + id + '"]').toggleClass('hide');
  $('body').find('[data-notice-comment-edit-textarea="' + id + '"]').toggleClass('hide');
}

function noticeGoogleDriveSave(pickData) {
   var data = {};
   data.notice_id = notice_id;
   data.external = 'gdrive';
   data.files = pickData;
   $.post(admin_url + 'notices/add_external_attachment', data).done(function () {
    var location = window.location.href;
    window.location.href = location.split('?')[0] + '?tab=attachments';
 });
}
/* notice document change  */
	function delete_notice_document(notice_id) {
		
    requestGet('notices/delete_notice_document/'+notice_id).done(function(){
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-agreeremove-img').addClass('hide');
		 var location = window.location.href;
    window.location.href = location.split('?')[0] + '?tab=tab_content';
    });
}

function delete_signed_notice_document(notice_id) {
    requestGet('notices/delete_signed_notice_document/'+notice_id).done(function(){
        $('body').find('#signed-contact-profile-image').removeClass('hide');
        $('body').find('#signed-contact-agreeremove-img').addClass('hide');
		 var location = window.location.href;
    window.location.href = location.split('?')[0] + '?tab=tab_content';
    });
}


function save_as_notice_new_version(notice_id){
   $('#new-version-modal').modal('show');
}

function update_sharepoint(noticeid) {
	 var version='no';
	 if($('#add_new_version').is(":checked"))   
         version=$('#add_new_version').val();
        else
			version='no';
	
		 $.post(admin_url + 'notices/update_noticefile_version/' +noticeid+'/'+version).done(function (response) {
			console.log(response.message);
           response = JSON.parse(response);
		  alert_float(response.alert, response.message);
		  setTimeout(function() {
              window.location.reload();
          }, 500);
          
        })
    
}
	$( "#payment_terms" ).change(function() {
  
		var cttype = $('#payment_terms').val();
		
		noticeinstallment_action(cttype);
		
	});
	function noticeinstallment_action(intype){
       
            if(intype =='One Time'){
                $('#notice_install').addClass('hide');
               
            } else if(intype =='installment'){
                $('#notice_install').removeClass('hide');
               
            } else {
                $('#notice_install').addClass('hide');
               
            }
        }
$('select[name="notice_type"]').change(function(){
        
    get_templates_of_notice_ajax();
    
    });
    function get_templates_of_notice_ajax() { 
        var clientSelected = $('select[name="notice_type"]').val();//alert(clientSelected);
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
</script>

</body>
</html>
<div class="modal" id="new-version-modal" tabindex="-1" role="dialog" style="z-index: 9999999999999;">
  <div class="modal-dialog mtop20" role="document" >
    <div class="modal-content">
      <?php //echo form_open(admin_url('notices/update_noticefile_version'),['id'=>'receivable-payment-form']); ?>
      <div class="modal-header " > 
        <h4 class="modal-title "><?php echo _l('version') ?></h4>
      </div>
      <div class="modal-content">   

        <div class="rows ">
            <div class="col-md-8 text-right">
               <div class="checkbox checkbox-success">
                  <input type="checkbox" name="add_new_version" id="add_new_version" value="yes" >
                  <label for="add_new_version" >
                  <?php echo _l( 'save_as_new_version'); ?>
                  </label>
               </div>
            </div>
            <div class="col-md-12">
            <div class="alert alert-info">If you click the 'save as new version' checkbox, a new version of notice will be created. Otherwise the latest notice will be replaced.</div>
         </div>
         </div>
         <hr>
         <br>
      </div>
      <!-- <div class="modal-body">
       
      </div> -->
      <div class="modal-footer">
        <button type="button"  onclick="update_sharepoint(<?php echo $notice->id; ?>); return false;" class="btn btn-warning" data-dismiss="modal">GO</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      <?php //echo form_close(); ?>

    </div>
  </div>
</div>