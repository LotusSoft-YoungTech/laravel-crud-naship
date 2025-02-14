<?php
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(TestCase::class, RefreshDatabase::class);

test('test_of_redirecting_guest_to _loginpage', function () {
    $response = $this->get(route('products.create'));

    $response->assertRedirect('/login');
});


test('Test_of_Product_user_relation', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['user_id' => $user->id]);
   
    expect($product->user->id)->toBe($user->id);
});

// Test to check if a product can be created

test('test_of_product_can_be_created', function () {
    Storage::fake('public');

    $user = User::factory()->create();
    $file=UploadedFile::fake()->image('avatar.jpg');
    $product = Product::create([
        'name' => 'Test Product',
        'price' => 99.99,
        'description' => 'Test Description',
        'user_id' => $user->id,
        'image' => $file,
    ]);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Product',
        'price' => 99.99,
        'user_id' => $user->id,
    ]);
});

// Test to check if a product can be updated
test('Test_of_product_can_be_updated', function () {
    $product = Product::factory()->create();

    $product->update(['name' => 'Updated Product']);
    $this->assertDatabaseHas('products', [
        'id' => $product->id,
        'name' => 'Updated Product',
    ]);
});

test('product_can_be_deleted', function () {
    $product = Product::factory()->create();
    $product->delete();
    $this->assertDatabaseMissing('products', [
        'id' => $product->id,
    ]);
});
