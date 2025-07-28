<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Banner;
use App\Models\Collection;
use App\Models\HomePageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        // Récupérer les paramètres de la page d'accueil avec valeurs par défaut
        $homeSettings = [
            'hero_title' => HomePageSetting::get('hero_title', 'ASTROLAB') ?: 'ASTROLAB',
            'hero_subtitle' => HomePageSetting::get('hero_subtitle', 'DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS') ?: 'DES ÉDITIONS ÉPHÉMÈRES EXCLUSIVES IMAGINÉES PAR DES ILLUSTRATEURS INDÉPENDANTS',
            'hero_image' => HomePageSetting::get('hero_image') ?: null,
            'promotion_image' => HomePageSetting::get('promotion_image') ?: null,
        ];
        
        // S'assurer que toutes les valeurs sont des chaînes ou null
        foreach ($homeSettings as $key => $value) {
            if (is_array($value)) {
                $homeSettings[$key] = !empty($value) ? $value[0] : null;
            }
        }
        
        // Récupérer la première bannière (pour compatibilité)
        $banner = Banner::first();
        
        // Récupérer la première collection
        $collection = Collection::first();
        
        // Récupérer les produits
        $products = Product::with('media')->get();

        return view('home', compact('products', 'banner', 'collection', 'homeSettings'));
    }  
}