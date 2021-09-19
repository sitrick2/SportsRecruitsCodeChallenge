<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class UserRepository implements UserRepositoryInterface
{
    public function create(array $inputData): User
    {
        return User::create($inputData);
    }

    public function update(User $user, array $inputData): User
    {
        $user->update($inputData);
        return $user;
    }

    public function delete(User $user): User
    {
        $user->delete();
        return $user;
    }

    public function getPlayers(): LazyCollection
    {
        return User::players()->cursor();
    }

    public function getUnassignedPlayers(?int $count = null): LazyCollection
    {
        /* @var Builder $players */
        $players = User::players()->unassigned();

        if ($count !== null) {
            $players->limit($count);
        }

        return $players->cursor();
    }

    public function getTotalPlayerCount(): int
    {
        return User::players()->count();
    }

    public function getCoaches(): LazyCollection
    {
        return User::coaches()->cursor();
    }

    public function getUnassignedCoaches(?int $count = null): LazyCollection
    {
        /* @var Builder $coaches */
        $coaches = User::coaches()->unassigned();

        if ($count !== null) {
            $coaches->limit($count);
        }

        return $coaches->cursor();
    }

    public function getGoalies(): LazyCollection
    {
        return User::goalies()->cursor();
    }

    public function getUnassignedGoalies(?int $count = null): LazyCollection
    {
        /* @var Builder $goalies */
        $goalies = User::goalies()->unassigned();

        if ($count !== null) {
            $goalies->limit($count);
        }

        return $goalies->cursor();
    }
}
