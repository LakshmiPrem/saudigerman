<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require APPPATH . 'libraries/REST_Controller.php';
class Token extends REST_Controller {
    /** 
     * @response array
     */
    public function index_get() {
        $data = $this->Api_model->create_token($this->api_customer_id);

        // ***** Response ******
        $http_code = $data['http_code'];
        unset($data['http_code']);
        $this->response($data, $http_code);
    }
}
?>