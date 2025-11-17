<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                     <div class="_buttons">
                        <a href="#" class="btn btn-info pull-left" data-toggle="modal" data-target="#customer_shareholder_modal"><?php echo _l('new_shareholder'); ?></a>
                    </div>
                    <div class="clearfix"></div>
                    <hr class="hr-panel-heading" />
                    <div class="clearfix"></div>
                    <?php render_datatable(array(
                        _l('shareholder_name'),
						_l('client_firstname'),_l('client_lastname'),
						_l('stake_type'),
						 _l('client_phonenumber'),
						_l('client_email'),
                        _l('options'),
                        ),'customer-shareholders'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/clients/client_shareholder'); ?>
<?php init_tail(); ?>
<script>
   $(function(){
        initDataTable('.table-customer-shareholders', window.location.href, [1], [1]);
   });
</script>
</body>
</html>
