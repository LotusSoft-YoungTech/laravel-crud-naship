<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductApiRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductApiRepository implements ProductApiRepositoryInterface
{
    public function getAll()
    {
        return Product::select('id', 'name', 'price', 'description','image')->get();
    }
    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'description' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $product = new Product();
        $product->name = $request->name;
        $product->price = $request->price;
        $product->description = $request->description;
        $product->user_id = $user->id;

        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('products', $imageName, 'public');
            $product->image = $path;
        }

        $product->save();
        return $product;
    }
   
    public function show($id){

        Product::select('id', 'name', 'price', 'description','image')
                  ->where('id', $id)
                  ->first();
    }
    public function update(Request $request,$data){

        $product = Product::find($data);
        if (!$product) {
            return response()->json(['error' => 'Product not found'], 404);
        }

        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->price = $request->input('price');

        if ($request->hasFile('image')) {
            if (Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $path = $image->storeAs('public/products', $imageName);
            $product->image = $path;
        }

        $product->save();
        return $product;

    }
    public function delete($id){
        $product = Product::find($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        if ($product->image && file_exists(public_path('images/' . $product->image))) {
            unlink(public_path('images/' . $product->image));
        }

        $product->delete();
   
    }

}
