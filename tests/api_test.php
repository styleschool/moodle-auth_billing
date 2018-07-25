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

require_once($CFG->dirroot . '/auth/billing/lib.php');

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
    protected $email = 'example@domain.org';

    /**
     * Имя проверочного пользователя.
     *
     * @var string  $firstname
     */
    protected $firstname = 'Ada';

    /**
     * Фамилия проверочного пользователя.
     *
     * @var string  $lastname
     */
    protected $lastname = 'Lovelace';

    /**
     * Пароль проверочного пользователя.
     *
     * @var string  $password
     */
    protected $password = 'qwerty123456';

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
        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
    }

    /**
     * @testdox Проверка неверного пользователя
     */
    public function test_check_invalid_user() {
        $result = auth_billing::check_user($this->$email, random_string(15));
        $this->assertInternalType('boolean', $result);
        $this->assertFalse($result);
    }

    /**
     * @testdox Проверка корректного пользователя
     */
    public function test_check_valid_user() {
        $result = auth_billing::check_user($this->$email, $this->$password);
        $this->assertInternalType('boolean', $result);
        $this->assertTrue($result);
    }

    /**
     * @testdox Генерация профиля неверного пользователя
     */
    public function test_create_profile_invalid_user() {
        $result = auth_billing::create_profile(random_string(15));
        $this->assertInternalType('array', $result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox Генерация профиля верного пользователя
     */
    public function test_create_profile_new_user() {
        $result = auth_billing::create_profile($this->$email);
        $this->assertInternalType('array', $result);
        $this->assertNotEmpty($result);

        /* Проверка полей */
        $this->assertEquals('billing', $result['auth']);
        $this->assertEquals($this->$email, $result['email']);
        $this->assertEquals($this->$firstname, $result['firstname']);
        $this->assertEquals($this->$lastname, $result['lastname']);

        /* Проверка пароля */
        $this->assertInternalType('string', $result['password']);
        $this->assertEmpty($result['password']);
    }

    /**
     * @testdox Получение идентификатора некорректного пользователя
     */
    public function test_get_id_invalid_user() {
        $result = auth_billing::get_id_user(random_string(15));
        $this->assertInternalType('string', $result);
        $this->assertEmpty($result);
    }

    /**
     * @testdox Получение идентификатора корректного пользователя
     */
    public function test_get_id_valid_user() {
        $result = auth_billing::get_id_user($this->$email);
        $this->assertInternalType('string', $result);
        $this->assertNotEmpty($result);
    }
}