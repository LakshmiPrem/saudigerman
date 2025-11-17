<?php

/**

 * Included in application/views/admin/clients/client.php

 */

?>

<script>

	

	

	var project_id = '';

	var outstand='';

    <?php if(isset($project)){?>

      project_id = '<?php echo $project->id; ?>';

	 outstand = '<?php echo $project->outstanding_amount; ?>';

	//alert(project_id);

    <?php } ?>

	var contactsNotSortable = $('.table-installments').find('th').length - 1;

    var insTable =   initDataTable('.table-installments', admin_url + 'projects/installments/' + project_id, [contactsNotSortable], [contactsNotSortable]);

    setTimeout(function(){

        init_datepicker();

    },1000);

    

    



	function installment(client_id, contact_id) {

	//alert(client_id);

    if (typeof(contact_id) == 'undefined') {

        contact_id = '';

    }

    $.post(admin_url + 'projects/installment/' + client_id +'/'+contact_id).done(function(response) {

		//alert(response);

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

        amount: 'required',

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



            var credit_limit =outstand; //$('#hid_credit_limit').val();

		    var balance = parseFloat(credit_limit) - parseFloat(response.totalpaid);

            $('#def_total_paid').html('AED' +response.totalpaid);

            $('#def_balance').html('AED' +balance);

             



            

            



    }).fail(function(error){

        alert_float('danger', JSON.parse(error.responseText));

    });

    return false;

}

	$(function(){







    _validate_form('#installment-info-form', {

       // claiming_amount: 'required',

       // outstanding_amount: 'required',

      //  nature_of_settlement : 'required',

       // settlement_type : 'required'

    },manage_installment_info_form);



    function manage_installment_info_form(){

        if(confirm('Do you want to reset installments ? ')){

            $.ajax({

                type: "POST",

                mimeType: "multipart/form-data",

                url: $('#installment-info-form').attr('action'),

                data: $('#installment-info-form').serialize(),

            }).done(function(response){ 

                $('.table-installments').DataTable().ajax.reload();
                 window.location.reload();
                setTimeout(function(){

                    init_datepicker();

                },1000);

                alert_float('success', 'Successfully Updated');


            }).fail(function(error){

                alert_float('danger', 'Error');

            });

        }else{

            return false;

        }

    }







	 $('#btn_installmentpost').click(function(){

	   //stop submit the form, we will post it manually.

       // event.preventDefault();



        // Get form

        var form = $('#installementForm')[0];

        if(confirm('Do you want to reset installments ? ')){

			 // Create an FormData object

        var data = new FormData(form);

		alert($('#installementForm').attr('action'));	

        $.ajax({

           type: "POST",

            mimeType: "multipart/form-data",

            url: $('#installementForm').attr('action'),

            data: data,

            processData: false,

            contentType: false,

            cache: false

		 }).done(function(response){ 

            $('.table-installments').DataTable().ajax.reload();

            setTimeout(function(){

                init_datepicker();

            },1000);

            alert_float('success', 'Successfully Updated');

        }).fail(function(error){

            alert_float('danger', 'Error');

        });

        }

        

  });

	});

	$(function(){

    	/*$('#btn_installment').click(function(){

    	 

            if(confirm('Do you want to reset installments ? ')){

    			var project_id =$('input[name="projectid"]').val();

                var out_inst  =  $('#outstanding_amount').val();

    			 var exe_amt  =  $('#execution_amount').val();

    			var settle_type = $('#settlement_type').val();

    			var settle_desc = $('#installment_desc').val();

    			var nature_sett = $('#nature_of_settlement').val();

    			var no_installment = $('#no_of_installment').val();

    			var claim_amt = $('#claiming_amount').val();

    			var exe_percent = $('#execution_percent').val();

    			var dateString = $('#execution_duedate').val();

    			var exe_duedate = dateString.split("/").reverse().join("-");

    			var dateString1 = $('#installment_start_date').val();

    			var ins_startdate = dateString1.split("/").reverse().join("-");

    			$purl='projects/reset_installments/'+settle_type+'/'+nature_sett+'/'+out_inst+'/'+exe_amt+'/'+claim_amt+'/'+no_installment+'/'+exe_percent+'/'+exe_duedate+'/'+ins_startdate+'/'+settle_desc+'/';

                var form_URL =  admin_url +$purl+project_id;

        			alert(form_URL);

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

            

        });*/

	});



  function reload_tbl(){

     $('.table-installments').DataTable().ajax.reload();

  }



  function append_notify(installment_id){

    $('#addiT').html('');

    $('#addiT').append(hidden_input('installment_id',installment_id));

  }





	var outstand1='';

    <?php if(isset($project)){?>

    //  project_id = '<?php echo $project->id; ?>';

	 outstand1 = '<?php echo $project->claiming_amount; ?>';

	//alert(project_id);

    <?php } ?>

	var contactsNotSortable = $('.table-payinstallments').find('th').length - 1;

    initDataTable('.table-payinstallments', admin_url + 'projects/payinstallments/' + project_id, [contactsNotSortable], [contactsNotSortable]);



	function payinstallment(client_id, contact_id) {

	//alert(client_id);

    if (typeof(contact_id) == 'undefined') {

        contact_id = '';

    }

    $.post(admin_url + 'projects/payinstallment/' + client_id +'/'+contact_id).done(function(response) {

		//alert(response);

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

        payvalidate_installment_form();



    }).fail(function(error) {

        var response = JSON.parse(error.responseText);

        alert_float('danger', response.message);

    });

}



