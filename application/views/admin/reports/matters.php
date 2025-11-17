<?php init_head(); ?>
<div id="wrapper">
   <div class="content">
      <div class="row">
         <div class="col-md-12">
            <div class="panel_s">
               <div class="panel-body">
                  <div class="row">
					  <?php if(get_option('enable_legaldashboard')==1){ ?>
                     <div class="col-md-4 border-right">
                      <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('matter_report_heading'); ?></h4>
                      <hr />
	
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-litigation-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('litigation_report'); ?></a>
                      </p>
						  <hr class="hr-10" />
                       <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-litigation-update-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('collective_litigation_update_report'); ?></a>
                      </p>
                       <hr class="hr-10" />

                      <p>
                        <a href="#" class="font-medium hide" onclick="init_report(this,'matter-litigation-country-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('litigation_country_report'); ?></a>
                      </p>
                       
                       
                         <hr class="hr-10 hide" />                      
                  <!--   <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-update-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('matter_updates'); ?></a>
                      </p>
                      <hr class="hr-10" />   -->                  
                     <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-activecase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('active_case'); ?></a>
                      </p>
                      <hr class="hr-10" />                      
                     <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-closecase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('closed_case_report'); ?></a>
                      </p>
                     
                      <hr class="hr-10" />
						<p>
                         <a href="#" class="font-medium hide" onclick="init_report(this,'matter-transfercase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('transfer_case'); ?></a>
                      </p>
                        <hr class="hr-10 hide" />
						<p>
                         <a href="#" class="font-medium hide" onclick="init_report(this,'matter-companycase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('casecompany_report'); ?></a>
                      </p>
                       <hr class="hr-10 hide" />
                        <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-execution-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('execution_report'); ?></a>
                      </p>
                       <hr class="hr-10 hide" />
                        <p>
                        <a href="#" class="font-medium hide" onclick="init_report(this,'matter-legalaction-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('legalaction_report'); ?></a>
                      </p>
                       <hr class="hr-10 hide" />
                       <p >
                        <a href="#" class="font-medium hide" onclick="init_report(this,'matter-casenature-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('case_wise'); ?></a>
                      </p>
                     
                      <!-- 
                      <hr class="hr-10" />
                      <p>
                      <a href="#" class="font-medium" onclick="init_report(this,'proposals-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('proposals_report'); ?></a></p>
                      <hr class="hr-10" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'estimates-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('estimates_report'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'customers-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_sales_type_customer'); ?></a>
                      </p>

                      <hr class="hr-10" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'receivables-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('receivable_report'); ?></a>
                      </p> 
                       <hr class="hr-10" />
                       <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-individualcase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('single_case_report'); ?></a>
                      </p>-->
                       <hr class="hr-10" />
                       <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'settlement-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('settlement_report'); ?></a>
                      </p>
						  <hr class="hr-10" />
						 <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'hearings-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('hearings_report'); ?></a>
                      </p>
                      <hr class="hr-10" />
						  <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-others-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('legal_projects'); ?></a>
                      </p>
                        <hr class="hr-10" />
						 <hr class="hr-10 hide" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-totalreceived-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('received_report'); ?></a>
                       
                      </p>
                     
                      <?php if(total_rows('tblinvoices',array('status'=>5)) > 0){ ?>
                      <hr class="hr-10" />
                      <!-- <p class="text-danger">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo _l('sales_report_cancelled_invoices_not_included'); ?>
                     </p> -->
                     <?php } ?>
                  </div>
					  <?php } ?>
                    <div class="col-md-4 border-right">
                      <h4 class="no-margin font-medium"><i class="fa fa-balance-scale" aria-hidden="true"></i> <?php echo _l('matter_report_heading'); ?></h4>
                      <hr />

                    

                     <!--<p>
                        <a href="#" class="font-medium" onclick="init_report(this,'cheque-bounce-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('chequebounce_report'); ?></a>
                      </p>
                        <hr class="hr-10" />-->
