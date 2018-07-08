<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Settings of the plugin.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($ADMIN->fulltree) {

    $settings->add(new admin_setting_configtext('auth_billing/host',
        new lang_string('url', 'moodle'), '', null, PARAM_URL));

    $settings->add(new admin_setting_configtext('auth_billing/path',
        new lang_string('path', 'moodle'), '', '', PARAM_RAW));

    $settings->add(new admin_setting_configtext('auth_billing/token',
        new lang_string('password', 'moodle'), '', '', PARAM_RAW));

    /* Display locking / mapping of profile fields. */
    $authplugin = get_auth_plugin('billing');
    display_auth_lock_options($settings, $authplugin->authtype,
        $authplugin->userfields, get_string('auth_fieldlocks_help', 'auth'), false, false);
}