<?php
declare(strict_types=1);

namespace App\Data;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;

class FactoryDataSource
{
    private Connection $connection;

    public function __construct()
    {
        $this->connection = ConnectionProvider::getConnection();
    }

    /**
     * @return FactoryData[]
     */
    public function listFactories(ListFactoriesParams $params): array
    {
        $queryParams = [];
        $query = $this->buildSqlQuery($params, $queryParams);

        $stmt = $this->connection->execute($query, $queryParams);
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_map(fn($row) => $this->hydrateData($row), $rows);
    }

    /**
     * @return array<string,string> - отображает ID на название
     */
    public function listFactoryStatusSelectOptions(): array
    {
        $stmt = $this->connection->execute(<<<SQL
            SELECT
              id,
              status_text
            FROM status
            SQL
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_combine(
            array_column($rows, 'id'),
            array_column($rows, 'status_text')
        );
    }

    public function getPageCount(ListFactoriesParams $params, int $pageSize): int
    {
        $queryParams = [];
        $whereConditionsStr = $this->getWhereConditionsStr($params, $queryParams);

        $stmt = $this->connection->execute(<<<SQL
            SELECT COUNT(*)
            FROM (
                SELECT
                  f.id
                FROM factory f
                  INNER JOIN status s ON f.status_id = s.id
                  INNER JOIN production p ON f.id = p.factory_id
                WHERE {$whereConditionsStr}
                GROUP BY f.id
            ) t
            SQL, $queryParams
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_COLUMN);
        $rowsCount = (int)($rows[0] ?? 0);
        $pageCount = ceil($rowsCount/$pageSize);
        echo ('total ' . $rowsCount);
        return (int) $pageCount;
    }


    /**
     * @return array<string,string> - отображает ID на название
     */
    public function listDepthSelectOptions(): array
    {
        $stmt = $this->connection->execute(<<<SQL
            SELECT DISTINCT 
              processing_depth
            FROM factory 
            ORDER BY processing_depth 
            SQL
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_column($rows, 'processing_depth');
    }

    /**
     * @return array<string,string> - отображает ID на название
     */
    public function listProductSelectOptions(): array
    {
        $stmt = $this->connection->execute(<<<SQL
            SELECT DISTINCT 
              production
            FROM production
            ORDER BY production
            SQL
        );
        $rows = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return array_column($rows, 'production');
    }

    /**
     * @param string[] $row
     * @return FactoryData
     */
    private function hydrateData(array $row): FactoryData
    {
        try
        {
            return new FactoryData(
                $row['full_name'],
                $row['short_name'],
                $row['legal_address'],
                $row['actual_address'],
                $row['processing_depth'],
                $row['register_info'],
                $row['status_text'],
                json_decode($row['products'], true, 512, JSON_THROW_ON_ERROR)
            );
        }
        catch (\Exception $e)
        {
            throw new \RuntimeException($e->getMessage(), $e->getCode(), $e);
        }
    }

    private function buildSqlQuery(ListFactoriesParams $params, array &$queryParams): string
    {
        $isSortAscending = $params->isSortAscending() ? 'ASC' : 'DESC';
        $sortByField = $params->getSortByField();
        $pageSize = $params->getPageSize();
        $pageNo = $params->getPageNo();

        $whereConditionsStr = $this->getWhereConditionsStr($params, $queryParams);
        $offset = $pageSize * ($pageNo - 1);

        return <<<SQL
        SELECT
          f.full_name,
          f.short_name,
          f.legal_address,
          f.actual_address,
          f.processing_depth,
          f.register_info,
          s.status_text,
          JSON_ARRAYAGG(p.production) AS products
        FROM factory f
          INNER JOIN status s ON f.status_id = s.id
          INNER JOIN production p ON f.id = p.factory_id
        WHERE {$whereConditionsStr}
         GROUP BY f.id
         ORDER BY {$sortByField} {$isSortAscending}
         LIMIT {$pageSize} OFFSET {$offset} 
        SQL;
    }

    private function buildFilterWhereCondition(FactoryFilter $filter, array &$queryParams): string
    {
        switch ($filter->getFilterByField())
        {
            case FactoryFilter::FILTER_BY_STATUS:
                $queryParams[] = $filter->getValue();
                return 'f.status_id = ?';
            case FactoryFilter::FILTER_BY_DEPTH:
                $queryParams[] = $filter->getValue();
                return 'f.processing_depth = ?';
            case FactoryFilter::FILTER_BY_PRODUCT:
                $queryParams[] = $filter->getValue();
                return 'p.production = ?';
            default:
                throw new \RuntimeException("Filtering is not implemented for field {$filter->getFilterByField()}");
        }
    }

    private function getWhereConditionsStr(ListFactoriesParams $params,  array &$queryParams): string
    {
        $whereConditions = [];
        foreach ($params->getFilters() as $filter)
        {
            $whereConditions[] = $this->buildFilterWhereCondition($filter, $queryParams);
        }
        $searchQuery = $params->getSearchQuery();

        $whereConditionsStr = '';
        if ($searchQuery !== '')
        {
            $searchQuery = "%" . $searchQuery . "%";
            $index = count($queryParams);
            $queryParams = array_fill($index,8, $searchQuery);
            $whereConditionsStr = "f.full_name LIKE ? OR
                                 f.short_name LIKE ? OR
                                 f.legal_address LIKE ? OR
                                 f.actual_address LIKE ? OR
                                 f.register_info LIKE ? OR
                                 f.processing_depth LIKE ? OR
                                 s.status_text LIKE ? OR
                                 p.production LIKE ?";
        }

        if (count($whereConditions) > 0)
        {
            if ($whereConditionsStr !== '')
            {
                $whereConditionsStr .= ' AND ';
            }
            $whereConditionsStr .= implode(' AND ', $whereConditions);
        }
        else
        {
            if ($whereConditionsStr === '')
            {
                $whereConditionsStr = 'TRUE';
            }
        }

        return $whereConditionsStr;
    }
}
