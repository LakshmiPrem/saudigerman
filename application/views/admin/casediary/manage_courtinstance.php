<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
             <div class="panel_s">
                <div class="panel-body">
                    <div class="_buttons">
                        <a href="#" onclick="new_court_instance(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('create_instance'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('name'),
						_l('additional_name'),
						_l('active'),
                        _l('options')
                        ),'courtinstances'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/casediary/court_instance'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-courtinstances', window.location.href, [1], [1]);
    });
</script>
</body>
</html>
