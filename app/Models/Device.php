<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Device extends Model
{
    use HasFactory;
    protected $table='devices';
    protected $primaryKey = 'deviceId';
    public $timestamps = false;
    protected $fillable=[
        'deviceId',
        'name',
        'deleted',
        'replacedByDeviceId',
        'createdBy',
        'updatedBy',
    ];
}
