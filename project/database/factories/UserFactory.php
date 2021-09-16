<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{

    protected $model = User::class;

    /**
     * @throws \Exception
     */
    public function definition(): array
    {
        $userType = random_int(1, 20) === 1 ? User::USER_TYPE_COACH : User::USER_TYPE_PLAYER;

        return [
            'user_type' =>  $userType,
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'ranking' => $this->rankByExpectedDistribution($userType === User::USER_TYPE_PLAYER)
        ];
    }

    public function player(): UserFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => User::USER_TYPE_PLAYER,
                'ranking' => $this->rankByExpectedDistribution()
            ];
        });
    }

    public function coach(): UserFactory
    {
        return $this->state(function (array $attributes) {
            return [
                'user_type' => User::USER_TYPE_COACH,
                'ranking' => $this->rankByExpectedDistribution(false)
            ];
        });
    }

    /**
     * @throws \Exception
     */
    private function rankByExpectedDistribution(bool $isPlayer = true): int
    {
        if (!$isPlayer) { // if coach, 1 in 5 are ranked 1, rest ranked 0
            return random_int(1, 5) === 1 ? 1 : 0;
        }

        $randomIntForProbability = random_int(1, 100);

        if ($randomIntForProbability <= 78) { //distribution according to seeded data is 78% of players are rank 3
            return 3;
        }

        if ($randomIntForProbability >= 79 && $randomIntForProbability <= 85) { // 7% rank 3
            return 4;
        }

        if ($randomIntForProbability >= 86 && $randomIntForProbability <= 91) {// 6% rank 5
            return 5;
        }

        if ($randomIntForProbability >= 92 && $randomIntForProbability <= 96) {// 5% rank 5
            return 2;
        }

        return 1; //4% rank 1
    }
}
