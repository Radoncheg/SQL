<?php
declare(strict_types=1);

namespace App\Data;

class ListFactoriesParams
{
    public const SORT_BY_STATUS = 's.status_text';
    public const SORT_BY_DEPTH = 'f.processing_depth';
    public const SORT_BY_FULL_NAME = 'f.full_name';
    public const SORT_BY_SHORT_NAME = 'f.short_name';
    public const SORT_BY_ID = 'f.id';

    private const ALL_SORT_BY = [
        self::SORT_BY_STATUS,
        self::SORT_BY_DEPTH,
        self::SORT_BY_FULL_NAME,
        self::SORT_BY_SHORT_NAME,
        self::SORT_BY_ID,
    ];

    private string $searchQuery;
    /** @var FactoryFilter[] */
    private array $filters;
    private string $sortByField;
    private bool $sortAscending;
    private int $pageSize;
    private int $pageNo;

    /**
     * @param string $searchQuery
     * @param FactoryFilter[] $filters
     * @param string $sortByField
     * @param bool $sortAscending
     * @param int $pageSize
     * @param int $pageNo
     */
    public function __construct(
        string $searchQuery,
        array $filters,
        string $sortByField,
        bool $sortAscending,
        int $pageSize,
        int $pageNo
    )
    {
        if ($pageSize <= 0)
        {
            throw new \InvalidArgumentException("List page size must be positive number, got $pageSize");
        }
        if ($pageNo < 1)
        {
            throw new \InvalidArgumentException("List page number must be positive number, got $pageNo");
        }
        if (!in_array($sortByField, self::ALL_SORT_BY, true))
        {
            throw new \InvalidArgumentException("List cannot be sorted by field '$sortByField'");
        }

        $this->searchQuery = $searchQuery;
        $this->filters = $filters;
        $this->sortByField = $sortByField;
        $this->sortAscending = $sortAscending;
        $this->pageSize = $pageSize;
        $this->pageNo = $pageNo;
    }

    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    /**
     * @return FactoryFilter[]
     */
    public function getFilters(): array
    {
        return $this->filters;
    }

    public function getSortByField(): string
    {
        return $this->sortByField;
    }

    public function isSortAscending(): bool
    {
        return $this->sortAscending;
    }

    public function getPageSize(): int
    {
        return $this->pageSize;
    }

    public function getPageNo(): int
    {
        return $this->pageNo;
    }
}
