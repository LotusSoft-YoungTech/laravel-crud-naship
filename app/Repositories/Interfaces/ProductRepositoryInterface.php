<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;

interface ProductRepositoryInterface
{
    public function getAllProducts();
    public function storeProduct(array $data);
    public function findProductById($id);
    public function updateProduct(Product $product, array $data);
    public function deleteProduct($id);
}
