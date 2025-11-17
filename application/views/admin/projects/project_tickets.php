<?php defined('BASEPATH') or exit('No direct script access allowed');
    $this->load->view('admin/tickets/summary',array('project_id'=>$project->id));
    echo form_hidden('project_id',$project->id);
    echo '<div class="clearfix"></div>';
    if(((get_option('access_tickets_to_none_staff_members') == 1 && !is_staff_member()) || is_staff_member())){?>
	<!--	  <div class="dropdown col-md-4">
  			<button class="btn btn-info dropdown-toggle  mbot25" type="button" data-toggle="dropdown"><?= _l('new_ticket')?>
  			<span class="caret"></span></button>
  			<ul class="dropdown-menu ">
  			 <?php 
				foreach($services as $casetype){
				if($casetype['serviceid']!=10){
				?>
				<li><a href="<?php echo admin_url('tickets/add?project_id='.$project->id.'&service_type='.$casetype['service_slug']); ?>"><?=_l($casetype['service_slug'])?></a></li>
			 <?php }} ?>
 		 </ul>
		</div>-->
  <?php  }
    echo AdminTicketsTableStructure('tickets-table');
?>
