<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        
        $products = Product::with('media')->get();

        return view('home', compact('products'));
    }  
}
// ::with('media')->get() // Charge les médias associés