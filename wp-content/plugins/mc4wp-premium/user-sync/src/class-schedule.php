<?php

namespace MC4WP\User_Sync;

class Schedule
{
    public function hook()
    {
        add_filter('cron_schedules', [$this, 'filter_cron_schedules'], 10, 1);
    }

    public function filter_cron_schedules($schedules)
    {
        $schedules['every-10-minutes'] = [
            'interval' => 600,
            'display' => __('Every 10 minutes', 'mc4wp-user-sync'),
        ];
        return $schedules;
    }

    public function setup()
    {
        add_filter('cron_schedules', [$this, 'filter_cron_schedules'], 10, 1);
        $next_run = wp_next_scheduled('mc4wp_user_sync_process_queue');
        if ($next_run && ($next_run - time()) < 600) {
            return;
        }

        wp_schedule_event(time() + 600, 'every-10-minutes', 'mc4wp_user_sync_process_queue');
    }

    public function clear()
    {
        wp_clear_scheduled_hook('mc4wp_user_sync_process_queue');
    }
}
