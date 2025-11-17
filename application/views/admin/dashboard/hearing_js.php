<!--- edit hearing ------------->

<script type="text/javascript">

   function hearingTableall(tableName='',projectID) { 
	
     var fnServerParams = {  };
     var tableName_ = '.table-stage-hearings';
   /*  if ($.fn.DataTable.isDataTable(tableName_)) {
       $(tableName_).DataTable().destroy();
     }*/
     _table_api = initDataTable(tableName_, admin_url + 'projects/hearings_overview_tables/'+projectID+'/'+tableName, false, false, fnServerParams, [
       [0, 'ASC'],
       [0, 'ASC']
       ]);
  }  

  function hearingTable(tableName,projectID) { 

     var fnServerParams = {  };

     var tableName_ = '.table-'+tableName+'-hearings';

     if ($.fn.DataTable.isDataTable(tableName_)) {

       $(tableName_).DataTable().destroy();

     }

     _table_api = initDataTable(tableName_, admin_url + 'projects/hearings_tables/'+projectID+'/'+tableName, false, false, fnServerParams, [

       [0, 'ASC'],

       [0, 'ASC']

       ]);

  }

  var hearingTypeGlobal = '1';



function courtClick(courtname,courtid) { 

   if(courtname == 'All' || courtname == 'all' ){

      $('#btn_add_hearing').text('<?php echo _l('add_new').' '._l('hearing'); ?>');

   }else{

      $('#btn_add_hearing').removeClass('hide');

      $('#btn_add_hearing').show();

      $('#btn_add_hearing').text('<?php echo _l('add_new'); ?> '+courtname);

   }

   hearingTypeGlobal = courtid;

   $('.edit_hearing').css('display','none'); 

   hearingTable(courtid,project_id);

}



var hidden_columns = [5,6,7];

</script>

<script type="text/javascript">

   

function init_hearing(id) {

    if ($('#task-modal').is(':visible')) {

        $('#task-modal').modal('hide');

    }

    // In case header error

    if (init_hearing_modal_data(id, undefined)) {

        $('#hearing-modal').modal('show');

    }

   }

   // Fetches lead modal data, can be edit/add/view

function init_hearing_modal_data(id, url) {

    

    var requestURL = (typeof (url) != 'undefined' ? url : 'projects/hearing/') + (typeof (id) != 'undefined' ? id : '');

    var str = requestURL;

    var res = str.charAt(str.length-1);

    if(res == '/')

      requestURL += '?hearing_type='+$("#myTab li.active").attr('id')+'&projectid='+project_id;

    requestGetJSON(requestURL).done(function (response) {

        _hearing_init_data(response, id);

    }).fail(function (data) {

        alert_float('danger', data.responseText);

    });

}



// Add lead data returned from server to the lead modal

function _hearing_init_data(data, id) {

   

    var hash = window.location.hash;



    var $hearingModal = $('#hearing-modal');

    //$('#lead_reminder_modal').html(data.leadView.reminder_data);



    $hearingModal.find('.data').html(data.leadView.data);

   // $hearingModal.find('select[name="project_id"]').selectpicker('val',project_id);

    $hearingModal.find('#div_hearing_project').hide();

    //$hearingModal.find('#hearing_type').selectpicker('val',$("#myTab li.active").attr('id'));

    $hearingModal.find('select[name="h_instance_id"]').selectpicker('val',$("#myTab li.active").attr('id'));
	$hearingModal.find('#div_stage_project').hide();
	
    


    



    $hearingModal.modal({

        show: true,

        backdrop: 'static'

    });

    tinymce.remove('#comments');

    init_editor('#comments');

    tinymce.remove('#proceedings');

    init_editor('#proceedings');

    init_selectpicker();

    init_datepicker();

    validate_hearing_form();

}

// Lead form validation

function validate_hearing_form() {

    var validationObject = {

        subject: 'required',

        hearing_date : 'required',

        court_no: 'required',

        h_instance_id:'required'

    };



    var messages = {};



    appValidateForm($('#hearing-form'), validationObject, hearing_form_handler, messages);

}

// Lead profile data function form handler

function hearing_form_handler(form) {

    form = $(form);

    var data = form.serialize();

    var leadid = $('#hearing-modal').find('input[name="leadid"]').val();

   $('#proceedings').val(tinyMCE.get('proceedings').getContent());

   $('#comments').val(tinyMCE.get('comments').getContent());

    //$('.lead-save-btn').addClass('disabled');

    $.post(form.attr('action'), data).done(function (response) {

        response = JSON.parse(response);

        if (response.message !== '') {

            alert_float('success', response.message);

        }

        if (response.proposal_warning && response.proposal_warning != false) {

            $("body").find('#lead_proposal_warning').removeClass('hide');

            $("body").find('#lead-modal').animate({

                scrollTop: 0

            }, 800);

        } else {

            _hearing_init_data(response, response.id);

        }
       windows.location.reload();
        //if ($.fn.DataTable.isDataTable('.table-leads')) {

          //  hearingTable(response.hearing_type,project_id);

         //   hearingTable('all',project_id);

        //} 

    }).fail(function (data) {

        alert_float('danger', data.responseText);

        return false;

    });

    return false;

}



 function setHearingId(hearingId){

    $('#hid_hearing_id').val(hearingId);

  }

const queryString = window.location.search;

const urlParams = new URLSearchParams(queryString);

const product = urlParams.get('group');

if(product == 'hearings'){ 

    courtClick('all', 'all');

}



</script>