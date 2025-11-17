<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         
<?php if(isset($contract)) { ?>
   <div class="col-md-12">
      <div class="panel_s">
         <div class="panel-body">
            <h4 class="no-margin"><?php echo $contract->subject; ?><?php 
            if(!empty($contract->contract_refno)){
               echo ' - '.$contract->contract_refno;
            }
          ?>
          
               <?php if($contract->other_party!=0){?>
                        <a href="<?php echo admin_url('opposite_parties/opposite_party/'.$contract->other_party); ?>" class="pull-right" >
                  <?php echo get_opposite_party_name($contract->other_party);?>
               </a>
               <?php } ?></h4><br> 
                 <!--14112025 start-->
  <?php
// Check if current user should see the review button
$show_review_button = false;
$current_user_id = get_staff_user_id();
$found_current_user = false;
$all_previous_completed = true;
$has_rejection = false;

foreach ($contract_approvals as $approval) {
    // Check if any approval is rejected
    if ($approval['status'] === 'rejected') {
        $has_rejection = true;
        break;
    }
    
    // If we found the current user in previous iteration
    if ($found_current_user) {
        break;
    }
    
    // Check if this is the current user
    if ((int)$approval['staffid'] == (int)$current_user_id) {
        $found_current_user = true;
        // Only show button if all previous approvals are completed,
        // current approval is pending, and approval_heading_id is 11
        if ($all_previous_completed && 
            $approval['status'] !== 'reviewed' && 
            $approval['status'] !== 'signed' && 
            $approval['status'] !== 'rejected' &&
            isset($approval['approval_heading_id']) && 
            (int)$approval['approval_heading_id'] == 11) {
            $show_review_button = true;
        }
        break;
    }
    
    // Check if previous approvals are completed
    if ($approval['status'] !== 'reviewed' && $approval['status'] !== 'signed') {
        $all_previous_completed = false;
    }
}

// Display the button if conditions are met and no rejections exist
if ($show_review_button && !$has_rejection) { ?>
    <a href="<?php echo admin_url('contracts/contract_external_review/' . $contract->id); ?>" class="btn btn-info pull-right">
        <?php echo _l('revew_now'); ?>
    </a>
<?php } ?>
        <!--14112025 end-->
               
                                                   <?php
$all_signed_or_reviewed = true;

if (!empty($contract_approvals)) {
    foreach ($contract_approvals as $approval) {
        $status = strtolower(trim($approval['status'] ?? ''));
        if ($status !== 'signed' && $status !== 'reviewed') {
            $all_signed_or_reviewed = false;
            break; // ❌ Found one not signed/reviewed, stop checking
        }
    }
}

if (
    $contract->marked_as_signed == 0 &&
    staff_can('edit', 'contracts') &&
    $all_signed_or_reviewed
) {
    ?>
    <a href="<?php echo admin_url('contracts/mark_as_signed/' . $contract->id); ?>" class="btn btn-info pull-right">
        <?php echo _l('mark_as_signed'); ?>
    </a>
<?php } ?>

                                                        <?php
                                                   if($contract->marked_as_signed == 1) { 
                                                        $totalversions = total_rows(db_prefix().'contract_versions','contractid='.$contract->id);
                                                       if($totalversions>0){
                                                            $latest_version=get_current_contract_versioninfo($contract->id);
                                                         
                                                            $path1 = site_url('download/downloadagreementversion/'. $latest_version->contractid.'/'.$latest_version->id);
                                                   
                                                            $file_path   = get_upload_path_by_type('contract').$latest_version->contractid.'/'.$latest_version->version_internal_file_path;
                                                            if(file_exists($file_path)){ 
                                                             $dispaly = '<a href="'. $path1 .'"  class="btn btn-info pull-right mright5" data-toggle="tooltip" download title="'._l('signed_contract').'" data-placement="bottom">'._l('download').' </a>';
                                                                      
                                                               echo $dispaly;
                                                            }
                                                         }
                                                  
                                                 } ?>
               <?php if($contract->project_id!=0){?>
                        <a href="<?php echo admin_url('projects/view/'.$contract->project_id); ?>" class="btn btn-info pull-right hide" >
                  <?php echo _l('goback_project'); ?>
               </a>
               <?php } ?></h4><br>

			 <span class="pull-right" style="font-size:14px;font-weight: bold;padding-right:85px;"> <?php echo 'Prepared By : '. get_staff_full_name($contract->addedfrom); ?></span>
		  <?php if(isset($contract) && $contract->contract_template_id != ''){ ?>
            <a href="<?php echo site_url('contract/'.$contract->id.'/'.$contract->hash); ?>" target="_blank">
               <?php echo _l('view_contract'); ?>
            </a>
		  <?php } ?>
<?php
// ============================================
// VIEW FILE: Display Sign/Stamp Buttons
// ============================================
$user_id = get_staff_user_id();
$can_sign = false;
$can_stamp = false;
$is_current_signer = false;

// Get addedfrom user - initialize
$addedfrom_user_id = 0;

// Sort approvers by their order
$sorted_approvals = $contract_approvals;

// Find the addedfrom user and check if they've signed
$addedfrom_signed = false;
$addedfrom_found = false;

foreach ($sorted_approvals as $approval) {
    // Get addedfrom from approval array (should be same in all records)
    if (isset($approval['addedfrom']) && (int)$approval['addedfrom'] > 0 && $addedfrom_user_id == 0) {
        $addedfrom_user_id = (int)$approval['addedfrom'];
    }
    
    $staffid = isset($approval['staffid']) ? (int)$approval['staffid'] : 0;
    $status = isset($approval['status']) ? strtolower($approval['status']) : '';
    
    // Check if this is the addedfrom user and their signing status
    if ($addedfrom_user_id > 0 && $staffid == $addedfrom_user_id) {
        
        $addedfrom_found = true;
        // Consider both 'signed' and 'reviewed' as completed
        if ($status == 'signed' || $status == 'reviewed') {
            $addedfrom_signed = true;
        }
    }
}

// Now check if current user can sign
// Logic: If addedfrom user exists and hasn't signed/reviewed, only they can sign
if ($addedfrom_user_id > 0 && $addedfrom_found && !$addedfrom_signed) {
    // Only the creator (addedfrom user) can sign
    // print_r($user_id);
    if ($user_id == $addedfrom_user_id) {
        foreach ($sorted_approvals as $approval) {
            $staffid = isset($approval['staffid']) ? (int)$approval['staffid'] : 0;
            
            if ($staffid == $user_id) {print_r('inside');
                $placeholder = isset($approval['sign_placeholder']) ? trim($approval['sign_placeholder']) : '';
                $status = isset($approval['status']) ? strtolower($approval['status']) : '';
                
                if (
                    $status !== 'signed' &&
                    $status !== 'reviewed' &&
                    $status !== 'rejected' &&
                    !empty($placeholder) &&
                    $placeholder !== '[]' &&
                    $placeholder !== 'null'
                ) {
                    $can_sign = true;
                    $is_current_signer = true;
                }
                break;
            }
        }
    }
} else {
    // Creator has signed/reviewed or doesn't exist - use normal order logic
    $current_position = -1;
    $user_position = -1;
    
    foreach ($sorted_approvals as $index => $approval) {
        $staffid = isset($approval['staffid']) ? (int)$approval['staffid'] : 0;
        $status = isset($approval['status']) ? strtolower($approval['status']) : '';
        
        // Track user's position
        if ($staffid == $user_id) {
            $user_position = $index;
        }
        
        // Find the first unsigned/unreviewed position
        if ($current_position == -1 && !in_array($status, ['signed', 'reviewed', 'rejected'])) {
            $current_position = $index;
        }
    }
    
    // Check if user can sign based on their position
    if ($user_position !== -1 && $user_position == $current_position) {
        $approval = $sorted_approvals[$user_position];
        $placeholder = isset($approval['sign_placeholder']) ? trim($approval['sign_placeholder']) : '';
        $status = isset($approval['status']) ? strtolower($approval['status']) : '';
        
        if (
            !in_array($status, ['signed', 'reviewed', 'rejected']) &&
            !empty($placeholder) &&
            $placeholder !== '[]' &&
            $placeholder !== 'null'
        ) {
            $can_sign = true;
            $is_current_signer = true;
        }
    }
}

// Check if user can apply stamp
if (is_stamper($user_id)) {
    $can_stamp = true;
}

// Additional check: Stamp placeholder must exist
$stamp_placeholder = isset($contract->stamp_placeholder) ? trim($contract->stamp_placeholder) : '';
$has_stamp_placeholder = !empty($stamp_placeholder) && $stamp_placeholder !== '[]' && $stamp_placeholder !== 'null';
?>

<!-- Sign Button -->
<?php if ($can_sign): ?>
    <button class="btn btn-primary pull-right" style="margin-right: 15px;" data-toggle="modal" data-target="#signatureModal">
        <i class="fa fa-pencil"></i> Sign Now
    </button>
<?php elseif ($addedfrom_user_id > 0 && $addedfrom_found && !$addedfrom_signed && $user_id !== $addedfrom_user_id): ?>
    <!-- Waiting for creator to sign first -->
    <button class="btn btn-default pull-right" style="margin-right: 15px;" disabled title="Waiting for contract creator to sign first">
        <i class="fa fa-clock-o"></i> Waiting for Creator to Sign
    </button>
<?php elseif (
    isset($user_position) 
    && $user_position !== -1 
    && isset($current_position) 
    && $user_position > $current_position
    && !in_array(strtolower($sorted_approvals[$user_position]['status']), ['signed', 'reviewed'])
): ?>
    <!-- Show info message if user is in queue but not their turn -->
    <button class="btn btn-default pull-right" style="margin-right: 15px;" disabled title="Waiting for previous approvers to sign">
        <i class="fa fa-clock-o"></i> Waiting for Your Turn
    </button>
<?php endif; ?>


<!-- Stamp Button -->
<?php if ($can_stamp && $has_stamp_placeholder): ?>
    <button class="btn btn-success pull-right" style="margin-right: 15px;" data-toggle="modal" data-target="#stampModal">
        <i class="fa fa-certificate"></i> Apply Stamp
    </button>
<?php endif; ?>
          
                <?php if($contract->ticketid!=0 && get_option('enable_legal_request')==1 ){?>
              <a href="<?php echo admin_url('tickets/ticket/'.$contract->ticketid); ?>" target="_blank" class="pull-right hide" >
               <?php echo _l('view_ticket'); ?>
            </a>
            <?php } ?>
            <hr class="hr-panel-heading" />
            <?php if($contract->trash > 0){
               echo '<div class="ribbon default"><span>'._l('contract_trash').'</span></div>';
            } ?>
            <div class="horizontal-scrollable-tabs preview-tabs-top">
               <div class="scroller arrow-left"><i class="fa fa-angle-left"></i></div>
               <div class="scroller arrow-right"><i class="fa fa-angle-right"></i></div>
               <div class="horizontal-tabs">
                  <ul class="nav nav-tabs tabs-in-body-no-margin contract-tab nav-tabs-horizontal mbot15" role="tablist">
                     <li role="presentation" class="<?php if(!$this->input->get('tab') || $this->input->get('tab') == 'tab_overview'){echo 'active';} ?>">
                        <a href="#tab_overview" aria-controls="tab_overview" role="tab" data-toggle="tab">
                           <?php echo _l('contract_overview'); ?>
                        </a>
                     </li>
                     <li role="presentation" class="<?php if($this->input->get('tab') == 'tab_contract'){echo 'active';} ?> <?php if(empty($contract->contract_filename)) echo 'hide';?>">
                        <a href="#tab_contract" aria-controls="tab_contract" role="tab" data-toggle="tab">
                           <?php echo ($contract->type == 'contracts') ? _l('contract_sign_preview') : _l('po_sign_preview'); ?>
                        </a>
                     </li> 
                       <?php 
$show_tabs = false;

if (is_admin()) {
    // Admin can see all tabs without any conditions
    $show_tabs = true;
} else {
    // Non-admin users need to meet the conditions
    foreach ($contract_approvals as $approvals) {
        $approval_heading_id = isset($approvals['approval_heading_id']) ? (int)$approvals['approval_heading_id'] : 0;

        if (
            $approval_heading_id != 11 &&
            isset($approvals['staffid']) &&
            (int)$approvals['staffid'] == (int)get_staff_user_id()
        ) {
            $show_tabs = true;
            break;
        }
    }
}

if ($show_tabs) { ?>

    <li role="presentation" class="<?php if( $this->input->get('tab') == 'tab_content'){echo 'active';} ?>">
        <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
            <?php echo ($contract->type == 'contracts') ? _l('contract_contract') : _l('purchase_order'); ?>
        </a>
    </li>

    <li role="presentation" class="<?php if($this->input->get('tab') == 'approvals'){echo 'active';} ?>">
        <a href="#approvals" aria-controls="approvals" role="tab" data-toggle="tab">
            <?php echo _l('approvals'); ?>
        </a>
    </li>
    <?php if($contract->type=='contracts') { ?>
    <li role="presentation" class="<?php if($this->input->get('tab') == 'tab_version'){echo 'active';} ?>">
        <a href="#tab_version" aria-controls="tab_version" role="tab" data-toggle="tab">
            <?php echo _l('contract_version'); ?>
        </a>
    </li> 
    
    <li role="presentation">
        <a href="#tab_negotiation" aria-controls="tab_negotiation" class="<?php if($this->input->get('tab') == 'negotiation'){echo 'active';} ?>" role="tab" data-toggle="tab" onclick="get_contract_comments('negotiation'); return false;">
            <?php echo _l('contract_negotiations'); ?>
            <?php
            $totalnegotiations = total_rows(db_prefix().'contract_comments',['contract_id' => $contract->id, 'comment_type' => 'negotiation']);
            ?>
            <span class="badge comments-indicator-nego<?php echo $totalnegotiations == 0 ? ' hide' : ''; ?>"><?php echo $totalnegotiations; ?></span>
        </a>
    </li>
    
    <li role="presentation" class="<?php if($this->input->get('tab') == 'renewals'){echo 'active';} ?>">
        <a href="#renewals" aria-controls="renewals" role="tab" data-toggle="tab">
            <?php echo _l('no_contract_renewals_history_heading'); ?>
            <?php if($totalRenewals = count($contract_renewal_history)) { ?>
                <span class="badge"><?php echo $totalRenewals; ?></span>
            <?php } ?>
        </a>
    </li>

    <li role="presentation" class="<?php if($this->input->get('tab') == 'comparison'){echo 'active';} ?>">
        <a href="#comparisons" aria-controls="comparisons" role="tab" data-toggle="tab">
            <?php echo _l('contract_comparison'); ?>
        </a>
    </li>
    
    <li role="presentation" class="<?php if($this->input->get('tab') == 'tab_amendment'){echo 'active';} ?>">
        <a href="#tab_amendment" aria-controls="tab_amendment" role="tab" data-toggle="tab">
            <?php echo _l('contract_amendment'); ?>
        </a>
    </li> 
    
    <li role="presentation" class="<?php if($this->input->get('tab') == 'attachments'){echo 'active';} ?>">
        <a href="#attachments" aria-controls="attachments" role="tab" data-toggle="tab">
            <?php echo _l('contract_attachments'); ?>
            <?php if($totalAttachments = count($contract->attachments)) { ?>
                <span class="badge attachments-indicator"><?php echo $totalAttachments; ?></span>
            <?php } ?>
        </a>
    </li>
    <?php } ?>
    <li role="presentation">
        <a href="#tab_comments" aria-controls="tab_comments" class="<?php if($this->input->get('tab') == 'comments'){echo 'active';} ?>" role="tab" data-toggle="tab" onclick="get_contract_comments(); return false;">
            <?php echo _l('contract_comments'); ?>
            <?php
            $totalComments = total_rows(db_prefix().'contract_comments','contract_id='.$contract->id)
            ?>
            <span class="badge comments-indicator<?php echo $totalComments == 0 ? ' hide' : ''; ?>"><?php echo $totalComments; ?></span>
        </a>
    </li>
<?php if($contract->type=='contracts') { ?>
    <li role="presentation" class="<?php if($this->input->get('tab') == 'risklist'){echo 'active';} ?>">
        <a href="#risklists" aria-controls="risklists" role="tab" data-toggle="tab">
            <?php echo _l('checklists'); ?>
        </a>
    </li>

    <li role="presentation" class="tab-separator">
        <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab" onclick="init_rel_tasks_table(<?php echo $contract->id; ?>,'contract'); return false;">
            <?php echo _l('tasks'); ?>
        </a>
    </li>
    
    <li role="presentation"  class="<?php if($this->input->get('tab') == 'reminder'){echo 'active';} ?>">
        <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $contract->id ;?> + '/' + 'contract', undefined, undefined, undefined,[1,'asc']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
            <?php echo _l('set_reminder'); ?>
            <?php
            $total_reminders = total_rows(db_prefix().'reminders',
                array(
                    'isnotified'=>0,
                    'staff'=>get_staff_user_id(),
                    'rel_type'=>'contract',
                    'rel_id'=>$contract->id
                )
            );
            if($total_reminders > 0){
                echo '<span class="badge">'.$total_reminders.'</span>';
            }
            ?>
        </a>
    </li>
    
    <li role="presentation" class="tab-separator">
        <a href="#tab_notes" onclick="get_sales_notes(<?php echo $contract->id; ?>,'contracts'); return false" aria-controls="tab_notes" role="tab" data-toggle="tab">
            <?php echo _l('updates'); ?>
            <span class="notes-total">
                <?php if($totalNotes > 0){ ?>
                    <span class="badge"><?php echo $totalNotes; ?></span>
                <?php } ?>
            </span>
        </a>
    </li>
    
    <li role="presentation" class="tab-separator hide">
        <a href="#tab_templates" onclick="get_templates('contracts', <?php echo $contract->id ?>); return false" aria-controls="tab_templates" role="tab" data-toggle="tab">
            <?php echo _l('templates'); ?>
        </a>
    </li>
    
    <li role="presentation" class="<?php if($this->input->get('tab') == 'tab_contracts'){echo 'active';} ?> <?php if(empty($contract->contract_template_id) && empty($contract->content)) echo 'hide';?>">
        <a href="#tab_contracts" aria-controls="tab_contracts" role="tab" data-toggle="tab">
            <?php echo _l('contract_content'); ?>
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
        <a href="#" onclick="contract_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>">
            <i class="fa fa-expand"></i>
        </a>
    </li>
<?php } ?>
<?php } ?>
                  </ul>
               </div>
            </div>
            <div class="tab-content">
                <!-----------tab overview------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if(!$this->input->get('tab') ||$this->input->get('tab') == 'tab_overview'){echo ' active';} ?>" id="tab_overview">
        <?php if($contract->is_receivable==1){?>
         <div class="col-md-12 text-right _buttons">
                                             <div class="btn-group">
        
<button type="button" <?php if(get_option('enable_openai')==0){ echo 'title="For Advance features Contact Admin"';}else{echo _l('generate_contract');} ?> <?php if(get_option('enable_openai')==0){ echo 'disabled style="cursor:not-allowed;" onclick="return false;"';}?>
            class="btn btn-success w-100 mbot10" 
            id="getAISummaryBtn"
            onclick="getAiSummary(<?php echo $contract->id; ?>)" 
            >
        <i class="fa fa-magic"></i> <?=_l('get_ai_summary')?> 
    </button>
</div>
</div>
<?php } ?>
  <?php $this->load->view( 'admin/contracts/contracts_overview'); ?>
				</div>
<!-----------tab overview------------------------------------------------------------>
            <div role="tabpanel" class="tab-pane<?php if( $this->input->get('tab') == 'tab_content'){echo ' active';} ?>" id="tab_content">
                  <div class="row">
                                    <?php if($contract->signed == 1){ ?>
                                       <div class="col-md-12">
                                          <div class="alert alert-success">
                                             <?php echo _l('document_signed_info',array(
                                                '<b>'.$contract->acceptance_firstname . ' ' . $contract->acceptance_lastname . '</b> (<a href="mailto:'.$contract->acceptance_email.'">'.$contract->acceptance_email.'</a>)',
                                                '<b>'. _dt($contract->acceptance_date).'</b>',
                                                '<b>'.$contract->acceptance_ip.'</b>')
                                             ); ?>
                                          </div>
                                       </div>
                                       <?php } else if($contract->marked_as_signed == 1) { ?>
                                          <div class="col-md-12">
                                             <div class="alert alert-info">
                                                <?php echo _l('contract_marked_as_signed_info'); ?>
                                             </div>
                                          </div>
                                       <?php } ?>
					   <?php if($contract->party_signed == 1){ ?>
                                       <div class="col-md-12">
                                          <div class="alert alert-info">
                                             <?php echo _l('partydocument_signed_info',array(
                                                '<b>'.$contract->partyacc_firstname . ' ' . $contract->partyacc_lastname . '</b> (<a href="mailto:'.$contract->partyacc_email.'">'.$contract->partyacc_email.'</a>)',
                                                '<b>'. _dt($contract->partyacc_date).'</b>',
                                                '<b>'.$contract->partyacc_ip.'</b>')
                                             ); ?>
                                          </div>
                                       </div>
                                       <?php }?>
                                    <div class="col-md-12 text-right _buttons">
                                             <div class="btn-group">
                                                      <?php if(isset($contract) && $contract->contract_filename == ''){ ?>
                                                       <a href="<?php echo admin_url('contracts/generate/'.$contract->id) ?>" data-toggle="tooltip"  class="btn btn-info mright15 hide" <?php if(get_option('enable_openai')==0){ echo 'title="For Advance features Contact Admin"';}else{echo _l('generate_contract');} ?> <?php if(get_option('enable_openai')==0){ echo 'disabled style="cursor:not-allowed;" onclick="return false;"';}?> >
                                                         <i class="fa fa-globe"></i>
                                                         <?php echo _l('generate_contract_AI'); ?>
                                                         </a>
                                                         <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_contract'); ?>" class="btn btn-info" onclick="upload_contractfile(<?php echo $contract->id; ?>); return false;">
                                                         <i class="fa fa-file-pdf-o"></i>
                                                         <?php echo _l('upload_contract'); ?>
                                                         </a>
                                                         
                                                         <!--<a href="#" class="btn btn-default dropdown-toggle hide" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf-o"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>-->
                                                         <!--<ul class="dropdown-menu dropdown-menu-right">-->
                                                         <!--<li class="hidden-xs"><a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>-->
                                                         <!--<li class="hidden-xs"><a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>-->
                                                         <!--<li><a href="<?php echo admin_url('contracts/pdf/'.$contract->id); ?>"><?php echo _l('download'); ?></a></li>-->
                                                         <!--<li>-->
                                                         <!--<a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?print=true'); ?>" target="_blank">-->
                                                         <!--<?php echo _l('print'); ?>-->
                                                         <!--</a>-->
                                                         <!--</li>-->
                                                         <!--</ul>-->
                                                      <?php }
                                                      else{
                                                          ?>
                                                            <a href="<?php echo admin_url('contracts/generate/'.$contract->id.'/review') ?>" data-toggle="tooltip" <?php if(get_option('enable_openai')==0){ echo 'title="For Advance features Contact Admin"';}else{echo _l('generate_contract');} ?> <?php if(get_option('enable_openai')==0){ echo 'disabled style="cursor:not-allowed;" onclick="return false;"';}?> class="btn btn-info mright15 hide" >
                                                         <i class="fa fa-globe"></i>
                                                         <?php echo _l('review_contract_AI'); ?>
                                                         </a>
                                                        <?php
                                                         $totalversions = total_rows(db_prefix().'contract_versions','contractid='.$contract->id);
                                                          if($totalversions==0){ ?>
                                                         
                                                          <a href="<?php echo admin_url('contracts/generate/'.$contract->id.'/improve') ?>" data-toggle="tooltip"  class="btn btn-info mright15 hide" <?php if(get_option('enable_openai')==0){ echo 'title="For Advance features Contact Admin"';}else{echo _l('generate_contract');} ?> <?php if(get_option('enable_openai')==0){ echo 'disabled style="cursor:not-allowed;" onclick="return false;"';}?> >
                                                         <i class="fa fa-globe"></i>
                                                         <?php echo _l('improve_contract_AI'); ?>
                                                         </a>
                                                       <?php  } 
                                                         if($totalversions>0){
                                                            $latest_version=get_current_contract_versioninfo($contract->id);
                                                         
                                                            $path1 = site_url('download/downloadagreementversion/'. $latest_version->contractid.'/'.$latest_version->id);
                                                   
                                                            $file_path   = get_upload_path_by_type('contract').$latest_version->contractid.'/'.$latest_version->version_internal_file_path;
                                                            if(file_exists($file_path)){ 
                                                            
                                                               $dispaly = '<a href="'. $path1 .'"  class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" download title="'._l('latest_agreement').'" data-placement="bottom"><i class="fa  fa-file-pdf-o" aria-hidden="true"></i></a>';
                                                                  
                                                               echo $dispaly;
                                                            }
                                                         }
                                                         else{
                                                            ?>
												  <?php if($contract->marked_as_signed ==0 && $contract->signed == 0 && $contract->party_signed == 0){ ?>
												 <?php if(empty($contract->contract_template_id)){ ?>
                                                            <a href="#" onclick="delete_contract_document(<?php echo $contract->id; ?>); return false;" class="btn btn-danger mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_contract')?></a>
												 <?php } ?>
												 <?php } ?>
                                                            <?php 
                                                            $file_path   = get_upload_path_by_type('contract').$contract->id.'/';
                                                               //$lpath        = base_url('uploads/contracts/').$contract->id.'/';
                                                            $path = site_url('download/downloadagreement/'. $contract->id); 															   
                                                            if(file_exists($file_path.$contract->contract_filename)){ ?>
                                                               <a download href="<?php echo $path ; ?>"  class="btn btn-default btn-with-tooltip mright5" data-toggle="tooltip" download title="<?php echo _l('latest_agreement'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o" aria-hidden="true"></i></a>
                                                            <?php }
                                                            $path = site_url('download/downloadagreement/'. $contract->id); ?>
                                                               <!-- <a download href="<?php echo $path;?>"  class="btn btn-default maleft10"><i class="fa fa-download"></i> <?php echo _l('contract'); ?></a>
                                                               <a href="#" onclick="delete_contract_document(<?php echo $contract->id; ?>); return false;" class="btn btn-default mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_contract')?></a>  -->       
                                                            <?php 
                                                         }
                                                      }?>
					<?php if(isset($contract) && $contract->contract_template_id != ''){ ?>
                                                        
                                                         
                                                         <!--<a href="#" class="btn btn-default dropdown-toggle hide" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-file-pdf-o"></i><?php if(is_mobile()){echo ' PDF';} ?> <span class="caret"></span></a>-->
                                                         <!--<ul class="dropdown-menu dropdown-menu-right">-->
                                                         <!--<li class="hidden-xs"><a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?output_type=I'); ?>"><?php echo _l('view_pdf'); ?></a></li>-->
                                                         <!--<li class="hidden-xs"><a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?output_type=I'); ?>" target="_blank"><?php echo _l('view_pdf_in_new_window'); ?></a></li>-->
                                                         <!--<li><a href="<?php echo admin_url('contracts/pdf/'.$contract->id); ?>"><?php echo _l('download'); ?></a></li>-->
                                                         <!--<li>-->
                                                         <!--<a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?print=true'); ?>" target="_blank">-->
                                                         <!--<?php echo _l('print'); ?>-->
                                                         <!--</a>-->
                                                         <!--</li>-->
                                                         <!--</ul>-->
                                                      <?php }?>
                                    <!------------------signed contract--------------------------------------------------------------->
                                    <?php if(isset($contract) && ($contract->marked_as_signed == 1 || ( $contract->signed == 1 && $contract->party_signed == 1))){ ?>
                                                      <?php if(isset($contract) && $contract->signed_contract_filename == ''){ ?>
                                                         <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_signedcontract'); ?>" class="btn btn-info hide" onclick="upload_signed_contractfile(<?php echo $contract->id; ?>); return false;">
                                                         <i class="fa fa-upload"></i>
                                                         <?php echo _l('upload_signedcontract'); ?>
                                                         </a>
                                                      <?php }else{
                                                         $file_path   = get_upload_path_by_type('contract').$contract->id.'/';
                                                         $path1 = site_url('download/downloadsigned_agreement/'.$contract->id); 
                                                         if(file_exists($file_path.$contract->signed_contract_filename)){?>
                                                            <a href="#" onclick="delete_signed_contract_document(<?php echo $contract->id; ?>);" class="btn btn-danger mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_signed_contract')?></a>
                                                            <a download href="<?php echo $path1; ?>" class="btn btn-success btn-with-tooltip mright5" data-toggle="tooltip" download title="<?php echo _l('signed_contract'); ?>" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i></a>
                                                         <?php } 
                                                      } ?>
                                    <?php } ?>
                                    <!-------------------signed contract-------------------------------------------------------------->
                                          
                                       
                                             </div>
                                             <a href="#" class="btn btn-default" data-target="#contract_send_to_client_modal" data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('contract_send_to_email'); ?>" data-placement="bottom">
                                                <i class="fa fa-envelope"></i></span>
                                             </a>
                                             
                                             <a href="#" class="btn btn-default" data-target="#contract_send_to_otherparty_modal" data-toggle="modal"><span class="btn-with-tooltip"  data-toggle="tooltip" data-title="<?php echo _l('contract_send_to_email_otherparty'); ?>" data-placement="bottom">
                                                <i class="fa fa-envelope" style="color:#3be13b"></i></span>
                                             </a>
                                             <!-- <a href="#" class="btn btn-warning" data-target="#contract_send_for_approval" data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('contract_send_for_approval'); ?>" data-placement="bottom">
                                                <i class="fa fa-envelope" style="color:white;"></i></span>
                                             </a> -->
                                                   <a target="_blank" href="<?php echo admin_url('contracts/legalcontract_approval/'.$contract->id); ?>" class="btn btn-default btn-with-tooltip hide" data-toggle="tooltip" title="Approval" data-placement="bottom" data-original-title="Download Contract Approval"> <i class="fa fa-check"></i> </a>
                                             <div class="btn-group">
                                                <button type="button" class="btn btn-default pull-left dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                   <?php echo _l('more'); ?> <span class="caret"></span>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-right">
													 <?php if(isset($contract) && $contract->contract_template_id != ''){ ?>
                                                   <li>
                                                      <a href="<?php echo site_url('contract/'.$contract->id.'/'.$contract->hash); ?>" target="_blank">
                                                         <?php echo _l('view_contract'); ?>
                                                      </a>
                                                   </li>
													<?php } ?>
                                                   <?php
                                                   if($contract->sended == 0 && $contract->signed == 0 && staff_can('edit', 'contracts')) { ?>
                                                   <li class="hide">
                                                      <a href="<?php echo admin_url('contracts/mark_as_send_sms/'.$contract->id); ?>">
                                                         <?php echo _l('mark_as_send_sms'); ?>
                                                      </a>
                                                   </li>
                                                   <?php } ?>
                                                   <?php
                                                   if($contract->sended == 0 && staff_can('edit', 'contracts')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('contracts/mark_as_send/'.$contract->id); ?>">
                                                         <?php echo _l('mark_as_send_email'); ?>
                                                      </a>
                                                   </li>
                                             
                                                <?php } ?>
                                          
                                                   <?php
                                                   if($contract->signed == 0 && $contract->marked_as_signed == 0 && staff_can('edit', 'contracts')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('contracts/mark_as_signed/'.$contract->id); ?>">
                                                         <?php echo _l('mark_as_signed'); ?>
                                                      </a>
                                                   </li>
                                                <?php } else if($contract->signed == 0 && $contract->marked_as_signed == 1 && staff_can('edit', 'contracts')) { ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('contracts/unmark_as_signed/'.$contract->id); ?>">
                                                         <?php echo _l('unmark_as_signed'); ?>
                                                      </a>
                                                   </li>
                                                <?php } ?>
                                                <?php hooks()->do_action('after_contract_view_as_client_link', $contract); ?>
                                                <?php if(has_permission('contracts','','create')){ ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('contracts/copy/'.$contract->id); ?>">
                                                         <?php echo _l('contract_copy'); ?>
                                                      </a>
                                                   </li>
                                                <?php } ?>
                                                <?php if($contract->signed == 1 && has_permission('contracts','','delete')){ ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('contracts/clear_signature/'.$contract->id); ?>" class="_delete">
                                                         <?php echo _l('clear_signature'); ?>
                                                      </a>
                                                   </li>
                                                <?php } ?>
                                                <?php if(has_permission('contracts','','delete')){ ?>
                                                   <li>
                                                      <a href="<?php echo admin_url('contracts/delete/'.$contract->id); ?>" class="_delete">
                                                         <?php echo _l('delete'); ?></a>
                                                      </li>
                                                   <?php } ?>
                                                </ul>
                                             </div>
                                    </div>

                                    
                 </div>
                 <hr class="hr-panel-heading" />
                 <?php if(!staff_can('edit','contracts')) { ?>
                  <div class="alert alert-warning contract-edit-permissions">
                     <?php echo _l('contract_content_permission_edit_warning'); ?>
                  </div>
               <?php } ?>

       

                  <ul class="nav nav-tabs hide">
                     <li class="active">   <a data-toggle="tab" href="#home"><?php if($contract->final_doc == 'base') { echo '<i class="fa fa-check fa-lg" style="color:green;"></i>'; } ?>  <?php echo _l('base_document') ?></a></li>
                     <?php foreach ($contract_versions as $contract_version) { ?>
                         <li><a data-toggle="tab" href="#version-<?php echo $contract_version['version'];?>"><?php if($contract->final_doc == $contract_version['id']) { echo '<i class="fa fa-check fa-lg" style="color:green;"></i>'; } ?> <?php echo strtoupper(_l('version')).'-'.$contract_version['version'] ?></a></li>
                     <?php } ?>
                   
                  </ul>

                  <div class="tab-content hide">
                     <div id="home" class="tab-pane fade in active">
                        <h4><?php echo _l('base_document') ?></h4>
                         <hr>
                        <?php $path = site_url('download/downloadagreement/'. $contract->id); ?>
                        <p>
                           <!--<a download href="<?php echo $path;?>"  class="btn btn-default maleft10"><i class="fa fa-download"></i> <?php echo _l('download'); ?></a>

                           <a href="#" onclick="delete_contract_document(<?php echo $contract->id; ?>); return false;" class="btn btn-danger mleft10 " id="contact-agreeremove-img"><i class="fa fa-remove"></i><?=_l('change_contract')?></a>-->

                           <a href="<?php echo $contract->sharepoint_link; ?>" target="_blank" class="btn btn-warning btn-sm mleft20" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit') ?> </a>
                           <?php if($contract->final_doc != 'base'){  ?>
                          <!-- <a class="btn btn-success   pull-right" href="<?php echo admin_url('contracts/mark_as_final_doc/'.$contract->id.'/base') ?>"><i class="fa fa-star-o"></i> <?php echo _l('mark_as_final') ?></a>-->
                           <?php } ?>

                        </p>
                       </div>
                     <?php foreach ($contract_versions as $contract_version) { 
                        $path1 = site_url('download/downloadagreementversion/'. $contract->id.'/'.$contract_version['id']); ?>
                        <div id="version-<?php echo $contract_version['version'];?>" class="tab-pane fade">
                           <h4><?php echo strtoupper(_l('version')).'-'.$contract_version['version'] ?></h4>
                           <hr>
                           <p> 
                             <!-- <a download href="<?php echo $path1;?>"  class="btn btn-default mleft10"><i class="fa fa-download"></i> <?php echo _l('download'); ?></a>-->

                              <a href="<?php echo $contract_version['version_sharpoint_link'] ?>"  target="_blank" class="btn btn-warning btn-sm maleft10" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit') ?> </a>
                              <?php if($contract->final_doc != $contract_version['id']){  ?>
                             <!-- <a class="btn btn-success pull-right" href="<?php echo admin_url('contracts/mark_as_final_doc/'.$contract->id.'/'.$contract_version['id']) ?>"><i class="fa fa-star-o"></i> <?php echo _l('mark_as_final') ?></a>-->
                              <?php } ?>
                           </p>
                       </div>
                     <?php } ?>
                  </div>

              <?php //} ?>
			
         <div class="panel_s">
         <div class="panel-body">
                  <?php echo form_open($this->uri->uri_string(),array('id'=>'contract-form')); ?>
                  <?php if($contract->type=='contracts'){ ?>
                  <div class="form-group">
                     <div class="checkbox checkbox-primary no-mtop checkbox-inline" 
     style="display:flex; align-items:center; gap:40px; flex-wrap:wrap; margin-top:10px;">

 

  <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="is_payable" name="is_payable"
      <?php if(isset($contract)){if($contract->is_payable == 1){echo ' checked';}}; ?>
      onclick="handleSingleSelect(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="is_payable" style=" color:#333; cursor:pointer;">
      <?php echo _l('is_payable'); ?>
    </label>
  </div>

  <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="is_receivable" name="is_receivable"
      <?php if(isset($contract)){if($contract->is_receivable == 1){echo ' checked';}}; ?>
      onclick="handleSingleSelect(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="is_receivable" style=" color:#333; cursor:pointer;">
      <?php echo _l('is_receivable'); ?>
    </label>
  </div>
 <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="trash" name="trash"
      <?php if(isset($contract)){if($contract->trash == 1){echo ' checked';}}; ?>
      onclick="handleSingleSelect(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="trash" style=" color:#333; cursor:pointer;">
      <?php echo _l('contract_trash'); ?>
    </label>
  </div>
  
   <div style="display:flex; align-items:center; gap:8px;">
    <input type="checkbox" id="is_non_std" name="is_non_std" 
    <?php if(isset($contract)){if($contract->is_non_std == 1){echo ' checked';}}; ?>
     
      onclick="toggleUploadAndTemplate(this)"
      style="width:18px; height:18px; cursor:pointer;">
    <label for="is_non_std" style=" color:#333; cursor:pointer;">
      <?php echo _l('is_non_std'); ?>
    </label>
  </div>
</div>
                     <div class="checkbox checkbox-primary checkbox-inline hide">
                        <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client" <?php if(isset($contract)){if($contract->not_visible_to_client == 1){echo 'checked';}}; ?>>
                        <label for="not_visible_to_client"><?php echo _l('contract_not_visible_to_client'); ?></label>
                     </div>
                  </div>
                  <?php } ?>
                         <div class="col-md-4">
             <?php $value = (isset($contract) ? $contract->subject : ''); ?>
            <i class="fa fa-question-circle pull-left" data-toggle="tooltip" title="<?php echo _l('contract_subject_tooltip'); ?>"></i>
            <?php echo render_input('subject','contract_subject',$value); ?>
            </div>
            
			   
                 <div class="col-md-4" <?php if(isset($contract)){ ?> style="pointer-events: none;" <?php } ?>>
                <div class="form-group select-placeholder f_client_id">
                    <?php
                      $selected = '';

                      if (isset($contract)) {
                          $selected = $contract->client;
                      } elseif (isset($customer_id)) {
                          $selected = $customer_id;
                      } else {
                          $selected = '4'; // fallback
                      }
                      ?>

                    <?php echo render_select('client',$clients,             
                            array('userid', 'company'), 'client',$selected,            
                            array('data-none-selected-text' => _l('dropdown_non_selected_tex'),
                                'data-live-search' => 'true',
                                'data-width' => '100%',
                                'class' => 'ajax-search select'
                            )
                        );
                    ?>
                </div>
            </div>
            <?php if($contract->type=='contracts'){ ?>
            <div class="col-md-4">
               <div class="form-group select-placeholder projects-wrapper<?php if((isset($contract) && !customer_has_projects($contract->client))){ echo ' hide';} ?>">
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
            </div>
            </div>
            <?php } ?>
            <?php ########## Opposite Party ##############  ?>
         <!-- <div class="col-md-6"> -->
         <div class="col-md-4">
           <?php $selected = (isset($contract) ? $contract->other_party: '');
                        if($selected == ''){
                         $selected = (isset($party_id) ? $party_id: '');
                      }
            /*if(is_admin() ){
            echo render_select_with_input_group('opposite_party',$oppositeparty_names,array('id','name'),'casediary_oppositeparty',$selected,'<a href="#" onclick="new_opposite_party();return false;"><i class="fa fa-plus"></i></a>');
            } else {*/
				    echo render_select('other_party',$oppositeparty_names,array('id','name'),'name_party',$selected);
           // echo render_input('other_party','other_party',$value);
           // }?>
         </div> 

          <?php if($contract->type=='contracts'){ ?>
		
         <div class="col-md-4">
            <div class="form-group">
               <label for="contract_value"><?php echo _l('contract_value'); ?></label>
               <div class="input-group" data-toggle="tooltip" title="<?php echo _l('contract_value_tooltip'); ?>">
                  <input type="number" class="form-control" id= "contract_value" name="contract_value" value="<?php if(isset($contract)){echo $contract->contract_value; }?>">
                  <div class="input-group-addon">
                     <?php echo $base_currency->symbol; ?>
                  </div>
               </div>
            </div>
         </div>
         <div class="col-md-4">
            <?php
            $selected = (isset($contract) ? $contract->contract_type : '');
            if(is_admin() || get_option('staff_members_create_inline_contract_types') == '1'){
              echo render_select_with_input_group('contract_type',$types,array('id','name'),'contract_type',$selected,'<a href="#" onclick="new_type();return false;"><i class="fa fa-plus"></i></a>');
           } else {
            echo render_select('contract_type',$types,array('id','name'),'contract_type',$selected);
         }
         ?>
			 
         <?php $value = (isset($contract) ? $contract->type_stamp : '');
            
           // echo render_input('type_stamp','term',$value);
         ?>
        </div>
			
         
           <div class="col-md-4 <?php //if(isset($contract) && !empty($contract->contract_template_id)) echo 'hide';?>" id="div_template" <?php if(isset($contract) && !empty($contract->contract_template_id)){?> style="pointer-events: none;"<?php }?>>
             <?php 
                           
                $selected = (isset($contract) ? $contract->contract_template_id : '');?>
                  <?php  echo render_select('contract_template_id',$templates,array('id','name'),'contract_template',$selected); ?>
          </div>
		   <div class="col-md-4">
               <?php $value = (isset($contract) ? _d($contract->datestart) : _d(date('Y-m-d'))); ?>
               <?php echo render_date_input('datestart','contract_start_date',$value); ?>
            </div>
            <div class="col-md-4">
               <?php $value = (isset($contract) ? _d($contract->dateend) : ''); ?>
               <?php echo render_date_input('dateend','contract_end_date',$value); ?>
            </div>
			 <div class="col-md-4">
			 <?php $selected = (isset($contract) ? $contract->payment_terms : '');
                     $payment_terms=get_payment_terms();  
                        echo render_select('payment_terms',$payment_terms,array('id','name'),'payment_terms',$selected,array());?>
			 </div>
			 
			 <!--------------additional--------------------------------->    
			 

       <div class="col-md-4" id="contract_category_div">
			 <?php $selected = (isset($contract) ? $contract->contract_category : '');
                     $category=get_contract_category();  
                        echo render_select('contract_category',$category,array('id','name'),'contract_category',$selected,array());?>
			 </div>

       <div class="col-md-4" id="contract_subcategory_div">
			 <?php $selected = (isset($contract) ? $contract->contract_subcategory : '');
                     $sub_category=get_contract_subcategory();  
                        echo render_select('contract_subcategory',$sub_category,array('id','name'),'contract_subcategory',$selected,array());?>
			 </div>

       <div class="col-md-4">
			 <?php $selected = (isset($contract) ? $contract->purchaser : '');
                     $staffs=$this->db->get_where('tblstaff',array('active'=>1))->result_array(); 
                        echo render_select('purchaser',$staffs,array('staffid',array('firstname','lastname')),'purchaser',$selected,array());?>
			 </div>

       <div class="col-md-4">
			 <?php $selected = (isset($contract) ? $contract->contract_department : '');
                     $this->load->model('departments_model');
                      $departments=$this->departments_model->get();
                        echo render_select('contract_department',$departments,array('departmentid','name'),'contract_department',$selected,array());?>
			 </div>
       <?php } ?>


           <?php if((isset($contract) && $contract->contract_filename == NULL)){ ?> 
            <div class="form-group col-md-4">
               <label for="installment_receipt" class="profile-image"><?php echo _l('upload_contract'); ?></label>
               <input type="file" name="agree_attachment" class="form-control" id="agree_attachment">
            </div>
         <?php } ?>

          <?php if($contract->type=='contracts'){ ?>

   <!--------------additional--------------------------------->
			 <div id="contract_install" class="hide">
            <div class="col-md-4">
               <?php $value = (isset($contract) ? $contract->no_of_installment : ''); ?>
               <?php echo render_input('no_of_installment','no_of_installment',$value); ?>
            </div>
            <div class="col-md-4">
               <?php $value = (isset($contract) ? _d($contract->default_effective_date) : ''); ?>
               <?php echo render_date_input('default_effective_date','default_effective_date',$value); ?>
            </div>
           <div class="col-md-4">
               <?php $value = (isset($contract) ? $contract->installment_amount : ''); ?>
               <?php echo render_input('installment_amount','installment_amount',$value,'number'); ?>
            </div>
			</div>
			 <div class="col-md-4 hide">
			   <?php $selected = (isset($contract) ? $contract->status : '2'); ?>
                      <?php echo render_select('status',$statuses,array('id','name'),'status',$selected,array(),array(),'','',false); ?>
			 </div>
			  <div class="col-md-4 hide">
               <?php $value = (isset($contract) ? _d($contract->final_expiry_date) : ''); ?>
               <?php echo render_date_input('final_expiry_date','final_expiry_date',$value); ?>
            </div>
              <div class="col-md-4 ">
                       
                     <?php
                         $selected = array();
                         if(isset($project_members)){
                            foreach($project_members as $member){
                                array_push($selected,$member['staff_id']);
                            }
                        } else {
                            array_push($selected,get_staff_user_id());
                        }
                        echo render_select('project_members[]',$staff,array('staffid',array('firstname','lastname')),'contract_assignees',$selected,array('multiple'=>true,'data-actions-box'=>true),array(),'','',false);
                        ?>
                    
                     </div>
                           
                             <div class="col-md-4 mtop35 hide">
                  <div class="checkbox checkbox-primary billable">
               <input type="checkbox" id="is_autorenewal" name="is_autorenewal" <?php if(isset($contract)){if($contract->is_autorenewal == 1){echo 'checked';}}; ?>>
               <label for="is_autorenewal"><?php echo _l('is_autorenewal'); ?></label>
            </div>
				   </div>
           <?php } ?>
           <div class="col-md-12">
         <?php $value = (isset($contract) ? $contract->description : ''); ?>
         <?php echo render_textarea('description','contract_description',$value,array('rows'=>10)); ?>
			 </div>
         <?php $rel_id = (isset($contract) ? $contract->id : false); ?>
         <?php echo render_custom_fields('contracts',$rel_id); ?>
         <div class="btn-bottom-toolbar text-right">
            <button type="submit" class="btn btn-info"><?php echo _l('submit'); ?></button>
         </div>
         <?php echo form_close(); ?>
      </div>
      </div>
      </div>
	<!-----------tab contract------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'tab_contracts'){echo ' active';} ?> <?php if(empty($contract->contract_template_id) && empty($contract->content)) echo 'hide';?>" id="tab_contracts">
         <div class="row mtop20">

         <?php  if(isset($contract)&& !empty($contract->contract_template_id)){ ?>
                                       <div class="col-md-12">
                                          <?php if(isset($contract_merge_fields)){ ?>
                                             <hr class="hr-panel-heading" />
                                             <p class="bold mtop10 text-right"> <a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                                             <div class=" avilable_merge_fields mtop15 hide">
                                                <ul class="list-group">
                                                   <?php
                                                   foreach($contract_merge_fields as $field){
                                                   foreach($field as $f){
                                                      echo '<li class="list-group-item"><b>'.$f['name'].'</b>  <a href="#" class="pull-right" onclick="insert_merge_field(this); return false">'.$f['key'].'</a></li>';
                                                   }
                                                }
                                                ?>
                                             </ul>
                                          </div>
                                       <?php } ?>
                                       </div>
			         <div class="col-md-12">
                        <?php if(isset($contract_closure_fields)){ ?>
                           <hr class="hr-panel-heading" />
                           <p class="bold mtop10 text-right"><a href="#" onclick="slideToggle('.avilable_closure_fields'); return false;"><?php echo _l('available_closure'); ?></a></p>
                           <div class=" avilable_closure_fields mtop15 hide">
                              <ul class="list-group">
                                 <?php
                                 foreach($contract_closure_fields as $f1){?>
                                   
                                      <li class="list-group-item"><b><?=$f1['name']?></b>  <a href="#" class="pull-right" onclick="insert_template(this,'contracts',<?php echo $f1['id']; ?>);return false;" ><?=$f1['name']?></a></li>
									 
                                   
                               <?php }
                                ?>
                             </ul>
                          </div>
                       <?php } ?>
						
                    </div>
                                 <?php } ?> 
     
         </div>
		      <hr class="hr-panel-heading" />
                 <?php if(!staff_can('edit','contracts')) { ?>
                  <div class="alert alert-warning contract-edit-permissions">
                     <?php echo _l('contract_content_permission_edit_warning'); ?>
                  </div>
               <?php } ?>
                <div class="tc-content <?php if(staff_can('edit','contracts')){echo ' editable';}?> <?php if(!empty($contract->review_content)){ echo ' col-md-6';} else { echo ' col-md-12' ;} ?>"
                  style="border:1px solid #d2d2d2;min-height:70px; border-radius:4px;">
                  <?php
                  if(empty($contract->content) && staff_can('edit','contracts')){
                    echo hooks()->apply_filters('new_contract_default_content', '<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>');
                 } else {
                    echo $contract->content;
                 }
							
                 ?>
      </div>
       <div class="tc-content col-md-6 <?php if(empty($contract->review_content)){ echo ' hide' ;} ?>"
                  style="border:1px solid #d2d2d2;min-height:70px; border-radius:4px;padding-left: 3px;">
                  <?php
                  if(!empty($contract->review_content) && staff_can('edit','contracts')){
                   
                    echo $contract->review_content;
                 }
							
                 ?>
      </div>
		  	 <div class="row mtop25 mbot20">
              <?php if(!empty($contract->signature)) { ?>
              
                  <div class="col-md-6  text-left">
                     <div class="bold">
						 <h4><?php echo _l('first_party')?></h4>
                        <p class="no-mbot"><?php echo _l('contract_signed_by') . ": {$contract->acceptance_firstname} {$contract->acceptance_lastname}"?></p>
                        <p class="no-mbot"><?php echo _l('contract_signed_date') . ': ' . _dt($contract->acceptance_date) ?></p>
                        <p class="no-mbot"><?php echo _l('contract_signed_ip') . ": {$contract->acceptance_ip}"?></p>
                     </div>
                     <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                     <?php if($contract->signed == 1 && has_permission('contracts','','delete')){ ?>
                        <a href="<?php echo admin_url('contracts/clear_signature/'.$contract->id); ?>" data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>" class="_delete text-danger">
                           <i class="fa fa-remove"></i>
                        </a>
                     <?php } ?>
                     </p>
                     <div class="pull-left">
                        <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_upload_path_by_type('contract').$contract->id.'/'.$contract->signature)); ?>" class="img-responsive" alt="">
                     </div>
               </div>
           
         <?php } ?>
		     
             
				    <?php if(!empty($contract->party_signature)) { ?>
                  <div class="col-md-6 text-right">
                     <div class="bold">
						  <h4><?php echo _l('second_party')?></h4>
                        <p class="no-mbot"><?php echo _l('contract_signed_by') . ": {$contract->partyacc_firstname} {$contract->partyacc_lastname}"?></p>
                        <p class="no-mbot"><?php echo _l('contract_signed_date') . ': ' . _dt($contract->partyacc_date) ?></p>
                        <p class="no-mbot"><?php echo _l('contract_signed_ip') . ": {$contract->partyacc_ip}"?></p>
                     </div>
                     <p class="bold"><?php echo _l('document_customer_signature_text'); ?>
                     <?php if($contract->party_signed == 1 && has_permission('contracts','','delete')){ ?>
                        <a href="<?php echo admin_url('contracts/clear_partysignature/'.$contract->id); ?>" data-toggle="tooltip" title="<?php echo _l('clear_signature'); ?>" class="_delete text-danger">
                           <i class="fa fa-remove"></i>
                        </a>
                     <?php } ?>
                     </p>
                     <div class="pull-right">
                        <img src="<?php echo site_url('download/preview_image?path='.protected_file_url_by_path(get_upload_path_by_type('contract').$contract->id.'/'.$contract->party_signature)); ?>" class="img-responsive" alt="">
                     </div>
               </div>
          
         <?php } ?>
				     </div>
				</div>
