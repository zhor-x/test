<?php

namespace App\DTO\Admin;


use App\Enums\UserRole;

readonly class UserDTO
{
    public function __construct(
        private string   $name,
        private string   $email,
        private string   $phone,
        private UserRole $role,
    )
    {

    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPhone(): string
    {
        return $this->phone;
    }

    public function getRole(): UserRole
    {
        return $this->role;
    }

}
