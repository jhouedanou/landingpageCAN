<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Team extends Model
{
    protected $fillable = [
        'name',
        'iso_code',
        'group',
    ];

    /**
     * Get the flag image URL for this team.
     */
    public function getFlagUrlAttribute(): string
    {
        return "https://flagcdn.com/w40/{$this->iso_code}.png";
    }

    /**
     * Get the high-res flag image URL for this team.
     */
    public function getFlagUrl80Attribute(): string
    {
        return "https://flagcdn.com/w80/{$this->iso_code}.png";
    }

    /**
     * Get home matches for this team.
     */
    public function homeMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'home_team_id');
    }

    /**
     * Get away matches for this team.
     */
    public function awayMatches(): HasMany
    {
        return $this->hasMany(MatchGame::class, 'away_team_id');
    }
}
