<?php

namespace App\Models;

use Faker\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

/**
 * App\Models\Team
 *
 * @property int $id
 * @property string $team_name
 * @property-read \App\Models\User|null $coach
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Models\User[] $players
 * @property-read int|null $players_count
 * @method static \Database\Factories\TeamFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|Team newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Team query()
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Team whereTeamName($value)
 * @mixin \Eloquent
 */
class Team extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'team_name'
    ];

    /**
     * Relationship Definitions
     */
    public function coach(): HasOne
    {
        return $this->hasOne(User::class)->where('user_type', User::USER_TYPE_COACH);
    }

    public function players(): HasMany
    {
        return $this->hasMany(User::class, 'team_id')->where('user_type', User::USER_TYPE_PLAYER);
    }

    /**
     * Getters/Setters/Convenience Methods
     */
    public function totalPlayerRanking()
    {
        return $this->players()->sum('ranking');
    }

    public function playerCount()
    {
        return $this->players()->count();
    }

    public static function generateTeamName(): string
    {
        $faker = Factory::create();
        return Str::title($faker->city() . " " . $faker->word());
    }
}
