<?php

namespace App\Repositories\Team;

use App\Models\Team;
use App\Models\User;
use Illuminate\Support\Collection;
use Log;

class TeamRepository implements TeamRepositoryInterface
{
    public function create(?string $teamName = null, ?Collection $players = null, ?User $coach = null): Team
    {
        $team = new Team();
        $team->team_name = $teamName ?? Team::generateTeamName();
        $team->save();

        if ($coach !== null && $coach->isCoach()) {
            $team->coach()->save($coach);
        }


        if ($players !== null) {
            $players = $this->filterAccidentalNonPlayers($players);
            $team->players()->saveMany($players);
        }

        return $team;
    }

    public function createMultiple(int $numToCreate, ?array $teamNames = null, ?Collection $coaches = null): Collection
    {
        $teams = new Collection();
        if ($numToCreate < $teamNames) {
            Log::warning('More team names provided than specified in $numToCreate value, honoring $numToCreate and discarding remaining team names.');
        }

        if ($coaches === null || $numToCreate > $coaches->count()) {
            Log::warning('Creating more teams than Coaches provided.');
        }

        $counter = 0;
        while ($counter < $numToCreate) {
            //create team with name
            $team = new Team();
            $team->team_name = $teamNames[$counter] ?? Team::generateTeamName();
            $team->save();

            //assign coach if one has been provided
            if ($coaches !== null && $coach = $coaches->get($counter)) {
                $team->coach()->save($coach);
            }

            //add to collection
            $teams->add($team);

            $counter++;
        }

        return $teams;
    }

    public function update(Team $team, ?string $newName = null, ?Collection $players = null): Team
    {
        $team->team_name = $newName ?? $team->team_name;

        if ($players !== null) {
            $team->players()->saveMany($players);
        }

        $team->save();

        return $team;
    }

    public function delete(Team $team): Team
    {
        $team->delete();
        return $team;
    }

    public function haveTeamsBeenGenerated(): bool
    {
        return Team::count() > 0;
    }

    private function filterAccidentalNonPlayers(Collection $players): Collection
    {
        return $players->filter(function (User $user) {
            if (!$user->isPlayer()) {
                Log::warning('User ID ' . $user->id . ' incorrectly submitted as user_type player.');
                return false;
            }

            return true;
        });
    }

    private function filterAccidentalNonCoaches(Collection $coaches): Collection
    {
        return $coaches->filter(function (User $user) {
            if (!$user->isCoach()) {
                Log::warning('User ID ' . $user->id . ' incorrectly submitted as user_type coach.');
                return false;
            }

            return true;
        });
    }
}
