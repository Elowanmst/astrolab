<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LegalController extends Controller
{
    /**
     * Affiche les conditions générales de vente
     */
    public function cgv()
    {
        return view('legal.cgv');
    }

    /**
     * Affiche la page livraisons et retours
     */
    public function shippingReturns()
    {
        return view('legal.shipping-returns');
    }
}
