<?php init_head(); ?>
<style>
#ribbon_project_<?php echo $project->id; ?> span::before {
  border-top: 3px solid <?php echo $project_status['color']; ?>;
  border-left: 3px solid <?php echo $project_status['color']; ?>;
}
</style>
<div id="wrapper">
  <?php echo form_hidden('project_id',$project->id) ?>
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s project-top-panel panel-full">
          <div class="panel-body _buttons">
            <div class="row">
              <div class="col-md-8 project-heading">
                <h3 class="hide project-name"><?php echo $project->name; ?></h3>
                <div id="project_view_name">
                 <select class="selectpicker" id="project_top" data-width="fit"<?php if(count($other_projects) > 4){ ?> data-live-search="true" <?php } ?>>
                   <option value="<?php echo $project->id; ?>" selected><?php echo $project->name; ?></option>
                   <?php foreach($other_projects as $op){ ?>
                   <option value="<?php echo $op['id']; ?>" data-subtext="<?php echo _l($op['case_type']); ?>">#<?php echo $op['id']; ?> - <?php echo $op['name']; ?></option>
                   <?php } ?>
                 </select>
               </div>
             </div>
             <div class="col-md-4 text-right hide">
              <?php if(has_permission('tasks','','create')){ ?>
              <a href="#" onclick="new_template_task_from_relation(undefined,'casetemplates',<?php echo $project->id; ?>); return false;" class="btn btn-info"><?php echo _l('new_task'); ?></a>
              <?php } ?>
              <?php
              $invoice_func = 'pre_invoice_project';
              ?>
              
              <?php
              $project_pin_tooltip = _l('pin_matter');
             
              ?>
              <div class="btn-group">
                <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                  <?php echo _l('more'); ?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-right width200 project-actions">
                  <li>
                   <a href="<?php echo admin_url('casediary/pin_action/'.$project->id); ?>">
                    <?php echo $project_pin_tooltip; ?>
                  </a>
                </li>
                <?php if(has_permission('casediary','','edit')){ ?>
                <li>
                  <a href="<?php echo admin_url('casediary/casediary/'.$project->id); ?>">
                    <?php echo _l('edit_matter'); ?>
                  </a>
                </li>
                <?php } ?>
                <?php if(has_permission('casediary','','create')){ ?>
                <li>
                  <a href="#" onclick="copy_project(); return false;">
                    <?php echo _l('copy_matter'); ?>
                  </a>
                </li>
                <?php } ?>
                <?php if(has_permission('casediary','','create') || has_permission('casediary','','edit')){ ?>
                <li class="divider"></li>
                <?php foreach($statuses as $status){
                  if($status['id'] == $project->status){continue;}
                  ?>
                  <li>
                    <a href="#" onclick="project_mark_as_modal(<?php echo $status['id']; ?>,<?php echo $project->id; ?>); return false;"><?php echo _l('project_mark_as',$status['name']); ?></a>
                  </li>
                  <?php } ?>
                  <?php } ?>
                  <li class="divider"></li>
                  <?php if(has_permission('casediary','','create')){ ?>
                  <li>
                   <a href="<?php echo admin_url('casediary/export_project_data/'.$project->id); ?>" target="_blank"><?php echo _l('export_matter_data'); ?></a>
                 </li>
                 <?php } ?>
                 <?php if(is_admin()){ ?>
                 <!-- <li>
                  <a href="<?php echo admin_url('casediary/view_project_as_client/'.$project->id .'/'.$project->clientid); ?>" target="_blank"><?php echo _l('project_view_as_client'); ?></a>
                </li> -->
                <?php } ?>
                <?php if(has_permission('casediary','','delete')){ ?>
                <li>
                  <a href="<?php echo admin_url('casetemplates/delete/'.$project->id); ?>" class="_delete">
                    <span class="text-danger"><?php echo _l('delete_matter'); ?></span>
                  </a>
                </li>
                <?php } ?>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="panel_s project-menu-panel">
      <div class="panel-body">
        <?php echo '<div class="ribbon project-status-ribbon-'.$project->status.'" id="ribbon_project_'.$project->id.'"><span style="background:'.$project_status['color'].'">'.$project_status['name'].'</span></div>'; ?>
        <?php $this->load->view('admin/casetemplates/project_tabs'); ?>
      </div>

    </div>
    
    <div class="panel_s">
      <div class="panel-body">
        <?php echo $group_view; ?>
      </div>
    </div>
  </div>
</div>
</div>
</div>
</div>
</div>
<?php if(isset($discussion)){
  echo form_hidden('discussion_id',$discussion->id);
  echo form_hidden('discussion_user_profile_image_url',$discussion_user_profile_image_url);
  echo form_hidden('current_user_is_admin',$current_user_is_admin);
}
echo form_hidden('project_percent',$percent);
?>
<div id="invoice_project"></div>
<div id="pre_invoice_project"></div>
<?php init_tail(); ?>
<?php $discussion_lang = get_project_discussions_language_array(); ?>
<?php echo app_script('assets/js','casetemplates.js'); ?>
<!-- For invoices table -->
<script>
  taskid = '<?php echo $this->input->get('taskid'); ?>';
