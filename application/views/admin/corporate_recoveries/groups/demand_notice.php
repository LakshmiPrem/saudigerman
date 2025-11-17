<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('demand_notice'); ?></h4>
<div class="col-md-12">

  <a  href="<?php echo admin_url('corporate_recoveries/generate_demand_notice_word/'.$client->userid); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('proposal_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i> <?php echo _l('generate_demand_notice'); ?></a>
<?php 
// Check Demand Notice generated or not ?
$num_ro = $this->db->get_where('tbldebt_demandnotice',array('defaulter_id'=>$client->userid,'rel_name'=>'corporate'));

$path        = base_url('uploads/corporate_recovery/').$client->userid.'/';
$file_path   = get_upload_path_by_type('corporate_recovery').$client->userid.'/';

if(file_exists($file_path.'Demand Notice.docx')){ ?>
 

  <a href="<?php echo $path.'Demand Notice.docx'; ?>"  class="btn btn-danger btn-with-tooltip" data-toggle="tooltip" download title="<?php echo _l('demand_notice'); ?>" data-placement="bottom"><i class="fa fa-file-word-o" aria-hidden="true"></i> Download Demand Notice</a>

  <!-- <button type="button" data-toggle="modal" data-original-file-name="<?php echo ''; ?>" data-filetype="<?php echo ''; ?>" data-path="<?php echo ''; ?>" data-target="#send_demand_notice" class="btn btn-info btn-icon "><i class="fa fa-envelope"></i> Send Demand Notice</button> -->

 <div class="clearfix"></div>
 
<div class="row">
     <hr class="hr-panel-heading" />
</div>
<?php }else{?>
  <!-- <a  href="<?php echo admin_url('corporate_recoveries/generate_demand_notice_word/'.$client->userid); ?>" class="btn btn-default btn-with-tooltip" data-toggle="tooltip" title="<?php echo _l('proposal_pdf'); ?>" data-placement="bottom"><i class="fa fa-file-pdf-o"></i> <?php echo _l('generate_demand_notice'); ?></a> -->
<?php } ?>
 <div class="clearfix"></div>
 <div class="usernote hide">
    <?php echo form_open(admin_url( 'corporate_recoveries/save_demand_notice/'.$client->userid)); ?>
    <div class="row">
        <div class="col-md-8">
          <?php  
          $defaulter_address  = $client->full_name;
          $defaulter_address .="\r\n";
          $defaulter_address .= $client->address;
          $value=( isset($demand_notice) ? $demand_notice->notice_to : $defaulter_address); ?>
          <?php echo render_textarea( 'notice_to', 'to',$value,array( 'rows'=>4)); ?>
        </div>
        <div class="col-md-4 mtop35">
        <?php $value=( isset($demand_notice) ? _d($demand_notice->demand_notice_date) : date('Y-m-d')); ?>
        <?php echo render_date_input('demand_notice_date', 'demand_notice_date',$value); ?>
        </div>
        <div class="col-md-12">
          <?php 
          $subject = 'Demand Notice regarding unpaid overdue invoices availed by'.$client->full_name.', from '.get_company_name($client->client_id);


          $value=( isset($demand_notice) ? $demand_notice->subject :  $subject ); ?>
          <?php echo render_textarea( 'subject', 'subject',$value,array( 'rows'=>5),array(),'','');  ?>
        </div>
        <div class="col-md-12">
          <?php 
          $content = '
We, being the Power of Attorney of the '.get_company_name($client->client_id) .' ( Here in after called “Client”.), incorporated in UAE under the laws of the United Arab Emirates and the constituted attorney to initiate recovery proceedings and for managing, handling and conducting all the legal issues in on behalf of the Client and to represent them in its legal proceedings including civil and criminal litigations.   
Under instructions from the Client, we are hereby issuing this Demand Notice to you seeking final settlement of the liability. The firm’s liability currently stands to AED 84,585.00/- ( AED  Eighty Four Thousand Five Hundred and Eighty Five Only) , which they have unscrupulously  defaulted and neglected to repay without any intimation to the Client.
Therefore, we hereby call upon you and demand you to settle the aforesaid sums and resolve this issue once for all, within 07 days from the date of receipt of this notice by you.   Otherwise, as the Power of Attorney, we will be constrained to proceed by invoking both civil and criminal proceedings against you without any further reference to you and all the additional expenses incurred will be on your cost.  
Thanking you, 
Yours faithfully 
For '.get_option("invoice_company_name"); 
          $value=( isset($demand_notice) ? $demand_notice->notice_content : $content); ?>
          <?php echo render_textarea('notice_content', 'notice_content',$value ,array('rows'=>15),array(),'','');  ?>
        </div>
        <div class="col-md-12">
          <?php 
          $footer = 'Brijesh
Executive Director';
          $value=( isset($demand_notice) ? $demand_notice->footer : $footer); ?>
          <?php echo render_textarea( 'footer', 'footer',$value,array('rows'=>2),array(),'','');  ?>
        </div>
    </div>
   
    <button class="btn btn-info pull-right mbot15">
        <?php echo _l( 'submit'); ?>
    </button>
    <?php echo form_close(); ?>
</div>
<?php } ?>
<?php //include_once(APPPATH . 'views/admin/recoveries/modals/send_demand_notice_modal.php'); ?>
