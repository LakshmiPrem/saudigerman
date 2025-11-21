<?php
/**
 * Included in application/views/admin/clients/client.php
 */
?>
<script>
Dropzone.options.clientAttachmentsUpload = false;
var customer_id = $('input[name="userid"]').val();
$(function() {

    if ($('#client-attachments-upload').length > 0) {
        new Dropzone('#client-attachments-upload',$.extend({},_dropzone_defaults(),{
            paramName: "file",
            accept: function(file, done) {
                done();
            },
            success: function(file, response) {
                if (this.getUploadingFiles().length === 0 && this.getQueuedFiles().length === 0) {
                    window.location.reload();
                }
            }
        }));
    }

    // Save button not hidden if passed from url ?tab= we need to re-click again
    if (tab_active) {
        $('body').find('.nav-tabs [href="#' + tab_active + '"]').click();
    }

    $('a[href="#contacts"],a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').addClass('hide');
    });

    $('.profile-tabs a').not('a[href="#contacts"],a[href="#customer_admins"]').on('click', function() {
        $('.btn-bottom-toolbar').removeClass('hide');
    });

    $("input[name='tasks_related_to[]']").on('change', function() {
        var tasks_related_values = []
        $('#tasks_related_filter :checkbox:checked').each(function(i) {
            tasks_related_values[i] = $(this).val();
        });
        $('input[name="tasks_related_to"]').val(tasks_related_values.join());
        $('.table-rel-tasks').DataTable().ajax.reload();
    });

    var contact_id = get_url_param('contactid');
    if (contact_id) {
        contact(customer_id, contact_id);
        $('a[href="#contacts"]').click();
    }

    $('body').on('change', '.onoffswitch input.customer_file', function(event, state) {
        var invoker = $(this);
        var checked_visibility = invoker.prop('checked');
        var share_file_modal = $('#customer_file_share_file_with');
        setTimeout(function() {
            $('input[name="file_id"]').val(invoker.attr('data-id'));
            if (checked_visibility && share_file_modal.attr('data-total-contacts') > 1) {
                share_file_modal.modal('show');
            } else {
                do_share_file_contacts();
            }
        }, 200);
    });
    // If user clicked save and add new contact
    var new_contact = get_url_param('new_contact');
    if (new_contact) {
        contact(customer_id);
        $('a[href="#contacts"]').click();
    }
    $('.customer-form-submiter').on('click', function() {
        var form = $('.client-form');
        if (form.valid()) {
            if ($(this).hasClass('save-and-add-contact')) {
                form.find('.additional').html(hidden_input('save_and_add_contact', 'true'));
            } else {
                form.find('.additional').html('');
            }
            form.submit();
        }
    });

    if (typeof(Dropbox) != 'undefined' && $('#dropbox-chooser').length > 0) {
        document.getElementById("dropbox-chooser").appendChild(Dropbox.createChooseButton({
            success: function(files) {
                $.post(admin_url + 'clients/add_external_attachment', {
                    files: files,
                    clientid: customer_id,
                    external: 'dropbox'
                }).done(function() {
                    window.location.reload();
                });
            },
            linkType: "preview",
            extensions: app_allowed_files.split(','),
        }));
    }

    var client_id = '';
    <?php if(isset($client)){?>
      client_id = '<?php echo $client->client_id; ?>';
    <?php } ?>

    /* Custome profile tickets table */
    var ticketsNotSortable = $('.table-tickets-single').find('th').length - 1;
    _table_api = initDataTable('.table-tickets-single', admin_url + 'tickets/index/false/' + client_id, [ticketsNotSortable], [ticketsNotSortable], 'undefined', [$('table thead .ticket_created_column').index(), 'DESC'])
    if (_table_api) {
        _table_api.column(5).visible(false, false).columns.adjust();
    }
   /* Custome profile contracts table */
    var contractsNotSortable = $('.table-contracts-single-client').find('th').length - 1;
	
    _table_api = initDataTable('.table-contracts-single-client', admin_url + 'contracts/table/'+client_id+'/' + customer_id+'/'+'contracts', [contractsNotSortable], [contractsNotSortable], 'undefined', [0, 'DESC']);
/**********************************************************************************************/
      /* Custome profile documents table */
    var documentsNotSortable = $('.table-documents-single-client').find('th').length - 1;
    _table_api = initDataTable('.table-documents-single-client', admin_url + 'documents/table/' + customer_id, [documentsNotSortable], [documentsNotSortable], 'undefined', [3, 'DESC']);
