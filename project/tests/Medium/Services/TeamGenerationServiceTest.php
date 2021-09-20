<?php

namespace Tests\Medium\Services;

use App\Models\Team;
use App\Models\User;
use App\Services\TeamGeneration\TeamGenerationServiceInterface as TeamGenerationService;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Collection;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;
use Tests\TestCase;

class TeamGenerationServiceTest extends TestCase
{
    use DatabaseMigrations;

    private TeamGenerationService $teamGenerationService;

    public function setUp(): void
    {
        parent::setUp();
        $this->teamGenerationService = app(TeamGenerationService::class);
    }

    /**
     * @throws \PHPUnit\Framework\Exception
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGenerateTeamsWithExactNumberOfPlayers(): void
    {
        $this->loadTestData();
        $teams = $this->teamGenerationService->generateTeams(20);
        $this->assertTeamsAreConstructedCorrectly($teams, 20);
    }

    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    public function testGenerateTeamsWithOverflowPlayerCounts(): void
    {
        $this->loadTestData(['num_of_players' => 81]);
        $teams = $this->teamGenerationService->generateTeams(20);
        $this->assertTeamsAreConstructedCorrectly($teams, 20);
    }

    public function testGenerateTeamsWithUnderfilledPlayerCounts(): void
    {
        $this->loadTestData(['num_of_players' => 68]);
        $teams = $this->teamGenerationService->generateTeams(20);
        $this->assertTeamsAreConstructedCorrectly($teams, 20);
    }

    public function testThrowsExceptionIfCannotFillRoster(): void
    {
        $this->loadTestData(['num_of_players' => 56]);
        $this->expectException(BadRequestException::class);
        $teams = $this->teamGenerationService->generateTeams(20);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    private function assertTeamsAreConstructedCorrectly(Collection $teams, int $avgTeamSize): void
    {
        $this->assertEquals(0, $teams->count() % 2); //test created even number of teams
        foreach ($teams as $team) {
            $this->assertTeamHasOneCoachAndOneGoalie($team);
            $this->assertTeamHasCorrectNumberOfPlayers($team, $avgTeamSize);
        }
        $this->assertAllEligiblePlayersUsed();
        $this->assertTeamsAreEvenlyBalanced($teams);
    }

    private function assertAllEligiblePlayersUsed(): void
    {
        $unusedPlayerCount = User::players()->unassigned()->count();
        $this->assertEquals(0, $unusedPlayerCount);
    }

    /**
     * @throws \PHPUnit\Framework\ExpectationFailedException
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\Exception
     */
    private function assertTeamHasOneCoachAndOneGoalie(Team $team): void
    {
        $this->assertNotNull($team->coach);
        $this->assertNotNull($team->players);
        $goalies = $team->players->where('can_play_goalie', true);
        $this->assertGreaterThan(0, $goalies->count());
    }


    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    private function assertTeamHasCorrectNumberOfPlayers(Team $team, int $avgTeamSize): void
    {
        $this->assertGreaterThanOrEqual($avgTeamSize * 0.90, $team->players->count());
        $this->assertLessThanOrEqual($avgTeamSize * 1.10, $team->players->count());
    }

    /**
     * @throws \SebastianBergmann\RecursionContext\InvalidArgumentException
     * @throws \PHPUnit\Framework\ExpectationFailedException
     */
    private function assertTeamsAreEvenlyBalanced(Collection $teams): void
    {
        $rankings = [];
        foreach ($teams as $team) {
            /* @var Team $team */
            $rankings[] = $team->totalPlayerRanking();
        }

        $avgRanking = array_sum($rankings) / $teams->count();
        $max = max($rankings);
        $min = min($rankings);

        //Test that team rankings are +/- 5% from the average.
        $this->assertGreaterThan($avgRanking * 0.95, $min);
        $this->assertLessThan($avgRanking * 1.05, $max);
    }

    protected function loadTestData(?array $replacementVars = []): void
    {
        User::factory()->coach()->count($replacementVars['num_of_teams'] ?? 4)->create();
        User::factory()->goalie()->count($replacementVars['num_of_teams'] ?? 4)->create();
        User::factory()->player()->count($replacementVars['num_of_players'] ?? 76)->create();
    }
}
