<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Preuve horodatée et géolocalisée d'une présence en point de vente (PDV).
 * Persistée à chaque détection de proximité : check-in explicite (/api/check-in)
 * ou pronostic soumis sur place. Voir migration create_check_ins_table.
 */
class CheckIn extends Model
{
    use HasFactory;

    public const SOURCE_CHECKIN = 'checkin';
    public const SOURCE_PREDICTION = 'prediction';

    protected $fillable = [
        'user_id',
        'bar_id',
        'prediction_id',
        'latitude',
        'longitude',
        'gps_accuracy',
        'distance_m',
        'source',
        'ip_address',
        'user_agent',
    ];

    protected $casts = [
        'latitude' => 'float',
        'longitude' => 'float',
        'gps_accuracy' => 'float',
        'distance_m' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bar()
    {
        return $this->belongsTo(Bar::class);
    }

    public function prediction()
    {
        return $this->belongsTo(Prediction::class);
    }
}
