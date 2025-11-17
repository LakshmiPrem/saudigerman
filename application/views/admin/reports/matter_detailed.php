<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="content">
        <div class="row">
            <div class="col-md-12">
                <div class="panel_s">
                    <div class="panel-body">
                        <div class="row">
                           <?php //echo form_open(admin_url('reports/matter_detailed'),array('id'=>'det-form')); ?>
                              <div class="col-md-4">
                              <?php $selected=( isset($case_id) ? $case_id : ''); ?>
                                 <?php  echo render_select('case_id',$cases,array('id',array('proejct_name','company')),'projects',$selected);?>
                              </div>
                              <div class="clearfix"></div>
                           <?php //echo form_close(); ?>
                           </div>
                        </div>
                    </div>
                     <div class="panel_s">

                        <?php if($selected != ''){?>
                        <div class="panel-body">
                           
                           <div class="table-responsive">
                              <div class="pull-right">
                               <?php  if(has_permission('projects','','create')){ ?>
                              
                                 <a class="btn btn-info" href="<?php echo admin_url('projects/export_project_data/'.$case_id); ?>" target="_blank"><i class="fa fa-file-pdf-o"></i> <?php echo _l('export_project_data'); ?></a>
                        
                              <?php } ?>
                           </div> <br>
                              <h3><?php echo  _l('project_name') . ': ' . $project->name ; ?></h3>
                              <h3><?php echo ucwords(_l('project_overview')) ?></h3>
                                <div class="panel-body">
                              <div class="row"> <div class="col-md-12">
                              <?php if (!empty($project->description)) { ?>
                                 <p><b style="background-color:#f0f0f0;"><?= _l('project_description');?></b><br /><br /> <?= $project->description ?></p>
                                   <?php  } ?>
                             <?php if ($project->billing_type == 1) {
    $type_name = 'project_billing_type_fixed_cost';
} elseif ($project->billing_type == 2) {
    $type_name = 'project_billing_type_project_hours';
} else {
    $type_name = 'project_billing_type_project_task_hours';
} ?></div></div></div>
                              <table class="table">
                                 <thead>
                                    <tr>
                                       <th width="35%"><b style="background-color:#f0f0f0;"><?=_l('project_overview')?></b></th>
                                       <th width="35%"><b style="background-color:#f0f0f0;"><?=ucwords(_l('finance_overview'))?></b></th>
                                       <th width="30%"><b style="background-color:#f0f0f0;"><?=ucwords(_l('project_customer1'))?></b></th>
                                    </tr>
                                 </thead>
                                 <?php
                                 if ($project->billing_type == 1 || $project->billing_type == 2) {
    if ($project->billing_type == 1) {
        $html = '<b>' . _l('project_total_cost') . ': </b>' . app_format_money($project->project_cost, $project->currency_data) . '<br />';
    } else {
        $html = '<b>' . _l('project_rate_per_hour') . ': </b>' . app_format_money($project->project_rate_per_hour, $project->currency_data) . '<br />';
    }
}?>
                                 <tbody>
                                    <tr>
                                      <?php $casetypes=get_case_client_types();
												  foreach($casetypes as $case1){
													  if($case1['id']==$project->case_type){
														$type1=$case1['name'];  
													  }
													  
												  }?>
										<td><div class="col-md-6 mbot10"><b><?=_l('case_type')?></div><div class="col-md-6 mbot10"></b><?=$type1;?></div><br><div class="col-md-6">

                                       <?php $status = get_project_status_by_id($project->status); ?>
                                       <b><?=_l('project_status') ?></div><div class="col-md-6 mbot10"></b><?=$status['name']?></div><br><div class="col-md-6 mbot10">

                                       <b><?=_l('legal_requestdt') ?></div><div class="col-md-6 mbot10"></b>  <?php if($project->project_created!='0000-00-00') echo _d($project->project_created); else echo 'Not mentioned'; ?></div><br><div class="col-md-6 mbot10">
                                       <b><?=_l('project_start_date') ?></div><div class="col-md-6 mbot10"></b><?php if($project->start_date!=" ") echo _d($project->start_date); else echo 'Not mentioned'; ?></div><br>
                                    
                                       
                                       </td>


                                       <td>
                                       <div class="col-md-6 mbot10">
										   <b><?=_l('claiming_amount') ?></div><div class="col-md-6 mbot10"></b><?=app_format_money($project->claiming_amount, $project->currency_data)?></div><div class="col-md-6 mbot10">
                                          
                                           <b><?=_l('project_overview_expenses') ?></div><div class="col-md-6 mbot10"></b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id], 'field' => 'paid_amount']), $project->currency_data) ?></div><div class="col-md-6 mbot10">
									
									
                                    <b><?=_l('total_amount') ?></div><div class="col-md-6 mbot10"></b><?php
											$exp=sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id], 'field' => 'paid_amount']);
											$tamount=app_format_money($project->claiming_amount+$exp, $project->currency_data);echo $tamount; ?></div><div class="col-md-6 mbot10">
                                          <b><?=_l('execution_amount') ?></div><div class="col-md-6 mbot10"></b><?=app_format_money($project->execution_amount, $project->currency_data)?></div><div class="col-md-6 mbot10">
                                          <b><?=_l('settlement_amount') ?></div><div class="col-md-6 mbot10"></b><?=app_format_money($project->outstanding_amount, $project->currency_data)?></div><div class="col-md-6 mbot10">
                                          <b><?=_l('project_overview_settle_paid') ?></div><div class="col-md-6 mbot10"></b>
                                          <?php echo app_format_money(sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status'=>'paid'),'field'=>'amount_received'))+sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'partially_paid'),'field'=>'amount_received')), $project->currency_data); ?>
                                         </div><div class="col-md-6 mbot10">
                                           <b><?=_l('project_overview_settle_balance') ?></div><div class="col-md-6 mbot10"></b><?php
												$total=$project->claiming_amount+sum_from_table(db_prefix().'expenses',array('where'=>array('project_id'=>$project->id),'field'=>'paid_amount'));
												  $paid1=sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'paid'),'field'=>'amount_received'));
												  $paid2=sum_from_table(db_prefix().'recoveries_installments',array('where'=>array('recovery_id'=>$project->id,'installment_status '=>'partially_paid'),'field'=>'amount_received'));
												  $bal=$total-($paid1+$paid2);
												  echo app_format_money($bal, $project->currency_data); ?></div>

       <!--
                                    <b><?=_l('project_overview_expenses_refund') ?>:</b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'invoiceid !=' => 'NULL', 'billable' => 1], 'field' => 'amount']), $project->currency_data)?><br>

                                     <b><?=_l('project_overview_expenses_unbilled') ?>:</b><?=app_format_money(sum_from_table(db_prefix() . 'expenses', ['where' => ['project_id' => $project->id, 'invoiceid IS NULL', 'billable' => 1], 'field' => 'amount']), $project->currency_data)?>-->

                                       </td>

                                       <td>
                                        <div class="col-md-6 mbot10">
										   <b><?=_l('casediary_oppositeparty') ?></div><div class="col-md-6 mbot10"></b><?=  get_opposite_party_name($project->opposite_party);?></div>
                                        <div class="col-md-6 mbot10">
										   <b><?=_l('ledger_code') ?></div><div class="col-md-6 mbot10"></b><?= $project->ledger_code;?></div>
                                      <div class="col-md-12 mbot10" >
                                          <b><?=$project->client_data->company?></b>
                                          <b><?=$project->client_data->address?></b><br>
                                         <?php if (!empty($project->client_data->city)) { 
                                                echo $project->client_data->city;
                                         } ?>

                                         <?php if (!empty($project->client_data->state)) {
                                              echo $project->client_data->state;
                                          }?>
                                          <?php $country = get_country_short_name($project->client_data->country);?>
                                          <?php 
                                          if (!empty($country)) {
                                              echo "<br>".$country;
                                          }?>

                                         <?php  if (!empty($project->client_data->zip)) {
                                              echo $project->client_data->zip;
                                             }

                                          if (!empty($project->client_data->phonenumber)) {
                                              echo  "<br />".$project->client_data->phonenumber;
                                          }

                                          if (!empty($project->client_data->vat)) {
                                             echo  "<br>"._l('client_vat_number') . ': ' . $project->client_data->vat;
                                          } ?>
                                          </div>
                                       </td>
                                    </tr>
                                    <tr>
                                    	<td> <p class="bold font-size-14 project-info"><?php echo _l('lawyer_attending'); ?></p></td>
                                    	<td></td>
                                    	<td> <p class="bold font-size-14 project-info"><?php echo _l('legal_coordinator'); ?></p></td>
                                    </tr>
                                    <tr>
                                    	<td><?php if(count($asslawyers) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_projectlawyer') . '</p>';
   }
	 foreach($asslawyers as $plawyer){
		   echo '<a href="#"><b>'.get_staff_full_name($plawyer['assigneeid']).'</b></a><br>';
	   }?></td>
                                    	<td></td>
                                    	<td><?php if(count($legals) == 0){
      echo '<p class="text-muted no-mbot mtop15">' . _l('no_description_projectlegal') . '</p>';
   }
	  
	 foreach($legals as $plegal){
		   echo '<a href="#"><b>'.get_staff_full_name($plegal['legal_ids']).'</b></a><br>';
	   }?></td>
                                    </tr>
                                 </tbody>
                              </table>

