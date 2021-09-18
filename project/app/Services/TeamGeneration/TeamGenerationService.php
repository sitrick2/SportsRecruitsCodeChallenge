<?php

namespace App\Services\TeamGeneration;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Team\TeamRepositoryInterface as TeamRepository;
use App\Repositories\User\UserRepositoryInterface as UserRepository;
use Illuminate\Support\Collection;

class TeamGenerationService implements TeamGenerationServiceInterface
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository, TeamRepository $teamRepository)
    {
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
    }

    public function generateTeams(?int $avgTeamSize = null, ?array $teamNames = null): Collection
    {
        $numberOfTeams = $this->determineNumberOfTeams($avgTeamSize);
        $teams = $this->teamRepository->createMultiple($numberOfTeams, $teamNames);
        $teams = $this->assignCoachesToTeams($teams);
        $teams = $this->assignGoaliesToTeams($teams);
        $this->distributePlayersAcrossTeams();

        return $teams;
    }

    private function determineNumberOfTeams(?int $avgTeamSize): int
    {
        $playerCount = $this->userRepository->getTotalPlayerCount();
        $avgTeamSize = $avgTeamSize ?? (int) config('teams.default_avg_team_size');
        return $playerCount / $avgTeamSize;
    }

    private function assignCoachesToTeams(Collection $teams): Collection
    {
        $coaches = $this->userRepository->getCoaches();
        foreach ($teams as $team) {
            /* @var Team $team */
            $team->coach()->save($coaches->pop());
        }

        return $teams;
    }

    private function assignGoaliesToTeams(Collection $teams)
    {

    }

    private function distributePlayersAcrossTeams()
    {

    }
}
