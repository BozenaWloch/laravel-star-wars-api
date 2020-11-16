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
    foreach ($person['films_ids'] ?? [] as $filmId) {
        $films[] = $this->starWarsAPI->getFilmById($filmId);
    }

    return $films;
}

    public function getFilm(int $filmId): array
    {
        return $this->starWarsAPI->getFilmById($filmId);
    }

    public function getPersonSpecies(int $personId): array
{
    $person = $this->getPersonById($personId);

    $species = [];
    foreach ($person['species_ids'] as $specieId) {
        $species[] = $this->starWarsAPI->getSpecieById($specieId);
    }

    return $species;
}

    public function getSpecie(int $specieId): array
    {
        return $this->starWarsAPI->getSpecieById($specieId);
    }

    public function getPersonVehicles(int $personId): array
    {
        $person = $this->getPersonById($personId);

        $vehicles = [];
        foreach ($person['vehicles_ids'] as $vehicleId) {
            $species[] = $this->starWarsAPI->getVehicleById($vehicleId);
        }

        return $vehicles;
    }

    public function getVehicle(int $vehicleId): array
    {
        return $this->starWarsAPI->getVehicleById($vehicleId);
    }
}