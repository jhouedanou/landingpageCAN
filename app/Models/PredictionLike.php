<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PredictionLike extends Model
{
    protected $fillable = ['user_id', 'prediction_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }
}
