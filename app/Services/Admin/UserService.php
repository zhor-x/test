<?php

namespace App\Services\Admin;

use App\DTO\Admin\ListDTO;
use App\DTO\Admin\UserDTO;
use App\Mail\PasswordMail;
use App\Repositories\Admin\Users\UserRepositoryInterface;
use App\Traits\AuthTrait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;
use function Symfony\Component\String\u;

readonly class UserService
{

    use AuthTrait;

    public function __construct(private UserRepositoryInterface $repository)
    {
    }

    public function getPagination(ListDTO $payload): Collection|LengthAwarePaginator
    {
        return $this->repository->getPagination($payload);
    }

    public function getById(int $groupId)
    {
        $question = $this->repository->getById($groupId);

        $question->load('answers.translation');

        return $question;
    }

    public function store(UserDTO $payload)
    {
        $user = $this->repository->store($payload);
        $token = $this->resetPasswordToken($user->email);
        Mail::to($user->email)->send(new PasswordMail($user, $token));

        return $user;
    }


    public function update(UserDTO $payload, int $userId): void
    {
        $this->repository->update($payload, $userId);
    }

    public function destroy(int $userId): void
    {
        $this->repository->destroy($userId);
    }

    public function groupUserList(ListDTO $payload): Collection|\Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return $this->repository->groupUserList($payload);
    }

    public function results(int $id)
    {
       $user =  $this->repository->getById($id);
       $user->load('userExamsTest.questions');
       return $user;
    }
}
