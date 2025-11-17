<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Project_installment_paid_to_staff extends App_mail_template
{
    protected $for = 'staff';

    protected $staff_email;

    protected $staffid;

    protected $project_id;

    public $slug = 'installment-paid-notification';

    public $rel_type = 'installment';

    public function __construct($staff_email, $staffid, $project_id)
    {
        parent::__construct();

        $this->staff_email = $staff_email;
        $this->staffid     = $staffid;
        $this->project_id     = $project_id;
    }

    public function build()
    {
        $this->to($this->staff_email)
        ->set_rel_id($this->project_id)
        ->set_staff_id($this->staffid)
        ->set_merge_fields('staff_merge_fields', $this->staffid)
        ->set_merge_fields('projects_merge_fields', $this->project_id);
    }
}
