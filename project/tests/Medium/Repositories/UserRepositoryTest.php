<?php

namespace Tests\Medium\Repositories;

use App\Models\User;
use App\Repositories\User\UserRepositoryInterface as UserRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class UserRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    private UserRepository $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = app(UserRepository::class);
        $this->loadTestData();
    }

    public function testCanCreateUser(): void
    {
        $user = $this->userRepository->create($this->testData);
        $this->assertModelMatchesExpectedData($user);
    }

    protected function loadTestData(?array $replacementVars = null): void
    {
        $this->testData = [
            'user_type' => User::USER_TYPE_PLAYER,
            'first_name' => 'David',
            'last_name' => 'Sitrick',
            'ranking' => 5,
            'can_play_goalie' => false,
        ];

        if ($replacementVars !== null) {
            $this->testData = array_replace($this->testData, $replacementVars);
        }
    }
}
