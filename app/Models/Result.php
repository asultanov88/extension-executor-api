<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Result extends Model
{
    use HasFactory;
    protected $table='results';
    protected $primaryKey = 'ResultId';
    public $timestamps = false;
    protected $fillable=[
        'resultId',
        'description',
    ];
}