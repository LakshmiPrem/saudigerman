<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php if(isset($client)){ ?>
<h4 class="customer-profile-group-heading"><?php echo _l('contracts_notes_tab'); ?></h4>
<div class="col-md-12">
  <a href="#" onclick="defendar(<?php echo $client->id; ?>); return false;" class="btn btn-success mtop15 mbot10" ><?php echo _l('new_contacts'); ?></a>
<!-- <a href="#"  class="btn btn-info new-contact center" onclick="slideToggle('.usercontact'); return false;"><?php echo _l('new_contacts'); ?></a>-->
 <div class="clearfix"></div>
<div class="row">
     <hr class="hr-panel-heading" />
</div>
 <div class="clearfix"></div>
 <!--<div class="usercontact hide">
    <?php echo form_open(admin_url( 'misc/add_note/'.$client->userid.'/oppositeparty')); ?>
    <?php echo render_textarea( 'description', 'note_description', '',array( 'rows'=>5)); ?>
    <button class="btn btn-info pull-right mbot15">
        <?php echo _l( 'submit'); ?>
    </button>
    <?php echo form_close(); ?>
</div>-->
<div class="clearfix"></div>
<div class="mtop15">
    <table class="table dt-table table-defenders" data-order-col="2" data-order-type="desc">
        <thead>
            <tr>
                <th>
                    <?php echo _l( 'contact_name'); ?>
                </th>
                <th>
                    <?php echo _l( 'party_type'); ?>
                </th>
                <th>
                    <?php echo _l( 'designation'); ?>
                </th>
                 <th>
                    <?php echo _l( 'emailid'); ?>
                </th>
                <th>
                    <?php echo _l( 'options'); ?>
                </th>
            </tr>
        </thead>
        <tbody>
         
            <?php foreach($party_contacts as $row){ ?>
            <tr>
				<td><?=$row['contact_name']?></td>
               <?php $type=($row['party_type']='defendant')?'Defendant':'Authorized Signatory ';?>
                <td><?=$type?></td>
                <td><?=$row['designation']?></td>
                <td><?=$row['emailid']?></td>
                  
        <td>
                       <?php if($row['addedby'] == get_staff_user_id() || is_admin()){ ?>
            <a href="#" class="btn btn-default btn-icon" onclick="defendar(<?php echo $row['opposite_id'].','.$row['id']?>);return false;"><i class="fa fa-pencil-square-o"></i></a>
            <a href="<?php echo admin_url('opposite_parties/delete_defendar/'.$row['opposite_id'].'/'. $row['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
            <?php } ?>
        </td>
    </tr>
    <?php } ?>
</tbody>
</table>
</div>
<?php } ?>
<div id="contact_data"></div>