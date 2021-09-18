<?php

namespace App\Repositories\User;

use App\Models\User;
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

    public function getPlayers(): Collection
    {
        return User::players()->get();
    }

    public function getTotalPlayerCount(): int
    {
        return User::players()->count();
    }

    public function getCoaches(): LazyCollection
    {
        return User::coaches()->cursor();
    }

    public function getUnassignedCoaches(): LazyCollection
    {
        return User::coaches()->unassigned()->cursor();
    }

    public function getGoalies(): LazyCollection
    {
        return User::goalies()->cursor();
    }

    public function getUnassignedGoalies(): Collection
    {
        return User::goalies()->unassigned()->get();
    }
}
