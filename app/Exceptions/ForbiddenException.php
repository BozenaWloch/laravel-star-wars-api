<?php
declare(strict_types=1);

namespace App\Exceptions;

use Exception;
use Illuminate\Http\JsonResponse;

class ForbiddenException extends Exception
{
    public function __construct($message = 'Forbidden', $code = JsonResponse::HTTP_FORBIDDEN)
    {
        parent::__construct($message, $code);
    }
}
