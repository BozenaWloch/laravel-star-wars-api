<?php
declare(strict_types=1);

namespace App\Policies;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthPolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    public function register(?User $user, Request $request): bool
    {
        return true;
    }

    public function login(?User $user, Request $request): bool
    {
        return true;
    }

    public function logout(User $user, Request $request): bool
    {
        return true;
    }

    public function resetPassword(?User $user, Request $request): bool
    {
        return true;
    }

    public function updatePassword(?User $user, Request $request): bool
    {
        return true;
    }
}
