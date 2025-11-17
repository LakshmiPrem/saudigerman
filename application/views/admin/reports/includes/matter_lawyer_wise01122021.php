    <div id="matter-lawyers-report" class="hide">
      <div class="row">
         
         <div class="col-md-4">
            <div class="form-group">
                 <?php echo render_select('lawyerid2',$lawyers_arr,array('staffid','full_name'),'lawyer_assigned','');?>
            </div>
         </div>
         <div class="clearfix"></div>
      </div>
         <table class="table table-matter-lawyer-report scroll-responsive">
            <thead>
               <tr>
                  <th><?php echo _l('project_customer'); ?></th>
                  <th><?php echo _l('case_title'); ?></th>
                  <th><?php echo _l('project_members');?></th>
                 <!--  <th><?php echo _l('casediary_oppositeparty'); ?></th> -->
                  <th><?php echo _l('casediary_file_no'); ?></th>
                  <th><?php echo _l('project_start_date'); ?></th>
                  <th><?php echo _l('case_number'); ?></th>
                  <th><?php echo _l('project_status'); ?></th>
               </tr>
            </thead>
            <tbody></tbody>
            <tfoot>
               <tr>
                  <td></td>
                  <td></td>
                  <td></td>
                  <!-- <td></td> -->
                  <td></td>
                  <td></td>
                  <td></td>
                  <td></td>
               </tr>
            </tfoot>
         </table>
   </div>
