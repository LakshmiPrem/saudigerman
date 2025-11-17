<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="_filters _hidden_inputs">
         </div>
         <div class="col-md-12">
            <div class="panel_s mbot10">
               <div class="panel-body _buttons">
                  <?php if(has_permission('proposals','','create')){ ?>
                  <a href="<?php echo admin_url('agreements/agreement'); ?>" class="btn btn-info pull-left display-block">
                  <?php echo _l('new_service_agreement'); ?>
                  </a>
                  <?php } ?>
                  <!-- <a href="<?php echo admin_url('proposals/pipeline/'.$switch_pipeline); ?>" class="btn btn-default mleft5 pull-left hidden-xs"><?php echo _l('leads_switch_to_kanban'); ?></a> -->
                  
               
               </div>
            </div>
            <div class="row">
               <div class="col-md-12" id="small-table">
                  <div class="panel_s">
                     <div class="panel-body">
                        <!-- if invoiceid found in url -->
                        <?php echo form_hidden('proposal_id',$proposal_id); ?>
                        <?php
                           $table_data = array(
                              _l('agreement') . ' #',
                              _l('proposal_subject'),
                              _l('file_number'),
                              _l('proposal_to'),
                              _l('valid_for'),
                              _l('proposal_date'),
                             // _l('proposal_open_till'),
                             _l('proposal_date'),   
                              _l('proposal_date_created'),
                              _l('ss'),
                            );

                             render_datatable($table_data,'agreements');
                           ?>
                     </div>
                  </div>
               </div>
               <div class="col-md-7 small-table-right-col">
                  <div id="proposal" class="hide">
                  </div>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
<script>var hidden_columns = [];</script>
<?php init_tail(); ?>
<div id="convert_helper"></div>
<script>
   var proposal_id;
   $(function(){
     var Proposals_ServerParams = {};
     $.each($('._hidden_inputs._filters input'),function(){
       Proposals_ServerParams[$(this).attr('name')] = '[name="'+$(this).attr('name')+'"]';
     });
     initDataTable('.table-agreements', admin_url+'agreements/table', ['undefined'], ['undefined'], Proposals_ServerParams, [0, 'DESC']);
     //init_proposal();
   });
</script>
<?php //echo app_stylesheet('assets/css','proposals.css'); ?>
<?php //echo app_script('assets/js','proposals.js'); ?>
</body>
</html>
