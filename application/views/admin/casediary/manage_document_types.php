<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
             <div class="panel_s">
                <div class="panel-body">
                    <div class="_buttons">
                        <a href="#" onclick="new_document_type(); return false;" class="btn btn-info pull-left display-block"><?php echo _l('new_case_nature'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('name'),
						_l('category'),
						_l('active'),
                        _l('options')
                        ),'document_type'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/casediary/document_type'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-document_type', window.location.href, [1], [1]);
    });
</script>
</body>
</html>
