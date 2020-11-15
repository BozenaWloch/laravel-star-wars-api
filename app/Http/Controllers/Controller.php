<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;

/**
 * @OA\Info(title="LaravelBasicAPI", version="1.0"),
 * @OA\SecurityScheme(
 *     type="http",
 *     description="Login with email and password to get the authentication token",
 *     name="Token",
 *     in="header",
 *     scheme="bearer",
 *     bearerFormat="apitoken",
 *     securityScheme="apiAuth",
 * )
 * @OA\Schema(
 *  schema="Pagination",
 *  @OA\Property(property="pagination", type="object",
 *     @OA\Property(property="total", type="integer", example="1"),
 *     @OA\Property(property="count", type="integer", example="1"),
 *     @OA\Property(property="per_page", type="integer", example="1"),
 *     @OA\Property(property="current_page", type="integer", example="2"),
 *     @OA\Property(property="total_pages", type="integer", example="1"),
 *     @OA\Property(property="links", type="object",
 *          @OA\Property(property="next", type="url", example="http://localhost?page=3"),
 *          @OA\Property(property="previous", type="url", example="http://localhost?page=1"),
 *     ),
 *  ),
 * )
 */
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected function isAuthorizedAdmin(): bool
    {
        if (Auth::check()) {
            /** @var User $user */
            $user = Auth::user();

            return $user->isAdmin();
        }

        return false;
    }
}
