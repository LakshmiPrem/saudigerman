<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
 <?php echo form_open_multipart((isset($hearing) ? admin_url('projects/hearing/'.$hearing->id) : admin_url('projects/hearing')),array('id'=>'hearing-form')); ?>
 <div class="modal-header">
   <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
   <h4 class="modal-title"> <?php echo 'Manage Dashboard Boxes'; ?>
   </h4>
</div> 
<div class="modal-body" style="overflow-y: auto;">
  <div class="row">
      <div class="panel">
        <div class="panel-body">
          <table class="table table-bordered table-responsive" id="table-dashboard-boxes">
         <thead>
           <tr>
             <th width="70%">Box</th>
             <th width="20%">Enable/Disable</th>
             <th width="10%">Order</th>
           </tr>
         </thead>
         <tbody>
            <?php foreach ($dashboard_box as $key => $value) { ?>
                <tr>
                  <td><?php echo $value['box_name'] ?></td>
                  <td> <div class="onoffswitch" data-toggle="tooltip" data-title="">
                      <input type="checkbox" data-switch-url="<?php echo admin_url() . 'misc/change_dash_box_status'; ?>" name="onoffswitch" class="onoffswitch-checkbox" id="<?php echo $value['id'] ?>" data-id="<?php echo $value['id'] ?>" <?php echo ($value['box_status'] == 1 ?  'checked' : '')  ?>>
                      <label class="onoffswitch-label" for="<?php echo $value['id']; ?>"></label>
                      </div>
                  </td>
                  <td><input class="form-control" type="number" data-id="<?php echo $value['id']; ?>" value="<?php echo $value['box_order']  ?>" min="1" name="box_order" onblur="change_box_order(this); return false;"></td>
                  
                </tr>
            <?php } ?>     
         </tbody>
       </table>  
        </div>
      </div>
  </div>
</div>  

<style type="text/css">

#table-dashboard-boxes {
  font-family: Arial, Helvetica, sans-serif;
  border-collapse: collapse;
  width: 100%;
}

#table-dashboard-boxes td, #customers th {
  border: 1px solid #ddd;
  padding: 8px;
}

#table-dashboard-boxes tr:nth-child(even){background-color: #f2f2f2;}

#table-dashboard-boxes tr:hover {background-color: #ddd;}

#table-dashboard-boxes th {
  padding-top: 12px;
  padding-bottom: 12px;
  text-align: left;
  background-color: #0d3c61;
  color: white;
}
</style>
<script type="text/javascript">
    function change_box_order(invoker) {
        var box_id    = $(invoker).data('id');
        var box_order = $(invoker).val();

        var data ={'box_id' : box_id , 'box_order' : box_order};

        $.post(admin_url + 'misc/change_box_order', data).done(function () {
          //var location = window.location.href;
       });

    }
</script>