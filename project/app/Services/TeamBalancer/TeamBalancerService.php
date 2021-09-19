<?php


namespace App\Services\TeamBalancer;


use App\Models\Team;
use Illuminate\Support\Collection;
use Illuminate\Support\LazyCollection;

class TeamBalancerService implements TeamBalancerServiceInterface
{
    public function balanceTeamPair(Team $team1, Team $team2, LazyCollection $playerPool)
    {
        //TODO
    }
}
