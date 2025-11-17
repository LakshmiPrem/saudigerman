<!---  court instance ------------->

<script type="text/javascript">

   

function init_court_instance(id ) { 

    if ($('#task-modal').is(':visible')) {

        $('#task-modal').modal('hide');

    }

    // In case header error

    if (init_court_instance_modal_data(id, undefined)) {

        $('#court-instance-modal').modal('show');

    }

   }

   // Fetches  modal data, can be edit/add/view

function init_court_instance_modal_data(id, url) {

    var requestURL = (typeof (url) != 'undefined' ? url : 'projects/court_instance/') + (typeof (id) != 'undefined' ? id : '');

    requestGetJSON(requestURL).done(function (response) {

        _court_instance_init_data(response, id);

    }).fail(function (data) {

        alert_float('danger', data.responseText);

    });

}



// Add lead data returned from server to the lead modal

function _court_instance_init_data(data, id) {

    var hash = window.location.hash;



    var $hearingModal = $('#court-instance-modal');

    //$('#lead_reminder_modal').html(data.leadView.reminder_data);



    $hearingModal.find('.data').html(data.leadView.data);

    $hearingModal.find('input.project_id').val(project_id);
	client_posi = '<?php echo $project->client_position; ?>';
	oppose_posi = '<?php echo $project->oppositeparty_position; ?>';
	$hearingModal.find('select[name="client_position"]').selectpicker('val',client_posi);
	$hearingModal.find('select[name="opposite_party_position"]').selectpicker('val',oppose_posi);

    $hearingModal.modal({

        show: true,

        backdrop: 'static'

    });

    tinymce.remove('#case_details');

    init_editor('#case_details');

    tinymce.remove('#details_of_claim');

    init_editor('#details_of_claim');

    init_selectpicker();

    init_datepicker();

    validate_court_instance_form();

}

// Lead form validation

function validate_court_instance_form() {

    var validationObject = {

        details_type: 'required',

        case_number : 'required',

        court_no: 'required',

    };



    var messages = {};



    appValidateForm($('#court-instance-form'), validationObject, court_instance_form_handler, messages);

}

// Lead profile data function form handler

function court_instance_form_handler(form) {

    form = $(form);

    var data = form.serialize();

    var leadid = $('#court-instance-modal').find('input[name="leadid"]').val();

   $('#case_details').val(tinyMCE.get('case_details').getContent());

   $('#details_of_claim').val(tinyMCE.get('details_of_claim').getContent());

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

            //_court_instance_init_data(response, response.id);

            window.location.reload();

        }

        

    }).fail(function (data) {

        alert_float('danger', data.responseText);

        return false;

    });

    return false;

}



   



 function setHearingId(hearingId){

    $('#hid_hearing_id').val(hearingId);

  }



</script>

