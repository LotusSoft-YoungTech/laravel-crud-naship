<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(\Illuminate\Foundation\Testing\RefreshDatabase::class);
uses(Tests\TestCase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->token = $this->user->createToken('test-token')->plainTextToken;
    $this->headers = ['Authorization' => 'Bearer ' . $this->token];
});


test('can create a product and verify user relation', function () {
  
    Storage::fake('public');
    $user = User::factory()->create();
    $token = $user->createToken('test-token')->plainTextToken;
    $headers = ['Authorization' => 'Bearer ' . $token];
    $response = $this->postJson('/api/products', [
        'name' => 'Test Product',
        'price' => 99.99,
        'description' => 'Test description',
        'image' => UploadedFile::fake()->image('product.jpg')
    ], $headers);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Product added successfully',
            'product' => [
                'name' => 'Test Product',
                'price' => 99.99,
                'description' => 'Test description',
                'user_id' => $user->id  
            ]
        ]);
    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'user_id' => $user->id  
    ]);
    $product = Product::where('name', 'Test Product')->first();
    $this->assertNotNull($product);
    $this->assertEquals($user->id, $product->user->id);  
});

test('requires authentication to create product', function () {
    $response = $this->postJson('/api/products', [
        'name' => 'Test Product',
        'price' => 99.99,
        'description' => 'Test description'
    ]);

    $response->assertStatus(401);
});


test('update product', function () {
    Storage::fake('public');
    $product = Product::factory()->create();
    $newImage = UploadedFile::fake()->image('new-image.jpg');

    $response = $this->putJson("/api/products/{$product->id}", [
        'name' => 'Updated Name',
        'price' => 199.99,
        'description' => 'Updated description',
        'image' => $newImage
    ], $this->headers);

    $response->assertStatus(200)
        ->assertJson([
            'product' => [
                'name' => 'Updated Name',
                'price' => 199.99,
                'description' => 'Updated description'
            ]
        ]);

    
});

test('delete product', function () {
    Storage::fake('public');
    $product = Product::factory()->create(['image' => 'products/test-image.jpg']);

    $response = $this->deleteJson("/api/products/{$product->id}", [], $this->headers);

    $response->assertStatus(200)
        ->assertJson(['message' => 'Product deleted successfully']);

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
    Storage::disk('public')->assertMissing($product->image);
});

test('validation rules for product creation', function () {
    $response = $this->postJson('/api/products', [
        'name' => '',
        'price' => 'not-a-number',
        'description' => ''
    ], $this->headers);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'price', 'description']);
});