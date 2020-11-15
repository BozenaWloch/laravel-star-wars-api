<?php
declare(strict_types=1);

namespace App\Http\Requests\User;

use App\Http\Requests\Request;
use Illuminate\Validation\Rule;

class ReadRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return [
            'user_id' => [
                'required',
                Rule::exists('users', 'id'),
            ],
        ];
    }

    public function prepareForValidation(): void
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $this->route();

        $this->merge([
            'user_id' => $route->parameter('userId'),
        ]);
    }
}
