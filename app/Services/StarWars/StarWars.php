<?php
declare(strict_types=1);

namespace App\Services\StarWars;

use App\Services\StarWarsAPI;

class StarWars
{

    /**
     * @var StarWarsAPI
     */
    private $starWarsAPI;

    public function __construct(StarWarsAPI $starWarsAPI)
    {
        $this->starWarsAPI = $starWarsAPI;
    }

    public function getRandomPerson(): array
    {
        $people = $this->starWarsAPI->getPeople();

        return $people[array_rand($people)];
    }

    public function getPersonById(int $personId): array
    {
        return $this->starWarsAPI->getPersonById($personId);
    }

    public function getPersonFilms(int $personId): array
    {
        $person = $this->getPersonById($personId);

        $films = [];
        foreach ($person['films'] as $filmUrl) {
            $films[] = $this->starWarsAPI->getFilmByUrl($filmUrl);
        }

        return $films;
    }

    public function getFilm(int $filmId): array
    {
       return $this->starWarsAPI->getFilmById($filmId);
    }
}