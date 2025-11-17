<script type="text/javascript">

      $('.save_changes_expense_approval_ticket').on('click', function(e) {

        e.preventDefault();

        var approval_name = $('input[name="approval_name"]').val();

        approval_name = approval_name.trim();

        if(approval_name == '' ){

            return false;

        }

        if(approval_name.length > 0){

           var data={};

            data = $('#approval *').serialize();

            data += '&ticketid=' + project_id;

            if (typeof(csrfData) !== 'undefined') {

                data += '&' + csrfData['token_name'] + '=' + csrfData['hash'];

            }

            console.log(data);

            $.post(admin_url + 'projects/update_expense_approvals', data).done(function(response) {

                response = JSON.parse(response);

                console.log(response);

                if (response.success == true) {

                    $('input[name="approval_name"]').val(' ');

                    $('input[name="approval_name"]').val('');

                   window.location.reload();               

                }else{

                    alert_float('danger','Something went wrong...Please check Approval Name ');

                }

            }); 

        }else{

            alert();

        }

        

    });



   function update_approval_status(th,id) {

        var status = $(th).val();

        requestGetJSON('projects/change_approval_status_ajax/' + id + '/' + status).done(function(response) {



            alert_float(response.alert, response.message);

			 window.location.reload();  

        });

    };



   function update_approval_remarks(th,id) {

      var remarks = $(th).val();

      var data={"remarks" : remarks};

      $.post(admin_url + 'projects/change_approval_remarks_ajax/'+id, data).done(function(response) {

         response = JSON.parse(response);

         alert_float(response.alert, response.message);

      });

   } 

 

   function change_expense_approval_status(th,id) {

        var status = $(th).val();

        requestGetJSON('projects/change_expense_approval_status_ajax/' + id + '/' + status).done(function(response) {

            alert_float(response.alert, response.message);

        });

    };

	 
	 $('#project-expense-form input[name="amount"] ,input[name="last_amount"] ,input[name="balance_amount"],input[name="vat_amount"]').blur(function(){

		

         var total_amount = $('#project-expense-form input[name="amount"]').val();

		

         var paid_amount = $('#project-expense-form input[name="last_amount"]').val();

		 var vat_amount = $('#project-expense-form input[name="vat_amount"]').val();

         var balance = $('#project-expense-form input[name="balance_amount"]').val();

		 if(isNaN(vat_amount)||vat_amount=='')vat_amount=0;

		 if(isNaN(paid_amount)||paid_amount=='')paid_amount=0;

		  var balance = total_amount - paid_amount-vat_amount;

         $('#project-expense-form input[name="balance_amount"]').val(parseFloat(balance).toFixed(2));

         

    });

	

</script>