<?php

namespace App\Http\Controllers;

use App\Models\Team;
use App\Repositories\Team\TeamRepositoryInterface as TeamRepository;
use App\Services\TeamGeneration\TeamGenerationServiceInterface as TeamGenerationService;
use Illuminate\Http\Request;

class TeamGenerationController extends Controller
{
    private TeamGenerationService $teamGenerationService;
    private TeamRepository $teamRepository;

    public function __construct(TeamGenerationService $teamGenerationService, TeamRepository $teamRepository)
    {
        $this->teamGenerationService = $teamGenerationService;
        $this->teamRepository = $teamRepository;
    }

    public function create(Request $request)
    {
        if ($this->teamRepository->haveTeamsBeenGenerated()) {
            return Team::with(['coach', 'players'])->get();
        }

        $teams = $this->teamGenerationService->generateTeams($request->get('avgTeamSize'), $request->get('teamNames'));
    }
}
