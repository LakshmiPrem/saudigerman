<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Team_meetinglink_to_client extends App_mail_template
{
    protected $for = 'customer';

    protected $email;

    protected $details;

  

    public $slug = 'new-team-meeting';

    public $rel_type = 'client';

    public function __construct($email, $details)
    {
        parent::__construct();
        $this->email         = $email;
        $this->details       = $details;
    }

    public function build()
    {
        $this->to($this->email)
        ->set_merge_fields('teams_merge_fields',$this->details);
        
    }
}
