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
 * Класс плагина аутентификации.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->libdir . '/authlib.php');
require_once(__DIR__ . '/lib.php');

/**
 * Основной класс плагина.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

class auth_plugin_billing extends auth_plugin_base {
    /**
     * Конструктор.
     */
    public function __construct() {
        $this->authtype = 'billing';
        $this->config = get_config('auth_billing');
    }

    /**
     * Старый синтаксис конструктора.
     * Устарело в PHP7.
     *
     * @deprecated  since   Moodle 3.1
     */
    public function auth_plugin_billing() {
        debugging('Use of class name as constructor is deprecated', DEBUG_DEVELOPER);
        self::__construct();
    }

    /**
     * Перенаправление пользователя на изначальную страницу.
     */
    protected static function redirect() {
        global $CFG, $SESSION;

        $wantsurl = optional_param('wantsurl', '', PARAM_URL);
        $redirect = new moodle_url($CFG->wwwroot);

        if (isset($SESSION->wantsurl)) {
            $redirect = new moodle_url($SESSION->wantsurl);
        }

        if (!empty($wantsurl)) {
            $redirect = new moodle_url($wantsurl);
        }

        redirect($redirect);
    }

    /**
     * Истинно, если пользователь существует и идентификация успешна.
     *
     * @param   string  $username   Логин пользователя
     * @param   string  $password   Пароль пользователя
     * @return  boolean             Результат проверки
     */
    public function user_login($username, $password) {
        if (!validate_email($username)) {
            if (!$user = get_complete_user_data('username', $username)) {
                return false;
            }

            if (!auth_billing::check_user($user->email, $password)) {
                return false;
            }
        } else {
            if (!auth_billing::check_user($username, $password)) {
                return false;
            }

            if (!$user = get_complete_user_data('email', $username)) {
                if (!auth_billing::create_user($username)) {
                    return false;
                }

                $user = get_complete_user_data('email', $username);
            }
        }

        complete_user_login($user);
        self::redirect();
    }

    /**
     * Обновление пароля пользователя.
     * Вызывается при смене пароля.
     *
     * @param   object  $user           Пользователь
     * @param   string  $newpassword    Пароль
     * @return  boolean                 Результат
     */
    public function user_update_password($user, $newpassword) {
        $user = get_complete_user_data('id', $user->id);
        return update_internal_user_password($user, $newpassword);
    }

    /**
     * Истинно, если плагин позволяет пользователю установить личный пароль.
     *
     * @return  boolean
     */
    public function prevent_local_passwords() {
        return false;
    }

    /**
     * Истинно, если плагин является внутренним.
     *
     * @return  boolean
     */
    public function is_internal() {
        return true;
    }

    /**
     * Истинно, если плагин изменяет пароль пользователя.
     *
     * @return  boolean
     */
    public function can_change_password() {
        return false;
    }

    /**
     * Получение адреса URL для смены пароля.
     *
     * @return  moodle_url
     */
    public function change_password_url() {
        return null;
    }

    /**
     * Истинно, если плагин позволяет сбросить пароль.
     *
     * @return  boolean
     */
    public function can_reset_password() {
        return false;
    }

    /**
     * Истинно, если плагин устанавливается вручную.
     *
     * @return  boolean
     */
    public function can_be_manually_set() {
        return true;
    }
}