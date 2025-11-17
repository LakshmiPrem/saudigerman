<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Files extends ClientsController
{
    public function index($hash)
    {
       

      

        $this->disableNavigation();
        $this->disableSubMenu();

       
        $data['bodyclass'] = 'contract contract-view';
        $data['identity_confirmation_enabled'] = true;
        $data['bodyclass'] .= ' identity-confirmation';
        $this->app_scripts->theme('sticky-js','assets/plugins/sticky/sticky.js');
        $this->app_css->add('app-css', base_url($this->app_css->core_file('assets/css', 'style.css')) );
        $this->load->model('contracts_model');
        $data['file_data']=$this->contracts_model->get_contracts_by_hash($hash);
        $data['password']=$this->contracts_model->get_file_otp_by_hash($hash);
        $this->app_css->remove('reset-css','customers-area-default');
        $data                      = hooks()->apply_filters('contract_customers_area_view_data', $data);
        // print_r($data);
        $this->data($data);
        $this->view('publicfiles');
        $this->layout(); 
    }
    public function get_otp_old($id)
    {
        $this->load->model('emails_model');
       $data['party_uploademail']=$this->input->get('party_uploademail');
       $hash=$this->input->get('otherparty_hash');
       // Generate a random 4-digit OTP
$otp = rand(1000, 9999);

       $subject="Your One-Time Password (OTP)";
       $message = '
<html>
<head>
  <style>
    .container {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      color: #333;
    }
    .otp-box {
      display: inline-block;
      background-color: #007bff;
      color: #fff;
      font-size: 24px;
      font-weight: bold;
      letter-spacing: 3px;
      padding: 10px 20px;
      border-radius: 8px;
      margin: 10px 0;
    }
    .copy-btn {
      background-color: #28a745;
      color: #fff;
      border: none;
      padding: 8px 14px;
      font-size: 14px;
      border-radius: 5px;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Your OTP Code</h2>
    <p>Please use the OTP below to complete your verification:</p>
    <div class="otp-box" id="otp">'.$otp.'</div><br>
    <button class="copy-btn" onclick="copyOTP()">Copy OTP</button>
    <p style="font-size: 13px; color: #777;">This OTP is valid for the next 10 minutes.</p>
  </div>

  <script>
    function copyOTP() {
      var otp = document.getElementById("otp").innerText;
      navigator.clipboard.writeText(otp);
      alert("OTP copied to clipboard: " + otp);
    }
  </script>
</body>
</html>
';
// print_r($data['party_uploademail']);
        // $success = $this->emails_model->send_simple_email( $data['party_uploademail'], $subject, $message);
        // print_r($success);
         $this->load->model('contracts_model');
         $data['party_otp']=$otp;
        $success=$this->contracts_model->update_contract_otp($id,$data);
        // print_r($success);
        if ($success == true) {
            $message =  _l('otp_send_successfull');
                $updated          = true;
              
               }else{
               
                $message = '';
                $updated          = false;
           }
           set_alert('success',  $message);
        //    redirect($_SERVER['HTTP_REFERER']);
        // $this->index($hash);
            
    }
    public function get_otp($id)
{
    $this->load->model('emails_model');
    $data['party_uploademail'] = $this->input->get('party_uploademail');
    $hash = $this->input->get('otherparty_hash');

    // Generate a random 4-digit OTP
    $otp = rand(1000, 9999);

    $subject = "Your One-Time Password (OTP)";
    $message = '
<html>
<head>
  <style>
    .container {
      font-family: Arial, sans-serif;
      background-color: #f9f9f9;
      border-radius: 10px;
      padding: 20px;
      text-align: center;
      color: #333;
    }
    .otp-box {
      display: inline-block;
      background-color: #007bff;
      color: #fff;
      font-size: 24px;
      font-weight: bold;
      letter-spacing: 3px;
      padding: 10px 20px;
      border-radius: 8px;
      margin: 10px 0;
      user-select: all; /* Allows easy copy */
    }
    .note {
      font-size: 13px;
      color: #777;
    }
    .copy-hint {
      font-size: 12px;
      color: #555;
      margin-top: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Your OTP Code</h2>
    <p>Please use the OTP below to complete your verification:</p>
    <div class="otp-box">' . $otp . '</div>
    <div class="copy-hint">(Select and copy the OTP above)</div>
    <p class="note">This OTP is valid for the next 10 minutes.</p>
  </div>
</body>
</html>';

$success = $this->emails_model->send_simple_email( $data['party_uploademail'], $subject, $message);
    $this->load->model('contracts_model');
    $data['party_otp'] = $otp;
    $success = $this->contracts_model->update_contract_otp($id, $data);

    if ($success) {
        $response = [
            'status'  => 'success',
            'message' => 'OTP saved successfully',
            'otp'     => $otp
        ];
    } else {
        $response = [
            'status'  => 'error',
            'message' => 'Failed to save OTP'
        ];
    }

    // Return JSON so AJAX can read it
    echo json_encode($response);
    exit;
}

    public function upload($id,$hash)
{
    // print_r('test1');
 $success=handle_project_contract_file_upload($id);
   if ($success == true) {
            $message =  _l('contract_upload_successfull');
                $updated          = true;
              
               }else{
               
                $message = '';
                $updated          = false;
           }
           set_alert('success',  $message);
           $this->index($hash);
}

public function pdf($id)
    {
      
       
$this->load->model('contracts_model');
        $contract = $this->contracts_model->get($id);

        try {
            $pdf = contract_pdf($contract);
        } catch (Exception $e) {
            echo $e->getMessage();
            die;
        }

        $type = 'D';

        if ($this->input->get('output_type')) {
            $type = $this->input->get('output_type');
        }

        if ($this->input->get('print')) {
            $type = 'I';
        }

        $pdf->Output(slug_it($contract->subject) . '.pdf', $type);
    }

}
