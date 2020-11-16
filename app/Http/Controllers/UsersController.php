<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\Hydrators\UserHydrator;
use App\Http\Requests\User\CreateAdminRequest;
use App\Http\Requests\User\DeleteRequest;
use App\Http\Requests\User\ReadRequest;
use App\Http\Requests\User\UpdateRequest;
use App\Http\Resources\Response\UserResourceResponse;
use App\Models\Role;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;

class UsersController extends Controller
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

    public function __construct(
        UserResourceResponse $userResourceResponse,
        UserRepository $userRepository,
        UserHydrator $userHydrator
    ) {
        $this->userResourceResponse = $userResourceResponse;
        $this->userHydrator = $userHydrator;
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}",
     *     summary="Display selected user data",
     *     tags={"users"},
     *     @OA\Parameter(
     *        in="path",
     *        name="userId",
     *        required=true,
     *        example="1",
     *        @OA\Schema(
     *           type="integer",
     *        )
     *     ),
     *     @OA\Parameter(
     *        in="query",
     *        name="include",
     *        required=false,
     *        example="acceptances,logo",
     *        description="Available options: ",
     *        @OA\Schema(
     *           type="string",
     *        )
     *     ),
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *          ref="#/components/schemas/User",
     *       )
     *     )
     * )
     *
     * @param \App\Http\Requests\User\ReadRequest $request
     * @param int                                 $userId
     */
    public function read(ReadRequest $request, int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        return $this->userResourceResponse->transform($request, $user);
    }

    /**
     * @OA\Patch(
     *     path="/users/{userId}",
     *     summary="Edit user data",
     *     tags={"users"},
     *     @OA\Parameter(
     *        in="path",
     *        name="userId",
     *        required=true,
     *        example="1",
     *        @OA\Schema(
     *           type="integer",
     *        )
     *     ),
     *     security={{ "apiAuth": {} }},
     *     @OA\RequestBody(
     *        required=true,
     *        description="Pass user data",
     *        @OA\JsonContent(
     *           @OA\Property(property="email", type="string", format="email", example="user@mail.com"),
     *           @OA\Property(property="nick_name", type="string", example="userNick"),
     *           @OA\Property(property="first_name", type="string", example="Jane"),
     *           @OA\Property(property="last_name", type="string", example="Doe"),
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
     * @param \App\Http\Requests\User\UpdateRequest $request
     * @param int                                   $userId
     */
    public function update(UpdateRequest $request, int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        $user = $this->userHydrator->hydrate($user, $request);
        $this->userRepository->save($user);

        return $this->userResourceResponse->transform($request, $user);
    }

    /**
     * @OA\Post(
     *     path="/users/admin/create",
     *     summary="Create new admin",
     *     tags={"users"},
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
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *       @OA\JsonContent(
     *          ref="#/components/schemas/User",
     *       )
     *     )
     * )
     *
     * @param \App\Http\Requests\User\CreateAdminRequest $request
     * @param \App\Models\User                           $user
     */
    public function createAdmin(CreateAdminRequest $request, User $user): array
    {
        $user = $this->userHydrator->hydrate($user, $request);
        $user->role = Role::ROLE_ADMIN_ID;

        $this->userRepository->save($user);

        return $this->userResourceResponse->transform($request, $user);
    }

    /**
     * @OA\Delete(
     *     path="/users/{userId}",
     *     summary="Delete selected user",
     *     tags={"users"},
     *     @OA\Parameter(
     *        in="path",
     *        name="userId",
     *        required=true,
     *        example="1",
     *        @OA\Schema(
     *           type="integer",
     *        )
     *     ),
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *       response=200,
     *       description="Success",
     *         @OA\JsonContent(
     *            @OA\Property(property="message", type="string", example="User account removed successfully."),
     *         )
     *     )
     * )
     *
     * @param \App\Http\Requests\User\DeleteRequest $request
     * @param int                                   $userId
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(DeleteRequest $request, int $userId): JsonResponse
    {
        $user = $this->userRepository->getById($userId);

        $this->userRepository->delete($user);

        return new JsonResponse(['message' => 'User account removed successfully.'], JsonResponse::HTTP_OK);
    }
}
