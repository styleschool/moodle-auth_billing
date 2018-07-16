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
 * Тестирование API библиотеки.
 *
 * @package     auth_billing
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

global $CFG;

require_once(__DIR__ . '/../lib.php');

/**
 * Тестирование класса 'auth_billing'.
 *
 * @category    phpunit
 * @copyright   2018 "Valentin Popov" <info@valentineus.link>
 * @license     http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @package     auth_billing
 */
class auth_billing_api_testcase extends advanced_testcase {
    /**
     * Почта проверочного пользователя.
     *
     * @var string  $email
     */
    protected static $email = 'example@domain.org';

    /**
     * Имя проверочного пользователя.
     *
     * @var string  $firstname
     */
    protected static $firstname = 'Ada';

    /**
     * Фамилия проверочного пользователя.
     *
     * @var string  $lastname
     */
    protected static $lastname = 'Lovelace';

    /**
     * Пароль проверочного пользователя.
     *
     * @var string  $password
     */
    protected static $password = 'qwerty123456';

    /**
     * Настройка плагина перед тестированием.
     */
    public function setUp() {
        $this->resetAfterTest(true);

        /* Настройка плагина */
        set_config('api', '/v1', 'auth_billing');
        set_config('host', 'http://localhost:3000', 'auth_billing');
        set_config('token', 'test', 'auth_billing');
    }

    /**
     * @testdox Проверка доступности сервиса
     */
    public function test_check_service() {
        $result = auth_billing::check_service();
        $this->assertTrue($result);
    }

    /**
     * @testdox Проверка неверного пользователя
     */
    public function test_check_invalid_user() {
        $result = auth_billing::check_user(self::$email, random_string(15));
        $this->assertFalse($result);
    }

    /**
     * @testdox Проверка корректного пользователя
     */
    public function test_check_valid_user() {
        $result = auth_billing::check_user(self::$email, self::$password);
        $this->assertTrue($result);
    }

    /**
     * @testdox Создание неверного пользователя
     */
    public function test_create_invalid_user() {
        $result = auth_billing::create_user(random_string(15));
        $this->assertFalse($result);
    }

    /**
     * @testdox Создание нового пользователя
     */
    public function test_create_new_user() {
        /* Создание пользователя */
        $result = auth_billing::create_user(self::$email);
        $this->assertTrue($result);

        /* Попытка повторного создания пользователя */
        $result = auth_billing::create_user(self::$email);
        $this->assertFalse($result);

        /* Проверка созданного пользователя */
        $user = core_user::get_user_by_email(self::$email);
        $this->assertEmpty($user->password);
        $this->assertEquals($user->auth, 'billing');
        $this->assertEquals($user->confirmed, '1');
        $this->assertEquals($user->email, self::$email);
        $this->assertEquals($user->firstname, self::$firstname);
        $this->assertEquals($user->lastname, self::$lastname);
        $this->assertEquals($user->username, self::$email);
    }
}