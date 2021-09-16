<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
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
     * Players only local scope
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePlayers(Builder $query): Builder
    {
        return $query->where('user_type', static::USER_TYPE_PLAYER);
    }

    public function scopeCoaches(Builder $query): Builder
    {
        return $query->where('user_type', static::USER_TYPE_COACH);
    }

    public function canPlayGoalie(): bool
    {
        return (bool) $this->can_play_goalie;
    }

    public function getFullname(): string
    {
        return Str::title($this->first_name . ' ' . $this->last_name);
    }
}
