<?php
declare(strict_types=1);

namespace App\Http\Requests;

use App\Exceptions\ForbiddenException;
use Exception;
use Illuminate\Foundation\Http\FormRequest;

class Request extends FormRequest
{
    public function authorize(): bool
    {
        /** @var \Illuminate\Routing\Route $route */
        $route = $this->route();

        $action = \explode('@', $route->getActionName());
        $controller = $action[0];
        $ability = $action[1];

        try {
            return policy($controller)->{$ability}($this->user(), $this);
        } catch (Exception $exception) {
            throw new ForbiddenException();
        }
    }

    public function rules(): array
    {
        return [];
    }
}