<!------------------------------->

<div class="panel-group" id="accordion">
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion" href="#collapseOne">
           <?=ucwords(_l('court_instance_details'))?>
        </a>
      </h4>
    </div>
    <div id="collapseOne" class="panel-collapse collapse in">
      <div class="panel-body">
         <table class="table dt-table table-bordered table-striped" width="100%"  cellspacing="0" cellpadding="5" border="1">
            <thead>
               <tr>
      
                  <th><b><?=_l('court_instance')?></b></th>
                  <th><b><?=_l('case_no')?></b></th>
                  <th><b><?=_l('case_nature')?></b></th>
                  <th><b><?=_l('hearing_court')?></b></th>
                  <th><b><?=_l('claiming_amount')?></b></th>
                  <th><b><?=_l('judgement_amount')?></b></th>
                 
               </tr>

            </thead>
            <tbody>
              <?php //if(isset($scope)){
      foreach ($court_instances as $row_) { ?>
        <tr>
       
        <td><?=$row_['instance_name']?></td>
         <td><?=$row_['case_number']?></td>
          <td><?=$row_['case_nature_name']?></td>
           <td><?=$row_['courtname']?></td>
            <td><?=app_format_money($row_['claiming_amount'], $project->currency_data)?></td>
              <td><?php if($row_['instance_id']!='5')echo app_format_money($row_['execution_amount'], $project->currency_data); else echo '';?></td>
        
              
       
      </tr>
      <?php }//} ?>  
            </tbody>
         </table>
      </div>
    </div>
  </div>

  <!---------------hearings ------------------------------------->

  <?php foreach ($hearing_types as $key => $hearing_type) {
    $num_rows = total_rows('tblhearings',array('project_id'=>$project->id,'h_instance_id'=>$hearing_type['id']));
											
    if($num_rows > 0){ 
      $court_no = $hearing_type['id'].'_no';
   ?>
  <!--<div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapse<?=$hearing_type['id']?>">
          <?=$hearing_type['name']?>
        </a>
      </h4>
    </div>
    <div id="collapse<?=$hearing_type['id']?>" class="panel-collapse collapse in">
      <div class="panel-body">
         <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="asc">
        <thead>
    <tr> 
               
      <th><?php echo _l('hearing_date'); ?></th>
      <th><?php echo _l('hearing_list_subject'); ?></th>
      <th><?php echo _l('client'); ?></th>
      <th><?php echo _l('casediary_casenumber'); ?></th>
    <!--  <th><?php echo _l($court_no); ?></th>
      <th><?php echo _l('casediary_oppositeparty'); ?></th>
      <th><?php echo _l('lawyer_attending'); ?></th>
      <th><?php echo _l('court_decision'); ?></th>

    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($hearings as $row_hearing) {
        if($row_hearing->h_instance_id == $hearing_type['id']){
        ?>
        <tr>
        <td><?=_d($row_hearing->hearing_date)?></td>
        <td><?=$row_hearing->subject?></td>
        <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?=$project->client_data->company?></a></td>
        <td><?=$row_hearing->court_no?></td>
        <td><?=$row_hearing->opposite_party_name?></td>
         <td><?=get_staff_full_name($row_hearing->lawyer_id)?></td>
        <td><?=$row_hearing->proceedings?></td>
      </tr>
      <?php }} ?>  
  </tbody>
 </table>
      </div>
    </div>
  </div>-->
<?php } }?>
<!-------------------------------------------------------------------->
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseThree">
         <?=ucwords(_l('project_court_attach_grant'))?>
        </a>
      </h4>
    </div>
    <div id="collapseThree" class="panel-collapse collapse">
      <div class="panel-body">
           <table class="table dt-table table-bordered table-striped" width="100%"  cellspacing="0" cellpadding="5" border="1">
            <thead>
               <tr>
      
                  <th><b><?=_l('corder_type')?></b></th>
                  <th><b><?=_l('corder_date')?></b></th>
                  <th><b><?=_l('corder_amount')?></b></th>
                
                 
               </tr>

            </thead>
            <tbody>
              <?php //if(isset($scope)){
      foreach ($court_order as $row_) { ?>
        <tr>
       
        <td><?=get_document_type_name($row_['documentid']);?></td>
         <td><?=_d($row_['order_date'])?></td>
         <td><?=app_format_money($row_['corder_amount'], $project->currency_data)?></td>
       
       
      </tr>
      <?php }//} ?>  
            </tbody>
		  </table>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFour">
         <?=ucwords(_l('update_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseFour" class="panel-collapse collapse">
      <div class="panel-body" style="height: 300px;overflow: scroll">
         <table class="table dt-table table-bordered table-striped" width="100%"  cellspacing="0" cellpadding="5" border="1">
            <thead>
               <tr>
      
                  <th width="30%"><b><?=_l('date_added')?></b></th>
                  <th width="70%"><b><?=_l('details')?></b></th>
                 
                 
               </tr>

            </thead>
            <tbody>
              <?php //if(isset($scope)){
      foreach ($case_updates as $rowu) { ?>
        <tr>
       
        <td><?=_d($rowu['dateadded'])?></td>
         <td><?=$rowu['content']?></td>
            
              
       
      </tr>
      <?php }//} ?>  
            </tbody>
         </table>
      </div>
    </div>
  </div>

  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseFive">
         <?=ucwords(_l('project_expense_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseFive" class="panel-collapse collapse">
      <div class="panel-body">
        <?php 
         $tblhtml = '';
        $tblhtml .= ' <table class="table dt-table table-bordered table-striped" width="100%"  cellspacing="0" cellpadding="5" border="1">
<thead>
 <tr>  
    <th width="20%" style="text-align:center;"><b>'._l('categories').'</b></th>
	<th width="20%" style="text-align:center;"><b>'._l('total_amount').'</b></th>
    <th width="20%" style="text-align:center;"><b>'._l('paid_amount').'</b></th>
    <th width="20%" style="text-align:center;"><b>'._l('now_payable').'</b></th>
	<th width="20%" style="text-align:center;"><b>'._l('bal_payable').'</b></th>
   
  </tr></thead>';
$total_amount = $total_paid =$total_balance=$total_lastpaid=$tvat=$tpay=0;
foreach ($expenses as  $expenses) {
$bpaid=$expenses['amount']-($expenses['paid_amount']+$expenses['last_amount']+$expenses['vat_amount']);
	$npayable=$expenses['last_amount']+$expenses['vat_amount'];
  $tblhtml .='<tr>';
  $tblhtml .= '<td width="20%">'.$expenses['category_name'].'</td>';
	
  $tblhtml .= '<td width="20%">'.number_format($expenses['amount'],2).'</td>';
  $tblhtml .= '<td width="20%">'.number_format($expenses['paid_amount'],2).'</td>';
  $tblhtml .= '<td width="20%">'.number_format($npayable,2).'</td>';
  $tblhtml .= '<td width="20%">'.number_format($bpaid,2).'</td>';
 
  $tblhtml .= '</tr>';

  $total_amount += $expenses['amount'];
  $total_paid += $expenses['paid_amount'];
  $total_lastpaid += $expenses['last_amount'];
  $total_balance += $bpaid;
	$tvat+=$expenses['vat_amount'];
	
}; 
$tpay=$total_lastpaid+$tvat;
$total_bpaid=$total_paid-$total_lastpaid;
$tblhtml .= '<tr><td colspan="5"></td></tr><tr>
                <td width="20%"><b>Total Expenses</b></td>
				<td width="20%"><b>'.number_format($total_amount,2).'</b></td>
                <td width="20%"><b>'.number_format($total_paid,2).'</b></td>
                <td width="20%"><b>'.number_format($tpay,2).'</b></td>
				<td width="20%"><b>'.number_format($total_balance,2).'</b></td>
              
            </tr>';
  
$tblhtml .= '<tbody></tbody>';
$tblhtml .= '</table> <br />';
         echo $tblhtml;
         ?>
      </div>
    </div>
  </div>

<!-- 
  <div class="panel panel-default">
    <div class="panel-heading">
      <h4 class="panel-title">
        <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#accordion" href="#collapseSix">
         <?=ucwords(_l('detailed_overview'))?>
        </a>
      </h4>
    </div>
    <div id="collapseSix" class="panel-collapse collapse">
      <div class="panel-body">
        Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
      </div>
    </div>
  </div> -->


</div>

<!------------------------------->











   

                           </div>
                                    
                                   
                        </div>
                     <?php } ?>
                     </div>
                    </div>
                </div>
            </div>
        </div>
        <?php init_tail(); ?>
</body>
</html>
<script type="text/javascript">
   $('#case_id').change(function(){
        var case_id = $(this).val();
        window.location.href= admin_url+'reports/matter_detailed/'+case_id;
   });
</script>

<style type="text/css">
   .panel-heading .accordion-toggle:after {
    /* symbol for "opening" panels */
    font-family: 'Glyphicons Halflings';  /* essential for enabling glyphicon */
    content: "\e114";    /* adjust as needed, taken from bootstrap.css */
    float: right;        /* adjust as needed */
    color: grey;         /* adjust as needed */
}
.panel-heading .accordion-toggle.collapsed:after {
    /* symbol for "collapsed" panels */
    content: "\e080";    /* adjust as needed, taken from bootstrap.css */
}
</style>