function payvalidate_installment_form() {

    _validate_form('#contact-form', {

        installment_amount: 'required',

        installment_date: 'required',

        

    },payinstallmentFormHandler);

}



function payinstallmentFormHandler(form) {

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

            if ($.fn.DataTable.isDataTable('.table-payinstallments')) {

                $('.table-payinstallments').DataTable().ajax.reload(null,false);

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



            var credit_limit =outstand1; //$('#hid_credit_limit').val();

		    var balance = parseFloat(credit_limit) - parseFloat(response.totalpaid);

            $('#def_total_paid').html(formatMoney(response.totalpaid));

            $('#def_balance').html(formatMoney(balance));

             



            

            



    }).fail(function(error){

        alert_float('danger', JSON.parse(error.responseText));

    });

    return false;

}

	$(function(){

	 $('#btn_payinstallment').click(function(){

	

        if(confirm('Do you want to reset installments ? ')){

			var project_id =$('input[name="projectid"]').val();

		

            var out_inst  =  $('#claiming_amount').val();

      //  var settle_type = $('#settlement_type').val();

		//	var nature_sett = $('#nature_of_settlement').val();

        var form_URL =  admin_url + 'projects/reset_payinstallments/'+out_inst+'/'+project_id;

        $.ajax({

            mimeType: "multipart/form-data",

            contentType: false,

            cache: false,

            processData: false,

            url: form_URL

        }).done(function(response){ 

			alert(response);

            $('.table-payinstallments').DataTable().ajax.reload(null,false);

            alert_float('success', 'Successfully Updated');

        }).fail(function(error){

            alert_float('danger', 'Error');

        });

        }

        

  });

	});



 function reload_tbl(){

     $('.table-installments').DataTable().ajax.reload();

     setTimeout(function(){

        init_datepicker();

     },1000);

  }



    $('#btn_save_installment_table_').click( function() { 

        var formData = insTable.$('input, select,textarea').serializeArray();

        formData.push({ 'name' :'csrf_token_name','value' : $('input[name="csrf_token_name"]').val()});

        $.ajax({

            url: admin_url+'projects/update_settlements_table/'+project_id,

            data:formData,

            type: 'POST',

        }).done(function(response){ 

            $('.table-installments').DataTable().ajax.reload(null,false);

            setTimeout(function(){

                init_datepicker();

            },1000);

            alert_float('success', 'Successfully Updated');

        }).fail(function(error){

            alert_float('danger', 'Error');

        });

    } );



    $('#addRow').on( 'click', function () {

        /*$('.table-installments').row.add( [

            counter +'.1',

            counter +'.2',

            counter +'.3',

            counter +'.4',

            counter +'.5'

        ] ).draw( false );

 

        counter++;*/

        var newRow = $('.table-installments').NewRow();

        $('.table-installments').Rows.Add(newRow);

        setTimeout(function(){

            init_datepicker();

        },1000);

    } );



</script>

