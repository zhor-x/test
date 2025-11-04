<?php

namespace App\DTO\Admin;


readonly class ListDTO
{
    public function __construct(
        private int|string $limit,
        private ?string    $query,
        private ?string    $orderBy,
        private ?string    $order
    )
    {
    }


    public function getQuery(): ?string
    {
        return $this->query;
    }

    public function getLimit(): int|string
    {
        return $this->limit;
    }
    

    public function getOrderBy(): ?string
    {
        return $this->orderBy;
    }

    public function getOrder(): ?string
    {
        return $this->order;
    }
}
