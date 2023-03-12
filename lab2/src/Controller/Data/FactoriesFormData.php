<?php
declare(strict_types=1);

namespace App\Controller\Data;

class FactoriesFormData
{
    private const DEFAULT_SEARCH_QUERY = 'f.id';
    private const FILTER_BY_STATUS = 'filter_by_status';
    private const FILTER_BY_DEPTH ='filter_by_depth';
    private const FILTER_BY_PRODUCT ='filter_by_product';
    private const SEARCH_QUERY ='search_query';
    private const SORT_BY_FIELD ='sort_by_field';
    private const SORT_ASCENDING ='sort_ascending';
    private const PAGE_NO ='page_no';

    public function __construct(
        private ?string $filterByStatus,
        private ?string $filterByDepth,
        private ?string $filterByProduct,
        private string $searchQuery,
        private string $sortByField,
        private bool $sortAscending,
        private int $pageNo,
    ) {}

    public function toArray(): array
    {
        return [
            self::FILTER_BY_STATUS => $this->filterByStatus,
            self::FILTER_BY_DEPTH => $this->filterByDepth,
            self::FILTER_BY_PRODUCT => $this->filterByProduct,
            self::SEARCH_QUERY => $this->searchQuery,
            self::SORT_BY_FIELD => $this->sortByField,
            self::SORT_ASCENDING => $this->sortAscending,
            self::PAGE_NO> $this->pageNo
        ];
    }

    public static function fromArray(array $parameters): self
    {
        return new self(
            $parameters[self::FILTER_BY_STATUS] ?: null,
            $parameters[self::FILTER_BY_DEPTH] ?: null,
            $parameters[self::FILTER_BY_PRODUCT] ?: null,
            $parameters[self::SEARCH_QUERY] ?? '',
            $parameters[self::SORT_BY_FIELD] ? : self::DEFAULT_SEARCH_QUERY,
            $parameters[self::SORT_ASCENDING] === 'true',
            max((int) $parameters[self::PAGE_NO], 1)
        );
    }

    public function getFilterByStatus(): ?string
    {
        return $this->filterByStatus;
    }

    public function getFilterByDepth(): ?string
    {
        return $this->filterByDepth;
    }

    public function getFilterByProduct(): ?string
    {
        return $this->filterByProduct;
    }

    public function getSearchQuery(): string
    {
        return $this->searchQuery;
    }

    public function getSortByField(): string
    {
        return $this->sortByField;
    }

    public function isSortAscending(): bool
    {
        return $this->sortAscending;
    }

    public function getPageNo(): int
    {
        return $this->pageNo;
    }
}
