<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    use HasFactory;
    protected $table='screenshots';
    protected $primaryKey = 'screenshotId';
    public $timestamps = false;
    protected $fillable=[
        'screenshotId',
        'uuid',
        'blob',
    ];
}
