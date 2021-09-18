<?php

namespace App\Services\TeamGeneration;

use Illuminate\Support\Collection;

interface TeamGenerationServiceInterface
{
    public function generateTeams(?int $avgTeamSize = null, ?array $teamNames = null): Collection;
}
