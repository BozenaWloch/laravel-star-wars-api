<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class UserRepository
{
    public function model(): User
    {
        return new User();
    }

    public function getAll(): Collection
    {
        return $this->model()->all();
    }

    public function getByNickOrEmail(string $nickName): User
    {
        /** @var \App\Models\User $user */
        $user = $this->model()->query()->where('email', '=', $nickName)
            ->orWhere('nick_name', '=', $nickName)
            ->firstOrFail();

        return $user;
    }

    public function getById(int $userId): User
    {
        /** @var \App\Models\User $user */
        $user = $this->model()->query()->findOrFail($userId);

        return $user;
    }

    public function save(User $user): bool
    {
        return $user->save();
    }

    public function delete(User $user): ?bool
    {
        return $user->delete();
    }
}
