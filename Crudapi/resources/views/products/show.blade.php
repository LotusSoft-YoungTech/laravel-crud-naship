@extends('layout.app')

@section('content')
    <h1>{{ $product->name }}</h1>
    <p>Price: {{ $product->price }}</p>
    <p>{{ $product->description }}</p>
    <img src="{{ asset('images/'.$product->image) }}" width="200">

    <br><br>
    <a href="{{ route('products.index') }}" class="btn btn-secondary">Back to List</a>
    <a href="{{ route('products.edit', $product) }}" class="btn btn-warning">Edit</a>

    <form action="{{ route('products.destroy', $product) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger">Delete</button>
    </form>
@endsection
