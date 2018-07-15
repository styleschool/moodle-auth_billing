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
 * Библиотека функций плагина.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

/**
 * Класс для работы со внешней системой.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_billing {
    /**
     * Проверяет во внешней системе данные пользователя.
     *
     * @param   string  $email      Электронный адрес
     * @param   string  $password   Пароль пользователя
     * @return  boolean             Результат синхронизации
     */
    public static function check_user(string $email, string $password) {
        $config = get_config('auth_billing');
        $url = new moodle_url($config->host . $config->api . '/authorization');
        $param = array('email' => $email, 'password' => $password, 'token' => $config->token);

        if (isset(self::send_package($url, $param)[0])) {
            return self::send_package($url, $param)[0];
        }

        return false;
    }

    /**
     * Создаёт локального пользователя, используя информацию внешней системы.
     *
     * @param   string  $email  Электронный адрес
     * @return  boolean         Результат выполнения
     */
    public static function create_user(string $email) {
        global $CFG;

        /* Не допускаем дублирования пользователя */
        if (core_user::get_user_by_email($email)) {
            return false;
        }

        if ($remoteuser = self::get_remote_user($email)) {
            /* Создание пользователя */
            $localuser = new stdClass();
            $localuser->auth = 'billing';
            $localuser->email = $email;
            $localuser->mnethostid = $CFG->mnet_localhost_id;
            $localuser->secret = random_string(15);
            $localuser->username = $email;

            /* Поля профиля */
            $localuser->firstname = isset($remoteuser['profile']->firstname) ? $remoteuser['profile']->firstname : '';
            $localuser->lastname = isset($remoteuser['profile']->lastname) ? $remoteuser['profile']->lastname : '';

            /* Пароль аккаунта */
            $localuser->confirmed = 1;
            $localuser->password = '';

            return (bool) user_create_user($localuser, false, true);
        }

        return false;
    }

    /**
     * Получает данные пользователя из внешней системы.
     *
     * @param   string  $email  Электронный адрес
     * @return  array           Данные пользователя
     */
    private static function get_remote_user(string $email) {
        $config = get_config('auth_billing');
        $url = new moodle_url($config->host . $config->api . '/get_user');
        $param = array('email' => $email, 'token' => $config->token);
        return self::send_package($url, $param);
    }

    /**
     * Отправляет данные удалённому серверу.
     *
     * @param   moodle_url  $endpoint   Адрес сервера
     * @param   array       $param      Пакет данных
     * @return  array                   Полученный ответ
     */
    private static function send_package(moodle_url $endpoint, array $param) {
        $curl = new curl();
        $curl->setHeader(array('Content-Type: application/json'));
        $contents = $curl->post($endpoint, json_encode($param));
        $contents = json_decode($contents);

        if ($contents !== false) {
            return (array) $contents;
        }

        return array();
    }
}