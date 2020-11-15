<?php
declare(strict_types=1);

namespace App\Repositories;

use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Str;

class PasswordResetRepository
{
    public function model(): PasswordReset
    {
        return new PasswordReset();
    }

    public function create(User $user): PasswordReset
    {
        /** @var \App\Models\PasswordReset $passwordReset */
        $passwordReset = $this->model()->query()->create([
            'email' => $user->email ?? $user->nick_name,
            'token' => $this->generateToken($user->id),
        ]);

        return $passwordReset;
    }

    public function getActiveByToken(string $token): PasswordReset
    {
        /** @var \App\Models\PasswordReset $passwordReset */
        $passwordReset = $this->model()->query()
            ->where('token', '=', $token)
            ->where('created_at', '>=', Carbon::now()->subHour())
            ->firstOrFail();

        return $passwordReset;
    }

    private function generateToken(int $userId): string
    {
        return \sprintf('%s-%s-%s', $userId, Str::random(16), now()->timestamp);
    }
}
