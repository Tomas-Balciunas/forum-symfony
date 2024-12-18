<?php

namespace App\Service\Misc;

use App\Data\Config;
use Doctrine\ORM\QueryBuilder;

class Paginator
{
    public int $count;
    private int $range = Config::PAGE_RANGE;
    private int $edges = Config::PAGE_EDGES;
    private QueryBuilder $countQuery;
    private QueryBuilder $fullQuery;
    private int $page;
    private int $pages;
    private int $limit;
    private array $results = [];

    public function __construct(
        int          $page,
        QueryBuilder $query,
        QueryBuilder $countQuery,
        int          $limit = Config::PAGE_SIZE,
        int          $edges = Config::PAGE_EDGES,
        int          $range = Config::PAGE_RANGE,
    )
    {
        $this->page = $page;
        $this->countQuery = $countQuery;
        $this->fullQuery = $query;
        $this->limit = $limit;
        $this->edges = $edges;
        $this->range = $range;
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
        return $this->countQuery
            ->getQuery()
            ->getSingleScalarResult();
    }

    private function setPages(): void
    {
        $this->pages = ceil(($this->count / $this->limit));
    }

    public function fetchResults()
    {
        return $this->fullQuery
            ->setMaxResults($this->limit)
            ->setFirstResult(($this->page - 1) * $this->limit)
            ->getQuery()
            ->getResult();
    }

    public function getRange(): array
    {
        if ($this->pages <= $this->edges * 2) {
            return [];
        }

        if ($this->edges > $this->range) {
            if ($this->page <= ($this->edges - $this->range) || $this->page > $this->pages - $this->edges + $this->range) {
                return [];
            }
        }

        return range(
            ($this->hasPrecedingPages() ? $this->page - $this->range : $this->edges + 1),
            ($this->hasFollowupPages() ? $this->page + $this->range : $this->pages - $this->edges)
        );
    }

    public function hasPrecedingPages(): bool
    {
        return ($this->page - $this->range - $this->edges) > 1;
    }

    public function hasFollowupPages(): bool
    {
        return ($this->page + $this->range + $this->edges) < $this->pages;
    }

    public function getStartEdgeRange(): array
    {
        if ($this->pages < $this->edges) {
            return range(1, $this->pages);
        }

        return range(1, $this->edges);
    }

    public function getEndEdgeRange(): array
    {
        if ($this->pages <= $this->edges) {
            return [];
        }

        if ($this->pages < ($this->edges * 2)) {
            return range($this->edges + 1, $this->pages);
        }

        return range(($this->pages - ($this->edges - 1)), $this->pages);
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

    public function getPostIndex(int $key): int
    {
        return ($key + 1) + ($this->page - 1) * $this->limit;
    }
}