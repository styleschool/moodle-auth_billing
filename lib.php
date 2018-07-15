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
 * Отправляет данные удалённому серверу.
 *
 * @param   moodle_url  $endpoint   Адрес сервера
 * @param   array       $parameters Пакет данных
 * @return  array                   Полученный ответ
 */
function auth_billing_send_package(moodle_url $endpoint, array $parameters) {
    $curl = new curl();
    $result = array();

    $curl->setHeader(array('Content-Type: application/json'));
    $contents = $curl->post($endpoint, json_encode($parameters));
    $response = $curl->getResponse();

    $result['contents'] = (array) json_decode($contents);
    $result['response'] = (array) $response;
    return $result;
}