/*********************************************************************************************/
    var CasediaryNotSortable = $('.table-casediary-single-client').find('th').length - 1;
    console.log('Not Sortable'+CasediaryNotSortable);
    initDataTable('.table-casediary-single-client', admin_url + 'casediary/recovery_table/' + customer_id, [CasediaryNotSortable], [CasediaryNotSortable], 'undefined',[3, 'DESC']);
/**********************************************************************************************/
    /* Custome profile contacts table */
    var contactsNotSortable = $('.table-contacts').find('th').length - 1;
    initDataTable('.table-contacts', admin_url + 'clients/contacts/' + customer_id, [contactsNotSortable], [contactsNotSortable]);

    /* Customer profile invoices table */
    initDataTable('.table-invoices-single-client',
        admin_url + 'invoices/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'DESC'],
            [0, 'DESC']
        ]);

   initDataTable('.table-credit-notes', admin_url+'credit_notes/table/'+customer_id, ['undefined'], ['undefined'], undefined, [0, 'DESC']);

    /* Custome profile Estimates table */
    initDataTable('.table-estimates-single-client',
        admin_url + 'estimates/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [
            [3, 'DESC'],
            [0, 'DESC']
        ]);



    /* Custome profile payments table */
    initDataTable('.table-payments-single-client',
        admin_url + 'payments/table/' + customer_id, [7], [7],
        'undefined', [6, 'DESC']);

    /* Custome profile reminders table */
    initDataTable('.table-reminders', admin_url + 'misc/get_reminders/' + customer_id + '/' + 'oppositeparty', [3], [3], undefined, [1, 'ASC']);

    /* Custome profile expenses table */
    initDataTable('.table-expenses-single-recovery',
        admin_url + 'expenses/recovery_table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [5, 'DESC']);

    /* Custome profile proposals table */
    initDataTable('.table-proposals-client-profile',
        admin_url + 'proposals/proposal_relations/' + customer_id + '/customer',
        'undefined',
        'undefined',
        'undefined', [6, 'DESC']);

    /* Custome profile projects table */ 

    var notSortableProjects = $('.table-projects-single-client').find('th').length - 1;
    initDataTable('.table-projects-single-client', admin_url + 'projects/tableoppo/'+ customer_id, [notSortableProjects], [notSortableProjects], 'undefined', <?php echo hooks()->apply_filters('projects_table_default_order',json_encode(array(5,'ASC'))); ?>);


/* Customer profile expenses table */
    initDataTable('.table-expenses-single-client',
        admin_url + 'expenses/table/'+client_id+'/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [5, 'desc']);
        
        
/* Custome profile expenses table */
    /*initDataTable('.table-receipts-single-client',
        admin_url + 'receipts/table/' + customer_id,
        'undefined',
        'undefined',
        'undefined', [5, 'DESC']);*/
    var vRules = {};
   
    vRules = {
       // client_id:'required',
		// tradelicence:'required',
		city:'required',
        name: 'required',
    }

    _validate_form($('.client-form'), vRules);

    $('.billing-same-as-customer').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="billing_street"]').val($('textarea[name="address"]').val());
        $('input[name="billing_city"]').val($('input[name="city"]').val());
        $('input[name="billing_state"]').val($('input[name="state"]').val());
        $('input[name="billing_zip"]').val($('input[name="zip"]').val());
        $('select[name="billing_country"]').selectpicker('val', $('select[name="country"]').selectpicker('val'));
    });

    $('.customer-copy-billing-address').on('click', function(e) {
        e.preventDefault();
        $('textarea[name="shipping_street"]').val($('textarea[name="billing_street"]').val());
        $('input[name="shipping_city"]').val($('input[name="billing_city"]').val());
        $('input[name="shipping_state"]').val($('input[name="billing_state"]').val());
        $('input[name="shipping_zip"]').val($('input[name="billing_zip"]').val());
        $('select[name="shipping_country"]').selectpicker('val', $('select[name="billing_country"]').selectpicker('val'));
    });

    $('body').on('hidden.bs.modal', '#contact', function() {
        $('#contact_data').empty();
    });

    $('.client-form').on('submit', function() {
        $('select[name="default_currency"]').prop('disabled', false);
    });

});

