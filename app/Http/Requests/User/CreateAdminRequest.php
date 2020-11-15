<?php
declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule as ValidationRule;

class CreateAdminRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'email' => [
                'required_without:nick_name',
                'email',
                ValidationRule::unique('users', 'email'),
            ],
            'nick_name' => [
                'required_without:email',
                'string',
                'min:3',
                'max:255',
                ValidationRule::unique('users', 'nick_name'),
            ],
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^.*(?=.{1,})(?=.*[A-Z])(?=.{1,})(?=.*[a-z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            ],
            'first_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
            'last_name' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
        ];
    }
}
