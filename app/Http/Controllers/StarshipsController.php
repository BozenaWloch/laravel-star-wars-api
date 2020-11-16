<?php
declare(strict_types=1);
namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Requests\Request;
use App\Repositories\UserRepository;
use App\Services\StarWars\StarWars;

class StarshipsController extends Controller
{

    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var StarWars
     */
    private $starWars;

    public function __construct(UserRepository $userRepository, StarWars $starWars)
    {
        $this->userRepository = $userRepository;
        $this->starWars = $starWars;
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/starships",
     *     summary="Display selected user starships",
     *     tags={"starships"},
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
     *       description="Success"
     *     )
     * )
     * @param Request $request
     * @param int $userId
     * @return array
     */
    public function list(Request $request, int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        return $this->starWars->getPersonStarships($user->external_id);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/starships/{starshipsId}",
     *     summary="Display selected user starships",
     *     tags={"starships"},
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
     *        in="path",
     *        name="starshipsId",
     *        required=true,
     *        example="1",
     *        @OA\Schema(
     *           type="integer",
     *        )
     *     ),
     *     security={{ "apiAuth": {} }},
     *     @OA\Response(
     *       response=200,
     *       description="Success"
     *     )
     * )
     * @param Request $request
     * @param int $userId
     * @param int $starshipId
     *
     * @return array
     * @throws ForbiddenException
     */
    public function read(Request $request, int $userId, int $starshipId): array
    {
        $user = $this->userRepository->getById($userId);

        $person = $this->starWars->getPersonById($user->external_id);

        if (!in_array($starshipId, $person['starships_ids'] ?? [])) {
            throw new ForbiddenException();
        }

        return $film = $this->starWars->getStarship($starshipId);
    }
}
