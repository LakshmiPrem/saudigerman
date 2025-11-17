<?php
 $quotes = get_option('dashboard_daily_quotes');
 $quotess = json_decode($quotes);
 $m = 0;
 $dir = DAILY_QUOTES_FOLDER;
 $files = scandir($dir,1);
 $dashboard_date = get_option('dashboard_date');
 if(date('Y-m-d') == $dashboard_date){
    $qcount = get_option('dashboard_quotes_count');
     foreach($quotess->daily_quotes as $item){
        if($item->no==$qcount){
            $quote = $item->quote;
                break;
        }
    }
    $icount = get_option('dashboard_img_count');
    for($i=1;$i<sizeof($files);$i++){
        $fileCount = explode('.', $files[$i]);
        if($fileCount[0] == $icount){
            $isrc = $files[$i];
            $imgPath =  base_url('uploads/dailyquotes/' . $isrc);

            break;
        }

    }
   }else{
     $qcount = get_option('dashboard_quotes_count');
     if($qcount>=5){
         $newQCount = 1;
     }else{
         $newQCount = $qcount+1;
     }
    
     foreach($quotess->daily_quotes as $item){
        if($item->no==$newQCount){
            $quote = $item->quote;
                break;
        }
     }
 //   Update quotes Count
     $this->db->where('name', 'dashboard_quotes_count');
     $this->db->update('tbloptions',array("value"=>$newQCount));
//   Update dashboard date to curent date
     $this->db->where('name', 'dashboard_date');
     $this->db->update('tbloptions',array("value"=>date('Y-m-d')));

    $icount = get_option('dashboard_img_count');
    if($icount >= 10){
        $newICount = 1;

    }else{
        $newICount = $icount+1;
    }
    for($i=1;$i<sizeof($files);$i++){
        $fileCount = explode('.', $files[$i]);
        if($fileCount[0] == $newICount){
            $isrc = $files[$i];
            $imgPath =  base_url('uploads/dailyquotes/' . $isrc);
            break;
        }
    }
    //   Update images Count
     $this->db->where('name', 'dashboard_img_count');
     $this->db->update('tbloptions',array("value"=>$newICount));
   }

$currentDateTime = date('Y-m-d h:i A');
 $newDateTime = date('h:i A', strtotime($currentDateTime));
 $explode = explode(' ', $newDateTime);
 $ampm = $explode[1];
 $explode2 = explode(':', $explode[0]);
 $hour = $explode2[0];
 $minutes = $explode2[1];
 $week = date('l');
 $ampm =date('A');

?>

<div class="widget" id="widget-quotes" data-name="daily-quotes">
    <div class="col-xs-12 ind-nv" style="padding: 10px 17px;color: white;position: absolute;text-align: right;">
      <a href="<?php echo admin_url('todo'); ?>" data-toggle="tooltip" title="" data-placement="bottom" data-original-title="Todo items" style="
"><i class="fa fa-check-square-o fa-fw fa-lg"></i>
        <span class="label bg-warning icon-total-indicator nav-total-todos<?php if($current_user->total_unfinished_todos == 0){echo ' hide';} ?>" style="
    font-size: 8px;
    border-radius: 84px;
    position: absolute;
    right:66px;
    top: 5px;
"><?php echo $current_user->total_unfinished_todos; ?></span>
            </a>
<!--a href="#" id="top-timers" class="dropdown-toggle top-timers" data-toggle="dropdown">
            <i class="fa fa-clock-o fa-fw fa-lg" aria-hidden="true"></i>
            <span class="label bg-success icon-total-indicator icon-started-timers<?php if ($totalTimers = count($startedTimers) == 0){ echo ' hide'; }?>">
            <?php echo count($startedTimers); ?>
            </span>
            </a>
            <ul class="dropdown-menu animated fadeIn started-timers-top width350" id="started-timers-top">
               <?php $this->load->view('admin/tasks/started_timers',array('startedTimers'=>$startedTimers)); ?>
            </ul-->

<a href="#" class="dropdown-toggle notifications-icon" data-toggle="dropdown" aria-expanded="true">
                          <?php $this->load->view('admin/includes/notifications'); ?>
                                                  </a>
    </div>
<center class="panel-body" style="
    background-image: url('<?php echo $imgPath;?>');
    padding: 12% 17px;
    margin-bottom: 18px;
    border-radius: 2px;
    box-shadow: inset 0 0 0 2000px #00000047;
    /* background-size: cover; */
    /* background-attachment: fixed; */
    background-size: cover;
    background-repeat: no-repeat;
    height: 220px;
