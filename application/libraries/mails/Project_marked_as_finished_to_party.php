<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Project_marked_as_finished_to_party extends App_mail_template
{
    protected $for = 'staff';

    protected $project;

    protected $party;

    protected $additional_data;

    public $slug = 'project-marked-as-finished-to-party';

    public $rel_type = 'project';

    public function __construct($project,$partymail,$cc='')
    {
        parent::__construct();
        $this->project         = $project;
       $this->party           = $partymail;
		$this->cc			  = $cc;
    }

    public function build()
    {
        $this->to($this->party)
        ->set_rel_id($this->project->id)
		->cc($this->cc)
        ->set_merge_fields('client_merge_fields', $this->project->clientid)
      //  ->set_merge_fields('staff_merge_fields', $this->staff['staff_id'])
        ->set_merge_fields('projects_merge_fields', $this->project->id);
    }
}