<!-----------tab content------------------------------------------------------------>








    <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'tab_contract'){echo ' active';} ?> <?php if(empty($contract->contract_filename)) echo 'hide';?>" id="tab_contract">
       <div class="col-md-12">
    <div class="col-md-4">
        <h3>Preview PDF & Sign Below</h3>
        
<?php 
$user_id = get_staff_user_id();
$user_approval = null;
$show_rejection_section = false;
$allowed_by_addedfrom = false;

// Check if current user is in contract_approvals
foreach ($contract_approvals as $approval) {
    if (isset($approval['addedfrom']) && (int)$approval['addedfrom'] === (int)$user_id) {
        $allowed_by_addedfrom = true;
    }

    if (isset($approval['staffid']) && (int)$approval['staffid'] === (int)$user_id) {
        $user_approval = $approval;

        // Extract needed fields safely
        $status = isset($approval['status']) ? strtolower(trim($approval['status'])) : '';
        $placeholder = isset($approval['sign_placeholder']) ? trim($approval['sign_placeholder']) : '';
        $approval_status = isset($approval['approval_status']) ? (int)$approval['approval_status'] : 0;

        // Check conditions to show rejection section
        if (
            !in_array($status, ['signed', 'rejected']) && // not signed/rejected
            !empty($placeholder) &&                       // placeholder not empty
            $placeholder !== '[]' &&                      // placeholder not empty array
            $placeholder !== 'null' &&                    // placeholder not 'null' string
            $approval_status !== 2                        // approval_status not 2
        ) {
            $show_rejection_section = true;
        }
    }
}

