<?php defined('BASEPATH') or exit('No direct script access allowed'); ?>
<?php init_head(); ?>
<div id="wrapper">
    <div class="screen-options-area"></div>
    <div class="screen-options-btn">
        <?php echo _l('dashboard_options'); ?>
    </div>
    <div class="content">
        <div class="row">

            <?php $this->load->view('admin/includes/alerts'); ?>

            <?php hooks()->do_action( 'before_start_render_dashboard_content' ); ?>

            <div class="clearfix"></div>

            <div class="col-md-12 mtop30" data-container="top-12">
                <?php render_dashboard_widgets('top-12'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_top_container'); ?>

            <div class="col-md-6" data-container="middle-left-6">
                <?php render_dashboard_widgets('middle-left-6'); ?>
            </div>
            <div class="col-md-6" data-container="middle-right-6">
                <?php render_dashboard_widgets('middle-right-6'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard_half_container'); ?>

            <div class="col-md-8" data-container="left-8">
                <?php render_dashboard_widgets('left-8'); ?>
            </div>
            <div class="col-md-4" data-container="right-4">
                <?php render_dashboard_widgets('right-4'); ?>
            </div>

            <div class="clearfix"></div>

            <div class="col-md-4" data-container="bottom-left-4">
                <?php render_dashboard_widgets('bottom-left-4'); ?>
            </div>
             <div class="col-md-4" data-container="bottom-middle-4">
                <?php render_dashboard_widgets('bottom-middle-4'); ?>
            </div>
            <div class="col-md-4" data-container="bottom-right-4">
                <?php render_dashboard_widgets('bottom-right-4'); ?>
            </div>

            <?php hooks()->do_action('after_dashboard'); ?>
        </div>
    </div>
</div>
<script>
    app.calendarIDs = '<?php echo json_encode($google_ids_calendars); ?>';
</script>
<?php init_tail(); ?>
<?php $this->load->view('admin/utilities/calendar_template'); ?>
<?php $this->load->view('admin/dashboard/dashboard_js'); ?>
</body>
</html>


<script type="text/javascript">



$("#search_").on("keyup", function() {
  var SearchCount = 0;  
  var input = $(this).val().toUpperCase();
  $(".searchCard").each(function() {
    if ($(this).data("string").toUpperCase().indexOf(input) < 0) {
      $(this).hide();
    } else {
        SearchCount++;
      $(this).show();
    }
  });
  if(SearchCount == 0){
    $('.no_result').toggleClass('hide');
  }else{
    $('.no_result').addClass('hide');
  }

});
</script>
<script src="<?php echo site_url('assets/js/')?>paginga.jquery.js"></script>

<script type="text/javascript">
    $(function() {
                $(".paginate").paginga({
                    
                });
            });
</script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paginationjs/2.1.5/pagination.css" integrity="sha512-QmxybGIvkSI8+CGxkt5JAcGOKIzHDqBMs/hdemwisj4EeGLMXxCm9h8YgoCwIvndnuN1NdZxT4pdsesLXSaKaA==" crossorigin="anonymous" referrerpolicy="no-referrer" />