<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<!-- Project Tasks -->
    <div class="panel_s">
        <div class="panel-body">
              <div class="row">
                <?php echo form_open(admin_url('projects/reset_installments/'.$project->id),array('id'=>'installment-info-form',)); ?>
               
                <div class="col-md-3">
              
                 <?php $value=( isset($project) ? $project->claiming_amount: ''); ?>
                        <?php echo render_input('claiming_amount', 'claiming_amount',$value,'number'); ?>
                </div>
                <div class="col-md-3">
              
                 <?php $value=( isset($project) ? $project->execution_amount: ''); ?>
                        <?php echo render_input('execution_amount', 'execution_amount',$value,'number'); ?>
                        </div>
                    <div class="col-md-3">
                          <?php $value=( isset($project) ? $project->execution_percent: ''); ?>
                         <?php echo render_input('execution_percent', 'interest_rate',$value,'number'); ?>
                     </div>
               <div class="col-md-3">
                         <?php $value = (isset($project) ? _d($project->execution_duedate) : ''); ?>
                          <?php echo render_date_input('execution_duedate', 'execution_duedate',$value); ?>
              </div>
                
                 <div class="col-md-3">
              
                 <?php $value=( isset($project) ? $project->outstanding_amount: ''); ?>
                        <?php echo render_input('outstanding_amount', 'settlement_amount',$value,'number'); ?>
              </div>
                 <div class="col-md-3">
                
                    <?php $value=( isset($client) ? _d($project->installment_start_date) : _d(date('Y-m-d'))); ?>
               <?php echo render_date_input( 'installment_start_date', 'installment_start_date',$value); ?>
              </div>
                 <div class="col-md-3">

            <?php $selected = (isset($project) ? $project->nature_of_settlement : '');
            
           
            echo render_select('nature_of_settlement',$settle_nature,array('id','name'),'nature_of_settlement',$selected);
            ?>
          </div>
                     <?php ########## settlement type##############  ?>
          <div class="col-md-3">
            <?php echo form_hidden('projectid',$project->id) ?>
            <?php $selected = (isset($project) ? $project->settlement_type : '');
            
           
            echo render_select('settlement_type',$settle_type,array('id','name'),'settlement_type',$selected);
            ?>
          </div>
               <div class="col-md-3">
              
                 <?php $value = (isset($project) ? $project->no_of_installment : '');?>
                        <?php echo render_input('no_of_installment', 'no_of_installment',$value,'number'); ?>
              </div>
                <div class="col-md-6">
              
                 <?php $value = (isset($project) ? $project->installment_desc : '');?>
                        <?php echo render_textarea('installment_desc', 'project_description',html_entity_decode($value, ENT_COMPAT, 'UTF-8')); ?>
                        
              </div>
              
              <div class="col-md-3">
                <button type="submit" style="margin-top: 25px;" id="btn_installment" class="btn btn-info">Save</button>
              </div>
              </form>
            </div> 
        </div>
    </div>
  <div class="col-md-12">
         
             
            
            <hr> 
                   <!--   <div class="inline-block new-contact-wrapper" data-title="<?php echo _l('customer_contact_person_only_one_allowed'); ?>" >
               <a href="#" onclick="installment(<?php echo $project->id; ?>); return false;" class="btn btn-info new-contact mbot25"><?php echo _l('new_installment'); ?></a>

              
            </div>-->
             <div style="float: right;">
                   
                 </div>
                             <div class="row mbot15">
          
            <div class="col-md-4 col-xs-6 border-right " style="height: 110px;">
                <h3 class="bold"><span id="def_credit_limit"><?php echo app_format_money($project->outstanding_amount,$this->projects_model->get_currency($project->id)); ?></span></h3>
                <span class="text-dark"><?php echo _l('outstanding_amount'); ?></span>
            </div>

            <?php 
             $totalpaid = 0;
			 $bal=0;
              $totalpaid_qry = $this->db->query('SELECT SUM(installment_amount) as totalpaid FROM `tblrecoveries_installments` WHERE recovery_id = ? AND installment_status = ? AND recovery_type = ?',array($project->id,'paid','project_recovery'))->row();
             if($totalpaid_qry->totalpaid > 0){
                $totalpaid = $totalpaid_qry->totalpaid;
             }
 $bal=$project->outstanding_amount - $totalpaid;

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
               _l('#'),
              _l('installment_date'),
              _l('installment_amount'),
              _l('amount_received'),
              _l('installment_status'),
              _l('is_verified'),
              _l('verified_by'),
              _l('verified_date'),
              _l('remarks')
            );
            array_push($table_data,_l('options'));
            echo render_datatable($table_data,'installments'); ?>
            <div class="col-md-12 text-center">
                            <button type="button"   id="btn_save_installment_table_" class="btn btn-info center">Save</button>

                           <!--  <a href="#" onclick="installment(<?php echo $project->id; ?>); return false;" class="btn btn-info new-contact center"><?php echo _l('new_installment'); ?></a>-->

            </div>


</div>
<div id="contact_data"></div>