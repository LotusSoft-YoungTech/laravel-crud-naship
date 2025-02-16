<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Sanctum\HasApiTokens;

class Product extends Model
{ use HasApiTokens;
    use HasFactory;
    protected $fillable = ['name', 'price', 'description', 'image','user_id'];
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
