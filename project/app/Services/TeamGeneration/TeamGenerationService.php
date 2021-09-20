<?php

namespace App\Services\TeamGeneration;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Team\TeamRepositoryInterface as TeamRepository;
use App\Repositories\User\UserRepositoryInterface as UserRepository;
use App\Services\TeamBalancer\TeamBalancerServiceInterface as TeamBalancerService;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

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
        $teams = $this->distributePlayersEvenlyAcrossTeams($teams, $avgTeamSize);
        $teams = $this->handleOverflowPlayers($teams, $avgTeamSize);

        return $this->teamBalancerService->balanceTeams($teams);
    }

    private function determineNumberOfTeams(int $avgTeamSize): int
    {
        $playerCount = $this->userRepository->getTotalPlayerCount();
        $numberOfTeams = floor($playerCount / $avgTeamSize);

        if ($numberOfTeams % 2 === 0) {// if even number of teams, all set
            return $numberOfTeams;
        }

        $maxOpenExtraSlotsPerTeam = ($avgTeamSize * 1.10) - $avgTeamSize; //we have $maxOpenExtraSlotsPerTeam to fill with overflow players
        $smallerTeamCount = $numberOfTeams - 1;
        $overflowCount = $playerCount % ($avgTeamSize * $smallerTeamCount); //checking how many overflow players from avg team size we have if we reduce the team count by 1
        if ($overflowCount <= ($maxOpenExtraSlotsPerTeam * $smallerTeamCount)) {
            return $smallerTeamCount;
        }

        // too many overflow players to use with open slots if we reduce the number of teams by 1. Now test to see if we can fill out the rosters if we add a team.
        $largerTeamCount = $numberOfTeams + 1;
        $minimumAcceptableTeamSize = ($avgTeamSize * 0.90); //minimum acceptable team size is 10% less than declared average team size
        if ($playerCount / $largerTeamCount >= $minimumAcceptableTeamSize) { //check if we can fill out the rosters enough to meet minimum requirements
            return $largerTeamCount;
        }

        throw new BadRequestException('Cannot create an even number of teams with the provided values. Please adjust the average team size or the number of players available in the pool.');
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

    private function distributePlayersEvenlyAcrossTeams(Collection $teams, int $avgTeamSize): Collection
    {
        $players = $this->userRepository->getUnassignedPlayers();

        //first assignment pass, random distribution
        foreach ($teams as $team) {
            $teamSizeMinusGoalie = $avgTeamSize - 1; // -1 to account for goalie already assigned
            /* @var Team $team */
            if ($players->count() >= $teamSizeMinusGoalie){
                $team->players()->saveMany($players->random($teamSizeMinusGoalie));
            } else {
                $team->players()->saveMany($players->all());
            }
        }

        return $teams;
    }

    public function handleOverflowPlayers(Collection $teams, int $avgTeamSize): Collection
    {
        $teams = $this->teamBalancerService->sortTeamCollectionByTotalPlayerRanking($teams);
        $remainingPlayers = $this->userRepository->getUnassignedPlayers();
        while ($remainingPlayers->count() > 0) {
            foreach ($teams as $team) {
                if ($remainingPlayers->count() === 0) {
                    break;
                }

                /* @var Team $team */
                if ($team->players->count() <= ($avgTeamSize * 1.10)) {
                    $team->players()->save($remainingPlayers->get(0));
                    $team->refresh();
                }
            }
        }
        return $teams;
    }
}
