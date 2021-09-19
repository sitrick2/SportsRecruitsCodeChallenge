<?php


namespace App\Services\TeamBalancer;


use Illuminate\Support\Collection;

interface TeamBalancerServiceInterface
{
    public function balanceTeams(Collection $teams);
}
