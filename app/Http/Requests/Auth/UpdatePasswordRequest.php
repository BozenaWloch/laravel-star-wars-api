<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class UpdatePasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'password' => [
                'required',
                'min:8',
                'confirmed',
                'regex:/^.*(?=.{1,})(?=.*[A-Z])(?=.{1,})(?=.*[a-z])(?=.*[0-9])(?=.*[\d\x])(?=.*[!$#%]).*$/',
            ],
            'token' => [
                'required',
                'string',
                'min:3',
                'max:255',
            ],
        ];
    }
}
