<?php
declare(strict_types=1);

namespace App\Http\Requests\Hydrators;

use App\Http\Requests\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserHydrator
{
    public function hydrate(User $user, Request $request): User
    {
        $this->setFirstName($user, $request);
        $this->setLastName($user, $request);
        $this->setNickName($user, $request);
        $this->setEmail($user, $request);
        $this->setPassword($user, $request);
        $this->setIsBlocked($user, $request);

        return $user;
    }

    private function setFirstName(User $user, Request $request): void
    {
        if ($request->has('first_name')) {
            $user->first_name = $request->get('first_name');
        }
    }

    private function setLastName(User $user, Request $request): void
    {
        if ($request->has('last_name')) {
            $user->last_name = $request->get('last_name');
        }
    }

    private function setNickName(User $user, Request $request): void
    {
        if ($request->has('nick_name') && null !== $request->get('nick_name')) {
            $user->nick_name = \trim($request->get('nick_name'), ' ');
        }
    }

    private function setEmail(User $user, Request $request): void
    {
        if ($request->has('email') && null !== $request->get('email')) {
            $user->email = \trim($request->get('email'), ' ');
        }
    }

    private function setPassword(User $user, Request $request): void
    {
        if ($request->has('password') && null !== $request->get('password')) {
            $user->password = Hash::make($request->get('password'));
        }
    }

    private function setIsBlocked(User $user, Request $request): void
    {
        if ($request->has('is_blocked') && null !== $request->get('is_blocked')) {
            $user->is_blocked = $request->get('is_blocked');
        }
    }
}
