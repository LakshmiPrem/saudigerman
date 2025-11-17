<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
             <div class="panel_s">
                <div class="panel-body">
                    <div class="_buttons">
                        <a href="#" onclick="new_Courts(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_court'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('name'),
                        _l('options')
                        ),'courts'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/casediary/courts'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-courts', window.location.href, [1], [1]);
    });
</script>
</body>
</html>
