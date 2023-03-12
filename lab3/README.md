# Пример: простейший сервис для хранения статей внутренней Wiki

Пример для курса "Базы данных", сделан на PHP8 и MySQL8, проверен на Linux.

В этом примере есть:

1. Работа с транзакциями (см. классы `CourseService` и `Synchronization`)
2. INSERT ODKU, также известный как UPSERT (см. классы `CourseRepository`, `TagRepository`)
3. Применение оптимистичной блокировки (см. класс `CourseRepository`)

## Схема БД

![Schema](docs/wiki-backend-schema.png)

## Запуск примера на Linux

Краткий план действий:

1. Установить docker и docker-compose
2. Запустить контейнеры по инструкции из файла `docs/docker.md`
3. Открыть в MySQL Workbench и выполнить последовательно SQL запросы из двух файлов:
   - `data/create_user.sql`
   - `data/init.sql`
4. Открыть в браузере http://localhost

В идеале у вас:
- По адресу http://localhost/courses/list отдаётся пустой JSON массив
- При включении XDebug отладка работает

## Запуск примера в Windows

Краткий план действий:

1. Установить MySQL 8 и PHP 8.2
2. Запустить MySQL server
3. Открыть в MySQL Workbench и выполнить последовательно SQL запросы из двух файлов:
   - `data/create_user.sql`
   - `data/init.sql`
4. Открыть консоль и запустить отладочный сервер PHP:
    - `cd public`
    - `php -S localhost:8888`
5. Открыть в браузере `http://localhost:8888/courses/list` - будет пустой JSON массив

## Проверка работы

Проверить работу можно с помощью Postman. Готовые запросы можно импортировать в Postman из файла `docs/Postman.json`.
