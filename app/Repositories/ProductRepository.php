<?php

namespace App\Repositories;

use App\Models\Product;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductRepository implements ProductRepositoryInterface
{
    public function getAllProducts()
    {
        return Cache::remember('products.index', 60, function () {
            return Product::with('user:id,name')
                ->select('user_id', 'id', 'name', 'price', 'description')
                ->get();
        });
    }

    public function storeProduct(array $data)
    {
        $product = new Product();
        $product->name = strip_tags($data['name']);
        $product->price = $data['price'];
        $product->description = strip_tags($data['description']);
        $product->user_id = Auth::id();

        if (isset($data['image'])) {
            $image = $data['image'];
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();
        return $product;
    }

    public function findProductById($id)
    {
        return Product::select('id', 'name', 'price', 'description', 'image')->findOrFail($id);
    }

    public function updateProduct(Product $product, array $data)
    {
        $product->name = strip_tags($data['name']);
        $product->price = $data['price'];
        $product->description = strip_tags($data['description']);

        if (isset($data['image'])) {
            if ($product->image && file_exists(public_path('images/' . $product->image))) {
                unlink(public_path('images/' . $product->image));
            }
            $image = $data['image'];
            $imageName = time().'.'.$image->getClientOriginalExtension();
            $image->move(public_path('images'), $imageName);
            $product->image = $imageName;
        }

        $product->save();
        return $product;
    }

    public function deleteProduct($id)
    {
        $product = Product::findOrFail($id, ['id', 'user_id', 'image']);
        
       
        if ($product->image && file_exists(public_path('images/' . $product->image))) {
            unlink(public_path('images/' . $product->image));
        }
        
        $product->delete();
        Cache::forget('products.index');

        return true;
    }
}
