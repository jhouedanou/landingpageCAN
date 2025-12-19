<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'match_id',
        'predicted_winner',
        'score_a',
        'score_b',
        'predict_draw',
        'penalty_winner',
        'points_earned',
    ];

    protected $casts = [
        'predict_draw' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }
}
