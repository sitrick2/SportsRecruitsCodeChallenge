<?php

namespace Tests\Medium\Repositories;

use App\Models\Team;
use App\Models\User;
use App\Repositories\Team\TeamRepositoryInterface as TeamRepository;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Tests\TestCase;

class TeamRepositoryTest extends TestCase
{
    use DatabaseMigrations;

    private TeamRepository $teamRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamRepository = app(TeamRepository::class);
        $this->loadTestData();
    }

    public function testCreateTeamWithTeamName(): void
    {
        $team = $this->teamRepository->create($this->testData['team_name']);

        $this->assertModelMatchesExpectedData($team);
    }

    public function testCreateTeamWithoutProvidingTeamName(): void
    {
        $team = $this->teamRepository->create();
        $this->testData = null;

        $this->assertModelMatchesExpectedData($team);
        $this->assertTeamNamedCorrectly($team);
    }

    public function testCreateTeamWithPlayersIncluded(): void
    {
        $players = User::factory()->player()
            ->count(20)
            ->create();
        $team = $this->teamRepository->create(null, $players);

        $this->assertEquals(20, $team->players->count());
    }

    public function testCreateTeamWithCoach(): void
    {
        $coach = User::factory()->coach()
            ->create();
        $team = $this->teamRepository->create(null, null, $coach);

        $this->assertEquals($coach->id, $team->coach->id);
    }

    public function testCannotAssignPlayerAsCoach(): void
    {
        $player = User::factory()->player()->create();
        $team = $this->teamRepository->create(null, null, $player);
        $this->assertNull($team->coach);
    }

    public function testCannotAssignCoachAsPlayer(): void
    {
        $coach = User::factory()->coach()->create();
        $players = User::factory()->player()->count(3)->create();
        $players->add($coach);
        $team = $this->teamRepository->create(null, $players);
        $this->assertSame(3, $team->players->count());
    }

    public function testCreateMultipleTeamsWithNames(): void
    {
        $teamNames = [
            'Team1',
            'Team2',
            'Team3'
        ];

        $teams = $this->teamRepository->createMultiple(3, $teamNames);
        $this->assertEquals(3, $teams->count());
        $this->assertEquals($teamNames, $teams->pluck('team_name')->toArray());
    }

    public function testCreatingMoreTeamsThanNamesProvided(): void
    {
        $teamNames = [
            'Team1'
        ];

        $teams = $this->teamRepository->createMultiple(3, $teamNames);
        $this->assertEquals(3, $teams->count());
    }

    public function testDiscardingExcessNames(): void
    {
        $teamNames = [
            'Team1',
            'Team2',
            'Team3',
            'Team4',
            'Team5'
        ];

        $teams = $this->teamRepository->createMultiple(3, $teamNames);
        $this->assertEquals(3, $teams->count());
        unset($teamNames[4], $teamNames[3]);
        $this->assertEquals($teamNames, $teams->pluck('team_name')->toArray());
    }

    public function testCreateMultipleTeamsIncludingCoaches(): void
    {
        $cb = function(Team $team) {
            return $team->coach !== null;
        };

        $coaches = User::factory()->coach()->count(3)->create();
        $teams = $this->teamRepository->createMultiple(3, null, $coaches);
        $this->assertEquals(3, $teams->count());
        $this->assertEquals(3, $teams->filter($cb)->count());
    }

    public function testCreatingMoreTeamsThanCoachesAvailable(): void
    {
        $cb = function(Team $team) {
            return $team->coach !== null;
        };

        $coaches = User::factory()->coach()->count(2)->create();
        $teams = $this->teamRepository->createMultiple(3, null, $coaches);
        $this->assertEquals(3, $teams->count());
        $this->assertEquals(2, $teams->filter($cb)->count());
    }

    protected function loadTestData(?array $replacementVars = null): void
    {
        $this->testData = [
            'team_name' => 'Test Team'
        ];

        if ($replacementVars !== null) {
            $this->testData = array_replace($this->testData, $replacementVars);
        }
    }

    private function assertTeamNamedCorrectly(Team $team, ?string $teamName = null): void
    {
        $this->assertNotNull($team->team_name);
        $this->assertIsString($team->team_name);

        if ($teamName !== null) {
            $this->assertSame($teamName, $team->team_name);
        }
    }
}
