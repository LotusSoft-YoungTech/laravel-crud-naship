<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\User;
use App\Repositories\Interfaces\ProductRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Productswebcontroller extends Controller
{
    protected $productRepository;

    public function __construct(ProductRepositoryInterface $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    public function index()
    {
        $products = $this->productRepository->getAllProducts();
        return view('products.index', compact('products'));
    }

    public function create()
    {
        return Auth::check() ? view('products.create') : redirect('/login');
    }

    public function store(Request $request)
    {
        $data=$request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $this->productRepository->storeProduct($data);

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }

    public function show(string $id)
    {
        $product = $this->productRepository->findProductById($id);
        return view('products.show', compact('product'));
    }

    public function edit(string $id)
    {
        $product = $this->productRepository->findProductById($id);
        return view('products.edit', compact('product'));
    }

    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required',
            'description' => 'required',
            'price' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $this->productRepository->updateProduct($product, $request->all());

        return redirect()->route('products.index')->with('success', 'Product updated successfully.');
    }

    public function destroy(string $id)
    {
        $deleted = $this->productRepository->deleteProduct($id);

        if (!$deleted) {
            return redirect()->route('products.index')->with('error', 'You are not authorized to delete this product.');
        }

        return redirect()->route('products.index')->with('success', 'Product deleted successfully.');
    }
}
