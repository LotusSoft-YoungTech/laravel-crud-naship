<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class Productswebcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    
    public function create()
    {
        if (Auth::check()) {
            return view('products.create');
        } else {
            return redirect('/login');
        }
       
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{ 
    $request->validate([
        'name' => 'required',
        'price' => 'required',
        'description' => 'required',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $product = new Product();
    $product->name = $request->name;
    $product->price = $request->price;
    $product->description = $request->description;
    $product->user_id = Auth::id(); 

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $product->image = $imageName;
    }

    $product->save();

    return redirect()->route('products.index')->with('success', 'Product created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::find($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::find($id);
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
{
    if ($product->user_id !== Auth::id()) {
        return redirect()->route('products.index')->with('error', 'You are not authorized to update this product.');
    }

    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'price' => 'required',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

    $product->name = $request->name;
    $product->price = $request->price;
    $product->description = $request->description;

    if ($request->hasFile('image')) {
        $image = $request->file('image');
        $imageName = time().'.'.$image->getClientOriginalExtension();
        $image->move(public_path('images'), $imageName);
        $product->image = $imageName;
    }

    $product->save();

    return redirect()->route('products.index')->with('success', 'Product updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
{
    $product = Product::find($id);

    if ($product->user_id !== Auth::id()) {
        return redirect()->route('products.index')->with('error', 'You are not authorized to delete this product.');
    }

    if (Storage::disk('public')->exists('Product/' . $product->image)) {
        Storage::disk('public')->delete('Product/' . $product->image);
    }

    $product->delete();
    return redirect()->route('products.index')->with('success', 'Product deleted successfully');
}
}
