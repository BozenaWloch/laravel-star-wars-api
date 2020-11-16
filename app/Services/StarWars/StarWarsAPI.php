<?php
declare(strict_types=1);

namespace App\Services;

use App\Exceptions\StarWarsAPIException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Cache\Repository as CacheRepository;

class StarWarsAPI
{
    private const PERSON_DEPENDENCIES = [
        'films',
        'species',
        'vehicles',
        'starships',
        'planets',
    ];

    /**
     * @var \App\Services\StarWarsAPIGuzzleClient
     */
    private $starWarsAPIGuzzleClient;

    /**
     * @var \Illuminate\Cache\Repository
     */
    private $cacheRepository;

    /**
     * StarWarsAPI constructor.
     *
     * @param \App\Services\StarWarsAPIGuzzleClient $starWarsAPIGuzzle
     * @param \Illuminate\Cache\Repository          $cacheRepository
     */
    public function __construct(StarWarsAPIGuzzleClient $starWarsAPIGuzzle, CacheRepository $cacheRepository)
    {
        $this->starWarsAPIGuzzleClient = $starWarsAPIGuzzle->getClient();
        $this->cacheRepository = $cacheRepository;
    }

    public function getPeople(): array
    {
        if ($this->cacheRepository->has('people')) {
            return $this->cacheRepository->get('people');
        }

        $people = [];
        $pageNumber = 1;

        try {
            do {
                $response = $this->starWarsAPIGuzzleClient->request('GET', 'people', [
                    'query' => [
                        'page' => $pageNumber,
                    ],
                ]);
                $decodedResponse = \json_decode((string) $response->getBody(), true);

                if (isset($decodedResponse['results'])) {
                    foreach ($decodedResponse['results'] as $result) {
                        if (!isset($result['url'])) {
                            continue;
                        }

                        $result['id'] = $this->getResourceIdFromUrl($result['url']);

                        $people[] = $result;
                    }
                }

                ++$pageNumber;
            } while (isset($decodedResponse['next']) && null !== $decodedResponse['next']);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during people request.');
        }

        $this->cacheRepository->put('people', $people, now()->addHours(24));

        return $people;
    }

    public function getPersonById(int $personId): array
    {
        $personCacheKey = \sprintf('person.%s', $personId);

        if ($this->cacheRepository->has($personCacheKey)) {
            return $this->cacheRepository->get($personCacheKey);
        }

        $uri = \sprintf('people/%s', $personId);

        try {
            $response = $this->starWarsAPIGuzzleClient->request('GET', $uri);
            $person = \json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during person request.');
        }

        foreach (self::PERSON_DEPENDENCIES as $dependency) {
            if (isset($person[$dependency])) {
                $personDependencyIds = [];
                foreach ($person[$dependency] as $personDependency) {
                    $personDependencyIds[] = $this->getResourceIdFromUrl($personDependency);
                }

                $person[\sprintf('%s_ids', $dependency)] = $personDependencyIds;
            }
        }

        $this->cacheRepository->put($personCacheKey, $person, now()->addHours(24));

        return $person;
    }

    public function getFilmById(int $filmId): array
    {
        $filmCacheKey = \sprintf('film.%s', $filmId);

        if ($this->cacheRepository->has($filmCacheKey)) {
            return $this->cacheRepository->get($filmCacheKey);
        }

        $uri = \sprintf('films/%s', $filmId);

        try {
            $response = $this->starWarsAPIGuzzleClient->request('GET', $uri);
            $film = \json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during film request.');
        }

        $this->cacheRepository->put($filmCacheKey, $film, now()->addHours(24));

        return $film;
    }

    public function getSpecieById(int $specieId): array
    {
        $specieCacheKey = \sprintf('species.%s', $specieId);

        if ($this->cacheRepository->has($specieCacheKey)) {
            return $this->cacheRepository->get($specieCacheKey);
        }

        $uri = \sprintf('species/%s', $specieId);

        try {
            $response = $this->starWarsAPIGuzzleClient->request('GET', $uri);
            $specie = \json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during specie request.');
        }

        $this->cacheRepository->put($specieCacheKey, $specie, now()->addHours(24));

        return $specie;
    }

    public function getVehicleById(int $vehicleId): array
    {
        $vehicleCacheKey = \sprintf('vehicles.%s', $vehicleId);

        if ($this->cacheRepository->has($vehicleCacheKey)) {
            return $this->cacheRepository->get($vehicleCacheKey);
        }

        $uri = \sprintf('vehicles/%s', $vehicleId);

        try {
            $response = $this->starWarsAPIGuzzleClient->request('GET', $uri);
            $vehicle = \json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during vehicle request.');
        }

        $this->cacheRepository->put($vehicleCacheKey, $vehicle, now()->addHours(24));

        return $vehicle;
    }

    public function getStarshipById(int $starshipId): array
    {
        $starshipCacheKey = \sprintf('starships.%s', $starshipId);

        if ($this->cacheRepository->has($starshipCacheKey)) {
            return $this->cacheRepository->get($starshipCacheKey);
        }

        $uri = \sprintf('starships/%s', $starshipId);

        try {
            $response = $this->starWarsAPIGuzzleClient->request('GET', $uri);
            $starship = \json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during starship request.');
        }

        $this->cacheRepository->put($starshipCacheKey, $starship, now()->addHours(24));

        return $starship;
    }

    public function getPlanetById(int $planetId): array
    {
        $planetCacheKey = \sprintf('planets.%s', $planetId);

        if ($this->cacheRepository->has($planetCacheKey)) {
            return $this->cacheRepository->get($planetCacheKey);
        }

        $uri = \sprintf('planets/%s', $planetId);

        try {
            $response = $this->starWarsAPIGuzzleClient->request('GET', $uri);
            $planet = \json_decode((string) $response->getBody(), true);
        } catch (ClientException $exception) {
            $error = \json_decode((string) $exception->getResponse()->getBody()->getContents(), true);

            throw new StarWarsAPIException($error['detail'] ?? 'Something went wrong during planet request.');
        }

        $this->cacheRepository->put($planetCacheKey, $planet, now()->addHours(24));

        return $planet;
    }

    private function getResourceIdFromUrl(string $url): int
    {
        $urlPathParts = \explode('/', \trim(\parse_url($url, PHP_URL_PATH), '/'));

        return (int) \end($urlPathParts);
    }
}
