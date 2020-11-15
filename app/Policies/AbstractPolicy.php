<?php
declare(strict_types=1);

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class AbstractPolicy
{
    public function isAdmin(User $user): bool
    {
        return Role::ROLE_ADMIN_ID === $user->role;
    }
}
