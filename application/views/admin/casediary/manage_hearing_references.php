<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
             <div class="panel_s">
                <div class="panel-body">
                    <div class="_buttons">
                        <a href="#" onclick="new_hearingReference(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_hearing_reference'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('name'),
	 					_l('active'),
                        _l('options')
                        ),'hearingReference'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/casediary/hearing_reference'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-hearingReference', window.location.href, [1], [1]);
    });
</script>
</body>
</html>
