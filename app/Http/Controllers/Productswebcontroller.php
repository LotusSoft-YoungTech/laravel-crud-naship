<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class Productswebcontroller extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()

    {    
        $products = Cache::remember('products.index', 60, function () {
            return Product::with('user:id,name')
                ->select('user_id', 'id', 'name', 'price', 'description')
                ->paginate(10);
        });
        // $products= Product::with('user:id,name')->select('user_id','id','name', 'price', 'description')->get();
        // $products = Product::select( 'id','name', 'price', 'description')->get();
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
    $product->name = strip_tags($request->name);
    $product->price = $request->price;
    $product->description = strip_tags($request->description);
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
       
        $product = Product::select('id','name', 'price', 'description','image')->findOrFail($id);
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $product = Product::select('id','name', 'price', 'description','image')->where('id',$id)->firstOrFail();
        return view('products.edit', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
{
   

    $request->validate([
        'name' => 'required',
        'description' => 'required',
        'price' => 'required',
        'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

        $product->name = strip_tags($request->name);
        $product->price = $request->price;

        $product->description = strip_tags($request->description);
      
        if ($request->hasFile('image')) {
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }
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
        $product = Product::findOrFail($id, ['id', 'user_id', 'image']);
        
        if ($product->user_id !== Auth::id()) {
            return redirect()->route('products.index')->with('error', 'You are not authorized to delete this product.');
        }

        if ($product->image && file_exists(public_path('images/' . $product->image))) {
            unlink(public_path('images/' . $product->image));
        }
        $product->delete();
        Cache::forget('products.index');
        return redirect()->route('products.index')->with('success', 'Product deleted successfully');
    }
}
