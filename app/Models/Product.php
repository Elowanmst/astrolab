<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\CropPosition;
use App\Enums\ProductSize;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    
    protected $fillable = [
        'name',
        'description',
        'price',
        // 'stock', // Supprimé - maintenant géré par ProductSizeStock
        'category_id',
        'color',
        // 'size', // Supprimé - maintenant géré par ProductSizeStock
        'material',
        'gender'
    ];

    protected $casts = [
        // 'size' => ProductSize::class, // Supprimé - maintenant dans ProductSizeStock
    ];

    /**
     * Bootstrap du modèle
     */
    protected static function boot()
    {
        parent::boot();

        // Synchroniser le stock total après la sauvegarde
        static::saved(function ($product) {
            if ($product->sizeStocks()->exists()) {
                $totalStock = $product->getTotalStock();
                // Éviter la récursion en utilisant une requête directe
                DB::table('products')
                    ->where('id', $product->id)
                    ->update(['stock' => $totalStock]);
            }
        });
    }
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    
    public function colors()
    {
        return $this->belongsToMany(Color::class);
    }
    
    /**
     * Relation avec les stocks par taille
     */
    public function sizeStocks()
    {
        return $this->hasMany(ProductSizeStock::class);
    }

    /**
     * Obtenir le stock pour une taille donnée
     */
    public function getStockForSize(string $size): int
    {
        $sizeStock = $this->sizeStocks()->where('size', $size)->first();
        return $sizeStock ? $sizeStock->stock : 0;
    }

    /**
     * Obtenir le stock total de toutes les tailles
     */
    public function getTotalStock(): int
    {
        return $this->sizeStocks()->sum('stock');
    }

    /**
     * Vérifier si une taille est disponible
     */
    public function isSizeAvailable(string $size): bool
    {
        return $this->getStockForSize($size) > 0;
    }

    /**
     * Obtenir toutes les tailles disponibles (avec stock > 0)
     */
    public function getAvailableSizes(): array
    {
        return $this->sizeStocks()
            ->where('stock', '>', 0)
            ->pluck('size')
            ->map(fn($size) => is_string($size) ? $size : $size->value)
            ->toArray();
    }

    /**
     * Réduire le stock d'une taille
     */
    public function decreaseStock(string $size, int $quantity = 1): bool
    {
        $sizeStock = $this->sizeStocks()->where('size', $size)->first();
        
        if (!$sizeStock || $sizeStock->stock < $quantity) {
            return false;
        }

        $sizeStock->decrement('stock', $quantity);
        return true;
    }

    /**
     * Augmenter le stock d'une taille
     */
    public function increaseStock(string $size, int $quantity = 1): void
    {
        $sizeStock = $this->sizeStocks()->where('size', $size)->first();
        
        if ($sizeStock) {
            $sizeStock->increment('stock', $quantity);
        } else {
            $this->sizeStocks()->create([
                'size' => $size,
                'stock' => $quantity
            ]);
        }
    }
    
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('products');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->width(400)
            ->height(300)
            ->crop(400, 300, CropPosition::Center)
            ->sharpen(10);

        $this->addMediaConversion('preview')
            ->width(800)
            ->height(800)
            ->crop(800, 800, CropPosition::Center)
            ->sharpen(10);
    }

}
