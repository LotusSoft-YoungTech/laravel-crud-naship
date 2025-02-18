<?php

namespace App\Repositories\Interfaces;

use App\Models\Product;
use Illuminate\Http\Request;

interface ProductApiRepositoryInterface
{
    public function getAll();
    public function store(Request $request);
    public function show($id);
    public function update(Request $request,$data);
    public function delete($id);

   
}
