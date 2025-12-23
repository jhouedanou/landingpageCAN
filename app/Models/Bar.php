<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bar extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'zone',
        'latitude',
        'longitude',
        'is_active',
        'type_pdv',
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Types de PDV disponibles
     */
    public static function getTypePdvOptions()
    {
        return [
            'dakar' => 'Points de vente Dakar',
            'regions' => 'Points de vente Régions',
            'chr' => 'Cafés-Hôtel-Restaurants (CHR)',
            'fanzone' => 'Fanzones',
            'fanzone_public' => 'Fanzone tout public',
            'fanzone_hotel' => 'Fanzone hôtel',
        ];
    }

    /**
     * Obtenir le nom lisible du type PDV
     */
    public function getTypePdvNameAttribute()
    {
        $types = self::getTypePdvOptions();
        return $types[$this->type_pdv] ?? $this->type_pdv;
    }

    /**
     * Get the animations for this bar.
     */
    public function animations()
    {
        return $this->hasMany(Animation::class);
    }
}
