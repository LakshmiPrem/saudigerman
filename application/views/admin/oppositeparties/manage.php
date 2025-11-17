<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="_buttons">
                            <a href="<?php echo admin_url('opposite_parties/opposite_party') ?>"
                                class="btn btn-info pull-left display-block">
                                <?php echo _l('new_opposite_party'); ?>
                            </a>


                        </div>



                        <div class="row" id="contract_summary">

                            <?php $minus_7_days = date('Y-m-d', strtotime("-7 days")); ?>
                            <?php $plus_7_days = date('Y-m-d', strtotime("+7 days"));
                            // $where_own = array();
                            
                            ?>
                            <div class="col-md-12"><br>
                                <h4 class="no-margin text-success">
                                    <?php echo _l('opposite_party_summary_heading'); ?>
                                </h4>
                            </div>
                            <div class="col-md-4 col-xs-6 border-right">
                                <h3 class="bold">
                                    <?php echo total_rows('tbloppositeparty',array('active' => 1)); ?>
                                </h3>
                                <span class="text-info">
                                    <?php echo _l('contract_summary_active'); ?>
                                </span>
                            </div>
                            <div class="col-md-4 col-xs-6 border-right">
                                <h3 class="bold">
                                    <?php echo total_rows( 'tbloppositeparty',array('active' => 0)); ?>
                                </h3>
                                <span class="text-danger">
                                    <?php echo _l('inactive'); ?>
                                </span>
                            </div>
                            
                            <div class="col-md-4 col-xs-6 border-right">
                                <h3 class="bold">
                                    <?php
                                    echo total_rows('tbloppositeparty', 'dateadded BETWEEN "' . $minus_7_days . '" AND "' . $plus_7_days.'"' ); ?>
                                </h3>
                                <span class="text-success">
                                    <?php echo _l('contract_summary_recently_added'); ?>
                                </span>
                            </div>
                            
                           
                            
                        </div>




                       
                        <hr class="hr-panel-heading" />
                        <div class="row">
                        <div class="col-md-3">
                <?php 
                // print_r($party_type);
                echo render_select('party_type',$party_type,array('id','provider_name'),'oppo_party_type');
                ?>
                </div>
                <div class="col-md-3">
                <?php 
                // print_r($active);
                echo render_select('active',$active,array('id','name'),'active/inactive');
                ?>
                </div>
                <div class="col-md-3">
                                <label><?php echo _l('dateadded'); ?></label>
                               <div class="select-placeholder">
                                <select name="dateadded" id="dateadded" class="selectpicker" data-width="100%">
                                 <option value=""  ><?php echo _l('report_sales_months_all_time'); ?></option>   
                                 <option value="today" ><?php echo _l('today'); ?></option>
                                 <option value="this_month"  ><?php echo _l('this_month'); ?></option>
                                 <option value="last_month"  ><?php echo _l('last_month'); ?></option>
                                 <option value="this_week" ><?php echo _l('this_week'); ?></option>
                                 <option value="last_week" ><?php echo _l('last_week'); ?></option>
                                 <option value="period"  onclick="unhide_period();return false;" ><?php echo _l('period'); ?></option>
                               </select>
                               </div>
                               
                            </div>
                            <div class="col-md-3 hide" id="oppo_period">
                            <?php echo render_date_input( 'start_date', 'project_start_date'); ?>
                            </div>
                            <div class="col-md-3 hide" id="oppo_period1">
                            <?php echo render_date_input( 'end_date', 'cend_date'); ?>
                            </div>
                            <div class="col-md-3 hide" id="oppo_period2">
                            <button class="btn btn-info mtop25" onclick="table_reload();return false;">apply</button>
                            </div>
                            </div>
                        <div class="clearfix"></div>
                        <?php render_datatable(
                            array(
                                _l('#'),
                                _l(''),
                                _l('opposite_company'),
                                _l('trade_licence'),
                                _l('trade_expiry_dt'),
                                //  _l('client'),
                                _l('email'),
                                _l('mobile'),
                                _l('city'),
                                _l('active'),
                                _l('options')

                            ), 'opposite_party'); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->load->view('admin/casediary/oppositeparty'); ?>
<?php init_tail(); ?>
<script>
    $(function () {
        var CustomersServerParams = {};
        CustomersServerParams['party_type'] = '[name="party_type"]';
        CustomersServerParams['active'] = '[name="active"]';
        CustomersServerParams['dateadded'] = '[name="dateadded"]';
        CustomersServerParams['start_date'] = '[name="start_date"]';
        CustomersServerParams['end_date'] = '[name="end_date"]';
    //    if( $("#dateadded").val()=="period"){
        
        // unhide_period();
    //    }
        tAPI=initDataTable('.table-opposite_party', window.location.href, [1], [1],CustomersServerParams);
        $.each(CustomersServerParams, function(i, obj) {
            $('select' + obj).on('change', function() {
                var data=$('select[name="dateadded"]').val();
        // alert();
        if( data=="period"){
        
        unhide_period();
       }
                // alert(CustomersServerParams['party_type']);
                 tAPI.ajax.reload();
            });
        });
    });
    function unhide_period(){
        $("#oppo_period").removeClass('hide');
        $("#oppo_period1").removeClass('hide');
        $("#oppo_period2").removeClass('hide');
    }
    function table_reload(){
        tAPI.ajax.reload();
    }
</script>
</body>

</html>