"><img src="data:image/svg+xml;utf8;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4KPCEtLSBHZW5lcmF0b3I6IEFkb2JlIElsbHVzdHJhdG9yIDE2LjAuMCwgU1ZHIEV4cG9ydCBQbHVnLUluIC4gU1ZHIFZlcnNpb246IDYuMDAgQnVpbGQgMCkgIC0tPgo8IURPQ1RZUEUgc3ZnIFBVQkxJQyAiLS8vVzNDLy9EVEQgU1ZHIDEuMS8vRU4iICJodHRwOi8vd3d3LnczLm9yZy9HcmFwaGljcy9TVkcvMS4xL0RURC9zdmcxMS5kdGQiPgo8c3ZnIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyIgeG1sbnM6eGxpbms9Imh0dHA6Ly93d3cudzMub3JnLzE5OTkveGxpbmsiIHZlcnNpb249IjEuMSIgaWQ9IkxheWVyXzEiIHg9IjBweCIgeT0iMHB4IiB3aWR0aD0iMzJweCIgaGVpZ2h0PSIzMnB4IiB2aWV3Qm94PSIwIDAgMTI4IDEyOCIgZW5hYmxlLWJhY2tncm91bmQ9Im5ldyAwIDAgMTI4IDEyOCIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+CjxnPgoJPGc+CgkJPHBhdGggZD0iTTQ0LDU2aC0zLjM5MWMyLjU3OC00LjQ3Myw1LjgwNS05LjAyLDguNTIzLTEyLjIxOWM0LjI4OS01LjA0NywzLjY4OC0xMi42MTctMS4zNTktMTYuOTFDNDUuNTE2LDI0Ljk0NSw0Mi43NSwyNCw0MCwyNCAgICBjLTMuMzkxLDAtNi43NSwxLjQyNi05LjEyNSw0LjIwN0MyNy43MTksMzEuODk4LDEyLDQ4LDEyLDY4djIwYzAsOC44MzYsNy4xNjQsMTYsMTYsMTZoMTZjOC44MzYsMCwxNi03LjE2NCwxNi0xNlY3MiAgICBDNjAsNjMuMTY0LDUyLjgzNiw1Niw0NCw1NnogTTUyLDg4YzAsNC40MS0zLjU4Niw4LTgsOEgyOGMtNC40MDYsMC04LTMuNTktOC04VjY4YzAtMTUuNDQ5LDEyLTI4Ljk4OCwxNS45MzgtMzMuNDM4bDEuMDIzLTEuMTYgICAgQzM4LDMyLjE4NCwzOS4zMTMsMzIsNDAsMzJjMC42NjQsMCwxLjY1NiwwLjE2OCwyLjU5NCwwLjk2NWMxLjY3MiwxLjQyNiwxLjg3NSwzLjk1MywwLjQzOCw1LjYzNyAgICBjLTMuMTcyLDMuNzM4LTYuNjcyLDguNzQ2LTkuMzUyLDEzLjQwMmMtMS40MywyLjQ3Ny0xLjQzLDUuNTIzLDAuMDA4LDhDMzUuMTA5LDYyLjQ3NywzNy43NSw2NCw0MC42MDksNjRINDRjNC40MTQsMCw4LDMuNTksOCw4ICAgIFY4OHogTTEwMCw1NmgtMy4zOTFjMi41NzgtNC40NzMsNS44MDUtOS4wMiw4LjUyMy0xMi4yMTljNC4yODktNS4wNDcsMy42ODgtMTIuNjE3LTEuMzU5LTE2LjkxICAgIEMxMDEuNTE2LDI0Ljk0NSw5OC43NSwyNCw5NS45OTIsMjRjLTMuMzgzLDAtNi43NSwxLjQyNi05LjExNyw0LjIwN0M4My43MTksMzEuODk4LDY4LDQ4LDY4LDY4djIwYzAsOC44MzYsNy4xNjQsMTYsMTYsMTZoMTYgICAgYzguODM2LDAsMTYtNy4xNjQsMTYtMTZWNzJDMTE2LDYzLjE2NCwxMDguODM2LDU2LDEwMCw1NnogTTEwOCw4OGMwLDQuNDEtMy41ODYsOC04LDhIODRjLTQuNDE0LDAtOC0zLjU5LTgtOFY2OCAgICBjMC0xNS40NjEsMTItMjksMTUuOTQ1LTMzLjQ0NWwxLjAxNi0xLjE1NkM5NCwzMi4xODQsOTUuMzA1LDMyLDk1Ljk5MiwzMmMwLjY3MiwwLDEuNjY0LDAuMTY4LDIuNjAyLDAuOTY1ICAgIGMxLjY3MiwxLjQyNiwxLjg3NSwzLjk1MywwLjQ0NSw1LjYzN2MtMy4xOCwzLjczOC02LjY4LDguNzQ2LTkuMzU5LDEzLjQwMmMtMS40MywyLjQ3Ny0xLjQzLDUuNTIzLDAsOCAgICBjMS40MywyLjQ3Myw0LjA3LDMuOTk2LDYuOTMsMy45OTZIMTAwYzQuNDE0LDAsOCwzLjU5LDgsOFY4OHoiIGZpbGw9IiNGRkZGRkYiLz4KCTwvZz4KPC9nPgo8L3N2Zz4K" style="">
<p style="
    color:  white;
    font-size: 19px;
    text-align:  center;
    text-shadow: #706f6dcc 1px 0 10px;
"><?php echo $quote;?>
</p>

    </div>
<div class="col-xs-12" style="
    position: absolute;
    bottom: 8px;
">
    <div><div style="
    float: left;
">
 <?php if($current_user->profile_image == NULL){ ?><img src="<?php echo base_url('assets/images/user-placeholder.jpg');?>" style="
    width: 37px;
    border-radius: 150px;
    border: 0px solid #edf1f5;
"><?php }else{?>
    <?php echo staff_profile_image($current_user->staffid,array('img','img-responsive','staff-profile-image-small'),'small',array('width'=>'37px','border-radius'=>'150px','border'=> '0px solid #edf1f5')); ?>
<?php }?>
<b style="color: white;font-size: 13px;font-weight: 600;padding: 11px 16px;position: relative;top: 3px;
"><?php echo _l('welcome_top',$current_user->firstname); ?></b></div>
<div style="
    float: right;
    color: white;
    padding: 12px 0px;
">
   <a href="#" onclick="logout(); return false;"><i class="fa fa-sign-out"></i><?php echo _l('nav_logout'); ?> </a></div></div>
</center>
</div>
<style type="text/css">
    .staff-profile-image-small{
        width: 37px;
    border-radius: 150px;
    border: 0px solid #edf1f5;
    }
</style>