<?php if(get_option('enable_legaldashboard')==1){ ?>
                     <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'police-case-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('policecase_report'); ?></a>
                      </p>
                        <hr class="hr-10" />   

                     <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-age-wise-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('age_wise'); ?></a>
                      </p>
                     <hr class="hr-10" />  
                      <p>
                        <a href="#" class="font-medium hide" onclick="init_report(this,'matter-labourcase-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('labourcase_report'); ?></a>
                      </p>                    
                    <!-- <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-clients-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('client_wise'); ?></a>
                      </p>-->
                          <hr class="hr-10 hide" />
                       <p >
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-lawyers-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('lawyer_wise'); ?></a>
                      </p>
                          <hr class="hr-10" />                      
                                
                     <p>
                        <a href="#" class="font-medium hide" onclick="init_report(this,'matter-verification-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('verification_report'); ?></a>
                      </p>
                          <hr class="hr-10 hide" />
                   <!--    <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'refundable-deposit'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('refundable_deposit_report'); ?></a>
                      </p> 
                         <hr class="hr-10" />                      
                   <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'matter-update-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('matter_updates'); ?></a>
                      </p>
                      <hr class="hr-10" />   -->                  
                    
                      
                       
                      <!-- <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'lawyer-timesheets'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('lawyer_timesheets'); ?></a>
                      </p>   
                      <hr class="hr-10" /> -->
                     <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'documents-expiry'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('documents_expiry_report'); ?></a>
                      </p> 
						<?php } ?>
                      <!-- 
                      <hr class="hr-10" />
                      <p>
                      <a href="#" class="font-medium" onclick="init_report(this,'proposals-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('proposals_report'); ?></a></p>
                      <hr class="hr-10" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'estimates-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('estimates_report'); ?></a>
                      </p>
                      <hr class="hr-10" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'customers-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_sales_type_customer'); ?></a>
                      </p>

                      <hr class="hr-10" />
                      <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'receivables-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('receivable_report'); ?></a>
                      </p> 
                      -->
                      
                      <hr class="hr-10 hide" />                      
                                
                     <p>
                        <a href="#" class="font-medium hide" onclick="init_report(this,'matter-handover-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('handover_report'); ?></a>
                      </p>
                        <hr class="hr-10 hide" />
                      
                       <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'agreements-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('agreements_report'); ?></a>
                      </p>
					 <hr class="hr-10" />
             <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'receivable-agreements-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('receivable_agreements_report'); ?></a>
                      </p>
           <hr class="hr-10" />
             <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'payable-agreements-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('payable_agreements_report'); ?></a>
                      </p>
                <hr class="hr-10" />
             <p>
                        <a href="#" class="font-medium" onclick="init_report(this,'signed-agreements-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('signed_agreements_report'); ?></a>
                      </p>
           <hr class="hr-10" />
                                         
                        <a href="#" class="font-medium" onclick="init_report(this,'legalrequests-report'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('legal_request_report'); ?></a>
                      </p>
                      <?php if(total_rows('tblinvoices',array('status'=>5)) > 0){ ?>
                      <hr class="hr-10" />
                      <!-- <p class="text-danger">
                        <i class="fa fa-exclamation-circle" aria-hidden="true"></i> <?php echo _l('sales_report_cancelled_invoices_not_included'); ?>
                     </p> -->
                     <?php } ?>
                  </div>
                  <div class="col-md-1 border-right hide">
                    <h4 class="no-margin font-medium"><i class="fa fa-area-chart" aria-hidden="true"></i> <?php echo _l('charts_based_report'); ?></h4>
                    <hr />
                    <p><a href="#" class="font-medium" onclick="init_report(this,'total-income'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_sales_type_income'); ?></a></p>
                    <hr class="hr-10" />
                    <p><a href="#" class="font-medium" onclick="init_report(this,'payment-modes'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('payment_modes_report'); ?></a></p>
                    <hr class="hr-10" />
                    <p><a href="#" class="font-medium" onclick="init_report(this,'customers-group'); return false;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?php echo _l('report_by_customer_groups'); ?></a></p>
                 </div>
                 <div class="col-md-3">
                  <?php if(isset($currencies)){ ?>
                  <div id="currency" class="form-group hide">
                     <label for="currency"><i class="fa fa-question-circle" data-toggle="tooltip" title="<?php echo _l('report_sales_base_currency_select_explanation'); ?>"></i> <?php echo _l('currency'); ?></label><br />
                     <select class="selectpicker" name="currency" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                        <?php foreach($currencies as $currency){
                           $selected = '';
                           if($currency['isdefault'] == 1){
                              $selected = 'selected';
                           }
                           ?>
                           <option value="<?php echo $currency['id']; ?>" <?php echo $selected; ?>><?php echo $currency['name']; ?></option>
                           <?php } ?>
                        </select>
                     </div>
                     <?php } ?>
                     <div id="income-years" class="hide mbot15">
                        <label for="payments_years"><?php echo _l('year'); ?></label><br />
                        <select class="selectpicker" name="payments_years" data-width="100%" onchange="total_income_bar_report();" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <?php foreach($payments_years as $year) { ?>
                           <option value="<?php echo $year['year']; ?>"<?php if($year['year'] == date('Y')){echo 'selected';} ?>>
                              <?php echo $year['year']; ?>
                           </option>
                           <?php } ?>
                        </select>
                     </div>
					 <div class="form-group hide" id="collection_filter">
                    <label for="filterdata-report"><?php echo _l('based_on'); ?></label><br />
                     <select class="selectpicker" name="change_option" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value="matter"><?php echo _l('filing_date'); ?></option>
                           <option value="update"><?php echo _l('update_date'); ?></option>
                           
                        </select>
         
                     </div>
                     <div class="form-group hide" id="report-time">
                        <label for="months-report"><?php echo _l('period_datepicker'); ?></label><br />
                        <select class="selectpicker" name="months-report" data-width="100%" data-none-selected-text="<?php echo _l('dropdown_non_selected_tex'); ?>">
                           <option value=""><?php echo _l('report_sales_months_all_time'); ?></option>
                           <option value="this_month"><?php echo _l('this_month'); ?></option>
                           <option value="1"><?php echo _l('last_month'); ?></option>
                           <option value="this_year"><?php echo _l('this_year'); ?></option>
                           <option value="last_year"><?php echo _l('last_year'); ?></option>
                           <option value="3" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-2 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_three_months'); ?></option>
                           <option value="6" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-5 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_six_months'); ?></option>
                           <option value="12" data-subtext="<?php echo _d(date('Y-m-01', strtotime("-11 MONTH"))); ?> - <?php echo _d(date('Y-m-t')); ?>"><?php echo _l('report_sales_months_twelve_months'); ?></option>
                           <option value="custom"><?php echo _l('period_datepicker'); ?></option>
                        </select>
                     </div>
                     <div id="date-range" class="hide mbot15">
                        <div class="row">
                           <div class="col-md-6">
                              <label for="report-from" class="control-label"><?php echo _l('report_sales_from_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" id="report-from" name="report-from">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                           <div class="col-md-6">
                              <label for="report-to" class="control-label"><?php echo _l('report_sales_to_date'); ?></label>
                              <div class="input-group date">
                                 <input type="text" class="form-control datepicker" disabled="disabled" id="report-to" name="report-to">
                                 <div class="input-group-addon">
                                    <i class="fa fa-calendar calendar-icon"></i>
                                 </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <div id="report" class="hide">
               <hr class="hr-panel-heading" />
               <h4 class="no-mtop"><?php echo _l('reports_sales_generated_report'); ?></h4>
               <hr class="hr-panel-heading" />

               <?php $this->load->view('admin/reports/includes/matter_client_wise'); ?>
               <?php $this->load->view('admin/reports/includes/matter_lawyer_wise'); ?>
               <?php $this->load->view('admin/reports/includes/matter_hearings'); ?>
                <?php $this->load->view('admin/reports/includes/matter_execution'); ?>
               <?php $this->load->view('admin/reports/includes/matter_lawyer_timesheets'); ?>
               <?php $this->load->view('admin/reports/includes/documents_expiry_report'); ?>
               <?php $this->load->view('admin/reports/includes/matter_detailed'); ?>
               <?php $this->load->view('admin/reports/includes/matter_litigation'); ?>
               <?php $this->load->view('admin/reports/includes/matter_litigation_update'); ?>
               <?php $this->load->view('admin/reports/includes/matter_others'); ?>
               <?php $this->load->view('admin/reports/includes/matter_updates'); ?>
               <?php $this->load->view('admin/reports/includes/agreements_report'); ?>
               <?php $this->load->view('admin/reports/includes/matter_cheque_bounce'); ?>
                 <?php $this->load->view('admin/reports/includes/matter_police_case'); ?>
               <?php $this->load->view('admin/reports/includes/matter_age_wise'); ?>
                <?php $this->load->view('admin/reports/includes/matter_settlement'); ?>
                 <?php $this->load->view('admin/reports/includes/matter_casenature'); ?>
                  <?php $this->load->view('admin/reports/includes/matter_activecase'); ?>
                   <?php $this->load->view('admin/reports/includes/matter_closecase'); ?>
                    <?php $this->load->view('admin/reports/includes/matter_individualcase'); ?>
                     <?php $this->load->view('admin/reports/includes/refundable_deposit'); ?>
                       <?php $this->load->view('admin/reports/includes/matter_labourcase'); ?>
                       <?php $this->load->view('admin/reports/includes/matter_legalaction'); ?>
                       <?php $this->load->view('admin/reports/includes/matter_litigation_country'); ?>
                         <?php $this->load->view('admin/reports/includes/matter_verification'); ?>
                           <?php $this->load->view('admin/reports/includes/matter_totalreceived'); ?>  
                            <?php $this->load->view('admin/reports/includes/matter_transfercase'); ?> 
                            <?php $this->load->view('admin/reports/includes/matter_companycase'); ?> 
                             <?php $this->load->view('admin/reports/includes/matter_handover'); ?>
                               <?php $this->load->view('admin/reports/includes/legalrequests_report'); ?> 
                               <?php $this->load->view('admin/reports/includes/receivable_agreements_report'); ?>
                               <?php $this->load->view('admin/reports/includes/payable_agreements_report'); ?>
                               <?php $this->load->view('admin/reports/includes/signed_agreements_report'); ?>
               </div>
            </div>
         </div>
      </div>
   </div>
</div>
</div>
<?php init_tail(); ?>
<?php $this->load->view('admin/reports/includes/matter_js'); ?>
</body>
</html>
