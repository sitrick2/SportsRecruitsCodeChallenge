<?php

namespace App\Repositories\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;

interface TeamRepositoryInterface
{
    public function create(?string $teamName = null, ?Collection $players = null, ?User $coach = null): Team;
    public function createMultiple(int $numToCreate, ?array $teamNames = null, ?Collection $coaches = null): Collection;
    public function update(Team $team, ?string $newName = null, ?Collection $players = null): Team;
    public function delete(Team $team): Team;
    public function haveTeamsBeenGenerated(): bool;
}
