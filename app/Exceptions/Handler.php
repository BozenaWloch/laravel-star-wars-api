<?php
declare(strict_types=1);

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {
    }

    public function render($request, Throwable $exception)
    {
        $code = $exception->getCode();
        $messages[] = $exception->getMessage();

        switch (\get_class($exception)) {
            case ModelNotFoundException::class:
                $code = JsonResponse::HTTP_NOT_FOUND;

                break;
            case ValidationException::class:
                $messages = $exception->errors();

                break;
            case AuthenticationException::class:
            case AuthorizationException::class:
                $code = JsonResponse::HTTP_UNAUTHORIZED;

                break;
        }

        0 === $code ? $code = JsonResponse::HTTP_BAD_REQUEST : $code;

        $errorResponse = [
            'httpCode' => $code,
            'errors'   => $messages,
        ];

        return new JsonResponse($errorResponse, $code);
    }
}
