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

* Запустить внешнюю службу в режиме тестирования:
```bash
docker run --detach \
    --env "MONGO_URL=$(printenv MONGO_URL)" \
    --env "NODE_ENV=development" \
    --env "ROOT_URL=http://localhost:3000" \
    --env "TOKEN=test" \
    --publish "3000:3000" \
    --restart=always
    registry.styleschool.ru/nodejs/api-service
```

* Тестирование плагина:
```bash
vendor/bin/phpunit --testdox --testsuite=auth_billing_testsuite
```
