<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'status',
        'total_amount',
        'shipping_name',
        'shipping_email',
        'shipping_phone',
        'shipping_address',
        'shipping_city',
        'shipping_postal_code',
        'shipping_country',
        'shipping_method',
        'relay_point_id',
        'relay_point_name',
        'relay_point_address',
        'relay_point_postal_code',
        'relay_point_city',
        'relay_point_data',
        'tracking_number',
        'notes',
        'shipped_at',
        'delivered_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'processing' => 'En traitement',
            'shipped' => 'Expédiée',
            'delivered' => 'Livrée',
            'cancelled' => 'Annulée',
            default => 'Inconnu'
        };
    }

    public function getStatusLabel()
    {
        return $this->status_label;
    }

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'AST-' . strtoupper(uniqid());
            }
        });
    }

    // Générateur automatique de numéro de commande
    public static function generateOrderNumber()
    {
        return 'AST-' . strtoupper(uniqid());
    }


}
