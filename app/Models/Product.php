<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Image\Enums\CropPosition;

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia;
    
    
    protected $fillable = [
        'name',
        'description',
        'price',
        'stock',
        'category_id',
        'color',
        'size',
        'material',
        'gender'
    ];
    
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
    public function colors()
    {
        return $this->belongsToMany(Color::class);
    }
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('products');
    }

    public function registerMediaConversions(Media $media = null): void
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
