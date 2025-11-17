<?php
defined('BASEPATH') or exit('No direct script access allowed');
class Casetemplate_model extends App_Model
{
    private $project_settings;

    public function __construct()
    {
        parent::__construct();

        $project_settings       = array(
            'available_features',
            'view_tasks',
            'create_tasks',
            'edit_tasks',
            'comment_on_tasks',
            'view_task_comments',
            'view_task_attachments',
            'view_task_checklist_items',
            'upload_on_tasks',
            'view_task_total_logged_time',
            'view_finance_overview',
            'upload_files',
            'open_discussions',
            'view_milestones',
            'view_gantt',
            'view_timesheets',
            'view_activity_log',
            'view_team_members',
            'hide_tasks_on_main_tasks_table',
        );
      
        $this->project_settings = hooks()->apply_filters('project_settings', $project_settings);
    }

    public function get_project_statuses()
    {
        $statuses = hooks()->apply_filters('before_get_project_statuses', [
            [
                'id'             => 1,
                'color'          => '#989898',
                'name'           => _l('project_status_1'),
                'order'          => 1,
                'filter_default' => true,
            ],
            [
                'id'             => 2,
                'color'          => '#03a9f4',
                'name'           => _l('project_status_2'),
                'order'          => 2,
                'filter_default' => true,
            ],
            [
                'id'             => 3,
                'color'          => '#ff6f00',
                'name'           => _l('project_status_3'),
                'order'          => 3,
                'filter_default' => true,
            ],
            [
                'id'             => 4,
                'color'          => '#84c529',
                'name'           => _l('project_status_4'),
                'order'          => 100,
                'filter_default' => false,
            ],
            [
                'id'             => 5,
                'color'          => '#989898',
                'name'           => _l('project_status_5'),
                'order'          => 4,
                'filter_default' => false,
            ],
        ]);

        usort($statuses, function ($a, $b) {
            return $a['order'] - $b['order'];
        });

        return $statuses;
    }

    public function get_distinct_tasks_timesheets_staff($project_id)
    {
        return $this->db->query('SELECT DISTINCT staff_id FROM tbltaskstimers LEFT JOIN tbltasks ON tbltasks.id = tbltaskstimers.task_id WHERE rel_type="casediary" AND rel_id=' . $project_id)->result_array();
    }

    public function get_most_used_billing_type()
    {
        return $this->db->query("SELECT billing_type, COUNT(*) AS total_usage
                FROM tblcasetemplates
                GROUP BY billing_type
                ORDER BY total_usage DESC
                LIMIT 1")->row();
    }

    public function timers_started_for_project($project_id, $where = array(), $task_timers_where = array())
    {
        $this->db->where($where);
        $this->db->where('end_time IS NULL');
        $this->db->where('tbltasks.rel_id', $project_id);
        $this->db->where('tbltasks.rel_type', 'casediary');
        $this->db->join('tbltasks', 'tbltasks.id=tbltaskstimers.task_id');
        $total = $this->db->count_all_results('tbltaskstimers');

        return $total > 0 ? true : false;
    }

    public function pin_action($id)
    {
        if (total_rows('tblpinnedcaseprojects', array(
            'staff_id' => get_staff_user_id(),
            'project_id' => $id,
        )) == 0) {
            $this->db->insert('tblpinnedcaseprojects', array(
                'staff_id' => get_staff_user_id(),
                'project_id' => $id,
            ));

            return true;
        } else {
            $this->db->where('project_id', $id);
            $this->db->where('staff_id', get_staff_user_id());
            $this->db->delete('tblpinnedcaseprojects');

            return true;
        }
    }

    public function get_currency($id)
    {
        $this->load->model('currencies_model');
        $customer_currency = $this->clients_model->get_customer_default_currency(get_client_id_by_project_id($id));
        if ($customer_currency != 0) {
            $currency = $this->currencies_model->get($customer_currency);
        } else {
            $currency = $this->currencies_model->get_base_currency();
        }

        return $currency;
    }

    public function calc_progress($id)
    {
        $this->db->select('progress_from_tasks,progress,status');
        $this->db->where('id', $id);
        $project =  $this->db->get('tblcasetemplates')->row();

        if ($project->status == 4) {
            return 100;
        }

        if ($project->progress_from_tasks == 1) {
            return $this->calc_progress_by_tasks($id);
        } else {
            return $project->progress;
        }
    }

    public function calc_progress_by_tasks($id)
    {
        $total_project_tasks  = total_rows('tbltasks', array(
            'rel_type' => 'case',
            'rel_id' => $id,
        ));
        $total_finished_tasks = total_rows('tbltasks', array(
            'rel_type' => 'casediary',
            'rel_id' => $id,
            'status' => 5,
        ));
        $percent              = 0;
        if ($total_finished_tasks >= floatval($total_project_tasks)) {
            $percent = 100;
        } else {
            if ($total_project_tasks !== 0) {
                $percent = number_format(($total_finished_tasks * 100) / $total_project_tasks, 2);
            }
        }

        return $percent;
    }

    public function get_last_project_settings()
    {
        $this->db->select('id');
        $this->db->order_by('id', 'DESC');
        $this->db->limit(1);
        $last_project = $this->db->get('tblcasetemplates')->row();
        if ($last_project) {
            return $this->get_project_settings($last_project->id);
        }

        return array();
    }

    public function get_settings()
    {
        return $this->project_settings;
    }

    public function get($id = '', $where = array())
    {
        $this->db->where($where);
        if (is_numeric($id)) {
            $this->db->where('id', $id);
            $project = $this->db->get('tblcasetemplates')->row();
            if ($project) {
                $project->shared_vault_entries = $this->clients_model->get_vault_entries($project->clientid, array('share_in_projects'=>1));
                $settings          = $this->get_project_settings($id);

                // SYNC NEW TABS
                $tabs = get_templates_tabs_admin(null);//get_relative_tab($project->case_type);
                $tabs_flatten = array();
                $settings_available_features = array();

                $available_features_index = false;
                /*foreach ($settings as $key => $setting) {
                    if ($setting['name'] == 'available_features') {
                        $available_features_index = $key;
                        $available_features = unserialize($setting['value']);
                        if (is_array($available_features)) {
                            foreach ($available_features as $name => $avf) {
                                $settings_available_features[] = $name;
                            }
                        }
                    }
                }
                foreach ($tabs as $tab) {
                    if (isset($tab['dropdown'])) {
                        foreach ($tab['dropdown'] as $d) {
                            $tabs_flatten[] = $d['name'];
                        }
                    } else {
                        $tabs_flatten[] = $tab['name'];
                    }
                }
                if (count($settings_available_features) != $tabs_flatten) {
                    foreach ($tabs_flatten as $tab) {
                        if (!in_array($tab, $settings_available_features)) {
                            if ($available_features_index) {
                                $current_available_features_settings = $settings[$available_features_index];
                                $tmp = unserialize($current_available_features_settings['value']);
                                $tmp[$tab] = 1;
                                $this->db->where('id', $current_available_features_settings['id']);
                                $this->db->update('tblcasetemplatesettings', array('value'=>serialize($tmp)));
                            }
                        }
                    }
                }
                $project->settings = new StdClass();
                foreach ($settings as $setting) {
                    $project->settings->{$setting['name']} = $setting['value'];
                }*/

                // In case any settings missing add them and set default 0 to prevent errors
                /*foreach ($this->project_settings as $setting) {
                    if (!isset($project->settings->{$setting})) {
                        $this->db->insert('tblcasetemplatesettings', array(
                            'project_id' => $id,
                            'name' => $setting,
                            'value' => 0,
                        ));
                        $project->settings->{$setting} = 0;
                    }
                }*/
                $project->client_data = new StdClass();
                //$project->client_data = $this->clients_model->get($project->clientid);

                return $project;
            }

            return null;
        }

        $this->db->select('*');
        //$this->db->join('tblclients', 'tblclients.userid=tblcasetemplates.clientid');
        $this->db->order_by('id', 'desc');

        return $this->db->get('tblcasetemplates')->result_array();
    }

    public function calculate_total_by_project_hourly_rate($seconds, $hourly_rate)
    {
        $hours       = seconds_to_time_format($seconds);
        $decimal     = sec2qty($seconds);
        $total_money = 0;
        $total_money += ($decimal * $hourly_rate);

        return array(
            'hours' => $hours,
            'total_money' => $total_money,
        );
    }

    public function calculate_total_by_task_hourly_rate($tasks)
    {
        $total_money    = 0;
        $_total_seconds = 0;

        foreach ($tasks as $task) {
            $seconds = $task['total_logged_time'];
            $_total_seconds += $seconds;
            $total_money += sec2qty($seconds) * $task['hourly_rate'];
        }

        return array(
            'total_money' => $total_money,
            'total_seconds' => $_total_seconds,
        );
    }

    public function get_tasks($id, $where = array(), $apply_restrictions = false, $count = false)
    {
        $has_permission = has_permission('tasks', '', 'view');
        $show_all_tasks_for_project_member = get_option('show_all_tasks_for_project_member');

        if (is_client_logged_in()) {
            $this->db->where('visible_to_client', 1);
        }

        $select = implode(', ', prefixed_table_fields_array('tbltasks')).',tblmilestones.name as milestone_name,
        (SELECT SUM(CASE
            WHEN end_time is NULL THEN '.time().'-start_time
            ELSE end_time-start_time
            END) FROM tbltaskstimers WHERE task_id=tbltasks.id) as total_logged_time,
           '.get_sql_select_task_assignees_ids().' as assignees_ids
        ';

        if(!is_client_logged_in() && is_staff_logged_in()) {
            $select .= ',(SELECT staffid FROM tblstafftaskassignees WHERE taskid=tbltasks.id AND staffid='.get_staff_user_id().') as current_user_is_assigned';
        }
        $this->db->select($select);

        $this->db->join('tblmilestones', 'tblmilestones.id = tbltasks.milestone', 'left');
        $this->db->where('rel_id', $id);
        $this->db->where('rel_type', 'casetemplates');
        if ($apply_restrictions == true) {
            if (!is_client_logged_in() && !$has_permission && $show_all_tasks_for_project_member == 0) {
                $this->db->where('(
                    tbltasks.id IN (SELECT taskid FROM tblstafftaskassignees WHERE staffid='.get_staff_user_id().')
                    OR tbltasks.id IN(SELECT taskid FROM tbltasksfollowers WHERE staffid='.get_staff_user_id().')
                    OR is_public = 1
                    OR (addedfrom ='.get_staff_user_id().' AND is_added_from_contact = 0)
                    )');
            }
        }
        $this->db->order_by('milestone_order', 'asc');
        $this->db->where($where);

        if ($count == false) {
            $tasks = $this->db->get('tbltasks')->result_array();
        } else {
            $tasks = $this->db->count_all_results('tbltasks');
        }

        return $tasks;
    }

    public function do_milestones_kanban_query($milestone_id, $project_id, $page = 1, $where = array(), $count = false)
    {
        $where['milestone'] = $milestone_id;

        if ($count == false) {
            if ($page > 1) {
                $page--;
                $position = ($page * get_option('tasks_kanban_limit'));
                $this->db->limit(get_option('tasks_kanban_limit'), $position);
            } else {
                $this->db->limit(get_option('tasks_kanban_limit'));
            }
        }

        return $this->get_tasks($project_id, $where, true, $count);
    }
    
    
    public function get_files($project_id)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('project_id', $project_id);

        return $this->db->get('tblcaseprojectfiles')->result_array();
    }

