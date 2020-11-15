<?php
declare(strict_types=1);

namespace App\Http\Requests\Auth;

use App\Http\Requests\Request;

class ResetPasswordRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'nick_name' => [
                'string',
                'min:3',
                'max:255',
            ],
        ];
    }
}
