<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Repositories\Interfaces\ProductApiRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends BaseController
{
    protected $productRepository;
    public function __construct(ProductApiRepositoryInterface $productRepository)
    {
       
        $this->middleware('auth:sanctum');
        $this->productRepository=$productRepository;
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json($this->productRepository->getAll(), 200);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
       $product=$this->productRepository->store($request);
    
        return response()->json([
            'message' => 'Product added successfully',
            'product' => $product,
        ], 201); 
    }
    

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $product = $this->productRepository->show($id);
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }
        return response()->json($product, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
            'image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        $product=$this->productRepository->update($request,$id);

        return response()->json(['message' => 'Product updated successfully', 'product' => $product], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $this->productRepository->delete($id);
        return response()->json(['message' => 'Product deleted successfully'], 200);
    }
}