</script>
<script>
  var gantt_data = {};
  <?php if(isset($gantt_data)){ ?>
    gantt_data = <?php echo json_encode($gantt_data); ?>;
    <?php } ?>
    var discussion_id = $('input[name="discussion_id"]').val();
    var discussion_user_profile_image_url = $('input[name="discussion_user_profile_image_url"]').val();
    var current_user_is_admin = $('input[name="current_user_is_admin"]').val();
    var project_id = $('input[name="project_id"]').val();
    $('input[name="case_id"]').val(project_id);
    if(typeof(discussion_id) != 'undefined'){
      discussion_comments('#discussion-comments',discussion_id,'regular');
    }
    $(function(){
     
  });

    function discussion_comments(selector,discussion_id,discussion_type){
     $(selector).comments({
       roundProfilePictures: true,
       textareaRows: 4,
       textareaRowsOnFocus: 6,
       profilePictureURL:discussion_user_profile_image_url,
       enableUpvoting: false,
       enableAttachments:true,
       popularText:'',
       enableDeletingCommentWithReplies:false,
       textareaPlaceholderText:"<?php echo $discussion_lang['discussion_add_comment']; ?>",
       newestText:"<?php echo $discussion_lang['discussion_newest']; ?>",
       oldestText:"<?php echo $discussion_lang['discussion_oldest']; ?>",
       attachmentsText:"<?php echo $discussion_lang['discussion_attachments']; ?>",
       sendText:"<?php echo $discussion_lang['discussion_send']; ?>",
       replyText:"<?php echo $discussion_lang['discussion_reply']; ?>",
       editText:"<?php echo $discussion_lang['discussion_edit']; ?>",
       editedText:"<?php echo $discussion_lang['discussion_edited']; ?>",
       youText:"<?php echo $discussion_lang['discussion_you']; ?>",
       saveText:"<?php echo $discussion_lang['discussion_save']; ?>",
       deleteText:"<?php echo $discussion_lang['discussion_delete']; ?>",
       viewAllRepliesText:"<?php echo $discussion_lang['discussion_view_all_replies'] . ' (__replyCount__)'; ?>",
       hideRepliesText:"<?php echo $discussion_lang['discussion_hide_replies']; ?>",
       noCommentsText:"<?php echo $discussion_lang['discussion_no_comments']; ?>",
       noAttachmentsText:"<?php echo $discussion_lang['discussion_no_attachments']; ?>",
       attachmentDropText:"<?php echo $discussion_lang['discussion_attachments_drop']; ?>",
       currentUserIsAdmin:current_user_is_admin,
       getComments: function(success, error) {
         $.get(admin_url + 'casetemplates/get_discussion_comments/'+discussion_id+'/'+discussion_type,function(response){
           success(response);
         },'json');
       },
       postComment: function(commentJSON, success, error) {
         $.ajax({
           type: 'post',
           url: admin_url + 'casetemplates/add_discussion_comment/'+discussion_id+'/'+discussion_type,
           data: commentJSON,
           success: function(comment) {
             comment = JSON.parse(comment);
             success(comment)
           },
           error: error
         });
       },
       putComment: function(commentJSON, success, error) {
         $.ajax({
           type: 'post',
           url: admin_url + 'casetemplates/update_discussion_comment',
           data: commentJSON,
           success: function(comment) {
             comment = JSON.parse(comment);
             success(comment)
           },
           error: error
         });
       },
       deleteComment: function(commentJSON, success, error) {
         $.ajax({
           type: 'post',
           url: admin_url + 'casetemplates/delete_discussion_comment/'+commentJSON.id,
           success: success,
           error: error
         });
       },
       timeFormatter: function(time) {
         return moment(time).fromNow();
       },
       uploadAttachments: function(commentArray, success, error) {
         var responses = 0;
         var successfulUploads = [];
         var serverResponded = function() {
           responses++;
             // Check if all requests have finished
             if(responses == commentArray.length) {
                 // Case: all failed
                 if(successfulUploads.length == 0) {
                   error();
                 // Case: some succeeded
               } else {
                 successfulUploads = JSON.parse(successfulUploads);
                 success(successfulUploads)
               }
             }
           }
           $(commentArray).each(function(index, commentJSON) {
             // Create form data
             var formData = new FormData();
             if(commentJSON.file.size && commentJSON.file.size > max_php_ini_upload_size_bytes){
              alert_float('danger',"<?php echo _l("file_exceeds_max_filesize"); ?>");
              serverResponded();
            } else {
             $(Object.keys(commentJSON)).each(function(index, key) {
               var value = commentJSON[key];
               if(value) formData.append(key, value);
             });

             if (typeof(csrfData) !== 'undefined') {
                formData.append(csrfData['token_name'], csrfData['hash']);
             }
             $.ajax({
               url: admin_url + 'casetemplates/add_discussion_comment/'+discussion_id+'/'+discussion_type,
               type: 'POST',
               data: formData,
               cache: false,
               contentType: false,
               processData: false,
               success: function(commentJSON) {
                 successfulUploads.push(commentJSON);
                 serverResponded();
               },
               error: function(data) {
                var error = JSON.parse(data.responseText);
                alert_float('danger',error.message);
                serverResponded();
              },
            });
           }
         });
         }
       });
}


