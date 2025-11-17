<?php init_head(); ?>
<div id="wrapper">
  <div class="content">
    <div class="row">


     
     <div class="col-md-12">
      <div class="panel_s">
        <div class="panel-body">
          <h4 class="no-margin font-medium"><i class="fa fa-area-chart menu-icon" aria-hidden="true"></i> <?php echo _l('als_profitability_report'); ?></h4>
                      <hr />
          <?php if(is_admin()){ ?>
         
          <?php } ?>
          
          <canvas id="timesheetsChart" style="max-height:400px;" width="350" height="350" class="hide"></canvas>
          <!-- <hr /> -->
          <div class="clearfix"></div>
          <div class="row">
           <div class="col-md-2">
            <label><?php echo _l('contract_start_date'); ?></label>
           <div class="select-placeholder">
              <select name="range" id="range" class="selectpicker" data-width="100%">
             <option value="today" selected><?php echo _l('today'); ?></option>
             <option value="this_month"><?php echo _l('this_month'); ?></option>
             <option value="last_month"><?php echo _l('last_month'); ?></option>
             <option value="this_week"><?php echo _l('this_week'); ?></option>
             <option value="last_week"><?php echo _l('last_week'); ?></option>
             <option value="period"><?php echo _l('period_datepicker'); ?></option>
           </select>
           </div>
           <div class="row mtop15">
             <div class="col-md-12 period hide">
              <?php echo render_date_input('period-from'); ?>
            </div>
            <div class="col-md-12 period hide">
              <?php echo render_date_input('period-to'); ?>
            </div>
          </div>
        </div>

      <!--   <div class="col-md-2">
            <label><?php echo _l('contract_end_date'); ?></label>
           <div class="select-placeholder">
              <select name="range2" id="range2" class="selectpicker" data-width="100%">
             <option value="today" selected><?php echo _l('today'); ?></option>
             <option value="this_month"><?php echo _l('this_month'); ?></option>
             <option value="last_month"><?php echo _l('last_month'); ?></option>
             <option value="this_week"><?php echo _l('this_week'); ?></option>
             <option value="last_week"><?php echo _l('last_week'); ?></option>
             <option value="period2"><?php echo _l('period_datepicker'); ?></option>
           </select>
           </div>
           <div class="row mtop15">
             <div class="col-md-12 period2 hide">
              <?php echo render_date_input('end-period-from'); ?>
            </div>
            <div class="col-md-12 period2 hide">
              <?php echo render_date_input('end-period-to'); ?>
            </div>
          </div>
        </div> -->
        <?php if(isset($view_all)){ ?>
        <div class="col-md-2">
       <div class="select-placeholder">
           <select name="staff_id" id="staff_id" class="selectpicker" data-width="100%">
           <option value=""><?php echo _l('all_staff_members'); ?></option>
           <option value="<?php echo get_staff_user_id(); ?>"><?php echo get_staff_full_name(get_staff_user_id()); ?></option>
           <?php foreach($staff_members_with_timesheets as $staff){ ?>
           <option value="<?php echo $staff['staff_id']; ?>"><?php echo get_staff_full_name($staff['staff_id']); ?></option>
           <?php } ?>
         </select>
       </div>
       </div>
       <?php } ?>
      

       <div class="col-md-3">
        <label><?php echo _l('clients'); ?></label>
         <div class="select-placeholder">
            <select id="clientid" name="clientid" data-live-search="true" data-width="100%" class="ajax-search" data-none-selected-text="<?php echo _l('clients'); ?>">         
            </select>
         </div>
       </div>

        <div class="col-md-3">
        <label><?php echo _l('projects'); ?></label>
         <div class="select-placeholder">
           <select data-empty-title="<?php echo _l('projects'); ?>" name="project_id" id="project_id" class="projects ajax-search" data-live-search="true" data-width="100%">
         </select>
         </div>
       </div>

      

       <div class="col-md-2 hide">
         <?php //echo render_select('task_id',$arr_tasks,array('id','name'),'tasks');
         ?>
         <div class="select-placeholder">
              <select name="task_id" id="task_id" class="selectpicker" data-width="100%" placeholder="Tasks" data-none-selected-text="Tasks">
                <option value="">Tasks</option>
                <?php foreach ($arr_tasks as $tasks) {?>
                <option value="<?=$tasks['id']?>"><?php echo $tasks['name']; ?></option>
                <?php } ?>
             </select>
        </div>
       </div>
       <div class="col-md-1 mtop15">
         <a href="#" id="apply_filters_timesheets" class="btn btn-default p7"><?php echo _l('apply'); ?></a>
       </div>
       <!-- <div class="mtop10 hide relative pull-right hide" id="group_by_tasks_wrapper">
        <span><?php echo _l('group_by_task'); ?></span>
        <div class="onoffswitch">
          <input type="checkbox" name="group_by_task" class="onoffswitch-checkbox" id="group_by_task">
          <label class="onoffswitch-label" for="group_by_task"></label>
        </div>
      </div> -->
      <div class="col-md-12">
        <hr class="no-mtop"/>
      </div>
    </div>
    <div class="clearfix"></div>
    <table class="table table-timesheets-report">
      <thead>
        <tr>
          <th><?php echo _l('client'); ?></th>
          <th><?php echo _l('matter'); ?></th>
          <th><?php echo _l('case_type'); ?></th>
          <th><?php echo _l('project_billing_type'); ?></th>
          <th><?php echo _l('client_total_hours'); ?></th>
          <th><?php echo _l('hours_in_timesheet'); ?></th>
          <th><?php echo _l('matter_total_cost'); ?></th>
          <th><?php echo _l('matter_billed_fees'); ?></th>
          <th><?php echo _l('collected_fees'); ?></th>
          <th><?php echo _l('timesheet_cost'); ?></th>
          <th><?php echo _l('timesheet_billing_cost');?></th>
          <th><?php echo _l('cash_profit'); ?></th>
          <th><?php echo _l('billing_profit'); ?></th>
          <th><?php echo _l('lawyers_assinged'); ?></th>
        </tr>
      </thead>
      <tbody></tbody>
      <tfoot>
        <tr>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <td></td>
         <th></th>
         <td></td>
         <td></td>
       </tr>
     </tfoot>
   </table>
 </div>
