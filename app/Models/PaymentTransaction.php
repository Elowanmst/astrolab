<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'transaction_id',
        'processor',
        'status',
        'amount',
        'fees',
        'currency',
        'payment_method',
        'card_last_4',
        'card_brand',
        'processor_response',
        'failure_reason',
        'processed_at',
        'refunded_at',
        'refunded_amount',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'fees' => 'decimal:2',
        'refunded_amount' => 'decimal:2',
        'processor_response' => 'array',
        'processed_at' => 'datetime',
        'refunded_at' => 'datetime',
    ];

    /**
     * Relation avec la commande
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Scope pour les transactions réussies
     */
    public function scopeSuccessful($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope pour les transactions échouées
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Scope pour un processeur spécifique
     */
    public function scopeByProcessor($query, $processor)
    {
        return $query->where('processor', $processor);
    }

    /**
     * Montant net (après frais)
     */
    public function getNetAmountAttribute()
    {
        return $this->amount - $this->fees;
    }

    /**
     * Statut formaté pour l'affichage
     */
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'En attente',
            'completed' => 'Complété',
            'failed' => 'Échoué',
            'cancelled' => 'Annulé',
            'refunded' => 'Remboursé',
            default => $this->status,
        };
    }
}
