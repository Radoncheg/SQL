<?php
declare(strict_types=1);

namespace App\Common\Database;

final class ConnectionProvider
{
    public static function getConnection(): Connection
    {
        static $connection = null;
        if ($connection === null)
        {
            // TODO: Добавить поддержку .env, чтобы упростить запуск примера в Windows
            $dsn = 'mysql:dbname=npz;host=127.0.0.1';
            $user = 'root';
            $password = '';
            $connection = new Connection($dsn, $user, $password);
        }
        return $connection;
    }
}
