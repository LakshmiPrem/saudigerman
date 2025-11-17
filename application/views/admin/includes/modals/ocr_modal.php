<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<div class="modal fade" id="ocr-modal" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
    <?php echo form_open_multipart('admin/ocr/ocr'); ?>
      <div class="modal-header">
        <h5 class="modal-title" id="ocr-modal">OCR</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="row">
          <div class="col-md-12">
              <label for="filechoose">Choose File</label>
              <input type="file" name="ocr_file" class="form-control-file" id="ocr_file_choose">
              <span class="text-danger">Only Image Files are Accepted</span>
          </div>
        </div>

        <div class="row">
          <div class="col-md-12">
            <input type='button' style="margin-top:20px;" class='btn btn-info' value='Convert' id='ocr_btn_upload'>
          </div>
        </div>

        <div class="row" style="margin-top:20px;">
          <div class="col-md-12">
            <textarea id="ocr_text" class="tinymce" name="ocr_text" rows="4" cols="50">
            </textarea>
          </div>
        </div>
         
            
          </div>
        
      </div>
      <!-- <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="submit" class="btn btn-primary">Save</button>
      </div> -->
      <?php echo form_close(); ?>
    </div>
  </div>
</div>
