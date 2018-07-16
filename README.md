# Плагин авторизации Billing

## Development

* Установить и настроить Moodle:
[Подробнее](https://download.moodle.org/).

* Установить систему тестирования:
[Подробнее](https://docs.moodle.org/dev/PHPUnit).

* Клонировать репозиторий, находясь в корневом каталоге Moodle:
```bash
git clone git@git.styleschool.ru:moodle/auth_billing.git auth/billing
```

* Установить зависимости:
```bash
composer install
```

* Тестирование плагина:
```bash
vendor/bin/phpunit --testsuite=auth_billing_testsuite
```
