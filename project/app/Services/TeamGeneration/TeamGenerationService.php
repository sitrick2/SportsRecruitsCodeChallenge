<?php

namespace App\Services\TeamGeneration;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Team\TeamRepositoryInterface as TeamRepository;
use App\Repositories\User\UserRepositoryInterface as UserRepository;
use App\Services\TeamBalancer\TeamBalancerServiceInterface as TeamBalancerService;
use Illuminate\Support\Collection;

class TeamGenerationService implements TeamGenerationServiceInterface
{
    private TeamRepository $teamRepository;
    private UserRepository $userRepository;
    private TeamBalancerService $teamBalancerService;

    public function __construct(UserRepository $userRepository, TeamRepository $teamRepository, TeamBalancerService $teamBalancerService)
    {
        $this->userRepository = $userRepository;
        $this->teamRepository = $teamRepository;
        $this->teamBalancerService = $teamBalancerService;
    }

    public function generateTeams(?int $avgTeamSize = null, ?array $teamNames = null): Collection
    {
        $avgTeamSize = $avgTeamSize ?? (int) config('teams.default_avg_team_size');

        $numberOfTeams = $this->determineNumberOfTeams($avgTeamSize);
        $teams = $this->teamRepository->createMultiple($numberOfTeams, $teamNames);
        $teams = $this->assignCoachesToTeams($teams);
        $teams = $this->assignGoaliesToTeams($teams);
        $teams = $this->distributePlayersAcrossTeams($teams, $avgTeamSize);

        return $this->teamBalancerService->balanceTeams($teams);
    }

    private function determineNumberOfTeams(int $avgTeamSize): int
    {
        $playerCount = $this->userRepository->getTotalPlayerCount();
        return $playerCount / $avgTeamSize;
    }

    private function assignCoachesToTeams(Collection $teams): Collection
    {
        $coaches = $this->userRepository->getUnassignedCoaches($teams->count());
        foreach ($teams as $team) {
            /* @var Team $team */
            $team->coach()->save($coaches->get(0));
        }

        return $teams;
    }

    private function assignGoaliesToTeams(Collection $teams): Collection
    {
        $goalies = $this->userRepository->getUnassignedGoalies($teams->count());
        foreach ($teams as $team) {
            /* @var Team $team */
            $team->players()->save($goalies->get(0));
        }

        return $teams;
    }

    private function distributePlayersAcrossTeams(Collection $teams, int $avgTeamSize): Collection
    {
        $players = $this->userRepository->getUnassignedPlayers();

        //first assignment pass, random distribution
        foreach ($teams as $team) {
            /* @var Team $team */
            $team->players()->saveMany($players->random($avgTeamSize - 1)); // -1 to account for goalie already assigned
        }

        return $teams;
    }
}
