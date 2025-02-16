@extends('layout.app')

@section('content')
    <h1>Edit Product</h1>

    <form action="{{ route('products.update', $product) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="name">Name</label>
            <input type="text" name="name" class="form-control" id="name" value="{{ old('name', $product->name) }}" required>
        </div>

        <div class="form-group">
            <label for="price">Price</label>
            <input type="number" name="price" class="form-control" id="price" value="{{ old('price', $product->price) }}" required>
        </div>

        <div class="form-group">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" id="description" rows="3" required>{{ old('description', $product->description) }}</textarea>
        </div>

        <div class="form-group">
            <label for="image">Image (Optional)</label>
            <input type="file" name="image" class="form-control" id="image">
            <br>
            <img src="{{ asset('images/' . $product->image) }}" width="100">
        </div>

        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@endsection
