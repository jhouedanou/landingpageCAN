<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PointLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'admin_id',
        'match_id',
        'bar_id',
        'source',
        'note',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Administrateur ayant déclenché l'ajustement (NULL = action CLI / système).
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function match()
    {
        return $this->belongsTo(MatchGame::class, 'match_id');
    }
}
