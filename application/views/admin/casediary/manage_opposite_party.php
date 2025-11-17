<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
             <div class="panel_s">
                <div class="panel-body">
                    <div class="_buttons">
                        <a href="<?php echo admin_url('opposite_parties/opposite_party') ?>"  class="btn btn-info pull-left display-block"><?php echo _l('new_opposite_party'); ?></a>

                       
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('#'),
                        _l('opposite_company'),
                        _l('firstname'),
                        _l('lastname'),
                        _l('customer'),
                        _l('email'),
                        _l('mobile'),
                        _l('city'),
                        _l('options')
                        ),'opposite_party'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/casediary/oppositeparty'); ?>
<?php init_tail(); ?>
<script>
    $(function(){
        initDataTable('.table-opposite_party', window.location.href, [1], [1]);
    });
</script>
</body>
</html>
