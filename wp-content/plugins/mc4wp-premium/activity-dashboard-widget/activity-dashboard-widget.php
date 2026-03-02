<?php

/*
Mailchimp Activity
Copyright (C) 2015-2023, ibericode <support@ibericode.com>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

defined('ABSPATH') or exit;

if (is_admin()) {
    require __DIR__ . '/includes/class-dashboard-widget.php';
    require __DIR__ . '/includes/class-activity-data.php';
    require __DIR__ . '/includes/class-size-data.php';
    $widget = new MC4WP_ADW_Dashboard_Widget(__FILE__, '1.0.2');
    $widget->add_hooks();
}
