<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">
      <div class="col-md-12">
        <div class="panel_s">
          <div class="panel-body">
            <?php echo $this->import->downloadSampleFormHtml(); ?>
            <?php echo $this->import->maxInputVarsWarningHtml(); ?>
            <?php if(!$this->import->isSimulation()) { ?>
              <?php echo $this->import->importGuidelinesInfoHtml(); ?>
              <?php echo $this->import->createSampleTableHtml(); ?>
            <?php } else { ?>
              <?php echo $this->import->simulationDataInfo(); ?>
              <?php echo $this->import->createSampleTableHtml(true); ?>
            <?php } ?>

            <div class="panel-group" id="accordion">
                <div class="panel panel-default">
                  <div class="panel-heading">
                    <h4 class="panel-title">
                      <a data-toggle="collapse" data-parent="#accordion" href="#collapse1">Case Type Values</a>
                      <p><small>Copy and paste the ID or Name values from the list below into the Case type column of the CSV file you're importing.</small></p>
                    </h4>
                  </div>
                  <div id="collapse1" class="panel-collapse collapse">
                    <ul class="list-group">
                    <?php
                      $case_types = get_case_client_types();
                      foreach($case_types as $cas){ ?>
                        <li class="list-group-item"><?php echo "ID : ".$cas['id'] ?>  /  <?php echo "Name : ".$cas['name'] ?> </li>
                      <?php } ?>
                    </ul>
                  </div>
                </div>
               
              </div> 
            <div class="row">

              <div class="col-md-4 mtop15">
                <?php echo form_open_multipart($this->uri->uri_string(),array('id'=>'import_form')) ;?>
                <?php echo form_hidden('clients_import','true'); ?>
                <?php echo render_input('file_csv','choose_csv_file','','file'); ?>
                <?php
                //echo render_input('default_pass_all','default_pass_clients_import',$this->input->post('default_pass_all')); ?>
                <div class="form-group">
                  <button type="button" class="btn btn-info import btn-import-submit"><?php echo _l('import'); ?></button>
                  <button type="button" class="btn btn-info simulate btn-import-submit"><?php echo _l('simulate_import'); ?></button>
                </div>
                <?php echo form_close(); ?>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>
<?php $this->load->view('admin/clients/client_group'); ?>
<div id="domMessage" style="display:none;"> 
    <h4>Please wait....it will take some time..<br><i class="fa fa-spinner fa-spin"></i></h4> 
</div> 
<?php init_tail(); ?>
<script type="text/javascript" src="<?php echo base_url('assets/js/blockUI.js');?>"></script>
<script src="<?php echo base_url('assets/plugins/jquery-validation/additional-methods.min.js'); ?>"></script>
<script>
 $(function(){
   appValidateForm($('#import_form'),{file_csv:{required:true,extension: "csv"}});
 });
</script>
</body>
</html>
