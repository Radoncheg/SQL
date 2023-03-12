<?php
declare(strict_types=1);

namespace App\Common\Database;

class Synchronization
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * Метод выполняет переданную функцию внутри открытой транзакции, в конце вызывая COMMIT либо ROLLBACK.
     *
     * @param \Closure $action
     * @return mixed|void
     * @throws null
     */
    public function doWithTransaction(\Closure $action)
    {
        $this->connection->beginTransaction();
        try
        {
            $result = $action();
            $this->connection->commit();
            return $result;
        }
        catch (\Throwable $exception)
        {
            $this->connection->rollBack();
            throw $exception;
        }
    }
}
