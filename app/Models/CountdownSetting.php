<?php
// filepath: app/Models/CountdownSetting.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class CountdownSetting extends Model
{
    protected $fillable = [
        'title',
        'end_date',
        'is_active'
    ];

    protected $casts = [
        'end_date' => 'datetime',
        'is_active' => 'boolean'
    ];

    /**
     * Obtenir le countdown actif
     */
    public static function getActive()
    {
        return static::where('is_active', true)
                    ->where('end_date', '>', now())
                    ->first();
    }

    /**
     * Vérifier si le countdown est expiré
     */
    public function isExpired(): bool
    {
        return $this->end_date->isPast();
    }

    /**
     * Obtenir le temps restant en secondes
     */
    public function getSecondsRemaining(): int
    {
        if ($this->isExpired()) {
            return 0;
        }
        
        return $this->end_date->diffInSeconds(now());
    }
}