<?php
    $customer_tabs = get_recoveries_profile_tabs($client->userid);
    $xtratabArr = array('casediary','matters','litigation_cases','hearing','calls','meetings');
?>
<ul class="nav navbar-pills nav-tabs nav-stacked customer-tabs" role="tablist">
   <?php
   $visible_customer_profile_tabs = get_option('visible_customer_profile_tabs');
   if($visible_customer_profile_tabs != 'all') {
      $visible_customer_profile_tabs = unserialize($visible_customer_profile_tabs);
   }
   foreach($customer_tabs as $tab){
      if((isset($tab['visible']) && $tab['visible'] == true) || !isset($tab['visible'])){

        // Check visibility from settings too
        if(is_array($visible_customer_profile_tabs) && $tab['name'] != 'profile') {
          if(!in_array($tab['name'], $visible_customer_profile_tabs)) {
            continue;
          }
        }
         /*if(in_array($tab['name'], $xtratabArr)){
            $menu_active = get_option('aside_menu_active');
            $menu_active = json_decode($menu_active);
            $flag = 0; 
            foreach($menu_active->aside_menu_active as $item){
            if($item->id == $tab['name']){
               $flag = 1;
               break; 
            }
            if(isset($item->children)){
              foreach($item->children as $_sub_menu_check){
                if($_sub_menu_check->id == $tab['name']){
                  $flag = 1;
                  break; 
                }
              }
            }
          }
          if($flag == 0){
            continue;
          }
        }*/
        ?>
      <li class="<?php if($tab['name'] == 'profile'){echo 'active ';} ?>customer_tab_<?php echo $tab['name']; ?>">
        <a data-group="<?php echo $tab['name']; ?>" href="<?php echo $tab['url']; ?>"><i class="<?php echo $tab['icon']; ?> menu-icon" aria-hidden="true"></i><?php echo $tab['lang']; ?>
            <?php if(isset($tab['id']) && $tab['id'] == 'reminders'){
              $total_reminders = total_rows('tblreminders',
                  array(
                   'isnotified'=>0,
                   'staff'=>get_staff_user_id(),
                   'rel_type'=>'corporate',
                   'rel_id'=>$client->userid
                   )
                  );
              if($total_reminders > 0){
                echo '<span class="badge">'.$total_reminders.'</span>';
              }
          }
          ?>
      </a>
  </li>
  <?php } ?>
  <?php } ?>
</ul>
