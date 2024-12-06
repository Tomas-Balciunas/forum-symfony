<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;

class Paginator
{
    private const RANGE = 3;
    private const EDGE_PAGES = 2;

    public int $count;
    private QueryBuilder $baseQuery;
    private int $page;
    private int $pages;
    private int $limit = 10;
    private array $results = [];

    public function __construct(int $page, QueryBuilder $query)
    {
        $this->page = $page;
        $this->baseQuery = $query;
    }

    public function paginate(): void
    {
        $this->count = $this->countRows();
        $this->setPages();

        if ($this->page < 1 || $this->page > $this->pages) {
            $this->page = 1;
        }

        $this->results = $this->fetchResults();
    }

    private function countRows(): int
    {
        $countQuery = clone $this->baseQuery;

        return $countQuery
            ->select('COUNT(1)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function setPages(): void
    {
        $this->pages = ceil(($this->count / $this->limit));
    }

    public function fetchResults()
    {
        return $this->baseQuery
            ->setMaxResults($this->limit)
            ->setFirstResult(($this->page - 1) * $this->limit)
            ->getQuery()
            ->getResult();
    }

    public function getRange(): array
    {
        if ($this->pages <= self::EDGE_PAGES * 2) {
            return [];
        }

        if (self::EDGE_PAGES > self::RANGE) {
            if ($this->page <= (self::EDGE_PAGES - self::RANGE) || $this->page > $this->pages - self::EDGE_PAGES + self::RANGE) {
                return [];
            }
        }

        return range(
            ($this->hasPrecedingPages() ? $this->page - self::RANGE : self::EDGE_PAGES + 1),
            ($this->hasFollowupPages() ? $this->page + self::RANGE : $this->pages - self::EDGE_PAGES)
        );
    }

    public function hasPrecedingPages(): bool
    {
        return ($this->page - self::RANGE - self::EDGE_PAGES) > 1;
    }

    public function hasFollowupPages(): bool
    {
        return ($this->page + self::RANGE + self::EDGE_PAGES) < $this->pages;
    }

    public function getStartEdgeRange(): array
    {
        if ($this->pages < self::EDGE_PAGES) {
            return range(1, $this->pages);
        }

        return range(1, self::EDGE_PAGES);
    }

    public function getEndEdgeRange(): array
    {
        if ($this->pages <= self::EDGE_PAGES) {
            return [];
        }

        if ($this->pages < (self::EDGE_PAGES * 2)) {
            return range(self::EDGE_PAGES + 1, $this->pages);
        }

        return range(($this->pages - (self::EDGE_PAGES - 1)), $this->pages);
    }

    public function getPages(): int
    {
        return $this->pages;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getResults(): array
    {
        return $this->results;
    }

    public function hasPreviousPage(): bool
    {
        return $this->page > 1;
    }

    public function getPrevious(): int
    {
        if ($this->page > 1) {
            return $this->page - 1;
        }

        return 1;
    }

    public function hasNextPage(): bool
    {
        return $this->page < $this->pages;
    }

    public function getNext(): int
    {
        if ($this->page < $this->pages) {
            return $this->page + 1;
        }

        return $this->pages;
    }
}