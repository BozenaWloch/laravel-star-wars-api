<?php
declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule as ValidationRule;

class UpdateRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $this->route();
        $userId = $route->parameter('userId');

        return [
            'email' => [
                'email',
                ValidationRule::unique('users', 'email')->ignore($userId),
            ],
            'nick_name' => [
                'string',
                'min:3',
                'max:255',
                ValidationRule::unique('users', 'nick_name')->ignore($userId),
            ],
            'first_name' => [
                'string',
                'min:3',
                'max:255',
            ],
            'last_name' => [
                'string',
                'min:3',
                'max:255',
            ],
            'password' => [
                'min:8',
                'confirmed',
                'regex:/^.*(?=.{1,})(?=.*[A-Z])(?=.{1,})(?=.*[a-z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            ],
        ];
    }
}
