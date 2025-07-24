<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'processor',
        'name',
        'description',
        'is_active',
        'is_test_mode',
        'config_data',
        'fee_percentage',
        'fee_fixed',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_test_mode' => 'boolean',
        'config_data' => 'array',
        'fee_percentage' => 'decimal:2',
        'fee_fixed' => 'decimal:2',
    ];

    /**
     * Scope pour les processeurs actifs
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Obtenir le processeur actif principal
     */
    public static function getActiveProcessor()
    {
        return static::where('is_active', true)->first();
    }

    /**
     * Calculer les frais de transaction
     */
    public function calculateFees($amount)
    {
        return ($amount * $this->fee_percentage / 100) + $this->fee_fixed;
    }

    /**
     * Obtenir la configuration pour un processeur spécifique
     */
    public static function getConfigFor($processor)
    {
        return static::where('processor', $processor)->first();
    }

    /**
     * Attributs masqués dans les formulaires (mots de passe, clés secrètes)
     */
    public function getSensitiveFields()
    {
        switch ($this->processor) {
            case 'stripe':
                return ['secret_key', 'webhook_secret'];
            case 'paypal':
                return ['client_secret'];
            case 'lyra':
                return ['key_test', 'key_prod'];
            default:
                return [];
        }
    }

    /**
     * Obtenir les champs de configuration selon le processeur
     */
    public function getConfigFields()
    {
        switch ($this->processor) {
            case 'stripe':
                return [
                    'public_key' => 'Clé publique',
                    'secret_key' => 'Clé secrète',
                    'webhook_secret' => 'Secret webhook',
                ];
            case 'paypal':
                return [
                    'client_id' => 'Client ID',
                    'client_secret' => 'Client Secret',
                    'sandbox' => 'Mode Sandbox',
                ];
            case 'lyra':
                return [
                    'shop_id' => 'Shop ID',
                    'key_test' => 'Clé Test',
                    'key_prod' => 'Clé Production',
                    'endpoint' => 'Endpoint API',
                ];
            default:
                return [];
        }
    }
}
