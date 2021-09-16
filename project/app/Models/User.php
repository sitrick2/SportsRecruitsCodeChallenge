<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

/**
 * App\Models\User
 *
 * @property int $id
 * @property string|null $user_type
 * @property string|null $first_name
 * @property string|null $last_name
 * @property int|null $ranking
 * @property int|null $can_play_goalie
 * @property-read string $fullname
 * @property-read bool $is_goalie
 * @method static Builder|User coaches()
 * @method static Builder|User players()
 * @method static Builder|User newModelQuery()
 * @method static Builder|User newQuery()
 * @method static Builder|User query()
 * @method static Builder|User whereCanPlayGoalie($value)
 * @method static Builder|User whereFirstName($value)
 * @method static Builder|User whereId($value)
 * @method static Builder|User whereLastName($value)
 * @method static Builder|User whereRanking($value)
 * @method static Builder|User whereUserType($value)
 * @mixin \Eloquent
 */
class User extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'user_type',
        'first_name',
        'last_name',
        'ranking',
        'can_play_goalie'
    ];

    public const USER_TYPE_PLAYER = 'player';
    public const USER_TYPE_COACH = 'coach';

    /**
     * Model Scopes
     */
    public function scopeCoaches(Builder $query): Builder
    {
        return $query->where('user_type', static::USER_TYPE_COACH);
    }

    public function scopeGoalies(Builder $query): Builder
    {
        return $this->scopePlayers($query)
            ->where('can_play_goalie', true);
    }

    public function scopePlayers(Builder $query): Builder
    {
        return $query->where('user_type', static::USER_TYPE_PLAYER);
    }

    /**
     * Relationship definitions
     */
    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /**
     * Getters/Setters/Convenience Methods
     */
    public function getFullName(): string
    {
        return Str::title($this->first_name . ' ' . $this->last_name);
    }

    public function canPlayGoalie(): bool
    {
        return (bool) $this->can_play_goalie;
    }
}
