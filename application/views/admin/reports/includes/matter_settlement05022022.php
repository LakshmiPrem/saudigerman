    <div id="matter-settlement" class="hide">
       <div class="row">
         
         <div class="col-md-3">
            <div class="form-group">
               
                <?php echo render_select('nature_type',$settle_nature,array('id','name'),'nature_type','');
				
				?>
            </div>
         </div>
         <div class="clearfix"></div>
      </div> 
        
         <table class="table table-matter-settlement-report scroll-responsive">
            <thead>
               <tr> 
                  <th><?php echo _l('installment_date'); ?></th>
                   <th><?php echo _l('installment_amount'); ?></th>
                  <th><?php echo _l('installment_status'); ?></th>
                  <th><?php echo _l('matter_details'); ?></th>
                  <th><?php echo _l('company'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                  <th><?php echo _l('remarks'); ?></th>
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
                 
               </tr>
            </tfoot>
         </table>
   </div>