</div>
</div>
</div>
</div>
</div>

<?php init_tail(); ?>
<script>
 var staff_member_select = $('select[name="staff_id"]');
 $(function() {

  init_ajax_projects_search();
  var ctx = document.getElementById("timesheetsChart");
  var chartOptions = {
    type: 'bar',
    data: {
      labels: [],
      datasets: [{
        label: '',
        data: [],
        backgroundColor: [],
        borderColor: [],
        borderWidth: 1
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      tooltips: {
        enabled: true,
        mode: 'single',
        callbacks: {
          label: function(tooltipItems, data) {
            return decimalToHM(tooltipItems.yLabel);
          }
        }
      },
      scales: {
        yAxes: [{
          ticks: {
            beginAtZero: true,
            min: 0,
            userCallback: function(label, index, labels) {
              return decimalToHM(label);
            },
          }
        }]
      },
    }
  };

  var timesheetsTable = $('.table-timesheets-report');
  $('#apply_filters_timesheets').on('click', function(e) {
    e.preventDefault();
    timesheetsTable.DataTable().ajax.reload();
  });

  $('body').on('change','#group_by_task',function(){
    var tApi = timesheetsTable.DataTable();
    var visible = $(this).prop('checked') == false;
    var tEndTimeIndex = $('.t-end-time').index();
    var tStartTimeIndex = $('.t-start-time').index();
    if(tEndTimeIndex == -1 && tStartTimeIndex == -1) {
      tStartTimeIndex = $(this).attr('data-start-time-index');
      tEndTimeIndex = $(this).attr('data-end-time-index');
    } else {
      $(this).attr('data-start-time-index',tStartTimeIndex);
      $(this).attr('data-end-time-index',tEndTimeIndex);
    }
    tApi.column(tEndTimeIndex).visible(visible, false).columns.adjust();
    tApi.column(tStartTimeIndex).visible(visible, false).columns.adjust();
    tApi.ajax.reload();
  });

  var timesheetsChart;
  var Timesheets_ServerParams = {};
  Timesheets_ServerParams['range'] = '[name="range"]';
  Timesheets_ServerParams['period-from'] = '[name="period-from"]';
  Timesheets_ServerParams['period-to'] = '[name="period-to"]';
  Timesheets_ServerParams['staff_id'] = '[name="staff_id"]';
  Timesheets_ServerParams['project_id'] = '[name="project_id"]';
  Timesheets_ServerParams['task_id'] = '[name="task_id"]';
  Timesheets_ServerParams['group_by_task'] = '[name="group_by_task"]:checked';
  Timesheets_ServerParams['matter_id'] = '[name="matter_id"]';
  Timesheets_ServerParams['clientid'] = '[name="clientid"]';
  initDataTable('.table-timesheets-report', window.location.href, undefined, undefined, Timesheets_ServerParams, [2, 'DESC']);

  timesheetsTable.on('init.dt',function(){
    var $dtFilter = $('body').find('.dataTables_filter');
    var $gr = $('#group_by_tasks_wrapper').clone()
    $('#group_by_tasks_wrapper').remove();
    $gr.removeClass('hide');
    $gr.find('span').css('position','absolute');
    $gr.find('span').css('top','2px');
    $gr.find('span').css((isRTL == 'true' ? 'right' : 'left'),'-90px');
    $dtFilter.before($gr,'<div class="clearfix"></div>');
  });
  timesheetsTable.on('draw.dt', function() {
    var TimesheetsTable = $(this).DataTable();
    var logged_time = TimesheetsTable.ajax.json().logged_time;
    var chartResponse = TimesheetsTable.ajax.json().chart;
    var chartType = TimesheetsTable.ajax.json().chart_type;
    $(this).find('tfoot').addClass('bold');
    $(this).find('tfoot td.total_billable_time_staff_h').html("<?php echo _l('total_billable_logged_hours_by_staff'); ?>: " + logged_time.total_billable_time_h);
    $(this).find('tfoot td.total_non_billable_time_staff_h').html("<?php echo _l('total_non_billable_logged_hours_by_staff'); ?>: " + logged_time.total_non_billable_time_h);

    $(this).find('tfoot td.total_billable_cost_staff_h').html("<?php echo _l('total_billable_cost_staff_h'); ?>: " + logged_time.total_billable_cost);
    $(this).find('tfoot td.total_hourly_rate_staff_h').html("<?php echo _l('total_hourly_rate_staff_h'); ?>: " + logged_time.total_hourly_rate);
    
    /*$(this).find('tfoot td.total_logged_time_timesheets_staff_h').html("<?php echo _l('total_logged_hours_by_staff'); ?>: " + logged_time.total_logged_time_h);
    $(this).find('tfoot td.total_logged_time_timesheets_staff_d').html("<?php echo _l('total_logged_hours_by_staff'); ?>: " + logged_time.total_logged_time_d);
    */if (typeof(timesheetsChart) !== 'undefined') {
      timesheetsChart.destroy();
    }
    if (chartType != 'month') {
      chartOptions.data.labels = chartResponse.labels;
    } else {
      chartOptions.data.labels = [];
      for (var i in chartResponse.labels) {
        chartOptions.data.labels.push(moment(chartResponse.labels[i]).format("MMM Do YY"));
      }
    }
    chartOptions.data.datasets[0].data = [];
    chartOptions.data.datasets[0].backgroundColor = [];
    chartOptions.data.datasets[0].borderColor = [];
    for (var i in chartResponse.data) {
      chartOptions.data.datasets[0].data.push(chartResponse.data[i]);
      if (chartResponse.data[i] == 0) {
        chartOptions.data.datasets[0].backgroundColor.push('rgba(167, 167, 167, 0.6)');
        chartOptions.data.datasets[0].borderColor.push('rgba(167, 167, 167, 1)');
      } else {
        chartOptions.data.datasets[0].backgroundColor.push('rgba(132, 197, 41, 0.6)');
        chartOptions.data.datasets[0].borderColor.push('rgba(132, 197, 41, 1)');
      }
    }

    var selected_staff_member = staff_member_select.val();
    var selected_staff_member_name = staff_member_select.find('option:selected').text();
    chartOptions.data.datasets[0].label = $('select[name="range"] option:selected').text() + (selected_staff_member != '' && selected_staff_member != undefined ? ' - ' + selected_staff_member_name : '');
    setTimeout(function() {
      timesheetsChart = new Chart(ctx, chartOptions);
    }, 30);
    do_timesheets_title();
  });
});
function do_timesheets_title(){
  var _temp;
  var range = $('select[name="range"]');
  var _range_heading = range.find('option:selected').text();
  if(range.val() != 'period'){
    _temp = _range_heading;
  } else {
    _temp = _range_heading + ' ('+$('input[name="period-from"]').val() +' - '+$('input[name="period-to"]').val()+') ';
  }
  $('head title').html( _temp + (staff_member_select.find('option:selected').text() != '' ? ' - ' + staff_member_select.find('option:selected').text() : ''));
}

function _my_timesheet() {
  $('#_my_timesheet').modal('show');
}


    $('select[name="range2"]').on('change', function() {
        var $period = $('.period2');
        if ($(this).val() == 'period2') {
            $period.removeClass('hide');
        } else {
            $period.addClass('hide');
            $period.find('input').val('');
        }
    });



</script>
</body>
</html>

