<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Project Tasks -->

  <div class="col-md-12">
          
               <div class="row">
      <div class="col-md-3">
                <?php echo form_hidden('projectid',$project->id) ?>
                 <?php $value=( isset($project) ? $project->claiming_amount: ''); ?>
                        <?php echo render_input('claiming_amount', 'claiming_amount',$value,'number'); ?>
              </div>
                

          
              
           
              <div class="col-md-3">
                <button type="button" style="margin-top: 25px;" id="btn_payinstallment" class="btn btn-info">Save</button>
              </div>
            </div> 
            <hr> 
                      <div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>" >
               <a href="#" onclick="payinstallment(<?php echo $project->id; ?>); return false;" class="btn btn-info new-contact mbot25"><?php echo _l('new_payment_schedule'); ?></a>

              
            </div>
             <div style="float: right;">
                   
                 </div>
                             <div class="row mbot15">
          
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
                <h3 class="bold"><span id="def_credit_limit"><?php echo app_format_money($project->claiming_amount,$this->projects_model->get_currency($project->id)); ?></span></h3>
                <span class="text-dark"><?php echo _l('claiming_amount'); ?></span>
            </div>

            <?php 
             $totalpaid = 0;
              $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblpayment_schedule` WHERE project_id = ? AND installment_status = ? AND project_type = ?',array($project->id,'paid','project'))->row();
             if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
             }
 $bal=$project->claiming_amount - $totalpaid;

            ?>
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
                <h3 class="bold"><span id="def_total_paid"><?php echo app_format_money($totalpaid,$this->projects_model->get_currency($project->id)); ?></span></h3>
                <span class="text-dark"><?php echo _l('total_paid'); ?></span>
            </div>
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
              
                <h3 class="bold"><span id="def_balance"><?php echo app_format_money($bal,$this->projects_model->get_currency($project->id)); ?></span></h3>
                <span class="text-dark"><?php echo _l('balance'); ?></span>
            </div>
          </div>

 <?php
             $table_data = array(
              _l('installment_date'),
              _l('installment_amount'),
              _l('installment_status'),
              _l('is_verified'),
              _l('verified_by'),
              _l('verified_date'),
              _l('remarks')
            );
            array_push($table_data,_l('options'));
            echo render_datatable($table_data,'payinstallments'); ?>

</div>
<div id="contact_data"></div>