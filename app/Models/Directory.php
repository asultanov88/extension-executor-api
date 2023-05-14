<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directory extends Model
{
    use HasFactory;
    protected $table='directories';
    protected $primaryKey = 'directoryId';
    public $timestamps = false;
    protected $fillable=[
        'directoryId',
        'name',
        'isProject',
        'parentDirectoryId',
        'projectId',
    ];
}
