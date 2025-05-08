<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{



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
}
