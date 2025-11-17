<?php

defined('BASEPATH') or exit('No direct script access allowed');

include_once(APPPATH . 'libraries/mails/traits/TicketTemplate.php');

class Ticket_status_rejected_to_staff extends App_mail_template
{
    use TicketTemplate;

    protected $for = 'staff';

    protected $staff_email;

    protected $staffid;

    protected $ticketid;

    protected $client_id;

    protected $contact_id;

    public $slug = 'ticket-status-rejected-to-staff';

    public $rel_type = 'ticket';

    public function __construct($staff_email, $staffid, $ticketid)
    {
        parent::__construct();

        $this->staff_email = $staff_email;
        $this->staffid     = $staffid;
        $this->ticketid    = $ticketid;
      
    }

    public function build()
    {

        $this->to($this->staff_email)
        ->set_rel_id($this->ticketid)
        ->set_staff_id($this->staffid)
        ->set_merge_fields('ticket_merge_fields', $this->slug, $this->ticketid);
    }
}
