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
 * Объявление класса плагина авторизации.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

require_once($CFG->dirroot . '/auth/billing/lib.php');
require_once($CFG->libdir . '/authlib.php');

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
        $this->errorlogtag = '[AUTH BILLING] ';
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
     * Истинно, если пользователь идентифицирован.
     *
     * @param   string  $username   Логин пользователя
     * @param   string  $password   Пароль пользователя
     * @return  boolean             Результат проверки
     */
    public function user_login($username, $password) {
        return auth_billing::check_user($username, $password);
    }

    /**
     * Получение информации о пользователе из внешней системы.
     *
     * @param   string  $username   Логин пользователя
     * @return  array               Пользовательская информация
     */
    public function get_userinfo($username) {
        return auth_billing::create_profile($username);
    }

    /**
     * Обновление пароля пользователя.
     *
     * @param   object  $user           Пользователь
     * @param   string  $newpassword    Пароль
     * @return  boolean                 Результат
     */
    public function user_update_password($user, $newpassword) {
        return false;
    }

    /**
     * Истинно, если плагин использует локальный пароль.
     *
     * @return  boolean
     */
    public function prevent_local_passwords() {
        return false;
    }

    /**
     * Истинно, если плагин не использует сторонние системы авторизации.
     *
     * @return  boolean
     */
    public function is_internal() {
        return false;
    }

    /**
     * Истинно, если плагин позволяет сменить пароль.
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
     * Истинно, если плагин разрешает сбрасывать пароль.
     *
     * @return  boolean
     */
    public function can_reset_password() {
        return false;
    }

    /**
     * Истинно, если плагин установлен вручную.
     *
     * @return  boolean
     */
    public function can_be_manually_set() {
        return true;
    }
}