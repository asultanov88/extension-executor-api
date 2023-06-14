<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $table='products';
    protected $primaryKey = 'productId';
    protected $fillable=[
        'productId',
        'name',
        'description',
        'createdBy',
        'updatedBy',
    ];
}
