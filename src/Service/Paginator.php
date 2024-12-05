<?php

namespace App\Service;

use Doctrine\ORM\QueryBuilder;

class Paginator
{
    private QueryBuilder $baseQuery;
    private int $page;
    private int $pages;
    private int $limit = 10;

    public int $count;
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

        if ($this->page > $this->pages) {
            $this->page = 1;
        }

        $this->results = $this->fetchResults();
    }

    public function fetchResults()
    {
        return $this->baseQuery
            ->setMaxResults($this->limit)
            ->setFirstResult(($this->page - 1) * $this->limit)
            ->getQuery()
            ->getResult();
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