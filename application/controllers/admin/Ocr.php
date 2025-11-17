<?php
use thiagoalessio\TesseractOCR\TesseractOCR;

defined('BASEPATH') or exit('No direct script access allowed');

class Ocr extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        //$this->load->model('oppositeparty_model');   
     
    }

    /* List all opposite parties */
    public function index()
    {
        $data['title'] = _l('ocr').' '._l('(Optical Character Recognition)');
        $this->load->view('admin/ocr/manage', $data);
    }
    public function ocr_tesseract()
    {
        
        $file_name=handle_image_file_upload();
        if($file_name){
            $path=get_upload_path_by_type('ocrimage');
            $fileRead = (new TesseractOCR($path . $file_name))
                //->tessdataDir('/uploads/' . $file_name)
                ->setLanguage('eng')
                ->run();
                if($fileRead){
                    echo json_encode(array(
                        'text' => $fileRead,
                    ));    
                }      
        }
    }

    public function ocr()
    {
        
        $file_name=handle_image_file_upload();
        if($file_name){
             $path=get_upload_path_by_type('ocrimage');
             $target_file=$path . $file_name;
             $fileData = fopen($target_file, 'r');
             $client = new \GuzzleHttp\Client();
             $r = $client->request('POST', 'https://api.ocr.space/parse/image',[
                'headers' => ['apiKey' => 'K81489105088957'],
                'multipart' => [
                    [
                        'name' => 'file',
                        'contents' => $fileData
                    ]
                ]
            ], ['file' => $fileData]);
            $response =  json_decode($r->getBody(),true);
            foreach($response['ParsedResults'] as $pareValue) {
                echo json_encode(array(
                    'text' => $pareValue['ParsedText'],
                ));
                
            }
             
            //$data=$response['ParsedResults'];
            //print_r($data['ParsedText']);
            // foreach($response['ParsedResults'] as $pareValue) {
            //     print_r($pareValue['ParsedText']);
            // }
             
        }
    }
}