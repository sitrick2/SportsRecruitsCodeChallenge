<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use App\Models\User;

class PlayersIntegrityTest extends TestCase
{
    public function testGoaliePlayersExist (): void
    {
		$result = User::players()->where('can_play_goalie', 1)->count();
		$this->assertTrue($result > 1);

    }
    public function testAtLeastOneGoaliePlayerPerTeam (): void
    {
/*
	    calculate how many teams can be made so that there is an even number of teams and they each have between 18-22 players.
	    Then check that there are at least as many players who can play goalie as there are teams
*/

    }
}
