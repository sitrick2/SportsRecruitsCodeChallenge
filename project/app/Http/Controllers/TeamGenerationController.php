<?php

namespace App\Http\Controllers;

use App\Services\TeamGeneration\TeamGenerationServiceInterface as TeamGenerationService;
use Illuminate\Http\Request;

class TeamGenerationController extends Controller
{
    private $teamGenerationService;

    public function __construct(TeamGenerationService $teamGenerationService)
    {
        $this->teamGenerationService = $teamGenerationService;
    }

    public function create(Request $request): void
    {
        $this->teamGenerationService->generateTeams($request->get('avgTeamSize'), $request->get('teamNames'));
    }
}
