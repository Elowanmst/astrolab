<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Enums\ProductSize;

class ProductSizeStock extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'size',
        'stock'
    ];

    protected $casts = [
        'size' => ProductSize::class,
        'stock' => 'integer',
    ];

    /**
     * Bootstrap du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Synchroniser le stock du produit parent après modification
        static::saved(function ($sizeStock) {
            $sizeStock->updateParentStock();
        });

        static::deleted(function ($sizeStock) {
            $sizeStock->updateParentStock();
        });
    }

    /**
     * Mettre à jour le stock total du produit parent
     */
    protected function updateParentStock()
    {
        if ($this->product_id) {
            $totalStock = static::where('product_id', $this->product_id)->sum('stock');
            DB::table('products')
                ->where('id', $this->product_id)
                ->update(['stock' => $totalStock]);
        }
    }

    /**
     * Relation avec le produit
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Vérifier si la taille est en rupture de stock
     */
    public function isOutOfStock(): bool
    {
        return $this->stock <= 0;
    }

    /**
     * Vérifier si la taille a un stock faible
     */
    public function isLowStock(int $threshold = 5): bool
    {
        return $this->stock > 0 && $this->stock <= $threshold;
    }
}
