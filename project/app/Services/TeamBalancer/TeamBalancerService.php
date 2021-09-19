<?php


namespace App\Services\TeamBalancer;


use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class TeamBalancerService implements TeamBalancerServiceInterface
{
    public function balanceTeams(Collection $teams): Collection
    {
        $teams = $this->sortTeamCollectionByTotalPlayerRanking($teams);

        while (!$this->teamsAreBalanced($teams)) {
            $teams = $this->adjustRosters($teams);
            $this->sortTeamCollectionByTotalPlayerRanking($teams);
        }

        return $teams;
    }

    private function adjustRosters(Collection $teams): Collection
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

    private function sortTeamCollectionByTotalPlayerRanking(Collection $teams): Collection
    {
        return $teams->sortBy(function (Team $team) {
            return $team->totalPlayerRanking();
        });
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
