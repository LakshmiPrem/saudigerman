<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Get notice short_url
 * @since  Version 2.7.3
 * @param  object $notice
 * @return string Url
 */
function get_notice_shortlink($notice)
{
    $long_url = site_url("notice/{$notice->id}/{$notice->hash}");
    if (!get_option('bitly_access_token')) {
        return $long_url;
    }

    // Check if notice has short link, if yes return short link
    if (!empty($notice->short_link)) {
        return $notice->short_link;
    }

    // Create short link and return the newly created short link
    $short_link = app_generate_short_link([
        'long_url'  => $long_url,
        'title'     => 'notice #'. $notice->id
    ]);

    if ($short_link) {
        $CI = &get_instance();
        $CI->db->where('id', $notice->id);
        $CI->db->update(db_prefix() . 'notices', [
            'short_link' => $short_link
        ]);
        return $short_link;
    }
    return $long_url;
}

/**
 * Check the notice view restrictions
 *
 * @param  int $id
 * @param  string $hash
 *
 * @return void
 */
function check_notice_restrictions($id, $hash)
{
    $CI = &get_instance();
    $CI->load->model('notices_model');

    if (!$hash || !$id) {
        show_404();
    }

    if (!is_client_logged_in() && !is_staff_logged_in()) {
        if (get_option('view_notice_only_logged_in') == 1) {
            redirect_after_login_to_current_url();
            redirect(site_url('authentication/login'));
        }
    }

    $notice = $CI->notices_model->get($id);

    if (!$notice || ($notice->hash != $hash)) {
        show_404();
    }

    // Do one more check
    if (!is_staff_logged_in()) {
        if (get_option('view_notice_only_logged_in') == 1) {
            if ($notice->client != get_client_user_id()) {
                show_404();
            }
        }
    }
}

/**
 * Function that will search possible notices templates in applicaion/views/admin/notices/templates
 * Will return any found files and user will be able to add new template
 *
 * @return array
 */
function get_notice_templates()
{
    $notice_templates = [];
    if (is_dir(VIEWPATH . 'admin/notices/templates')) {
        foreach (list_files(VIEWPATH . 'admin/notices/templates') as $template) {
            $notice_templates[] = $template;
        }
    }

    return $notice_templates;
}

/**
 * Send notice signed notification to staff members
 *
 * @param  int $notice_id
 *
 * @return void
 */
function send_notice_signed_notification_to_staff($notice_id)
{
    $CI = &get_instance();
    $CI->db->where('id', $notice_id);
    $notice = $CI->db->get(db_prefix() . 'notices')->row();

    if (!$notice) {
        return false;
    }

    // Get creator
    $CI->db->select('staffid, email');
    $CI->db->where('staffid', $notice->addedfrom);
    $staff_notice = $CI->db->get(db_prefix() . 'staff')->result_array();

    $notifiedUsers = [];

    foreach ($staff_notice as $member) {
        $notified = add_notification([
            'description'     => 'not_notice_signed',
            'touserid'        => $member['staffid'],
            'fromcompany'     => 1,
            'fromuserid'      => 0,
            'link'            => 'notices/notice/' . $notice->id,
            'additional_data' => serialize([
                '<b>' . $notice->subject . '</b>',
            ]),
        ]);

        if ($notified) {
            array_push($notifiedUsers, $member['staffid']);
        }

        send_mail_template('notice_signed_to_staff', $notice, $member);
    }

    pusher_trigger_notification($notifiedUsers);
}

/**
 * Get the recently created notices in the given days
 *
 * @param  integer $days
 * @param  integer|null $staffId
 *
 * @return integer
 */
function count_recently_created_notices($days = 7, $staffId = null)
{
    $diff1     = date('Y-m-d', strtotime('-' . $days . ' days'));
    $diff2     = date('Y-m-d', strtotime('+' . $days . ' days'));
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;
    $where_own = [];

    if (!staff_can('view', 'notices')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'notices', 'dateadded BETWEEN "' . $diff1 . '" AND "' . $diff2 . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : ''));
}

/**
 * Get total number of active notices
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_active_notices($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('notices', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'notices', '(DATE(dateend) >"' . date('Y-m-d') . '" AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ') OR (DATE(dateend) IS NULL AND trash=0' . (count($where_own) > 0 ? ' AND addedfrom=' . $staffId : '') . ')');
}

/**
 * Get total number of expired notices
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_expired_notices($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('notices', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'notices', array_merge(['DATE(dateend) <' => date('Y-m-d'), 'trash' => 0], $where_own));
}

/**
 * Get total number of trash notices
 *
 * @param integer|null $staffId
 *
 * @return integer
 */
function count_trash_notices($staffId = null)
{
    $where_own = [];
    $staffId   = is_null($staffId) ? get_staff_user_id() : $staffId;

    if (!has_permission('notices', '', 'view')) {
        $where_own = ['addedfrom' => $staffId];
    }

    return total_rows(db_prefix() . 'notices', array_merge(['trash' => 1], $where_own));
}
