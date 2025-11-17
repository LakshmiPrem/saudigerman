<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Download extends App_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->helper('download');
    }

    public function preview_video()
    {
        $path      = FCPATH . $this->input->get('path');
        $file_type = $this->input->get('type');

        $allowed_extensions = get_html5_video_extensions();

        $pathinfo = pathinfo($path);

        if (!file_exists($path) || !isset($pathinfo['extension']) || !in_array($pathinfo['extension'], $allowed_extensions)) {
            $file_type = 'image/jpg';
            $path      = FCPATH . 'assets/images/preview-not-available.jpg';
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Type: ' . $file_type);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        if (ob_get_contents()) {
            ob_end_clean();
        }

        hooks()->do_action('before_output_preview_video');

        $file = fopen($path, 'rb');
        if ($file !== false) {
            while (!feof($file)) {
                echo fread($file, 1024);
            }
            fclose($file);
        }
    }

    public function preview_image()
    {
        $path      = FCPATH . $this->input->get('path');
        $file_type = $this->input->get('type');

        $allowed_extensions = [
            'jpg',
            'jpeg',
            'png',
            'bmp',
            'gif',
            'tif',
        ];

        $pathinfo = pathinfo($path);

        if (!file_exists($path) || !isset($pathinfo['extension']) || !in_array($pathinfo['extension'], $allowed_extensions)) {
            $file_type = 'image/jpg';
            $path      = FCPATH . 'assets/images/preview-not-available.jpg';
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($path) . '"');
        header('Content-Type: ' . $file_type);
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');

        if (ob_get_contents()) {
            ob_end_clean();
        }

        hooks()->do_action('before_output_preview_image');
        $file = fopen($path, 'rb');
        if ($file !== false) {
            while (!feof($file)) {
                echo fread($file, 1024);
            }
            fclose($file);
        }
    }

    public function file($folder_indicator, $attachmentid = '')
    {
        $this->load->model('tickets_model');
        if ($folder_indicator == 'ticket') {
            if (is_logged_in()) {
                $this->db->where('id', $attachmentid);
                $attachment = $this->db->get(db_prefix() . 'ticket_attachments')->row();
                if (!$attachment) {
                    show_404();
                }
                $ticket   = $this->tickets_model->get_ticket_by_id($attachment->ticketid);
                $ticketid = $attachment->ticketid;
                if ($ticket->userid == get_client_user_id() || is_staff_logged_in()) {
                    if ($attachment->id != $attachmentid) {
                        show_404();
                    }
                    $path = get_upload_path_by_type('ticket') . $ticketid . '/' . $attachment->file_name;
                }
            }
        } elseif ($folder_indicator == 'newsfeed') {
            if (is_staff_logged_in()) {
                if (!$attachmentid) {
                    show_404();
                }
                $this->db->where('id', $attachmentid);
                $attachment = $this->db->get(db_prefix() . 'files')->row();
                if (!$attachment) {
                    show_404();
                }
                $path = get_upload_path_by_type('newsfeed') . $attachment->rel_id . '/' . $attachment->file_name;
            }
        } elseif ($folder_indicator == 'contract') {
            if (!$attachmentid) {
                show_404();
            }

            $this->db->where('attachment_key', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();
            if (!$attachment) {
                show_404();
            }

            if (!is_staff_logged_in()) {
                $this->db->select('not_visible_to_client');
                $this->db->where('id', $attachment->rel_id);
                $contract = $this->db->get(db_prefix() . 'contracts')->row();
                if ($contract->not_visible_to_client == 1) {
                    show_404();
                }
            }

            $path = get_upload_path_by_type('contract') . $attachment->rel_id . '/' . $attachment->file_name;
        } elseif ($folder_indicator == 'taskattachment') {
            if (!is_logged_in()) {
                show_404();
            }

            $this->db->where('attachment_key', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();

            if (!$attachment) {
                show_404();
            }
            $path = get_upload_path_by_type('task') . $attachment->rel_id . '/' . $attachment->file_name;
        } elseif ($folder_indicator == 'sales_attachment') {
            if (!is_staff_logged_in()) {
                $this->db->where('visible_to_customer', 1);
            }

            $this->db->where('attachment_key', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();
            if (!$attachment) {
                show_404();
            }

            $path = get_upload_path_by_type($attachment->rel_type) . $attachment->rel_id . '/' . $attachment->file_name;
        } elseif ($folder_indicator == 'expense') {
            if (!is_staff_logged_in()) {
                show_404();
            }
            $this->db->where('rel_id', $attachmentid);
            $this->db->where('rel_type', 'expense');
            $file = $this->db->get(db_prefix() . 'files')->row();
            $path = get_upload_path_by_type('expense') . $file->rel_id . '/' . $file->file_name;
        // l_attachment_key is if request is coming from public form
        } elseif ($folder_indicator == 'lead_attachment' || $folder_indicator == 'l_attachment_key') {
            if (!is_staff_logged_in() && strpos($_SERVER['HTTP_REFERER'], 'forms/l/') === false) {
                show_404();
            }

            // admin area
            if ($folder_indicator == 'lead_attachment') {
                $this->db->where('id', $attachmentid);
            } else {
                // Lead public form
                $this->db->where('attachment_key', $attachmentid);
            }

            $attachment = $this->db->get(db_prefix() . 'files')->row();

            if (!$attachment) {
                show_404();
            }

            $path = get_upload_path_by_type('lead') . $attachment->rel_id . '/' . $attachment->file_name;
        } elseif ($folder_indicator == 'client') {
            $this->db->where('attachment_key', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();
            if (!$attachment) {
                show_404();
            }
            if (has_permission('customers', '', 'view') || is_customer_admin($attachment->rel_id) || is_client_logged_in()) {
                $path = get_upload_path_by_type('customer') . $attachment->rel_id . '/' . $attachment->file_name;
            }
        }  elseif ($folder_indicator == 'estimate_request_attachment') {
            if (!is_staff_logged_in() && strpos($_SERVER['HTTP_REFERER'], 'forms/l/') === false) {
                show_404();
            }

            // admin area
            if ($folder_indicator == 'estimate_request_attachment') {
                $this->db->where('id', $attachmentid);
            } else {
                // Lead public form
                $this->db->where('attachment_key', $attachmentid);
            }

            $attachment = $this->db->get(db_prefix() . 'files')->row();

            if (!$attachment) {
                show_404();
            }

            $path = get_upload_path_by_type('estimate_request') . $attachment->rel_id . '/' . $attachment->file_name;
        } elseif ($folder_indicator == 'corporate') {
            if (!is_staff_logged_in()) {
                die();
            }
            $this->db->where('id', $attachmentid);
            $this->db->where('rel_type', 'corporate');
            $file = $this->db->get('tblfiles')->row();
            $path = get_upload_path_by_type('corporate') . $file->rel_id . '/' . $file->file_name;
        }elseif ($folder_indicator == 'intellectual_property') {
            if (!$attachmentid) {
                show_404();
            }

            $this->db->where('attachment_key', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();
            if (!$attachment) {
                show_404();
            }

            /*if (!is_staff_logged_in()) {
                $this->db->select('not_visible_to_client');
                $this->db->where('id', $attachment->rel_id);
                $contract = $this->db->get(db_prefix() . 'contracts')->row();
                if ($contract->not_visible_to_client == 1) {
                    show_404();
                }
            }*/

            $path = get_upload_path_by_type('intellectual_property') . $attachment->rel_id . '/' . $attachment->file_name;
        }elseif ($folder_indicator == 'trade_license') {
            if (!$attachmentid) {
                show_404();
            }

            $this->db->where('attachment_key', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();
            if (!$attachment) {
                show_404();
            }

            /*if (!is_staff_logged_in()) {
                $this->db->select('not_visible_to_client');
                $this->db->where('id', $attachment->rel_id);
                $contract = $this->db->get(db_prefix() . 'contracts')->row();
                if ($contract->not_visible_to_client == 1) {
                    show_404();
                }
            }*/

            $path = get_upload_path_by_type('trade_license') . $attachment->rel_id . '/' . $attachment->file_name;
        }elseif ($folder_indicator == 'oppositeparty') {
            if (!is_staff_logged_in()) {
                die();
            }
            $this->db->where('id', $attachmentid);
            $this->db->where('rel_type', 'oppositeparty');
            $file = $this->db->get('tblfiles')->row();
            $path = get_upload_path_by_type('oppositeparty') . $file->rel_id . '/' . $file->file_name;
        }elseif ($folder_indicator == 'document') {
            if (!$attachmentid) {
                show_404();
            }

            $this->db->where('id', $attachmentid);
            $attachment = $this->db->get(db_prefix() . 'files')->row();
            if (!$attachment) {
                show_404();
            }

            if (!is_staff_logged_in()) {
                $this->db->select('not_visible_to_client');
                $this->db->where('id', $attachment->rel_id);
                $contract = $this->db->get(db_prefix() . 'documents')->row();
                if ($contract->not_visible_to_client == 1) {
                    show_404();
                }
            }

            $path = get_upload_path_by_type('document') . $attachment->rel_id . '/' . $attachment->file_name;
        } else {
            die('folder not specified');
        }

        $path = hooks()->apply_filters('download_file_path', $path, [
            'folder'       => $folder_indicator,
            'attachmentid' => $attachmentid,
        ]);

        force_download($path, null);
    }
	public function downloadfile($projectid,$fileid){
		 $this->db->where('id', $fileid);
                $attachment = $this->db->get(db_prefix() . 'project_files')->row();
		 $path = get_upload_path_by_type('project') . $projectid . '/'.$attachment->file_name;
		 force_download($path, null);
	}
	public function downloadcontractfile($contractid,$fileid){
		 $this->db->where('id', $fileid);
                $attachment = $this->db->get(db_prefix() . 'contract_renewals')->row();
		 $path = get_upload_path_by_type('contract') . $contractid . '/'.$attachment->new_filename;
		 force_download($path, null);
	}
	public function downloadagreement($contractid){
		 $this->db->where('id', $contractid);
                $attachment = $this->db->get(db_prefix() . 'contracts')->row();
		 $path = get_upload_path_by_type('contract') . $contractid . '/'.$attachment->contract_filename;
		 force_download($path, null);
	}
	public function downloadconstitution($clientid,$fileid){
		 $this->db->where('id', $fileid);
                $attachment = $this->db->get(db_prefix() . 'client_subfile')->row();
		 $path = get_upload_path_by_type('customer_subfile_images') . $clientid . '/'.$attachment->file_name;
		 force_download($path, null);
	}
    public function downloadagreementversion($contractid,$contract_vid){
         $this->db->where('id', $contract_vid);
                $attachment = $this->db->get(db_prefix() . 'contract_versions')->row();
         $path = get_upload_path_by_type('contract') . $contractid . '/'.$attachment->version_internal_file_path;
         force_download($path, null);
    }
    public function downloadjudgement($projectid,$fileid){
        $this->db->where('id', $fileid);
               $attachment = $this->db->get(db_prefix() . 'project_judgement')->row();
        $path = get_upload_path_by_type('project') . $projectid . '/'.$attachment->judge_attachment;
        force_download($path, null);
   }
   public function downloadsigned_agreement($contractid){
    $this->db->where('id', $contractid);
           $attachment = $this->db->get(db_prefix() . 'contracts')->row();
    $path = get_upload_path_by_type('contract') . $contractid . '/'.$attachment->signed_contract_filename;
    force_download($path, null);
}
public function downloadlogofile($projectid){
		 $this->db->where('id', $projectid);
                $attachment = $this->db->get(db_prefix() . 'projects')->row();
		 $path = get_upload_path_by_type('project') . $projectid . '/'.$attachment->ip_logo;
		 force_download($path, null);
	}
	 public function downloadagreementamendment($contractid,$contract_amendid){
         $this->db->where('id', $contract_amendid);
                $attachment = $this->db->get(db_prefix() . 'contract_amendments')->row();
         $path = get_upload_path_by_type('contract') . $contractid . '/'.$attachment->amendement_file;
         force_download($path, null);
    }
    
    public function downloadagreementpostaction($contractid,$contract_actionid){
         $this->db->where('id', $contract_actionid);
                $attachment = $this->db->get(db_prefix() . 'contract_actions')->row();
         $path = get_upload_path_by_type('contract') . $contractid . '/'.$attachment->post_attachment;
         force_download($path, null);
    }
}
