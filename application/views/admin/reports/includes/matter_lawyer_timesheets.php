    <div id="matter-lawyers-timesheets" class="hide">
      <div class="row">
         
       <!--   <div class="col-md-4">
            <div class="form-group">
                <?php echo render_select('lawyerid2',$lawyers_arr,array('staffid','full_name'),'lawyer_attending','');?>
            </div>
         </div> -->
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-lawyer-timesheets-report scroll-responsive">
            <thead>
               <tr>
                  <th><?php echo _l('staff_member'); ?></th>
                  <th><?php echo _l('time_h').' '._l('billable'); ?></th>
                  <th><?php echo _l('time_h').' '._l('non_billable');?></th>
                  <th><?php echo _l('total'); ?></th>
               
                  
               </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
                
               </tr>
            </tfoot>
         </table>
   </div>
