<?php

namespace App\DTO\Admin;



readonly class UserGroupDTO
{
    public function __construct(
        private string $title,
        private array  $users,
    )
    {

    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUsers(): array
    {
        return $this->users;
    }
}
