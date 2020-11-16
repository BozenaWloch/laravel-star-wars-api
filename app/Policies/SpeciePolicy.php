<?php
declare(strict_types=1);

namespace App\Policies;

use App\Http\Requests\Request;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Auth\Access\HandlesAuthorization;

class SpeciePolicy extends AbstractPolicy
{
    use HandlesAuthorization;

    /**
     * @var \App\Repositories\UserRepository
     */
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function list(User $user, Request $request): bool
    {
        return $this->isOwner($user, $request);
    }

    public function read(User $user, Request $request): bool
    {
        return $this->isOwner($user, $request);
    }

    protected function isOwner(User $user, Request $request): bool
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $request->route();

        return (int) $route->parameter('userId') === $user->id;
    }
}
