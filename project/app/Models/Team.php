<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Team extends Model
{
    use HasFactory;

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
        return $this->hasMany(User::class)->where('user_type', User::USER_TYPE_PLAYER);
    }
}
