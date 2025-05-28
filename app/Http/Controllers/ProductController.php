<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Color;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::with('category', 'colors','media')->paginate(5);
        return view('product.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $categories = Category::all(); 
        $colors = Color::all(); // Fetch all colors
        return view('product.create', compact('categories', 'colors'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id', 
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'material' => 'nullable|string',
            'colors' => 'nullable|array', // Validation for colors
            'colors.*' => 'exists:colors,id', // Ensure each color exists in the colors table
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $product = Product::create($data);

        if ($request->has('colors')) {
            $product->colors()->attach($request->input('colors'));
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('products');
            }
        }

        return redirect()->route('products.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category', 'colors', 'media')->findOrFail($id);
        return view('product.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::with('colors', 'media')->findOrFail($id);
        $categories = Category::all();
        $colors = Color::all();
        return view('product.edit', compact('product', 'categories', 'colors'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $data = $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'category_id' => 'required|exists:categories,id', 
            'color' => 'nullable|string',
            'size' => 'nullable|string',
            'material' => 'nullable|string',
            'colors' => 'nullable|array', // Validation for colors
            'colors.*' => 'exists:colors,id', // Ensure each color exists in the colors table
            'images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $product = Product::findOrFail($id);
        $product->update($data);

        if ($request->has('colors')) {
            $product->colors()->sync($request->input('colors')); // Sync colors to update relationships
        }
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $product->addMedia($image)->toMediaCollection('products');
            }
        }

        return redirect()->route('products.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        Product::destroy($id);

        $product->clearMediaCollection('products');

        $product->delete();

        return redirect()->route('products.index');
    }
}
