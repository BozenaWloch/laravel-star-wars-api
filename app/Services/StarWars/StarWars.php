<?php
declare(strict_types=1);

namespace App\Services\StarWars;

use App\Services\StarWarsAPI;

class StarWars
{
    /**
     * @var \App\Services\StarWarsAPI
     */
    private $starWarsAPI;

    public function __construct(StarWarsAPI $starWarsAPI)
    {
        $this->starWarsAPI = $starWarsAPI;
    }

    public function getRandomPerson(): array
    {
        $people = $this->starWarsAPI->getPeople();

        return $people[\array_rand($people)];
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
        foreach ($person['species_ids'] ?? [] as $specieId) {
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
        foreach ($person['vehicles_ids'] ?? [] as $vehicleId) {
            $vehicles[] = $this->starWarsAPI->getVehicleById($vehicleId);
        }

        return $vehicles;
    }

    public function getVehicle(int $vehicleId): array
    {
        return $this->starWarsAPI->getVehicleById($vehicleId);
    }

    public function getPersonStarships(int $personId): array
    {
        $person = $this->getPersonById($personId);

        $starships = [];
        foreach ($person['starships_ids'] ?? [] as $starshipId) {
            $starships[] = $this->starWarsAPI->getStarshipById($starshipId);
        }

        return $starships;
    }

    public function getStarship(int $starshipId): array
    {
        return $this->starWarsAPI->getStarshipById($starshipId);
    }

    public function getPersonPlanets(int $personId): array
    {
        $person = $this->getPersonById($personId);

        $planets = [];
        foreach ($person['planets_ids'] ?? [] as $planetId) {
            $planets[] = $this->starWarsAPI->getPlanetById($planetId);
        }

        return $planets;
    }

    public function getPlanet(int $planetId): array
    {
        return $this->starWarsAPI->getPlanetById($planetId);
    }
}
