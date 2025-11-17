<?php init_head(); ?>

<div id="wrapper">

  <div class="content">

    <div class="row">

      <div class="col-md-4 left-column">

        <div class="panel_s">

          <div class="panel-body">

            <h4 class="no-margin"><?php echo $title; ?>

              <?php if(isset($contract) && has_permission('intellectual_property','','delete')){ ?>

              <small><a href="<?php echo admin_url('intellectual_property/delete/'.$contract->id); ?>" class="pull-right mleft5 text-danger _delete"><?php echo _l('delete'); ?></a></small>

              <?php } ?>

              <?php if(isset($contract) && has_permission('intellectual_property','','create')){ ?>

              <small><a href="<?php echo admin_url('trade_licenses/copy/'.$contract->id); ?>" class="pull-right hide"><?php echo _l('contract_copy'); ?></a></small>

              <?php } ?>

            </h4>

            <hr class="hr-panel-heading" />

            <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'ip-form')); ?>

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

          <?php $value = (isset($contract) ? $contract->subject : ''); ?>

          <?php echo render_input('subject','ip_subject',$value,'text',array('data-toggle'=>'tooltip','title'=>'license_tooltip')); ?>



          <?php $value = (isset($contract) ? $contract->file_no : ''); ?>

          <?php echo render_input('file_no','file_no',$value,'text'); ?>


          <div class="row">
          <div class="col-md-12 " id="iptype">
                   	         <?php 
          			           $ip_types = get_ip_types();
					           $selected = (isset($contract) ? $contract->ip_type : 1); ?>
        			         <?php echo render_select('ip_type',$ip_types,array('id','name'),'ip_type',$selected,[],[],'','',false); ?>
        			
						    </div>
                </div>



         <?php 

          $ip_statuses = get_ip_statuses();

          $selected = (isset($contract) ? $contract->ip_status : 'applied'); ?>

         <?php echo render_select('ip_status',$ip_statuses,array('id','name'),'ip_status',$selected); ?>



          <?php $value = (isset($contract) ? $contract->issued_by : ''); ?>

          <?php echo render_input('issued_by','issued_by',$value,'text'); ?>

         

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

            <?php echo render_date_input('issue_date','ip_issue_date',$value); ?>

          </div>

          <div class="col-md-6">

            <?php $value = (isset($contract) ? _d($contract->expiry_date) : ''); ?>

            <?php echo render_date_input('expiry_date','ip_expiry_date',$value); ?>

          </div>

        </div>


         <!-- ip fields  --------->
         <div class="row" id=ipinfo>
                         <div class="col-md-12">
                               <?php $selected = (isset($contract) ? $contract->ip_class : ''); 
                               $ip_class_dropdowns = get_ip_class_drop_down(); ?>
                              <?php echo render_select('ip_class',$ip_class_dropdowns,array('id','name'),'class',$selected,[],[],'','',false);?>  
                          </div>
                          <div class="col-md-12">
                             <?php $value = (isset($contract) ? $contract->ipregistration_no : ''); ?> 
                              <?php echo render_input('ipregistration_no','registration_no',$value,'text'); ?>
                          </div>
                          <div class="col-md-12 hide">  
                            <?php $value = (isset($contract) ? _d($contract->ipregistration_date) : _d(date('Y-m-d'))); ?>
                                <?php echo render_date_input('ipregistration_date','ipregistration_date',$value); ?>
                           </div>
                           <div class="col-md-12" id="div_trade_mark_type">
                            <?php 
                            $trade_mark_types = array(
                                                    ['id'=>'Word','name'=>'Word'],
                                                    ['id'=>'Logo','name'=>'Logo'],
                                                    );
                            $selected = (isset($contract) ? $contract->trade_mark_type : 'Logo' ); ?>
                            <?php echo render_select('trade_mark_type',$trade_mark_types,['id','name'],'trade_mark_type',$selected,[],[],'','',false); ?>
                               
                           </div>
                            <div class="col-md-12">
                             <?php $value = (isset($contract) ? $contract->ip_specification : ''); ?> 
                              <?php echo render_input('ip_specification','Specification',$value,'text'); ?>
                            </div>
						</div> 

            <?php 
			   if(total_rows('tblfiles',array('rel_id'=>$contract->id,'rel_type'=>'intellectual_property'))>0)
                        $hide_ip_attachment_ = 'hide';
			  else
                        $hide_ip_attachment_ = '';
                       
                        ?> 
 			        <div class="row mbot20 <?php echo $hide_ip_attachment_;?> " id="iplogo" >
                       <div class="col-md-12">
                            <?php $value=( isset($contract) ? $contract->ip_description : ''); ?>
                            <?php echo render_textarea('ip_description','ip_description',$value); ?>
                            
                            <label for="installment_receipt" class="profile-image"><?php echo _l('attach_artwork'); ?></label>
                            <?php if(isset($contract) && $contract->ip_logo!=''){
        				       $path = get_upload_path_by_type('intellectual_property') . $contract->id . '/'. $contract->ip_logo;
        					   if(file_exists($path)){?>
                   		           <a href="<?php echo site_url('download/downloadlogofile/'.$contract->id.'/'.$contract->ip_logo); ?>" class="btn btn-info btn-icon pull-right mbot10"><?php echo $contract->ip_logo.' '; ?><i class="fa fa-download"></i></a>
                                <?php }
                                } ?>
						   
                            <input type="file" name="ip_logo[]" accept="image/*" multiple  class="form-control" id="ip_logo">
						   
    					</div> 
                   
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

  <?php if(isset($contract)) { ?>

  <div class="col-md-8 right-column">

    <div class="panel_s">

      <div class="panel-body">

        <h4 class="no-margin"><?php echo _l('ip_overview'); ?></h4>

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

          <!-- <li role="presentation">

            <a href="#tab_tasks" aria-controls="tab_tasks" role="tab" data-toggle="tab" onclick="init_rel_tasks_table(<?php echo $contract->id; ?>,'trade_license'); return false;">

              <?php echo _l('tasks'); ?>

            </a>

          </li>  -->



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

          <?php echo form_open(admin_url('intellectual_property/add_contract_attachment/'.$contract->id),array('id'=>'contract-attachments-form','class'=>'dropzone')); ?>

          <?php echo form_close(); ?>

          <div class="text-right mtop15">

            <div id="dropbox-chooser"></div>

          </div>

          <div id="contract_attachments" class="mtop30">

            <?php

            $data = '<div class="row">';

            foreach($contract->attachments as $attachment) {

              $href_url = site_url('download/file/intellectual_property/'.$attachment['attachment_key']);

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

      <?php init_relation_tasks_table(array('data-new-rel-id'=>$contract->id,'data-new-rel-type'=>'intellectual_property')); ?>

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

<div class="ip_gallery" ></div>
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

<?php //$this->load->view('admin/contracts/send_to_client'); ?>

<?php //$this->load->view('admin/contracts/renew_contract'); ?>

<?php } ?>

<?php //$this->load->view('admin/contracts/contract_type'); ?>

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



    _validate_form($('#contract-form'),{client:'required',datestart:'required',subject:'required',ip_type:'required',file_no:'required'});

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

         $.post(admin_url + 'intellectual_property/save_contract_data', data).done(function(response) {

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

        $.get(admin_url + 'intellectual_property/delete_contract_attachment/'+id,function(response){

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
<script type="text/javascript">
    
    	
		






    
    $('#ip_type').change(function(){
     
        var type = $(this).val();
        // alert(type);
        if(type=="trademark"){
            $('#div_trade_mark_type').removeClass('hide');
        }else{
            $('#div_trade_mark_type').addClass('hide');  
        }
    }); 

    $(function() {
    // Multiple images preview in browser
    var imagesPreview = function(input, placeToInsertImagePreview) {

        if (input.files) {
            var filesAmount = input.files.length;

            for (i = 0; i < filesAmount; i++) {
                var reader = new FileReader();

                reader.onload = function(event) {
                    $($.parseHTML('<img width="80%">')).attr('src', event.target.result).appendTo(placeToInsertImagePreview);
                }

                reader.readAsDataURL(input.files[i]);
            }
        }

    };

    $('#ip_logo').on('change', function() {
        imagesPreview(this, 'div.ip_gallery');
    });
});

</script>
</body>

</html>