// Get first approver for auto-selection
$first_approver = !empty($contract_approvals) ? $contract_approvals[0] : null;
?>

<?php if ($allowed_by_addedfrom) { ?>
<div id="approvers">
    <h4>Approvers</h4>
    <!--14112025 start-->
    <!-- Dropdown to select approver -->
    <div class="form-group">
    <label for="approver-select">Select Approver:</label>
    <select id="approver-select" class="form-control">
        <option value="">-- Choose an approver --</option>

        <?php foreach ($contract_approvals as $a): ?>

            <?php 
                // Skip this approver if approval_heading_id == 11
                if (isset($a['approval_heading_id']) && (int)$a['approval_heading_id'] === 11) {
                    continue;
                }
            ?>

            <option value="<?= $a['staffid'] ?>"
                    data-name="<?= get_staff_full_name($a['staffid']) ?>">
                <?= get_staff_full_name($a['staffid']) ?>
            </option>

        <?php endforeach; ?>

    </select>
</div>
<!--14112025 end-->
    
    <!-- Single draggable approver box -->
    <div id="current-approver-box" style="<?= $first_approver ? '' : 'display: none;' ?>">
        <div class="approver" draggable="true"
            id="draggable-approver"
            data-id="<?= $first_approver ? $first_approver['staffid'] : '' ?>"
            data-name="<?= $first_approver ? get_staff_full_name($first_approver['staffid']) : '' ?>"
            data-type="signature"
            style="padding: 15px; background-color: #f0f8ff; border: 2px solid #007bff; border-radius: 5px; margin-bottom: 10px; cursor: move;">
            <strong id="approver-name-display"><?= $first_approver ? get_staff_full_name($first_approver['staffid']) : '' ?></strong><br>
            <input type="text" class="page-input form-control" 
                placeholder="Pages (e.g. 1,2,3)" 
                id="current-page-input"
                data-approver-id="<?= $first_approver ? $first_approver['staffid'] : '' ?>"
                style="margin-top: 8px;">
            <button class="btn btn-xs btn-danger clear-approver" 
                    id="current-clear-btn"
                    data-approver-id="<?= $first_approver ? $first_approver['staffid'] : '' ?>" 
                    style="margin-top:5px;">
                Clear All
            </button>
        </div>
    </div>
    
    <!-- Common checkboxes for all approvers -->
    <div style="margin-top: 15px; padding: 12px; background-color: #f9f9f9; border: 1px solid #ddd; border-radius: 5px;">
        <h5 style="margin-top: 0; margin-bottom: 10px; font-size: 14px;">Signature Options (Applied to All Approvers)</h5>
        <label style="display: block; margin-bottom: 8px;">
            <input type="checkbox" id="inc_app_name" class="signature-option" style="margin-right: 5px;">
             Approver Name
        </label>
        <label style="display: block;">
            <input type="checkbox" id="inc_time_stamp" class="signature-option" style="margin-right: 5px;">
            Timestamp
        </label>
    </div>
</div>

<!-- Stamp Section -->
<div id="stamp-section" style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ccc;">
    <h4>Company Stamp</h4>
    <div class="approver" draggable="true"
        data-id="company_stamp"
        data-name="Company Stamp"
        data-type="stamp">
        Company Stamp<br>
        <input type="text" class="page-input form-control" 
            placeholder="Pages (e.g. 1,2,3)" 
            data-approver-id="company_stamp">
        <button class="btn btn-xs btn-danger clear-approver" 
                data-approver-id="company_stamp" 
                style="margin-top:5px;">Clear Stamp</button>
    </div>
</div>

<!-- Combined Save Button -->
<div style="margin-top: 20px; padding-top: 20px; border-top: 2px solid #ccc; text-align: center;">
    <button class="btn btn-success btn-lg" id="saveAllPositions">
        <i class="fa fa-save"></i> Save All Positions
    </button>
</div>

<?php } if ($show_rejection_section) { ?>
<div style="margin-top: 20px; padding: 15px; border: 2px solid #d9534f; border-radius: 5px; background-color: #f9f9f9;">
    <h4 style="color: #d9534f;">
        <i class="fa fa-times-circle"></i> Rejection Options
    </h4>
    
    <div class="form-group">
        <label>
            <input type="checkbox" id="rejection-checkbox" style="margin-right: 8px;">
            Mark this contract as rejected
        </label>
    </div>
    
    <div class="form-group" id="rejection-reason-group" style="display: none;">
        <label for="rejection-reason">Reason for Rejection <span class="text-danger">*</span></label>
        <textarea 
            class="form-control" 
            id="rejection-reason" 
            rows="4" 
            placeholder="Please provide a reason for rejecting this contract..."
            disabled></textarea>
    </div>
    
    <div id="rejection-save-group" style="display: none; text-align: center; margin-top: 15px;">
        <button class="btn btn-danger" id="save-rejection">
            <i class="fa fa-times"></i> Submit Rejection
        </button>
    </div>
</div>
<?php } ?>
    </div>
    
    <div class="col-md-8">
        <div id="pdf-container">
            <canvas id="pdf-canvas"></canvas>
        </div>
        <div id="pdf-controls" style="text-align:center; margin:10px 0;">
            <button id="prev-page">Previous</button>
            <span>Page <span id="page-num">1</span> / <span id="page-count">?</span></span>
            <button id="next-page">Next</button>
        </div>
    </div>
