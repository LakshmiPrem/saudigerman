<div class="modal fade " tabindex="-1" id="_communication_modal" role="dialog" data-toggle="modal" >
   <div class="modal-dialog " role="document">
      <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">
          <?php echo get_project_name_by_id($data->case_id);  ?>
        </h4>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">

            <table class="table" width="100%">
              <thead>
                <tr>
                  <th width="25px" align="center">From</th>
                  <th width="10px">:</th>
                  <th style="width: 100px; overflow-wrap:anywhere ;">
                    <!-- <div class="row">
                      <div class="col-md-12">
                        <textarea class="form-control" rows="5" id="mail_from" name="mail_from">
                        <?=trim($data->mail_from)?>
                        </textarea></div>
                    </div>
                    <div class="row">
                    <div class="col-md-2 pull-right">
                      <button onclick="update_mail_from(<?=$data->id?>);" class="btn btn-info btn-sm" type="button">Save</button>
                    </div>
                    </div> -->

                    <?=trim($data->mail_from)?> </th>
                </tr>
                <tr>
                  <th align="center">To</th>
                  <th width="10px">:</th>
                  <th ><?=nl2br(str_replace(',', '<br>',$data->mail_to ))?></th>
                </tr>
                <tr>
                  <th align="center">Subject</th>
                  <th width="10px">:</th>
                  <th><?=$data->subject?></th>
                </tr>
                <tr>
                  <th align="center">Date</th>
                  <th width="10px">:</th>
                  <th><?=_d($data->date,true)?></th>
                </tr>
                <tr>
                  <th align="center">Mode</th>
                  <th width="10px">:</th>
                  <th><?=$data->mode?></th>
                </tr>
                <tr>
                  <th align="center">Content</th>
                  <th width="10px">:</th>
                  <th> 
                          <?php  echo render_textarea( 'case_details', '',nl2br(trim($data->content)));?>
                      
                          <a class="btn btn-default mtop25"  onclick="changeLanguageByButtonClick()">Translate</a>
                           <input class="hide" value="en" id="language"/>
                      <p class="translate" id="p1" style="visibility: hidden;" ></p>
                      <div id="google_translate_element" style="display:none"></div>
                      <?php  echo render_textarea( 'case_details_en', '');?>
                      
                       
              </th>
                </tr>
                <tr>
                  <th align="center">Attachments</th>
                  <th width="10px">:</th>
                  <th><ol  class="list">
                  <?php  foreach ($data->attachments as $value) { 
                    $path = CASEDIARY_UPLOADS_FOLDER .$data->case_id.'/'.$value->file_name;
                    ?>
                      <li class="list-item">
                        <?php
                        
                         ?>
                        <a href="<?php echo site_url('uploads/casediary/'.$data->case_id.'/'.$value['file_name']); ?>" target="_blank" ><?=$value['file_name']?></a></li>
                  <?php } ?>
                </ol></th>
                </tr>
              </thead>
            </table>
            
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
function googleTranslateElementInit() {
  new google.translate.TranslateElement({pageLanguage: "ar"}, 'google_translate_element');
}

function changeLanguageByButtonClick() {

  ///var language = document.getElementById("language").value;
  var selectField = document.querySelector("#google_translate_element select");
  for(var i=0; i < selectField.children.length; i++){
    var option = selectField.children[i];
    // find desired langauge and change the former language of the hidden selection-field 
    if(option.value=='en'){
       selectField.selectedIndex = i;
       // trigger change event afterwards to make google-lib translate this side
       selectField.dispatchEvent(new Event('change'));
		var src = document.getElementById("case_details").value;   
      document.getElementById("p1").innerHTML = src;
		//alert(src);
      //document.getElementById("txt2").value=document.getElementById("p1").innerHTML;
       break;
    }
  }
  setTimeout(function(){
  $('#case_details_en').val( $('#p1').text());
//document.getElementById("executor_translated").value=document.getElementById("p1").innerHTML;
  },1000);
} 
</script>
<script type="text/javascript" src="//translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>