@extends('layout.app')

@section('content')
    <h1>Product List</h1>

    

    <a href="{{ route('products.create') }}" class="btn btn-primary">Create New Product</a>

    <table class="table mt-3">
        <thead>
            <tr>
                <th>Name</th>
                <th>Price</th>
                <th>Description</th>
                <th>Image</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->name }}</td>
                    <td>{{ $product->price }}</td>  
                    <td>{{ $product->description }}</td>
                    <td><img src="{{ asset('images/'.$product->image) }}" width="100"></td>
                    <td>
                        @auth
                            
                        
                        <a href="{{ route('products.show', $product) }}" class="btn btn-info">View</a>
                        <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>
                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                        @endauth
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endsection