</div> 
         </div>
         
         
         
         




<!-----------tab version------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'tab_version'){echo ' active';} ?>" id="tab_version">
         <div class="row mtop20">

         <?php if(isset($contract)&& $contract->contract_filename == ''){ ?>
                                       <div class="col-md-12">
                                           <hr class="hr-panel-heading" />
                                            <a href="<?php echo admin_url('contracts/generate/'.$contract->id) ?>" data-toggle="tooltip" class="btn btn-info pull-right mbot15 hide" <?php if(get_option('enable_openai')==0){ echo 'title="For Advance features Contact Admin"';}else{echo _l('generate_contract');} ?> <?php if(get_option('enable_openai')==0){ echo 'disabled style="cursor:not-allowed;" onclick="return false;"';}?>>
                                                         <i class="fa fa-globe"></i>
                                                         <?php echo _l('generate_contract_AI'); ?>
                                                         </a>
                                          <?php if(isset($contract_merge_fields)){ ?>
                                             <hr class="hr-panel-heading" />
                                             <p class="bold mtop10 text-right hide"> <a href="#" onclick="slideToggle('.avilable_merge_fields'); return false;"><?php echo _l('available_merge_fields'); ?></a></p>
                                             <div class=" avilable_merge_fields mtop15 hide">
                                                <ul class="list-group">
                                                   <?php
                                                   foreach($contract_merge_fields as $field){
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
                                          <a href="#" data-toggle="tooltip" data-title="<?php echo _l('upload_contract'); ?>" class="btn btn-info" onclick="upload_contractversionfile(<?php echo $contract->id; ?>); return false;">
                                       <i class="fa fa-upload"></i>
                                       <?php echo _l('upload_contractversion'); ?>
                                    </a>
                                      <?php if(isset($contract) ){ ?>
                                                         <a href="<?php echo admin_url('contracts/generate/'.$contract->id.'/improve_version') ?>" data-toggle="tooltip" <?php if(get_option('enable_openai')==0){ echo 'title="For Advance features Contact Admin"';}else{echo _l('generate_contract');} ?> <?php if(get_option('enable_openai')==0){ echo 'disabled style="cursor:not-allowed;" onclick="return false;"';}?> class="btn btn-warning hide" >
                                                         <i class="fa fa-globe"></i>
                                                         <?php echo _l('improve_version_AI'); ?>
                                                         </a>
                                                      <?php } ?>
                                          <?php
                                    if(get_option('enable_sharepoint')==1){?>
                                          <?php if($totalversions==0){?>
                                          <a href="<?php echo $contract->sharepoint_link; ?>" target="_blank" class="btn btn-warning btn-sm mleft20" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit_base') ?> </a>
                                          <?php } ?>
                                          <a href="#" class="btn btn-success btn-sm mright10" onclick="save_as_contract_new_version(<?php $contract->id ?>); return false;"><!-- <i class=" fa fa-info-circle pull-left fa-lg" data-toggle="tooltip" data-title="<?php echo _l('load_latest_content_from_sharepoint_info'); ?>"></i> --> <?php echo _l('load_latest_content_from_sharepoint') ?> <i class="fa fa-arrow-circle-down"></i></a>
                                          <?php } ?>
                                       </p>
                              <?php } ?>

         <?php
                  // select all contract versions
                  $contract_versions = get_all_contract_versions($contract->id); ?>
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



   <?php foreach ($contract_versions as $contract_version){?>
      <tr>
      		<td><?php echo $contract_version['version']?></td>
			<td width="20%"><?php echo $contract_version['version_internal_file_path'];?>
			<?php if(get_option('enable_sharepoint')==1){?>
			 <!-- <a href="<?php echo $contract_version['version_sharpoint_link'] ?>"  target="_blank" class="btn btn-warning btn-sm mleft10" ><i class="fa fa-file-word-o"></i> <?php echo _l('view_edit') ?> </a>-->
			 <?php } ?>
			 </td>
			
			<td><?php echo _d($contract_version['dateadded'])?></td>
			<td><?php echo get_staff_full_name($contract_version['addedby'])?></td>
			<td><?php
		 $path1 = site_url('download/downloadagreementversion/'. $contract_version['contractid'].'/'.$contract_version['id']);
			 
    $file_path   = get_upload_path_by_type('contract').$contract_version['contractid'].'/'.$contract_version['version_internal_file_path'];
    if(file_exists($file_path)){ 
	
		$dispaly = '<a href="'. $path1 .'"  class="btn btn-sm btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('download').'" data-placement="bottom"><i class="fa fa-download" aria-hidden="true"></i></a>';
		
		echo $dispaly;
	}else{
        echo  '-';
    }
	 ?>
	
			</td>
			<td>
				                    <?php if($contract_version['active']== 1){?>
                              <?php if($contract->final_doc != $contract_version['id']){  ?>
                              <a class="btn btn-success" title=" <?php echo _l('mark_as_final') ?>" href="<?php echo admin_url('contracts/mark_as_final_doc/'.$contract->id.'/'.$contract_version['id']) ?>"><i class="fa fa-star-o"></i></a>
                              <?php } else { echo ''._l('marked_as_final'); }
                              } ?>
                              <?php echo icon_btn('contracts/delete_version/' . $contract_version['id'].'/'.$contract_version['contractid'], 'remove', 'btn-danger _delete hide'); ?>
			</td>
      <td>

		   <?php
            $checked = '';
            if($contract_version['active'] == 1){
              $checked = 'checked';
            }
            ?>
		  <div class="onoffswitch">
              <input type="checkbox" data-switch-url="<?php echo admin_url(); ?>contracts/change_version_status" id="<?php echo $contract_version['id']; ?>" data-id="<?php echo $contract_version['id']; ?>" class="onoffswitch-checkbox" value="<?php echo $contract_version['id']; ?>" <?php echo $checked; ?>>
              <label class="onoffswitch-label" for="<?php echo $contract_version['id']; ?>"></label>
            </div>
      </td>
		</tr>
   <?php } ?>
   </table>
         </div>
      </div>
<!-----------tab version------------------------------------------------------------>
<!-----------tab amendment------------------------------------------------------------>
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'tab_amendment'){echo ' active';} ?>" id="tab_amendment">
         <div class="row mtop20">

         <?php if(isset($contract)&& $contract->contract_filename == ''){ ?>
                                      
                                 <?php }else{ ?> 
                                       <hr class="hr-panel-heading" />
                                       <p class="bold mtop10 text-right"> 
                                          <a href="#" data-toggle="tooltip" data-title="<?php echo _l('contract_amendment'); ?>" class="btn btn-info" onclick="upload_contractamendment(<?php echo $contract->id; ?>); return false;">
                                       <i class="fa fa-upload"></i>
                                       <?php echo _l('create_ammendment'); ?>
                                    </a>
										  
                                  
                                       </p>
                              <?php } ?>

  
                    <table 
	class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('amendment_number')?></th>
			<th width="20%"><?php echo _l('file_name')?></th>
			<th><?php echo _l('amendment_text')?></th>
			<th><?php echo _l('contract_effectivedate')?></th>
			<th><?php echo _l('status')?></th>
			<th><?php echo _l('created_by')?></th>
			<th><?php echo _l('attachments')?></th>
			<th class="hide"><?php echo _l('action')?></th>
			
		</tr>
	</thead>



   <?php foreach ($contract_amendments as $contract_amendment){?>
      <tr>
      		<td><?php echo $contract_amendment['amendment_number']?></td>
			<td width="20%"><?php echo $contract_amendment['amendement_file'];?> </td>
			<td><?php echo $contract_amendment['amendment_text']?></td>
			<td><?php echo _d($contract_amendment['effective_date'])?></td>
			<td><?php echo ucwords($contract_amendment['amend_status'])?></td>
			<td><?php echo get_staff_full_name($contract_amendment['created_by'])?></td>
			<td><?php
		  $path1 = site_url('download/downloadagreementamendment/'. $contract_amendment['contract_id'].'/'.$contract_amendment['id']);
			 
    $file_path   = get_upload_path_by_type('contract').$contract_amendment['contract_id'].'/'.$contract_amendment['amendement_file'];
    if(file_exists($file_path) && !empty($contract_amendment['amendement_file'])){ 
	
		$dispaly = '<a href="'. $path1 .'"  class="btn btn-sm btn-warning btn-with-tooltip" data-toggle="tooltip" download title="'._l('download').'" data-placement="bottom"><i class="fa fa-download" aria-hidden="true"></i></a>';
		
		echo $dispaly;
	}else{
        echo  '-';
    }
	 ?>
	
			</td>
			<td>
				         
                              <?php echo icon_btn('contracts/delete_amendment/' . $contract_amendment['id'].'/'.$contract_amendment['contract_id'], 'remove', 'btn-danger _delete hide '); ?>
			</td>
     
		</tr>
   <?php } ?>
   </table>
         </div>
      </div>
<!-----------tab version------------------------------------------------------------>
<div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'comparison'){echo ' active';} ?>" id="comparisons">
      <div class="row">

    <div class="panel_s">

      <div class="panel-body">
        <?php
                  // select all contract versions
                  $contract_versions = get_all_contract_versions($contract->id); ?>
       <div class="col-md-6 border-right">

      <p class="bold"><?php echo _l('original_contract'); ?></p>

    <?php   echo render_select('first_contract',$contract_versions,array('id','version'),''); ?>

    <div class="" style="height: 500px;overflow-y: auto;">
  <?php echo render_textarea('orginal_contract','',$contract->content,array(),array('rows'=>'10'),'','tinymce'); ?>
    </div>

   </div>
       <div class="col-md-6 border-right">
        <p class="bold"><?php echo _l('version_contract'); ?></p>
        <?php   echo render_select('second_contract',$contract_versions,array('id','version'),''); ?>
      

<div class="" style="height: 500px;overflow-y: auto;">
       <?php echo render_textarea('version_contract','',$latest_version_contract,array(),array('rows'=>'10'),'','tinymce'); ?>
