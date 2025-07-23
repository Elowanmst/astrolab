<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'free_from_amount',
        'estimated_days_min',
        'estimated_days_max',
        'is_active',
        'sort_order',
        'restrictions',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'free_from_amount' => 'decimal:2',
        'is_active' => 'boolean',
        'restrictions' => 'array',
    ];

    /**
     * Scope pour les méthodes actives
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope pour l'ordre d'affichage
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Calculer le prix de livraison selon le montant du panier
     */
    public function calculatePrice($cartAmount)
    {
        // Si un seuil de gratuité est défini et que le montant du panier l'atteint
        if ($this->free_from_amount && $cartAmount >= $this->free_from_amount) {
            return 0;
        }

        return $this->price;
    }

    /**
     * Obtenir le délai d'estimation formaté
     */
    public function getEstimatedDeliveryAttribute()
    {
        if (!$this->estimated_days_min && !$this->estimated_days_max) {
            return null;
        }

        if ($this->estimated_days_min === $this->estimated_days_max) {
            return $this->estimated_days_min . ' jour' . ($this->estimated_days_min > 1 ? 's' : '');
        }

        return $this->estimated_days_min . '-' . $this->estimated_days_max . ' jours';
    }

    /**
     * Vérifier si la livraison est gratuite pour un montant donné
     */
    public function isFreeForAmount($amount)
    {
        return $this->free_from_amount && $amount >= $this->free_from_amount;
    }
}
