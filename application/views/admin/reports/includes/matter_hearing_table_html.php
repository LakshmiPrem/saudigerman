<table class="table table-matter-hearing-report scroll-responsive">

   <thead>

         <tr>

            <th><?php echo _l('file_no'); ?></th>

            <th><?php echo _l('casediary_casenumber'); ?></th>

            <th><?php echo _l('litigation_report_client'); ?></th>

            <th><?php echo _l('litigation_report_opposite'); ?></th>

           <!-- <th><?php echo _l('litigation_report_suit_type'); ?></th>

            <th><?php echo _l('hearing_court'); ?></th>-->

             <th class="<?php echo (get_option('enable_lawyer_in_role_report') == 1 ) ? ' ' : 'not_visible not-export'; ?>"><?php echo _l('lawyer_attending');?></th>

            <th><?php echo _l('litigation_report_previous_session'); ?></th>

            <th><?php echo _l('litigation_report_next_session'); ?></th>

            <th><?php echo _l('litigation_report_order_request'); ?></th>

            <th><?php echo _l('litigation_report_court_decision'); ?></th>                  

         </tr>

      </thead>

      <tbody></tbody>

      <tfoot>

         <tr>

            <td></td>

           <!-- <td></td>

            <td></td>-->

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