    public function get_file($id, $project_id = false)
    {
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        $this->db->where('id', $id);
        $file = $this->db->get('tblcaseprojectfiles')->row();

        if ($file && $project_id) {
            if ($file->project_id != $project_id) {
                return false;
            }
        }

        return $file;
    }

    

    public function total_logged_time($id)
    {
        $q = $this->db->query('
            SELECT SUM(CASE
                WHEN end_time is NULL THEN '.time().'-start_time
                ELSE end_time-start_time
                END) as total_logged_time
            FROM tbltaskstimers
            WHERE task_id IN (SELECT id FROM tbltasks WHERE rel_type="casediary" AND rel_id='.$id.')')
        ->row();

        return $q->total_logged_time;
    }

   

    public function add($data)
    {
        if (isset($data['notify_project_members_status_change'])) {
            unset($data['notify_project_members_status_change']);
        }
        $send_created_email = false;
        if (isset($data['send_created_email'])) {
            unset($data['send_created_email']);
            $send_created_email = true;
        }

        $data['billing_type'] = 1;
        $send_project_marked_as_finished_email_to_contacts = false;
        if (isset($data['project_marked_as_finished_email_to_contacts'])) {
            unset($data['project_marked_as_finished_email_to_contacts']);
            $send_project_marked_as_finished_email_to_contacts = true;
        }

        if (isset($data['settings'])) {
            $project_settings = $data['settings'];
            unset($data['settings']);
        }
        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }
        if (isset($data['progress_from_tasks'])) {
            $data['progress_from_tasks'] = 1;
        } else {
            $data['progress_from_tasks'] = 0;
        }



        $data['start_date'] = to_sql_date($data['start_date']);

        if (!empty($data['deadline'])) {
            $data['deadline'] = to_sql_date($data['deadline']);
        } else {
            unset($data['deadline']);
        }

        $data['project_created'] = date('Y-m-d');
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }
        if ($data['billing_type'] == 1) {
            $data['project_rate_per_hour'] = 0;
        } elseif ($data['billing_type'] == 2) {
            $data['project_cost'] = 0;
        } else {
            $data['project_rate_per_hour'] = 0;
            $data['project_cost']          = 0;
        }

        $data['addedfrom'] = get_staff_user_id();

        $tags = '';
        if (isset($data['tags'])) {
            $tags  = $data['tags'];
            unset($data['tags']);
        }

        $this->db->insert('tblcasetemplates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {

            handle_tags_save($tags, $insert_id, 'casetemplates');

            if (isset($project_members)) {
                $_pm['project_members'] = $project_members;
                $this->add_edit_members($_pm, $insert_id);
            }

            $this->log_activity($insert_id, 'matter_activity_created');

            //do_action('after_add_project', $insert_id);
            logActivity('New Matter Template Created [ID: ' . $insert_id . ']');

            return $insert_id;
        }

