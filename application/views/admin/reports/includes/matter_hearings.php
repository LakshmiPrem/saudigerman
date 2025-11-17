    <div id="matter-hearings" class="hide">
       <div class="row">
         
         <div class="col-md-3">
            <div class="form-group">
                <?php echo render_select('hearing_type',$hearing_types,array('id','name'),'hearing_type','');?>
            </div>
         </div>
         <div class="clearfix"></div>
      </div> 
         <table class="table table-matter-hearing-report scroll-responsive">
            <thead>
               <tr>
                  <th><?php echo _l('hearing_date'); ?></th>
                  <th><?php echo _l('hearing_list_subject'); ?></th>
                  <th><?php echo _l('hearing_type'); ?></th>
                  <th><?php echo _l('hearing_no'); ?></th>
                  <th><?php echo _l('client');?></th>
                  <th><?php echo _l('casediary_casenumber'); ?></th>
                  <th><?php echo _l('casediary_oppositeparty'); ?></th>
                  <th><?php echo _l('casediary_hallnumber'); ?></th>
                  <th><?php echo _l('hearing_court'); ?></th>
                  <th><?php echo _l('proceedings'); ?></th>
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
                  <td></td>
               </tr>
            </tfoot>
         </table>
   </div>
