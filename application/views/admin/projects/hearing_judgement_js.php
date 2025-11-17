<!---  court instance ------------->
<script type="text/javascript">
   
function init_hearing_judgement(id,invoker) { 
	 var cat_type = $(invoker).data('type');
	if(cat_type!='ruling'){
		cat_type='judgement';
	}
	
    if ($('#task-modal').is(':visible')) {
        $('#task-modal').modal('hide');
    }
    // In case header error
    if (init_hearing_judgement_modal_data(id, undefined,cat_type)) {
        $('#hearing-judgement-modal').modal('show');
    }
   }
   // Fetches  modal data, can be edit/add/view
function init_hearing_judgement_modal_data(id, url,invoker) {
  
	
    var requestURL = (typeof (url) != 'undefined' ? url : 'projects/hearing_judgement/') + (typeof (id) != 'undefined' ? id : '');//+'?rule_type='+invoker
	
    requestGetJSON(requestURL).done(function (response) {
        _hearing_judgement_init_data(response, id,invoker);
    }).fail(function (data) {
        alert_float('danger', data.responseText);
    });
}

// Add lead data returned from server to the lead modal
function _hearing_judgement_init_data(data, id,invoker) {
	
    var hash = window.location.hash;

    var $hearingModal = $('#hearing-judgement-modal');
    //$('#lead_reminder_modal').html(data.leadView.reminder_data);

    $hearingModal.find('.data').html(data.leadView.data);
    $hearingModal.find('input.project_id').val(project_id);
    //$hearingModal.find('div.total_rate').html('<span class="badge"><?php echo _l('project_total_cost').' :'.$project->project_cost;?></span>');
	$hearingModal.find('#div_stagejudge_project').hide();
	var project_stage = '<?php echo $project->project_stage; ?>';
	if(typeof (id) == 'undefined' || id=='undefined'){
		
    $hearingModal.find('select[name="stage_id"]').selectpicker('val',project_stage);
	 $hearingModal.find('select[name="judgement_ruling"]').selectpicker('val',invoker);
	}
    $hearingModal.modal({
        show: true,
        backdrop: 'static'
    });
    init_selectpicker();
    init_datepicker();
    validate_hearing_judgement_form();
}
// Lead form validation
function validate_hearing_judgement_form() {
    var validationObject = {
        judgement_ruling: 'required',
        judgement_date : 'required',
        stage_id: 'required',
        judgement_ruling_status: 'required',
		decree_order_status: 'required',
    };

    var messages = {};

    appValidateForm($('#hearing-judgement-form'), validationObject, hearing_judgement_form_handler, messages);
}
// Lead profile data function form handler
function hearing_judgement_form_handler(form) {
	 form = $(form);
    var formURL = $(form).attr("action");
     var formData = new FormData($(form)[0]);
	//form = $('#hearing-judgement-form');
 
  //  var data = form.serialize();
    var leadid = $('#hearing-judgement-modal').find('input[name="leadid"]').val();
    $('#btn_hearing_judgement').addClass('disabled');
  //  $.post(form.attr('action'), data).done(function (response) {
	 $.ajax({
        type: 'POST',
        data: formData,
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: formURL
    }).done(function(response){
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
            //_payment_schedule_init_data(response, response.id);
            window.location.reload();
        }
        window.location.reload();  
    }).fail(function (data) {
        alert_float('danger', data.responseText);
        return false;
    });
    return false;
}

</script>