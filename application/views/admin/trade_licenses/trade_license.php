<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-4 left-column">
        <div class="panel_s">
          <div class="panel-body">
            <h4 class="no-margin"><?php echo $title; ?>
              <?php if(isset($contract) && has_permission('trade_licenses','','delete')){ ?>
              <small><a href="<?php echo admin_url('trade_licenses/delete/'.$contract->id); ?>" class="pull-right mleft5 text-danger _delete"><?php echo _l('delete'); ?></a></small>
              <?php } ?>
              <?php if(isset($contract) && has_permission('trade_licenses','','create')){ ?>
              <small><a href="<?php echo admin_url('trade_licenses/copy/'.$contract->id); ?>" class="pull-right hide"><?php echo _l('contract_copy'); ?></a></small>
              <?php } ?>
            </h4>
            <hr class="hr-panel-heading" />
            <?php echo form_open($this->uri->uri_string(),array('id'=>'contract-form')); ?>
            <div class="form-group">
              <div class="checkbox checkbox-primary no-mtop checkbox-inline">
                <input type="checkbox" id="trash" name="trash" data-toggle="tooltip" title="<?php echo _l('contract_trash_tooltip'); ?>" <?php if(isset($contract)){if($contract->trash == 1){echo 'checked';}}; ?>>
                <label for="trash"><?php echo _l('contract_trash'); ?></label>
              </div>
              <div class="checkbox checkbox-primary checkbox-inline">
                <input type="checkbox" name="not_visible_to_client" id="not_visible_to_client" <?php if(isset($contract)){if($contract->not_visible_to_client == 1){echo 'checked';}}; ?>>
                <label for="not_visible_to_client"><?php echo _l('contract_not_visible_to_client'); ?></label>
              </div>
            </div>
            <div class="form-group select-placeholder">
              <label for="clientid" class="control-label"><span class="text-danger">* </span><?php echo _l('contract_client_string'); ?></label>
              <select id="clientid" name="client" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                <?php $selected = (isset($contract) ? $contract->client : '');
                if($selected == ''){
                 $selected = (isset($customer_id) ? $customer_id: '');
               }
               if($selected != ''){
                $rel_data = get_relation_data('customer',$selected);
                $rel_val = get_relation_values($rel_data,'customer');
                echo '<option value="'.$rel_val['id'].'" selected>'.$rel_val['name'].'</option>';
              } ?>
            </select>
          </div>
          <?php $value = (isset($contract) ? $contract->license_no : ''); ?>
          <?php echo render_input('license_no','license_no',$value,'text',array('data-toggle'=>'tooltip','title'=>'license_tooltip')); ?>

          <?php $value = (isset($contract) ? $contract->cr_no : ''); ?>
          <?php echo render_input('cr_no','cr_no',$value,'text'); ?>

          <?php $value = (isset($contract) ? $contract->organization_nature : ''); ?>
          <?php echo render_input('organization_nature','organization_nature',$value,'text'); ?>

          <div class="form-group">
            <label for="contract_value"><?php echo _l('share_capital'); ?></label>
            <div class="input-group" data-toggle="tooltip" title="<?php echo _l('share_capital'); ?>">
              <input type="number" class="form-control" name="share_capital" value="<?php if(isset($contract)){echo $contract->share_capital; }?>">
              <div class="input-group-addon">
                <?php echo $base_currency->symbol; ?>
              </div>
            </div>
          </div>
           <div class="form-group">
            <label for="contract_value"><?php echo _l('license_cost'); ?></label>
            <div class="input-group" data-toggle="tooltip" title="<?php echo _l('license_cost'); ?>">
              <input type="number" class="form-control" name="license_cost" value="<?php if(isset($contract)){echo $contract->license_cost; }?>">
              <div class="input-group-addon">
                <?php echo $base_currency->symbol; ?>
              </div>
            </div>
          </div>
         <!--  <?php
          $selected = (isset($contract) ? $contract->contract_type : '');
          if(is_admin() || get_option('staff_members_create_inline_contract_types') == '1'){
           echo render_select_with_input_group('contract_type',$types,array('id','name'),'contract_type',$selected,'<a href="#" onclick="new_type();return false;"><i class="fa fa-plus"></i></a>');
         } else {
          echo render_select('contract_type',$types,array('id','name'),'contract_type',$selected);
        }
        ?> -->
        <div class="row">
          <div class="col-md-6">
            <?php $value = (isset($contract) ? _d($contract->issue_date) : _d(date('Y-m-d'))); ?>
            <?php echo render_date_input('issue_date','issue_date',$value); ?>
          </div>
          <div class="col-md-6">
            <?php $value = (isset($contract) ? _d($contract->expiry_date) : ''); ?>
            <?php echo render_date_input('expiry_date','expiry_date',$value); ?>
          </div>
        </div>


        <?php $value = (isset($contract) ? $contract->share_holding_details : ''); ?>
        <?php echo render_textarea('share_holding_details','share_holding_details',$value,array('rows'=>4)); ?>

        <?php $value = (isset($contract) ? $contract->manager_details : ''); ?>
        <?php echo render_textarea('manager_details','manager_details',$value,array('rows'=>2)); ?>


         <?php $value = (isset($contract) ? $contract->board_directors : ''); ?>
         <?php echo render_textarea('board_directors','board_directors',$value,array('rows'=>2)); ?>


         <?php $value = (isset($contract) ? $contract->license_activity : ''); ?>
         <?php echo render_textarea('license_activity','license_activity',$value,array('rows'=>3)); ?>






        <?php $rel_id = (isset($contract) ? $contract->id : false); ?>
        <?php echo render_custom_fields('contracts',$rel_id); ?>
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
        <h4 class="no-margin"><?php echo _l('trade_license_edit_overview'); ?></h4>
        <hr class="hr-panel-heading" />
        <?php if($contract->trash > 0){
          echo '<div class="ribbon default"><span>'._l('contract_trash').'</span></div>';
        } ?>
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active">
            <a href="#tab_content" aria-controls="tab_content" role="tab" data-toggle="tab">
              <?php echo _l('content'); ?>
            </a>
          </li>
           <li role="presentation">
            <a href="#tab_attachments" aria-controls="tab_attachments" role="tab" data-toggle="tab">
              <?php echo _l('contract_attachments'); ?>
            </a>
          </li> 
         <!--  <li role="presentation">
            <a href="#tab_renewals" aria-controls="tab_renewals" role="tab" data-toggle="tab">
              <?php echo _l('no_contract_renewals_history_heading'); ?>
            </a>
          </li> -->
          <li role="presentation">
            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab" onclick="init_rel_tasks_table(<?php echo $contract->id; ?>,'trade_license'); return false;">
              <?php echo _l('tasks'); ?>
            </a>
          </li> 

            <?php ####################  Reminder ##########################################
          ?>
           <!--  <li role="presentation">
               <a href="#tab_reminders" onclick="initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + <?php echo $contract->id ;?> + '/' + 'trade_license', [4], [4],undefined,[1,'ASC']); return false;" aria-controls="tab_reminders" role="tab" data-toggle="tab">
               <?php echo _l('estimate_reminders'); ?>
               <?php
                  $total_reminders = total_rows('tblreminders',
                   array(
                    'isnotified'=>0,
                    'staff'=>get_staff_user_id(),
                    'rel_type'=>'trade_license',
                    'rel_id'=>$contract->id
                  )
                  );
                  if($total_reminders > 0){
                   echo '<span class="badge">'.$total_reminders.'</span>';
                  }
                  ?>
               </a>
            </li> -->
          <?php ##############################################################################?>

          <li role="presentation">
            <a href="#" onclick="contract_full_view(); return false;" data-toggle="tooltip" data-title="<?php echo _l('toggle_full_view'); ?>" class="toggle_view">
              <i class="fa fa-expand"></i></a>
            </li>
          </ul>
          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="tab_content">
              <div class="row">
                <div class="col-md-12 text-right _buttons hide">
                  <a href="<?php echo admin_url('contracts/pdf/'.$contract->id.'?print=true'); ?>" target="_blank" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('print'); ?>" data-placement="bottom"><i class="fa fa-print"></i></a>
                  <a href="<?php echo admin_url('contracts/pdf/'.$contract->id); ?>" class="btn btn-default mright5 btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('view_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i></a>
                  <a href="#" class="btn btn-default mright5" data-target="#contract_send_to_client_modal" data-toggle="modal"><span class="btn-with-tooltip" data-toggle="tooltip" data-title="<?php echo _l('contract_send_to_email'); ?>" data-placement="bottom"><i class="fa fa-envelope"></i></span></a>
                </div>

        <!-- Removed Available merge fields div -->

                <!--div class="col-md-12">
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
              </div-->
            </div>
            <hr class="hr-panel-heading" />
            <div class="editable tc-content" style="border:1px solid #f1f1f1;min-height:70px;">
              <?php
              if(empty($contract->content)){
               echo hooks()->do_action('new_contract_default_content','<span class="text-danger text-uppercase mtop15 editor-add-content-notice"> ' . _l('click_to_add_content') . '</span>');
             } else {
               echo $contract->content;
             }
             ?>
           </div>
         </div>
         <div role="tabpanel" class="tab-pane" id="tab_attachments">
          <?php echo form_open(admin_url('trade_licenses/add_contract_attachment/'.$contract->id),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>
          <?php echo form_close(); ?>
          <div class="text-right mtop15">
            <div id="dropbox-chooser"></div>
          </div>
          <div id="contract_attachments" class="mtop30">
            <?php
            $data = '<div class="row">';
            foreach($contract->attachments as $attachment) {
              $href_url = site_url('download/file/trade_license/'.$attachment['attachment_key']);
              if(!empty($attachment['external'])){
                $href_url = $attachment['external_link'];
              }
              $data .= '<div class="display-block contract-attachment-wrapper">';
              $data .= '<div class="col-md-10">';
              $data .= '<div class="pull-left"><i class="'.get_mime_class($attachment['filetype']).'"></i></div>';
              $data .= '<a href="'.$href_url.'">'.$attachment['file_name'].'</a>';
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

    <div role="tabpanel" class="tab-pane" id="tab_tasks">
      <?php init_relation_tasks_table(array('data-new-rel-id'=>$contract->id,'data-new-rel-type'=>'trade_license')); ?>
    </div>

    <?php ############################################################################################?>

     <div role="tabpanel" class="tab-pane" id="tab_reminders">
               <a href="#" class="btn btn-info btn-xs" data-toggle="modal" data-target=".reminder-modal-contract-<?php echo $contract->id; ?>"><i class="fa fa-bell-o"></i> <?php echo _l('contract_set_reminder_title'); ?></a>
               <hr />
               <?php render_datatable(array( _l( 'reminder_description'), _l( 'reminder_date'), _l( 'reminder_staff'), _l( 'reminder_is_notified'), _l( 'options'), ), 'reminders'); ?>
               <?php $this->load->view('admin/includes/modals/reminder',array('id'=>$contract->id,'name'=>'contract','members'=>$members,'reminder_title'=>_l('contract_set_reminder_title'))); ?>
    </div>
<?php ############################################################################################?>


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
<?php $this->load->view('admin/contracts/send_to_client'); ?>
<?php $this->load->view('admin/contracts/renew_contract'); ?>
<?php } ?>
<?php $this->load->view('admin/contracts/contract_type'); ?>
<script>
  Dropzone.autoDiscover = false;
  $(function(){

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
         $.post(admin_url+'contracts/add_external_attachment',{files:files,contract_id:contract_id,external:'dropbox'}).done(function(){
          var location = window.location.href;
          window.location.href= location.split('?')[0]+'?tab=tab_attachments';
        });
       },
       linkType: "preview",
       extensions: app_allowed_files.split(','),
     }));
    }

    _validate_form($('#contract-form'),{client:'required',datestart:'required',license_no:'required'});
    _validate_form($('#renew-contract-form'),{new_start_date:'required'});

  var _templates = [];
     /* $.each(contract_templates, function(i, template) {
      _templates.push({
        url: admin_url + 'contracts/get_template?name=' + template,
        title: template
      });
    });*/

    var editor_settings = {
      selector: 'div.editable',
      inline: true,
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
     toolbar2:'bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent',
     toolbar3: 'media image | forecolor backcolor link ',
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
         $.post(admin_url + 'trade_licenses/save_contract_data', data).done(function(response) {
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
  if (_templates.length > 0) {
    editor_settings.templates = _templates;
    editor_settings.plugins[3] = 'template ' + editor_settings.plugins[3];
  }

  tinymce.init(editor_settings);
});

function delete_contract_attachment(wrapper,id){
     if (confirm_delete()) {
        $.get(admin_url + 'trade_licenses/delete_contract_attachment/'+id,function(response){
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
</body>
</html>
