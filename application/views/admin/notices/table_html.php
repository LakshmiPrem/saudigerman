<?php defined('BASEPATH') or exit('No direct script access allowed');?>
<a href="#" data-toggle="modal" data-target="#notice_bulk_action" class="bulk-actions-btn table-btn hide" data-table=".table-notices"><?php echo _l('bulk_action'); ?></a>

<div class="modal fade bulk_actions" id="notice_bulk_action" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">
         <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <h4 class="modal-title"><?php echo _l('bulk_actions'); ?></h4>
         </div>
         <div class="modal-body">
            
              <div id="mass_notice_div">
               <div class="checkbox" >
                    <input type="checkbox" name="mass_notice" id="mass_notice" checked>
                    <label for="mass_notice"><?php echo _l('mass_notice'); ?></label>
                </div>

              
                
                <?php  echo render_select('mass_notice_status',$notice_statuses,array('id','name'),'status'); ?>
		 
              </div>
            
            

            
         </div>
         <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo _l('close'); ?></button>
            <a href="#" class="btn btn-info" onclick="notice_bulk_action(this); return false;"><?php echo _l('confirm'); ?></a>
         </div>
      </div>
      <!-- /.modal-content -->
   </div>
   <!-- /.modal-dialog -->
</div>
<!-- /.modal -->
<?php
$table_data = array(
	   'name'=>'<span class="hide"> - </span><div class="checkbox mass_select_all_wrap"><input type="checkbox" id="mass_select_all" data-to-table="notices"><label></label></div>',
 _l('the_number_sign'),
 _l('notice_trackno'),
 _l('notice_list_subject'),
 array(
   'name'=>_l('notice_list_client'),
   'th_attrs'=>array('class'=> (isset($client) ? 'not_visible' : ''))
 ),
 _l('other_party'),
 _l('notice_types_list_name'),
 _l('notice_value'),
 _l('notice_start_date'),
 _l('final_expiry_date'),
 (!isset($project) ? _l('project') : array(
   'name'=>_l('project'),
   'th_attrs'=>array('class'=>'not_visible')
 )),
 _l('status'),
 array(
   'name'=>_l('signature'),
   'th_attrs'=>array('class'=>'not_visible')
 ),
 array(
   'name'=>_l('total_comments'),
   'th_attrs'=>array('class'=>'not_visible')
 ),
 
 _l('signed_notice')
);
$custom_fields = get_custom_fields('notices',array('show_on_table'=>1));

foreach($custom_fields as $field){
 	array_push($table_data,$field['name']);
}

$table_data = hooks()->apply_filters('notices_table_columns', $table_data);

render_datatable($table_data, (isset($class) ? $class : 'notices'),[],[
  'data-last-order-identifier' => 'notices',
  'data-default-order'         => get_table_last_order('notices'),
]);

?>
