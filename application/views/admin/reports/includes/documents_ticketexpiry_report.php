    <div id="documents-expiry" class="hide">
      <div class="row">
         
     
            <div class="col-md-4">
            <div class="form-group">
               <label for="sale_agent_invoices"><?php echo _l('clients'); ?></label>
                <select id="clientid" name="clientid14" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
               </select>
            </div>
         </div>
           <div class="col-md-4">
           <div class="form-group">
                     <label class=""><?php echo _l('document_type'); ?></label>
                  <select class="form-control selectpicker" id="document_type" name="document_type">
                     <option value=""><?php  echo _l('all');?></option>
                     <?php foreach($document_types as $doc_type){ ?>
                        <option value="<?=$doc_type['id']?>"><?=$doc_type['name']?></option>
                     <?php } ?>
                  </select>
                  </div>
		  </div>
         <div class="clearfix"></div>
      </div>
         <table class="table table-documents-expiry-report scroll-responsive">
            <thead>
               <tr>
                  <th class="not_sortable">Sr. No.</th>
                 <!-- <th><?php echo _l('subject'); ?></th>-->
                  <th><?php echo _l('client');?></th>
                   <th><?php echo _l('customer_name');?></th>
                    <th><?php echo _l('customer_code');?></th>
                  <th><?php echo _l('reference_no');?></th>
                  <th><?php echo _l('document_type');?></th>
                   <th><?php echo _l('document_number');?></th>
                    <th><?php echo _l('document_name');?></th>
                  <th><?php echo _l('nationality'); ?></th>
                  <th><?php echo _l('expiry_date'); ?></th>
                 
               </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
               <tr>
                 <td></td>
                 <td></td>
                 <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                  
               </tr>
            </tfoot>
         </table>
   </div>
