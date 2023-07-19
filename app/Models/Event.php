<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;
    protected $table='events';
    protected $primaryKey = 'eventId';
    protected $fillable=[
        'eventId',
        'title',
        'description',
        'environment',
        'createdBy',
        'statusId',
        'productId',
    ];
}
