<style type="text/css">
  .hearing.nav-tabs>li>a{
    font-size: 12.5px;
  }
</style>
<ul class="nav nav-tabs hearing" role="tablist" id="myTab">
  <?php foreach ($hearing_types as $key => $court) {  ?>
      <li role="presentation" <?php if($hearing_type_tab == $court['id']) {  ?> class="active" <?php } ?>>
        <a href="#tab_content<?=$court['id']?>" aria-controls="tab_content" role="tab" data-toggle="tab" onclick="courtClick('<?php echo $court['name']; ?>','<?=$court['id']?>')">
          <?php echo $court['name']; ?>
        </a>
      </li>
  <?php } ?>       
</ul>
<div class="tab-content">
  <button id="btn_add_hearing"  type="button" data-toggle="collapse" data-target="#demo" class="btn btn-info mbot25">ADD <?php echo _l($hearing_type_tab);?> </button>
  <div id="demo" class="collapse">
    <?php $this->load->view('admin/projects/project_hearing');?>
  </div>
  <?php if(isset($hearings)){ ?>
    <?php $this->load->view('admin/projects/project_edit_hearing');?>
  <?php } ?>
    <?php foreach ($hearing_types as $key => $court) { 

     $court_no = $court['id'].'_no';

     ?>
    <div role="tabpanel" class="tab-pane  <?php if($hearing_type_tab ==  $court['id']) { ?> active <?php } ?>" id="tab_content<?=$court['id']?>">
      <div class="task-table">
   
      <table class="table dt-table scroll-responsive table-project-hearings" data-order-col="1" data-order-type="asc">
        <thead>
    <tr> 
               
      <th><?php echo _l('hearing_date'); ?></th>
      <th><?php echo _l('hearing_list_subject'); ?></th>
      <th><?php echo _l('client'); ?></th>
      <th><?php echo _l('casediary_casenumber'); ?></th>
      <th><?php echo _l($court_no); ?></th>
      <th><?php echo _l('casediary_oppositeparty'); ?></th>
      <th><?php echo _l('court_decision'); ?></th>
      <th></th>

    </tr>
  </thead>
  <tbody>
    <?php 
      foreach ($hearings as $row_hearing) {
        if($row_hearing->hearing_type == $court['id']){
        ?>
        <tr>
        <td><?=_d($row_hearing->hearing_date)?></td>
        <td><a data-toggle="collapse"  onclick="show_edit_form('#div_hearngForm<?=$row_hearing->id?>')" > <?=$row_hearing->subject?></a></td>
        <td><a href="<?php echo admin_url(); ?>clients/client/<?php echo $project->clientid; ?>"><?=$project->client_data->company?></a></td>
        <td><?=$row_hearing->case_number?></td>
        <td><?=$row_hearing->court_no?></td>
        <td><?=$row_hearing->opposite_party_name?></td>
        <td><?=$row_hearing->proceedings?></td>
        <td><a class="btn btn-danger _delete btn-icon" href="<?php echo admin_url('casediary/delete_hearing/'.$row_hearing->project_id.'/'.$row_hearing->id) ?>"><i class="fa fa-remove"></i></a></td>
      </tr>
      <?php }} ?>  
  </tbody>
 </table>
</div>
      </div>
    <?php } ?>
     
</div>

<script type="text/javascript">
  
  function courtClick(courtname,courtid) { 
     $("label[for='court_no']").text(courtname+' No');
     $('#btn_add_hearing').text('ADD '+courtname);
    /*if(courtname == 'First Instance' || courtid == 'first_instance'){ 
      $("label[for='court_no']").text("First Instance No");
      $('#btn_add_hearing').text('ADD FIRST INSTANCE');
    }else if(courtname == 'Appeal' || courtid == 'appeal'){
      $("label[for='court_no']").text("Appeal No");
      $('#btn_add_hearing').text('ADD APPEAL');
    }else if(courtname == 'Cassation' || courtid == 'cassation'){
      $("label[for='court_no']").text("Cassation No");
      $('#btn_add_hearing').text('ADD CASSATION');
    }else if(courtname == 'Execution Appeal' || courtid == 'execution_appeal'){
      $("label[for='court_no']").text("Execution Appeal No");
      $('#btn_add_hearing').text('ADD EXECUTION APPEAL');
    }else if(courtname == 'Execution' || courtid == 'execution'){
      $("label[for='court_no']").text("Execution No");
      $('#btn_add_hearing').text('ADD EXECUTION');
    }else if(courtname == 'Small Claim' || courtid == 'small_claim'){
      $("label[for='court_no']").text("Small Claim No");
      $('#btn_add_hearing').text('ADD SMALL CLAIM');
    }*/

    //var ctype = $("#hearing-form :input[name='hearing_type']");

    $('#hearing-form input[name="hearing_type"]').val(courtid); 
   
    $('.edit_hearing').css('display','none'); 
  }

 courtClick('<?php echo _l($hearing_type_tab); ?>', '<?php echo $hearing_type_tab; ?>');
</script>
<script type="text/javascript">
  /*_validate_form($('#case-form'),{file_no:'required',case_number:'required',case_title:'required'},$('#case-form'));*/

  function init_hearing(hearingid=''){
   
    $('#hearing').modal('show');
    $('#hearing .edit-title').addClass('hide');

  }


  $('#btn_add_hearing').click(function(){
    $('.edit_hearing').hide();
  });

  function show_edit_form(formDivID){
    $('#demo').removeClass('in');
    $('#demo').attr("aria-expanded","false");
    $('.edit_hearing').hide();
    $(formDivID).show();
  }

  function setHearingId(hearingId){
    $('#hid_hearing_id').val(hearingId);
  }

</script>

<?php $this->load->view('admin/casediary/hearing_reference'); ?>
<?php $this->load->view('admin/casediary/court_degree'); ?>
<?php $this->load->view('admin/casediary/hallnumber'); ?>
<?php $this->load->view('admin/casediary/court_region'); ?>