function delete_contact_profile_image(contact_id) {
    requestGet('clients/delete_contact_profile_image/'+contact_id).done(function(){
        $('body').find('#contact-profile-image').removeClass('hide');
        $('body').find('#contact-remove-img').addClass('hide');
        $('body').find('#contact-img').attr('src', '<?php echo base_url('assets/images/user-placeholder.jpg'); ?>');
    });
}

function validate_contact_form() {
    _validate_form('#contact-form', {
        firstname: 'required',
        lastname: 'required',
        password: {
            required: {
                depends: function(element) {
                    var sent_set_password = $('input[name="send_set_password_email"]');
                    if ($('#contact input[name="contactid"]').val() == '' && sent_set_password.prop('checked') == false) {
                        return true;
                    }
                }
            }
        },
        email: {
            required: true,
            email: true,
            // Use this hook only if the contacts are not logging into the customers area and you are not using support tickets piping.
            <?php if(hooks()->apply_filters('contact_email_unique',"true") === "true"){ ?>
            remote: {
                url: admin_url + "misc/contact_email_exists",
                type: 'post',
                data: {
                    email: function() {
                        return $('#contact input[name="email"]').val();
                    },
                    userid: function() {
                        return $('body').find('input[name="contactid"]').val();
                    }
                }
            }
            <?php } ?>
        }
    }, contactFormHandler);
}

function contactFormHandler(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);
    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
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
            if (response.success) {
                alert_float('success', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }
            }
            if ($.fn.DataTable.isDataTable('.table-contacts')) {
                $('.table-contacts').DataTable().ajax.reload(null,false);
            }
            if (response.proposal_warning && response.proposal_warning != false) {
                $('body').find('#contact_proposal_warning').removeClass('hide');
                $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
                $('#contact').animate({
                    scrollTop: 0
                }, 800);
            } else {
                $('#contact').modal('hide');
            }
            if(response.has_primary_contact == true){
                $('#client-show-primary-contact-wrapper').removeClass('hide');
            }
    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}

