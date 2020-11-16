<?php
declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

class StarWarsAPIGuzzleClient
{
    /**
     * @var array
     */
    private $options;

    public function __construct()
    {
        $this->options = [
            'base_uri' => env('STAR_WARS_API_URL', 'https://swapi.dev/api/'),
            'timeout'  => 60,
        ];
    }

    public function getClient(): Client
    {
        return new Client($this->options);
    }
}
