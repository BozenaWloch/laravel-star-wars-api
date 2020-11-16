<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Requests\Request;
use App\Repositories\UserRepository;
use App\Services\StarWars\StarWars;

class FilmsController extends Controller
{
    /**
     * @var \App\Repositories\UserRepository
     */
    private $userRepository;

    /**
     * @var \App\Services\StarWars\StarWars
     */
    private $starWars;

    public function __construct(UserRepository $userRepository, StarWars $starWars)
    {
        $this->userRepository = $userRepository;
        $this->starWars = $starWars;
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/films",
     *     summary="Display selected user films",
     *     tags={"films"},
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
     *
     * @param \App\Http\Requests\Request $request
     * @param int                        $userId
     *
     * @return array
     */
    public function list(Request $request, int $userId): array
    {
        $user = $this->userRepository->getById($userId);

        return $this->starWars->getPersonFilms($user->external_id);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/films/{filmId}",
     *     summary="Display selected user film",
     *     tags={"films"},
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
     *        name="filmId",
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
     *
     * @param \App\Http\Requests\Request $request
     * @param int                        $userId
     * @param int                        $filmId
     *
     * @throws \App\Exceptions\ForbiddenException
     *
     * @return array
     */
    public function read(Request $request, int $userId, int $filmId): array
    {
        $user = $this->userRepository->getById($userId);

        $person = $this->starWars->getPersonById($user->external_id);

        if (!\in_array($filmId, $person['films_ids'] ?? [])) {
            throw new ForbiddenException();
        }

        return $film = $this->starWars->getFilm($filmId);
    }
}
