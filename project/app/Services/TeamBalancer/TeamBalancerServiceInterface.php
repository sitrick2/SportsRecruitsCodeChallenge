<?php


namespace App\Services\TeamBalancer;


use Illuminate\Support\Collection;

interface TeamBalancerServiceInterface
{
    public function balanceTeams(Collection $teams);
    public function sortTeamCollectionByTotalPlayerRanking(Collection $teams): Collection;
    public function sortTeamCollectionByRosterSize(Collection $teams): Collection;
}
