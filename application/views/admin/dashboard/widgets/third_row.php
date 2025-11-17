
<!--div class="row">
      <div class="col-md-12"-->
         <!-- <div class="panel">
            <div class="panel-body"> -->
            <!-- <div class="row">
      <div class="col-md-12"> -->
<?php #################Police Cases######################## 

$_where = 'status=1';
if (!has_permission('tickets', '', 'view')) {
    $_where .= ' AND assigned = ' . get_staff_user_id() ;   
}

$tickets_list  = $this->db->order_by('DATE(date)','desc')->limit(5)->select('ticketid,name,userid,subject,message,date')->from('tbltickets')->where($_where)->get()->result_array(); 
$tickets_count  = $this->db->from('tbltickets')->where($_where)->count_all_results();

  ?>
<div class="col-md-4 <?php if(!in_array(6, $active_boxes)) echo 'hide';  ?>">
  <div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading"><i class="fa fa-user-circle fa-lg" aria-hidden="true"></i> <?php echo _l('new').' '._l('legal_requests') ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $tickets_count ?></a> </div>
      <div class="panel-body alen-panel" >

        <ul class="list-group">
          <?php 
            if(sizeof($tickets_list) > 0){ 
              foreach ($tickets_list as $key => $value) { 
             ?>
              <li class="list-group-item <?php //echo $li_class; ?>">
                <span class="badge badge-dashboard hide"><?php echo date('Y M d',strtotime($value['date'])); ?><?php //echo $value['phonenumber']; ?></span>
                <a target="_blank" href="<?php echo admin_url('tickets/ticket/'.$value['ticketid']); ?>"><?php echo $value['subject']; ?></a>
                <p style="margin:0 0 5px;"><?php echo get_company_name($value['userid']); ?></p>
                <p  style="margin:0 0 5px;" ><strong><?php echo _l($value['name']);?></strong></p>
              </li>
              <?php }
           }else{ ?>
              <li class="list-group-item center_li">
                <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o" aria-hidden="true"></i></p>
              </li>
            <?php 
           } ?>
          
        </ul>
       
        
      </div>
      <div class="panel-footer panel-footer-height">
         <span class="" > 
          <a class="btn btn-link btn-sm " style="" target="_blank"  href="<?php echo admin_url('tickets') ?>"><?php echo _l('view_all_legal_requests'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
         </span> 
      </div>


      <!-- Table -->
       
       <!-- <div class="panel-footer">Panel footer</div> -->
  </div>

</div>   

<?php 
############   Box 2 Papers to be signed ################# ?>
<?php 
if(is_admin()){ 
$_where = 'expiry_date IS NOT NULL AND expiry_date != " " AND expiry_date BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 30 DAY)';

$project_documents_list  = $this->db->order_by('DATE(expiry_date)','desc')->limit(5)->select('id,document_type,expiry_date,subject,project_id')->from('tblproject_files')->where($_where)->get()->result_array();
$subfile_documents_list  = $this->db->order_by('DATE(expiry_date)','desc')->limit(5)->select('id,userid,document_type,expiry_date,subject,issue_date')->from('tblclient_subfile')->where($_where)->get()->result_array();        

$project_documents_count  = $this->db->from('tblproject_files')->where($_where)->count_all_results();
$subfile_documents_count  = $this->db->from('tblclient_subfile')->where($_where)->count_all_results();
$total_doc_count=$project_documents_count + $subfile_documents_count;?>


<?php 
$i=0;$doc_lists=[];
foreach ($project_documents_list as $key => $value) { 
  $value['related_to']='Project';
  $doc_lists[$i]=$value;
  $i++;
}
foreach ($subfile_documents_list as $key => $value) { 
  $value['related_to']='Client';
  $doc_lists[$i]=$value;
  $i++;
}?>

<div class="col-md-4 <?php if(!in_array(7, $active_boxes)) echo 'hide';  ?>">
  <div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading"><i class="fa fa-pencil-square fa-lg" aria-hidden="true"></i> <?php echo _l('documents_expiry'); ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $total_doc_count ?></a></div>
    <div class="panel-body alen-panel" >
      <ul class="list-group">
          <?php 
            if(sizeof($doc_lists) > 0){ 
              foreach ($doc_lists as $key => $value) { 
             ?>
              <li class="list-group-item <?php //echo $li_class; ?>">
              
              <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Document Expiry Date"><?php echo date($value['expiry_date']); ?></span>
                <p><?php echo $value['subject']; ?></p>
                <p>Related To : <?php echo $value['related_to']; ?></p>
                <?php if(isset($value['project_id'])) { ?><p style="margin:0 0 5px;"><a target="_blank" href="<?php echo admin_url('projects/view/'.$value['project_id']) ?>"><?php echo get_project_name_by_id($value['project_id']); ?></a></p> <?php } ?>
                <?php if(isset($value['userid'])) { ?><p style="margin:0 0 5px;"><a target="_blank" href="<?php echo admin_url('clients/client/'.$value['userid']) ?>"><?php echo get_company_name($value['userid']); ?></a></p> <?php } ?>
                 
              </li>
              <?php }
           }else{ ?>
              <li class="list-group-item center_li">
                <p><?php echo _l('no_data_found'); ?><i class="fa fa-frown-o" aria-hidden="true"></i></p>
              </li>
            <?php 
           } ?>
          
        </ul>
        
       
    </div>
     <div class="panel-footer panel-footer-height">
         <span class="" > 
          <a class="btn btn-link btn-sm " target="_blank"  href="<?php echo admin_url('defence_papers') ?>"><?php echo _l('view_all'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
         </span> 
      </div>

    

  </div>
</div>   
<?php } ?>
<?php ################## Box 2 end ####################################################### ?>       

<?php ########### Execution cases #####################################


    //if( has_permission('contracts','','view')){ 

      $where = ' dateend BETWEEN CURDATE() and DATE_ADD(CURDATE(), INTERVAL 30 DAY)';
    
    if(!has_permission('contracts','','view')){

      $where .= ' AND tblcontracts.id IN (SELECT contractid FROM ' . db_prefix() . 'contracts_assigned WHERE staff_id=' . get_staff_user_id() . ')';
    }

    $renewal_contract_list  = $this->db->order_by('DATE(tblcontracts.dateend)','desc')->limit(5)->group_by('tblcontracts.id')->select('tblcontracts.id,tblclients.company,tblcontracts.subject,tblcontracts.dateadded,tblcontracts.dateend,tblcontracts.contract_value,tblcontracts.final_expiry_date')->from('tblcontracts')->where($where)->join('tblprojects','tblprojects.clientid = tblcontracts.project_id','left')->join('tblclients','tblclients.userid = tblcontracts.client','left')->get()->result_array();
    $renewal_contract_count  = $contracts_count  = $this->db->from('tblcontracts')->where($where,NULL,FALSE)->count_all_results();


?>
<div class="col-md-4 <?php if(!in_array(8, $active_boxes)) echo 'hide';  ?>">
  <div class="panel panel-default">
    <!-- Default panel contents -->
    <div class="panel-heading"><i class="fa fa-paper-plane-o fa-lg" aria-hidden="true"></i>
    <?php echo _l('contract_renewal'); ?><a class="btn btn-primary btn-sm pull-right mb-4 count_link"> <?php echo $renewal_contract_count ?></a></div>
    <div class="panel-body alen-panel" >
        <ul class="list-group">
          <?php 
            if(sizeof($renewal_contract_list) > 0){ 
              foreach ($renewal_contract_list as $key => $value) { 
             ?>
              <li class="list-group-item <?php //echo $li_class; ?>">
                  <span class="badge badge-dashboard" data-toggle="tooltip" data-placement="top" title="Contract Renewal End date" ><?php echo $value['dateend']; ?></span>
                                  
                  <a  href="<?php echo admin_url('contracts/contract/'.$value['id']); ?>"><?php echo $value['subject']; ?></a>
                  <p class="alen-p" style="margin:0 0 5px;"><?php echo $value['contract_value']; ?> |  
                  <?php echo $value['company']; ?></p>
                 
              </li>
              <?php }
           }else{ ?>
              <li class="list-group-item center_li">

                <p><?php echo _l('no_data_found'); ?> <i class="fa fa-frown-o fa-2x" aria-hidden="true"></i></p>
              </li>
            <?php 
           } ?>
          
        </ul>
    </div>
     <div class="panel-footer panel-footer-height">
         <span class="" > 
          <a class="btn btn-link btn-sm"  target="_blank"  href="#"><?php echo _l('view_contract_renewals'); ?>  <i class="fa fa-arrow-right fa-lg mleft5" aria-hidden="true"></i></a>
         </span> 
      </div>

   
  </div>
</div>
<!-- </div>
</div> -->

