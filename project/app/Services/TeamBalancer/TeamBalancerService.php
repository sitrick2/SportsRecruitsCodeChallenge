<?php


namespace App\Services\TeamBalancer;


use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class TeamBalancerService implements TeamBalancerServiceInterface
{
    public function balanceTeams(Collection $teams): Collection
    {
        while (!$this->rostersAreSizedCorrectly($teams)) {
            $teams = $this->rebalancePlayersForRosterSize($teams);
        }

        $teams = $this->sortTeamCollectionByTotalPlayerRanking($teams);

        while (!$this->teamsAreBalanced($teams)) {
            $teams = $this->rebalancePlayersForRanking($teams);
            $teams = $this->sortTeamCollectionByTotalPlayerRanking($teams);
        }
        return $teams->sortBy('id');
    }

    public function sortTeamCollectionByTotalPlayerRanking(Collection $teams): Collection
    {
        return $teams->sortBy(function (Team $team) {
            return $team->totalPlayerRanking();
        });
    }

    public function sortTeamCollectionByRosterSize(Collection $teams): Collection
    {
        return $teams->sortBy(function (Team $team) {
            return $team->playerCount();
        });
    }

    private function rostersAreSizedCorrectly(Collection $teams): bool
    {
        if (Cache::get('rosters_sized_correctly')) {
            return true;
        }

        $avgRosterSize = config('teams.default_avg_team_size');
        foreach ($teams as $team) {
            if ($team->playerCount() < ($avgRosterSize * 0.90)) {
                return false;
            }
        }

        Cache::put('rosters_sized_correctly', true, 30);
        return true;
    }

    private function rebalancePlayersForRosterSize(Collection $teams): Collection
    {
        while (!$this->rostersAreSizedCorrectly($teams)) {
            $teams = $this->sortTeamCollectionByRosterSize($teams);

            $receivingTeam = $teams->first();
            $givingTeam = $teams->last();

            $receivingTeam->players()->save($givingTeam->players->where('can_play_goalie', false)->first());

            $receivingTeam->refresh();
            $givingTeam->refresh();
        }

        return $this->sortTeamCollectionByTotalPlayerRanking($teams);
    }

    private function rebalancePlayersForRanking(Collection $teams): Collection
    {
        $topPlayer = $teams->last()->players
            ->sortByDesc('ranking')
            ->where('can_play_goalie', false) // don't give away the team goalie
            ->first();

        $bottomPlayer = $teams->first()->players
            ->sortBy('ranking')
            ->where('can_play_goalie', false) // don't give away the team goalie
            ->first();

        $teams->first()->players()->save($topPlayer);
        $teams->last()->players()->save($bottomPlayer);

        return $teams;
    }

    /**
     * Checks to see if teams are within +/- 5% of the average team talent rating.
     */
    private function teamsAreBalanced(Collection $teams): bool
    {
        $max = $teams->last()->totalPlayerRanking();
        $min = $teams->first()->totalPlayerRanking();

        $avg = $teams->average(function (Team $team) {
            return $team->totalPlayerRanking();
        });


        return $max <= ($avg * 1.05) && $min >= ($avg * 0.95);
    }
}
