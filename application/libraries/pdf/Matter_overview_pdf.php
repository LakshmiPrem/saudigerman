<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(__DIR__ . '/App_pdf.php');

class Matter_overview_pdf extends App_pdf
{
    protected $project_id;

    public function __construct($project_id)
    {
        parent::__construct();

        $this->project_id = $project_id;
		$this->SetFont('aealarabiya', '', 10);
    }
 
    public function prepare()
    {
        $project = $this->ci->projects_model->get($this->project_id);
        //$this->ci->load->model('hearing_model');
        $hearings = $this->ci->hearing_model->get_hearings_by_project_id($this->project_id);
        $this->SetTitle($project->name);
        $members                = $this->ci->projects_model->get_project_members($project->id);
        $project->currency_data = $this->ci->projects_model->get_currency($project->id);
		 $data['asslawyers']= get_all_assignees_byproject($project->id);
		 $data['legals']=get_all_legal_byproject($project->id);
		  $data['agent_stakeholders']=get_all_assignees('project',$this->project_id);
		    $data['case_updates'] = $this->ci->casediary_model->get_case_updates($project->id,'project');
		   $data['court_order']       = $this->ci->casediary_model->get_courtorders($project->id,'1');
				$data['court_instances'] = $this->ci->casediary_model->get_project_instances_by_project_id($project->id);
		  $this->ci->load->model('expenses_model');
       // $data['expenses']=  $this->ci->expenses_model->get_expenses_cat_total($project->id);
        // Add <br /> tag and wrap over div element every image to prevent overlaping over text
        $project->description = preg_replace('/(<img[^>]+>(?:<\/img>)?)/i', '<br><br><div>$1</div><br><br>', $project->description);
	  $this->ci->load->model('contracts_model');
		$data['project_contracts']=$this->ci->contracts_model->get('',['project_id'=>$this->project_id]);
		$data['project_subfiles']=$this->ci->projects_model->get_subversionfiles($this->project_id);
		
		 $this->ci->load->model('intellectual_property_model');
	//	$data['project_trademarks']=$this->ci->intellectual_property_model->get_trademarkbyproject($this->project_id, [], true);
        $data['project']    = $project;
        $data['hearings']   = $hearings;
        $data['milestones'] = $this->ci->projects_model->get_milestones($project->id);
        $data['timesheets'] = $this->ci->projects_model->get_timesheets($project->id);
		$data['lastcourt_instances'] = $this->ci->casediary_model->get_project_instances_by_project_id($this->project_id,[],1);
        $data['tasks']             = $this->ci->projects_model->get_tasks($project->id, [], false);
        $data['total_logged_time'] = seconds_to_time_format($this->ci->projects_model->total_logged_time($project->id));
        if ($project->deadline) {
            $data['total_days'] = round((human_to_unix($project->deadline . ' 00:00') - human_to_unix($project->start_date . ' 00:00')) / 3600 / 24);
        } else {
            $data['total_days'] = '/';
        }
        $data['total_members'] = count($members);
        $data['total_tickets'] = total_rows(db_prefix().'tickets', [
                'project_id' => $project->id,
            ]);
        $data['total_invoices'] = total_rows(db_prefix().'invoices', [
                'project_id' => $project->id,
            ]);

        $this->ci->load->model('invoices_model');

        $data['invoices_total_data'] = $this->ci->invoices_model->get_invoices_total([
                'currency'   => $project->currency_data->id,
                'project_id' => $project->id,
            ]);

        $data['total_milestones']     = count($data['milestones']);
        $data['total_files_attached'] = total_rows(db_prefix().'project_files', [
                'project_id' => $project->id,
            ]);
        $data['total_discussion'] = total_rows(db_prefix().'projectdiscussions', [
                'project_id' => $project->id,
            ]);
        $data['members'] = $members;

        $this->set_view_vars($data);

        return $this->build();
    }

    protected function type()
    {
        return 'project-data';
    }

    protected function file_path()
    {
        $customPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/my_matteroverview_data_pdf.php';
        $actualPath = APPPATH . 'views/themes/' . active_clients_theme() . '/views/matteroverview_data_pdf.php';

        if (file_exists($customPath)) {
            $actualPath = $customPath;
        }

        return $actualPath;
    }

 }
