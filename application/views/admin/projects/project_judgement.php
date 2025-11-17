<div class="content">
  <div class="row">

    <div class="panel_s">
      <div class="panel-body">
   <div class="col-md-12">
        <a href="#" onclick="init_hearing_judgement('undefined',this);return false;" data-type="ruling" class="btn btn-info mbot25"><?php echo _l('set_hearing_ruling'); ?></a>
  </div>

      
    <?php
     render_datatable(array(
     _l('court_instance'),
     _l('judgement_award'),
     _l('judgement_ruling'),
	 _l('judgement_date'),
     _l('decree_order_status'),
     _l('judgement_status'),
	_l('added_by'),
	_l('summary'),
	_l('discussion_attachments'),
     ),'project-judgement'); ?>

  </div>
</div>
</div>
</div>