</div>


        

      </div>
      </div>
      </div>
      </div>
   <?php if(has_permission('contracts', '', 'create') || has_permission('contracts', '', 'edit') ){ ?>
       
    <hr />
    <button id="compare-btn" class="btn btn-warning"><?php echo _l('compare_document'); ?></button>
    <hr />
    <div class="row">
       <div class="col-md-12">
        <div class="panel_s panel-info">
                     <div class="panel-body">
<div class="tc-content project-overview-description">
   <p class="text-uppercase bold text-dark font-medium" style="color: green"><?php echo _l('last_comparison_result'); ?></p>
   <hr class="hr-panel-heading project-area-separation" />
  <div id="comparisonResult"></div>
   <?php if(empty($contract->comparison_result)){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_comparison_result') . '</p>';
   }
   echo '<b>'.check_for_links($contract->comparison_result).'</b>';?>
</div>
      </div></div></div></div>
<?php echo form_open(admin_url('contracts/fetch_contractcomparison/'.$contract->id)); ?>
  <div class="hide">
   <?php echo render_input('right_version','document2',$latestversionid,'text'); ?>
  </div>
<button type="submit" class="btn btn-info hide"><?php echo _l('compare_document'); ?></button>
<?php echo form_close(); ?>

<hr>
  
   <?php } ?>
   <div class="clearfix"></div>
  <?php if (isset($view_compareurl)&& !empty($view_compareurl)){?>
  <a href="<?=$view_compareurl?>" target="_blank"><?php echo _l('view_comparison'); ?></a>
  <?php } ?>
<?php if (get_staff_user_id()=='1k'){?>
  <a href="<?= site_url('assets/images/drafftable_sample_preview.png')?>" target="_blank"><?php echo '|  '._l('preview_comparison'); ?></a>
  <?php } ?>

  
</div>
        <div role="tabpanel" class="tab-pane" id="tab_reminders">
                  <a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target=".reminder-modal-contract-<?php echo $contract->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('set_reminder'); ?></a>
                  <hr />
                  <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified')), 'reminders'); ?>
               </div>
      <div role="tabpanel" class="tab-pane" id="tab_notes">
         <?php echo form_open(admin_url('contracts/add_note/'.$contract->id),array('id'=>'sales-notes','class'=>'contract-notes-form')); ?>
         <?php echo render_textarea('description'); ?>
         <div class="text-right">
            <button type="submit" class="btn btn-info mtop15 mbot15"><?php echo _l('contract_add_note'); ?></button>
         </div>
         <?php echo form_close(); ?>
         <hr />
         <div class="panel_s mtop20 no-shadow" id="sales_notes_area">
         </div>
      </div>
      <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'comments'){echo ' active';} ?>" id="tab_comments">
         <div class="row contract-comments mtop15">
            <div class="col-md-12">
               <div id="contract-comments"></div>
               <div class="clearfix"></div><?php if(isset($contract)){ if($contract->marked_as_signed == 1){ $readonly='readonly';} else{ $readonly='';} } ?>
               <textarea name="content" id="comment" rows="4" class="form-control mtop15 contract-comment" <?php echo $readonly;?>></textarea>
               <button type="button" class="btn btn-info mtop10 pull-right" onclick="add_contract_comment();"><?php echo _l('proposal_add_comment'); ?></button>
            </div>
         </div>
      </div>
      <div role="tabpanel" class="tab-pane <?php if($this->input->get('tab') == 'negotiation'){echo ' active';} ?>" id="tab_negotiation">
         <div class="row contract-negotiation mtop15">
            <div class="col-md-12">
               <div class="_buttons">
         <a href="#" class="btn btn-success" data-toggle="modal" data-target="#negotiation">
             <?php echo _l('add_negotiation'); ?>
         </a>
      </div>
   </div>
         </div>
          <div class="row mtop20">
 
      <table 
	class="table dt-table">
	<thead>
		<tr>
			<th><?php echo _l('negotiation')?></th>
		    <th><?php echo _l('orginal_value')?></th>
			<th><?php echo _l('negotiate_value')?></th>
			<th><?php echo _l('added_by')?></th>
			<th><?php echo _l('dateadded')?></th>
         	<th><?php echo _l('action')?></th>
		
			
		</tr>
	</thead>



   <?php foreach ($contract_negotiations as $contract_negotiation){?>
      <tr>
      		<td><?php echo $contract_negotiation['content']?></td>
			<td><?php echo $contract->contract_value;?> </td>
			<td><?php echo $contract_negotiation['negotiate_value'];?> </td>
			<td><?php echo get_staff_full_name($contract_negotiation['staffid']);?> </td>
			<td><?php echo _d($contract_negotiation['dateadded']);?> </td>
         <td> <a href="<?php echo admin_url('contracts/remove_negotiation/'.$contract_negotiation['id'].'/'.$contract->id ); ?>" class="btn btn-danger _delete "><?php echo _l('delete') ?></a></td>
		
     
		</tr>
   <?php } ?>
   </table>
         </div>
      </div>
      
      <div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'attachments'){echo ' active';} ?>" id="attachments">
         <?php echo form_open(admin_url('contracts/add_contract_attachment/'.$contract->id),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>
         <?php echo form_close(); ?>
         <div class="text-right mtop15">
            <button class="gpicker" data-on-pick="contractGoogleDriveSave">
               <i class="fa fa-google" aria-hidden="true"></i>
               <?php echo _l('choose_from_google_drive'); ?>
            </button>
            <div id="dropbox-chooser"></div>
            <div class="clearfix"></div>
         </div>
         <!-- <img src="https://drive.google.com/uc?id=14mZI6xBjf-KjZzVuQe8-rjtv_wXEbDTw" /> -->

         <div id="contract_attachments" class="mtop30">
            <?php
            $data = '<div class="row">';
            foreach($contract->attachments as $attachment) {
             $href_url = site_url('download/file/contract/'.$attachment['attachment_key']);
             if(!empty($attachment['external'])){
              $href_url = $attachment['external_link'];
           }
           $data .= '<div class="display-block contract-attachment-wrapper">';
           $data .= '<div class="col-md-10">';
           $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
           $data .= '<a href="'.$href_url.'"'.(!empty($attachment['external']) ? ' target="_blank"' : '').'>'.$attachment['file_name'].'</a>';
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
<div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'risklist'){echo ' active';} ?>" id="risklists">
   <?php if(has_permission('contracts', '', 'create') || has_permission('contracts', '', 'edit') ){ ?>
       
    <hr />
<?php echo form_open(admin_url('contracts/save_risk_checklist/'.$contract->id)); ?>
<div class="col-md-6">
    <?php
                         $selected = array();
                         if(isset($contract_risklist)){
                            foreach($contract_risklist as $risk){
                                array_push($selected,$risk['riskid']);
                            }
                        } 
                        echo render_select('risklist[]',$risklists,array('id','name'),'risk_checklist',$selected,array('multiple'=>true,'data-actions-box'=>true,'required'=>true),array(),'','',false);
                        ?>
                        </div>
                        <div class="col-md-6 mtop25">

<button type="submit" class="btn btn-info"><?php echo _l('add_list'); ?></button>
</div>
<?php echo form_close(); ?>

<hr>
   <?php } ?>
   <div class="clearfix"></div>
      <div class="row">
                  <div class="col-md-12">
                     <?php if(count($contract_risklist)>0){ ?> 
                        <table class="table table-bordered text-center">
                          <tr><th>
							    <div class="col-md-4 mtop20 bold" style="text-transform: uppercase"><?=_l('keyarea_provision')?> </div>
                          <div class="col-md-5 mtop20 bold"style="text-transform: uppercase"><?=_l('remarks')?></div>
							   <div class="col-md-3 mtop20 bold" style="text-transform: uppercase"><?=_l('status')?> </div></th></tr>
                          <?php foreach($contract_risklist as $approval){ ?>
                           <tr>
                              <th>
                                 <div class="col-md-4 mtop20">
                                    <?=$approval['riskname'].'<br>'.$approval['riskprovision']?>
                              
                                 </div>
                             
                                 <div class="col-md-5 mtop20">
                                    <?php echo render_textarea('remarks','',$approval['remarks'],array('rows'=>1,'placeholder'=>'Remarks','onblur'=>'update_riskapproval_remarks(this,'.$approval['id'].')')); ?>
                                 </div>
                                     <div class="col-md-3 mtop20">
                                   <?php $statuses = [['id'=>'0','name'=>''],['id'=>'1','name'=>'Complaint'],['id'=>'2','name'=>'Non-Complaint'],['id'=>'3','name'=>'Not Relevant']];?>
                                    <?php  if($approval['addedfrom'] != get_staff_user_id()){ 
                                       $attr=array('disabled'=>'disabled'); } else{
                                          $attr=array('onchange'=>'update_riskapproval_status(this,'.$approval['id'].')');}
                                          ?>
                                    <?php echo render_select('approval_status',$statuses,array('id','name'),'',$approval['approval_status'],$attr,array(),'no-mbot','',false); ?>
                                 </div>
                              </th>
                           </tr>
                           <?php }  ?>
                        </table>


                     <?php }?>
							</div>
	</div>

</div>
<div role="tabpanel" class="tab-pane<?php if($this->input->get('tab') == 'renewals'){echo ' active';} ?>" id="renewals">
   <?php if(has_permission('contracts', '', 'create') || has_permission('contracts', '', 'edit')){ ?>
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
               <a href="<?php echo admin_url('contracts/delete_renewal/'.$renewal['id'] . '/'.$renewal['contractid']); ?>" class="pull-right _delete text-danger"><i class="fa fa-remove"></i></a>
               <br />
            <?php } ?>
            <small class="text-muted"><?php echo _dt($renewal['date_renewed']); ?></small>
            <hr class="hr-10" />
             <?php if(($renewal['renewed_by_staff_id'] == get_staff_user_id() || is_admin()) && !empty($renewal['new_fiilename'])){ ?>
                <a  href="<?php echo site_url('download/downloadcontractfile/').$renewal['contractid'].'/'.$renewal['id']; ?>" class="pull-right text-info" title="Download Renewal" ><i class="fa fa-download"></i></a>
               
               <br />
            <?php } ?>
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
              $contract_renewal_value_tooltip = ' data-toggle="tooltip" data-title="'._l('contract_renewal_old_value', app_format_money($renewal['old_value'], $base_currency)).'"';
           } ?>
           <span class="text-success bold"<?php echo $contract_renewal_value_tooltip; ?>>
            <?php echo _l('contract_renewal_new_value', app_format_money($renewal['new_value'], $base_currency)); ?>
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
   <?php if(has_permission('contracts', '', 'create') || has_permission('contracts', '', 'edit') ){ ?>
       
      <div class="_buttons">
        <?php 
        if($contract->type=='contracts')
					$service='contract';
        else
        $service='po';?>
         <?php if(!get_contract_count($contract->id,$service)){ ?>
         <a class="btn btn-info" href="#" onclick="load_approval_modal('<?php echo admin_url('approval/approvals?rel_name='.$service.'&rel_id='.$contract->id); ?>');return false;"><?=_l('new_approval')?></a>
         <?php }else{?>
			 <a class="btn btn-info" href="#" onclick="load_approval_modal('<?php echo admin_url('approval/approvals?rel_name='.$service.'&rel_id='.$contract->id); ?>');return false;"><?=_l('edit_approval')?></a>			
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
    get_tracked_emails($contract->id, 'contract'))
);
?>
</div>
<div role="tabpanel" class="tab-pane" id="tab_tasks">
   <?php init_relation_tasks_table(array('data-new-rel-id'=>$contract->id,'data-new-rel-type'=>'contract')); ?>
</div>
<div role="tabpanel" class="tab-pane" id="tab_templates">
   <div class="row contract-templates hide">
      <div class="col-md-12">
         <button type="button" class="btn btn-info" onclick="add_template('contracts', <?php echo $contract->id ?>);"><?php echo _l('add_template'); ?></button>
         <hr>
      </div>
      <div class="col-md-12">
         <div id="contract-templates" class="contract-templates-wrapper"></div>
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
<?php if(isset($contract)){ ?>
   <!-- init table tasks -->
   <script>
      var contract_id = '<?php echo $contract->id; ?>';
  </script>
   
   <?php $this->load->view('admin/contracts/send_to_client'); ?>
   <?php $this->load->view('admin/contracts/send_to_otherparty'); ?>
   <?php //$this->load->view('admin/contracts/send_for_approval'); ?>
   <?php $this->load->view('admin/contracts/renew_contract'); ?>
   <?php $this->load->view('admin/contracts/contract_type'); ?>
   <?php $this->load->view('admin/contracts/external_contract_upload'); ?>
   <?php $this->load->view('admin/contracts/external_contract_version_upload'); ?>
    <?php $this->load->view('admin/contracts/external_contract_amendment_upload'); ?>
   <?php $this->load->view('admin/contracts/external_signed_contract_upload'); ?>
<?php $this->load->view('admin/approval/approval_js'); ?>
<?php $this->load->view('admin/contracts/negotiations'); ?>
<script type="text/javascript">
	init_approval_table( '<?php echo $service; ?>', '<?php echo $contract->id; ?>');
</script>
<!-- The reminders modal -->
<?php $this->load->view('admin/includes/modals/reminder',array(
   'id'=>$contract->id,
   'name'=>'contract',
   'members'=>$staff,
   'reminder_title'=>_l('set_reminder'))
); ?>
<?php } 
foreach ($contract_approvals as &$approval) {
    if (isset($approval['staffid'])) {
        $approval['staff_name'] = get_staff_full_name($approval['staffid']);
        // Optional: if you want to replace staffid completely
        // $approval['staffid'] = get_staff_full_name($approval['staffid']);
    }
}?>


