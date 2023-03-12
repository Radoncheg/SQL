# Установка Docker и docker-compose

## Установка в Ubuntu Linux

Официальные инструкции:

- Docker CE для Ubuntu: https://docs.docker.com/engine/install/ubuntu/#install-docker-ce
- Docker Compose V1: https://docs.docker.com/compose/install/other/

Рекомендуется добавить текущего пользователя в группы docker и www-data, чтобы:

1. Использовать команду `docker` без `sudo`
2. Предоставлять доступ к файлам проекта пользователю `www-data` в docker-контейнере

Добавление в группы:

```bash
sudo usermod -a -G docker $USER
sudo usermod -a -G www-data $USER
```

Эффект наступит после Sign Out / Sign In либо перезагрузки.

## Использование

```bash
# Собрать образ (выполняется один раз)
docker-compose build

# Запустить контейнеры в фоновом режиме
docker-compose up -d

# Проверить состояние контейнеров
docker-compose ps

# Смотреть логи контейнеров (Ctrl+C для остановки)
docker-compose logs -f

# Открыть сессию bash в контейнере
docker/bin/ui-table-app-bash

# Чистка кеша Twig
bin/cleanup-cache

# Остановить контейнеры
docker-compose down --remove-orphans
```

Чистка данных:

```bash
# УДАЛИТЬ ВСЕ ДАННЫЕ локальной базы данных (находятся в docker volume)
docker volume rm 2_ui_table_ui_table_db_data
```

## Использование XDebug

Для отладки используйте XDebug:

1. Docker-контейнеры настраивать не надо, в них xdebug уже включён и настроен на подключение к локальному хосту
2. Для браузера установите расширение XDebug Helper
    - Chrome: https://chrome.google.com/webstore/detail/xdebug-helper/eadndfjplgieldjbigjakmdgkmoaaaoc
    - Firefox: https://addons.mozilla.org/en-US/firefox/addon/xdebug-helper-for-firefox/
3. В своей IDE настройте работу с XDebug, используя любую инструкцию для docker-контейнеров
    - Для PhpStorm раздел "PHP > Servers" уже настроен как полагается
