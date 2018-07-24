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

require_once($CFG->dirroot . '/user/lib.php');
require_once($CFG->libdir . '/filelib.php');

/**
 * Класс для работы со внешней системой.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
class auth_billing {
    /**
     * Проверка работоспособности службы.
     *
     * @return  boolean Результат проверки
     */
    public static function check_service() {
        $result = self::run_method('test', array());

        if (isset($result['answer'])) {
            return (bool) $result['answer'];
        }

        return false;
    }

    /**
     * Проверка пользователя во внешней системе авторизации.
     *
     * @param   string  $email      Электронный адрес
     * @param   string  $password   Пароль пользователя
     * @return  boolean             Результат проверки
     */
    public static function check_user($email, $password) {
        $param = array('email' => $email, 'password' => $password);
        $result = self::run_method('authorization', $param);

        if (isset($result[0])) {
            return (bool) $result[0];
        }

        return false;
    }

    /**
     * Создание профиля пользователя из данных внешней системы.
     *
     * @param   string  $email  Электронный адрес
     * @return  array           Профиль пользователя
     */
    public static function create_profile($email) {
        global $CFG;

        $localuser = array();

        if ($remoteuser = self::get_remote_user($email)) {
            $localuser['auth'] = 'billing';
            $localuser['email'] = $email;
            $localuser['mnethostid'] = $CFG->mnet_localhost_id;
            $localuser['secret'] = random_string(15);

            /* Поля профиля */
            $localuser['firstname'] = isset($remoteuser['profile']->firstname) ? $remoteuser['profile']->firstname : '';
            $localuser['lastname'] = isset($remoteuser['profile']->lastname) ? $remoteuser['profile']->lastname : '';

            /* Пароль аккаунта */
            $localuser['confirmed'] = 1;
            $localuser['password'] = '';
        }

        return $localuser;
    }

    /**
     * Получение идентификатора внешнего пользователя.
     *
     * @param   string  $email  Электронный адрес
     * @return  string          Идентификатор пользователя
     */
    public static function get_id_user($email) {
        $result = '';

        /* Поиск данных в кэше */
        $cachename = md5('get_id_user' . $email);
        if ($result = self::get_cache($cachename)) {
            return $result;
        }

        if ($remoteuser = self::get_remote_user($email)) {
            self::set_cache($cachename, $remoteuser['_id']);
            $result = $remoteuser['_id'];
        }

        return $result;
    }

    /**
     * Получение информации о пользователе из внешней системы.
     *
     * @param   string  $email  Электронный адрес
     * @return  array           Данные пользователя
     */
    protected static function get_remote_user($email) {
        $param = array('email' => $email);
        return self::run_method('get_user_by_email', $param);
    }

    /**
     * Вызов метода с указанными параметрами.
     *
     * @param   string  $method Название метода
     * @param   array   $param  Параметры
     * @return  array           Результат
     */
    protected static function run_method($method, $param) {
        $config = get_config('auth_billing');

        $url = new moodle_url($config->host . $config->api . '/' . $method);
        $param = array_merge($param, array('token' => $config->token));

        $curl = new curl();
        $curl->setHeader(array('Content-Type: application/json'));
        $contents = $curl->post($url, json_encode($param));
        $contents = json_decode($contents);

        if ($contents !== false) {
            return (array) $contents;
        }

        return array();
    }

    /**
     * Получаем данные из кэша.
     *
     * @param   string  $key    Ключ
     * @return  string          Значение
     */
    private static function get_cache($key) {
        $cache = cache::make('auth_billing', 'defaults');
        return $cache->get($key);
    }

    /**
     * Сохраняем данные в кэш.
     *
     * @param   string  $key    Ключ
     * @param   string  $value  Значение
     * @return  boolean         Результат
     */
    private static function set_cache($key, $value) {
        $cache = cache::make('auth_billing', 'defaults');
        return $cache->set($key, $value);
    }
}