<script>
   Dropzone.autoDiscover = false;
   $(function () {
	  // get_templates_of_contract_ajax1();
     init_ajax_project_search_by_customer_id();
      get_contract_comments_overview();
      var approvalType = '<?php echo ($contract->type == "contracts" ? "contract" : $contract->type); ?>';
     init_approval_table_overview( approvalType, '<?php echo $contract->id; ?>');
     if ($('#contract-attachments-form').length > 0) {
        new Dropzone("#contract-attachments-form",appCreateDropzoneOptions({
           success: function (file) {
              if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                 var location = window.location.href;
                 window.location.href = location.split('?')[0] + '?tab=attachments';
              }
           }
        }));
     }

    // In case user expect the submit btn to save the contract content
    $('#contract-form').on('submit', function () {
     $('#inline-editor-save-btn').click();
     return true;
  });

    if (typeof (Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
     document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
        success: function (files) {
           $.post(admin_url + 'contracts/add_external_attachment', {
              files: files,
              contract_id: contract_id,
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

  appValidateForm($('#contract-form'), {
     client: 'required',
     datestart: 'required',
     subject: 'required'
  });

  appValidateForm($('#renew-contract-form'), {
     new_start_date: 'required'
  });

  var _templates = [];
  $.each(contractsTemplates, function (i, template) {
     _templates.push({
        url: admin_url + 'contracts/get_template?name=' + template,
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
           save_contract_content(true);
        });

        editor.addShortcut('Meta+S', '', 'mceSave');

        editor.on('MouseLeave blur', function () {
           if (tinymce.activeEditor.isDirty()) {
              save_contract_content();
           }
        });

        editor.on('MouseDown ContextMenu', function () {
           if (!is_mobile() && !$('.left-column').hasClass('hide')) {
              contract_full_view();
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
         save_contract_content();
      }
   });
  }

  tinymce.init(editor_settings);
  var tab1='<?php echo $tab; ?>';
	   if(tab1=='comments')
        get_contract_comments();
	   
var cttype = $('#payment_terms').val();
		
		contractinstallment_action(cttype);
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
					 $('#contract_value').val(response.file_amount);
					$('#other_party').selectpicker('val',response.opposteparty);
					
					 var ctype = $('#client');
				ctype.find('option:first').after('<option value="'+response.userid+'">'+response.company+'</option>');
                ctype.selectpicker('val',response.userid);
                ctype.selectpicker('refresh');
												
				}
			});
		});
function save_contract_content(manual) {
  var editor = tinyMCE.activeEditor;
  var data = {};
  data.contract_id = contract_id;
  data.content = editor.getContent();
  $.post(admin_url + 'contracts/save_contract_data', data).done(function (response) {
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

 function delete_contract_attachment(wrapper, id) {
  if (confirm_delete()) {
     $.get(admin_url + 'contracts/delete_contract_attachment/' + id, function (response) {
        if (response.success == true) {
           $(wrapper).parents('.contract-attachment-wrapper').remove();

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

function contract_full_view() {
  $('.left-column').toggleClass('hide');
  $('.right-column').toggleClass('col-md-7');
  $('.right-column').toggleClass('col-md-12');
  $(window).trigger('resize');
}

function add_contract_comment(type='comment') {
   if(type=="comment"){
var comment = $('#comment').val();
   }else{
     var comment = $('#negotiation').val(); 
   }
  
  if (comment == '') {
     return;
  }
//   alert(comment);
  var data = {};
  data.content = comment;
   data.comment_type = type;
  data.contract_id = contract_id;
  $('body').append('<div class="dt-loader"></div>');
  $.post(admin_url + 'contracts/add_comment', data).done(function (response) {
     response = JSON.parse(response);
     $('body').find('.dt-loader').remove();
     if (response.success == true) {
        $('#comment').val('');
         $('#negotiation').val('');
        get_contract_comments(type);
     }
  });
}

function get_contract_comments(type='comment') {
  if (typeof (contract_id) == 'undefined') {
     return;
  }
  requestGet('contracts/get_comments/' + contract_id).done(function (response) {
    
    
      var totalComments= $('[data-commentid]').length;
     var commentsIndicator = $('.comments-indicator');
      //  var commentsIndicator_nego = $('.comments-indicator-nego');
     
       $('#contract-comments').html(response);
         if(totalComments == 0) {
      commentsIndicator.addClass('hide');
  
 
     }
   
});
}

function remove_contract_comment(commentid) {
  if (confirm_delete()) {
     requestGetJSON('contracts/remove_comment/' + commentid).done(function (response) {
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

function edit_contract_comment(id) {
  var content = $('body').find('[data-contract-comment-edit-textarea="' + id + '"] textarea').val();
  if (content != '') {
     $.post(admin_url + 'contracts/edit_comment/' + id, {
        content: content
     }).done(function (response) {
        response = JSON.parse(response);
        if (response.success == true) {
           alert_float('success', response.message);
           $('body').find('[data-contract-comment="' + id + '"]').html(nl2br(content));
        }
     });
     toggle_contract_comment_edit(id);
  }
}

function toggle_contract_comment_edit(id) {
  $('body').find('[data-contract-comment="' + id + '"]').toggleClass('hide');
  $('body').find('[data-contract-comment-edit-textarea="' + id + '"]').toggleClass('hide');
}

function contractGoogleDriveSave(pickData) {
   var data = {};
   data.contract_id = contract_id;
   data.external = 'gdrive';
   data.files = pickData;
   $.post(admin_url + 'contracts/add_external_attachment', data).done(function () {
    var location = window.location.href;
    window.location.href = location.split('?')[0] + '?tab=attachments';
 });
}
/* contract document change  */
	function delete_contract_document(contract_id) {
		
    requestGet('contracts/delete_contract_document/'+contract_id).done(function(){
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-agreeremove-img').addClass('hide');
		 var location = window.location.href;
    window.location.href = location.split('?')[0] + '?tab=tab_content';
    });
}

function delete_signed_contract_document(contract_id) {
    requestGet('contracts/delete_signed_contract_document/'+contract_id).done(function(){
        $('body').find('#signed-contact-profile-image').removeClass('hide');
        $('body').find('#signed-contact-agreeremove-img').addClass('hide');
		 var location = window.location.href;
    window.location.href = location.split('?')[0] + '?tab=tab_content';
    });
}


function save_as_contract_new_version(contract_id){
   $('#new-version-modal').modal('show');
}

function update_sharepoint(contractid) {
	 var version='no';
	 if($('#add_new_version').is(":checked"))   
         version=$('#add_new_version').val();
        else
			version='no';
	
		 $.post(admin_url + 'contracts/update_contractfile_version/' +contractid+'/'+version).done(function (response) {
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
		
		contractinstallment_action(cttype);
		
	});
	function contractinstallment_action(intype){
       
            if(intype =='One Time'){
                $('#contract_install').addClass('hide');
               
            } else if(intype =='installment'){
                $('#contract_install').removeClass('hide');
               
            } else {
                $('#contract_install').addClass('hide');
               
            }
        }
$('select[name="contract_type"]').change(function(){
        
  //  get_templates_of_contract_ajax1();
    
    });
    function get_templates_of_contract_ajax1() { 
        var clientSelected = $('select[name="contract_type"]').val();
        if(clientSelected !=''){
			$('#div_template').removeClass('hide');
            $.get(admin_url + 'contracts/get_templates_of_contract/'+clientSelected,function(response){
                var ctype = $('select[name="contract_template_id"]');
                $('select[name="contract_template_id"] option').remove();
                if(response ){
                    ctype.append('<option value=""></option>').selectpicker(); 
                    for (var i = 0; i < response.length; i++) {
                        ctype.append('<option value="'+response[i].id+'">'+response[i].name+'</option>').selectpicker();
                    }
                    <?php if(isset($contract)){ 
                            //$opp_ids = array_column($project->assigned_opposite_parties,'opposite_party_id');
                            $toe_id = $contract->contract_template_id;
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
  function update_riskapproval_status(th,id) {
        var status = $(th).val();
	 // alert(status);
        requestGetJSON('contracts/change_riskapproval_status_ajax/' + id + '/' + status).done(function(response) {
            alert_float(response.alert, response.message);
			setTimeout(function() {
              window.location.reload();
          }, 500);
        });
    };
   function update_riskapproval_remarks(th,id) {
      var remarks = $(th).val();
      var data={"remarks" : remarks};
      $.post(admin_url + 'contracts/change_riskapproval_remarks_ajax/'+id, data).done(function(response) {
         response = JSON.parse(response);
         alert_float(response.alert, response.message);
      });
   } 
   
   
function init_approval_table_overview(rel_name,rel_id,type=''){   
    var type = 'overview';
    
				
				$('#div_approvals_list_overview').html('');
				$.ajax({
					url: "<?php echo admin_url('approval/table')?>/"+rel_name+'/'+rel_id,
					data: {
                        
                        type: type,
                    },
					success: function(response)
					{
						$('#div_approvals_list_overview').html(response);
						
					}
				});
			}
 function get_contract_comments_overview() {
  if (typeof (contract_id) == 'undefined') {
     return;
  }
  requestGet('contracts/get_comments/' + contract_id).done(function (response) {
     $('#contract-comments-overview').html(response);
     var totalComments = $('[data-commentid]').length;
     var commentsIndicator = $('.comments-indicator');
      var contract_comments_overview=$('.contract_comments_overview');
     if(totalComments == 0) {
      commentsIndicator.addClass('hide');
      contract_comments_overview.addClass('hide');
   } else {
      commentsIndicator.removeClass('hide');
      contract_comments_overview.removeClass('hide');
      commentsIndicator.text(totalComments);
   }
});
}
</script>
<script>
$('#first_contract').on('change', function() {
              
        var department = $(this).val();
        var url=admin_url+'contracts/getversionInfo';
        // AJAX request
      $.ajax({
        url:url,
        method: 'post',
        data: {versionid: department},
        dataType: 'json',
        success: function(response){
          // $('#other_party').val(response.opposteparty);
           tinymce.get("orginal_contract").setContent(response.version_content);
                                     
        }
      });
    });
    $('#second_contract').on('change', function() {
              
        var department = $(this).val();
        var url=admin_url+'contracts/getversionInfo';
        // AJAX request
      $.ajax({
        url:url,
        method: 'post',
        data: {versionid: department},
        dataType: 'json',
        success: function(response){
          // $('#other_party').val(response.opposteparty);
           tinymce.get("version_contract").setContent(response.version_content);
                                     
        }
      });
    });
function handleSingleSelect(checkbox) {
  const checkboxes = document.querySelectorAll('#trash, #is_payable, #is_receivable');
  checkboxes.forEach(cb => {
    if (cb !== checkbox) cb.checked = false;
  });
  
  toggleCategoryFields();
}

$(document).ready(function () {
  // Check the value of PHP variable
  var isNonStd = <?php echo isset($contract) && $contract->is_non_std == 1 ? 'true' : 'false'; ?>;

   
   toggleCategoryFields();
  

 
});


function toggleCategoryFields() {
  var isPayableChecked = $('#is_payable').is(':checked');
  var isReceivableChecked = $('#is_receivable').is(':checked');

  var contractCategoryDiv = $('#contract_category_div');
  var contractSubcategoryDiv = $('#contract_subcategory_div');

  var contractCategoryGroup = $('#contract_category').closest('.form-group');
  var contractSubcategoryGroup = $('#contract_subcategory').closest('.form-group');

  if (isPayableChecked) {
    // Show both category and subcategory
    contractCategoryDiv.removeClass('hide');
    contractSubcategoryDiv.removeClass('hide');
    contractCategoryGroup.removeClass('hide');
    contractSubcategoryGroup.removeClass('hide');
  } else {
    // Hide both category and subcategory
    contractCategoryDiv.addClass('hide');
    contractSubcategoryDiv.addClass('hide');
    contractCategoryGroup.addClass('hide');
    contractSubcategoryGroup.addClass('hide');
  }
}


function toggleUploadAndTemplate() {
  const isNonStdChecked = document.querySelector('#is_non_std').checked;
  const ispayableChecked = document.querySelector('#is_payable').checked;
  const isreceivableChecked = document.querySelector('#is_receivable').checked;

  const uploadDiv = document.querySelector('#agree_attachment').closest('.form-group');
  const templateDiv = document.querySelector('#div_template');

   

  if (isNonStdChecked) {
    uploadDiv.classList.add('hide');      // Hide upload
    templateDiv.classList.remove('hide'); // Show template
    
  } else {
    uploadDiv.classList.remove('hide');   // Show upload
    templateDiv.classList.add('hide');    // Hide template
    
  }

}

function getAiSummary(contractId) {  
    
    let btn = $("#getAISummaryBtn");

    // Change button to "processing"
    btn.prop("disabled", true);
    btn.html('<i class="fa fa-spinner fa-spin"></i> Processing...');
  
  $.post(admin_url + 'contracts/summarizeWithAI/'+contractId, {
    contract_id: contractId,
    
  }).done(function(response) {
   response = JSON.parse(response);
   if (response.success == true) {
       
       btn.prop("disabled", false);
       btn.html('<i class="fa fa-magic"></i> Get AI Summary');
      
      location.reload();

            
            $(document).ready(function(){
                if ($("#aiSummarySection").length) {
                    $('html, body').animate({
                        scrollTop: $("#aiSummarySection").offset().top
                    }, 800);
                }
            });
   }
  });
  
}
$('#compare-btn').on('click', function() {
  $.ajax({
    url: admin_url + 'contracts/compare_versions',
    type: 'POST',
    data: {
      old_text: $('#orginal_contract').val(),
      new_text: $('#version_contract').val(),
      contractID:contract_id
    },
     beforeSend: function(){
      $('#comparisonResult').html('<p>Comparing versions...</p>');
    },
    success: function(res) {
      var data = JSON.parse(res);
      if (data.status === 'success') {
        $('#comparisonResult').html(data.highlight_html);

        //tinymce.activeEditor.setContent(data.highlight_html);
      }
    }
  });
});

</script>
<script src="https://mozilla.github.io/pdf.js/build/pdf.js"></script>
<!-- ✅ Styles -->
<style>
#approvers, #stamp-section {
  width: 200px;
  padding: 10px;
}
.approver {
  background: #e7f1ff;
  border: 1px solid #007bff;
  border-radius: 6px;
  padding: 6px;
  margin-bottom: 8px;
  text-align: center;
  cursor: grab;
}
.approver[data-type="stamp"] {
  background: #fff3cd;
  border-color: #ffc107;
}
#pdf-container {
  position: relative;
  flex: 1;
  overflow: auto;
  border: 1px solid #ccc;
  height: 80vh;
}

.sign-box {
  position: absolute;
  background: rgba(0,123,255,0.2);
  border: 2px dashed #007bff;
  border-radius: 4px;
  padding: 5px;
  font-size: 10px;
  cursor: move;
  /* ✅ Make boxes bigger to accommodate images */
  width: 150px;
  height: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  text-align: center;
  word-wrap: break-word;
  overflow: hidden;
}

.sign-box[data-type="stamp"] {
  background: rgba(255,193,7,0.2);
  border-color: #ffc107;
  /* ✅ Stamp can be different size if needed */
  width: 120px;
  height: 120px;
}

.sign-box .remove-box {
  position: absolute;
  top: 2px;
  right: 2px;
  cursor: pointer;
  color: red;
  font-weight: bold;
  font-size: 14px;
  line-height: 1;
  background: white;
  border-radius: 50%;
  width: 18px;
  height: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 10;
}

canvas {
  display: block;
  margin: 0 auto;
}
button.save {
  margin-top: 10px;
  padding: 6px 12px;
}
</style>

<!-- ✅ PDF.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js"></script>

<script>
const url = "<?= admin_url('contracts/view_uploadpdf/'.$contract->id) ?>";
const pdfjsLib = window['pdfjsLib'];
pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

let pdfDoc = null;
let pageNum = 1;
let scale = 1.4;

const canvas = document.getElementById('pdf-canvas');
const ctx = canvas.getContext('2d');
const pdfContainer = document.getElementById('pdf-container');

// ✅ Pass saved placeholders from PHP to JS
const savedPlaceholders = <?= json_encode($contract_approvals) ?>;
const savedStampPlaceholder = <?= json_encode($contract->stamp_placeholder ?? null) ?>;
const loggedInStaffId = "<?= $user_id ?>";
const isAdmin = <?= is_admin() ? 'true' : 'false' ?>;
const allowedByAddedFrom = <?= $allowed_by_addedfrom ? 'true' : 'false' ?>; // ✅ NEW

// ✅ Track removed placeholders
const removedApprovers = new Set();
const removedStamp = { isRemoved: false };

// ✅ Dragging state for existing boxes
let draggedBox = null;
let dragOffsetX = 0;
let dragOffsetY = 0;

// ✅ Load PDF
pdfjsLib.getDocument(url).promise.then(pdf => {
  pdfDoc = pdf;
  document.getElementById('page-count').textContent = pdfDoc.numPages;
  renderPage(pageNum);
});

// ✅ Render PDF page
function renderPage(num) {
  pdfDoc.getPage(num).then(page => {
    const viewport = page.getViewport({ scale });
    canvas.height = viewport.height;
    canvas.width = viewport.width;

    // Clear previous boxes
    document.querySelectorAll('.sign-box').forEach(e => e.remove());

    // Render PDF
    const renderTask = page.render({ canvasContext: ctx, viewport });
    renderTask.promise.then(() => {
      drawSavedPlaceholders(num);
      drawSavedStampPlaceholder(num);
      document.getElementById('page-num').textContent = num;
    });
  });
}

// ✅ Navigation
document.getElementById('prev-page').addEventListener('click', () => {
  if (pageNum <= 1) return;
  pageNum--;
  renderPage(pageNum);
});
document.getElementById('next-page').addEventListener('click', () => {
  if (pageNum >= pdfDoc.numPages) return;
  pageNum++;
  renderPage(pageNum);
});

// ✅ Helper function to create placeholder box
function createPlaceholderBox(id, name, type, x, y, page, isSaved = false) {
  const box = document.createElement('div');
  box.className = 'sign-box' + (isSaved ? ' saved-placeholder' : '');
  if (type === 'stamp') {
    box.setAttribute('data-type', 'stamp');
  }
  
  const pageLabel = page === 'all' ? 'all' : 'p' + page;
  
  // ✅ Only show close button if allowedByAddedFrom is true
  const closeButton = allowedByAddedFrom 
    ? '<span class="remove-box">×</span>' 
    : '';
  
  box.innerHTML = `
    ${closeButton}
    <div style="font-size: 10px; line-height: 1.2;">
      <strong>${name}</strong><br>
      <span style="font-size: 9px;">(${pageLabel})</span>
    </div>
  `;
  
  box.style.left = `${x}px`;
  box.style.top = `${y}px`;
  box.dataset.approver_id = id;
  box.dataset.page = page;
  box.style.cursor = allowedByAddedFrom ? 'move' : 'default'; // ✅ Only show move cursor if allowed
  
  // ✅ Add remove functionality (only if button exists)
  if (allowedByAddedFrom) {
    const removeBtn = box.querySelector('.remove-box');
    if (removeBtn) {
      removeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        if (confirm('Remove this placeholder?')) {
          box.remove();
          // Track removal
          setTimeout(() => {
            if (type === 'stamp') {
              const remainingStamps = document.querySelectorAll('.sign-box[data-type="stamp"]');
              if (remainingStamps.length === 0) {
                removedStamp.isRemoved = true;
              }
            } else {
              const remainingBoxes = document.querySelectorAll(`.sign-box[data-approver_id="${id}"]`);
              if (remainingBoxes.length === 0) {
                removedApprovers.add(id);
              }
            }
          }, 100);
        }
      });
    }
  }
  
  // ✅ Make box draggable on mousedown (only if allowedByAddedFrom)
  if (allowedByAddedFrom) {
    box.addEventListener('mousedown', function(e) {
      // Don't start drag if clicking the close button
      if (e.target.classList.contains('remove-box')) return;
      
      e.preventDefault();
      draggedBox = box;
      
      const rect = box.getBoundingClientRect();
      const containerRect = pdfContainer.getBoundingClientRect();
      
      dragOffsetX = e.clientX - rect.left;
      dragOffsetY = e.clientY - rect.top;
      
      box.style.opacity = '0.7';
      box.style.zIndex = '1000';
    });
  } else {
    // For non-allowed users, show pointer cursor but no drag
    box.style.cursor = 'default';
  }
  
  return box;
}

// ✅ Global mousemove handler for dragging existing boxes
document.addEventListener('mousemove', function(e) {
  if (!draggedBox) return;
  
  const containerRect = pdfContainer.getBoundingClientRect();
  const scrollX = pdfContainer.scrollLeft;
  const scrollY = pdfContainer.scrollTop;
  
  let x = e.clientX - containerRect.left + scrollX - dragOffsetX;
  let y = e.clientY - containerRect.top + scrollY - dragOffsetY;
  
  // Keep within bounds
  x = Math.max(0, Math.min(x, canvas.width - 150));
  y = Math.max(0, Math.min(y, canvas.height - 60));
  
  draggedBox.style.left = `${x}px`;
  draggedBox.style.top = `${y}px`;
  
  // Auto-scroll
  const offsetY = e.clientY - containerRect.top;
  if (offsetY < 50) {
    pdfContainer.scrollTop -= 20;
  } else if (offsetY > containerRect.height - 50) {
    pdfContainer.scrollTop += 20;
  }
});

// ✅ Global mouseup handler
document.addEventListener('mouseup', function(e) {
  if (draggedBox) {
    draggedBox.style.opacity = '1';
    draggedBox.style.zIndex = '';
    draggedBox = null;
  }
});

// ✅ Drag/drop logic for NEW placeholders from sidebar
let isDragging = false;
let alignmentGuide = null;

pdfContainer.addEventListener('drop', e => {
  e.preventDefault();
  isDragging = false;
  
  if (alignmentGuide) {
    alignmentGuide.remove();
    alignmentGuide = null;
  }

  const id = e.dataTransfer.getData('id');
  const name = e.dataTransfer.getData('name');
  const type = e.dataTransfer.getData('type');

  // ✅ Check if this approver has already signed (for signatures only)
  if (type !== 'stamp') {
    const approver = savedPlaceholders.find(a => a.staffid == id);
    if (approver && (approver.status === 'signed' || approver.approval_status === '3')) {
      alert('This approver has already signed. Cannot add new placeholder.');
      return;
    }
  }

  const existingBox = document.querySelector(
    `.sign-box[data-approver_id="${id}"][data-page="${pageNum}"]`
  );
  
  if (existingBox) {
    existingBox.remove();
  }

  const containerRect = pdfContainer.getBoundingClientRect();
  const scrollX = pdfContainer.scrollLeft;
  const scrollY = pdfContainer.scrollTop;

  let offsetX = 75;
  let offsetY = 50;
  
  if (type === 'stamp') {
    offsetX = 60;
    offsetY = 60;
  }

  let x = e.clientX - containerRect.left + scrollX - offsetX;
  let y = e.clientY - containerRect.top + scrollY - offsetY;

  // ✅ SMART Y-AXIS ALIGNMENT: Snap to existing placeholders' Y position if close
  if (type !== 'stamp') {
    const existingSignatures = Array.from(document.querySelectorAll(
      `.sign-box:not([data-type="stamp"])[data-page="${pageNum}"]`
    ));

    if (existingSignatures.length > 0) {
      let closestBox = null;
      let minDistance = Infinity;
      const dropY = y + offsetY;

      existingSignatures.forEach(box => {
        const boxY = parseFloat(box.style.top);
        const distance = Math.abs(boxY - dropY);
        
        if (distance < 80 && distance < minDistance) {
          minDistance = distance;
          closestBox = box;
        }
      });

      if (closestBox) {
        y = parseFloat(closestBox.style.top);
      }
    }
  }

  const box = createPlaceholderBox(id, name, type, x, y, pageNum, false);
  pdfContainer.appendChild(box);
});

// ✅ Visual alignment guide when dragging from sidebar
pdfContainer.addEventListener('dragover', e => {
  e.preventDefault();
  isDragging = true;

  const rect = pdfContainer.getBoundingClientRect();
  const offsetY = e.clientY - rect.top;

  // Auto-scroll logic
  if (offsetY < 50) {
    pdfContainer.scrollTop -= 20;
  } else if (offsetY > rect.height - 50) {
    pdfContainer.scrollTop += 20;
  }

  // Show alignment guide
  const scrollX = pdfContainer.scrollLeft;
  const scrollY = pdfContainer.scrollTop;
  const mouseY = e.clientY - rect.top + scrollY;

  const existingSignatures = Array.from(document.querySelectorAll(
    `.sign-box:not([data-type="stamp"])[data-page="${pageNum}"]`
  ));

  let showGuide = false;
  let guideY = 0;

  existingSignatures.forEach(box => {
    const boxY = parseFloat(box.style.top);
    if (Math.abs(boxY - mouseY + 50) < 80) {
      showGuide = true;
      guideY = boxY;
    }
  });

  if (showGuide && !alignmentGuide) {
    alignmentGuide = document.createElement('div');
    alignmentGuide.style.position = 'absolute';
    alignmentGuide.style.left = '0';
    alignmentGuide.style.width = '100%';
    alignmentGuide.style.height = '2px';
    alignmentGuide.style.background = '#007bff';
    alignmentGuide.style.pointerEvents = 'none';
    alignmentGuide.style.zIndex = '1000';
    alignmentGuide.style.top = guideY + 'px';
    pdfContainer.appendChild(alignmentGuide);
  } else if (!showGuide && alignmentGuide) {
    alignmentGuide.remove();
    alignmentGuide = null;
  } else if (showGuide && alignmentGuide) {
    alignmentGuide.style.top = guideY + 'px';
  }
});

pdfContainer.addEventListener('dragleave', () => {
  if (alignmentGuide) {
    alignmentGuide.remove();
    alignmentGuide = null;
  }
});

// ✅ Setup drag for ALL approver elements (including stamp and dynamic approver)
function setupDragEvents() {
  document.querySelectorAll('.approver').forEach(el => {
    el.addEventListener('dragstart', e => {
      const id = e.currentTarget.dataset.id || e.currentTarget.getAttribute('data-id');
      const name = e.currentTarget.dataset.name || e.currentTarget.getAttribute('data-name');
      const type = e.currentTarget.dataset.type || e.currentTarget.getAttribute('data-type') || 'signature';
      
      if (id && name) {
        e.dataTransfer.setData('id', id);
        e.dataTransfer.setData('name', name);
        e.dataTransfer.setData('type', type);
      } else {
        e.preventDefault();
        alert('Please select an approver from the dropdown first.');
      }
    });
  });
}

// Initial setup
setupDragEvents();

// Re-setup when dropdown changes (called from dropdown change event)
window.refreshDragEvents = setupDragEvents;

// ✅ Draw saved placeholders (signatures)
function drawSavedPlaceholders(pageNum) {
  if (!savedPlaceholders || !Array.isArray(savedPlaceholders)) return;

  savedPlaceholders.forEach(a => {
    if (!isAdmin && a.staffid != loggedInStaffId) return;
    
    if (!a.sign_placeholder || a.sign_placeholder === '[]' || a.sign_placeholder.trim() === '') return;
    
    let coords;
    try {
      coords = JSON.parse(a.sign_placeholder);
      if (!Array.isArray(coords) || coords.length === 0) return;
    } catch (e) {
      console.warn("Invalid placeholder JSON for approver", a.staff_name);
      return;
    }

    coords.forEach(pos => {
      if (pos.page === pageNum || pos.page === 'all') {
        const box = createPlaceholderBox(
          a.staffid,
          a.staff_name || 'Approver',
          'signature',
          pos.x,
          pos.y,
          pos.page,
          true
        );
        pdfContainer.appendChild(box);
      }
    });
  });
}

// ✅ Draw saved stamp placeholder
function drawSavedStampPlaceholder(pageNum) {
  if (!savedStampPlaceholder || savedStampPlaceholder === '[]' || savedStampPlaceholder.trim() === '') return;
  
  let coords;
  try {
    coords = JSON.parse(savedStampPlaceholder);
    if (!Array.isArray(coords) || coords.length === 0) return;
  } catch (e) {
    console.warn("Invalid stamp placeholder JSON");
    return;
  }

  coords.forEach(pos => {
    if (pos.page === pageNum || pos.page === 'all') {
      const box = createPlaceholderBox(
        'company_stamp',
        'Company Stamp',
        'stamp',
        pos.x,
        pos.y,
        pos.page,
        true
      );
      pdfContainer.appendChild(box);
    }
  });
}

$(document).ready(function() {
    // Store page input values for all approvers
    var approverPageInputs = {};
    
    // Load existing page inputs from PHP data
    <?php foreach ($contract_approvals as $a): ?>
        approverPageInputs['<?= $a['staffid'] ?>'] = '';
    <?php endforeach; ?>
    
    // Load common checkbox states from first approver (all should have same values)
    <?php 
    $first_inc_name = false;
    $first_inc_timestamp = false;
    if (!empty($contract_approvals)) {
        $first_inc_name = isset($contract_approvals[0]['inc_app_name']) && $contract_approvals[0]['inc_app_name'] == '1';
        $first_inc_timestamp = isset($contract_approvals[0]['inc_time_stamp']) && $contract_approvals[0]['inc_time_stamp'] == '1';
    }
    ?>
    $('#inc_app_name').prop('checked', <?= $first_inc_name ? 'true' : 'false' ?>);
    $('#inc_time_stamp').prop('checked', <?= $first_inc_timestamp ? 'true' : 'false' ?>);
    
    // Auto-select first approver on page load
    <?php if ($first_approver && $allowed_by_addedfrom): ?>
        $('#approver-select').val('<?= $first_approver['staffid'] ?>').trigger('change');
    <?php endif; ?>
    
    // Handle approver dropdown change
    $('#approver-select').on('change', function() {
        var selectedId = $(this).val();
        var selectedName = $(this).find('option:selected').data('name');
        
        // Save current input value before switching
        var currentId = $('#current-page-input').attr('data-approver-id');
        if (currentId) {
            approverPageInputs[currentId] = $('#current-page-input').val();
        }
        
        if (selectedId) {
            // Show the draggable box
            $('#current-approver-box').show();
            
            // Update box data
            $('#draggable-approver').attr('data-id', selectedId);
            $('#draggable-approver').attr('data-name', selectedName);
            $('#approver-name-display').text(selectedName);
            
            // Update page input data attribute
            $('#current-page-input').attr('data-approver-id', selectedId);
            
            // Update clear button data attribute
            $('#current-clear-btn').attr('data-approver-id', selectedId);
            
            // Load stored page input value for this approver
            if (approverPageInputs[selectedId]) {
                $('#current-page-input').val(approverPageInputs[selectedId]);
            } else {
                $('#current-page-input').val('');
            }
            
            // Refresh drag events
            if (window.refreshDragEvents) {
                window.refreshDragEvents();
            }
            
            console.log('✅ Selected approver:', selectedName, '(ID:', selectedId + ')');
        } else {
            // Hide box if no selection
            $('#current-approver-box').hide();
            $('#draggable-approver').attr('data-id', '');
            $('#draggable-approver').attr('data-name', '');
            $('#approver-name-display').text('');
            $('#current-page-input').val('').attr('data-approver-id', '');
            $('#current-clear-btn').attr('data-approver-id', '');
        }
    });
    
    // Sync page input changes
    $('#current-page-input').on('change keyup', function() {
        var approverId = $(this).attr('data-approver-id');
        if (approverId) {
            approverPageInputs[approverId] = $(this).val();
        }
    });
    
    // Make stored values accessible globally for save function
    window.getApproverPageInput = function(approverId) {
        return approverPageInputs[approverId] || '';
    };
    
    // Get common checkbox states (same for all approvers)
    window.getCommonCheckboxStates = function() {
        return {
            inc_app_name: $('#inc_app_name').is(':checked'),
            inc_time_stamp: $('#inc_time_stamp').is(':checked')
        };
    };
});

// Updated Save All Positions function
$('#saveAllPositions').on('click', function() {
  const $button = $(this);
  $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Saving...');
  
  const currentApproversOnScreen = new Set();
  const positionsByApprover = {};
  
  $('.sign-box:not([data-type="stamp"])').each(function() {
    const approver_id = $(this).data('approver_id');
    currentApproversOnScreen.add(approver_id);
    
    const x = parseFloat($(this).css('left'));
    const y = parseFloat($(this).css('top'));
    const page = $(this).data('page');
    
    if (!positionsByApprover[approver_id]) {
      positionsByApprover[approver_id] = [];
    }
    
    positionsByApprover[approver_id].push({ x, y, page });
  });

  const signaturePositions = [];
  
  // Get common checkbox states (same for all approvers)
  const commonCheckboxStates = typeof window.getCommonCheckboxStates === 'function' 
    ? window.getCommonCheckboxStates() 
    : { inc_app_name: false, inc_time_stamp: false };
  
  for (let approver_id in positionsByApprover) {
    let pagesInput = '';
    
    if (typeof window.getApproverPageInput === 'function') {
      pagesInput = window.getApproverPageInput(approver_id);
    } else {
      const currentDropdownId = $('#approver-select').val();
      if (currentDropdownId == approver_id) {
        pagesInput = $('#current-page-input').val().trim();
      }
    }
    
    // Use common checkbox states for all approvers
    signaturePositions.push({
      approver_id: approver_id,
      coords: positionsByApprover[approver_id],
      pages: pagesInput,
      inc_app_name: commonCheckboxStates.inc_app_name ? '1' : '0',
      inc_time_stamp: commonCheckboxStates.inc_time_stamp ? '1' : '0'
    });
  }
  
  removedApprovers.forEach(approver_id => {
    if (!currentApproversOnScreen.has(approver_id)) {
      signaturePositions.push({
        approver_id: approver_id,
        coords: [],
        pages: '',
        inc_app_name: commonCheckboxStates.inc_app_name ? '1' : '0',
        inc_time_stamp: commonCheckboxStates.inc_time_stamp ? '1' : '0'
      });
    }
  });

  const stampBoxes = [];
  let hasStampBoxes = false;
  $('.sign-box[data-type="stamp"]').each(function() {
    hasStampBoxes = true;
    const x = parseFloat($(this).css('left'));
    const y = parseFloat($(this).css('top'));
    const page = $(this).data('page');
    stampBoxes.push({ x, y, page });
  });
  
  const stampPages = $('.page-input[data-approver-id="company_stamp"]').val().trim();
  
  if (removedStamp.isRemoved && stampBoxes.length === 0) {
    hasStampBoxes = true;
  }

  $.ajax({
    url: "<?= admin_url('contracts/save_all_placeholders') ?>",
    type: "POST",
    data: {
      type:"<?= $contract->type ?>",
      contract_id: "<?= $contract->id ?>",
      signature_positions: JSON.stringify(signaturePositions),
      stamp_positions: JSON.stringify(stampBoxes),
      stamp_pages: stampPages,
      has_stamp_boxes: hasStampBoxes,
      stamp_removed: removedStamp.isRemoved
    },
    success: function(response) {
      try {
        const data = JSON.parse(response);
        alert_float('success', data.message || 'All positions saved successfully.');
      } catch (e) {
        alert_float('success', 'All positions saved successfully.');
      }
      
      removedApprovers.clear();
      removedStamp.isRemoved = false;

      setTimeout(function() {
        window.location.href = window.location.pathname + '?tab=tab_contract';
      }, 800);
    },
    error: function(xhr) {
      alert_float('danger', 'Error saving positions: ' + xhr.statusText);
      console.error(xhr.responseText);
      $button.prop('disabled', false).html('<i class="fa fa-save"></i> Save All Positions');
    }
  });
});

// ✅ Clear approver/stamp placeholders
document.querySelectorAll('.clear-approver').forEach(btn => {
  btn.addEventListener('click', function(e) {
    e.stopPropagation();
    const approverId = this.dataset.approverId;
    
    const boxesToRemove = document.querySelectorAll(`.sign-box[data-approver_id="${approverId}"]`);
    const count = boxesToRemove.length;
    
    if (count === 0) {
      alert('No placeholders found for this approver.');
      return;
    }
    
    if (confirm(`Clear all ${count} placeholder(s) for this ${approverId === 'company_stamp' ? 'stamp' : 'approver'}?`)) {
      boxesToRemove.forEach(box => box.remove());
      
      if (approverId === 'company_stamp') {
        removedStamp.isRemoved = true;
      } else {
        removedApprovers.add(approverId);
      }
      
      const pageInput = document.querySelector(`.page-input[data-approver-id="${approverId}"]`);
      if (pageInput) {
        pageInput.value = '';
      }
      
      alert_float('success', `${count} placeholder(s) cleared. Remember to save changes.`);
    }
  });
});


</script>
<div class="modal fade" id="signatureModal" tabindex="-1" role="dialog" aria-labelledby="signatureModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="signatureModalLabel">Sign Contract</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#uploadTab" aria-controls="uploadTab" role="tab" data-toggle="tab">
              Upload
            </a>
          </li>
          <li role="presentation">
            <a href="#drawTab" aria-controls="drawTab" role="tab" data-toggle="tab">
              Draw
            </a>
          </li>
          <li role="presentation">
            <a href="#typeTab" aria-controls="typeTab" role="tab" data-toggle="tab">
              Type
            </a>
          </li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content" style="margin-top:15px;">

          <!-- Upload Signature Tab -->
          <div role="tabpanel" class="tab-pane active" id="uploadTab">
            <div class="form-group">
              <label>Upload Signature Image</label>
              <input type="file" id="sig_upload" accept="image/png,image/jpeg,image/jpg" class="form-control">
              <small class="text-muted">Accepted formats: PNG, JPG, JPEG</small>
            </div>
            <div id="upload_preview" class="text-center mt-3" style="display:none;">
              <img id="upload_preview_img" src="" style="max-width:100%; max-height:200px; border:1px solid #ddd; padding:10px;">
            </div>
            <div class="text-center mt-3">
              <button id="save_upload" class="btn btn-success">
                <i class="fa fa-check"></i> Submit Signature
              </button>
            </div>
          </div>

          <!-- Draw Signature Tab -->
          <div role="tabpanel" class="tab-pane" id="drawTab">
            <canvas id="sig_pad" width="600" height="200" style="border:1px solid #ddd; display:block; margin:auto; background:#fff;"></canvas>
            <div class="text-center mt-3">
              <button id="clear_draw" class="btn btn-secondary btn-sm">
                <i class="fa fa-eraser"></i> Clear
              </button>
              <button id="save_draw" class="btn btn-success btn-sm">
                <i class="fa fa-check"></i> Submit Signature
              </button>
            </div>
          </div>

          <!-- Type Signature Tab -->
          <div role="tabpanel" class="tab-pane" id="typeTab">
            <div class="form-group">
              <label for="signature_name">Name</label>
              <input type="text" id="signature_name" class="form-control" placeholder="Enter your name" value="">
            </div>
            
            <!-- Font Style Selection -->
            <div class="form-group">
              <label>Font Style</label>
              <div class="row">
                <div class="col-sm-4 col-xs-6 mb-3">
                  <div class="signature-font-option" data-font="'Dancing Script', cursive" style="cursor:pointer; padding:20px; border:2px solid #ddd; border-radius:5px; text-align:center; transition: all 0.3s;">
                    <div class="signature-preview" style="font-family: 'Dancing Script', cursive; font-size: 28px; color: #1e3a8a;">
                      Techies
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xs-6 mb-3">
                  <div class="signature-font-option" data-font="'Great Vibes', cursive" style="cursor:pointer; padding:20px; border:2px solid #ddd; border-radius:5px; text-align:center; transition: all 0.3s;">
                    <div class="signature-preview" style="font-family: 'Great Vibes', cursive; font-size: 32px; color: #1e3a8a;">
                      Techies
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xs-6 mb-3">
                  <div class="signature-font-option" data-font="'Allura', cursive" style="cursor:pointer; padding:20px; border:2px solid #ddd; border-radius:5px; text-align:center; transition: all 0.3s;">
                    <div class="signature-preview" style="font-family: 'Allura', cursive; font-size: 32px; color: #1e3a8a;">
                      Techies
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xs-6 mb-3">
                  <div class="signature-font-option" data-font="'Pacifico', cursive" style="cursor:pointer; padding:20px; border:2px solid #ddd; border-radius:5px; text-align:center; transition: all 0.3s;">
                    <div class="signature-preview" style="font-family: 'Pacifico', cursive; font-size: 28px; color: #1e3a8a;">
                      Techies
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xs-6 mb-3">
                  <div class="signature-font-option" data-font="'Sacramento', cursive" style="cursor:pointer; padding:20px; border:2px solid #ddd; border-radius:5px; text-align:center; transition: all 0.3s;">
                    <div class="signature-preview" style="font-family: 'Sacramento', cursive; font-size: 32px; color: #1e3a8a;">
                      Techies
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xs-6 mb-3">
                  <div class="signature-font-option" data-font="'Tangerine', cursive" style="cursor:pointer; padding:20px; border:2px solid #ddd; border-radius:5px; text-align:center; transition: all 0.3s;">
                    <div class="signature-preview" style="font-family: 'Tangerine', cursive; font-size: 38px; color: #1e3a8a;">
                      Techies
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Live Preview -->
            <div class="form-group">
              <label>Preview</label>
              <div id="typed_signature_preview" style="padding:30px; border:1px solid #ddd; border-radius:5px; text-align:center; background:#f9f9f9; min-height:150px; display:flex; align-items:center; justify-content:center;">
                <span id="preview_text" style="font-family: 'Dancing Script', cursive; font-size: 48px; color: #1e3a8a;">
                  Your Signature
                </span>
              </div>
            </div>

            <!-- Font Size Slider -->
            <div class="form-group">
              <label>Size: <span id="font_size_display">58</span>px</label>
              <input type="range" id="font_size_slider" class="form-control-range" min="50" max="120" value="58" style="width:100%;">
            </div>

            <div class="text-center mt-3">
              <button id="save_typed" class="btn btn-success">
                <i class="fa fa-check"></i> Apply Signature
              </button>
            </div>
          </div>

          <!-- Upload Signature Tab -->
          <div role="tabpanel" class="tab-pane" id="uploadTab">
            <div class="form-group">
              <label>Upload Signature Image</label>
              <input type="file" id="sig_upload" accept="image/png,image/jpeg,image/jpg" class="form-control">
              <small class="text-muted">Accepted formats: PNG, JPG, JPEG</small>
            </div>
            <div id="upload_preview" class="text-center mt-3" style="display:none;">
              <img id="upload_preview_img" src="" style="max-width:100%; max-height:200px; border:1px solid #ddd; padding:10px;">
            </div>
            <div class="text-center mt-3">
              <button id="save_upload" class="btn btn-success">
                <i class="fa fa-check"></i> Submit Signature
              </button>
            </div>
          </div>

        </div>

        
      </div>
    </div>
  </div>
</div>
</body>
</html>



<!-- Load Google Fonts for Signature Styles -->
<link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@700&family=Great+Vibes&family=Allura&family=Pacifico&family=Sacramento&family=Tangerine:wght@700&display=swap" rel="stylesheet">

<!-- Signature Pad Script -->
<script src="https://cdn.jsdelivr.net/npm/signature_pad@4.1.6/dist/signature_pad.umd.min.js"></script>

<script>
$(document).ready(function() {
  var pad = null;
  var selectedFont = "'Dancing Script', cursive";
  var selectedFontSize = 48;

  // Initialize staff name
  var staffName = "<?= get_staff_full_name(get_staff_user_id()) ?>";
  $('#signature_name').val(staffName);
  updateTypedPreview();

  // Initialize Signature Pad when Draw tab shown
  $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
    var target = $(e.target).attr("href");
    if (target === '#drawTab' && !pad) {
      pad = new SignaturePad(document.getElementById('sig_pad'), {
        backgroundColor: 'rgb(255, 255, 255)'
      });
      $('#clear_draw').click(function() { pad.clear(); });
    }
  });

  // Font selection
  $('.signature-font-option').click(function() {
    $('.signature-font-option').css({
      'border': '2px solid #ddd',
      'box-shadow': 'none'
    });
    $(this).css({
      'border': '2px solid #007bff',
      'box-shadow': '0 0 10px rgba(0,123,255,0.3)'
    });
    selectedFont = $(this).data('font');
    updateTypedPreview();
  });

  // Font size slider
  $('#font_size_slider').on('input', function() {
    selectedFontSize = $(this).val();
    $('#font_size_display').text(selectedFontSize);
    updateTypedPreview();
  });

  // Update typed signature name
  $('#signature_name').on('input', function() {
    updateTypedPreview();
  });

  // Update preview function
  function updateTypedPreview() {
    var name = $('#signature_name').val() || 'Your Signature';
    $('#preview_text').text(name).css({
      'font-family': selectedFont,
      'font-size': selectedFontSize + 'px',
      'color': '#1e3a8a'
    });
    
    // Update all font preview samples with current name
    $('.signature-font-option').each(function() {
      $(this).find('.signature-preview').text(name);
    });
  }

  // Upload preview
  $('#sig_upload').change(function() {
    var file = this.files[0];
    if (file) {
      var reader = new FileReader();
      reader.onload = function(e) {
        $('#upload_preview_img').attr('src', e.target.result);
        $('#upload_preview').show();
      };
      reader.readAsDataURL(file);
    }
  });

  // Save functions
  function saveSignature(source) {
  

    var formData = new FormData();
    formData.append('type', '<?= $contract->type ?>');
    formData.append('contract_id', '<?= $contract->id ?>');
    formData.append('csrf_token_name', csrfData.hash);

    if (source === 'upload') {
      var file = $('#sig_upload')[0].files[0];
      if (!file) {
        alert_float('warning', 'Please upload a signature image.');
        return;
      }
      formData.append('file', file);
    } 
    else if (source === 'draw') {
      if (pad && !pad.isEmpty()) {
        formData.append('signature', pad.toDataURL());
      } else {
        alert_float('warning', 'Please draw your signature.');
        return;
      }
    } 
    else if (source === 'type') {
      var name = $('#signature_name').val().trim();
      if (!name) {
        alert_float('warning', 'Please enter your name.');
        return;
      }
      
      // Generate signature image from typed text
      var canvas = document.createElement('canvas');
      canvas.width = 1200;
      canvas.height = 400;
      var ctx = canvas.getContext('2d');
      
      // White background
      ctx.fillStyle = '#ffffff';
      ctx.fillRect(0, 0, canvas.width, canvas.height);
      
      // Draw signature text with larger scale
      ctx.fillStyle = '#1e3a8a';
      var scaledFontSize = selectedFontSize * 2; // Double the size for better quality
      ctx.font = scaledFontSize + 'px ' + selectedFont.replace(/'/g, '');
      ctx.textAlign = 'center';
      ctx.textBaseline = 'middle';
      ctx.fillText(name, canvas.width / 2, canvas.height / 2);
      
      formData.append('signature', canvas.toDataURL());
    }

    $.ajax({
      url: "<?= admin_url('contracts/save_signature') ?>",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        try {
          var data = JSON.parse(response);
          alert_float('success', data.message || 'Signature saved successfully.');
        } catch(e) {
          alert_float('success', 'Signature saved successfully.');
        }

        $('#signatureModal').modal('hide');
        
        setTimeout(function() {
          window.location.href = window.location.pathname + '?tab=tab_contract';
        }, 1200);
      },
      error: function(xhr) {
        alert_float('danger', 'Error saving signature: ' + xhr.statusText);
        console.error(xhr.responseText);
      }
    });
  }

  $('#save_upload').click(function() { saveSignature('upload'); });
  $('#save_draw').click(function() { saveSignature('draw'); });
  $('#save_typed').click(function() { saveSignature('type'); });

  // Select first font by default
  $('.signature-font-option').first().click();
});
</script>

<style>
.signature-font-option {
  transition: all 0.3s ease;
}

.signature-font-option:hover {
  border-color: #007bff !important;
  box-shadow: 0 0 5px rgba(0,123,255,0.2);
  transform: translateY(-2px);
}

#typed_signature_preview {
  transition: all 0.3s ease;
}

.nav-tabs > li.active > a {
  border-bottom: 3px solid #007bff !important;
}

.mb-3 {
  margin-bottom: 15px;
}

.mt-3 {
  margin-top: 15px;
}

/* Custom range slider styling */
#font_size_slider {
  -webkit-appearance: none;
  width: 100%;
  height: 6px;
  border-radius: 5px;
  background: #ddd;
  outline: none;
  margin-top: 10px;
}

#font_size_slider::-webkit-slider-thumb {
  -webkit-appearance: none;
  appearance: none;
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #007bff;
  cursor: pointer;
}

#font_size_slider::-moz-range-thumb {
  width: 20px;
  height: 20px;
  border-radius: 50%;
  background: #007bff;
  cursor: pointer;
}
</style>
<script>
$(document).ready(function() {
    // Toggle rejection reason textarea when checkbox is checked
    $('#rejection-checkbox').change(function() {
        if ($(this).is(':checked')) {
            $('#rejection-reason-group').slideDown();
            $('#rejection-reason').prop('disabled', false);
            $('#rejection-save-group').slideDown();
        } else {
            $('#rejection-reason-group').slideUp();
            $('#rejection-reason').prop('disabled', true).val('');
            $('#rejection-save-group').slideUp();
        }
    });
    
    // Handle rejection submission
    $('#save-rejection').click(function() {
        var reason = $('#rejection-reason').val().trim();
        
        if (!reason) {
            alert_float('warning', 'Please provide a reason for rejection.');
            return;
        }
        
        if (!confirm('Are you sure you want to reject this contract? This action cannot be undone.')) {
            return;
        }
        
        var formData = new FormData();
        formData.append('contract_id', '<?= $contract->id ?>');
        formData.append('rejected_reason', reason);
        formData.append('csrf_token_name', csrfData.hash);
        
        // Disable button to prevent double submission
        $('#save-rejection').prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Submitting...');
        
        $.ajax({
            url: "<?= admin_url('contracts/save_rejection') ?>",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    var data = JSON.parse(response);
                    if (data.success) {
                        alert_float('success', data.message || 'Contract rejection saved successfully.');
                        
                        // Redirect after success
                        setTimeout(function() {
                            window.location.href = window.location.pathname + '?tab=tab_contract';
                        }, 1200);
                    } else {
                        alert_float('danger', data.message || 'Error saving rejection.');
                        $('#save-rejection').prop('disabled', false).html('<i class="fa fa-times"></i> Submit Rejection');
                    }
                } catch(e) {
                    alert_float('danger', 'Error processing response.');
                    $('#save-rejection').prop('disabled', false).html('<i class="fa fa-times"></i> Submit Rejection');
                }
            },
            error: function(xhr) {
                alert_float('danger', 'Error saving rejection: ' + xhr.statusText);
                console.error(xhr.responseText);
                $('#save-rejection').prop('disabled', false).html('<i class="fa fa-times"></i> Submit Rejection');
            }
        });
    });
});
</script>
</body>
</html>
<div class="modal" id="new-version-modal" tabindex="-1" role="dialog" style="z-index: 9999999999999;">
  <div class="modal-dialog mtop20" role="document" >
    <div class="modal-content">
      <?php //echo form_open(admin_url('contracts/update_contractfile_version'),['id'=>'receivable-payment-form']); ?>
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
            <div class="alert alert-info">If you click the 'save as new version' checkbox, a new version of contract will be created. Otherwise the latest contract will be replaced .</div>
         </div>
         </div>
         <hr>
         <br>
      </div>
      <!-- <div class="modal-body">
       
      </div> -->
      <div class="modal-footer">
        <button type="button"  onclick="update_sharepoint(<?php echo $contract->id; ?>); return false;" class="btn btn-warning" data-dismiss="modal">GO</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
      <?php //echo form_close(); ?>

    </div>
  </div>
</div>


<!-- Stamp Modal -->
<div class="modal fade" id="stampModal" tabindex="-1" role="dialog" aria-labelledby="stampModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      
      <div class="modal-header">
        <h5 class="modal-title" id="stampModalLabel">
          <i class="fa fa-certificate"></i> Apply Company Stamp
        </h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span>&times;</span>
        </button>
      </div>
      
      <div class="modal-body text-center">
        <div class="alert alert-info">
          <i class="fa fa-info-circle"></i> 
          The company stamp will be applied to the positions you've set in the PDF.
        </div>
        
        <!-- Display Stamp Preview -->
        <div style="padding: 20px; background-color: #f9f9f9; border: 2px dashed #ccc; border-radius: 5px; margin: 20px 0;">
          <p style="margin-bottom: 10px; font-weight: bold;">Stamp Preview:</p>
          <img src="<?= base_url('uploads/company/signature.png') ?>" 
               alt="Company Stamp" 
               style="max-width: 200px; max-height: 200px; border: 1px solid #ddd; padding: 10px; background: white;">
        </div>
        
        <div class="alert alert-warning">
          <i class="fa fa-exclamation-triangle"></i>
          <strong>Warning:</strong> This action will permanently apply the stamp to the contract PDF.
        </div>
      </div>
      
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">
          <i class="fa fa-times"></i> Cancel
        </button>
        <button type="button" id="apply_stamp" class="btn btn-success">
          <i class="fa fa-check"></i> Apply Stamp Now
        </button>
      </div>
      
    </div>
  </div>
</div>

<script>
$(document).ready(function() {
  
  // Apply Stamp
  $('#apply_stamp').click(function() {
    var $button = $(this);
    $button.prop('disabled', true).html('<i class="fa fa-spinner fa-spin"></i> Applying...');
    
    var formData = new FormData();
    formData.append('contract_id', '<?= $contract->id ?>');
    formData.append('csrf_token_name', csrfData.hash);

    $.ajax({
      url: "<?= admin_url('contracts/save_stamp') ?>",
      type: "POST",
      data: formData,
      processData: false,
      contentType: false,
      success: function(response) {
        try {
          var data = JSON.parse(response);
          
          if (data.success) {
            alert_float('success', data.message || 'Stamp applied successfully.');
            $('#stampModal').modal('hide');
            
            // Redirect to tab
            setTimeout(function() {
              window.location.href = window.location.pathname + '?tab=tab_contract';
            }, 1200);
          } else {
            alert_float('danger', data.error || 'Error applying stamp.');
            $button.prop('disabled', false).html('<i class="fa fa-check"></i> Apply Stamp Now');
          }
        } catch(e) {
          alert_float('danger', 'Error processing response.');
          console.error(e);
          $button.prop('disabled', false).html('<i class="fa fa-check"></i> Apply Stamp Now');
        }
      },
      error: function(xhr) {
        alert_float('danger', 'Error applying stamp: ' + xhr.statusText);
        console.error(xhr.responseText);
        $button.prop('disabled', false).html('<i class="fa fa-check"></i> Apply Stamp Now');
      }
    });
  });
  
});
</script>
<script>
$(document).ready(function() {
    // ✅ Store page input values for all approvers
    var approverPageInputs = {};
    
    // ✅ Load existing page inputs from hidden storage
    <?php foreach ($contract_approvals as $a): ?>
        approverPageInputs['<?= $a['staffid'] ?>'] = '';
    <?php endforeach; ?>
    
    // ✅ Handle approver dropdown change
    $('#approver-select').on('change', function() {
        var selectedId = $(this).val();
        var selectedName = $(this).find('option:selected').data('name');
        
        // Save current input value before switching
        var currentId = $('#current-page-input').attr('data-approver-id');
        if (currentId) {
            approverPageInputs[currentId] = $('#current-page-input').val();
            $('#current-page-input').data('approver-' + currentId, $('#current-page-input').val());
        }
        
        if (selectedId) {
            // Show the draggable box
            $('#current-approver-box').show();
            
            // Update box data
            $('#draggable-approver').attr('data-id', selectedId);
            $('#draggable-approver').attr('data-name', selectedName);
            $('#approver-name-display').text(selectedName);
            
            // Update page input data attribute
            $('#current-page-input').attr('data-approver-id', selectedId);
            
            // Update clear button data attribute
            $('#current-clear-btn').attr('data-approver-id', selectedId);
            
            // Load stored page input value for this approver
            if (approverPageInputs[selectedId]) {
                $('#current-page-input').val(approverPageInputs[selectedId]);
            } else {
                $('#current-page-input').val('');
            }
            
            // Refresh drag events
            if (window.refreshDragEvents) {
                window.refreshDragEvents();
            }
            
            console.log('✅ Selected approver:', selectedName, '(ID:', selectedId + ')');
        } else {
            // Hide box if no selection
            $('#current-approver-box').hide();
            $('#draggable-approver').attr('data-id', '');
            $('#draggable-approver').attr('data-name', '');
            $('#approver-name-display').text('');
            $('#current-page-input').val('').attr('data-approver-id', '');
            $('#current-clear-btn').attr('data-approver-id', '');
        }
    });
    
    // ✅ Sync page input changes
    $('#current-page-input').on('change keyup', function() {
        var approverId = $(this).attr('data-approver-id');
        if (approverId) {
            approverPageInputs[approverId] = $(this).val();
            $(this).data('approver-' + approverId, $(this).val());
        }
    });
    
    // ✅ Make stored values accessible globally for save function
    window.getApproverPageInput = function(approverId) {
        return approverPageInputs[approverId] || '';
    };
});
</script>
