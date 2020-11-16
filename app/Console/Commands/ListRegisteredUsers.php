<?php

namespace App\Console\Commands;

use App\Repositories\UserRepository;
use App\Services\StarWars\StarWars;
use Illuminate\Console\Command;

class ListRegisteredUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get users list';
    /**
     * @var UserRepository
     */
    private $userRepository;
    /**
     * @var StarWars
     */
    private $starWars;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(UserRepository $userRepository, StarWars $starWars)
    {
        $this->userRepository = $userRepository;
        $this->starWars = $starWars;

        parent::__construct();

    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = $this->userRepository->getAll();

        $usersData = [];
        foreach ($users as $user) {
            $person = $this->starWars->getPersonById($user->external_id);

            $usersData[] = [
                'email' => $user->email,
                'nick' => $user->nick_name,
                'hero_name' => $person['name'],
            ];
        }

        print_r($usersData);

        return 0;
    }
}
