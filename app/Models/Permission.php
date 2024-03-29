<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;
    protected $table='permissions';
    protected $primaryKey = 'permissionId';
    public $timestamps = false;
    protected $fillable=[
        'permissionId',
        'name',
        'description',
    ];
}
