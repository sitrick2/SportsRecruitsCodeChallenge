<?php

namespace App\Repositories\User;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

interface UserRepositoryInterface
{
    public function create(array $inputData): User;
    public function update(User $user, array $inputData): User;
    public function delete(User $user): User;
    public function getPlayers(): Collection;
    public function getCoaches(): LazyCollection;
    public function getUnassignedCoaches(): LazyCollection;
    public function getGoalies(): LazyCollection;
    public function getTotalPlayerCount(): int;
}
