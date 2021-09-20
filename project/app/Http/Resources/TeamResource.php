<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'team_name' => $this->team_name,
            'players' => $this->players,
            'coach' => $this->coach,
            'goalie' => $this->players->where('can_play_goalie', true)->first(),
            'num_of_players' => $this->players->count(),
            'total_team_rating' => $this->totalPlayerRanking()
        ];
    }
}