        return false;
    }

    public function update($data, $id)
    {
        $this->db->select('status');
        $this->db->where('id', $id);
        $old_status = $this->db->get('tblcasetemplates')->row()->status;

        $send_created_email = false;
        if (isset($data['send_created_email'])) {
            unset($data['send_created_email']);
            $send_created_email = true;
        }

        $send_project_marked_as_finished_email_to_contacts = false;
        if (isset($data['project_marked_as_finished_email_to_contacts'])) {
            unset($data['project_marked_as_finished_email_to_contacts']);
            $send_project_marked_as_finished_email_to_contacts = true;
        }
        if(!empty($data['session_date'])){
            $data['session_date'] = to_sql_date($data['session_date']);
        }
        $original_project = $this->get($id);

        if (isset($data['notify_project_members_status_change'])) {
            $notify_project_members_status_change = true;
            unset($data['notify_project_members_status_change']);
        }
        $affectedRows = 0;
        if (!isset($data['settings'])) {
            $this->db->where('project_id', $id);
            $this->db->update('tblcasetemplatesettings', array(
                'value' => 0,
            ));
            if ($this->db->affected_rows() > 0) {
                $affectedRows++;
            }
        } else {
            $_settings = array();
            $_values = array();

            foreach ($data['settings'] as $name => $val) {
                array_push($_settings, $name);
                $_values[$name] = $val;
            }

            unset($data['settings']);
            $original_settings = $this->get_project_settings($id);

            foreach ($original_settings as $setting) {
                if ($setting['name'] != 'available_features') {
                    if (in_array($setting['name'], $_settings)) {
                        $value_setting = 1;
                    } else {
                        $value_setting = 0;
                    }
                } else {

                    //$tabs = get_project_tabs_admin(null);
                    $tabs = get_templates_tabs_admin(null);
                    $tab_settings = array();
                    foreach ($_values[$setting['name']] as $tab) {
                        $tab_settings[$tab] = 1;
                    }
                    foreach ($tabs as $tab) {
                        if (!isset($tab['dropdown'])) {
                            if (!in_array($tab['name'], $_values[$setting['name']])) {
                                $tab_settings[$tab['name']] = 0;
                            }
                        } else {
                            foreach ($tab['dropdown'] as $tab_dropdown) {
                                if (!in_array($tab_dropdown['name'], $_values[$setting['name']])) {
                                    $tab_settings[$tab_dropdown['name']] = 0;
                                }
                            }
                        }
                    }
                    $value_setting = serialize($tab_settings);
                }


                $this->db->where('project_id', $id);
                $this->db->where('name', $setting['name']);
                $this->db->update('tblcasetemplatesettings', array(
                    'value' => $value_setting,
                ));
                if ($this->db->affected_rows() > 0) {
                    $affectedRows++;
                }
            }
        }

        if ($old_status == 4 && $data['status'] != 4) {
            $data['date_finished'] = null;
        } elseif (isset($data['date_finished'])) {
            $data['date_finished'] = to_sql_date($data['date_finished'], true);
        }

        if (isset($data['progress_from_tasks'])) {
            $data['progress_from_tasks'] = 1;
        } else {
            $data['progress_from_tasks'] = 0;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if (!empty($data['deadline'])) {
            $data['deadline'] = to_sql_date($data['deadline']);
        } else {
            $data['deadline'] = null;
        }
        $data['billing_type'] = 1;
        $data['start_date'] = to_sql_date($data['start_date']);
        if ($data['billing_type'] == 1) {
            $data['project_rate_per_hour'] = 0;
        } elseif ($data['billing_type'] == 2) {
            $data['project_cost'] = 0;
        } else {
            $data['project_rate_per_hour'] = 0;
            $data['project_cost']          = 0;
        }
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
            unset($data['project_members']);
        }
        $_pm = array();
        if (isset($project_members)) {
            $_pm['project_members'] = $project_members;
        }
        if ($this->add_edit_members($_pm, $id)) {
            $affectedRows++;
        }
        if (isset($data['mark_all_tasks_as_completed'])) {
            $mark_all_tasks_as_completed = true;
            unset($data['mark_all_tasks_as_completed']);
        }

        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'casediary')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }


        $_data['data'] = $data;
        $_data['id']   = $id;

        $this->db->where('id', $id);
        $this->db->update('tblcasetemplates', $data);

        if ($this->db->affected_rows() > 0) {
            if (isset($mark_all_tasks_as_completed)) {
                $this->_mark_all_project_tasks_as_completed($id);
            }
            $affectedRows++;
        }

        if ($affectedRows > 0) {
            $this->log_activity($id, 'project_activity_updated');
            log_activity('Matter Template  Updated [ID: ' . $id . ']');

            if ($original_project->status != $data['status']) {
                /*do_action('project_status_changed', array(
                    'status' => $data['status'],
                    'project_id' => $id,
                ));*/
                // Give space this log to be on top
                sleep(1);
                if ($data['status'] == 4) {
                    $this->log_activity($id, 'project_marked_as_finished');
                    $this->db->where('id', $id);
                    $this->db->update('tblcasetemplates', array('date_finished'=>date('Y-m-d H:i:s')));
                } else {
                    $this->log_activity($id, 'project_status_updated', '<b><lang>project_status_' . $data['status'] . '</lang></b>');
                }

            }

            return true;
        }

        return false;
    }

    private function _mark_all_project_tasks_as_completed($id)
    {
        $this->db->where('rel_type', 'casetemplates');
        $this->db->where('rel_id', $id);
        $this->db->update('tbltasks', array(
            'status' => 5,
            'datefinished' => date('Y-m-d H:i:s'),
        ));
        $tasks = $this->get_tasks($id);
        foreach ($tasks as $task) {
            $this->db->where('task_id', $task['id']);
            $this->db->where('end_time IS NULL');
            $this->db->update('tbltaskstimers', array(
                'end_time' => time(),
            ));
        }
        $this->log_activity($id, 'project_activity_marked_all_tasks_as_complete');
    }

    public function add_edit_members($data, $id)
    {
        $affectedRows = 0;
        if (isset($data['project_members'])) {
            $project_members = $data['project_members'];
        }

        $new_project_members_to_receive_email = array();
        $this->db->select('name,clientid');
        $this->db->where('id', $id);
        $project = $this->db->get('tblcasetemplates')->row();
        $project_name = $project->name;
        $client_id = $project->clientid;

        $project_members_in = $this->get_project_members($id);
        if (sizeof($project_members_in) > 0) {
            foreach ($project_members_in as $project_member) {
                if (isset($project_members)) {
                    if (!in_array($project_member['staff_id'], $project_members)) {
                        $this->db->where('project_id', $id);
                        $this->db->where('staff_id', $project_member['staff_id']);
                        $this->db->delete('tblcasetemplatemembers');
                        if ($this->db->affected_rows() > 0) {
                           /* $this->db->where('staff_id', $project_member['staff_id']);
                            $this->db->where('project_id', $id);
                            $this->db->delete('tblpinnedcaseprojects');*/

                            $this->log_activity($id, 'project_activity_removed_team_member', get_staff_full_name($project_member['staff_id']));
                            $affectedRows++;
                        }
                    }
                } else {
                    $this->db->where('project_id', $id);
                    $this->db->delete('tblcasetemplatemembers');
                    if ($this->db->affected_rows() > 0) {
                        $affectedRows++;
                    }
                }
            }
            if (isset($project_members)) {
                $notifiedUsers = array();
                foreach ($project_members as $staff_id) {
                    $this->db->where('project_id', $id);
                    $this->db->where('staff_id', $staff_id);
                    $_exists = $this->db->get('tblcasetemplatemembers')->row();
                    if (!$_exists) {
                        if (empty($staff_id)) {
                            continue;
                        }
                        $this->db->insert('tblcasetemplatemembers', array(
                            'project_id' => $id,
                            'staff_id' => $staff_id,
                        ));
                        if ($this->db->affected_rows() > 0) {
                            if ($staff_id != get_staff_user_id()) {
                                $notified = add_notification(array(
                                    'fromuserid' => get_staff_user_id(),
                                    'description' => 'not_staff_added_as_matter_member',
                                    'link' => 'casediary/view/' . $id,
                                    'touserid' => $staff_id,
                                    'additional_data' => serialize(array(
                                        $project_name,
                                    )),
                                ));
                                array_push($new_project_members_to_receive_email, $staff_id);
                                if ($notified) {
                                    array_push($notifiedUsers, $staff_id);
                                }
                            }


                            $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                            $affectedRows++;
                        }
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        } else {
            if (isset($project_members)) {
                $notifiedUsers = array();
                foreach ($project_members as $staff_id) {
                    if (empty($staff_id)) {
                        continue;
                    }
                    $this->db->insert('tblcasetemplatemembers', array(
                        'project_id' => $id,
                        'staff_id' => $staff_id,
                    ));
                    if ($this->db->affected_rows() > 0) {
                        if ($staff_id != get_staff_user_id()) {
                            $notified = add_notification(array(
                                'fromuserid' => get_staff_user_id(),
                                'description' => 'not_staff_added_as_project_member',
                                'link' => 'casediary/view/' . $id,
                                'touserid' => $staff_id,
                                'additional_data' => serialize(array(
                                    $project_name,
                                )),
                            ));
                            array_push($new_project_members_to_receive_email, $staff_id);
                            if ($notifiedUsers) {
                                array_push($notifiedUsers, $staff_id);
                            }
                        }
                        $this->log_activity($id, 'project_activity_added_team_member', get_staff_full_name($staff_id));
                        $affectedRows++;
                    }
                }
                pusher_trigger_notification($notifiedUsers);
            }
        }

        if (count($new_project_members_to_receive_email) > 0) {
            $this->load->model('emails_model');
            $all_members = $this->get_project_members($id);
            foreach ($all_members as $data) {
                if (in_array($data['staff_id'], $new_project_members_to_receive_email)) {
                    $merge_fields = array();
                    $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($client_id));
                    $merge_fields = array_merge($merge_fields, get_staff_merge_fields($data['staff_id']));
                    $merge_fields = array_merge($merge_fields, get_matter_merge_fields($id));
                    $this->emails_model->send_email_template('staff-added-as-case-member', $data['email'], $merge_fields);
                }
            }
        }
        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    public function is_member($project_id, $staff_id = '')
    {
        if (!is_numeric($staff_id)) {
            $staff_id = get_staff_user_id();
        }
        $member = total_rows('tblcasetemplatemembers', array(
            'staff_id' => $staff_id,
            'project_id' => $project_id,
        ));
        if ($member > 0) {
            return true;
        }

        return false;
    }

    public function get_projects_for_ticket($client_id)
    {
        return $this->get('', array(
            'clientid' => $client_id,
        ));
    }

    public function get_project_settings($project_id)
    {
        $this->db->where('project_id', $project_id);

        return $this->db->get('tblcasetemplatesettings')->result_array();
    }

    public function get_project_members($id)
    {
        $this->db->select('email,project_id,staff_id');
        $this->db->join('tblstaff', 'tblstaff.staffid=tblcasetemplatemembers.staff_id');
        $this->db->where('project_id', $id);

        return $this->db->get('tblcasetemplatemembers')->result_array();
    }

    public function remove_team_member($project_id, $staff_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', $staff_id);
        $this->db->delete('tblcasetemplatemembers');
        if ($this->db->affected_rows() > 0) {

            // Remove member from tasks where is assigned
            $this->db->where('staffid', $staff_id);
            $this->db->where('taskid IN (SELECT id FROM tbltasks WHERE rel_type="casediary" AND rel_id="'.$project_id.'")');
            $this->db->delete('tblstafftaskassignees');

            $this->log_activity($project_id, 'project_activity_removed_team_member', get_staff_full_name($staff_id));

            return true;
        }

        return false;
    }

    public function get_timesheets($project_id, $tasks_ids = array())
    {
        if (count($tasks_ids) == 0) {
            $tasks     = $this->get_tasks($project_id);
            $tasks_ids = array();
            foreach ($tasks as $task) {
                array_push($tasks_ids, $task['id']);
            }
        }
        if (count($tasks_ids) > 0) {
            $this->db->where('task_id IN(' . implode(', ', $tasks_ids) . ')');
            $timesheets = $this->db->get('tbltaskstimers')->result_array();
            $i          = 0;
            foreach ($timesheets as $t) {
                $task                         = $this->tasks_model->get($t['task_id']);
                $timesheets[$i]['task_data']  = $task;
                $timesheets[$i]['staff_name'] = get_staff_full_name($t['staff_id']);
                if (!is_null($t['end_time'])) {
                    $timesheets[$i]['total_spent'] = $t['end_time'] - $t['start_time'];
                } else {
                    $timesheets[$i]['total_spent'] = time() - $t['start_time'];
                }
                $i++;
            }

            return $timesheets;
        } else {
            return array();
        }
    }

    public function get_discussion($id, $project_id = '')
    {
        if ($project_id != '') {
            $this->db->where('project_id', $project_id);
        }
        $this->db->where('id', $id);
        if (is_client_logged_in()) {
            $this->db->where('show_to_customer', 1);
            $this->db->where('project_id IN (SELECT id FROM tblcasetemplates WHERE clientid=' . get_client_user_id() . ')');
        }
        $discussion = $this->db->get('tblcaseprojectdiscussions')->row();
        if ($discussion) {
            return $discussion;
        }

        return false;
    }

    public function get_discussion_comment($id)
    {
        $this->db->where('id', $id);
        $comment = $this->db->get('tblcaseprojectdiscussioncomments')->row();
        if ($comment->contact_id != 0) {
            if (is_client_logged_in()) {
                if ($comment->contact_id == get_contact_user_id()) {
                    $comment->created_by_current_user = true;
                } else {
                    $comment->created_by_current_user = false;
                }
            } else {
                $comment->created_by_current_user = false;
            }
            $comment->profile_picture_url = contact_profile_image_url($comment->contact_id);
        } else {
            if (is_client_logged_in()) {
                $comment->created_by_current_user = false;
            } else {
                if (is_staff_logged_in()) {
                    if ($comment->staff_id == get_staff_user_id()) {
                        $comment->created_by_current_user = true;
                    } else {
                        $comment->created_by_current_user = false;
                    }
                } else {
                    $comment->created_by_current_user = false;
                }
            }
            if (is_admin($comment->staff_id)) {
                $comment->created_by_admin = true;
            } else {
                $comment->created_by_admin = false;
            }
            $comment->profile_picture_url = staff_profile_image_url($comment->staff_id);
        }
        $comment->created = (strtotime($comment->created) * 1000);
        if (!empty($comment->modified)) {
            $comment->modified = (strtotime($comment->modified) * 1000);
        }
        if (!is_null($comment->file_name)) {
            $comment->file_url = site_url('uploads/discussions/' . $comment->discussion_id . '/' . $comment->file_name);
        }

        return $comment;
    }

    public function get_discussion_comments($id, $type)
    {
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $comments = $this->db->get('tblcaseprojectdiscussioncomments')->result_array();
        $i        = 0;
        foreach ($comments as $comment) {
            if ($comment['contact_id'] != 0) {
                if (is_client_logged_in()) {
                    if ($comment['contact_id'] == get_contact_user_id()) {
                        $comments[$i]['created_by_current_user'] = true;
                    } else {
                        $comments[$i]['created_by_current_user'] = false;
                    }
                } else {
                    $comments[$i]['created_by_current_user'] = false;
                }
                $comments[$i]['profile_picture_url'] = contact_profile_image_url($comment['contact_id']);
            } else {
                if (is_client_logged_in()) {
                    $comments[$i]['created_by_current_user'] = false;
                } else {
                    if (is_staff_logged_in()) {
                        if ($comment['staff_id'] == get_staff_user_id()) {
                            $comments[$i]['created_by_current_user'] = true;
                        } else {
                            $comments[$i]['created_by_current_user'] = false;
                        }
                    } else {
                        $comments[$i]['created_by_current_user'] = false;
                    }
                }
                if (is_admin($comment['staff_id'])) {
                    $comments[$i]['created_by_admin'] = true;
                } else {
                    $comments[$i]['created_by_admin'] = false;
                }
                $comments[$i]['profile_picture_url'] = staff_profile_image_url($comment['staff_id']);
            }
            if (!is_null($comment['file_name'])) {
                $comments[$i]['file_url'] = site_url('uploads/discussions/' . $id . '/' . $comment['file_name']);
            }
            $comments[$i]['created'] = (strtotime($comment['created']) * 1000);
            if (!empty($comment['modified'])) {
                $comments[$i]['modified'] = (strtotime($comment['modified']) * 1000);
            }
            $i++;
        }

        return $comments;
    }

    public function get_discussions($project_id)
    {
        $this->db->where('project_id', $project_id);
        if (is_client_logged_in()) {
            $this->db->where('show_to_customer', 1);
        }
        $discussions = $this->db->get('tblcaseprojectdiscussions')->result_array();
        $i           = 0;
        foreach ($discussions as $discussion) {
            $discussions[$i]['total_comments'] = total_rows('tblcaseprojectdiscussioncomments', array(
                'discussion_id' => $discussion['id'],
            ));
            $i++;
        }

        return $discussions;
    }

    public function add_discussion_comment($data, $discussion_id, $type)
    {
        $discussion               = $this->get_discussion($discussion_id);
        $_data['discussion_id']   = $discussion_id;
        $_data['discussion_type'] = $type;
        if (isset($data['content'])) {
            $_data['content'] = $data['content'];
        }
        if (isset($data['parent']) && $data['parent'] != null) {
            $_data['parent'] = $data['parent'];
        }
        if (is_client_logged_in()) {
            $_data['contact_id'] = get_contact_user_id();
            $_data['fullname']   = get_contact_full_name($_data['contact_id']);
            $_data['staff_id']   = 0;
        } else {
            $_data['contact_id'] = 0;
            $_data['staff_id']   = get_staff_user_id();
            $_data['fullname']   = get_staff_full_name($_data['staff_id']);
        }
        $_data            = handle_project_discussion_comment_attachments($discussion_id, $data, $_data);
        $_data['created'] = date('Y-m-d H:i:s');
        $this->db->insert('tblcaseprojectdiscussioncomments', $_data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            if ($type == 'regular') {
                $discussion = $this->get_discussion($discussion_id);
                $not_link   = 'projects/view/' . $discussion->project_id . '?group=project_discussions&discussion_id=' . $discussion_id;
            } else {
                $discussion                   = $this->get_file($discussion_id);
                $not_link                     = 'projects/view/' . $discussion->project_id . '?group=project_files&file_id=' . $discussion_id;
                $discussion->show_to_customer = $discussion->visible_to_customer;
            }

            $this->send_project_email_template($discussion->project_id, 'new-project-discussion-comment-to-staff', 'new-project-discussion-comment-to-customer', $discussion->show_to_customer, array(
                'staff' => array(
                    'discussion_id' => $discussion_id,
                    'discussion_comment_id' => $insert_id,
                    'discussion_type' => $type,
                ),
                'customers' => array(
                    'customer_template' => true,
                    'discussion_id' => $discussion_id,
                    'discussion_comment_id' => $insert_id,
                    'discussion_type' => $type,
                ),
            ));


            $this->log_activity($discussion->project_id, 'project_activity_commented_on_discussion', $discussion->subject, $discussion->show_to_customer);

            $notification_data = array(
                'description' => 'not_commented_on_project_discussion',
                'link' => $not_link,
            );

            if (is_client_logged_in()) {
                $notification_data['fromclientid'] = get_contact_user_id();
            } else {
                $notification_data['fromuserid'] = get_staff_user_id();
            }

            $members = $this->get_project_members($discussion->project_id);
            $notifiedUsers = array();
            foreach ($members as $member) {
                if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                    continue;
                }
                $notification_data['touserid'] = $member['staff_id'];
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
            pusher_trigger_notification($notifiedUsers);

            $this->_update_discussion_last_activity($discussion_id, $type);

            return $this->get_discussion_comment($insert_id);
        }

        return false;
    }

    public function update_discussion_comment($data)
    {
        $comment = $this->get_discussion_comment($data['id']);
        $this->db->where('id', $data['id']);
        $this->db->update('tblcaseprojectdiscussioncomments', array(
            'modified' => date('Y-m-d H:i:s'),
            'content' => $data['content'],
        ));
        if ($this->db->affected_rows() > 0) {
            $this->_update_discussion_last_activity($comment->discussion_id, $comment->discussion_type);
        }

        return $this->get_discussion_comment($data['id']);
    }

    public function delete_discussion_comment($id)
    {
        $comment = $this->get_discussion_comment($id);
        $this->db->where('id', $id);
        $this->db->delete('tblcaseprojectdiscussioncomments');
        if ($this->db->affected_rows() > 0) {
            $this->delete_discussion_comment_attachment($comment->file_name, $comment->discussion_id);

            $additional_data = '';
            if ($comment->discussion_type == 'regular') {
                $discussion = $this->get_discussion($comment->discussion_id);
                $not        = 'project_activity_deleted_discussion_comment';
                $additional_data .= $discussion->subject . '<br />' . $comment->content;
            } else {
                $discussion = $this->get_file($comment->discussion_id);
                $not        = 'project_activity_deleted_file_discussion_comment';
                $additional_data .= $discussion->subject . '<br />' . $comment->content;
            }

            if (!is_null($comment->file_name)) {
                $additional_data .= $comment->file_name;
            }
            $this->log_activity($discussion->project_id, $not, $additional_data);
        }
        $this->db->where('parent', $id);
        $this->db->update('tblcaseprojectdiscussioncomments', array(
            'parent' => null,
        ));
        if ($this->db->affected_rows() > 0) {
            $this->_update_discussion_last_activity($comment->discussion_id, $comment->discussion_type);
        }

        return true;
    }

    public function delete_discussion_comment_attachment($file_name, $discussion_id)
    {
        $path = PROJECT_DISCUSSION_ATTACHMENT_FOLDER . $discussion_id;
        if (!is_null($file_name)) {
            if (file_exists($path . '/' . $file_name)) {
                unlink($path . '/' . $file_name);
            }
        }
        if (is_dir($path)) {
            // Check if no attachments left, so we can delete the folder also
            $other_attachments = list_files($path);
            if (count($other_attachments) == 0) {
                delete_dir($path);
            }
        }
    }

    public function add_discussion($data)
    {
        if (is_client_logged_in()) {
            $data['contact_id']       = get_contact_user_id();
            $data['staff_id']         = 0;
            $data['show_to_customer'] = 1;
        } else {
            $data['staff_id']   = get_staff_user_id();
            $data['contact_id'] = 0;
            if (isset($data['show_to_customer'])) {
                $data['show_to_customer'] = 1;
            } else {
                $data['show_to_customer'] = 0;
            }
        }
        $data['meeting_date'] = to_sql_date($data['meeting_date'],true);
        $data['datecreated'] = date('Y-m-d H:i:s');
        $data['description'] = nl2br($data['description']);
        $this->db->insert('tblcaseprojectdiscussions', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $members           = $this->get_project_members($data['project_id']);
            $notification_data = array(
                'description' => 'not_created_new_project_discussion',
                'link' => 'projects/view/' . $data['project_id'] . '?group=project_discussions&discussion_id=' . $insert_id,
            );

            if (is_client_logged_in()) {
                $notification_data['fromclientid'] = get_contact_user_id();
            } else {
                $notification_data['fromuserid'] = get_staff_user_id();
            }

            $notifiedUsers = array();
            foreach ($members as $member) {
                if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                    continue;
                }
                $notification_data['touserid'] = $member['staff_id'];
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $member['staff_id']);
                }
            }
            pusher_trigger_notification($notifiedUsers);
            $this->send_project_email_template($data['project_id'], 'new-project-discussion-created-to-staff', 'new-project-discussion-created-to-customer', $data['show_to_customer'], array(
                'staff' => array(
                    'discussion_id' => $insert_id,
                    'discussion_type' => 'regular',
                ),
                'customers' => array(
                    'customer_template' => true,
                    'discussion_id' => $insert_id,
                    'discussion_type' => 'regular',
                ),
            ));
            $this->log_activity($data['project_id'], 'project_activity_created_discussion', $data['subject'], $data['show_to_customer']);

            return $insert_id;
        }

        return false;
    }

    public function edit_discussion($data, $id)
    {
        $this->db->where('id', $id);
        if (isset($data['show_to_customer'])) {
            $data['show_to_customer'] = 1;
        } else {
            $data['show_to_customer'] = 0;
        }
        $data['meeting_date'] = to_sql_date($data['meeting_date'],true);
        $data['description'] = nl2br($data['description']);
        $this->db->update('tblcaseprojectdiscussions', $data);
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($data['project_id'], 'project_activity_updated_discussion', $data['subject'], $data['show_to_customer']);

            return true;
        }

        return false;
    }

    public function delete_discussion($id)
    {
        $discussion = $this->get_discussion($id);
        $this->db->where('id', $id);
        $this->db->delete('tblcaseprojectdiscussions');
        if ($this->db->affected_rows() > 0) {
            $this->log_activity($discussion->project_id, 'project_activity_deleted_discussion', $discussion->subject, $discussion->show_to_customer);
            $this->_delete_discussion_comments($id, 'regular');

            return true;
        }

        return false;
    }

    public function copy($project_id, $data)
    {
        $project   = $this->get($project_id);
        $settings  = $this->get_project_settings($project_id);
        $_new_data = array();
        $fields    = $this->db->list_fields('tblcasetemplates');
        foreach ($fields as $field) {
            if (isset($project->$field)) {
                $_new_data[$field] = $project->$field;
            }
        }

        unset($_new_data['id']);
        $_new_data['clientid'] = $data['clientid_copy_project'];
        unset($_new_data['clientid_copy_project']);

        $_new_data['start_date'] = to_sql_date($data['start_date']);

        if ($_new_data['start_date'] > date('Y-m-d')) {
            $_new_data['status'] = 1;
        } else {
            $_new_data['status'] = 2;
        }
        if ($data['deadline']) {
            $_new_data['deadline'] = to_sql_date($data['deadline']);
        } else {
            $_new_data['deadline'] = null;
        }

        $_new_data['project_created'] = date('Y-m-d H:i:s');
        $_new_data['addedfrom']       = get_staff_user_id();

        $_new_data['date_finished'] = null;

        $_new_data['is_template'] = "n";
        $this->db->insert('tblcasetemplates', $_new_data);
        $id = $this->db->insert_id();
        if ($id) {
            $tags = get_tags_in($project_id, 'casediary');
            handle_tags_save($tags, $id, 'casediary');

            foreach ($settings as $setting) {
                $this->db->insert('tblcasetemplatesettings', array(
                    'project_id' => $id,
                    'name' => $setting['name'],
                    'value' => $setting['value'],
                ));
            }
            $added_tasks = array();
            $tasks       = $this->get_tasks($project_id);
            if (isset($data['tasks'])) {
                foreach ($tasks as $task) {
                    if (isset($data['task_include_followers'])) {
                        $copy_task_data['copy_task_followers'] = 'true';
                    }
                    if (isset($data['task_include_assignees'])) {
                        $copy_task_data['copy_task_assignees'] = 'true';
                    }
                    if (isset($data['tasks_include_checklist_items'])) {
                        $copy_task_data['copy_task_checklist_items'] = 'true';
                    }
                    $copy_task_data['copy_from'] = $task['id'];
                    $task_id                     = $this->tasks_model->copy($copy_task_data, array(
                        'rel_id' => $id,
                        'rel_type' => 'casediary',
                        'last_recurring_date' => null,
                        'status'=>$data['copy_project_task_status'],
                    ));
                    if ($task_id) {
                        array_push($added_tasks, $task_id);
                    }
                }
            }
            if (isset($data['milestones'])) {
                $milestones        = $this->get_milestones($project_id);
                $_added_milestones = array();
                foreach ($milestones as $milestone) {
                    $dCreated = new DateTime($milestone['datecreated']);
                    $dDuedate = new DateTime($milestone['due_date']);
                    $dDiff    = $dCreated->diff($dDuedate);
                    $due_date = date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY'))));

                    $this->db->insert('tblmilestones', array(
                        'name' => $milestone['name'],
                        'project_id' => $id,
                        'milestone_order' => $milestone['milestone_order'],
                        'description_visible_to_customer' => $milestone['description_visible_to_customer'],
                        'description' => $milestone['description'],
                        'due_date' => $due_date,
                        'datecreated' => date('Y-m-d'),
                        'color' => $milestone['color'],
                    ));

                    $milestone_id = $this->db->insert_id();
                    if ($milestone_id) {
                        $_added_milestone_data         = array();
                        $_added_milestone_data['id']   = $milestone_id;
                        $_added_milestone_data['name'] = $milestone['name'];
                        $_added_milestones[]           = $_added_milestone_data;
                    }
                }
                if (isset($data['tasks'])) {
                    if (count($added_tasks) > 0) {
                        // Original project tasks
                        foreach ($tasks as $task) {
                            if ($task['milestone'] != 0) {
                                $this->db->where('id', $task['milestone']);
                                $milestone = $this->db->get('tblmilestones')->row();
                                if ($milestone) {
                                    $name = $milestone->name;
                                    foreach ($_added_milestones as $added_milestone) {
                                        if ($name == $added_milestone['name']) {
                                            $this->db->where('id IN (' . implode(', ', $added_tasks) . ')');
                                            $this->db->where('milestone', $task['milestone']);
                                            $this->db->update('tbltasks', array(
                                                'milestone' => $added_milestone['id'],
                                            ));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                // milestones not set
                if (count($added_tasks)) {
                    foreach ($added_tasks as $task) {
                        $this->db->where('id', $task['id']);
                        $this->db->update('tbltasks', array(
                            'milestone' => 0,
                        ));
                    }
                }
            }
            if (isset($data['members'])) {
                $members  = $this->get_project_members($project_id);
                $_members = array();
                foreach ($members as $member) {
                    array_push($_members, $member['staff_id']);
                }
                $this->add_edit_members(array(
                    'project_members' => $_members,
                ), $id);
            }

            $this->log_activity($id, 'project_activity_created');
            logActivity('Project Copied [ID: ' . $project_id . ', NewID: ' . $id . ']');

            return $id;
        }

        return false;
    }

public function copy_template($project_id, $data)
    {
        $project   = $this->get($project_id);
        $settings  = $this->get_project_settings($project_id);
        $_new_data = array();
        $fields    = $this->db->list_fields('tblcasetemplates');
        foreach ($fields as $field) {
            if (isset($project->$field)) {
                $_new_data[$field] = $project->$field;
            }
        }
        unset($_new_data['id']);
        $_new_data['clientid'] = $data['clientid_copy_project'];
        unset($_new_data['clientid_copy_project']);

        //unset($_new_data['name']);
        $_new_data['name'] = $data['copy_project_name'];
        //unset($_new_data['copy_project_name']);

        $_new_data['start_date'] = to_sql_date($data['start_date']);

        if ($_new_data['start_date'] > date('Y-m-d')) {
            $_new_data['status'] = 1;
        } else {
            $_new_data['status'] = 2;
        }
        if ($data['deadline']) {
            $_new_data['deadline'] = to_sql_date($data['deadline']);
        } else {
            $_new_data['deadline'] = null;
        }

        $_new_data['project_created'] = date('Y-m-d H:i:s');
        $_new_data['addedfrom']       = get_staff_user_id();

        $_new_data['date_finished'] = null;

        $_new_data['is_template'] = "n";
        $_new_data['name'] = $data['copy_project_name'];
        
        $this->db->insert('tblcasetemplates', $_new_data);
        $id = $this->db->insert_id();
        if ($id) {
            $tags = get_tags_in($project_id, 'casediary');
            handle_tags_save($tags, $id, 'casediary');

            foreach ($settings as $setting) {
                $this->db->insert('tblcasetemplatesettings', array(
                    'project_id' => $id,
                    'name' => $_new_data['name'],
                    'value' => $setting['value'],
                ));
            }
            $added_tasks = array();
            $tasks       = $this->get_tasks($project_id);
            if (isset($data['tasks'])) {
                foreach ($tasks as $task) {
                    if (isset($data['task_include_followers'])) {
                        $copy_task_data['copy_task_followers'] = 'true';
                    }
                    if (isset($data['task_include_assignees'])) {
                        $copy_task_data['copy_task_assignees'] = 'true';
                    }
                    if (isset($data['tasks_include_checklist_items'])) {
                        $copy_task_data['copy_task_checklist_items'] = 'true';
                    }
                    $copy_task_data['copy_from'] = $task['id'];
                    $task_id                     = $this->tasks_model->copy($copy_task_data, array(
                        'rel_id' => $id,
                        'rel_type' => 'casediary',
                        'last_recurring_date' => null,
                        'status'=>$data['copy_project_task_status'],
                    ));
                    if ($task_id) {
                        array_push($added_tasks, $task_id);
                    }
                }
            }
            if (isset($data['milestones'])) {
                $milestones        = $this->get_milestones($project_id);
                $_added_milestones = array();
                foreach ($milestones as $milestone) {
                    $dCreated = new DateTime($milestone['datecreated']);
                    $dDuedate = new DateTime($milestone['due_date']);
                    $dDiff    = $dCreated->diff($dDuedate);
                    $due_date = date('Y-m-d', strtotime(date('Y-m-d', strtotime('+' . $dDiff->days . 'DAY'))));

                    $this->db->insert('tblmilestones', array(
                        'name' => $milestone['name'],
                        'project_id' => $id,
                        'milestone_order' => $milestone['milestone_order'],
                        'description_visible_to_customer' => $milestone['description_visible_to_customer'],
                        'description' => $milestone['description'],
                        'due_date' => $due_date,
                        'datecreated' => date('Y-m-d'),
                        'color' => $milestone['color'],
                    ));

                    $milestone_id = $this->db->insert_id();
                    if ($milestone_id) {
                        $_added_milestone_data         = array();
                        $_added_milestone_data['id']   = $milestone_id;
                        $_added_milestone_data['name'] = $milestone['name'];
                        $_added_milestones[]           = $_added_milestone_data;
                    }
                }
                if (isset($data['tasks'])) {
                    if (count($added_tasks) > 0) {
                        // Original project tasks
                        foreach ($tasks as $task) {
                            if ($task['milestone'] != 0) {
                                $this->db->where('id', $task['milestone']);
                                $milestone = $this->db->get('tblmilestones')->row();
                                if ($milestone) {
                                    $name = $milestone->name;
                                    foreach ($_added_milestones as $added_milestone) {
                                        if ($name == $added_milestone['name']) {
                                            $this->db->where('id IN (' . implode(', ', $added_tasks) . ')');
                                            $this->db->where('milestone', $task['milestone']);
                                            $this->db->update('tbltasks', array(
                                                'milestone' => $added_milestone['id'],
                                            ));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            } else {
                // milestones not set
                if (count($added_tasks)) {
                    foreach ($added_tasks as $task) {
                        $this->db->where('id', $task['id']);
                        $this->db->update('tbltasks', array(
                            'milestone' => 0,
                        ));
                    }
                }
            }
            if (isset($data['members'])) {
                $members  = $this->get_project_members($project_id);
                $_members = array();
                foreach ($members as $member) {
                    array_push($_members, $member['staff_id']);
                }
                $this->add_edit_members(array(
                    'project_members' => $_members,
                ), $id);
            }

            $this->log_activity($id, 'project_activity_created');
            logActivity('Project Copied [ID: ' . $project_id . ', NewID: ' . $id . ']');

            return $id;
        }

        return false;
    }
    public function get_staff_notes($project_id)
    {
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', get_staff_user_id());
        $notes = $this->db->get('tblcaseprojectnotes')->row();
        if ($notes) {
            return $notes->content;
        }

        return '';
    }

    public function save_note($data, $project_id)
    {
        // Check if the note exists for this project;
        $this->db->where('project_id', $project_id);
        $this->db->where('staff_id', get_staff_user_id());
        $notes = $this->db->get('tblcaseprojectnotes')->row();
        if ($notes) {
            $this->db->where('id', $notes->id);
            $this->db->update('tblcaseprojectnotes', array(
                'content' => $data['content'],
            ));
            if ($this->db->affected_rows() > 0) {
                return true;
            }

            return false;
        } else {
            $this->db->insert('tblcaseprojectnotes', array(
                'staff_id' => get_staff_user_id(),
                'content' => $data['content'],
                'project_id' => $project_id,
            ));
            $insert_id = $this->db->insert_id();
            if ($insert_id) {
                return true;
            }

            return false;
        }

        return false;
    }

    public function delete($project_id)
    {
        $project_name = get_matter_template_name_by_id($project_id);

        $this->db->where('id', $project_id);
        $this->db->delete('tblcasetemplates');
        if ($this->db->affected_rows() > 0) {
            $this->db->where('project_id', $project_id);
            $this->db->delete('tblcasetemplatemembers');
    
            $tasks = $this->db->get_where('tblstafftasks_templates',array('rel_id'=>$project_id))->result_array();
            foreach ($tasks as $task) {
                $this->tasks_model->delete_task($task['id'], false);
            }

            $this->db->where('project_id', $project_id);
            $this->db->delete('tblcasetemplateactactivity');

            logActivity('Matter Template Deleted [ID: ' . $project_id . ', Name: ' . $project_name . ']');

            return true;
        }

        return false;
    }

    public function get_activity($id = '', $limit = '', $only_project_members_activity = false)
    {
        if (!is_client_logged_in()) {
            $has_permission = has_permission('casediary', '', 'view');
            if (!$has_permission) {
                $this->db->where('project_id IN (SELECT project_id FROM tblcasetemplatemembers WHERE staff_id=' . get_staff_user_id() . ')');
            }
        }
        if (is_client_logged_in()) {
            $this->db->where('visible_to_customer', 1);
        }
        if (is_numeric($id)) {
            $this->db->where('project_id', $id);
        }
        if (is_numeric($limit)) {
            $this->db->limit($limit);
        }
        $this->db->order_by('dateadded', 'desc');
        $activities = $this->db->get('tblcasetemplateactactivity')->result_array();
        $i          = 0;
        foreach ($activities as $activity) {
            $seconds          = get_string_between($activity['additional_data'], '<seconds>', '</seconds>');
            $other_lang_keys  = get_string_between($activity['additional_data'], '<lang>', '</lang>');
            $_additional_data = $activity['additional_data'];
            if ($seconds != '') {
                $_additional_data = str_replace('<seconds>' . $seconds . '</seconds>', seconds_to_time_format($seconds), $_additional_data);
            }
            if ($other_lang_keys != '') {
                $_additional_data = str_replace('<lang>' . $other_lang_keys . '</lang>', _l($other_lang_keys), $_additional_data);
            }
            if (strpos($_additional_data, 'project_status_') !== false) {
                $_additional_data = get_project_status_by_id(strafter($_additional_data, 'project_status_'));
            }
            $activities[$i]['description']     = _l($activities[$i]['description_key']);
            $activities[$i]['additional_data'] = $_additional_data;
            $activities[$i]['project_name'] = get_project_name_by_id($activity['project_id']);
            unset($activities[$i]['description_key']);
            $i++;
        }

        return $activities;
    }

    public function log_activity($project_id, $description_key, $additional_data = '', $visible_to_customer = 1)
    {
        if (!DEFINED('CRON')) {
            if (is_client_logged_in()) {
                $data['contact_id'] = get_contact_user_id();
                $data['staff_id']   = 0;
                $data['fullname']   = get_contact_full_name(get_contact_user_id());
            } elseif (is_staff_logged_in()) {
                $data['contact_id'] = 0;
                $data['staff_id']   = get_staff_user_id();
                $data['fullname']   = get_staff_full_name(get_staff_user_id());
            }
        } else {
            $data['contact_id'] = 0;
            $data['staff_id']   = 0;
            $data['fullname']   = '[CRON]';
        }
        $data['description_key']     = $description_key;
        $data['additional_data']     = $additional_data;
        $data['visible_to_customer'] = $visible_to_customer;
        $data['project_id']          = $project_id;
        $data['dateadded']           = date('Y-m-d H:i:s');

        // $data = do_action('before_log_project_activity', $data);

        $this->db->insert('tblcasetemplateactactivity', $data);
    }

    public function new_project_file_notification($file_id, $project_id)
    {
        $file = $this->get_file($file_id);

        $additional_data = $file->file_name;
        $this->log_activity($project_id, 'matter_activity_uploaded_file', $additional_data, $file->visible_to_customer);

        $members = $this->get_project_members($project_id);
        $notification_data = array(
           'description'=>'not_project_file_uploaded',
           'link'=>'projects/view/'.$project_id.'?group=project_files&file_id='.$file_id,
           );

        if (is_client_logged_in()) {
            $notification_data['fromclientid'] = get_contact_user_id();
        } else {
            $notification_data['fromuserid'] = get_staff_user_id();
        }

        $notifiedUsers = array();
        foreach ($members as $member) {
            if ($member['staff_id'] == get_staff_user_id() && !is_client_logged_in()) {
                continue;
            }
            $notification_data['touserid'] = $member['staff_id'];
            if (add_notification($notification_data)) {
                array_push($notifiedUsers, $member['staff_id']);
            }
        }
        pusher_trigger_notification($notifiedUsers);

        $this->send_project_email_template(
           $project_id,
           'new-project-file-uploaded-to-staff',
           'new-project-file-uploaded-to-customer',
           $file->visible_to_customer,
           array(
            'staff'=>array('discussion_id'=>$file_id, 'discussion_type'=>'file'),
            'customers'=>array('customer_template'=>true, 'discussion_id'=>$file_id, 'discussion_type'=>'file'),
            )
           );
    }

    public function add_external_file($data)
    {
        $insert['dateadded'] = date('Y-m-d H:i:s');
        $insert['project_id'] = $data['project_id'];
        $insert['external'] = $data['external'];
        $insert['visible_to_customer'] = $data['visible_to_customer'];
        $insert['file_name'] = $data['files'][0]['name'];
        $insert['subject'] = $data['files'][0]['name'];
        $insert['external_link'] = $data['files'][0]['link'];

        $path_parts            = pathinfo($data['files'][0]['name']);
        $insert['filetype']      = get_mime_by_extension('.' . $path_parts['extension']);

        if (isset($data['files'][0]['thumbnailLink'])) {
            $insert['thumbnail_link'] = $data['files'][0]['thumbnailLink'];
        }

        if (isset($data['staffid'])) {
            $insert['staffid'] = $data['staffid'];
        } elseif (isset($data['contact_id'])) {
            $insert['contact_id'] = $data['contact_id'];
        }

        $this->db->insert('tblcaseprojectfiles', $insert);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            $this->new_project_file_notification($insert_id, $data['project_id']);

            return $insert_id;
        }

        return false;
    }

    public function send_project_email_template($project_id, $staff_template, $customer_template, $action_visible_to_customer, $additional_data = array())
    {
        if (count($additional_data) == 0) {
            $additional_data['customers'] = array();
            $additional_data['staff']     = array();
        } elseif (count($additional_data) == 1) {
            if (!isset($additional_data['staff'])) {
                $additional_data['staff'] = array();
            } else {
                $additional_data['customers'] = array();
            }
        }

        $project = $this->get($project_id);
        $members = $this->get_project_members($project_id);

        $this->load->model('emails_model');
        foreach ($members as $member) {
            if (is_staff_logged_in()) {
                if ($member['staff_id'] == get_staff_user_id()) {
                    continue;
                }
            }
            $merge_fields = array();
            $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($project->clientid));
            $merge_fields = array_merge($merge_fields, get_staff_merge_fields($member['staff_id']));
            $merge_fields = array_merge($merge_fields, get_project_merge_fields($project->id, $additional_data['staff']));
            $this->emails_model->send_email_template($staff_template, $member['email'], $merge_fields);
        }
        if ($action_visible_to_customer == 1) {
            $contacts = $this->clients_model->get_contacts($project->clientid, array('active'=>1, 'project_emails'=>1));
            foreach ($contacts as $contact) {
                if (is_client_logged_in()) {
                    if ($contact['id'] == get_contact_user_id()) {
                        continue;
                    }
                }
                $merge_fields = array();
                $merge_fields = array_merge($merge_fields, get_client_contact_merge_fields($project->clientid, $contact['id']));
                $merge_fields = array_merge($merge_fields, get_project_merge_fields($project->id, $additional_data['customers']));
                $this->emails_model->send_email_template($customer_template, $contact['email'], $merge_fields);
            }
        }
    }

    private function _get_project_billing_data($id)
    {
        $this->db->select('billing_type,project_rate_per_hour');
        $this->db->where('id', $id);

        return $this->db->get('tblcasetemplates')->row();
    }

    public function total_logged_time_by_billing_type($id, $conditions = array())
    {
        $project_data = $this->_get_project_billing_data($id);
        $data         = array();
        if ($project_data->billing_type == 2) {
            $seconds             = $this->total_logged_time($id);
            $data                = $this->projects_model->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        } elseif ($project_data->billing_type == 3) {
            $data = $this->_get_data_total_logged_time($id);
        }

        return $data;
    }

    public function data_billable_time($id)
    {
        return $this->_get_data_total_logged_time($id, array(
            'billable' => 1,
        ));
    }

    public function data_billed_time($id)
    {
        return $this->_get_data_total_logged_time($id, array(
            'billable' => 1,
            'billed' => 1,
        ));
    }

    public function data_unbilled_time($id)
    {
        return $this->_get_data_total_logged_time($id, array(
            'billable' => 1,
            'billed' => 0,
        ));
    }

    private function _delete_discussion_comments($id, $type)
    {
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $comments = $this->db->get('tblcaseprojectdiscussioncomments')->result_array();
        foreach ($comments as $comment) {
            $this->delete_discussion_comment_attachment($comment['file_name'], $id);
        }
        $this->db->where('discussion_id', $id);
        $this->db->where('discussion_type', $type);
        $this->db->delete('tblcaseprojectdiscussioncomments');
    }

    private function _get_data_total_logged_time($id, $conditions = array())
    {
        $project_data = $this->_get_project_billing_data($id);
        $tasks        = $this->get_tasks($id, $conditions);

        if ($project_data->billing_type == 3) {
            $data                = $this->calculate_total_by_task_hourly_rate($tasks);
            $data['logged_time'] = seconds_to_time_format($data['total_seconds']);
        } elseif ($project_data->billing_type == 2) {
            $seconds = 0;
            foreach ($tasks as $task) {
                $seconds += $task['total_logged_time'];
            }
            $data                = $this->calculate_total_by_project_hourly_rate($seconds, $project_data->project_rate_per_hour);
            $data['logged_time'] = $data['hours'];
        }

        return $data;
    }

    private function _update_discussion_last_activity($id, $type)
    {
        if ($type == 'file') {
            $table = 'tblcaseprojectfiles';
        } elseif ($type == 'regular') {
            $table = 'tblcaseprojectdiscussions';
        }
        $this->db->where('id', $id);
        $this->db->update($table, array(
            'last_activity' => date('Y-m-d H:i:s'),
        ));
    }

    public function get_project_templates(){
        $this->db->select('id,name');
        $this->db->where('is_template', "y");
        return  $this->db->get('tblcasetemplates')->result_array();
    }

    public function get_caseacts($case_id)
    {
       $this->db->select('tblacts.*');
       $this->db->join('tblcaseacts','tblcaseacts.act_id = tblacts.id');
       $this->db->where('tblcaseacts.case_id',$case_id);
       return $this->db->get('tblacts')->result();
    }

    public function add_case_acts($data){
        //Delete All Existing Acts
        $this->db->where('case_id',$data['case_id']);
        $this->db->delete('tblcaseacts');
        $id = false;
        // Insert acts
        $idata['case_id'] = $data['case_id'];
        foreach ($data['act'] as $key => $value) {
            $idata['act_id'] = $value;
            $this->db->insert('tblcaseacts',$idata);
            $id = $this->db->insert_id();    
        }
        return $id;
    }

    public function add_scope($data){
        $this->db->insert('tblcasetemplate_scopes',$data);
        return true;
    }

    public function get_scopes($project_id)
    {
        $this->db->where('casetemplate_id', $project_id);
        return $this->db->get('tblcasetemplate_scopes')->result_array();
    }

    public function edit_scope($data, $id)
    {
        $this->db->where('id', $id);
        $this->db->update('tblcasetemplate_scopes', array(
            'scope_description' => nl2br($data['description'])
        ));
        if ($this->db->affected_rows() > 0) {
            return true;
        }

        return false;
    }
   
    public function delete_scope($id)
    {
        $this->db->where('id', $id);
        $this->db->delete('tblcasetemplate_scopes');
        //if ($this->db->affected_rows() > 0) {
            return true;
        //}

        //return false;
    }

    function get_document_master_templates($template_id){
         $this->db->where('template_id', $template_id);
        return $this->db->get('tbldocumentmaster')->result_array();
    }


    public function new_document_checklist($data){

        $this->db->insert('tbldocumentmaster',$data);
        $id = $this->db->insert_id();    
        return $id;
    }

     public function update_document_checklist($id,$data){
        $this->db->where('id', $id);
        $this->db->update('tbldocumentmaster',$data);
        return true;
    }

    public function add_to_template($data){
        $data['document_checklists'] = implode(',', $data['document_checklists']);
        $this->db->where('id',$data['casetemplate_id']);
        unset($data['casetemplate_id']);
        $this->db->update('tblcasetemplates',$data);
        return true;
    }
    
    
    
    // Template

     /**
     * Add new staff task
     * @param array $data task $_POST data
     * @return mixed
     */
    public function add_template_task($data, $clientRequest = false)
    {
        $ticket_to_task = false;

        if (isset($data['ticket_to_task'])) {
            $ticket_to_task = true;
            unset($data['ticket_to_task']);
        }

        $data['startdate'] = to_sql_date($data['startdate']);
        $data['duedate']   = to_sql_date($data['duedate']);
        $data['dateadded'] = date('Y-m-d H:i:s');
        $data['addedfrom'] = $clientRequest == false ? get_staff_user_id() : get_contact_user_id();
        $data['is_added_from_contact'] = $clientRequest == false ? 0 : 1;

        $checklistItems = array();
        if (isset($data['checklist_items']) && count($data['checklist_items']) > 0) {
            $checklistItems = $data['checklist_items'];
            unset($data['checklist_items']);
        }

        if ($clientRequest == false) {
            $defaultStatus = get_option('default_task_status');
            if ($defaultStatus == 'auto') {
                if (date('Y-m-d') >= $data['startdate']) {
                    $data['status'] = 4;
                } else {
                    $data['status'] = 1;
                }
            } else {
                $data['status'] = $defaultStatus;
            }
        } else {
            // When client create task the default status is NOT STARTED
            // After staff will get the task will change the status
            $data['status'] = 1;
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            unset($data['custom_fields']);
        }

        if (isset($data['is_public'])) {
            $data['is_public'] = 1;
        } else {
            $data['is_public'] = 0;
        }


        if (isset($data['repeat_every']) && $data['repeat_every'] != '') {
            $data['recurring'] = 1;
            if ($data['repeat_every'] == 'custom') {
                $data['repeat_every']     = $data['repeat_every_custom'];
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
            } else {
                $_temp                    = explode('-', $data['repeat_every']);
                $data['recurring_type']   = $_temp[1];
                $data['repeat_every']     = $_temp[0];
                $data['custom_recurring'] = 0;
            }
        } else { 
            $data['recurring']    = 0;
            $data['repeat_every'] = null;
        }

        if (isset($data['repeat_type_custom']) && isset($data['repeat_every_custom'])) {
            unset($data['repeat_type_custom']);
            unset($data['repeat_every_custom']);
        }

        unset($data['settings']);
        /*if (isset($data['recurring_ends_on']) && $data['recurring_ends_on'] == '') {
            unset($data['recurring_ends_on']);
        } elseif (isset($data['recurring_ends_on']) && $data['recurring_ends_on'] != '') {
            $data['recurring_ends_on'] = to_sql_date($data['recurring_ends_on']);
        }

        if (isset($data['repeat_every']) && $data['repeat_every'] != '') {
            $data['recurring'] = 1;
            if ($data['repeat_every'] == 'custom') {
                $data['repeat_every']     = $data['repeat_every_custom'];
                $data['recurring_type']   = $data['repeat_type_custom'];
                $data['custom_recurring'] = 1;
            } else {
                $_temp                    = explode('-', $data['repeat_every']);
                $data['recurring_type']   = $_temp[1];
                $data['repeat_every']     = $_temp[0];
                $data['custom_recurring'] = 0;
            }
        } else {
            $data['recurring'] = 0;
        }

        if (isset($data['repeat_type_custom']) && isset($data['repeat_every_custom'])) {
            unset($data['repeat_type_custom']);
            unset($data['repeat_every_custom']);
        }*/

        if (is_client_logged_in() || $clientRequest) {
            $data['visible_to_client'] = 1;
        } else {
            if (isset($data['visible_to_client'])) {
                $data['visible_to_client'] = 1;
            } else {
                $data['visible_to_client'] = 0;
            }
        }

        if (isset($data['billable'])) {
            $data['billable'] = 1;
        } else {
            $data['billable'] = 0;
        }

        $data['milestone'] = 0;
        
        if (empty($data['rel_type'])) {
            unset($data['rel_type']);
            unset($data['rel_id']);
        } else {
            if (empty($data['rel_id'])) {
                unset($data['rel_type']);
                unset($data['rel_id']);
            }
        }

        //$data = do_action('before_add_task', $data);

        $tags = '';
        if (isset($data['tags'])) {
            $tags  = $data['tags'];
            unset($data['tags']);
        }

        $this->db->insert('tblstafftasks_templates', $data);
        $insert_id = $this->db->insert_id();
        if ($insert_id) {
            
            handle_tags_save($tags, $insert_id, 'task_template');
            

           

            if ($clientRequest == false) {
                $new_task_auto_assign_creator = (get_option('new_task_auto_assign_current_member') == '1' ? true : false);

                if (isset($data['rel_type']) && $data['rel_type'] == 'project' && !$this->projects_model->is_member($data['rel_id'])) {
                    $new_task_auto_assign_creator = false;
                }
            }

            log_activity('New Task in Case Template Added [ID:' . $insert_id . ', Name: ' . $data['name'] . ']');
            return $insert_id;
        }

        return false;
    }

     /**
     * Get task by id
     * @param  mixed $id task id
     * @return object
     */
    public function get_temp_task($id, $where = array())
    {
        $is_admin = is_admin();
        $this->db->where('id', $id);
        $this->db->where($where);
        $task = $this->db->get('tblstafftasks_templates')->row();
        if ($task) {
            $task->comments        = [];
            $task->assignees       = [];//$this->get_task_assignees($id);
            $task->assignees_ids = array();

            foreach ($task->assignees as $follower) {
                array_push($task->assignees_ids, $follower['assigneeid']);
            }

            $task->followers       = [];
            $task->followers_ids = array();
            foreach ($task->followers as $follower) {
                array_push($task->followers_ids, $follower['followerid']);
            }

            //$task->attachments     = $this->get_task_attachments($id);
            //$task->timesheets      = $this->get_timesheeets($id);
            $task->checklist_items = [];

            if (is_staff_logged_in()) {
                $task->current_user_is_assigned = true;
                $task->current_user_is_creator = true;
            }

            $task->milestone_name = '';
        }

        return  $task;
    }

    /**
     * Delete task and all connections
     * @param  mixed $id taskid
     * @return boolean
     */
    public function delete_temp_task($id, $log_activity = true)
    {
        $this->db->select('rel_type,rel_id,name,visible_to_client,addedfrom');
        $this->db->where('id', $id);
        $task = $this->db->get('tblstafftasks_templates')->row();

        $this->db->where('id', $id);
        $this->db->delete('tblstafftasks_templates');
        if ($this->db->affected_rows() > 0) {

          /*  $task_creator =  $task->addedfrom;
            //if ($task_creator != get_staff_user_id()) {

                $notification_data = array(
                'description' => 'Task  -'.$task->name.'- Deleted by '.get_staff_full_name(get_staff_user_id()),
                'touserid' => $task->addedfrom,
                'link' => '#taskid=' . $id,
                );

                $notification_data['additional_data'] = '';
                if (add_notification($notification_data)) {
                    array_push($notifiedUsers, $task->addedfrom);
                }
                $staffemail = $this->db->get_where('tblstaff',array('staffid'=>$task->addedfrom))->row()->email;
                $email = $staffemail;
                $subject = 'Task Deleted';
                $message = 'Dear  '.get_staff_full_name($task->addedfrom).',<br><br><br>Task Deleted <br><br> Task Name : '.$task->name.'<br><br>'.'Deleted By : '.get_staff_full_name(get_staff_user_id()).'<br><br>Best Regards.'; 
               $this->load->model('emails_model');
                $this->emails_model->send_simple_email($email, $subject, $message);
            //}


            // Log activity only if task is deleted indivudual not when deleting all projects
            if ($task->rel_type == 'project' && $log_activity == true) {
                $this->projects_model->log_activity($task->rel_id, 'project_activity_task_deleted', $task->name, $task->visible_to_client);
            }

             if ($task->rel_type == 'casediary' && $log_activity == true) {
                $this->casediary_model->log_activity($task->rel_id, 'project_activity_task_deleted', $task->name, $task->visible_to_client);
            }


            $this->db->where('taskid', $id);
            $this->db->delete('tblstafftasksfollowers');

            $this->db->where('taskid', $id);
            $this->db->delete('tblstafftaskassignees');

            $this->db->where('taskid', $id);
            $this->db->delete('tblstafftaskcomments');

            $this->db->where('taskid', $id);
            $this->db->delete('tbltaskchecklists');
            // Delete the custom field values
            $this->db->where('relid', $id);
            $this->db->where('fieldto', 'tasks');
            $this->db->delete('tblcustomfieldsvalues');

            $this->db->where('task_id', $id);
            $this->db->delete('tbltaskstimers');


            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'task');
            $this->db->delete('tbltags_in');


            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'task');
            $attachments = $this->db->get('tblfiles')->result_array();
            foreach ($attachments as $at) {
                $this->remove_task_attachment($at['id']);
            }

            $this->db->where('rel_id', $id);
            $this->db->where('rel_type', 'task');
            $this->db->delete('tblitemsrelated');

            if (is_dir(get_upload_path_by_type('task') . $id)) {
                delete_dir(get_upload_path_by_type('task') . $id);
            }*/

            return true;
        }

        return false;
    }
    
     /**
     * Update task data
     * @param  array $data task data $_POST
     * @param  mixed $id   task id
     * @return boolean
     */
    public function update_template_task($data, $id, $clientRequest = false)
    {
        $affectedRows      = 0;
        $data['startdate'] = to_sql_date($data['startdate']);
        $data['duedate']   = to_sql_date($data['duedate']);

        $checklistItems = array();
        if (isset($data['checklist_items']) && count($data['checklist_items']) > 0) {
            $checklistItems = $data['checklist_items'];
            unset($data['checklist_items']);
        }

        if (isset($data['datefinished'])) {
            $data['datefinished'] = to_sql_date($data['datefinished'], true);
        }

        if (isset($data['custom_fields'])) {
            $custom_fields = $data['custom_fields'];
            if (handle_custom_fields_post($id, $custom_fields)) {
                $affectedRows++;
            }
            unset($data['custom_fields']);
        }

        if ($clientRequest == false) {
            $data['repeat_every'] ='';
                
            $data['recurring'] = 0;
           //$data['recurring_ends_on'] = null;


            if (isset($data['repeat_type_custom']) && isset($data['repeat_every_custom'])) {
                unset($data['repeat_type_custom']);
                unset($data['repeat_every_custom']);
            }

            if (isset($data['is_public'])) {
                $data['is_public'] = 1;
            } else {
                $data['is_public'] = 0;
            }
            if (isset($data['billable'])) {
                $data['billable'] = 1;
            } else {
                $data['billable'] = 0;
            }

            if (isset($data['visible_to_client'])) {
                $data['visible_to_client'] = 1;
            } else {
                $data['visible_to_client'] = 0;
            }
        }

        if ((!isset($data['milestone']) || $data['milestone'] == '') || (isset($data['milestone']) && $data['milestone'] == '')) {
            $data['milestone'] = 0;
        } else {
            if ($data['rel_type'] != 'project') {
                $data['milestone'] = 0;
            }
        }


        if (empty($data['rel_type'])) {
            $data['rel_id']   = null;
            $data['rel_type'] = null;
        } else {
            if (empty($data['rel_id'])) {
                $data['rel_id']   = null;
                $data['rel_type'] = null;
            }
        }


        if (isset($data['tags'])) {
            if (handle_tags_save($data['tags'], $id, 'task_template')) {
                $affectedRows++;
            }
            unset($data['tags']);
        }

        $this->db->where('id', $id);
        $this->db->update('tblstafftasks_templates', $data);
        if ($this->db->affected_rows() > 0) {
            $affectedRows++;
            log_activity('Task in Matter Template Updated [ID:' . $id . ', Name: ' . $data['name'] . ']');
        }

        if ($affectedRows > 0) {
            return true;
        }

        return false;
    }

    

}
