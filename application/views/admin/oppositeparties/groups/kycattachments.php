<h4 class="no-mtop bold"><?php echo _l('customer_attachments'); ?>
    <br />
    <small class="text-info"></small>
</h4>
<hr />
<?php if(isset($client)){ ?>

<?php if(has_permission('projects','','create')){
     ?>
  <div class="row">

    <div class="panel_s">
      <div class="panel-body">
   <div class="col-md-12">
        <a href="#" onclick="upload_partykycfile(<?=$client->id?>);return false;" class="btn btn-info mbot25"><?php echo _l('add_kycfile'); ?></a>
  </div>

 
<div class="text-right" style="margin-top:-25px;">
   <button class="gpicker" data-on-pick="projectFileGoogleDriveSave">
    <i class="fa fa-google" aria-hidden="true"></i>
    <?php echo _l('choose_from_google_drive'); ?>
  </button>
  <div id="dropbox-chooser"></div>
</div>
<div class="clearfix"></div>
<div class="mtop25"></div>

<div class="attachments">
    <div class="mtop25">

        <table class="table dt-table scroll-responsive" data-order-col="2" data-order-type="desc">
            <thead>
                <tr>
                   <th><?php echo _l('subject'); ?></th>
                   <th><?php echo _l('document_type'); ?></th>
                    <th width="30%"><?php echo _l('customer_attachments_file'); ?></th>
                    <th><?php echo _l('file_date_uploaded'); ?></th>
                    <th><?php echo _l('options'); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($attachments as $type => $attachment){
                    $download_indicator = 'id';
                    $key_indicator = 'rel_id';
                    $upload_path = get_upload_path_by_type($type);
                    if($type == 'invoice'){
                        $url = site_url() .'download/file/sales_attachment/';
                        $download_indicator = 'attachment_key';
                    } else if($type == 'proposal'){
                        $url = site_url() .'download/file/sales_attachment/';
                        $download_indicator = 'attachment_key';
                    } else if($type == 'estimate'){
                        $url = site_url() .'download/file/sales_attachment/';
                        $download_indicator = 'attachment_key';
                    } else if($type == 'contract'){
                        $url = site_url() .'download/file/contract/';
                    } else if($type == 'lead'){
                        $url = site_url() .'download/file/lead_attachment/';
                    } else if($type == 'task'){
                        $url = site_url() .'download/file/taskattachment/';
                    } else if($type == 'ticket'){
                        $url = site_url() .'download/file/ticket/';
                        $key_indicator = 'ticketid';
                    } else if($type == 'customer'){
                        $url = site_url() .'download/file/client/';
                    } else if($type == 'expense'){
                        $url = site_url() .'download/file/expense/';
                        $download_indicator = 'rel_id';
                    }else if($type == 'oppositeparty'){
                        $url = site_url() .'download/file/oppositeparty/';
                        $download_indicator = 'id';
                    }

                    ?>
                    <?php foreach($attachment as $_att){
                        ?>
                        <tr id="tr_file_<?php echo $_att['id']; ?>">
                             <td><?php echo $_att['subject'];?></td>
                               <td><?php echo get_document_type_name($_att['document_type']);?></td>
                            <td>
                             <?php
                             $path = $upload_path . $_att[$key_indicator] . '/' . $_att['file_name'];
                             $is_image = false;
                             if(!isset($_att['external'])) {
                                $attachment_url = $url . $_att[$download_indicator];
                                $is_image = is_image($path);
                                $img_url = site_url('download/preview_image?path='.protected_file_url_by_path($path,true).'&type='.$_att['filetype']);
                                $lightBoxUrl = site_url('download/preview_image?path='.protected_file_url_by_path($path).'&type='.$_att['filetype']);
                            } else if(isset($_att['external']) && !empty($_att['external'])){

                                if(!empty($_att['thumbnail_link'])){
                                    $is_image = true;
                                    $img_url = optimize_dropbox_thumbnail($_att['thumbnail_link']);
                                }

                                $attachment_url = $_att['external_link'];
                            }
                            if($is_image){
                                echo '<div class="preview_image">';
                            }
                            ?>
                            <a href="<?php if($is_image){ echo isset($lightBoxUrl) ? $lightBoxUrl : $img_url; } else {echo $attachment_url; } ?>"<?php if($is_image){ ?> data-lightbox="customer-profile" <?php } ?> class="display-block mbot5">
                                <?php if($is_image){ ?>
                                 <i class="<?php echo get_mime_class($_att['filetype']); ?>"></i> <?php echo $_att['file_name']; ?>
                                <div class="table-image hide">
                                    <div class="text-center"><i class="fa fa-spinner fa-spin mtop30"></i></div>
                                    <img src="#" class="img-table-loading" data-orig="<?php echo $img_url; ?>">
                                </div>
                               <?php } else { ?>
                               <i class="<?php echo get_mime_class($_att['filetype']); ?>"></i> <?php echo $_att['file_name']; ?>
                               <?php } ?>
                           </a>
                           <?php if($is_image){ echo '</div>'; } ?>
                    </td>
                   
                    <td data-order="<?php echo $_att['dateadded']; ?>"><?php echo _dt($_att['dateadded']); ?></td>
                    <td>
                        <?php /*if(!isset($_att['external'])){  ?>
                        <button type="button" data-toggle="modal" data-file-name="<?php echo $_att['file_name']; ?>" data-filetype="<?php echo $_att['filetype']; ?>" data-path="<?php echo $path; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>
                        <?php } else if(isset($_att['external']) && !empty($_att['external'])) {
                            echo '<a href="'.$_att['external_link'].'" class="btn btn-info btn-icon" target="_blank"><i class="fa fa-dropbox"></i></a>';
                        }*/ ?>
                        <?php if($type == 'oppositeparty'){ ?>
                        <a href="<?php echo admin_url('opposite_parties/delete_attachment/'.$_att['rel_id'].'/'.$_att['id']); ?>"  class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                        <?php } ?>
                    </td>
                    <?php } ?>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

</div>
    </div>
</div>
</div>

</div>
    
<?php } ?>
<?php } ?>