</script>
<script type="text/javascript">
  /*_validate_form($('#case-form'),{file_no:'required',case_number:'required',case_title:'required'},$('#case-form'));*/

  function init_hearing(hearingid=''){
   
    $('#hearing').modal('show');
    $('#hearing .edit-title').addClass('hide');

  }


  $('#btn_add_hearing').click(function(){
    $('.edit_hearing').hide();
  });

  function show_edit_form(formDivID){
    $('#demo').removeClass('in');
    $('#demo').attr("aria-expanded","false");
    $('.edit_hearing').hide();
    $(formDivID).show();
  }

  function setHearingId(hearingId){
    $('#hid_hearing_id').val(hearingId);
  }

</script>
</body>
</html>
<div id="_project_hearing"></div>

<script type="text/javascript">
  
    var HearingsServerParams = {
         "report-to": "[name='report-to']",
         "report-from": "[name='report-from']",
    };
    var allhearingsTable = $('.table-all-hearings');
    var headers_hearings = $('.table-all-hearings').find('th');
    var not_sortable_casediary = (headers_hearings.length - 1);
    var project_id = '<?=$project->id?>';
    var tAPI = initDataTable('.table-case-hearings', admin_url+'casetemplates/casehearings/'+project_id, [not_sortable_casediary], [not_sortable_casediary], HearingsServerParams,[1,'ASC']);
   /* $.each(HearingsServerParams, function(i, obj) {
        $('select' + obj).on('change', function() {
            allhearingsTable.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
     });*/


   function delete_hearing_attachment(wrapper,id){
           if (confirm_delete()) {
              $.get(admin_url + 'casetemplates/delete_hearing_attachment/'+id,function(response){
                 if(response.success == true){
                  $(wrapper).parents('.contract-attachment-wrapper').remove();
                } else {
                  alert_float('danger',response.message);
                }
              },'json');
           }
          return false;
      }
    var ActServerParams = {};
    var caseactsTable = $('.table-case-acts');
    var headers_hearings = $('.table-case-acts').find('th');
    var not_sortable_casediary = (headers_hearings.length - 1);
    var case_id = '<?=$project->id?>';
    var tAPI = initDataTable('.table-case-acts', admin_url+'casetemplates/caseacts/'+case_id, [not_sortable_casediary], [not_sortable_casediary], ActServerParams,[1,'ASC']);  

    /* attach a submit handler to the form */
    $("#case-act-form").submit(function(event) {

        /* stop form from submitting normally */
        event.preventDefault();

        /* get the action attribute from the <form action=""> element */
        var $form = $(this),
        url = $form.attr('action');

        /* Send the data using post with element id name and name2*/
        var posting = $.post(url, {
          case_id: project_id,
          act: $('#act').val()
        });

        /* Alerts the results */
        posting.done(function(data) { 
          data = JSON.parse(data);
          alert_float('success',data.message);
          var allhearingsTable = $('.table-case-acts');
          allhearingsTable.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
        });
        posting.fail(function() {
          //alert('Failed');//$('#result').text('failed');

        });
        var allhearingsTable = $('.table-case-acts');
          allhearingsTable.DataTable().ajax.reload()
                .columns.adjust()
                .responsive.recalc();
      
    });

    /*$("body").on('click', '.new-case-task-to-milestone', function(e) {
        e.preventDefault();
        var milestone_id = $(this).parents('.milestone-column').data('col-status-id');
        new_task(admin_url + 'tasks/task?rel_type=casediary&rel_id=' + project_id + '&milestone_id=' + milestone_id);
        $('body [data-toggle="popover"]').popover('hide');
    });*/

    // Global function to edit note
function edit_scope(id) {
    var description = $("body").find('[data-note-edit-textarea="' + id + '"] textarea').val();
    if (description != '') {
        $.post(admin_url + 'casetemplates/edit_scope/' + id, {
            description: description
        }).done(function(response) {
            response = JSON.parse(response);
            if (response.success == true) {
                alert_float('success', response.message);
                $("body").find('[data-note-description="' + id + '"]').html(nl2br(description));
            }
        });
        toggle_edit_note(id);
    }

}

</script>