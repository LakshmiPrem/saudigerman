<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Teams_merge_fields extends App_merge_fields
{
    public function build()
    {
        return [
                [
                    'name'      => 'Status',
                    'key'       => '{status}',
                    'available' => [
                        'airticket',
                    ],
                ],

                [
                    'name'      => 'Link',
                    'key'       => '{link}',
                    'available' => [
                        'airticket',
                    ],
                ],
                
                
            ];
    }

    /**
 * Merge fields for tickets
 * @param  string $staff  
 * @param  mixed $user_id 
 * @param  mixed $status  
 * @return array
 */
    public function format($insert)
    {
        $fields = [];
        

        $fields['{client}']   = '';
        $fields['{link}']    = '';
        $fields['{start}']   = '';
        $fields['{end}']    = '';
        $fields['{meetingid}']    = '';


        if (!$insert) {
            return $fields;
        }


        $fields['{client}']   = get_company_name($insert['clientid']);
        $fields['{link}']    = $insert['meeting_url'];
        $fields['{start}']   = $insert['start_date'];
        $fields['{end}']    = $insert['end_date'];
        $fields['{subject}']    = $insert['subject'];
        $fields['{meetingid}']    = $insert['meeting_id'];

        return hooks()->apply_filters('teams_merge_fields', $fields, []);

    }
}