function contact(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    $.post(admin_url + 'clients/contact/' + client_id + '/' + contact_id).done(function(response) {
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
        validate_contact_form();
    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}

function update_all_proposal_emails_linked_to_contact(contact_id) {
    var data = {};
    data.update = true;
    data.original_email = $('body').find('#contact_update_proposals_emails').data('original-email');
    $.post(admin_url + 'clients/update_all_proposal_emails_linked_to_customer/' + contact_id, data).done(function(response) {
        response = JSON.parse(response);
        if (response.success) {
            alert_float('success', response.message);
        }
        $('#contact').modal('hide');
    });
}

function do_share_file_contacts(edit_contacts, file_id) {
    var contacts_shared_ids = $('select[name="share_contacts_id[]"]');
    if (typeof(edit_contacts) == 'undefined' && typeof(file_id) == 'undefined') {
        var contacts_shared_ids_selected = $('select[name="share_contacts_id[]"]').val();
    } else {
        var _temp = edit_contacts.toString().split(',');
        for (var cshare_id in _temp) {
            contacts_shared_ids.find('option[value="' + _temp[cshare_id] + '"]').attr('selected', true);
        }
        contacts_shared_ids.selectpicker('refresh');
        $('input[name="file_id"]').val(file_id);
        $('#customer_file_share_file_with').modal('show');
        return;
    }
    var file_id = $('input[name="file_id"]').val();
    $.post(admin_url + 'clients/update_file_share_visibility', {
        file_id: file_id,
        share_contacts_id: contacts_shared_ids_selected,
        customer_id: $('input[name="userid"]').val()
    }).done(function() {
        window.location.reload();
    });
}

function save_longitude_and_latitude(clientid) {
    var data = {};
    data.latitude = $('#latitude').val();
    data.longitude = $('#longitude').val();
    data.google_map_url = $('#google_map_url').val();

    $.post(admin_url + 'corporate_recoveries/save_longitude_and_latitude/'+clientid, data).done(function(response) {
       if(response == 'success') {
            alert_float('success', "<?php echo _l('updated_successfully', _l('recovery')); ?>");
       }
        setTimeout(function(){
            window.location.reload();
        },1200);
    }).fail(function(error) {
        alert_float('danger', error.responseText);
    });
}

function fetch_lat_long_from_google_cprofile() {
    var data = {};
    data.address = $('#long_lat_wrapper').data('address');
    data.city = $('#long_lat_wrapper').data('city');
    data.country = $('#long_lat_wrapper').data('country');
    $('#gmaps-search-icon').removeClass('fa-google').addClass('fa-spinner fa-spin');
    $.post(admin_url + 'misc/fetch_address_info_gmaps', data).done(function(data) {
        data = JSON.parse(data);
        $('#gmaps-search-icon').removeClass('fa-spinner fa-spin').addClass('fa-google');
        if (data.response.status == 'OK') {
            $('input[name="latitude"]').val(data.lat);
            $('input[name="longitude"]').val(data.lng);
        } else {
            if (data.response.status == 'ZERO_RESULTS') {
                alert_float('warning', "<?php echo _l('g_search_address_not_found'); ?>");
            } else {
                alert_float('danger', data.response.status);
            }
        }
    });
}


var contactsNotSortable = $('.table-installments').find('th').length - 1;
    initDataTable('.table-installments', admin_url + 'corporate_recoveries/installments/' + customer_id, [contactsNotSortable], [contactsNotSortable]);
function installment(client_id, contact_id) {
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    $.post(admin_url + 'corporate_recoveries/installment/' + client_id +'/'+contact_id).done(function(response) {
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="firstname"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
        validate_installment_form();

    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}

function validate_installment_form() {
    _validate_form('#contact-form', {
        installment_amount: 'required',
        installment_date: 'required',
        
    },installmentFormHandler);
}

function installmentFormHandler(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);
    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
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

            if (response.success) {
                alert_float('success', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }


            }
            if ($.fn.DataTable.isDataTable('.table-installments')) {
                $('.table-installments').DataTable().ajax.reload(null,false);
            }
            if (response.proposal_warning && response.proposal_warning != false) {
                $('body').find('#contact_proposal_warning').removeClass('hide');
                $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
                $('#contact').animate({
                    scrollTop: 0
                }, 800);
            } else {
                $('#contact').modal('hide');
            }
            if(response.has_primary_contact == true){
                $('#client-show-primary-contact-wrapper').removeClass('hide');
            }

            var credit_limit = $('#hid_credit_limit').val();
            var balance = parseFloat(credit_limit) - parseFloat(response.totalpaid);
            $('#def_total_paid').html(formatMoney(response.totalpaid));
            $('#def_balance').html(formatMoney(balance));
             

            
            

    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}


  function update_settlement_type(settlement_type,defaulterID){
    
    var form_URL =  admin_url + 'corporate_recoveries/update_settlement_type/'+settlement_type+'/'+defaulterID
    $.ajax({
        mimeType: "multipart/form-data",
        contentType: false,
        cache: false,
        processData: false,
        url: form_URL
    }).done(function(response){
        alert_float('success', 'Settlement Type Updated');
    }).fail(function(error){
        alert_float('danger', 'Error');
    });
  }  

  /*$('#number_of_installments').change(function(){
        if(confirm('Do you want to reset installments ? ')){
            var num_inst  =  $('#number_of_installments').val();
        var date_inst = $('#installment_start_date').val();
        var form_URL =  admin_url + 'debt_collections/reset_installments/'+num_inst+'/'+date_inst+'/'+customer_id;
        $.ajax({
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            url: form_URL
        }).done(function(response){ 
            $('.table-installments').DataTable().ajax.reload(null,false);
        }).fail(function(error){
            alert_float('danger', 'Error');
        });
        }
        
  });*/
  $('#btn_installment').click(function(){
        if(confirm('Do you want to reset installments ? ')){
            var num_inst  =  $('#number_of_installments').val();
        var date_inst = $('#installment_start_date').val();
        var form_URL =  admin_url + 'corporate_recoveries/reset_installments/'+num_inst+'/'+date_inst+'/'+customer_id;
        $.ajax({
            mimeType: "multipart/form-data",
            contentType: false,
            cache: false,
            processData: false,
            url: form_URL
        }).done(function(response){ 
            $('.table-installments').DataTable().ajax.reload(null,false);
            alert_float('success', 'Successfully Updated');
        }).fail(function(error){
            alert_float('danger', 'Error');
        });
        }
        
  });
function partner(id='') {
    url =  admin_url + 'corporate_recoveries/partner/'+id;
    requestGet(url).done(function(response) {
        $('#_partner').html(response);
        $("body").find('#_partner_modal').modal({ show: true, backdrop: 'static' });
        $('#additional').append(hidden_input('recovery_id',customer_id));
    });
}
 
    $(function(){
     _validate_form($('#import_form'),{file_csv:{required:true,extension: "csv"}});
     $('.btn-import-submit').on('click',function(){
       /*if($(this).hasClass('simulate')){
         $('#import_form').append(hidden_input('simulate',true));
       }*/
       $('#import_form').submit();
     });
   });
    
  $('#contact_code').change(function(){ 
    if ($(this).val() == '8') { $('.ptp_div').removeClass('hide'); }else{ $('.ptp_div').addClass('hide'); } 
  });

  function reload_tbl(){
     $('.table-installments').DataTable().ajax.reload();
  }

  function append_notify(installment_id){
    $('#addiT').html('');
    $('#addiT').append(hidden_input('installment_id',installment_id));
  }
	
	/*
	Contact form adding
	*/
	/*var  client_id = '<?php echo $client->id; ?>';
	var contactsNotSortable = $('.table-defenders').find('th').length - 1;
    var insTable =   initDataTable('.table-defenders', admin_url + 'opposite_parties/defendars/' + client_id, [contactsNotSortable], [contactsNotSortable]);
    setTimeout(function(){
        init_datepicker();
    },1000);*/
	function defendar(client_id, contact_id) {
	
    if (typeof(contact_id) == 'undefined') {
        contact_id = '';
    }
    $.post(admin_url + 'opposite_parties/defendar/' + client_id +'/'+contact_id).done(function(response) {
		//alert(response);
        $('#contact_data').html(response);
        $('#contact').modal({
            show: true,
            backdrop: 'static'
        });
        $('body').off('shown.bs.modal','#contact');
        $('body').on('shown.bs.modal', '#contact', function() {
            if (contact_id == '') {
                $('#contact').find('input[name="contact_name"]').focus();
            }
        });
        init_selectpicker();
        init_datepicker();
        custom_fields_hyperlink();
        validate_defendar_form();

    }).fail(function(error) {
        var response = JSON.parse(error.responseText);
        alert_float('danger', response.message);
    });
}

function validate_defendar_form() {
    _validate_form('#contact-form1', {
        party_type: 'required',
        contact_name:'required',
       // emailid: 'required',
        
    },defendarFormHandler);
}

function defendarFormHandler(form) {
    $('#contact input[name="is_primary"]').prop('disabled', false);
    var formURL = $(form).attr("action");
    var formData = new FormData($(form)[0]);
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

            if (response.success) {
                alert_float('success', response.message);
                if(typeof(response.is_individual) != 'undefined' && response.is_individual) {
                    $('.new-contact').addClass('disabled');
                    if(!$('.new-contact-wrapper')[0].hasAttribute('data-toggle')) {
                        $('.new-contact-wrapper').attr('data-toggle','tooltip');
                    }
                }

 				window.location.reload();
            }
          /*  if ($.fn.DataTable.isDataTable('.table-defenders')) {
                $('.table-defenders').DataTable().ajax.reload(null,false);

            }*/
            if (response.proposal_warning && response.proposal_warning != false) {
                $('body').find('#contact_proposal_warning').removeClass('hide');
                $('body').find('#contact_update_proposals_emails').attr('data-original-email', response.original_email);
                $('#contact').animate({
                    scrollTop: 0
                }, 800);
            } else {
                $('#contact').modal('hide');
            }
            if(response.has_primary_contact == true){
                $('#client-show-primary-contact-wrapper').removeClass('hide');
            }

          //  var credit_limit =outstand; //$('#hid_credit_limit').val();
		//    var balance = parseFloat(credit_limit) - parseFloat(response.totalpaid);
          // $('#def_total_paid').html('AED' +response.totalpaid);
           // $('#def_balance').html('AED' +balance);
             

            
            

    }).fail(function(error){
        alert_float('danger', JSON.parse(error.responseText));
    });
    return false;
}
$('#type').on('change', function() {
    var type = $('select[name="type"]').val();
    // alert(type);
    if(type==1){
        $('#company').addClass('hide');
        $('#individual').removeClass('hide');
    }else{
        $('#individual').addClass('hide');
        $('#company').removeClass('hide');
    }
                
            });

</script>
