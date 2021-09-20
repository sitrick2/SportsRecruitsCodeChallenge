<?php

namespace Tests\Unit;

use App\Models\Team;
use App\Services\TeamGeneration\TeamGenerationServiceInterface as TeamGenerationService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;
use App\Models\User;

class PlayersIntegrityTest extends TestCase
{
    use DatabaseMigrations;

    private TeamGenerationService $teamGenerationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamGenerationService = app(TeamGenerationService::class);
    }

    public function testFullFunctionality(): void
    {
        $this->loadTestData();
        $this->teamGenerationService->generateTeams(20);
        $this->assertGoaliePlayersExist();
    }

    private function assertGoaliePlayersExist(): void
    {
		$result = User::players()->where('can_play_goalie', true)->count();
		$this->assertTrue($result >= 4); //Creating 4 teams, ensuring there are at least 4 goalies

    }
    public function testAtLeastOneGoaliePlayerPerTeam (): void
    {
/*
	    calculate how many teams can be made so that there is an even number of teams and they each have between 18-22 players.
	    Then check that there are at least as many players who can play goalie as there are teams
*/
        $teams = Team::all();
        $this->assertEquals(0, $teams->count() % 2);

        foreach ($teams as $team) {
            /** @var Team $team */
            $this->assertTrue($team->playerCount() >= 18 && $team->playerCount() <= 22);
            $this->assertNotNull($team->players->where('can_play_goalie', true)->first());
        }
    }

    protected function loadTestData(?array $replacementVars = []): void
    {
        $this->artisan('db:seed');
    }
}
