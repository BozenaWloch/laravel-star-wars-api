<?php
declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ForbiddenException;
use App\Http\Requests\Request;
use App\Repositories\UserRepository;
use App\Services\StarWars\StarWars;

class SpeciesController extends Controller
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
     *     path="/users/{userId}/species",
     *     summary="Display selected user species",
     *     tags={"species"},
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

        return $this->starWars->getPersonSpecies($user->external_id);
    }

    /**
     * @OA\Get(
     *     path="/users/{userId}/species/{specieId}",
     *     summary="Display selected user specie",
     *     tags={"species"},
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
     *        name="specieId",
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
     * @param int                        $specieId
     *
     * @throws \App\Exceptions\ForbiddenException
     *
     * @return array
     */
    public function read(Request $request, int $userId, int $specieId): array
    {
        $user = $this->userRepository->getById($userId);

        $person = $this->starWars->getPersonById($user->external_id);

        if (!\in_array($specieId, $person['species_ids'] ?? [])) {
            throw new ForbiddenException();
        }

        return $film = $this->starWars->getSpecie($specieId);
    }
}
