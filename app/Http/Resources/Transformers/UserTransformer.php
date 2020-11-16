<?php
declare(strict_types=1);

namespace App\Http\Resources\Transformers;

use App\Models\User;

class UserTransformer extends AbstractTransformer
{
    /**
     * @var string[]
     */
    protected $availableIncludes = [
    ];

    public function transform(User $user)
    {
        return [
            'id'          => $user->id,
            'first_name'  => $user->first_name,
            'last_name'   => $user->last_name,
            'nick_name'   => $user->nick_name,
            'email'       => $user->email,
            'role'        => $user->role,
            'external_id' => $user->external_id,
            'is_blocked'  => $user->is_blocked,
            'created_at'  => $this->formatDate($user->created_at),
            'updated_at'  => $this->formatDate($user->updated_at),
        ];
    }
}
