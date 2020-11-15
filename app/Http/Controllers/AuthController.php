<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\ResetPasswordRequest;
use App\Http\Requests\Auth\UpdatePasswordRequest;
use App\Http\Requests\Hydrators\AcceptanceHydrator;
use App\Http\Requests\Hydrators\UserHydrator;
use App\Http\Resources\Response\UserResourceResponse;
use App\Mail\PasswordResetEmail;
use App\Models\Role;
use App\Models\User;
use App\Repositories\AcceptanceRepository;
use App\Repositories\PasswordResetRepository;
use App\Repositories\UserRepository;
use App\Services\FileManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\UnauthorizedException;

class AuthController extends Controller
{
    /**
     * @var \App\Http\Resources\Response\UserResourceResponse
     */
    private $userResourceResponse;

    /**
     * @var \App\Http\Requests\Hydrators\UserHydrator
     */
    private UserHydrator $userHydrator;

    /**
     * @var \App\Repositories\UserRepository
     */
    private UserRepository $userRepository;

    /**
     * @var \App\Repositories\PasswordResetRepository
     */
    private PasswordResetRepository $passwordResetRepository;

    public function __construct(
        UserResourceResponse $userResourceResponse,
        UserRepository $userRepository,
        PasswordResetRepository $passwordResetRepository,
        UserHydrator $userHydrator
    ) {
        $this->userResourceResponse = $userResourceResponse;
        $this->userHydrator = $userHydrator;
        $this->userRepository = $userRepository;
        $this->passwordResetRepository = $passwordResetRepository;
    }

    /**
     * @OA\Post(
     *     path="/register",
     *     summary="Register new user",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *        required=true,
     *        description="Pass user data",
     *        @OA\JsonContent(
     *          required={"password","password_confirmation","first_name","last_name"},
     *           @OA\Property(property="email", type="string", format="email", example="user@mail.com"),
     *           @OA\Property(property="nick_name", type="string", example="userNick"),
     *           @OA\Property(property="first_name", type="string", example="Jane"),
     *           @OA\Property(property="last_name", type="string", example="Doe"),
     *           @OA\Property(property="password", type="string", format="password", example="PassWord12345#"),
     *           @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345#"),
     *        ),
     *     ),
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *          ref="#/components/schemas/User",
     *       )
     *     )
     * )
     *
     * @param \App\Http\Requests\Auth\RegisterRequest $request
     * @param \App\Models\User                        $user
     */
    public function register(RegisterRequest $request, User $user): array
    {
        $user = $this->userHydrator->hydrate($user, $request);
        $user->role = Role::ROLE_CLIENT_ID;
        $this->userRepository->save($user);

        return $this->userResourceResponse->transform($request, $user);
    }

    /**
     * @OA\Post(
     *     path="/login",
     *     summary="User login",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *        required=true,
     *        description="Pass user data",
     *        @OA\JsonContent(
     *           required={"nick_name", "password"},
     *           @OA\Property(property="nick_name", type="string", example="userNick"),
     *           @OA\Property(property="password", type="string", format="password", example="PassWord12345#"),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *            @OA\Property(property="api_token", type="string", example="code"),
     *            @OA\Property(property="user_id", type="integer", example=1),
     *            @OA\Property(property="role", type="integer", example=2, description="Available roles: Admin = 1, Client = 2"),
     *         )
     *     )
     * )
     *
     * @param \App\Http\Requests\Auth\LoginRequest $request
     */
    public function login(LoginRequest $request): JsonResponse
    {
        $user = $this->userRepository->getByNickOrEmail($request->get('nick_name'));

        if (!Hash::check($request->get('password'), $user->password)) {
            throw new UnauthorizedException('Login or password doesn\'t match', JsonResponse::HTTP_UNAUTHORIZED);
        }

        if ($user->is_blocked) {
            throw new ForbiddenException('This account is temporary blocked.');
        }

        $token = $user->createToken('web_token')->plainTextToken;

        return new JsonResponse([
            'api_token' => $token,
            'user_id'   => $user->id,
            'role'      => $user->role,
        ], JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Get(
     *     path="/logout",
     *     summary="User logout",
     *     tags={"auth"},
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Logged out successfully."),
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        Auth::user()->currentAccessToken()->delete();

        return new JsonResponse(['message' => 'Logged out successfully.'], JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/password/reset",
     *     summary="Reset user password",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *        required=true,
     *        description="Pass user data",
     *        @OA\JsonContent(
     *           required={"nick_name"},
     *           @OA\Property(property="nick_name", type="string", example="userNick"),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Link for password reset sent on email.")
     *         )
     *     )
     * )
     *
     * @param \App\Http\Requests\Auth\ResetPasswordRequest $request
     */
    public function resetPassword(ResetPasswordRequest $request): JsonResponse
    {
        $user = $this->userRepository->getByNickOrEmail($request->get('nick_name'));

        $passwordToken = $this->passwordResetRepository->create($user);

        Mail::to($user->email)->send(new PasswordResetEmail($user->email, $passwordToken->token));

        return new JsonResponse(['message' => 'Link for password reset sent on email.'], JsonResponse::HTTP_OK);
    }

    /**
     * @OA\Post(
     *     path="/password/update",
     *     summary="Update user password",
     *     tags={"auth"},
     *     @OA\RequestBody(
     *        required=true,
     *        description="Pass user data",
     *        @OA\JsonContent(
     *           required={"token", "password", "password_confirmation"},
     *           @OA\Property(property="token", type="string", example="token_code"),
     *           @OA\Property(property="password", type="string", format="password", example="PassWord12345#"),
     *           @OA\Property(property="password_confirmation", type="string", format="password", example="PassWord12345#"),
     *        ),
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Success",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="Password updated successfully.")
     *         )
     *     )
     * )
     *
     * @param \App\Http\Requests\Auth\UpdatePasswordRequest $request
     */
    public function updatePassword(UpdatePasswordRequest $request): JsonResponse
    {
        $passwordToken = $this->passwordResetRepository->getActiveByToken($request->get('token'));

        $user = $this->userRepository->getByNickOrEmail($passwordToken->email);
        $user = $this->userHydrator->hydrate($user, $request);
        $this->userRepository->save($user);

        return new JsonResponse(['message' => 'Password updated successfully.'], JsonResponse::HTTP_OK);
    }
}
